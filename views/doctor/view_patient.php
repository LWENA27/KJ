<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Patient Details</h1>
        <div class="flex space-x-3">
            <button onclick="attendPatient(<?php echo $patient['id']; ?>)"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-stethoscope mr-2"></i>Attend Patient
            </button>
            <a href="/KJ/doctor/view_lab_results/<?php echo $patient['id']; ?>"
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-vial mr-2"></i>View Lab Results
            </a>
            <a href="/KJ/doctor/patient_journey/<?php echo $patient['id']; ?>"
               class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-route mr-2"></i>View Journey
            </a>
            <a href="/KJ/doctor/patients" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i>Back to Patients
            </a>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-user mr-3 text-blue-600"></i>Patient Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo htmlspecialchars($patient['first_name']); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo htmlspecialchars($patient['last_name']); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    <?php
                    $dob = $patient['date_of_birth'] ?? null;
                    if (!empty($dob)) {
                        $ts = strtotime($dob);
                        if ($ts !== false) {
                            echo date('M j, Y', $ts);
                            $age = date_diff(date_create($dob), date_create('today'))->y;
                            echo " ({$age} years old)";
                        } else {
                            echo 'Not provided';
                        }
                    } else {
                        echo 'Not provided';
                    }
                    ?>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo ucfirst(htmlspecialchars($patient['gender'] ?? 'Not specified')); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo htmlspecialchars($patient['phone'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo htmlspecialchars($patient['email'] ?? 'Not provided'); ?></p>
            </div>
            <div class="md:col-span-2 lg:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo htmlspecialchars($patient['address'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Occupation</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo htmlspecialchars($patient['occupation'] ?? 'Not specified'); ?></p>
            </div>
        </div>
    </div>

    <!-- Emergency Contact -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-phone mr-3 text-red-600"></i>Emergency Contact
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo htmlspecialchars($patient['emergency_contact_name'] ?? 'Not provided'); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo htmlspecialchars($patient['emergency_contact_phone'] ?? 'Not provided'); ?></p>
            </div>
        </div>
    </div>

    <!-- Vital Signs -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-heartbeat mr-3 text-red-600"></i>Vital Signs (Collected by Receptionist)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Temperature</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    <?php echo htmlspecialchars($patient['temperature'] ?? 'Not recorded'); ?>
                    <?php if (!empty($patient['temperature'])) echo '°C'; ?>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    <?php echo htmlspecialchars($patient['blood_pressure'] ?? 'Not recorded'); ?>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pulse Rate</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    <?php echo htmlspecialchars($patient['pulse_rate'] ?? 'Not recorded'); ?>
                    <?php if (!empty($patient['pulse_rate'])) echo ' bpm'; ?>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Body Weight</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    <?php echo htmlspecialchars($patient['body_weight'] ?? 'Not recorded'); ?>
                    <?php if (!empty($patient['body_weight'])) echo ' kg'; ?>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Height</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    <?php echo htmlspecialchars($patient['height'] ?? 'Not recorded'); ?>
                    <?php if (!empty($patient['height'])) echo ' cm'; ?>
                </p>
            </div>
            <?php if (!empty($patient['body_weight']) && !empty($patient['height'])): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">BMI</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    <?php
                    $bmi = $patient['body_weight'] / (($patient['height'] / 100) ** 2);
                    echo number_format($bmi, 1);
                    ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Consultation History -->
    <?php if (!empty($consultations)): ?>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-history mr-3 text-purple-600"></i>Consultation History
        </h3>
        <div class="space-y-4">
            <?php foreach ($consultations as $consultation): ?>
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium text-gray-900">
                            <?php echo date('M j, Y \a\t H:i', strtotime($consultation['appointment_date'])); ?>
                        </p>
                        <?php if (!empty($consultation['main_complaint'])): ?>
                        <p class="text-sm text-gray-600 mt-1">
                            <strong>M/C:</strong> <?php echo htmlspecialchars($consultation['main_complaint']); ?>
                        </p>
                        <?php endif; ?>
                        <?php if (!empty($consultation['final_diagnosis'])): ?>
                        <p class="text-sm text-gray-600 mt-1">
                            <strong>Diagnosis:</strong> <?php echo htmlspecialchars($consultation['final_diagnosis']); ?>
                        </p>
                        <?php endif; ?>
                        <?php if (!empty($consultation['prescription'])): ?>
                        <p class="text-sm text-gray-600 mt-1">
                            <strong>RX:</strong> <?php echo htmlspecialchars($consultation['prescription']); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                        <?php
                        switch ($consultation['status']) {
                            case 'scheduled':
                                echo 'bg-yellow-100 text-yellow-800';
                                break;
                            case 'in_progress':
                                echo 'bg-blue-100 text-blue-800';
                                break;
                            case 'completed':
                                echo 'bg-green-100 text-green-800';
                                break;
                            case 'cancelled':
                                echo 'bg-red-100 text-red-800';
                                break;
                            default:
                                echo 'bg-gray-100 text-gray-800';
                        }
                        ?>">
                        <?php echo ucfirst($consultation['status']); ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Attend Patient Modal -->
<div id="attendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-5/6 lg:w-4/5 shadow-lg rounded-md bg-white max-h-screen overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4 sticky top-0 bg-white pb-4 border-b">
                <h3 class="text-lg font-medium text-gray-900">Complete Patient Consultation</h3>
                <button onclick="closeAttendModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="attendForm" method="POST" action="/KJ/doctor/start_consultation" onsubmit="return validateConsultationForm()" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" id="attendPatientId" name="patient_id">
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
                                <input type="radio" name="next_step" value="lab_tests" class="mr-2" onchange="toggleSection('lab')">
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

                <div class="flex justify-end space-x-3 pt-4 border-t sticky bottom-0 bg-white">
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
let selectedTests = [];
let selectedMedicines = [];

// Form validation before submission
function validateConsultationForm() {
    const nextStep = document.querySelector('input[name="next_step"]:checked');

    if (!nextStep) {
        alert('Please select a next step decision (Lab Tests, Medicine, Both, or Discharge)');
        return false;
    }

    // Check if tests are selected when lab option is chosen
    if ((nextStep.value === 'lab' || nextStep.value === 'both') && selectedTests.length === 0) {
        alert('Please select at least one lab test');
        return false;
    }

    // Check if medicines are selected when medicine option is chosen
    if ((nextStep.value === 'medicine' || nextStep.value === 'both') && selectedMedicines.length === 0) {
        alert('Please select at least one medicine');
        return false;
    }

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
        if (medicine.quantity > medicine.stock_quantity) {
            alert(`Cannot prescribe ${medicine.quantity} units of ${medicine.name}. Only ${medicine.stock_quantity} available in stock.`);
            return false;
        }
    }

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
}

function closeAttendModal() {
    document.getElementById('attendModal').classList.add('hidden');
}

function toggleSection(section) {
    const labSection = document.getElementById('labSection');
    const medicineSection = document.getElementById('medicineSection');
    
    // Hide all sections first
    labSection.classList.add('hidden');
    medicineSection.classList.add('hidden');
    
    // Show relevant sections
    if (section === 'lab' || section === 'both') {
        labSection.classList.remove('hidden');
    }
    if (section === 'medicine' || section === 'both') {
        medicineSection.classList.remove('hidden');
    }
}

// Lab Tests Search
let testSearchTimeout;
let currentTestFocus = -1;

document.getElementById('testSearch').addEventListener('input', function() {
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
});// Add keyboard navigation for test search
document.getElementById('testSearch').addEventListener('keydown', function(e) {
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
                <div class="text-sm text-gray-600">${test.category} - $${test.price}</div>
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
    if (!selectedTests.some(selected => selected.id === test.id)) {
        selectedTests.push(test);
        updateSelectedTestsList();
        document.getElementById('testResults').classList.add('hidden');
        document.getElementById('testSearch').value = '';
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
                    <div class="text-sm text-gray-600">${test.category} - $${test.price}</div>
                </div>
                <button type="button" onclick="removeTest(${test.id})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            `;
            listDiv.appendChild(div);
        });
    }
    
    // Update hidden field
    document.getElementById('selectedTests').value = JSON.stringify(selectedTests.map(test => test.id));
}

// Medicine Search
let medicineSearchTimeout;
let currentMedicineFocus = -1;

document.getElementById('medicineSearch').addEventListener('input', function() {
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
});// Add keyboard navigation for medicine search
document.getElementById('medicineSearch').addEventListener('keydown', function(e) {
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
                <div class="text-sm text-gray-600">${medicine.generic_name} - $${medicine.unit_price}</div>
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

function updateMedicineDetails(medicineId, field, value) {
    const medicine = selectedMedicines.find(m => m.id === medicineId);
    if (medicine) {
        if (field === 'quantity') {
            const quantity = parseInt(value);
            if (quantity > medicine.stock_quantity) {
                alert(`Cannot prescribe more than available stock (${medicine.stock_quantity})`);
                // Reset to maximum available
                value = medicine.stock_quantity;
                // Update the input field
                event.target.value = value;
            } else if (quantity < 1) {
                value = 1;
                event.target.value = value;
            }
        }

        medicine[field] = value;
        // Update hidden field
        document.getElementById('selectedMedicines').value = JSON.stringify(selectedMedicines);
    }
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
                        <div class="text-sm text-gray-600">${medicine.generic_name} - $${medicine.unit_price}</div>
                    </div>
                    <button type="button" onclick="removeMedicine(${medicine.id})" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                    <div>
                        <label class="block text-xs text-gray-600">Quantity</label>
                        <input type="number" min="1" value="${medicine.quantity}" 
                               onchange="updateMedicineDetails(${medicine.id}, 'quantity', this.value)"
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
document.getElementById('attendModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAttendModal();
    }
});
</script>
