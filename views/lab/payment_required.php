<?php
$pageTitle = 'Payment Required - Lab';
$userRole = 'lab_technician';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . ($BASE_PATH ?? '') . '/auth/login');
    exit;
}

// Extract variables from passed data
$patient_name = htmlspecialchars(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''));
$patient_id = $patient['id'] ?? ($test['patient_id'] ?? 0);
$registration_number = htmlspecialchars($patient['registration_number'] ?? 'N/A');
$test_name = htmlspecialchars($test['test_name'] ?? 'Unknown Test');
$test_code = htmlspecialchars($test['test_code'] ?? '');
$test_order_id = $test['id'] ?? 0;
$visit_date = $test['visit_date'] ?? $test['created_at'] ?? date('Y-m-d');
$test_price = $test['price'] ?? 0;
?>

<div class="min-h-screen bg-gradient-to-br from-yellow-50 via-white to-orange-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="<?= $BASE_PATH ?>/lab/dashboard" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <div class="p-3 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-xl shadow-lg">
                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                </div>
                Lab Test Payment Required
            </h1>
            <p class="text-gray-600 mt-2">This patient has not paid for the lab test</p>
        </div>

        <div class="max-w-2xl mx-auto">
            <!-- Patient Info Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 mb-6">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-user"></i>
                        Patient Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Patient Name</p>
                            <p class="text-lg font-semibold text-gray-900"><?= $patient_name ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Registration Number</p>
                            <p class="text-lg font-mono text-gray-900"><?= $registration_number ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Info Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 mb-6">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-flask"></i>
                        Test Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Test Name</p>
                            <p class="text-lg font-semibold text-gray-900"><?= $test_name ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Test Code</p>
                            <p class="text-lg font-mono text-gray-900"><?= $test_code ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Test Price</p>
                            <p class="text-lg font-semibold text-blue-600">TZS <?= number_format($test_price) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Payment Status</p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                Not Paid
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warning Card -->
            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl border-2 border-yellow-300 p-6 mb-6">
                <div class="flex items-start gap-4">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-info-circle text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-yellow-800">Lab Test Payment Required</h3>
                        <p class="text-yellow-700 mt-1">
                            This patient must pay for the lab test at the <strong>Accountant</strong> desk before you can proceed with sample collection or testing.
                        </p>
                        <div class="mt-4 p-4 bg-white rounded-lg border border-yellow-200">
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                Please direct the patient to the accountant for payment, then they can return for the test.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workflow Status -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Workflow Progress</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <span class="text-sm text-gray-600">Patient Registration - Completed</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <span class="text-sm text-gray-600">Lab Test Ordered - Completed</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <span class="text-sm font-medium text-gray-900">Lab Test Payment - Pending</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <span class="text-sm text-gray-400">Sample Collection - Locked</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <span class="text-sm text-gray-400">Results Entry - Locked</span>
                    </div>
                </div>
            </div>

            <!-- Emergency Override Section -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-red-200">
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-ambulance"></i>
                        Emergency / Special Case Override
                    </h2>
                </div>
                <div class="p-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <p class="text-red-700 text-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <strong>Warning:</strong> Only use this option for genuine emergencies or special circumstances. 
                            All overrides are logged for audit purposes and will be reviewed by administration.
                        </p>
                    </div>

                    <form method="POST" action="<?= $BASE_PATH ?>/lab/override_payment" class="space-y-4" id="overrideForm">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="test_order_id" value="<?= $test_order_id ?>">
                        <input type="hidden" name="patient_id" value="<?= $patient_id ?>">
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-clipboard text-red-500 mr-2"></i>
                                Reason for Proceeding Without Payment *
                            </label>
                            <select id="override_type" onchange="toggleCustomReason()" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 mb-3">
                                <option value="">-- Select Reason --</option>
                                <option value="Medical Emergency - Urgent test required">Medical Emergency - Urgent test required</option>
                                <option value="Medical Emergency - Critical condition">Medical Emergency - Critical condition</option>
                                <option value="Patient will pay after test">Patient will pay after test</option>
                                <option value="Insurance pending verification">Insurance pending verification</option>
                                <option value="Management approved waiver">Management approved waiver</option>
                                <option value="Free medical camp / charity">Free medical camp / charity</option>
                                <option value="custom">Other (specify below)</option>
                            </select>
                            
                            <textarea name="override_reason" id="override_reason" rows="3" required
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                      placeholder="Please provide a detailed reason for proceeding without payment..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                This reason will be recorded in the audit log and linked to your user account.
                            </p>
                        </div>

                        <div class="flex items-center gap-3 pt-4">
                            <label class="flex items-center">
                                <input type="checkbox" id="confirm_override" required
                                       class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                                <span class="ml-2 text-sm text-gray-700">
                                    I confirm this is a valid reason and understand this action is logged
                                </span>
                            </label>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <button type="submit" id="submit_override" disabled
                                class="w-full sm:flex-1 inline-flex items-center justify-center bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-all font-semibold disabled:opacity-50 disabled:cursor-not-allowed shadow-md">
                                <i class="fas fa-flask mr-2"></i>
                                Proceed with Lab Test (Override)
                            </button>
                            <a href="<?= $BASE_PATH ?>/lab/dashboard" 
                               class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all text-center">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 flex justify-center gap-4">
                <a href="<?= $BASE_PATH ?>/lab/dashboard" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all shadow-md">
                    <i class="fas fa-home mr-2"></i>Return to Dashboard
                </a>
                <a href="<?= $BASE_PATH ?>/lab/tests" 
                   class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all shadow-md">
                    <i class="fas fa-vials mr-2"></i>View All Tests
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle custom reason visibility
function toggleCustomReason() {
    const select = document.getElementById('override_type');
    const textarea = document.getElementById('override_reason');
    
    if (select.value && select.value !== 'custom') {
        textarea.value = select.value;
        validateForm();
    } else if (select.value === 'custom') {
        textarea.value = '';
        textarea.focus();
    }
}

// Enable submit button only when checkbox is checked and reason provided
function validateForm() {
    const reason = document.getElementById('override_reason').value.trim();
    const confirmed = document.getElementById('confirm_override').checked;
    document.getElementById('submit_override').disabled = !(confirmed && reason.length >= 10);
}

document.getElementById('confirm_override').addEventListener('change', validateForm);
document.getElementById('override_reason').addEventListener('input', validateForm);

// Validate form before submit
document.getElementById('overrideForm').addEventListener('submit', function(e) {
    const reason = document.getElementById('override_reason').value.trim();
    const confirmed = document.getElementById('confirm_override').checked;
    
    if (!reason) {
        e.preventDefault();
        alert('Please provide a reason for the override');
        return false;
    }
    
    if (!confirmed) {
        e.preventDefault();
        alert('Please confirm that you understand this action is logged');
        return false;
    }
    
    if (reason.length < 10) {
        e.preventDefault();
        alert('Please provide a more detailed reason (at least 10 characters)');
        return false;
    }
});
</script>
