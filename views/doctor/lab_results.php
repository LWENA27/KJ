<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Lab Results</h1>
        <a href="<?= $BASE_PATH ?>/doctor/lab_results" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            <i class="fas fa-search mr-2"></i>Search Results
        </a>
    </div>

    <!-- Lab Results Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Test Results</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($results as $result): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-green-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php $apt = $result['appointment_date'] ?? $result['visit_date'] ?? $result['created_at']; echo safe_date('M j, Y', $apt, 'N/A'); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo htmlspecialchars($result['test_name']); ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?php if ($result['result_value']): ?>
                                <span class="font-medium"><?php echo htmlspecialchars($result['result_value']); ?></span>
                                <?php if ($result['result_text']): ?>
                                    <br><span class="text-xs text-gray-600"><?php echo htmlspecialchars($result['result_text']); ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-gray-500">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo safe_date('M j, Y', $result['created_at'], 'N/A'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button 
                                data-test-id="<?php echo $result['id']; ?>"
                                data-patient-id="<?php echo $result['patient_id']; ?>"
                                data-patient-name="<?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?>"
                                data-test-name="<?php echo htmlspecialchars($result['test_name']); ?>"
                                data-result-value="<?php echo htmlspecialchars($result['result_value'] ?? ''); ?>"
                                data-result-text="<?php echo htmlspecialchars($result['result_text'] ?? ''); ?>"
                                data-result-unit="<?php echo htmlspecialchars($result['result_unit'] ?? ''); ?>"
                                data-completed-at="<?php echo $result['completed_at'] ? date('M j, Y H:i', strtotime($result['completed_at'])) : ''; ?>"
                                data-status="<?php echo htmlspecialchars($result['status']); ?>"
                                class="view-details-btn text-blue-600 hover:text-blue-900 mr-3 focus:outline-none">
                                <i class="fas fa-eye mr-1"></i> View Details
                            </button>
                            <?php if ($result['status'] === 'completed'): ?>
                            <button 
                                data-patient-id="<?php echo $result['patient_id']; ?>"
                                class="prescribe-btn text-green-600 hover:text-green-900 focus:outline-none">
                                <i class="fas fa-prescription mr-1"></i> Prescribe Medicine
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

<!-- Test Details Modal -->
<div id="testDetailsModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop with blur effect -->
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" id="modalBackdrop"></div>
    
    <!-- Modal Content -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-xl shadow-2xl transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-flask text-blue-600 mr-3"></i>
                    Lab Test Result Details
                </h3>
                <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Body -->
            <div class="px-6 py-4">
                <!-- Patient & Test Info -->
                <div class="bg-blue-50 p-4 rounded-lg mb-5">
                    <div class="flex items-center mb-2">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900" id="patientName"></h4>
                            <p class="text-sm text-gray-600" id="testName"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Result Details -->
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Result Value</label>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <div class="text-lg font-medium" id="resultValue"></div>
                                <div class="text-sm text-gray-500" id="resultUnit"></div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <span id="statusBadge" class="inline-flex px-2 py-1 text-xs font-medium rounded-full"></span>
                                <div class="text-sm text-gray-500 mt-1" id="completedAt"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 min-h-[80px]" id="resultNotes">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                <button type="button" id="openPrescriptionFromDetails"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg shadow-sm hover:bg-green-700">
                    <i class="fas fa-prescription mr-2"></i>Prescribe Medicine
                </button>
                <button type="button" id="closeModalBtn"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 shadow-sm hover:bg-gray-50">
                    Close
                </button>
                <button type="button" id="printResultBtn"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i>Print Result
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Prescription Modal -->
<div id="prescriptionModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
    
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl transform transition-all w-full mx-4
                    md:min-w-[720px] lg:min-w-[880px] max-w-4xl min-h-[560px] overflow-y-auto"
             style="margin-left:100px;">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-prescription text-green-600 mr-3"></i>
                    Prescribe Medicine
                </h3>
                <button type="button" id="closePrescriptionModal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Body -->
            <form id="prescriptionForm" method="POST" action="<?= $BASE_PATH ?>/doctor/prescribe_medicine" onsubmit="return handlePrescriptionSubmit();">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="patient_id" id="prescriptionPatientId">
                <input type="hidden" name="selected_medicines" id="selectedMedicinesJson" value="[]">
                
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        <!-- Medicine Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search Medicine</label>
                            <div class="relative">
                                <div class="flex">
                                    <input type="text" id="medicineSearch" 
                                           placeholder="Type to search medicines..."
                                           autocomplete="off"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <button type="button" onclick="clearMedicineSearch()" id="clearMedicineSearch"
                                            class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200 hidden">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="medicineResults" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 hidden max-h-60 overflow-y-auto shadow-lg"></div>
                            </div>
                        </div>

                        <!-- Selected Medicines -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selected Medicines</label>
                            <div id="selectedMedicinesList" class="space-y-2">
                                <!-- Selected medicines will be added here dynamically -->
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prescription Notes</label>
                            <textarea name="notes" rows="3" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                    placeholder="Additional instructions or notes about the prescription..."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                    <button type="button" id="cancelPrescription"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 shadow-sm hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg shadow-sm hover:bg-green-700">
                        <i class="fas fa-check mr-2"></i>Submit Prescription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Initialize variables
let selectedMedicines = [];

// Form validation function
function validatePrescriptionForm() {
    // Get the currently selected medicines from the UI
    const listDiv = document.getElementById('selectedMedicinesList');
    const medicines = listDiv.querySelectorAll('div[data-medicine-id]');
    
    // Check if any medicines are selected
    if (!medicines.length || selectedMedicines.length === 0) {
        alert('Please select at least one medicine');
        return false;
    }

    let validationPassed = true;
    let medicineData = [];

    medicines.forEach(div => {
        const medicineId = parseInt(div.dataset.medicineId);
        const medicine = selectedMedicines.find(m => m.id === medicineId);
        if (!medicine) return;

        const qtyInput = div.querySelector('input[name$="[quantity]"]');
        const textInput = div.querySelector('input[type="text"]');

        const qty = Number(qtyInput?.value);
        const combined = textInput?.value?.trim();

        // Validate each field
        if (isNaN(qty) || qty < 1) {
            alert(`Please enter a valid quantity for ${medicine.name}`);
            validationPassed = false;
            return;
        }

        if (!combined) {
            alert(`Please specify dosage/instructions for ${medicine.name}`);
            validationPassed = false;
            return;
        }

        if (qty > medicine.stock_quantity) {
            alert(`Cannot prescribe ${qty} units of ${medicine.name}. Only ${medicine.stock_quantity} available in stock.`);
            validationPassed = false;
            return;
        }

        // Build controller-compatible entry (provide sensible defaults)
        medicineData.push({
            id: medicine.id,
            name: medicine.name,
            quantity: qty,
            dosage: combined,
            frequency: medicine.frequency || 'Once daily',
            duration: medicine.duration || 1,
            instructions: '' ,
            unit_price: medicine.unit_price
        });
    });

    if (!validationPassed) {
        return false;
    }

    // Update hidden input with validated data
    document.getElementById('selectedMedicinesJson').value = JSON.stringify(medicineData);
    return true;
}

// Handle prescription form submission
function handlePrescriptionSubmit() {
    // Sync data from DOM first
    syncSelectedMedicinesFromDOM();

    // Validate the form
    if (!validatePrescriptionForm()) {
        return false; // Stop submission if validation fails
    }
    
    // Disable submit button and show loading state
    const submitBtn = document.querySelector('#prescriptionForm button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
    }
    
    // Allow the form to submit normally
    return true;
}

// Sync selected medicines from DOM
function syncSelectedMedicinesFromDOM() {
    try {
        const listDiv = document.getElementById('selectedMedicinesList');
        if (!listDiv) return;

        selectedMedicines = selectedMedicines.map(m => {
            const div = Array.from(listDiv.querySelectorAll('div[data-medicine-id]')).find(d => d.dataset.medicineId == m.id);
            if (!div) return m;

            const qtyInput = div.querySelector('input[name$="[quantity]"]');
            const textInput = div.querySelector('input[type="text"]');

            return {
                ...m,
                quantity: qtyInput ? Number(qtyInput.value) : m.quantity,
                // combined dosage/instructions stored in dosage property
                dosage: textInput ? textInput.value : m.dosage,
                frequency: m.frequency || 'Once daily',
                duration: m.duration || 1,
                instructions: ''
            };
        });
    } catch (e) {
        console.warn('syncSelectedMedicinesFromDOM failed', e);
    }
}

// Medicine search event handler
document.addEventListener('DOMContentLoaded', function() {
    let medicineSearchTimeout;
    const medicineSearchInput = document.getElementById('medicineSearch');
    
    if (medicineSearchInput) {
        medicineSearchInput.addEventListener('input', function() {
            clearTimeout(medicineSearchTimeout);
            const query = this.value.trim();
            
            const clearBtn = document.getElementById('clearMedicineSearch');
            if (query.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
                document.getElementById('medicineResults').classList.add('hidden');
                return;
            }

            if (query.length < 2) return;

            medicineSearchTimeout = setTimeout(() => {
                const url = `${window.location.origin}/KJ/doctor/search_medicines?q=${encodeURIComponent(query)}`;
                fetch(url)
                    .then(response => response.json())
                    .then(medicines => {
                        console.log('Medicines found:', medicines); // Debug log
                        displayMedicineResults(medicines);
                    })
                    .catch(error => console.error('Error searching medicines:', error));
            }, 300);
        });

        // Prevent form submission on enter in search field
        medicineSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                return false;
            }
        });
    }
});

// Display medicine search results
function displayMedicineResults(medicines) {
    console.log('Displaying medicines:', medicines); // Debug log
    const resultsDiv = document.getElementById('medicineResults');
    if (!resultsDiv) return;
    
    resultsDiv.innerHTML = '';

    if (!Array.isArray(medicines) || medicines.length === 0) {
        resultsDiv.innerHTML = '<div class="p-3 text-gray-500">No medicines found</div>';
    } else {
        medicines.forEach(medicine => {
            const isSelected = selectedMedicines.some(selected => selected.id === medicine.id);
            const div = document.createElement('div');
            div.className = `p-3 hover:bg-gray-100 cursor-pointer border-b ${isSelected ? 'bg-green-50' : ''}`;
            div.innerHTML = `
                <div class="font-medium">${medicine.name || 'Unknown Medicine'}</div>
                <div class="text-sm text-gray-600">${medicine.generic_name || ''} ${medicine.strength || ''} - Tsh ${parseFloat(medicine.unit_price || 0).toLocaleString('en-US')}</div>
                <div class="text-xs text-gray-500">Stock: ${medicine.stock_quantity || 0} ${medicine.unit || 'units'}</div>
            `;

            if (!isSelected) {
                div.addEventListener('click', () => {
                    console.log('Medicine clicked:', medicine); // Debug log
                    addMedicine(medicine);
                });
            }

            resultsDiv.appendChild(div);
        });
    }

    resultsDiv.classList.remove('hidden');
}

// Add medicine to selection
function addMedicine(medicine) {
    console.log('Adding medicine:', medicine); // Debug log
    if (!selectedMedicines.some(selected => selected.id === medicine.id)) {
        const newMedicine = {
            ...medicine,
            quantity: 1,
            dosage: '',
            instructions: ''
        };
        selectedMedicines.push(newMedicine);
        console.log('Updated selectedMedicines:', selectedMedicines); // Debug log
        
        // Update both the UI and hidden input
        updateSelectedMedicinesList();
        // keep the hidden input synced with controller-compatible defaults
        const payload = selectedMedicines.map(m => ({
            id: m.id,
            quantity: m.quantity,
            dosage: m.dosage || '',
            frequency: m.frequency || 'Once daily',
            duration: m.duration || 1,
            instructions: ''
        }));
        document.getElementById('selectedMedicinesJson').value = JSON.stringify(payload);
        
        // Clear search
        document.getElementById('medicineResults').classList.add('hidden');
        document.getElementById('medicineSearch').value = '';
        document.getElementById('clearMedicineSearch').classList.add('hidden');
        
        // Focus on the quantity input of the newly added medicine
        setTimeout(() => {
            const newMedicineDiv = document.querySelector(`div[data-medicine-id="${medicine.id}"]`);
            if (newMedicineDiv) {
                const qtyInput = newMedicineDiv.querySelector('input[name$="[quantity]"]');
                if (qtyInput) {
                    qtyInput.focus();
                }
            }
        }, 100);
    }
}

// Remove medicine from selection
function removeMedicine(medicineId) {
    selectedMedicines = selectedMedicines.filter(medicine => medicine.id !== medicineId);
    updateSelectedMedicinesList();
}

// Update medicine details
function updateMedicineDetails(medicineId, field, value) {
    const medicine = selectedMedicines.find(m => m.id === medicineId);
    if (!medicine) return;

    if (field === 'quantity') {
        let quantity = parseInt(value, 10);
        const stock = Number(medicine.stock_quantity);
        if (isNaN(quantity) || quantity < 1) {
            quantity = 1;
        }
        if (quantity > stock) {
            alert(`Cannot prescribe more than available stock (${stock})`);
            quantity = stock;
        }
        medicine[field] = quantity;
    } else {
        medicine[field] = value;
    }
}

// Update the selected medicines list display
function updateSelectedMedicinesList() {
    const listDiv = document.getElementById('selectedMedicinesList');
    listDiv.innerHTML = '';

    if (selectedMedicines.length === 0) {
        listDiv.innerHTML = '<div class="text-gray-500 text-sm">No medicines selected</div>';
        return;
    }

    selectedMedicines.forEach(medicine => {
        const div = document.createElement('div');
        div.className = 'p-3 bg-white border rounded-md';
        div.setAttribute('data-medicine-id', medicine.id);
        div.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <div>
                    <div class="font-medium">${medicine.name}</div>
                    <div class="text-sm text-gray-600">${medicine.generic_name} ${medicine.strength} - Tsh ${parseFloat(medicine.unit_price).toLocaleString('en-US')}</div>
                    <div class="text-xs text-gray-500">Available: ${medicine.stock_quantity} ${medicine.unit}</div>
                </div>
                <button type="button" onclick="removeMedicine(${medicine.id})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs text-gray-600">Quantity</label>
                    <input type="number" name="medicines[${medicine.id}][quantity]" 
                           min="1" max="${medicine.stock_quantity}" value="${medicine.quantity}"
                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                           onchange="updateMedicineDetails(${medicine.id}, 'quantity', this.value)">
                </div>
                <div>
                    <label class="block text-xs text-gray-600">Dosage / Instructions</label>
                    <input type="text" name="medicines[${medicine.id}][dosage]" 
                           value="${medicine.dosage}"
                           placeholder="e.g., 500mg, 1 tab twice daily after meals"
                           class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                           onchange="updateMedicineDetails(${medicine.id}, 'dosage', this.value)">
                </div>
            </div>
        `;
        listDiv.appendChild(div);
    });
}

// Clear medicine search
function clearMedicineSearch() {
    document.getElementById('medicineSearch').value = '';
    document.getElementById('clearMedicineSearch').classList.add('hidden');
    document.getElementById('medicineResults').classList.add('hidden');
}

// Reset prescription form completely
function resetPrescriptionForm() {
    selectedMedicines = [];
    updateSelectedMedicinesList();
    
    const form = document.getElementById('prescriptionForm');
    form.reset();
    document.getElementById('selectedMedicinesJson').value = '[]';
    
    // Clear search field and hide results
    document.getElementById('medicineSearch').value = '';
    document.getElementById('medicineResults').classList.add('hidden');
    document.getElementById('clearMedicineSearch').classList.add('hidden');
    
    // Reset submit button state
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Submit Prescription';
    }
}

// Close modal handlers
document.getElementById('closePrescriptionModal').addEventListener('click', () => {
    document.getElementById('prescriptionModal').classList.add('hidden');
    resetPrescriptionForm();
});

document.getElementById('cancelPrescription').addEventListener('click', () => {
    document.getElementById('prescriptionModal').classList.add('hidden');
    resetPrescriptionForm();
});

// Hide search results when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#medicineSearch') && !e.target.closest('#medicineResults')) {
        document.getElementById('medicineResults')?.classList.add('hidden');
    }
});

// Event handler for prescribe buttons
document.querySelectorAll('.prescribe-btn').forEach(button => {
    button.addEventListener('click', function() {
        const patientId = this.getAttribute('data-patient-id');
        document.getElementById('prescriptionPatientId').value = patientId;
        selectedMedicines = []; // Reset selected medicines
        updateSelectedMedicinesList();
        
        // Reset the entire form
        const form = document.getElementById('prescriptionForm');
        form.reset();
        document.getElementById('selectedMedicinesJson').value = '[]';
        
        // Clear search field and hide results
        document.getElementById('medicineSearch').value = '';
        document.getElementById('medicineResults').classList.add('hidden');
        document.getElementById('clearMedicineSearch').classList.add('hidden');
        
        // Reset submit button state (in case it was stuck loading)
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Submit Prescription';
        }
        
        document.getElementById('prescriptionModal').classList.remove('hidden');
    });
});

// Open prescription modal from details modal button
const openPrescriptionFromDetails = document.getElementById('openPrescriptionFromDetails');
if (openPrescriptionFromDetails) {
    openPrescriptionFromDetails.addEventListener('click', function() {
        // Try to read patient id from the details modal data (set when opening details)
        const patientNameElem = document.getElementById('patientName');
        const patientName = patientNameElem ? patientNameElem.textContent : '';
        // Try to locate first matching row in the table to get patient id via data attribute
        // Fallback: read from hidden prescriptionPatientId
        let pid = document.getElementById('prescriptionPatientId').value || '';
        if (!pid) {
            // look through view-details buttons for a matching patient name
            const btn = Array.from(document.querySelectorAll('.view-details-btn')).find(b => b.getAttribute('data-patient-name') === patientName);
            if (btn) pid = btn.getAttribute('data-patient-id');
        }

        if (!pid) {
            alert('Unable to determine patient for prescription. Please open Prescribe from the patient row.');
            return;
        }

        document.getElementById('prescriptionPatientId').value = pid;
        selectedMedicines = [];
        updateSelectedMedicinesList();
        
        // Reset the entire form
        const form = document.getElementById('prescriptionForm');
        form.reset();
        document.getElementById('selectedMedicinesJson').value = '[]';
        
        // Clear search field and hide results
        document.getElementById('medicineSearch').value = '';
        document.getElementById('medicineResults').classList.add('hidden');
        document.getElementById('clearMedicineSearch').classList.add('hidden');
        
        // Reset submit button state
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Submit Prescription';
        }
        
        document.getElementById('prescriptionModal').classList.remove('hidden');
        // Close the details modal
        document.getElementById('testDetailsModal').classList.add('hidden');
    });
}
</script>

<script>
// Initialize the Details Modal functionality when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    const testDetailsModal = document.getElementById('testDetailsModal');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const closeModal = document.getElementById('closeModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const printResultBtn = document.getElementById('printResultBtn');
    
    // Get all view details buttons
    const viewDetailsButtons = document.querySelectorAll('.view-details-btn');
    
    // Function to open modal
    function openModal(testData) {
        // Populate modal with test data
        document.getElementById('patientName').textContent = testData.patientName;
        document.getElementById('testName').textContent = testData.testName;
        document.getElementById('resultValue').textContent = testData.resultValue || 'N/A';
        document.getElementById('resultUnit').textContent = testData.resultUnit || '';
        document.getElementById('resultNotes').textContent = testData.resultText || 'No notes available';
        document.getElementById('completedAt').textContent = testData.completedAt ? `Completed: ${testData.completedAt}` : '';
        
        // Set status badge color
        const statusBadge = document.getElementById('statusBadge');
        statusBadge.textContent = testData.status.charAt(0).toUpperCase() + testData.status.slice(1);
        
        switch (testData.status) {
            case 'pending':
                statusBadge.className = 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800';
                break;
            case 'completed':
                statusBadge.className = 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800';
                break;
            case 'reviewed':
                statusBadge.className = 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800';
                break;
            default:
                statusBadge.className = 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800';
        }
        
        // Show the modal
        testDetailsModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        
        // Animate in
        setTimeout(() => {
            modalBackdrop.classList.add('opacity-100');
        }, 50);
    }
    
    // Function to close modal
    function closeModalHandler() {
        // Hide the modal
        testDetailsModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    
    // Add click event to all view details buttons
    viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function() {
            const testData = {
                testId: this.getAttribute('data-test-id'),
                patientName: this.getAttribute('data-patient-name'),
                testName: this.getAttribute('data-test-name'),
                resultValue: this.getAttribute('data-result-value'),
                resultUnit: this.getAttribute('data-result-unit'),
                resultText: this.getAttribute('data-result-text'),
                completedAt: this.getAttribute('data-completed-at'),
                status: this.getAttribute('data-status')
            };
            
            openModal(testData);
        });
    });
    
    // Close modal events
    closeModal.addEventListener('click', closeModalHandler);
    closeModalBtn.addEventListener('click', closeModalHandler);
    modalBackdrop.addEventListener('click', closeModalHandler);
    
    // Print result
    printResultBtn.addEventListener('click', function() {
        // Create a printable version of the test details
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Lab Test Result</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        margin: 20px;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    .header h1 {
                        margin-bottom: 5px;
                    }
                    .details {
                        margin-bottom: 20px;
                    }
                    .result-box {
                        border: 1px solid #ddd;
                        padding: 15px;
                        margin-bottom: 20px;
                    }
                    .footer {
                        margin-top: 40px;
                        border-top: 1px solid #ddd;
                        padding-top: 10px;
                        font-size: 12px;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Laboratory Test Result</h1>
                    <p>HOSPITALI Medical Center</p>
                </div>
                <div class="details">
                    <p><strong>Patient Name:</strong> ${document.getElementById('patientName').textContent}</p>
                    <p><strong>Test Name:</strong> ${document.getElementById('testName').textContent}</p>
                    <p><strong>Date:</strong> ${document.getElementById('completedAt').textContent.replace('Completed: ', '')}</p>
                </div>
                <div class="result-box">
                    <h3>Test Results</h3>
                    <p><strong>Result Value:</strong> ${document.getElementById('resultValue').textContent} ${document.getElementById('resultUnit').textContent}</p>
                    <p><strong>Status:</strong> ${document.getElementById('statusBadge').textContent}</p>
                    <p><strong>Notes:</strong> ${document.getElementById('resultNotes').textContent}</p>
                </div>
                <div class="footer">
                    <p>This is an automatically generated report. Please consult with your physician to interpret these results.</p>
                    <p>Printed on: ${new Date().toLocaleString()}</p>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        setTimeout(() => {
            printWindow.print();
        }, 500);
    });
});
</script>