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
                                    Tests: <?php echo htmlspecialchars($patient['test_names'] ?? 'Lab results available'); ?>
                                </p>
                                <?php if (!empty($patient['result_date'])): ?>
                                <span class="text-xs text-neutral-500">
                                    Results received: <?php echo htmlspecialchars(safe_date('M j, Y H:i', $patient['result_date'])); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_lab_results?id=<?php echo $patient['id']; ?>" 
                           class="btn btn-warning">
                            <i class="fas fa-eye mr-2"></i>Review Results
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        
    <!-- Available Patients -->
    <!-- Available Patients for Consultation -->
<div class="bg-white rounded-lg shadow mt-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Patients Waiting for Consultation</h3>
        <p class="text-sm text-gray-600">Patients who have registered for consultation today</p>
    </div>
    <div class="p-6">
        <?php if (empty($available_patients)): ?>
            <p class="text-gray-500 text-center py-4">No patients waiting for consultation</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registration Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($available_patients as $patient): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Age: <?php 
                                                    if (!empty($patient['date_of_birth'])) {
                                                        $age = date_diff(date_create($patient['date_of_birth']), date_create('today'))->y;
                                                        echo $age . ' years';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($patient['phone']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($patient['address']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo htmlspecialchars(safe_date('h:i A', $patient['created_at'] ?? $patient['visit_created_at'] ?? null, 'N/A')); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php echo $patient['consultation_registration_paid'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $patient['consultation_registration_paid'] ? 'Paid' : 'Pending Payment'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if ($patient['consultation_registration_paid']): ?>
                                        <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/attend_patient/<?php echo $patient['id']; ?>"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2 inline-block">
                                            <i class="fas fa-stethoscope mr-1"></i>Attend
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/attend_patient/<?php echo $patient['id']; ?>"
                                                class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded mr-2 inline-block"
                                                title="Payment required - Click to see override options">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Attend
                                        </a>
                                    <?php endif; ?>
                                    <button onclick="viewPatientDetails(<?php echo $patient['id']; ?>)"
                                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
                        <?php $r_status = (string)($result['status'] ?? 'unknown'); ?>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                            <?php
                            switch ($r_status) {
                                case 'pending':
                                    echo 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'completed':
                                    echo 'bg-green-100 text-green-800';
                                    break;
                                case 'reviewed':
                                    echo 'bg-blue-100 text-blue-800';
                                    break;
                                default:
                                    echo 'bg-gray-100 text-gray-800';
                                    break;
                            }
                            ?>">
                            <?php echo ucfirst($r_status); ?>
                        </span>
                            <p class="text-xs text-gray-400 mt-1">
                            <?php echo htmlspecialchars(safe_date('M j, H:i', $result['created_at'] ?? null, 'N/A')); ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

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
                                    - Completed at <?php echo htmlspecialchars(safe_date('H:i', $consultation['created_at'], 'N/A')); ?>
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


<script>
function viewPatientDetails(patientId) {
    window.location.href = '<?= $BASE_PATH ?>/doctor/view_patient/' + patientId;
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
                <label for="targetDoctor" class="form-label">Allocate to user</label>
                <select id="targetDoctor" name="target_doctor_id" required class="form-input">
                    <option value="">Select user</option>
                    <?php foreach (($other_users ?? $other_doctors) as $user): ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name'] . ' (' . ucfirst($user['role']) . ')'); ?></option>
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

        <form id="medicineModalForm" method="POST" action="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/prescribe_medicine" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" id="medicinePatientId" name="patient_id">
            <input type="hidden" id="medicinesJson" name="medicines" value="[]">
            <input type="hidden" id="selectedTestsJson" name="selected_tests" value="[]">
            <input type="hidden" name="next_step" value="medicine">

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
                        <select name="medicines[0][medicine_id]" class="form-input medicine-select" required>
                            <option value="">Select Medicine</option>
                            <!-- Options will be populated dynamically -->
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
// Global variable to store loaded medicines
let availableMedicines = [];

// Load medicines from API on page load
document.addEventListener('DOMContentLoaded', function() {
    loadMedicines();
});

function loadMedicines() {
    fetch('/KJ/doctor/search_medicines', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        availableMedicines = data;
        // Populate all existing medicine selects
        document.querySelectorAll('.medicine-select').forEach(select => {
            populateMedicineSelect(select);
        });
    })
    .catch(error => {
        console.error('Error loading medicines:', error);
        // Fallback: show error in selects
        document.querySelectorAll('.medicine-select').forEach(select => {
            select.innerHTML = '<option value="">Error loading medicines</option>';
        });
    });
}

function populateMedicineSelect(selectElement) {
    if (!selectElement) return;
    
    // Keep the "Select Medicine" option
    selectElement.innerHTML = '<option value="">Select Medicine</option>';
    
    // Add all available medicines
    availableMedicines.forEach(medicine => {
        const option = document.createElement('option');
        option.value = medicine.id;
        
        // Format: "Name Strength (Stock: X)"
        let displayName = medicine.name;
        if (medicine.strength) {
            displayName += ' ' + medicine.strength;
        }
        if (medicine.unit) {
            displayName += medicine.unit;
        }
        displayName += ' (Stock: ' + (medicine.stock_quantity || 0) + ')';
        
        option.textContent = displayName;
        selectElement.appendChild(option);
    });
}

let medicineCounter = 1;

function addMedicine() {
    const medicineList = document.getElementById('medicineList');
    const medicineItem = document.createElement('div');
    medicineItem.className = 'medicine-item grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border border-neutral-200 rounded-lg';
    medicineItem.innerHTML = `
        <select name="medicines[${medicineCounter}][medicine_id]" class="form-input medicine-select" required>
            <option value="">Select Medicine</option>
            <!-- Options will be populated dynamically -->
        </select>
        <input type="text" name="medicines[${medicineCounter}][quantity]" placeholder="Quantity" class="form-input" required>
        <input type="text" name="medicines[${medicineCounter}][dosage]" placeholder="Dosage instructions" class="form-input" required>
    `;
    medicineList.appendChild(medicineItem);
    
    // Populate the new select with medicine options
    populateMedicineSelect(medicineItem.querySelector('.medicine-select'));
    
    medicineCounter++;
}

    // Serialize medicines in medicineList into JSON before submit
    document.getElementById('medicineModalForm')?.addEventListener('submit', function(e) {
    const meds = [];
    const medicineList = document.getElementById('medicineList');
    if (medicineList) {
        // For each .medicine-item collect selected medicine_id, quantity, dosage
        medicineList.querySelectorAll('.medicine-item').forEach(item => {
            const select = item.querySelector('select[name^="medicines["][name$="[medicine_id]"]');
            const qty = item.querySelector('input[name$="[quantity]"]');
            const dosage = item.querySelector('input[name$="[dosage]"]');
            if (!select) return;
            const medId = parseInt(select.value, 10);
            if (!medId) return;
            meds.push({ id: medId, quantity: parseInt(qty.value, 10) || 1, dosage: (dosage.value || '').trim() });
        });
    }

    if (meds.length === 0) {
        e.preventDefault();
        alert('Please select at least one medicine');
        return false;
    }

    const medsInput = document.getElementById('medicinesJson');
    if (medsInput) medsInput.value = JSON.stringify(meds);
    // allow submit
});
</script>
