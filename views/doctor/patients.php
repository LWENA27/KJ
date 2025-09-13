<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">My Patients</h1>
    <a href="<?= $BASE_PATH ?>/doctor/patients" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            <i class="fas fa-search mr-2"></i>Search Patients
        </a>
    </div>

    <!-- Patients Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Patient List</h3>
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
                    <?php foreach ($patients as $patient): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        DOB: <?php echo date('M j, Y', strtotime($patient['date_of_birth'])); ?>
                                        (<?php echo date_diff(date_create($patient['date_of_birth']), date_create('today'))->y; ?> years)
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?php echo htmlspecialchars($patient['email'] ?? 'N/A'); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                <?php echo $patient['consultation_count']; ?> visits
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php
                            // This would need to be fetched from the database
                            echo 'Recent';
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                <?php
                                $status = $patient['workflow_status'] ?? 'registered';
                                switch ($status) {
                                    case 'registered':
                                        echo 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'consultation_started':
                                        echo 'bg-yellow-100 text-yellow-800';
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
                                <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewPatientDetails(<?php echo $patient['id']; ?>)"
                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye mr-1"></i>View Details
                            </button>
                            <?php if (($patient['workflow_status'] ?? 'registered') === 'results_review'): ?>
                            <button onclick="viewLabResults(<?php echo $patient['id']; ?>)"
                                    class="text-purple-600 hover:text-purple-900 mr-3">
                                <i class="fas fa-vial mr-1"></i>Lab Results
                            </button>
                            <button onclick="reviewResults(<?php echo $patient['id']; ?>)"
                                    class="text-orange-600 hover:text-orange-900 mr-3">
                                <i class="fas fa-clipboard-check mr-1"></i>Review Results
                            </button>
                            <?php else: ?>
                            <button onclick="attendPatient(<?php echo $patient['id']; ?>)"
                                    class="text-green-600 hover:text-green-900">
                                <i class="fas fa-stethoscope mr-1"></i>Attend
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
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
