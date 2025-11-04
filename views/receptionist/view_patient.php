<!-- Enhanced Patient Dashboard for Returning Patients -->
<div class="mb-6 no-print">
    <!-- Patient Overview Card -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h1>
                    <p class="text-blue-100">Reg: <?php echo htmlspecialchars($patient['registration_number']); ?> | 
                        Age: <?php 
                        $dob = $patient['date_of_birth'] ?? null;
                        if (!empty($dob)) {
                            echo date_diff(date_create($dob), date_create('today'))->y;
                        } else {
                            echo 'N/A';
                        }
                        ?> | 
                        <?php echo htmlspecialchars($patient['phone'] ?? 'No phone'); ?>
                    </p>
                    <div class="flex items-center mt-2 space-x-4">
                        <?php 
                        $lastVisit = !empty($consultations) ? $consultations[0]['created_at'] : null;
                        $visitCount = count($consultations ?? []);
                        ?>
                        <span class="bg-black bg-opacity-20 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-calendar mr-1"></i>
                            Last Visit: <?php echo $lastVisit ? date('d/m/Y', strtotime($lastVisit)) : 'First Visit'; ?>
                        </span>
                        <span class="bg-black bg-opacity-20 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-history mr-1"></i>
                            Total Visits: <?php echo $visitCount; ?>
                        </span>
                        <?php if ($visitCount > 1): ?>
                        <span class="bg-green-500 bg-opacity-80 px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fas fa-star mr-1"></i>Returning Patient
                        </span>
                        <?php else: ?>
                        <span class="bg-black-500 bg-opacity-80 px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fas fa-user-plus mr-1"></i>New Patient
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Status Indicators -->
            <div class="text-right">
                <div class="space-y-2">
                    <?php 
                    $hasUnpaidBills = false; // TODO: Calculate from payments
                    $hasActivePresciption = !empty($latest_consultation['prescription']);
                    $needsFollowup = true; // TODO: Check if follow-up is needed
                    ?>
                    
                    <?php if ($hasUnpaidBills): ?>
                    <div class="bg-red-500 bg-opacity-20 border border-red-300 px-3 py-1 rounded text-sm">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Outstanding Balance
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($hasActivePresciption): ?>
                    <div class="bg-purple-500 bg-opacity-20 border border-purple-300 px-3 py-1 rounded text-sm">
                        <i class="fas fa-pills mr-1"></i>Active Prescription
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($needsFollowup): ?>
                    <div class="bg-orange-500 bg-opacity-20 border border-orange-300 px-3 py-1 rounded text-sm">
                        <i class="fas fa-calendar-check mr-1"></i>Follow-up Due
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="bg-white rounded-lg shadow-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-bolt mr-2 text-yellow-500"></i>Quick Actions
            </h2>
            <div class="flex space-x-2">
                <button onclick="printMedicalRecord()"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                    <i class="fas fa-print mr-1"></i>Print Record
                </button>
                
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/create_revisit?patient_id=<?php echo $patient['id']; ?>"
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                    <i class="fas fa-user-check mr-1"></i>Create Revisit
                </a>
                
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/payments?patient_id=<?php echo $patient['id']; ?>&step=consultation"
                   class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                    <i class="fas fa-credit-card mr-1"></i>Process Payment
                </a>
                
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/appointments?patient_id=<?php echo $patient['id']; ?>"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                    <i class="fas fa-calendar-plus mr-1"></i>New Appointment
                </a>
                
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/dispense_medicines?patient_id=<?php echo $patient['id']; ?>"
                   class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                    <i class="fas fa-pills mr-1"></i>Dispense Medicine
                </a>
                
                <button onclick="updateContactInfo()" 
                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                    <i class="fas fa-edit mr-1"></i>Update Info
                </button>
                
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/patients" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Patients
                </a>
            </div>
        </div>
    </div>

    <!-- Visit History Timeline (for returning patients) -->
    <?php if (count($consultations ?? []) > 1): ?>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-history mr-3 text-blue-600"></i>
            Recent Visit History
        </h2>
        <div class="space-y-4 max-h-60 overflow-y-auto">
            <?php foreach (array_slice($consultations, 0, 5) as $index => $consultation): ?>
            <div class="flex items-start space-x-4 p-3 <?php echo $index === 0 ? 'bg-blue-50 border-l-4 border-blue-500' : 'bg-gray-50'; ?> rounded-lg">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-md text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h4 class="font-medium text-gray-900">
                            <?php echo $index === 0 ? 'Current Visit' : 'Visit #' . ($visitCount - $index); ?>
                        </h4>
                        <span class="text-sm text-gray-500">
                            <?php echo date('d M Y', strtotime($consultation['created_at'])); ?>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        <strong>Chief Complaint:</strong> <?php echo htmlspecialchars($consultation['main_complaint'] ?? 'Not recorded'); ?>
                    </p>
                    <?php if (!empty($consultation['final_diagnosis'])): ?>
                    <p class="text-sm text-gray-600">
                        <strong>Diagnosis:</strong> <?php echo htmlspecialchars($consultation['final_diagnosis']); ?>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($consultation['prescription'])): ?>
                    <p class="text-sm text-gray-600">
                        <strong>Treatment:</strong> <?php echo htmlspecialchars(substr($consultation['prescription'], 0, 100)); ?><?php echo strlen($consultation['prescription']) > 100 ? '...' : ''; ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($consultations) > 5): ?>
        <div class="mt-4 text-center">
            <button onclick="showAllVisits()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                <i class="fas fa-chevron-down mr-1"></i>Show all <?php echo count($consultations); ?> visits
            </button>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Visit History and Actions -->
<div class="mb-6 no-print">
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-history mr-3 text-indigo-600"></i>
                Patient Visit History
            </h2>
            <p class="text-gray-600 mt-1">Complete record of all patient visits and consultations</p>
        </div>

        <?php if (!empty($consultations)): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chief Complaint</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnosis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($consultations as $index => $consultation): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                    <?php echo $index + 1; ?>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        Visit #<?php echo $index + 1; ?>
                                    </div>
                                    <?php if ($index === 0): ?>
                                    <div class="text-xs text-green-600 font-medium">Most Recent</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">
                                <?php echo date('d M Y', strtotime($consultation['created_at'])); ?>
                            </div>
                            <div class="text-xs text-gray-500">
                                <?php echo date('H:i A', strtotime($consultation['created_at'])); ?>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs">
                                <?php echo htmlspecialchars(substr($consultation['main_complaint'] ?? 'Not recorded', 0, 80)); ?>
                                <?php if (strlen($consultation['main_complaint'] ?? '') > 80): ?>...<?php endif; ?>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs">
                                <?php 
                                $diagnosis = $consultation['final_diagnosis'] ?? $consultation['preliminary_diagnosis'] ?? 'Pending';
                                echo htmlspecialchars(substr($diagnosis, 0, 60));
                                if (strlen($diagnosis) > 60): ?>...<?php endif; ?>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <?php echo htmlspecialchars($consultation['doctor_name'] ?? 'Dr. Unknown'); ?>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $hasVitalSigns = false;
                            $hasPrescription = !empty($consultation['prescription']);
                            $hasLabResults = !empty($consultation['lab_investigation']);
                            
                            // Check if vital signs exist for this consultation
                            if (!empty($vital_signs)) {
                                foreach ($vital_signs as $vs) {
                                    if (is_array($vs) && isset($vs['consultation_id']) && $vs['consultation_id'] == $consultation['id']) {
                                        $hasVitalSigns = true;
                                        break;
                                    }
                                }
                            }
                            ?>
                            <div class="flex flex-col space-y-1">
                                <?php if ($hasVitalSigns): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-heartbeat mr-1"></i>Vitals Recorded
                                </span>
                                <?php endif; ?>
                                
                                <?php if ($hasPrescription): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-pills mr-1"></i>Prescribed
                                </span>
                                <?php endif; ?>
                                
                                <?php if ($hasLabResults): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-vial mr-1"></i>Lab Tests
                                </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col space-y-2">
                                <!-- View Medical Form Button -->
                                <button onclick="viewMedicalForm(<?php echo $consultation['id']; ?>, <?php echo $index; ?>)" 
                                        class="inline-flex items-center px-3 py-1 border border-blue-300 rounded-md text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-file-medical mr-1"></i>View Medical Form
                                </button>
                                
                                <!-- Print Medical Form Button -->
                                <button onclick="printMedicalForm(<?php echo $consultation['id']; ?>, <?php echo $index; ?>)" 
                                        class="inline-flex items-center px-3 py-1 border border-green-300 rounded-md text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 transition-colors">
                                    <i class="fas fa-print mr-1"></i>Print Form
                                </button>
                                
                                <!-- View Details Button -->
                                <button onclick="viewVisitDetails(<?php echo $consultation['id']; ?>)" 
                                        class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>View Details
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php else: ?>
        <div class="p-12 text-center">
            <i class="fas fa-calendar-times text-gray-400 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Visit History</h3>
            <p class="text-gray-500 mb-6">This patient hasn't had any consultations yet.</p>
            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/appointments?patient_id=<?php echo $patient['id']; ?>" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Schedule First Appointment
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Medical Form Modal (Hidden by default) -->
<div id="medicalFormModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <!-- Modal positioned to account for sidebar on larger screens -->
        <div class="bg-white rounded-lg w-full max-w-6xl max-h-[95vh] overflow-y-auto mx-4 lg:ml-72 lg:mr-8 shadow-2xl">
            <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
                <h3 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-file-medical text-blue-600 mr-2"></i>
                    Medical Form
                </h3>
                <div class="flex space-x-2">
                    <button onclick="printCurrentMedicalForm()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm transition-colors shadow-sm">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                    <button onclick="closeMedicalFormModal()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm transition-colors shadow-sm">
                        <i class="fas fa-times mr-2"></i>Close
                    </button>
                </div>
            </div>
            <div id="medicalFormContent" class="p-6">
            <!-- Medical form content will be loaded here -->
        </div>
    </div>
</div>

<!-- Enhanced Payment History Section (Receptionist-focused) -->
<div class="mb-6 no-print">
    <div class="bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-credit-card mr-3 text-green-600"></i>
                    Financial Summary
                </h2>
                
                <!-- Financial Status Cards -->
                <div class="flex space-x-4">
                    <?php 
                    $totalPaid = 0;
                    $totalPending = 0;
                    foreach ($payments ?? [] as $payment) {
                        if ($payment['payment_status'] === 'paid') {
                            $totalPaid += $payment['amount'];
                        } else {
                            $totalPending += $payment['amount'];
                        }
                    }
                    ?>
                    
                    <div class="bg-white rounded-lg px-4 py-2 shadow-sm">
                        <div class="text-sm text-gray-600">Total Paid</div>
                        <div class="text-lg font-bold text-green-600">TZS <?php echo number_format($totalPaid, 2); ?></div>
                    </div>
                    
                    <?php if ($totalPending > 0): ?>
                    <div class="bg-white rounded-lg px-4 py-2 shadow-sm">
                        <div class="text-sm text-gray-600">Outstanding</div>
                        <div class="text-lg font-bold text-red-600">TZS <?php echo number_format($totalPending, 2); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($payments)): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($payments as $payment): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo safe_date('d M Y', $payment['payment_date']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas <?php echo $payment['payment_type'] === 'consultation' ? 'fa-user-md' : ($payment['payment_type'] === 'lab_tests' ? 'fa-vial' : 'fa-pills'); ?> mr-2 text-blue-500"></i>
                                    <span class="text-sm text-gray-900"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $payment['payment_type']))); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <span class="<?php echo $payment['payment_status'] === 'paid' ? 'text-green-600' : 'text-red-600'; ?>">
                                    TZS <?php echo number_format($payment['amount'], 2); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $payment['payment_method']))); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    <?php 
                                    if ($payment['payment_status'] === 'paid') {
                                        echo 'bg-green-100 text-green-800 border border-green-200';
                                    } elseif ($payment['payment_status'] === 'pending') {
                                        echo 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                                    } else {
                                        echo 'bg-red-100 text-red-800 border border-red-200';
                                    }
                                    ?>">
                                    <?php if ($payment['payment_status'] === 'paid'): ?>
                                        <i class="fas fa-check-circle mr-1"></i>
                                    <?php elseif ($payment['payment_status'] === 'pending'): ?>
                                        <i class="fas fa-clock mr-1"></i>
                                    <?php else: ?>
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars(ucfirst($payment['payment_status'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if ($payment['payment_status'] !== 'paid'): ?>
                                    <button onclick="processPayment(<?php echo $payment['id']; ?>, '<?php echo htmlspecialchars($payment['payment_type']); ?>')" 
                                            class="text-green-600 hover:text-green-900 font-medium">
                                        <i class="fas fa-credit-card mr-1"></i>Collect Payment
                                    </button>
                                <?php else: ?>
                                    <button onclick="printReceipt(<?php echo $payment['id']; ?>)" 
                                            class="text-blue-600 hover:text-blue-900 font-medium">
                                        <i class="fas fa-receipt mr-1"></i>Print Receipt
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fas fa-credit-card text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Payment History</h3>
                <p class="text-gray-500 mb-6">This patient hasn't made any payments yet.</p>
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/payments?patient_id=<?php echo $patient['id']; ?>" 
                   class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create First Payment Record
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript for Enhanced Functionality -->
<script>
// Lab orders data for real-time updates
const labOrders = <?php echo json_encode($lab_orders ?? []); ?>;

// Medical form modal functionality
function viewMedicalForm(consultationId, visitIndex) {
    showToast(`Loading medical form for Visit ${visitIndex + 1}...`, 'info');
    
    // Here you would typically fetch the medical form via AJAX
    // For now, we'll generate it with the current consultation data
    generateMedicalForm(consultationId, visitIndex);
}

function generateMedicalForm(consultationId, visitIndex) {
    // Generate the medical form HTML for the specific consultation
    const medicalFormHTML = `
        <div class="bg-white border-2 border-gray-300 p-8 print:border-none print:p-4">
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
                <h2 class="text-xl font-bold text-center mb-4 underline">PATIENT RECORD - VISIT ${visitIndex + 1}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 text-sm">
                    <div class="flex items-center">
                        <span class="font-medium mr-2">DATE:</span>
                        <span class="border-b border-gray-400 flex-1 px-2">${new Date().toLocaleDateString('en-GB')}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium mr-2">REG NO:</span>
                        <span class="border-b border-gray-400 flex-1 px-2"><?php echo htmlspecialchars($patient['registration_number']); ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4 text-sm">
                    <div class="lg:col-span-2 flex items-center">
                        <span class="font-medium mr-2">PATIENT NAME:</span>
                        <span class="border-b border-gray-400 flex-1 px-2"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium mr-2">AGE:</span>
                        <span class="border-b border-gray-400 flex-1 px-2 mr-4"><?php 
                        $dob = $patient['date_of_birth'] ?? null;
                        if (!empty($dob)) {
                            echo date_diff(date_create($dob), date_create('today'))->y;
                        } else {
                            echo 'N/A';
                        }
                        ?></span>
                        <span class="font-medium mr-2">SEX:</span>
                        <span class="border-b border-gray-400 px-2"><?php echo strtoupper(substr($patient['gender'] ?? 'U', 0, 1)); ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 text-sm">
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

            <!-- Medical Form Template (this would be filled with actual data in a real implementation) -->
            <div class="mb-6">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
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
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="font-medium mb-1">Pulse Rate</div>
                        <div class="border border-gray-400 h-10 p-2 text-center">
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="font-medium mb-1">Body Weight</div>
                        <div class="border border-gray-400 h-10 p-2 text-center">
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="font-medium mb-1">Height</div>
                        <div class="border border-gray-400 h-10 p-2 text-center">
                        </div>
                    </div>
                </div>
            </div>

            ${labOrders && labOrders.length > 0 ? `
            <!-- Requested Lab Tests Summary -->
            <div class="mb-4 border border-blue-400 bg-blue-50 p-3">
                <h4 class="font-bold mb-2 text-blue-800">Lab Tests Requested for This Visit:</h4>
                <div class="grid grid-cols-3 gap-2 text-xs">
                    ${labOrders.map(order => `
                        <div class="flex items-center">
                            ${order.result_completed_at ? 
                                `<input type="checkbox" checked disabled class="mr-2">
                                <span class="text-green-700 font-semibold">${order.test_name}</span>` :
                                order.status === 'in_progress' ?
                                `<input type="checkbox" disabled class="mr-2">
                                <span class="text-blue-600">${order.test_name} (In Progress)</span>` :
                                `<input type="checkbox" disabled class="mr-2">
                                <span class="text-gray-600">${order.test_name} (Pending)</span>`
                            }
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}

            <!-- Note: Full medical form template would continue here -->
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-file-medical text-4xl mb-4"></i>
                <p>Complete medical form for Visit ${visitIndex + 1}</p>
                <p class="text-sm">This would contain all consultation details, vital signs, and lab results</p>
            </div>
        </div>
    `;
    
    document.getElementById('medicalFormContent').innerHTML = medicalFormHTML;
    
    // Show modal and adjust positioning for different screen sizes
    const modal = document.getElementById('medicalFormModal');
    modal.classList.remove('hidden');
    
    // Ensure proper focus management for accessibility
    const closeButton = modal.querySelector('button[onclick="closeMedicalFormModal()"]');
    if (closeButton) {
        closeButton.focus();
    }
}

function printMedicalForm(consultationId, visitIndex) {
    // Print medical form for specific consultation
    showToast(`Preparing medical form for printing - Visit ${visitIndex + 1}`, 'info');
    
    // Generate and show the form first, then print
    generateMedicalForm(consultationId, visitIndex);
    
    // Wait a moment for modal to show, then print
    setTimeout(() => {
        printCurrentMedicalForm();
    }, 500);
}

function printCurrentMedicalForm() {
    const printContent = document.getElementById('medicalFormContent');
    if (!printContent) {
        showToast('No medical form to print', 'error');
        return;
    }
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Medical Record</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                .text-center { text-align: center; }
                .font-bold { font-weight: bold; }
                .mb-2 { margin-bottom: 8px; }
                .mb-4 { margin-bottom: 16px; }
                .mb-6 { margin-bottom: 24px; }
                .border-b-2 { border-bottom: 2px solid #000; }
                .border { border: 1px solid #000; }
                .border-gray-400 { border-color: #9ca3af; }
                .border-b { border-bottom: 1px solid #000; }
                .grid { display: grid; }
                .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
                .grid-cols-5 { grid-template-columns: repeat(5, minmax(0, 1fr)); }
                .gap-4 { gap: 16px; }
                .flex { display: flex; }
                .items-center { align-items: center; }
                .justify-between { justify-content: space-between; }
                .p-2 { padding: 8px; }
                .p-8 { padding: 32px; }
                .px-2 { padding-left: 8px; padding-right: 8px; }
                .py-4 { padding-top: 16px; padding-bottom: 16px; }
                .pb-4 { padding-bottom: 16px; }
                .mr-2 { margin-right: 8px; }
                .h-20 { height: 80px; }
                .flex-1 { flex: 1 1 0%; }
                .col-span-2 { grid-column: span 2 / span 2; }
                .text-xl { font-size: 1.25rem; }
                .text-2xl { font-size: 1.5rem; }
                .text-sm { font-size: 0.875rem; }
                .underline { text-decoration: underline; }
                @media print {
                    body { margin: 0; padding: 10px; }
                    .border, .border-gray-400 { border-color: #000 !important; }
                }
            </style>
        </head>
        <body>
            ${printContent.innerHTML}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
    
    showToast('Medical form sent to printer', 'success');
}

function closeMedicalFormModal() {
    const modal = document.getElementById('medicalFormModal');
    modal.classList.add('hidden');
    
    // Return focus to the trigger button if available
    const activeElement = document.activeElement;
    if (activeElement && activeElement.blur) {
        activeElement.blur();
    }
}

// Handle escape key to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('medicalFormModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeMedicalFormModal();
        }
    }
});

function viewVisitDetails(consultationId) {
    // Navigate to detailed consultation view
    window.location.href = `<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/consultation_details?id=${consultationId}`;
}

function updateContactInfo() {
    // Create a modal to update patient contact information
    const modal = `
        <div id="contactModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Update Contact Information</h3>
                <form id="contactForm">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" id="phone" value="<?php echo htmlspecialchars($patient['phone'] ?? ''); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" value="<?php echo htmlspecialchars($patient['email'] ?? ''); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea id="address" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($patient['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="flex space-x-3 mt-6">
                        <button type="button" onclick="closeContactModal()" 
                                class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modal);
    
    // Handle form submission
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        showToast('Contact information updated successfully!', 'success');
        closeContactModal();
    });
}

function closeContactModal() {
    const modal = document.getElementById('contactModal');
    if (modal) {
        modal.remove();
    }
}

function showAllVisits() {
    window.location.href = '<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/patient_history?id=<?php echo $patient['id']; ?>';
}

function processPayment(paymentId, paymentType) {
    window.location.href = `<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/payments?payment_id=${paymentId}&type=${paymentType}`;
}

function printReceipt(paymentId) {
    window.open(`<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/print_receipt?payment_id=${paymentId}`, '_blank');
}

// Show toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white max-w-sm`;
    
    const bgColors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    toast.classList.add(bgColors[type] || bgColors.info);
    toast.innerHTML = `
        <div class="flex items-center space-x-3">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('medicalFormModal');
    if (e.target === modal) {
        closeMedicalFormModal();
    }
});
</script>

<style>
/* Responsive modal positioning */
#medicalFormModal {
    z-index: 9999;
}

/* Mobile devices (up to 768px) */
@media (max-width: 768px) {
    #medicalFormModal .bg-white {
        margin: 1rem !important;
        max-width: calc(100vw - 2rem) !important;
        max-height: calc(100vh - 2rem) !important;
    }
}

/* Tablet devices (769px to 1023px) */
@media (min-width: 769px) and (max-width: 1023px) {
    #medicalFormModal .bg-white {
        margin-left: 2rem !important;
        margin-right: 2rem !important;
        max-width: calc(100vw - 4rem) !important;
    }
}

/* Desktop with sidebar (1024px and up) */
@media (min-width: 1024px) {
    #medicalFormModal .bg-white {
        margin-left: 18rem !important; /* Account for sidebar width (256px + padding) */
        margin-right: 2rem !important;
        max-width: calc(100vw - 20rem) !important;
    }
}

/* Large screens */
@media (min-width: 1440px) {
    #medicalFormModal .bg-white {
        margin-left: 18rem !important;
        margin-right: 4rem !important;
        max-width: calc(100vw - 22rem) !important;
    }
}

/* Ensure modal content is scrollable */
#medicalFormContent {
    max-height: calc(95vh - 140px); /* Subtract header and button heights */
    overflow-y: auto;
}

/* Print styles */
@media print {
    #medicalFormModal {
        position: static !important;
        background: transparent !important;
    }
    
    #medicalFormModal .bg-white {
        margin: 0 !important;
        max-width: 100% !important;
        max-height: none !important;
        box-shadow: none !important;
    }
    
    .no-print {
        display: none !important;
    }
}