<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attend Patient</title>
    <!-- Add Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Add Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-6">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Patient Consultation</h1>
                <a href="/KJ/doctor/view_patient/<?php echo $patient['id']; ?>" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Patient Record
                </a>
            </div>
        </div>

        <!-- Patient Info Summary -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <span class="text-gray-600">Patient Name:</span>
                    <span class="font-medium"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></span>
                </div>
                <div>
                    <span class="text-gray-600">Patient RegNo:</span>
                    <span class="font-medium"><?php echo str_pad($patient['registration_number'], 6, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div>
                    <span class="text-gray-600">Date:</span>
                    <span class="font-medium"><?php echo date('d/m/Y'); ?></span>
                </div>
            </div>
        </div>

        <!-- Main Consultation Form -->
        <div class="bg-white rounded-lg shadow">
            <form id="attendForm" method="POST" action="/KJ/doctor/start_consultation" 
                  onsubmit="console.log('🚀 FORM SUBMITTING NOW...'); return validateConsultationForm();" class="p-6 space-y-6">
                
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">
                <input type="hidden" id="selectedTests" name="selected_tests" value="">
                <input type="hidden" id="selectedMedicines" name="selected_medicines" value="">

                <!-- Examination Section -->
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="text-lg font-medium text-blue-900 mb-4">Clinical Examination</h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- M/C (Main Complaint) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">M/C - Main Complaint *</label>
                            <textarea id="mainComplaint" name="main_complaint" rows="3" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Patient's main complaint and symptoms..."></textarea>
                        </div>

                        <!-- O/E (On Examination) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">O/E - On Examination *</label>
                            <textarea id="onExamination" name="on_examination" rows="3" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Physical examination findings..."></textarea>
                        </div>

                        <!-- Preliminary Diagnosis -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preliminary Diagnosis</label>
                            <textarea id="preliminaryDiagnosis" name="preliminary_diagnosis" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Initial working diagnosis..."></textarea>
                        </div>

                        <!-- Final Diagnosis -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Final Diagnosis</label>
                            <textarea id="finalDiagnosis" name="final_diagnosis" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Final confirmed diagnosis..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Decision Section -->
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h4 class="text-lg font-medium text-yellow-900 mb-4">Next Steps Decision</h4>
                    <div class="space-y-4">
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="next_step" value="lab_tests" class="mr-2" onchange="toggleSection('lab_tests')">
                                <span class="text-sm font-medium">Send to Lab for Tests</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="next_step" value="medicine" class="mr-2" onchange="toggleSection('medicine')">
                                <span class="text-sm font-medium">Prescribe Medicine</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="next_step" value="both" class="mr-2" onchange="toggleSection('both')">
                                <span class="text-sm font-medium">Both Lab & Medicine</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="next_step" value="discharge" class="mr-2" onchange="toggleSection('none')">
                                <span class="text-sm font-medium">Discharge Patient</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Lab Tests Section -->
                <div id="labSection" class="bg-purple-50 p-4 rounded-lg hidden">
                    <h4 class="text-lg font-medium text-purple-900 mb-4">Laboratory Tests</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search & Select Lab Tests</label>
                            <div class="relative">
                                <div class="flex">
                                    <input type="text" id="testSearch" placeholder="Type to search for lab tests..."
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <button type="button" onclick="clearTestSearch()" id="clearTestSearch"
                                        class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200 hidden">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="testResults" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 hidden max-h-60 overflow-y-auto shadow-lg"></div>
                            </div>
                        </div>
                        <div id="selectedTestsList" class="space-y-2">
                            <!-- Selected tests will appear here -->
                        </div>
                    </div>
                </div>

                <!-- Medicine Section -->
                <div id="medicineSection" class="bg-green-50 p-4 rounded-lg hidden">
                    <h4 class="text-lg font-medium text-green-900 mb-4">Medicine Prescription</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search & Select Medicines</label>
                            <div class="relative">
                                <div class="flex">
                                    <input type="text" id="medicineSearch" placeholder="Type to search for medicines..."
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <button type="button" onclick="clearMedicineSearch()" id="clearMedicineSearch"
                                        class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200 hidden">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="medicineResults" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 hidden max-h-60 overflow-y-auto shadow-lg"></div>
                            </div>
                        </div>
                        <div id="selectedMedicinesList" class="space-y-2">
                            <!-- Selected medicines will appear here -->
                        </div>
                    </div>
                </div>

                <!-- Treatment Plan -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Treatment Plan & Advice</h4>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Treatment Plan & Instructions</label>
                        <textarea id="treatmentPlan" name="treatment_plan" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Treatment plan, follow-up instructions, lifestyle advice..."></textarea>
                    </div>
                </div>

                <div class="bg-white border-t p-6 rounded-b-lg">
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeAttendModal()"
                            class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-150">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit"
                            class="px-8 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-150">
                            <i class="fas fa-save mr-2"></i>Complete Consultation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Keep your existing JavaScript -->
    <script>
        let selectedTests = [];
        let selectedMedicines = [];

        // Form validation before submission
        function validateConsultationForm() {
            console.log('=== FORM VALIDATION STARTED ===');
            const nextStep = document.querySelector('input[name="next_step"]:checked');
            console.log('Next step value:', nextStep ? nextStep.value : 'NONE');
            console.log('Selected tests:', selectedTests);
            console.log('Selected medicines:', selectedMedicines);

            if (!nextStep) {
                alert('Please select a next step decision (Lab Tests, Medicine, Both, or Discharge)');
                return false;
            }

            // Check if tests are selected when lab option is chosen
            if ((nextStep.value === 'lab_tests' || nextStep.value === 'both') && selectedTests.length === 0) {
                alert('Please select at least one lab test');
                return false;
            }

            // Check if medicines are selected when medicine option is chosen
            if ((nextStep.value === 'medicine' || nextStep.value === 'both') && selectedMedicines.length === 0) {
                alert('Please select at least one medicine');
                return false;
            }
            
            console.log('✅✅✅ Validation passed! Form will submit. ✅✅✅');
            console.log('=== FORM VALIDATION ENDED ===');

            // Validate medicine quantities and details
            for (const medicine of selectedMedicines) {
                if (!medicine.dosage.trim()) {
                    alert(`Please specify dosage for ${medicine.name}`);
                    return false;
                }
                if (!medicine.instructions.trim()) {
                    alert(`Please specify instructions for ${medicine.name}`);
                    return false;
                }
                // Ensure numeric comparison (quantities may be strings after being edited)
                const qty = Number(medicine.quantity);
                const stock = Number(medicine.stock_quantity);
                if (isNaN(qty) || qty < 1) {
                    alert(`Please enter a valid quantity for ${medicine.name}`);
                    return false;
                }
                if (qty > stock) {
                    alert(`Cannot prescribe ${qty} units of ${medicine.name}. Only ${stock} available in stock.`);
                    return false;
                }
            }

            console.log('🚀🚀🚀 RETURNING TRUE - FORM WILL NOW SUBMIT TO SERVER 🚀🚀🚀');
            return true;
        }

        function viewPatientDetails(patientId) {
            window.location.href = '/KJ/doctor/view_patient/' + patientId;
        }

        function attendPatient(patientId) {
            document.getElementById('attendPatientId').value = patientId;
            document.getElementById('attendModal').classList.remove('hidden');
            document.getElementById('attendForm').reset();
            selectedTests = [];
            selectedMedicines = [];
            updateSelectedTestsList();
            updateSelectedMedicinesList();
            // Prevent body scrolling when modal is open
            document.body.style.overflow = 'hidden';
        }

        function closeAttendModal() {
            document.getElementById('attendModal').classList.add('hidden');
            // Restore body scrolling
            document.body.style.overflow = 'auto';
        }

        function toggleSection(section) {
            const labSection = document.getElementById('labSection');
            const medicineSection = document.getElementById('medicineSection');

            // Hide all sections first
            labSection.classList.add('hidden');
            medicineSection.classList.add('hidden');

            // Show relevant sections
            if (section === 'lab_tests' || section === 'both') {
                labSection.classList.remove('hidden');
            }
            if (section === 'medicine' || section === 'both') {
                medicineSection.classList.remove('hidden');
            }
        }

        // Lab Tests Search
        let testSearchTimeout;
        let currentTestFocus = -1;

        const testSearchElement = document.getElementById('testSearch');
        if (testSearchElement) {
            testSearchElement.addEventListener('input', function() {
            clearTimeout(testSearchTimeout);
            const query = this.value.trim();

            // Show/hide clear button
            const clearBtn = document.getElementById('clearTestSearch');
            if (query.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }

            if (query.length < 2) {
                document.getElementById('testResults').classList.add('hidden');
                return;
            }

            // Show loading state
            showTestLoading();

            testSearchTimeout = setTimeout(() => {
                fetch(`/KJ/doctor/search_tests?q=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(tests => {
                        displayTestResults(tests);
                    })
                    .catch(error => {
                        console.error('Error searching tests:', error);
                        showTestError(error.message);
                    });
            }, 300);
            }); // End input event listener
            
            // Add keyboard navigation for test search
            testSearchElement.addEventListener('keydown', function(e) {
            const resultsDiv = document.getElementById('testResults');
            const items = resultsDiv.querySelectorAll('.search-result-item');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentTestFocus = Math.min(currentTestFocus + 1, items.length - 1);
                updateTestFocus(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentTestFocus = Math.max(currentTestFocus - 1, -1);
                updateTestFocus(items);
            } else if (e.key === 'Enter' && currentTestFocus >= 0) {
                e.preventDefault();
                items[currentTestFocus].click();
            } else if (e.key === 'Escape') {
                resultsDiv.classList.add('hidden');
                currentTestFocus = -1;
            }
            });
        } // End testSearchElement check

        function showTestLoading() {
            const resultsDiv = document.getElementById('testResults');
            resultsDiv.innerHTML = '<div class="p-3 text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Searching...</div>';
            resultsDiv.classList.remove('hidden');
        }

        function showTestError(message) {
            const resultsDiv = document.getElementById('testResults');
            resultsDiv.innerHTML = `<div class="p-3 text-red-500"><i class="fas fa-exclamation-triangle mr-2"></i>${message}</div>`;
            resultsDiv.classList.remove('hidden');
        }

        function updateTestFocus(items) {
            items.forEach((item, index) => {
                if (index === currentTestFocus) {
                    item.classList.add('bg-blue-100');
                } else {
                    item.classList.remove('bg-blue-100');
                }
            });
        }

        function displayTestResults(tests) {
            const resultsDiv = document.getElementById('testResults');
            resultsDiv.innerHTML = '';
            currentTestFocus = -1;

            if (tests.length === 0) {
                resultsDiv.innerHTML = '<div class="p-3 text-gray-500">No tests found</div>';
            } else {
                tests.forEach((test, index) => {
                    const isSelected = selectedTests.some(selected => selected.id === test.id);
                    const div = document.createElement('div');
                    div.className = `p-3 hover:bg-gray-100 cursor-pointer border-b search-result-item ${isSelected ? 'bg-blue-50' : ''}`;
                    div.setAttribute('data-index', index);
                    div.innerHTML = `
                    <div class="font-medium">${test.name}</div>
                    <div class="text-sm text-gray-600">${test.category} - Tsh ${parseFloat(test.price).toLocaleString('en-US')}</div>
                    <div class="text-xs text-gray-500">${test.description || ''}</div>
                `;

                    if (!isSelected) {
                        div.addEventListener('click', () => addTest(test));
                    }

                    resultsDiv.appendChild(div);
                });
            }

            resultsDiv.classList.remove('hidden');
        }

        function addTest(test) {
            console.log('Adding test:', test);
            if (!selectedTests.some(selected => selected.id === test.id)) {
                selectedTests.push(test);
                console.log('Test added. Total selected tests:', selectedTests.length);
                updateSelectedTestsList();
                document.getElementById('testResults').classList.add('hidden');
                document.getElementById('testSearch').value = '';
            } else {
                console.log('Test already selected');
            }
        }

        function removeTest(testId) {
            selectedTests = selectedTests.filter(test => test.id !== testId);
            updateSelectedTestsList();
        }

        function updateSelectedTestsList() {
            const listDiv = document.getElementById('selectedTestsList');
            listDiv.innerHTML = '';

            if (selectedTests.length === 0) {
                listDiv.innerHTML = '<div class="text-gray-500 text-sm">No tests selected</div>';
            } else {
                selectedTests.forEach(test => {
                    const div = document.createElement('div');
                    div.className = 'flex justify-between items-center p-3 bg-white border rounded-md';
                    div.innerHTML = `
                    <div>
                        <div class="font-medium">${test.name}</div>
                        <div class="text-sm text-gray-600">${test.category} - Tsh ${parseFloat(test.price).toLocaleString('en-US')}</div>
                    </div>
                    <button type="button" onclick="removeTest(${test.id})" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                    listDiv.appendChild(div);
                });
            }

            // Update hidden field
            const testIds = selectedTests.map(test => test.id);
            document.getElementById('selectedTests').value = JSON.stringify(testIds);
            console.log('Updated selectedTests hidden field:', JSON.stringify(testIds));
        }

        // Medicine Search
        let medicineSearchTimeout;
        let currentMedicineFocus = -1;

        const medicineSearchElement = document.getElementById('medicineSearch');
        if (medicineSearchElement) {
            medicineSearchElement.addEventListener('input', function() {
            clearTimeout(medicineSearchTimeout);
            const query = this.value.trim();

            // Show/hide clear button
            const clearBtn = document.getElementById('clearMedicineSearch');
            if (query.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }

            if (query.length < 2) {
                document.getElementById('medicineResults').classList.add('hidden');
                return;
            }

            // Show loading state
            showMedicineLoading();

            medicineSearchTimeout = setTimeout(() => {
                fetch(`/KJ/doctor/search_medicines?q=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(medicines => {
                        displayMedicineResults(medicines);
                    })
                    .catch(error => {
                        console.error('Error searching medicines:', error);
                        showMedicineError(error.message);
                    });
            }, 300);
            }); // End input event listener
            
            // Add keyboard navigation for medicine search
            medicineSearchElement.addEventListener('keydown', function(e) {
            const resultsDiv = document.getElementById('medicineResults');
            const items = resultsDiv.querySelectorAll('.search-result-item');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentMedicineFocus = Math.min(currentMedicineFocus + 1, items.length - 1);
                updateMedicineFocus(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentMedicineFocus = Math.max(currentMedicineFocus - 1, -1);
                updateMedicineFocus(items);
            } else if (e.key === 'Enter' && currentMedicineFocus >= 0) {
                e.preventDefault();
                items[currentMedicineFocus].click();
            } else if (e.key === 'Escape') {
                resultsDiv.classList.add('hidden');
                currentMedicineFocus = -1;
            }
            });
        } // End medicineSearchElement check

        function showMedicineLoading() {
            const resultsDiv = document.getElementById('medicineResults');
            resultsDiv.innerHTML = '<div class="p-3 text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>Searching...</div>';
            resultsDiv.classList.remove('hidden');
        }

        function showMedicineError(message) {
            const resultsDiv = document.getElementById('medicineResults');
            resultsDiv.innerHTML = `<div class="p-3 text-red-500"><i class="fas fa-exclamation-triangle mr-2"></i>${message}</div>`;
            resultsDiv.classList.remove('hidden');
        }

        function updateMedicineFocus(items) {
            items.forEach((item, index) => {
                if (index === currentMedicineFocus) {
                    item.classList.add('bg-green-100');
                } else {
                    item.classList.remove('bg-green-100');
                }
            });
        }

        function displayMedicineResults(medicines) {
            const resultsDiv = document.getElementById('medicineResults');
            resultsDiv.innerHTML = '';
            currentMedicineFocus = -1;

            if (medicines.length === 0) {
                resultsDiv.innerHTML = '<div class="p-3 text-gray-500">No medicines found</div>';
            } else {
                medicines.forEach((medicine, index) => {
                    const isSelected = selectedMedicines.some(selected => selected.id === medicine.id);
                    const div = document.createElement('div');
                    div.className = `p-3 hover:bg-gray-100 cursor-pointer border-b search-result-item ${isSelected ? 'bg-green-50' : ''}`;
                    div.setAttribute('data-index', index);
                    div.innerHTML = `
                    <div class="font-medium">${medicine.name}</div>
                    <div class="text-sm text-gray-600">${medicine.generic_name} - Tsh ${parseFloat(medicine.unit_price).toLocaleString('en-US')}</div>
                    <div class="text-xs text-gray-500">Stock: ${medicine.stock_quantity}</div>
                `;

                    if (!isSelected) {
                        div.addEventListener('click', () => addMedicine(medicine));
                    }

                    resultsDiv.appendChild(div);
                });
            }

            resultsDiv.classList.remove('hidden');
        }

        function addMedicine(medicine) {
            if (!selectedMedicines.some(selected => selected.id === medicine.id)) {
                selectedMedicines.push({
                    ...medicine,
                    quantity: 1,
                    dosage: '',
                    instructions: ''
                });
                updateSelectedMedicinesList();
                document.getElementById('medicineResults').classList.add('hidden');
                document.getElementById('medicineSearch').value = '';
            }
        }

        function removeMedicine(medicineId) {
            selectedMedicines = selectedMedicines.filter(medicine => medicine.id !== medicineId);
            updateSelectedMedicinesList();
        }

        function updateMedicineDetails(medicineId, field, elementOrValue) {
            const medicine = selectedMedicines.find(m => m.id === medicineId);
            if (!medicine) return;

            // Accept either the input element (this) or the raw value
            let value = (typeof elementOrValue === 'object') ? elementOrValue.value : elementOrValue;

            if (field === 'quantity') {
                // Parse integer and clamp
                let quantity = parseInt(value, 10);
                const stock = Number(medicine.stock_quantity);
                if (isNaN(quantity) || quantity < 1) {
                    quantity = 1;
                    if (typeof elementOrValue === 'object') elementOrValue.value = quantity;
                }
                if (quantity > stock) {
                    alert(`Cannot prescribe more than available stock (${stock})`);
                    quantity = stock;
                    if (typeof elementOrValue === 'object') elementOrValue.value = quantity;
                }
                medicine[field] = quantity;
            } else {
                medicine[field] = value;
            }

            // Update hidden field
            document.getElementById('selectedMedicines').value = JSON.stringify(selectedMedicines);
        }

        function updateSelectedMedicinesList() {
            const listDiv = document.getElementById('selectedMedicinesList');
            listDiv.innerHTML = '';

            if (selectedMedicines.length === 0) {
                listDiv.innerHTML = '<div class="text-gray-500 text-sm">No medicines selected</div>';
            } else {
                selectedMedicines.forEach(medicine => {
                    const div = document.createElement('div');
                    div.className = 'p-3 bg-white border rounded-md';
                    div.innerHTML = `
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="font-medium">${medicine.name}</div>
                            <div class="text-sm text-gray-600">${medicine.generic_name} - Tsh ${parseFloat(medicine.unit_price).toLocaleString('en-US')}</div>
                        </div>
                        <button type="button" onclick="removeMedicine(${medicine.id})" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div>
                            <label class="block text-xs text-gray-600">Quantity</label>
                <input type="number" min="1" value="${medicine.quantity}" 
                    onchange="updateMedicineDetails(${medicine.id}, 'quantity', this)"
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">Dosage</label>
                <input type="text" placeholder="e.g., 500mg" value="${medicine.dosage}"
                    onchange="updateMedicineDetails(${medicine.id}, 'dosage', this.value)"
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">Instructions</label>
                <input type="text" placeholder="e.g., 2x daily" value="${medicine.instructions}"
                    onchange="updateMedicineDetails(${medicine.id}, 'instructions', this.value)"
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                        </div>
                    </div>
                `;
                    listDiv.appendChild(div);
                });
            }

            // Update hidden field
            document.getElementById('selectedMedicines').value = JSON.stringify(selectedMedicines);
        }

        // Clear search functions
        function clearTestSearch() {
            document.getElementById('testSearch').value = '';
            document.getElementById('clearTestSearch').classList.add('hidden');
            document.getElementById('testResults').classList.add('hidden');
            currentTestFocus = -1;
        }

        function clearMedicineSearch() {
            document.getElementById('medicineSearch').value = '';
            document.getElementById('clearMedicineSearch').classList.add('hidden');
            document.getElementById('medicineResults').classList.add('hidden');
            currentMedicineFocus = -1;
        }

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#testSearch') && !e.target.closest('#testResults') && !e.target.closest('#clearTestSearch')) {
                document.getElementById('testResults').classList.add('hidden');
                currentTestFocus = -1;
            }
            if (!e.target.closest('#medicineSearch') && !e.target.closest('#medicineResults') && !e.target.closest('#clearMedicineSearch')) {
                document.getElementById('medicineResults').classList.add('hidden');
                currentMedicineFocus = -1;
            }
        });

        // Close modal when clicking outside
        const attendModalElement = document.getElementById('attendModal');
        if (attendModalElement) {
            attendModalElement.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeAttendModal();
                }
            });
        }

        // Print function for medical record
        function printMedicalRecord() {
            // Hide no-print elements
            const noPrintElements = document.querySelectorAll('.no-print');
            noPrintElements.forEach(el => el.style.display = 'none');

            // Print the page
            window.print();

            // Show no-print elements again
            noPrintElements.forEach(el => el.style.display = '');
        }
    </script>

</body>
</html>