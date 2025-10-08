<!-- Print Medical Record Form -->
<div class="mb-6 no-print">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Patient Medical Record</h1>
        <div class="flex space-x-3">
            <button onclick="printMedicalRecord()"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-print mr-2"></i>Print Record
            </button>
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
</div>

<!-- Medical Record Form (Printable) -->
<div id="medicalRecord" class="bg-white border-2 border-gray-300 p-8 max-w-5xl mx-auto print:border-none print:p-4">
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
        <h2 class="text-xl font-bold text-center mb-4 underline">PATIENT RECORD</h2>
        
        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
            <div class="flex items-center">
                <span class="font-medium mr-2">DATE:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo date('d/m/Y'); ?></span>
            </div>
            <div class="flex items-center">
                <span class="font-medium mr-2">REG NO:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo str_pad($patient['id'], 6, '0', STR_PAD_LEFT); ?></span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4 text-sm">
            <div class="col-span-2 flex items-center">
                <span class="font-medium mr-2">PATIENT NAME:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></span>
            </div>
            <div class="flex items-center">
                <span class="font-medium mr-2">AGE:</span>
                <span class="border-b border-gray-400 flex-1 px-2 mr-4">
                    <?php
                    $dob = $patient['date_of_birth'] ?? null;
                    if (!empty($dob)) {
                        $age = date_diff(date_create($dob), date_create('today'))->y;
                        echo $age;
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </span>
                <span class="font-medium mr-2">SEX:</span>
                <span class="border-b border-gray-400 px-2"><?php echo strtoupper(substr($patient['gender'] ?? 'U', 0, 1)); ?></span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6 text-sm">
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

    <!-- Vital Signs -->
    <div class="mb-6">
        <div class="grid grid-cols-5 gap-4 text-sm">
            <div class="text-center">
                <div class="font-medium mb-1">Temperature</div>
                <div class="border border-gray-400 h-20 p-2 text-center">
                    <?php echo htmlspecialchars($patient['temperature'] ?? ''); ?>
                    <?php if (!empty($patient['temperature'])) echo '°C'; ?>
                </div>
            </div>
            <div class="text-center">
                <div class="font-medium mb-1">Blood Pressure</div>
                <div class="border border-gray-400 h-20 p-2 text-center">
                    <?php echo htmlspecialchars($patient['blood_pressure'] ?? ''); ?>
                </div>
            </div>
            <div class="text-center">
                <div class="font-medium mb-1">Pulse Rate</div>
                <div class="border border-gray-400 h-20 p-2 text-center">
                    <?php echo htmlspecialchars($patient['pulse_rate'] ?? ''); ?>
                    <?php if (!empty($patient['pulse_rate'])) echo ' bpm'; ?>
                </div>
            </div>
            <div class="text-center">
                <div class="font-medium mb-1">Body Weight</div>
                <div class="border border-gray-400 h-20 p-2 text-center">
                    <?php echo htmlspecialchars($patient['body_weight'] ?? ''); ?>
                    <?php if (!empty($patient['body_weight'])) echo ' kg'; ?>
                </div>
            </div>
            <div class="text-center">
                <div class="font-medium mb-1">Height</div>
                <div class="border border-gray-400 h-20 p-2 text-center">
                    <?php echo htmlspecialchars($patient['height'] ?? ''); ?>
                    <?php if (!empty($patient['height'])) echo ' cm'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Clinical Examination -->
    <div class="mb-6">
        <div class="grid grid-cols-1 gap-4 text-sm">
            <?php
            // Get latest consultation for this patient
            $latest_consultation = null;
            if (!empty($consultations)) {
                $latest_consultation = $consultations[0];
            }
            ?>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="font-medium mb-1">M/C</div>
                    <div class="border border-gray-400 h-16 p-2">
                        <?php echo htmlspecialchars($latest_consultation['main_complaint'] ?? ''); ?>
                    </div>
                </div>
                <div>
                    <div class="font-medium mb-1">O/E</div>
                    <div class="border border-gray-400 h-16 p-2">
                        <?php echo htmlspecialchars($latest_consultation['on_examination'] ?? ''); ?>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="font-medium mb-1">Preliminary Dx</div>
                    <div class="border border-gray-400 h-16 p-2">
                        <?php echo htmlspecialchars($latest_consultation['preliminary_diagnosis'] ?? ''); ?>
                    </div>
                </div>
                <div>
                    <div class="font-medium mb-1">Final Dx</div>
                    <div class="border border-gray-400 h-16 p-2">
                        <?php echo htmlspecialchars($latest_consultation['final_diagnosis'] ?? ''); ?>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="font-medium mb-1">Lab Investigation</div>
                <div class="border border-gray-400 h-16 p-2">
                    <?php echo htmlspecialchars($latest_consultation['lab_investigation'] ?? ''); ?>
                </div>
            </div>
            
            <div>
                <div class="font-medium mb-1">RX</div>
                <div class="border border-gray-400 h-20 p-2">
                    <?php echo htmlspecialchars($latest_consultation['prescription'] ?? ''); ?>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
            <div class="flex items-center">
                <span class="font-medium mr-2">DATE:</span>
                <span class="border-b border-gray-400 flex-1 px-2">
                    <?php echo $latest_consultation ? date('d/m/Y', strtotime($latest_consultation['appointment_date'])) : ''; ?>
                </span>
            </div>
            <div class="flex items-center">
                <span class="font-medium mr-2">Dr Signature:</span>
                <span class="border-b border-gray-400 flex-1 px-2"></span>
            </div>
        </div>
    </div>

    <!-- Laboratory Results Grid -->
    <div class="mb-6 text-xs">
        <div class="grid grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <!-- Parasitology -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Parasitology</h4>
                    <div class="space-y-1">
                        <div>• mRDT…………………………………………………</div>
                        <div>• Blood Slide Smear……………………………….</div>
                        <div>……………………………………………………………………….</div>
                        <div>• Urine sedimentary</div>
                        <div>Urine appearance……………………………………………</div>
                        <div>Urine microscopic report………………………………</div>
                        <div>……………………………………………………………………….</div>
                        <div>• Urine Chemistry</div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>Leucocytes…………………….</div>
                            <div>PH………………………….</div>
                            <div>Protein………………………….</div>
                            <div>Blood……………………...</div>
                        </div>
                        <div>• Stool analysis</div>
                        <div>Stool appearance……………………………………………</div>
                        <div>Stool microscopic report…………………………………</div>
                        <div>………………………………………………………………………</div>
                    </div>
                </div>

                <!-- Hematology -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Hematology</h4>
                    <div class="space-y-1">
                        <div>• Hemoglobin……………………………….g/dL</div>
                        <div>• ESR…………………………………………………….</div>
                        <div>• Full blood picture……………………………….</div>
                        <div>Others………………………………………………………</div>
                    </div>
                </div>

                <!-- Clinical Chemistry -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Clinical chemistry</h4>
                    <div class="space-y-1">
                        <div>• Blood sugar………………………….…mmol/L</div>
                        <div>• Blood uric acid……………………………………</div>
                        <div>• Rheumatoid factor………………………………</div>
                        <div>• Others……………………………………………….</div>
                        <div>…………………………………………………………..</div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <!-- Serology -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Serology</h4>
                    <div class="space-y-1">
                        <div>• H.Pylori antigen………………………………….</div>
                        <div>• H.Pylori antibody.……………………………….</div>
                        <div>• RPP/Syphilis……………………………………...</div>
                        <div>• UPT……………………………………………………</div>
                        <div>• Salmonella typhi/parathyphiantigen…..</div>
                        <div>…………………………………………………………..</div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>• STO …………………………..</div>
                            <div>STH………………...</div>
                        </div>
                        <div>• Rheumatoid Factor…………………………….</div>
                        <div>Others…………………………………………………….</div>
                        <div>……………………………………………………………….</div>
                    </div>
                </div>

                <!-- Blood Transfusion -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Blood transfusion</h4>
                    <div class="space-y-1">
                        <div class="grid grid-cols-2 gap-2">
                            <div>• Blood group……………</div>
                            <div>Rhesus…………….</div>
                        </div>
                        <div>Others…………………………………………………….</div>
                    </div>
                </div>

                <!-- Test Signature -->
                <div class="border border-gray-400 p-3">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>Test performed by ……………………………………………………</div>
                        <div>Signature ……………………………..……………</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attend Patient Modal -->
<div id="attendModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full hidden" style="z-index: 9999;">
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white p-6 border-b rounded-t-lg">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-gray-900">Patient Consultation Form</h3>
                    <button onclick="closeAttendModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <form id="attendForm" method="POST" action="/KJ/doctor/start_consultation" onsubmit="return validateConsultationForm()" class="p-6 space-y-6">
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

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        font-size: 12px;
        line-height: 1.3;
    }
    
    #medicalRecord {
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        max-width: none !important;
    }
    
    .grid {
        display: grid !important;
    }
    
    .border {
        border: 1px solid #000 !important;
    }
    
    .border-b {
        border-bottom: 1px solid #000 !important;
    }
    
    .text-center {
        text-align: center !important;
    }
    
    .font-bold {
        font-weight: bold !important;
    }
    
    .font-medium {
        font-weight: 500 !important;
    }
    
    .underline {
        text-decoration: underline !important;
    }
    
    .p-2 {
        padding: 4px !important;
    }
    
    .p-3 {
        padding: 6px !important;
    }
    
    .mb-1 {
        margin-bottom: 2px !important;
    }
    
    .mb-2 {
        margin-bottom: 4px !important;
    }
    
    .mb-4 {
        margin-bottom: 8px !important;
    }
    
    .mb-6 {
        margin-bottom: 12px !important;
    }
    
    .space-y-1 > * + * {
        margin-top: 2px !important;
    }
    
    .space-y-4 > * + * {
        margin-top: 8px !important;
    }
}
