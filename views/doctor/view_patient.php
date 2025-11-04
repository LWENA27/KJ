<?php
// medical_record_view.php - Enhanced view with real lab data integration

// Assuming $patient_id is passed to this view
// First, fetch all necessary data

// 1. Get patient basic information (already exists in your code)
// 2. Get latest consultation details
// 3. Get all lab test orders for this patient
// 4. Get lab results mapped to specific test categories

// Presentation helper: find latest lab result from controller-provided map
// Controller must pass: $lab_results_map (see DoctorController::view_patient)
if (!isset($lab_results_map)) {
    $lab_results_map = [];
}

// Test aliases for better matching between form and database
$test_aliases = [
    'mRDT' => 'Malaria Test',
    'Blood Slide Smear' => 'Malaria Test', 
    'UPT' => 'Pregnancy Test',
    'RPP/Syphilis' => 'Syphilis Test (RPR)',
    'Blood uric acid' => 'Blood Uric Acid'
];

function findLabResult($map, $testName) {
    if (empty($map) || !$testName) return null;
    global $test_aliases;
    // try alias first
    if (isset($test_aliases[$testName])) {
        $alias = $test_aliases[$testName];
        if (isset($map[$alias])) return $map[$alias];
        $low_alias = strtolower($alias);
        if (isset($map[$low_alias])) return $map[$low_alias];
        $norm_alias = strtolower(preg_replace('/\s+/', '', $alias));
        if (isset($map[$norm_alias])) return $map[$norm_alias];
    }
    // try exact
    if (isset($map[$testName])) return $map[$testName];
    // try lowercased
    $low = strtolower($testName);
    if (isset($map[$low])) return $map[$low];
    // try normalized (no spaces)
    $norm = strtolower(preg_replace('/\s+/', '', $testName));
    if (isset($map[$norm])) return $map[$norm];
    // try substring match on keys
    foreach ($map as $k => $v) {
        if (stripos($k, $testName) !== false || stripos($testName, $k) !== false) {
            return $v;
        }
    }
    return null;
}

// Build map of requested tests for checkboxes
$requested_tests = [];
if (!empty($lab_orders)) {
    foreach ($lab_orders as $order) {
        $name = $order['test_name'] ?? '';
        if ($name) {
            $requested_tests[$name] = $order;
            // also normalize
            $norm = strtolower(preg_replace('/\s+/', '', $name));
            $requested_tests[$norm] = $order;
        }
    }
}

function isTestRequested($testName, $requested) {
    if (empty($requested) || !$testName) return false;
    // try exact
    if (isset($requested[$testName])) return true;
    // try normalized
    $norm = strtolower(preg_replace('/\s+/', '', $testName));
    if (isset($requested[$norm])) return true;
    // try substring
    foreach ($requested as $k => $v) {
        if (stripos($k, $testName) !== false || stripos($testName, $k) !== false) {
            return true;
        }
    }
    return false;
}
?>

<!-- Print Medical Record Form -->
<div class="mb-6 no-print">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Patient Medical Record</h1>
        <div class="flex space-x-3">
            <button onclick="printMedicalRecord()"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-print mr-2"></i>Print Record
            </button>
            <button onclick="window.location.href='<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/attend_patient/<?php echo $patient['id']; ?>'"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-stethoscope mr-2"></i>Attend Patient
            </button>
            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_lab_results/<?php echo $patient['id']; ?>"
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-vial mr-2"></i>View Lab Results
            </a>
            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/patient_journey/<?php echo $patient['id']; ?>"
               class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-route mr-2"></i>View Journey
            </a>
            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/patients" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i>Back to Patients
            </a>
        </div>
    </div>
</div>

<!-- Medical Record Form (Printable) -->
<div id="medicalRecord" class="bg-white border-2 border-gray-300 p-8 max-w-5xl mx-auto print:border-none print:p-4">
    <!-- Header -->
    <div class="text-center mb-6 border-b-2 border-gray-400 pb-4">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">KJ DISPENSARY</h1>
        <p class="text-sm text-gray-700">P.O.BOX 149, MBEYA</p>
        <p class="text-sm text-gray-700">PHONE 0776992746; centidispensary@gmail.com</p>
        <div class="flex justify-between mt-4 text-sm">
            <div>TOTAL………………………………………</div>
            <div>CASH PAID………….……………………….</div>
            <div>DEBIT………………………………………….</div>
        </div>
    </div>

    <!-- Patient Record Header -->
    <div class="mb-6">
        <h2 class="text-xl font-bold text-center mb-4 underline">PATIENT RECORD</h2>
        
        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
            <div class="flex items-center">
                <span class="font-medium mr-2">DATE:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo date('d/m/Y'); ?></span>
            </div>
            <div class="flex items-center">
                <span class="font-medium mr-2">REG NO:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo htmlspecialchars($patient['registration_number']); ?></span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4 text-sm">
            <div class="col-span-2 flex items-center">
                <span class="font-medium mr-2">PATIENT NAME:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></span>
            </div>
            <div class="flex items-center">
                <span class="font-medium mr-2">AGE:</span>
                <span class="border-b border-gray-400 flex-1 px-2 mr-4">
                    <?php
                    $dob = $patient['date_of_birth'] ?? null;
                    if (!empty($dob)) {
                        $age = date_diff(date_create($dob), date_create('today'))->y;
                        echo $age;
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </span>
                <span class="font-medium mr-2">SEX:</span>
                <span class="border-b border-gray-400 px-2"><?php echo strtoupper(substr($patient['gender'] ?? 'U', 0, 1)); ?></span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6 text-sm">
            <div class="flex items-center">
                <span class="font-medium mr-2">ADDRESS:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo htmlspecialchars($patient['address'] ?? ''); ?></span>
            </div>
            <div class="flex items-center">
                <span class="font-medium mr-2">OCCUPATION:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo htmlspecialchars($patient['occupation'] ?? ''); ?></span>
            </div>
            <div class="flex items-center">
                <span class="font-medium mr-2">PHONE NO:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo htmlspecialchars($patient['phone'] ?? ''); ?></span>
            </div>
        </div>
    </div>

    <!-- Vital Signs -->
    <div class="mb-6">
        <div class="grid grid-cols-5 gap-4 text-sm">
            <div class="text-center">
                <div class="font-medium mb-1">Temperature</div>
                <div class="border border-gray-400 h-10 p-2 text-center">
                    <?php 
                    if (!empty($vital_signs['temperature'])) {
                        echo htmlspecialchars($vital_signs['temperature']) . '°C';
                    }
                    ?>
                </div>
            </div>
            <div class="text-center">
                <div class="font-medium mb-1">Blood Pressure</div>
                <div class="border border-gray-400 h-10 p-2 text-center">
                    <?php 
                    if (!empty($vital_signs['blood_pressure_systolic']) && !empty($vital_signs['blood_pressure_diastolic'])) {
                        echo htmlspecialchars($vital_signs['blood_pressure_systolic']) . '/' . htmlspecialchars($vital_signs['blood_pressure_diastolic']);
                    }
                    ?>
                </div>
            </div>
            <div class="text-center">
                <div class="font-medium mb-1">Pulse Rate</div>
                <div class="border border-gray-400 h-10 p-2 text-center">
                    <?php 
                    if (!empty($vital_signs['pulse_rate'])) {
                        echo htmlspecialchars($vital_signs['pulse_rate']) . ' bpm';
                    }
                    ?>
                </div>
            </div>
            <div class="text-center">
                <div class="font-medium mb-1">Body Weight</div>
                <div class="border border-gray-400 h-10 p-2 text-center">
                    <?php 
                    if (!empty($vital_signs['weight'])) {
                        echo htmlspecialchars($vital_signs['weight']) . ' kg';
                    }
                    ?>
                </div>
            </div>
            <div class="text-center">
                <div class="font-medium mb-1">Height</div>
                <div class="border border-gray-400 h-10 p-2 text-center">
                    <?php 
                    if (!empty($vital_signs['height'])) {
                        echo htmlspecialchars($vital_signs['height']) . ' cm';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Clinical Examination -->
    <div class="mb-6">
        <div class="grid grid-cols-1 gap-4 text-sm">
            <?php
            // Use controller-provided $latest_consultation (scoped to latest visit) when available.
            if (!isset($latest_consultation)) {
                $latest_consultation = null;
                if (!empty($consultations)) {
                    $latest_consultation = $consultations[0];
                }
            }
            ?>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="font-medium mb-1">M/C</div>
                    <div class="border border-gray-400 h-16 p-2">
                        <?php echo htmlspecialchars($latest_consultation['main_complaint'] ?? ''); ?>
                    </div>
                </div>
                <div>
                    <div class="font-medium mb-1">O/E</div>
                    <div class="border border-gray-400 h-16 p-2">
                        <?php echo htmlspecialchars($latest_consultation['on_examination'] ?? ''); ?>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="font-medium mb-1">Preliminary Dx</div>
                    <div class="border border-gray-400 h-16 p-2">
                        <?php echo htmlspecialchars($latest_consultation['preliminary_diagnosis'] ?? ''); ?>
                    </div>
                </div>
                <div>
                    <div class="font-medium mb-1">Final Dx</div>
                    <div class="border border-gray-400 h-16 p-2">
                        <?php echo htmlspecialchars($latest_consultation['final_diagnosis'] ?? ''); ?>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="font-medium mb-1">Lab Investigation</div>
                <div class="border border-gray-400 h-16 p-2">
                    <?php echo htmlspecialchars($latest_consultation['lab_investigation'] ?? ''); ?>
                </div>
            </div>
         
            <div>
                <div class="font-medium mb-1">RX</div>
                <div class="border border-gray-400 h-20 p-2">
                    <?php echo htmlspecialchars($latest_consultation['prescription'] ?? ''); ?>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
            <div class="flex items-center">
                <span class="font-medium mr-2">DATE:</span>
                <span class="border-b border-gray-400 flex-1 px-2">
                    <?php 
                    $apt = $latest_consultation['appointment_date'] ?? $latest_consultation['visit_date'] ?? $latest_consultation['created_at'] ?? null; 
                    echo $apt ? date('d/m/Y', strtotime($apt)) : ''; 
                    ?>
                </span>
            </div>
            <div class="flex items-center">
                <span class="font-medium mr-2">Dr Signature:</span>
                <span class="border-b border-gray-400 flex-1 px-2"></span>
            </div>
        </div>
    </div>

    <!-- Laboratory Results Grid with REAL DATA -->
    <div class="mb-6 text-xs">
        <div class="grid grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <!-- Parasitology -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Parasitology</h4>
                    <div class="space-y-1">
                        <?php
                        // mRDT / Malaria Test
                        $malaria_result = findLabResult($lab_results_map, 'Malaria');
                        ?>
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('mRDT', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• mRDT</span>
                            <?php if ($malaria_result): ?>
                                <span class="ml-2 font-semibold <?php echo $malaria_result['is_normal'] ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo htmlspecialchars($malaria_result['result_value'] ?? $malaria_result['result_text']); ?>
                                </span>
                            <?php else: ?>
                                <span class="flex-1 border-b border-gray-300 mx-2"></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('Blood Slide Smear', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• Blood Slide Smear</span><span class="border-b border-gray-300 ml-2 flex-1"></span>
                        </div>
                        
                        <div class="mt-2">
                            <div class="font-medium flex items-center">
                                <input type="checkbox" <?php echo isTestRequested('Urine sedimentary', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                <span>• Urine sedimentary</span>
                            </div>
                            <div class="flex items-center ml-4">
                                <input type="checkbox" <?php echo isTestRequested('Urine appearance', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                <span>Urine appearance</span><span class="border-b border-gray-300 ml-2 flex-1"></span>
                            </div>
                            <div class="flex items-center ml-4">
                                <input type="checkbox" <?php echo isTestRequested('Urine microscopic report', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                <span>Urine microscopic report</span><span class="border-b border-gray-300 ml-2 flex-1"></span>
                            </div>
                        </div>
                        
                        <div class="mt-2">
                            <div class="font-medium flex items-center">
                                <input type="checkbox" <?php echo isTestRequested('Urine Chemistry', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                <span>• Urine Chemistry</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-1 ml-4">
                                <div class="flex items-center">
                                    <input type="checkbox" <?php echo isTestRequested('Leucocytes', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                    <span>Leucocytes</span><span class="border-b border-gray-300 ml-1 flex-1"></span>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" <?php echo isTestRequested('PH', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                    <span>PH</span><span class="border-b border-gray-300 ml-1 flex-1"></span>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" <?php echo isTestRequested('Protein', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                    <span>Protein</span><span class="border-b border-gray-300 ml-1 flex-1"></span>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" <?php echo isTestRequested('Blood', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                    <span>Blood</span><span class="border-b border-gray-300 ml-1 flex-1"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-2">
                            <div class="font-medium flex items-center">
                                <input type="checkbox" <?php echo isTestRequested('Stool analysis', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                <span>• Stool analysis</span>
                            </div>
                            <div class="flex items-center ml-4">
                                <input type="checkbox" <?php echo isTestRequested('Stool appearance', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                <span>Stool appearance</span><span class="border-b border-gray-300 ml-2 flex-1"></span>
                            </div>
                            <div class="flex items-center ml-4">
                                <input type="checkbox" <?php echo isTestRequested('Stool microscopic report', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                <span>Stool microscopic report</span><span class="border-b border-gray-300 ml-2 flex-1"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hematology -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Hematology</h4>
                    <div class="space-y-1">
                        <?php
                        $hb_result = findLabResult($lab_results_map, 'Hemoglobin');
                        $esr_result = findLabResult($lab_results_map, 'ESR');
                        $cbc_result = findLabResult($lab_results_map, 'Complete Blood Count');
                        ?>
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('Hemoglobin', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• Hemoglobin</span>
                            <?php if ($hb_result): ?>
                                <span class="ml-2 font-semibold <?php echo $hb_result['is_normal'] ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo htmlspecialchars($hb_result['result_value']); ?> g/dL
                                </span>
                            <?php else: ?>
                                <span class="flex-1 border-b border-gray-300 mx-2"></span>
                                <span>g/dL</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('ESR', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• ESR</span>
                            <?php if ($esr_result): ?>
                                <span class="ml-2 font-semibold <?php echo $esr_result['is_normal'] ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo htmlspecialchars($esr_result['result_value']); ?>
                                </span>
                            <?php else: ?>
                                <span class="flex-1 border-b border-gray-300 mx-2"></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('Full blood picture', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• Full blood picture</span>
                            <?php if ($cbc_result): ?>
                                <span class="ml-2 font-semibold text-blue-600">
                                    <?php echo htmlspecialchars($cbc_result['result_value'] ?? 'Completed'); ?>
                                </span>
                            <?php else: ?>
                                <span class="flex-1 border-b border-gray-300 mx-2"></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('Others', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>Others</span><span class="border-b border-gray-300 ml-2 flex-1"></span>
                        </div>
                    </div>
                </div>

                <!-- Clinical Chemistry -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Clinical chemistry</h4>
                    <div class="space-y-1">
                        <?php
                        $bs_result = findLabResult($lab_results_map, 'Blood Sugar');
                        $uric_result = findLabResult($lab_results_map, 'uric acid');
                        $rf_result = findLabResult($lab_results_map, 'Rheumatoid');
                        ?>
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('Blood sugar', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• Blood sugar</span>
                            <?php if ($bs_result): ?>
                                <span class="ml-2 font-semibold <?php echo $bs_result['is_normal'] ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo htmlspecialchars($bs_result['result_value']); ?> mmol/L
                                </span>
                            <?php else: ?>
                                <span class="flex-1 border-b border-gray-300 mx-2"></span>
                                <span>mmol/L</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('Blood uric acid', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• Blood uric acid</span>
                            <?php if ($uric_result): ?>
                                <span class="ml-2 font-semibold">
                                    <?php echo htmlspecialchars($uric_result['result_value']); ?>
                                </span>
                            <?php else: ?>
                                <span class="flex-1 border-b border-gray-300 mx-2"></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('Rheumatoid factor', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• Rheumatoid factor</span>
                            <?php if ($rf_result): ?>
                                <span class="ml-2 font-semibold">
                                    <?php echo htmlspecialchars($rf_result['result_value']); ?>
                                </span>
                            <?php else: ?>
                                <span class="flex-1 border-b border-gray-300 mx-2"></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('Others', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• Others</span><span class="border-b border-gray-300 ml-2 flex-1"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <!-- Serology -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Serology</h4>
                    <div class="space-y-1">
                        <?php
                        $hpylori_ag = findLabResult($lab_results_map, 'H.Pylori antigen');
                        $hpylori_ab = findLabResult($lab_results_map, 'H.Pylori antibody');
                        $syphilis = findLabResult($lab_results_map, 'Syphilis');
                        $pregnancy = findLabResult($lab_results_map, 'Pregnancy');
                        $typhoid = findLabResult($lab_results_map, 'Typhoid');
                        ?>
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('H.Pylori antigen', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• H.Pylori antigen</span>
                            <?php if ($hpylori_ag): ?>
                                <span class="ml-2 font-semibold">
                                    <?php echo htmlspecialchars($hpylori_ag['result_value']); ?>
                                </span>
                            <?php else: ?>
                                <span class="flex-1 border-b border-gray-300 mx-2"></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('H.Pylori antibody', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• H.Pylori antibody</span>
                            <?php if ($hpylori_ab): ?>
                                <span class="ml-2 font-semibold">
                                    <?php echo htmlspecialchars($hpylori_ab['result_value']); ?>
                                </span>
                            <?php else: ?>
                                <span class="flex-1 border-b border-gray-300 mx-2"></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('RPP/Syphilis', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• RPP/Syphilis</span>
                            <?php if ($syphilis): ?>
                                <span class="ml-2 font-semibold <?php echo $syphilis['is_normal'] ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo htmlspecialchars($syphilis['result_value']); ?>
                                </span>
                            <?php else: ?>
                                <span class="flex-1 border-b border-gray-300 mx-2"></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('UPT', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• UPT</span>
                            <?php if ($pregnancy): ?>
                                <span class="ml-2 font-semibold <?php echo $pregnancy['result_value'] == 'Positive' ? 'text-blue-600' : 'text-green-600'; ?>">
                                    <?php echo htmlspecialchars($pregnancy['result_value']); ?>
                                </span>
                            <?php else: ?>
                                <span class="flex-1 border-b border-gray-300 mx-2"></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('Salmonella typhi/paratyphi antigen', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• Salmonella typhi/paratyphi antigen</span>
                            <?php if ($typhoid): ?>
                                <span class="ml-2 font-semibold">
                                    <?php echo htmlspecialchars($typhoid['result_value']); ?>
                                </span>
                            <?php else: ?>
                                <span class="flex-1 border-b border-gray-300 mx-2"></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            <div class="flex items-center">
                                <input type="checkbox" <?php echo isTestRequested('STO', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                <span>• STO</span><span class="border-b border-gray-300 ml-1 flex-1"></span>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" <?php echo isTestRequested('STH', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                <span>STH</span><span class="border-b border-gray-300 ml-1 flex-1"></span>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('Rheumatoid Factor', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>• Rheumatoid Factor</span><span class="border-b border-gray-300 ml-2 flex-1"></span>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('Others', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>Others</span><span class="border-b border-gray-300 ml-2 flex-1"></span>
                        </div>
                    </div>
                </div>

                <!-- Blood Transfusion -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Blood transfusion</h4>
                    <div class="space-y-1">
                        <?php
                        $blood_group = findLabResult($lab_results_map, 'Blood Group');
                        ?>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="flex items-center">
                                <input type="checkbox" <?php echo isTestRequested('Blood group', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                <span>• Blood group</span>
                                <?php if ($blood_group): ?>
                                    <span class="ml-2 font-semibold text-blue-600">
                                        <?php echo htmlspecialchars($blood_group['result_value']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="flex-1 border-b border-gray-300 mx-1"></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" <?php echo isTestRequested('Rhesus', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                                <span>Rhesus</span>
                                <?php if ($blood_group && strpos($blood_group['result_value'], '+') !== false): ?>
                                    <span class="ml-2 font-semibold text-blue-600">+</span>
                                <?php elseif ($blood_group && strpos($blood_group['result_value'], '-') !== false): ?>
                                    <span class="ml-2 font-semibold text-blue-600">-</span>
                                <?php else: ?>
                                    <span class="flex-1 border-b border-gray-300 mx-1"></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" <?php echo isTestRequested('Others', $requested_tests) ? 'checked' : ''; ?> disabled class="mr-2">
                            <span>Others</span><span class="border-b border-gray-300 ml-2 flex-1"></span>
                        </div>
                    </div>
                </div>

                <!-- Test Signature -->
                <div class="border border-gray-400 p-3">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="flex items-center">
                            <span>Test performed by</span><span class="border-b border-gray-300 ml-2 flex-1"></span>
                        </div>
                        <div class="flex items-center">
                            <span>Signature</span><span class="border-b border-gray-300 ml-2 flex-1"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    /* Ensure consistent black colors */
    * { color: #000 !important; }
    
    .no-print {
        display: none !important;
    }
    
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
    
    #medicalRecord {
        border: 2px solid #000 !important;
        page-break-inside: avoid;
    }
    
    .border-gray-400 {
        border-color: #000 !important;
    }
    
    .text-green-600 {
        color: #16a34a !important;
    }
    
    .text-red-600 {
        color: #dc2626 !important;
    }
    
    .text-blue-600 {
        color: #2563eb !important;
    }
    
    /* Better page breaks */
    .page-break { page-break-before: always; }
}
</style>

<script>
function printMedicalRecord() {
    window.print();
}
</script>