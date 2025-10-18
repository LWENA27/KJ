<div class="space-y-6">
    <?php
    // Ensure journey data is available
    if (!isset($journey) || !is_array($journey)) {
        $journey = ['workflow' => null, 'consultations' => [], 'lab_results' => [], 'payments' => []];
    }

    // Ensure workflow data exists
    $workflow = $journey['workflow'] ?? null;
    ?>
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Patient Journey</h1>
        <div class="flex space-x-3">
            <button onclick="printJourney()"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-print mr-2"></i>Print Journey
            </button>
            <a href="<?= $BASE_PATH ?>/doctor/view_patient/<?php echo $patient['id']; ?>"
               class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-user mr-2"></i>View Patient
            </a>
            <a href="<?= $BASE_PATH ?>/doctor/patients" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i>Back to Patients
            </a>
        </div>
    </div>

    <!-- Patient Basic Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-user mr-3 text-blue-600"></i>Patient Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    <?php echo safe_date('M j, Y', $patient['date_of_birth'], 'N/A'); ?>
                    (<?php echo date_diff(date_create($patient['date_of_birth']), date_create('today'))->y; ?> years old)
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo htmlspecialchars($patient['phone'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Registration Date</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo safe_date('M j, Y H:i', $patient['created_at'], ''); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Status</label>
                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                    <?php
                    $workflow = $journey['workflow'] ?? null;
                    $current_step = $workflow ? ($workflow['current_step'] ?? 'registered') : 'registered';
                    switch ($current_step) {
                        case 'registered':
                            echo 'bg-yellow-100 text-yellow-800';
                            break;
                        case 'consultation_started':
                            echo 'bg-blue-100 text-blue-800';
                            break;
                        case 'lab_testing':
                            echo 'bg-purple-100 text-purple-800';
                            break;
                        case 'results_review':
                            echo 'bg-orange-100 text-orange-800';
                            break;
                        case 'medicine_prescribed':
                            echo 'bg-green-100 text-green-800';
                            break;
                        case 'final_payment_collected':
                            echo 'bg-gray-100 text-gray-800';
                            break;
                        case 'completed':
                            echo 'bg-gray-100 text-gray-800';
                            break;
                        default:
                            echo 'bg-gray-100 text-gray-800';
                    }
                    ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $current_step)); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Patient Journey Timeline -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Patient Journey Timeline</h3>
        </div>
        <div class="p-6">
            <div class="flow-root">
                <ul role="list" class="-mb-8">

                    <!-- Registration Step -->
                    <li>
                        <div class="relative pb-8">
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                        <i class="fas fa-user-plus text-white text-sm"></i>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900">Patient Registration</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Receptionist
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500">
                                        Patient registered with vital signs collected
                                    </p>
                                    <div class="mt-2 text-sm text-gray-700">
                                        <p><strong>Vitals:</strong>
                                            Temp: <?php echo $patient['temperature'] ?? 'N/A'; ?>°C,
                                            BP: <?php echo $patient['blood_pressure'] ?? 'N/A'; ?>,
                                            Pulse: <?php echo $patient['pulse_rate'] ?? 'N/A'; ?> bpm
                                        </p>
                                        <p><strong>Payment:</strong> <?php echo $workflow ? (($workflow['consultation_registration_paid'] ?? false) ? 'Paid' : 'Pending') : 'Pending'; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- Consultation Step -->
                    <?php if (!empty($journey['consultations'])): ?>
                    <?php foreach ($journey['consultations'] as $consultation): ?>
                    <li>
                        <div class="relative pb-8">
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                        <i class="fas fa-stethoscope text-white text-sm"></i>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900">Doctor Consultation</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Dr. <?php echo htmlspecialchars($consultation['doctor_first'] . ' ' . $consultation['doctor_last']); ?>
                                        </span>
                                        <?php $apt = $consultation['appointment_date'] ?? $consultation['visit_date'] ?? $consultation['created_at']; ?>
                                        <span class="text-xs text-gray-500"><?php echo date('M j, Y H:i', strtotime($apt)); ?></span>
                                    </div>
                                    <?php if (!empty($consultation['main_complaint'])): ?>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <strong>M/C:</strong> <?php echo htmlspecialchars($consultation['main_complaint']); ?>
                                    </p>
                                    <?php endif; ?>
                                    <?php if (!empty($consultation['final_diagnosis'])): ?>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <strong>Diagnosis:</strong> <?php echo htmlspecialchars($consultation['final_diagnosis']); ?>
                                    </p>
                                    <?php endif; ?>
                                    <?php if (!empty($consultation['prescription'])): ?>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <strong>RX:</strong> <?php echo htmlspecialchars($consultation['prescription']); ?>
                                    </p>
                                    <?php endif; ?>
                                    <?php if (!empty($consultation['lab_investigation'])): ?>
                                    <p class="text-sm text-orange-600 mt-1">
                                        <strong>Lab Tests Ordered:</strong> <?php echo htmlspecialchars($consultation['lab_investigation']); ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Lab Tests Step -->
                    <?php if (!empty($journey['lab_results'])): ?>
                    <?php foreach ($journey['lab_results'] as $result): ?>
                    <li>
                        <div class="relative pb-8">
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                        <i class="fas fa-flask text-white text-sm"></i>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900">Lab Test: <?php echo htmlspecialchars($result['test_name']); ?></p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Lab Technician
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500">
                                        Result: <?php echo htmlspecialchars($result['result_value'] ?? 'Pending'); ?>
                                    </p>
                                    <?php if (!empty($result['result_text'])): ?>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <?php echo htmlspecialchars($result['result_text']); ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Payment Step -->
                    <?php if (!empty($journey['payments'])): ?>
                    <?php foreach ($journey['payments'] as $payment): ?>
                    <li>
                        <div class="relative pb-8">
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                        <i class="fas fa-credit-card text-white text-sm"></i>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1 pt-1.5">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900">Payment Processed</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Receptionist
                                        </span>
                                        <span class="text-xs text-gray-500"><?php echo date('M j, Y H:i', strtotime($payment['payment_date'])); ?></span>
                                    </div>
                                    <p class="text-sm text-gray-500">
                                        Amount: TZS <?php echo number_format($payment['amount'], 2); ?> (<?php echo htmlspecialchars($payment['payment_method']); ?>)
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        Status: <?php echo ucfirst($payment['status']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </div>

    <!-- Workflow Status Summary -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-clipboard-list mr-3 text-indigo-600"></i>Workflow Status Summary
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 mb-2">
                    <i class="fas fa-user-plus text-blue-600"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Registration</p>
                <p class="text-xs text-gray-500"><?php echo $workflow ? (($workflow['consultation_registration_paid'] ?? false) ? 'Completed' : 'Pending') : 'Pending'; ?></p>
            </div>
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 mb-2">
                    <i class="fas fa-stethoscope text-green-600"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Consultation</p>
                <p class="text-xs text-gray-500"><?php echo !empty($journey['consultations']) ? 'Completed' : 'Pending'; ?></p>
            </div>
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 mb-2">
                    <i class="fas fa-flask text-purple-600"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Lab Tests</p>
                <p class="text-xs text-gray-500"><?php echo $workflow ? (($workflow['lab_tests_required'] ?? false) ? (!empty($journey['lab_results']) ? 'Completed' : 'In Progress') : 'Not Required') : 'Not Required'; ?></p>
            </div>
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-100 mb-2">
                    <i class="fas fa-credit-card text-yellow-600"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Payment</p>
                <p class="text-xs text-gray-500"><?php echo $workflow ? (($workflow['final_payment_collected'] ?? false) ? 'Completed' : 'Pending') : 'Pending'; ?></p>
            </div>
        </div>
    </div>
</div>

<script>
function printJourney() {
    window.print();
}
</script>
