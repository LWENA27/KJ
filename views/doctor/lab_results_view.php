<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Lab Results Review</h1>
        <div class="flex space-x-3">
            <a href="<?= $BASE_PATH ?>/doctor/view_patient/<?php echo $patient['id']; ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i>Back to Patient
            </a>
            <a href="<?= $BASE_PATH ?>/doctor/patient_journey/<?php echo $patient['id']; ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-timeline mr-2"></i>Patient Journey
            </a>
        </div>
    </div>

    <!-- Patient Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Patient Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <p class="text-gray-900"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Age</label>
                <p class="text-gray-900"><?php echo date_diff(date_create($patient['date_of_birth']), date_create('today'))->y; ?> years</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Gender</label>
                <p class="text-gray-900"><?php echo ucfirst($patient['gender']); ?></p>
            </div>
        </div>
    </div>

    <!-- Lab Results -->
    <?php if (empty($lab_results)): ?>
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <i class="fas fa-vial text-gray-400 text-4xl mb-4"></i>
        <p class="text-gray-500 text-lg">No lab results available for this patient</p>
        <p class="text-gray-400 text-sm">Results will appear here once tests are completed</p>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Laboratory Test Results</h3>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <?php foreach ($lab_results as $result): ?>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($result['test_name']); ?></h4>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($result['category']); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">
                                <?php echo safe_date('M j, Y H:i', $result['result_date'], 'N/A'); ?>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php if (!empty($result['result_value'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Result Value</label>
                            <p class="text-lg font-semibold text-gray-900">
                                <?php echo htmlspecialchars($result['result_value']); ?>
                                <?php if (!empty($result['unit'])): ?>
                                    <span class="text-sm text-gray-600"><?php echo htmlspecialchars($result['unit']); ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($result['normal_range'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Normal Range</label>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($result['normal_range']); ?></p>
                        </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                <?php
                                // Simple status determination - in real app, you'd have proper logic
                                if (!empty($result['result_value']) && !empty($result['normal_range'])) {
                                    echo 'bg-green-100 text-green-800';
                                    $status_text = 'Completed';
                                } else {
                                    echo 'bg-yellow-100 text-yellow-800';
                                    $status_text = 'Pending';
                                }
                                ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($result['result_text'])): ?>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                        <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md"><?php echo htmlspecialchars($result['result_text']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Treatment Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button onclick="prescribeMedicine(<?php echo $patient['id']; ?>)"
                    class="px-6 py-3 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-200">
                <i class="fas fa-pills mr-2"></i>Prescribe Medicine
            </button>
            <button onclick="finalizeTreatment(<?php echo $patient['id']; ?>)"
                    class="px-6 py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200">
                <i class="fas fa-check-circle mr-2"></i>Finalize Treatment
            </button>
            <button onclick="orderAdditionalTests(<?php echo $patient['id']; ?>)"
                    class="px-6 py-3 bg-purple-500 text-white rounded-md hover:bg-purple-600 transition duration-200">
                <i class="fas fa-vial mr-2"></i>Order Additional Tests
            </button>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Medicine Prescription Modal -->
<div id="prescriptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Prescribe Medicine Based on Results</h3>
                <button onclick="closePrescriptionModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="<?= $BASE_PATH ?>/doctor/review_results" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">
                <input type="hidden" name="action" value="prescribe">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Results Summary</label>
                    <textarea name="results_summary" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Summarize the lab results and clinical findings..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Medicine Prescription *</label>
                    <textarea name="prescription" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Prescribe medications with dosages and instructions..."></textarea>
                </div>

                <div class="bg-yellow-50 p-4 rounded-md">
                    <h4 class="text-sm font-medium text-yellow-800 mb-2">Medicine Search & Selection</h4>
                    <div class="space-y-2">
                        <input type="text" id="medicineSearch" placeholder="Search for medicines..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <div id="medicineResults" class="hidden max-h-40 overflow-y-auto border border-gray-300 rounded-md bg-white"></div>
                        <div id="selectedMedicinesList" class="space-y-1"></div>
                        <!-- Hidden fields for selected medicines -->
                        <div id="selectedMedicinesData"></div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closePrescriptionModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        <i class="fas fa-pills mr-2"></i>Prescribe Medicine
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Finalize Treatment Modal -->
<div id="finalizeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Finalize Treatment</h3>
                <button onclick="closeFinalizeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="<?= $BASE_PATH ?>/doctor/review_results" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">
                <input type="hidden" name="action" value="finalize">

                <div class="bg-blue-50 p-4 rounded-md mb-4">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">Treatment Summary</h4>
                    <p class="text-sm text-blue-700">Review all lab results and provide final treatment recommendations.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Results Summary & Final Diagnosis *</label>
                    <textarea name="results_summary" rows="5" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Provide comprehensive summary of lab results, diagnosis, and treatment recommendations..."></textarea>
                </div>

                <div class="bg-green-50 p-4 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-green-600 mr-2"></i>
                        <div>
                            <h4 class="text-sm font-medium text-green-800">No Medicine Prescription</h4>
                            <p class="text-sm text-green-700">This will finalize the treatment without prescribing any medications.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeFinalizeModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-check-circle mr-2"></i>Finalize Treatment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global variable to store selected medicines
let selectedMedicines = [];

function prescribeMedicine(patientId) {
    document.getElementById('prescriptionModal').classList.remove('hidden');
    // Initialize medicine search
    initializeMedicineSearch();
}

function finalizeTreatment(patientId) {
    document.getElementById('finalizeModal').classList.remove('hidden');
}

function closePrescriptionModal() {
    document.getElementById('prescriptionModal').classList.add('hidden');
    const searchInput = document.getElementById('medicineSearch');
    const resultsDiv = document.getElementById('medicineResults');
    if (searchInput) searchInput.value = '';
    if (resultsDiv) resultsDiv.classList.add('hidden');
    selectedMedicines = [];
    updateSelectedMedicinesDisplay();
}

function closeFinalizeModal() {
    document.getElementById('finalizeModal').classList.add('hidden');
}

function orderAdditionalTests(patientId) {
    if (confirm('Send patient back for additional lab tests? This will reset the workflow to lab testing phase.')) {
        // Create a form to submit the retest action
        const form = document.createElement('form');
        form.method = 'POST';
    form.action = '<?= $BASE_PATH ?>/doctor/review_results';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = 'csrf_token';
        csrfToken.value = '<?php echo htmlspecialchars($csrf_token); ?>';

        const patientIdInput = document.createElement('input');
        patientIdInput.type = 'hidden';
        patientIdInput.name = 'patient_id';
        patientIdInput.value = patientId;

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'retest';

        form.appendChild(csrfToken);
        form.appendChild(patientIdInput);
        form.appendChild(actionInput);

        document.body.appendChild(form);
        form.submit();
    }
}

function dischargePatient(patientId) {
    if (confirm('Discharge patient and mark treatment as completed?')) {
    window.location.href = `<?= $BASE_PATH ?>/doctor/view_patient/${patientId}`;
    }
}

// Medicine search functionality
function initializeMedicineSearch() {
    const searchInput = document.getElementById('medicineSearch');
    const resultsDiv = document.getElementById('medicineResults');

    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        if (query.length < 2) {
            resultsDiv.classList.add('hidden');
            return;
        }

        // Fetch medicines
    fetch(`<?= $BASE_PATH ?>/doctor/search_medicines?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                resultsDiv.innerHTML = '';

                if (data.length > 0) {
                    data.forEach(medicine => {
                        const div = document.createElement('div');
                        div.className = 'p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200';
                        div.innerHTML = `
                            <div class="font-medium">${medicine.name}</div>
                            <div class="text-sm text-gray-600">${medicine.generic_name} - $${medicine.unit_price} (${medicine.stock_quantity} in stock)</div>
                        `;
                        div.onclick = () => selectMedicine(medicine);
                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.classList.remove('hidden');
                } else {
                    resultsDiv.innerHTML = '<div class="p-2 text-gray-500">No medicines found</div>';
                    resultsDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error searching medicines:', error);
                resultsDiv.innerHTML = '<div class="p-2 text-red-500">Error searching medicines</div>';
                resultsDiv.classList.remove('hidden');
            });
    });
}

function selectMedicine(medicine) {
    // Check if already selected
    if (selectedMedicines.find(m => m.id === medicine.id)) {
        alert('Medicine already selected');
        return;
    }

    // Add to selected medicines
    selectedMedicines.push({
        id: medicine.id,
        name: medicine.name,
        generic_name: medicine.generic_name,
        unit_price: medicine.unit_price,
        stock_quantity: medicine.stock_quantity,
        quantity: 1,
        dosage: '',
        instructions: ''
    });

    updateSelectedMedicinesDisplay();
    document.getElementById('medicineSearch').value = '';
    document.getElementById('medicineResults').classList.add('hidden');
}

function updateSelectedMedicinesDisplay() {
    const listDiv = document.getElementById('selectedMedicinesList');
    const dataDiv = document.getElementById('selectedMedicinesData');
    if (!listDiv || !dataDiv) return;

    listDiv.innerHTML = '';
    dataDiv.innerHTML = '';

    if (selectedMedicines.length === 0) {
        listDiv.innerHTML = '<p class="text-sm text-gray-500">No medicines selected</p>';
        return;
    }

    selectedMedicines.forEach((medicine, index) => {
        const div = document.createElement('div');
        div.className = 'bg-gray-50 p-3 rounded-md';
        div.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="font-medium">${medicine.name}</div>
                    <div class="text-sm text-gray-600">${medicine.generic_name}</div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-2">
                        <div>
                            <label class="block text-xs text-gray-600">Quantity</label>
                            <input type="number" min="1" max="${medicine.stock_quantity}" value="${medicine.quantity}"
                                   onchange="updateMedicineDetail(${index}, 'quantity', this.value)"
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">Dosage</label>
                            <input type="text" placeholder="e.g., 500mg" value="${medicine.dosage}"
                                   onchange="updateMedicineDetail(${index}, 'dosage', this.value)"
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">Instructions</label>
                            <input type="text" placeholder="e.g., 2x daily" value="${medicine.instructions}"
                                   onchange="updateMedicineDetail(${index}, 'instructions', this.value)"
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                        </div>
                    </div>
                </div>
                <button type="button" onclick="removeMedicine(${index})" class="text-red-600 hover:text-red-800 ml-2">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        listDiv.appendChild(div);

        // Create hidden fields for form submission
        const medicineData = document.createElement('div');
        medicineData.innerHTML = `
            <input type="hidden" name="selected_medicines[${index}][id]" value="${medicine.id}">
            <input type="hidden" name="selected_medicines[${index}][name]" value="${medicine.name}">
            <input type="hidden" name="selected_medicines[${index}][generic_name]" value="${medicine.generic_name}">
            <input type="hidden" name="selected_medicines[${index}][quantity]" value="${medicine.quantity}">
            <input type="hidden" name="selected_medicines[${index}][dosage]" value="${medicine.dosage}">
            <input type="hidden" name="selected_medicines[${index}][instructions]" value="${medicine.instructions}">
        `;
        dataDiv.appendChild(medicineData);
    });

    // Update prescription textarea with formatted prescription
    updatePrescriptionTextarea();
}

function updateMedicineDetail(index, field, value) {
    selectedMedicines[index][field] = value;
    updatePrescriptionTextarea();
}

function removeMedicine(index) {
    selectedMedicines.splice(index, 1);
    updateSelectedMedicinesDisplay();
}

function updatePrescriptionTextarea() {
    const textarea = document.querySelector('#prescriptionModal textarea[name="prescription"]');
    if (!textarea) return;

    let prescriptionText = '';
    selectedMedicines.forEach(medicine => {
        if (medicine.quantity && medicine.dosage && medicine.instructions) {
            prescriptionText += `${medicine.name} (${medicine.generic_name})\n`;
            prescriptionText += `Quantity: ${medicine.quantity}\n`;
            prescriptionText += `Dosage: ${medicine.dosage}\n`;
            prescriptionText += `Instructions: ${medicine.instructions}\n\n`;
        }
    });

    textarea.value = prescriptionText.trim();
}

// Close modals when clicking outside
document.getElementById('prescriptionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePrescriptionModal();
    }
});

document.getElementById('finalizeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFinalizeModal();
    }
});
</script>
