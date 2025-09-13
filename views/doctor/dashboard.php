<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Doctor Dashboard</h1>
        <div class="text-sm text-gray-500">
            <?php echo date('l, F j, Y'); ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-calendar-check text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Today's Consultations</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['today_consultations']; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed Consultations</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['completed_consultations']; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-users text-purple-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Consultations</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_consultations']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Queue & Actions -->
    <div class="space-y-6">
        <!-- Patients Ready for Consultation -->
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-neutral-900">Patients Ready for Consultation</h3>
                    <span class="text-sm text-neutral-600"><?php echo count($pending_consultations); ?> patients waiting</span>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($pending_consultations)): ?>
                <div class="text-center py-8">
                    <div class="w-16 h-16 mx-auto mb-4 bg-neutral-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-clock text-2xl text-neutral-400"></i>
                    </div>
                    <h4 class="text-lg font-medium text-neutral-900 mb-2">No patients waiting</h4>
                    <p class="text-neutral-600">All consultation patients have been attended to</p>
                </div>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($pending_consultations as $patient): ?>
                    <div class="flex items-center justify-between p-4 border border-neutral-200 rounded-lg hover:bg-neutral-50 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-medium">
                                <?php echo strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="font-medium text-neutral-900">
                                    <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                </h4>
                                <div class="flex items-center space-x-4 text-sm text-neutral-600">
                                    <span>Age: <?php echo $patient['date_of_birth'] ? date_diff(date_create($patient['date_of_birth']), date_create('today'))->y : 'N/A'; ?></span>
                                    <span>Phone: <?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?></span>
                                    <span class="status-success">Consultation Paid</span>
                                </div>
                                <div class="mt-1">
                                    <span class="text-xs text-neutral-500">
                                        Registered: <?php echo date('M j, Y H:i', strtotime($patient['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_patient?id=<?php echo $patient['id']; ?>" 
                               class="btn btn-primary">
                                <i class="fas fa-stethoscope mr-2"></i>Start Consultation
                            </a>
                            <div class="relative">
                                <button class="btn btn-secondary dropdown-toggle" data-dropdown="patient-actions-<?php echo $patient['id']; ?>">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu" id="patient-actions-<?php echo $patient['id']; ?>">
                                    <button class="dropdown-item" onclick="openAllocateModal(<?php echo $patient['id']; ?>, '<?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>')">
                                        <i class="fas fa-user-md mr-2"></i>Allocate to Another Doctor
                                    </button>
                                    <button class="dropdown-item" onclick="openLabModal(<?php echo $patient['id']; ?>, '<?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>')">
                                        <i class="fas fa-vial mr-2"></i>Send to Lab
                                    </button>
                                    <button class="dropdown-item" onclick="openMedicineModal(<?php echo $patient['id']; ?>, '<?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>')">
                                        <i class="fas fa-pills mr-2"></i>Prescribe Medicine
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Patients Waiting for Lab Results Review -->
        <?php if (!empty($pending_results)): ?>
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200">
                <h3 class="text-lg font-semibold text-neutral-900">Lab Results Ready for Review</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php foreach ($pending_results as $patient): ?>
                    <div class="flex items-center justify-between p-4 border border-warning-200 bg-warning-50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-warning-500 rounded-full flex items-center justify-center text-white">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-neutral-900">
                                    <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                </h4>
                                <p class="text-sm text-neutral-600">
                                    Tests: <?php echo htmlspecialchars($patient['test_name'] ?? 'Lab results available'); ?>
                                </p>
                                <?php if (isset($patient['result_date'])): ?>
                                <span class="text-xs text-neutral-500">
                                    Results received: <?php echo date('M j, Y H:i', strtotime($patient['result_date'])); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/lab_results_view?patient_id=<?php echo $patient['id']; ?>" 
                           class="btn btn-warning">
                            <i class="fas fa-eye mr-2"></i>Review Results
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Today's Completed Consultations -->
        <?php if (!empty($today_completed)): ?>
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200">
                <h3 class="text-lg font-semibold text-neutral-900">Today's Completed Consultations</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <?php foreach ($today_completed as $consultation): ?>
                    <div class="flex items-center justify-between p-3 border border-success-200 bg-success-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-success-500 rounded-full flex items-center justify-center text-white text-sm">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <span class="font-medium text-neutral-900">
                                    <?php echo htmlspecialchars($consultation['first_name'] . ' ' . $consultation['last_name']); ?>
                                </span>
                                <span class="text-sm text-neutral-600 ml-2">
                                    - Completed at <?php echo date('H:i', strtotime($consultation['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                        <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_patient?id=<?php echo $consultation['patient_id']; ?>" 
                           class="text-success-600 hover:text-success-700">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Available Patients -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Available Patients</h3>
            <p class="text-sm text-gray-600">Patients who have paid for consultation and are ready to be seen</p>
        </div>
        <div class="p-6">
            <?php if (empty($available_patients)): ?>
            <p class="text-gray-500 text-center py-4">No patients available for consultation</p>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($available_patients as $patient): ?>
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user-check text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">
                                <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                Age: <?php echo date_diff(date_create($patient['date_of_birth']), date_create('today'))->y; ?> |
                                Phone: <?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?> |
                                Visits: <?php echo $patient['consultation_count']; ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="viewPatientDetails(<?php echo $patient['id']; ?>)"
                                class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-md transition duration-200">
                            <i class="fas fa-eye mr-1"></i>View Details
                        </button>
                        <button onclick="attendPatient(<?php echo $patient['id']; ?>)"
                                class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-sm rounded-md transition duration-200">
                            <i class="fas fa-stethoscope mr-1"></i>Attend
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Lab Results -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Lab Results</h3>
        </div>
        <div class="p-6">
            <?php if (empty($recent_results)): ?>
            <p class="text-gray-500 text-center py-4">No recent lab results</p>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($recent_results as $result): ?>
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-flask text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">
                                <?php echo htmlspecialchars($result['test_name']); ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                Patient: <?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                            <?php
                            switch ($result['status']) {
                                case 'pending':
                                    echo 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'completed':
                                    echo 'bg-green-100 text-green-800';
                                    break;
                                case 'reviewed':
                                    echo 'bg-blue-100 text-blue-800';
                                    break;
                            }
                            ?>">
                            <?php echo ucfirst($result['status']); ?>
                        </span>
                        <p class="text-xs text-gray-400 mt-1">
                            <?php echo date('M j, H:i', strtotime($result['created_at'])); ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
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
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
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

<script>
function viewPatientDetails(patientId) {
    window.location.href = '<?= $BASE_PATH ?>/doctor/view_patient/' + patientId;
}

function attendPatient(patientId) {
    document.getElementById('attendPatientId').value = patientId;
    document.getElementById('attendModal').classList.remove('hidden');
    document.getElementById('attendForm').reset();
}

function closeAttendModal() {
    document.getElementById('attendModal').classList.add('hidden');
}

function openAllocateModal(patientId, patientName) {
    document.getElementById('allocatePatientId').value = patientId;
    document.getElementById('allocatePatientName').textContent = patientName;
    document.getElementById('allocateModal').classList.remove('hidden');
}

function closeAllocateModal() {
    document.getElementById('allocateModal').classList.add('hidden');
}

function openLabModal(patientId, patientName) {
    document.getElementById('labPatientId').value = patientId;
    document.getElementById('labPatientName').textContent = patientName;
    document.getElementById('labModal').classList.remove('hidden');
}

function closeLabModal() {
    document.getElementById('labModal').classList.add('hidden');
}

function openMedicineModal(patientId, patientName) {
    document.getElementById('medicinePatientId').value = patientId;
    document.getElementById('medicinePatientName').textContent = patientName;
    document.getElementById('medicineModal').classList.remove('hidden');
}

function closeMedicineModal() {
    document.getElementById('medicineModal').classList.add('hidden');
}

// Dropdown functionality
document.addEventListener('click', function(e) {
    if (e.target.matches('.dropdown-toggle')) {
        e.preventDefault();
        const dropdownId = e.target.getAttribute('data-dropdown');
        const dropdown = document.getElementById(dropdownId);
        
        // Close all other dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu.id !== dropdownId) {
                menu.style.display = 'none';
            }
        });
        
        // Toggle current dropdown
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    } else {
        // Close all dropdowns when clicking outside
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});

// Close modal when clicking outside
document.getElementById('attendModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAttendModal();
    }
});

document.getElementById('allocateModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAllocateModal();
    }
});

document.getElementById('labModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLabModal();
    }
});

document.getElementById('medicineModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeMedicineModal();
    }
});
</script>

<!-- Allocate Patient Modal -->
<div id="allocateModal" class="modal-overlay hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Allocate Patient to Another Doctor</h3>
            <button onclick="closeAllocateModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/allocate_patient" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" id="allocatePatientId" name="patient_id">

            <div>
                <label class="form-label">Patient</label>
                <div id="allocatePatientName" class="form-input bg-neutral-50"></div>
            </div>

            <div>
                <label for="targetDoctor" class="form-label">Allocate to Doctor</label>
                <select id="targetDoctor" name="target_doctor_id" required class="form-input">
                    <option value="">Select Doctor</option>
                    <?php foreach ($other_doctors as $doctor): ?>
                    <option value="<?php echo $doctor['id']; ?>"><?php echo htmlspecialchars($doctor['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="allocateNotes" class="form-label">Notes/Reason</label>
                <textarea id="allocateNotes" name="notes" rows="3" class="form-input" 
                          placeholder="Reason for allocation..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeAllocateModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-md mr-2"></i>Allocate Patient
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Send to Lab Modal -->
<div id="labModal" class="modal-overlay hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Send Patient to Lab</h3>
            <button onclick="closeLabModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/send_to_lab" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" id="labPatientId" name="patient_id">

            <div>
                <label class="form-label">Patient</label>
                <div id="labPatientName" class="form-input bg-neutral-50"></div>
            </div>

            <div class="card p-4 bg-warning-50 border-warning-200">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-warning-600 mt-1 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-warning-900 mb-1">Payment Required</h4>
                        <p class="text-sm text-warning-700">
                            Patient must pay for lab tests at reception before tests can be conducted.
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <label class="form-label">Lab Tests Required</label>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    <label class="flex items-center">
                        <input type="checkbox" name="tests[]" value="1" class="mr-2">
                        <span>Complete Blood Count (CBC)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="tests[]" value="2" class="mr-2">
                        <span>Blood Sugar</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="tests[]" value="3" class="mr-2">
                        <span>Urinalysis</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="tests[]" value="4" class="mr-2">
                        <span>Malaria Test</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="tests[]" value="5" class="mr-2">
                        <span>X-Ray</span>
                    </label>
                </div>
            </div>

            <div>
                <label for="labNotes" class="form-label">Clinical Notes</label>
                <textarea id="labNotes" name="notes" rows="3" class="form-input" 
                          placeholder="Clinical indication for tests..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeLabModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-vial mr-2"></i>Send to Lab
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Prescribe Medicine Modal -->
<div id="medicineModal" class="modal-overlay hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Prescribe Medicine</h3>
            <button onclick="closeMedicineModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/send_to_medicine" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" id="medicinePatientId" name="patient_id">

            <div>
                <label class="form-label">Patient</label>
                <div id="medicinePatientName" class="form-input bg-neutral-50"></div>
            </div>

            <div class="card p-4 bg-info-50 border-info-200">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-info-600 mt-1 mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-info-900 mb-1">Payment Required</h4>
                        <p class="text-sm text-info-700">
                            Patient must pay for prescribed medicines at reception before dispensing.
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <label class="form-label">Medicine Prescriptions</label>
                <div id="medicineList" class="space-y-3">
                    <div class="medicine-item grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border border-neutral-200 rounded-lg">
                        <select name="medicines[0][medicine_id]" class="form-input" required>
                            <option value="">Select Medicine</option>
                            <option value="1">Paracetamol 500mg</option>
                            <option value="2">Amoxicillin 250mg</option>
                            <option value="3">Ibuprofen 400mg</option>
                            <option value="4">Multivitamin</option>
                        </select>
                        <input type="text" name="medicines[0][quantity]" placeholder="Quantity" class="form-input" required>
                        <input type="text" name="medicines[0][dosage]" placeholder="Dosage instructions" class="form-input" required>
                    </div>
                </div>
                <button type="button" onclick="addMedicine()" class="btn btn-sm btn-secondary mt-2">
                    <i class="fas fa-plus mr-2"></i>Add Another Medicine
                </button>
            </div>

            <div>
                <label for="medicineNotes" class="form-label">Prescription Notes</label>
                <textarea id="medicineNotes" name="notes" rows="3" class="form-input" 
                          placeholder="Special instructions..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeMedicineModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-medical">
                    <i class="fas fa-pills mr-2"></i>Prescribe Medicine
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let medicineCounter = 1;

function addMedicine() {
    const medicineList = document.getElementById('medicineList');
    const medicineItem = document.createElement('div');
    medicineItem.className = 'medicine-item grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border border-neutral-200 rounded-lg';
    medicineItem.innerHTML = `
        <select name="medicines[${medicineCounter}][medicine_id]" class="form-input" required>
            <option value="">Select Medicine</option>
            <option value="1">Paracetamol 500mg</option>
            <option value="2">Amoxicillin 250mg</option>
            <option value="3">Ibuprofen 400mg</option>
            <option value="4">Multivitamin</option>
        </select>
        <input type="text" name="medicines[${medicineCounter}][quantity]" placeholder="Quantity" class="form-input" required>
        <input type="text" name="medicines[${medicineCounter}][dosage]" placeholder="Dosage instructions" class="form-input" required>
    `;
    medicineList.appendChild(medicineItem);
    medicineCounter++;
}
</script>
