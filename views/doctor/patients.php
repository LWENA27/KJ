<div class="space-y-6">
    <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Patients</h1>
        <a href="<?= $BASE_PATH ?>/doctor/patients" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white px-4 py-3 rounded-md text-center font-medium">
            <i class="fas fa-search mr-2"></i>Search Patients
        </a>
    </div>

    <?php if (empty($patients)): ?>
    <!-- No Patients Message -->
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <div class="flex justify-center mb-4">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-md text-blue-600 text-4xl"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">No Patients Yet</h3>
        <p class="text-gray-600 text-lg">You don't have any patients registered yet.</p>
        <p class="text-gray-500 mt-2">Patients will appear here once they register and pay for consultation.</p>
    </div>
    <?php else: ?>

    <!-- Patients Display -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Patient List (<?php echo count($patients); ?> total)</h3>
        </div>
        
        <!-- Desktop Grouped View -->
        <div class="hidden lg:block">
            <?php
                // Define status groups and their display labels / classes
                $status_order = [
                    'registered' => ['label' => 'Registered', 'classes' => 'bg-blue-50 text-blue-800', 'icon' => 'fa-user-plus'],
                    'consultation_started' => ['label' => 'In Consultation', 'classes' => 'bg-yellow-50 text-yellow-800', 'icon' => 'fa-user-md'],
                    'results_review' => ['label' => 'Results Review', 'classes' => 'bg-orange-50 text-orange-800', 'icon' => 'fa-file-medical-alt'],
                    'lab_testing' => ['label' => 'Lab Testing', 'classes' => 'bg-purple-50 text-purple-800', 'icon' => 'fa-flask'],
                    'medicine_prescribed' => ['label' => 'Medicine Prescribed', 'classes' => 'bg-green-50 text-green-800', 'icon' => 'fa-pills'],
                    'final_payment_collected' => ['label' => 'Finalized / Paid', 'classes' => 'bg-gray-50 text-gray-800', 'icon' => 'fa-check-circle'],
                    'completed' => ['label' => 'Completed', 'classes' => 'bg-gray-50 text-gray-800', 'icon' => 'fa-clipboard-check'],
                ];

                // Group patients by status
                $groups = [];
                foreach ($patients as $p) {
                    $s = $p['workflow_status'] ?? 'registered';
                    if (!isset($groups[$s])) $groups[$s] = [];
                    $groups[$s][] = $p;
                }

                foreach ($status_order as $status_key => $meta):
                    $group_list = $groups[$status_key] ?? [];
                    // Skip empty groups
                    if (empty($group_list)) continue;
            ?>
            <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-white to-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-sm">
                            <i class="fas <?php echo $meta['icon']; ?> text-gray-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900"><?php echo $meta['label']; ?></h3>
                            <p class="text-sm text-gray-500"><?php echo count($group_list); ?> patient(s)</p>
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm font-medium">
                            <?php echo ucfirst(str_replace('_', ' ', $status_key)); ?>
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consultations</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($group_list as $patient): ?>
                                <?php
                                    $dob_raw = $patient['date_of_birth'] ?? null;
                                    $dob_readable = 'N/A';
                                    $age = 'N/A';
                                    if (!empty($dob_raw) && strtotime($dob_raw) !== false) {
                                        $dob_readable = date('M j, Y', strtotime($dob_raw));
                                        $age = date_diff(date_create($dob_raw), date_create('today'))->y . ' years';
                                    }
                                    $status = $patient['workflow_status'] ?? 'registered';
                                    $status_classes = 'bg-gray-100 text-gray-800';
                                    switch ($status) {
                                        case 'registered': $status_classes = 'bg-blue-100 text-blue-800'; break;
                                        case 'consultation_started': $status_classes = 'bg-yellow-100 text-yellow-800'; break;
                                        case 'lab_testing': $status_classes = 'bg-purple-100 text-purple-800'; break;
                                        case 'results_review': $status_classes = 'bg-orange-100 text-orange-800'; break;
                                        case 'medicine_prescribed': $status_classes = 'bg-green-100 text-green-800'; break;
                                        case 'final_payment_collected': case 'completed': $status_classes = 'bg-gray-100 text-gray-800'; break;
                                    }
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-white font-semibold text-blue-600">
                                                    <?php echo strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)); ?>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">DOB: <?php echo htmlspecialchars($dob_readable); ?> <span class="text-xs text-gray-400">(<?php echo htmlspecialchars($age); ?>)</span></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($patient['email'] ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo (int)($patient['consultation_count'] ?? 0); ?> visits</td>
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="inline-flex px-2 py-1 text-xs font-medium rounded-full <?php echo $status_classes; ?>"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $status))); ?></span></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <button onclick="viewPatientDetails(<?php echo $patient['id']; ?>)" class="btn-primary">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </button>
                                            <?php if ($status === 'results_review'): ?>
                                                <button onclick="viewLabResults(<?php echo $patient['id']; ?>)" class="btn-action btn-lab">
                                                    <i class="fas fa-vial mr-1"></i>Results
                                                </button>
                                                <button onclick="reviewResults(<?php echo $patient['id']; ?>)" class="btn-action btn-ghost">
                                                    <i class="fas fa-clipboard-check mr-1"></i>Review
                                                </button>
                                            <?php else: ?>
                                                <button onclick="attendPatient(<?php echo $patient['id']; ?>)" class="btn-action btn-allocate">
                                                    <i class="fas fa-stethoscope mr-1"></i>Attend
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Mobile Grouped Cards View -->
        <div class="lg:hidden mobile-patients-cards p-4">
            <?php
                // Reuse the same grouping logic for mobile: show panels per status
                $status_order_mobile = [
                    'registered' => ['label' => 'Registered', 'color' => 'blue'],
                    'consultation_started' => ['label' => 'In Consultation', 'color' => 'yellow'],
                    'results_review' => ['label' => 'Results Review', 'color' => 'orange'],
                    'lab_testing' => ['label' => 'Lab Testing', 'color' => 'purple'],
                    'medicine_prescribed' => ['label' => 'Medicine Prescribed', 'color' => 'green'],
                    'final_payment_collected' => ['label' => 'Finalized / Paid', 'color' => 'gray'],
                    'completed' => ['label' => 'Completed', 'color' => 'gray'],
                ];

                foreach ($status_order_mobile as $status_key => $m):
                    $group_list = $groups[$status_key] ?? [];
                    // Skip empty groups
                    if (empty($group_list)) continue;
            ?>
            <div class="mobile-group-card mb-4 border rounded-lg overflow-hidden">
                <div class="px-4 py-3 bg-<?php echo $m['color']; ?>-600 text-white flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <h4 class="font-semibold"><?php echo $m['label']; ?></h4>
                        <span class="bg-white text-<?php echo $m['color']; ?>-600 px-2 py-1 rounded-full text-xs font-medium"><?php echo count($group_list); ?></span>
                    </div>
                </div>
                <div class="p-4">
                    <?php foreach ($group_list as $patient): ?>
                            <?php $age = (!empty($patient['date_of_birth']) && strtotime($patient['date_of_birth']) !== false) ? date_diff(date_create($patient['date_of_birth']), date_create('today'))->y . ' yrs' : 'N/A'; ?>
                            <div class="mobile-patient-card mb-3 p-3 border rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo $age; ?> • <?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-700 font-semibold"><?php echo (int)($patient['consultation_count'] ?? 0); ?> visits</div>
                                        <div class="mt-2">
                                            <button onclick="viewPatientDetails(<?php echo $patient['id']; ?>)" class="mobile-btn-primary mr-2">View</button>
                                            <?php if (($patient['workflow_status'] ?? 'registered') === 'results_review'): ?>
                                                <button onclick="reviewResults(<?php echo $patient['id']; ?>)" class="mobile-btn-success">Review</button>
                                            <?php else: ?>
                                                <button onclick="attendPatient(<?php echo $patient['id']; ?>)" class="mobile-btn-success">Attend</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Attend Patient Modal -->
<div id="attendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Attend Patient</h3>
                <button onclick="closeAttendModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="attendForm" method="POST" action="<?= $BASE_PATH ?>/doctor/start_consultation" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
                <input type="hidden" id="attendPatientId" name="patient_id">

                <!-- M/C (Main Complaint) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">M/C - Main Complaint</label>
                    <textarea id="mainComplaint" name="main_complaint" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Patient's main complaint/symptoms..."></textarea>
                </div>

                <!-- O/E (On Examination) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">O/E - On Examination</label>
                    <textarea id="onExamination" name="on_examination" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Physical examination findings..."></textarea>
                </div>

                <!-- Preliminary Diagnosis -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preliminary Diagnosis</label>
                    <textarea id="preliminaryDiagnosis" name="preliminary_diagnosis" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Initial diagnosis..."></textarea>
                </div>

                <!-- Final Diagnosis -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Final Diagnosis</label>
                    <textarea id="finalDiagnosis" name="final_diagnosis" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Final diagnosis after examination..."></textarea>
                </div>

                <!-- Lab Investigation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lab Investigation</label>
                    <textarea id="labInvestigation" name="lab_investigation" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="List any lab tests or investigations required..."></textarea>
                </div>

                <!-- RX (Prescription) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">RX - Prescription</label>
                    <textarea id="prescription" name="prescription" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Prescribe medications with dosage and instructions..."></textarea>
                </div>

                <!-- Treatment Plan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Treatment Plan & Advice</label>
                    <textarea id="treatmentPlan" name="treatment_plan" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Treatment plan, follow-up instructions, lifestyle advice..."></textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAttendModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        <i class="fas fa-save mr-2"></i>Complete Consultation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Review Results Modal -->
<div id="reviewResultsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Review Lab Results</h3>
                <button onclick="closeReviewResultsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="reviewResultsForm" method="POST" action="<?= $BASE_PATH ?>/doctor/review_results" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
                <input type="hidden" id="reviewPatientId" name="patient_id">

                <div class="mb-4">
                    <h4 class="text-md font-medium text-gray-900 mb-2">Lab Results Summary</h4>
                    <div id="resultsSummary" class="bg-gray-50 p-4 rounded-md">
                        <!-- Results will be loaded here via JavaScript -->
                        <p class="text-sm text-gray-600">Loading results...</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Decision</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="action" value="prescribe" required
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Prescribe Medicine - Patient can proceed to payment</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="action" value="retest"
                                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Send for Retesting - Additional tests required</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeReviewResultsModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-save mr-2"></i>Submit Decision
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function viewPatientDetails(patientId) {
    window.location.href = '<?= $BASE_PATH ?>/doctor/view_patient/' + patientId;
}

function viewLabResults(patientId) {
    window.location.href = '<?= $BASE_PATH ?>/doctor/view_lab_results/' + patientId;
}

function attendPatient(patientId) {
    document.getElementById('attendPatientId').value = patientId;
    document.getElementById('attendModal').classList.remove('hidden');
    document.getElementById('attendForm').reset();
}

function closeAttendModal() {
    document.getElementById('attendModal').classList.add('hidden');
}

function reviewResults(patientId) {
    document.getElementById('reviewPatientId').value = patientId;
    document.getElementById('reviewResultsModal').classList.remove('hidden');
    // In a real implementation, you would fetch the lab results here
    document.getElementById('resultsSummary').innerHTML = '<p class="text-sm text-gray-600">Lab results would be displayed here...</p>';
}

function closeReviewResultsModal() {
    document.getElementById('reviewResultsModal').classList.add('hidden');
    document.getElementById('reviewResultsForm').reset();
}

// Close modals when clicking outside
document.getElementById('attendModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAttendModal();
    }
});

document.getElementById('reviewResultsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReviewResultsModal();
    }
});
</script>
