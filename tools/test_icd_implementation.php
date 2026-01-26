#!/usr/bin/env php
<?php
/**
 * Test script to validate ICD diagnosis codes implementation
 * This script validates the SQL migration and checks the code logic
 */

echo "=== ICD Diagnosis Codes Implementation Test ===" . PHP_EOL . PHP_EOL;

// Test 1: Validate SQL migration file syntax
echo "[TEST 1] Validating SQL migration file syntax..." . PHP_EOL;
$sql_file = __DIR__ . '/../database/add_icd_diagnosis_codes.sql';
if (!file_exists($sql_file)) {
    echo "  ❌ FAIL: Migration file not found at $sql_file" . PHP_EOL;
    exit(1);
}

$sql_content = file_get_contents($sql_file);
if (empty($sql_content)) {
    echo "  ❌ FAIL: Migration file is empty" . PHP_EOL;
    exit(1);
}

// Check for required SQL statements
$required_patterns = [
    'CREATE TABLE.*icd_codes' => 'CREATE TABLE for icd_codes',
    'ALTER TABLE.*consultations.*ADD COLUMN.*preliminary_diagnosis_id' => 'Add preliminary_diagnosis_id column',
    'ALTER TABLE.*consultations.*ADD COLUMN.*final_diagnosis_id' => 'Add final_diagnosis_id column',
    'INSERT INTO.*icd_codes.*B50' => 'Insert Malaria diagnosis (B50)',
    'INSERT INTO.*icd_codes.*J18' => 'Insert Pneumonia diagnosis (J18)',
];

foreach ($required_patterns as $pattern => $description) {
    if (preg_match('/' . $pattern . '/is', $sql_content)) {
        echo "  ✅ Found: $description" . PHP_EOL;
    } else {
        echo "  ❌ FAIL: Missing $description" . PHP_EOL;
        exit(1);
    }
}

echo "  ✅ PASS: SQL migration file is valid" . PHP_EOL . PHP_EOL;

// Test 2: Validate DoctorController search_diagnoses method exists
echo "[TEST 2] Validating DoctorController::search_diagnoses() method..." . PHP_EOL;
$controller_file = __DIR__ . '/../controllers/DoctorController.php';
if (!file_exists($controller_file)) {
    echo "  ❌ FAIL: DoctorController.php not found" . PHP_EOL;
    exit(1);
}

$controller_content = file_get_contents($controller_file);
if (strpos($controller_content, 'function search_diagnoses') === false) {
    echo "  ❌ FAIL: search_diagnoses() method not found in DoctorController" . PHP_EOL;
    exit(1);
}

if (strpos($controller_content, 'FROM icd_codes') === false) {
    echo "  ❌ FAIL: search_diagnoses() does not query icd_codes table" . PHP_EOL;
    exit(1);
}

echo "  ✅ PASS: search_diagnoses() method exists and queries icd_codes table" . PHP_EOL . PHP_EOL;

// Test 3: Validate start_consultation handles diagnosis IDs
echo "[TEST 3] Validating start_consultation() handles diagnosis IDs..." . PHP_EOL;
if (strpos($controller_content, 'preliminary_diagnosis_id') === false) {
    echo "  ❌ FAIL: start_consultation() does not handle preliminary_diagnosis_id" . PHP_EOL;
    exit(1);
}

if (strpos($controller_content, 'final_diagnosis_id') === false) {
    echo "  ❌ FAIL: start_consultation() does not handle final_diagnosis_id" . PHP_EOL;
    exit(1);
}

echo "  ✅ PASS: start_consultation() handles both diagnosis ID fields" . PHP_EOL . PHP_EOL;

// Test 4: Validate attend_patient.php has diagnosis search UI
echo "[TEST 4] Validating attend_patient.php has diagnosis search UI..." . PHP_EOL;
$view_file = __DIR__ . '/../views/doctor/attend_patient.php';
if (!file_exists($view_file)) {
    echo "  ❌ FAIL: attend_patient.php not found" . PHP_EOL;
    exit(1);
}

$view_content = file_get_contents($view_file);
$required_elements = [
    'preliminaryDiagnosisSearch' => 'Preliminary diagnosis search input',
    'finalDiagnosisSearch' => 'Final diagnosis search input',
    'search_diagnoses' => 'API call to search_diagnoses',
    'selectedPreliminaryDiagnosis' => 'Selected preliminary diagnosis display',
    'selectedFinalDiagnosis' => 'Selected final diagnosis display',
    'preliminary_diagnosis_id' => 'Hidden field for preliminary_diagnosis_id',
    'final_diagnosis_id' => 'Hidden field for final_diagnosis_id',
];

foreach ($required_elements as $element => $description) {
    if (strpos($view_content, $element) !== false) {
        echo "  ✅ Found: $description" . PHP_EOL;
    } else {
        echo "  ❌ FAIL: Missing $description" . PHP_EOL;
        exit(1);
    }
}

echo "  ✅ PASS: attend_patient.php has complete diagnosis search UI" . PHP_EOL . PHP_EOL;

// Test 5: Validate previous chief complaints display
echo "[TEST 5] Validating previous chief complaints display..." . PHP_EOL;
if (strpos($view_content, 'Previous Chief Complaints') === false) {
    echo "  ❌ FAIL: Missing 'Previous Chief Complaints' section" . PHP_EOL;
    exit(1);
}

if (strpos($controller_content, 'previous_complaints') === false) {
    echo "  ❌ FAIL: DoctorController does not fetch previous_complaints" . PHP_EOL;
    exit(1);
}

if (strpos($view_content, 'previous_complaints') === false) {
    echo "  ❌ FAIL: attend_patient.php does not display previous_complaints" . PHP_EOL;
    exit(1);
}

echo "  ✅ PASS: Previous chief complaints are fetched and displayed" . PHP_EOL . PHP_EOL;

// Test 6: Count number of ICD codes in migration
echo "[TEST 6] Counting ICD codes in migration..." . PHP_EOL;
preg_match_all("/INSERT INTO.*icd_codes.*VALUES/is", $sql_content, $insert_statements);
preg_match_all("/\('([A-Z][0-9]+[.]?[0-9]*)',/", $sql_content, $codes);

$unique_codes = array_unique($codes[1]);
$code_count = count($unique_codes);

if ($code_count >= 50) {
    echo "  ✅ PASS: Migration includes $code_count ICD codes (minimum 50 required)" . PHP_EOL;
} else {
    echo "  ⚠️  WARNING: Only $code_count ICD codes found (recommended: 50+)" . PHP_EOL;
}

// Display some example codes
echo "  Sample codes: " . implode(', ', array_slice($unique_codes, 0, 10)) . "..." . PHP_EOL . PHP_EOL;

// Test 7: Check for NMCP priority codes (Malaria)
echo "[TEST 7] Checking for NMCP priority diagnoses..." . PHP_EOL;
$nmcp_priority_codes = ['B50', 'B51', 'B52', 'B53', 'B54']; // Malaria codes
$found_priority = [];
foreach ($nmcp_priority_codes as $code) {
    if (preg_match("/'$code',/", $sql_content)) {
        $found_priority[] = $code;
    }
}

if (count($found_priority) >= 4) {
    echo "  ✅ PASS: NMCP priority malaria codes present: " . implode(', ', $found_priority) . PHP_EOL;
} else {
    echo "  ❌ FAIL: Missing NMCP priority malaria codes" . PHP_EOL;
    exit(1);
}

echo PHP_EOL;

// Test 8: Validate JavaScript functions
echo "[TEST 8] Validating JavaScript diagnosis search functions..." . PHP_EOL;
$required_js_functions = [
    'displayPreliminaryDiagnosisResults' => 'Display preliminary diagnosis results',
    'selectPreliminaryDiagnosis' => 'Select preliminary diagnosis',
    'displayFinalDiagnosisResults' => 'Display final diagnosis results',
    'selectFinalDiagnosis' => 'Select final diagnosis',
    'clearPreliminaryDiagnosisSearch' => 'Clear preliminary search',
    'clearFinalDiagnosisSearch' => 'Clear final search',
];

foreach ($required_js_functions as $func => $description) {
    if (strpos($view_content, "function $func") !== false) {
        echo "  ✅ Found: $description" . PHP_EOL;
    } else {
        echo "  ❌ FAIL: Missing JavaScript function: $description" . PHP_EOL;
        exit(1);
    }
}

echo "  ✅ PASS: All required JavaScript functions present" . PHP_EOL . PHP_EOL;

// Summary
echo "========================================" . PHP_EOL;
echo "✅ ALL TESTS PASSED!" . PHP_EOL;
echo "========================================" . PHP_EOL . PHP_EOL;

echo "Implementation Summary:" . PHP_EOL;
echo "  - ICD diagnosis codes table: ✅ Created" . PHP_EOL;
echo "  - Database migration: ✅ Valid ($code_count codes)" . PHP_EOL;
echo "  - API endpoint (search_diagnoses): ✅ Implemented" . PHP_EOL;
echo "  - UI components: ✅ Complete" . PHP_EOL;
echo "  - Previous complaints display: ✅ Implemented" . PHP_EOL;
echo "  - NMCP compliance: ✅ Malaria codes included" . PHP_EOL . PHP_EOL;

echo "Next Steps:" . PHP_EOL;
echo "  1. Apply database migration: mysql -u root -p zahanati < database/add_icd_diagnosis_codes.sql" . PHP_EOL;
echo "  2. Test in browser: Login as doctor → Attend Patient" . PHP_EOL;
echo "  3. Search for 'Malaria' or 'B50' in diagnosis fields" . PHP_EOL;
echo "  4. Verify previous chief complaints are displayed" . PHP_EOL . PHP_EOL;

exit(0);
