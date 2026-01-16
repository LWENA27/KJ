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

        <!-- Reopening Notification -->
        <?php if ($is_reopening ?? false): ?>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-900">Consultation in Progress</h3>
                    <p class="text-sm text-blue-800 mt-1">This is a previous consultation session. The form below contains your earlier notes. You can continue where you left off or make updates.</p>
                    <?php if ($consultation && $consultation['started_at']): ?>
                        <p class="text-xs text-blue-700 mt-1">
                            <i class="fas fa-clock mr-1"></i>Started: <?php echo date('d/m/Y H:i', strtotime($consultation['started_at'])); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

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

        <!-- Previous Chief Complaints History -->
        <?php if (!empty($previous_complaints)): ?>
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
            <div class="flex items-start mb-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-history text-amber-600 text-lg"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-amber-900">Previous Chief Complaints</h3>
                    <p class="text-xs text-amber-800 mt-1">Patient's recent medical history for reference</p>
                </div>
                <button type="button" onclick="toggleComplaintsHistory()" class="text-amber-700 hover:text-amber-900">
                    <i class="fas fa-chevron-down" id="complaintsToggleIcon"></i>
                </button>
            </div>
            <div id="complaintsHistory" class="space-y-2">
                <?php foreach ($previous_complaints as $index => $complaint): ?>
                <div class="bg-white border border-amber-200 rounded p-3 <?php echo $index >= 2 ? 'hidden complaints-extra' : ''; ?>">
                    <div class="flex justify-between items-start mb-1">
                        <div class="text-xs text-gray-500">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            <?php 
                            $timestamp = strtotime($complaint['created_at']);
                            echo $timestamp ? date('d/m/Y', $timestamp) : 'Unknown date';
                            ?>
                            <?php if (!empty($complaint['doctor_name'])): ?>
                            | <i class="fas fa-user-md mr-1"></i><?php echo htmlspecialchars($complaint['doctor_name']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="text-sm">
                        <strong class="text-gray-700">Chief Complaint:</strong>
                        <span class="text-gray-900"><?php echo htmlspecialchars($complaint['main_complaint']); ?></span>
                    </div>
                    <?php 
                    $diagnosis = '';
                    if (!empty(trim($complaint['final_diagnosis'] ?? ''))) {
                        $diagnosis = $complaint['final_diagnosis'];
                    } elseif (!empty(trim($complaint['preliminary_diagnosis'] ?? ''))) {
                        $diagnosis = $complaint['preliminary_diagnosis'];
                    }
                    if ($diagnosis):
                    ?>
                    <div class="text-xs text-gray-600 mt-1">
                        <i class="fas fa-stethoscope mr-1"></i>
                        <strong>Diagnosis:</strong> 
                        <?php echo htmlspecialchars($diagnosis); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <?php if (count($previous_complaints) > 2): ?>
                <button type="button" onclick="showAllComplaints()" id="showMoreBtn" class="text-xs text-amber-700 hover:text-amber-900 mt-2">
                    <i class="fas fa-chevron-down mr-1"></i>Show <?php echo count($previous_complaints) - 2; ?> more
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Main Consultation Form -->
        <div class="bg-white rounded-lg shadow">
        <!-- AFTER (fixed) -->
<form id="attendForm" method="POST" action="/KJ/doctor/start_consultation" 
    onsubmit="return handleFormSubmit(event);">        
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">
                <input type="hidden" id="selectedTests" name="selected_tests" value="">
                <input type="hidden" id="selectedMedicines" name="selected_medicines" value="">
                <input type="hidden" id="selectedAllocations" name="selected_allocations" value="">
                <input type="hidden" id="preliminaryDiagnosisId" name="preliminary_diagnosis_id" value="">
                <input type="hidden" id="finalDiagnosisId" name="final_diagnosis_id" value="">

                <!-- Examination Section -->
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="text-lg font-medium text-blue-900 mb-4">Clinical Examination</h4>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- M/C (Main Complaint) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">M/C - Main Complaint *</label>
                            <textarea id="mainComplaint" name="main_complaint" rows="3" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Patient's main complaint and symptoms..."><?php echo htmlspecialchars($consultation['main_complaint'] ?? ''); ?></textarea>
                        </div>

                        <!-- O/E (On Examination) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">O/E - On Examination *</label>
                            <textarea id="onExamination" name="on_examination" rows="3" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Physical examination findings..."><?php echo htmlspecialchars($consultation['on_examination'] ?? ''); ?></textarea>
                        </div>

                        <!-- Preliminary Diagnosis -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preliminary Diagnosis (Search ICD Code or enter manually)</label>
                            <div class="relative mb-2">
                                <div class="flex">
                                    <input type="text" id="preliminaryDiagnosisSearch" placeholder="Type to search diagnosis codes (e.g., Malaria, B50)..."
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500" autocomplete="off">
                                    <button type="button" onclick="clearPreliminaryDiagnosisSearch()" id="clearPreliminaryDiagnosisSearch"
                                        class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200 hidden">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="preliminaryDiagnosisResults" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 hidden max-h-60 overflow-y-auto shadow-lg"></div>
                            </div>
                            <div id="selectedPreliminaryDiagnosis" class="mb-2">
                                <?php if (!empty($consultation['preliminary_diagnosis'])): ?>
                                <div class="p-2 bg-blue-50 border border-blue-200 rounded-md text-sm">
                                    <strong>ICD Selected:</strong> <?php echo htmlspecialchars($consultation['preliminary_diagnosis']); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="text-xs text-gray-600 mb-1">Or enter diagnosis manually:</div>
                            <textarea id="preliminaryDiagnosis" name="preliminary_diagnosis" rows="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                placeholder="Type diagnosis if not found in ICD codes..."><?php echo htmlspecialchars($consultation['preliminary_diagnosis'] ?? ''); ?></textarea>
                        </div>

                        <!-- Final Diagnosis -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Final Diagnosis (Search ICD Code or enter manually)</label>
                            <div class="relative mb-2">
                                <div class="flex">
                                    <input type="text" id="finalDiagnosisSearch" placeholder="Type to search diagnosis codes (e.g., Pneumonia, J18)..."
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500" autocomplete="off">
                                    <button type="button" onclick="clearFinalDiagnosisSearch()" id="clearFinalDiagnosisSearch"
                                        class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200 hidden">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="finalDiagnosisResults" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 hidden max-h-60 overflow-y-auto shadow-lg"></div>
                            </div>
                            <div id="selectedFinalDiagnosis" class="mb-2">
                                <?php if (!empty($consultation['diagnosis']) || !empty($consultation['final_diagnosis'])): ?>
                                <div class="p-2 bg-blue-50 border border-blue-200 rounded-md text-sm">
                                    <strong>ICD Selected:</strong> <?php echo htmlspecialchars($consultation['final_diagnosis'] ?? $consultation['diagnosis'] ?? ''); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="text-xs text-gray-600 mb-1">Or enter diagnosis manually:</div>
                            <textarea id="finalDiagnosis" name="final_diagnosis" rows="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                placeholder="Type diagnosis if not found in ICD codes..."><?php echo htmlspecialchars($consultation['final_diagnosis'] ?? $consultation['diagnosis'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Decision Section -->
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h4 class="text-lg font-medium text-yellow-900 mb-4">Next Steps Decision</h4>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <label class="flex items-center">
                                <input type="radio" name="next_step" value="lab_tests" class="mr-2" onchange="toggleSection('lab_tests')">
                                <span class="text-sm font-medium">Lab Tests</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="next_step" value="medicine" class="mr-2" onchange="toggleSection('medicine')">
                                <span class="text-sm font-medium">Medicine</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="next_step" value="allocation" class="mr-2" onchange="toggleSection('allocation')">
                                <span class="text-sm font-medium">Allocate Services</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="next_step" value="lab_medicine" class="mr-2" onchange="toggleSection('lab_medicine')">
                                <span class="text-sm font-medium">Lab & Medicine</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="next_step" value="all" class="mr-2" onchange="toggleSection('all')">
                                <span class="text-sm font-medium">All (Lab, Med & Services)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="next_step" value="discharge" class="mr-2" onchange="toggleSection('none')">
                                <span class="text-sm font-medium">Discharge</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Lab Tests Section -->
                <div id="labSection" class="bg-purple-50 p-4 rounded-lg hidden">
                    <h4 class="text-lg font-medium text-purple-900 mb-4">
                        <i class="fas fa-flask mr-2"></i>Laboratory Tests
                    </h4>
                    <div class="bg-purple-100 border border-purple-300 rounded p-3 mb-4">
                        <p class="text-sm text-purple-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Required:</strong> Search and select at least one lab test below before completing consultation.
                        </p>
                    </div>
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
                    <h4 class="text-lg font-medium text-green-900 mb-4">
                        <i class="fas fa-pills mr-2"></i>Medicine Prescription
                    </h4>
                    <div class="bg-green-100 border border-green-300 rounded p-3 mb-4">
                        <p class="text-sm text-green-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Required:</strong> Search and select at least one medicine below before completing consultation.
                        </p>
                    </div>
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

                <!-- Allocation Section -->
                <div id="allocationSection" class="bg-indigo-50 p-4 rounded-lg hidden">
                    <h4 class="text-lg font-medium text-indigo-900 mb-4">
                        <i class="fas fa-user-md mr-2"></i>Allocate Service(s)
                    </h4>
                    <div class="bg-indigo-100 border border-indigo-300 rounded p-3 mb-4">
                        <p class="text-sm text-indigo-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Required:</strong> Search and select at least one service below before completing consultation.
                        </p>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search & Select Services</label>
                            <div class="relative">
                                <div class="flex">
                                    <input type="text" id="serviceSearchAlloc" placeholder="Type to search services..."
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500" autocomplete="off">
                                    <button type="button" onclick="clearServiceSearch()" id="clearServiceSearch"
                                        class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200 hidden">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="serviceResultsAlloc" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 hidden max-h-60 overflow-y-auto shadow-lg"></div>
                            </div>
                        </div>
                        <div id="selectedAllocationsList" class="space-y-2">
                            <!-- Selected allocations will appear here -->
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
                            placeholder="Treatment plan, follow-up instructions, lifestyle advice..."><?php echo htmlspecialchars($consultation['treatment_plan'] ?? ''); ?></textarea>
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
    // Use layout-provided BASE_PATH when available to keep fetch URLs correct
    // Default to empty string (root) instead of hard-coded '/KJ' so routes work
    // whether the app is served from the root or a subfolder.
    const BASE_PATH = (typeof window !== 'undefined' && window.BASE_PATH) ? window.BASE_PATH : '';

        let selectedTests = [];
        let selectedMedicines = [];

        // Form validation before submission
        function validateConsultationForm() {
    console.log('=== FORM VALIDATION STARTED ===');
    
    const nextStep = document.querySelector('input[name="next_step"]:checked');
    console.log('Next step value:', nextStep ? nextStep.value : 'NONE');
    
    if (!nextStep) {
        alert('Please select a next step decision (Lab Tests, Medicine, Allocate Services, or Discharge)');
        return false;
    }

    const nextStepValue = nextStep.value;
    
    // Check lab tests requirement
    if ((nextStepValue === 'lab_tests' || nextStepValue === 'lab_medicine' || nextStepValue === 'all') 
        && selectedTests.length === 0) {
        alert('Please select at least one lab test');
        return false;
    }

    // Check medicine requirement
    if ((nextStepValue === 'medicine' || nextStepValue === 'lab_medicine' || nextStepValue === 'all') 
        && selectedMedicines.length === 0) {
        alert('Please select at least one medicine');
        return false;
    }

    // Check allocations requirement
    if ((nextStepValue === 'allocation' || nextStepValue === 'all') 
        && selectedAllocations.length === 0) {
        alert('Please select at least one service to allocate');
        return false;
    }
    
    // Validate medicine details
    for (const medicine of selectedMedicines) {
        if (!medicine.dosage || !medicine.dosage.toString().trim()) {
            alert(`Please specify dosage/instructions for ${medicine.name}`);
            return false;
        }
        const qty = Number(medicine.quantity);
        if (isNaN(qty) || qty < 1) {
            alert(`Please enter a valid quantity for ${medicine.name}`);
            return false;
        }
    }

    console.log('✅ Validation passed!');
    return true;
}

    // Ensure selectedMedicines contains the latest values from the DOM inputs
        function syncSelectedMedicinesFromDOM() {
            try {
                const listDiv = document.getElementById('selectedMedicinesList');
                if (!listDiv) return;

                // For each medicine in selectedMedicines, try to find its DOM inputs and copy values back
                selectedMedicines = selectedMedicines.map(m => {
                    const div = Array.from(listDiv.querySelectorAll('div')).find(d => {
                        const title = d.querySelector('.font-medium');
                        return title && title.textContent.trim() === m.name;
                    });
                    if (!div) return m;

                    const qtyInput = div.querySelector('input[type="number"]');
                    const textInput = div.querySelector('input[type="text"]');

                    return {
                        ...m,
                        quantity: qtyInput ? Number(qtyInput.value) : m.quantity,
                        // We store the combined Dosage/Instructions in the `dosage` property
                        dosage: textInput ? textInput.value : m.dosage,
                        // Keep instructions empty (server will accept it) or set to previous value
                        instructions: ''
                    };
                });

                // Build a controller-compatible payload: id, quantity, dosage, frequency, duration, instructions
                const payload = selectedMedicines.map(m => ({
                    id: m.id,
                    quantity: m.quantity,
                    dosage: m.dosage || '',
                    frequency: m.frequency || 'Once daily',
                    duration: m.duration || 1,
                    instructions: m.instructions || ''
                }));

                document.getElementById('selectedMedicines').value = JSON.stringify(payload);
            } catch (e) {
                console.warn('syncSelectedMedicinesFromDOM failed', e);
            }
        }

        // --- Allocation support ---
        let selectedAllocations = [];
        let serviceSearchTimeout;

        const serviceSearchElement2 = document.getElementById('serviceSearchAlloc');
        if (serviceSearchElement2) {
            serviceSearchElement2.addEventListener('input', function() {
                clearTimeout(serviceSearchTimeout);
                const q = this.value.trim();
                const clearBtn = document.getElementById('clearServiceSearch');
                if (q.length > 0) clearBtn.classList.remove('hidden'); else { clearBtn.classList.add('hidden'); document.getElementById('serviceResultsAlloc').classList.add('hidden'); }
                if (q.length < 2) return;

                serviceSearchTimeout = setTimeout(() => {
                    fetch(`${BASE_PATH}/doctor/search_services?q=${encodeURIComponent(q)}`)
                        .then(r => r.json())
                        .then(services => displayServiceResults(services))
                        .catch(e => console.error('service search error', e));
                }, 250);
            });
        }

        function displayServiceResults(services) {
            const resultsDiv = document.getElementById('serviceResultsAlloc');
            resultsDiv.innerHTML = '';
            if (!Array.isArray(services) || services.length === 0) {
                resultsDiv.innerHTML = '<div class="p-3 text-gray-500">No services found</div>';
            } else {
                services.forEach(s => {
                    const isSelected = selectedAllocations.some(a => a.service_id === s.id);
                    const div = document.createElement('div');
                    div.className = `p-3 hover:bg-gray-100 cursor-pointer border-b ${isSelected? 'bg-indigo-50':''}`;
                    div.innerHTML = `<div class="font-medium">${s.name}</div><div class="text-sm text-gray-600">${s.description || ''} - Tsh ${parseFloat(s.price||0).toLocaleString()}</div>`;
                    if (!isSelected) div.addEventListener('click', () => addAllocation(s));
                    resultsDiv.appendChild(div);
                });
            }
            resultsDiv.classList.remove('hidden');
        }

        function addAllocation(service) {
            if (selectedAllocations.some(a => a.service_id === service.id)) return;
            selectedAllocations.push({ service_id: service.id, service_name: service.name, assigned_to: null, instructions: '' });
            updateSelectedAllocationsList();
            document.getElementById('serviceResultsAlloc').classList.add('hidden');
            document.getElementById('serviceSearchAlloc').value = '';
            document.getElementById('clearServiceSearch').classList.add('hidden');
        }

        function removeAllocation(serviceId) {
            selectedAllocations = selectedAllocations.filter(a => a.service_id !== serviceId);
            updateSelectedAllocationsList();
        }

        function updateAllocationField(serviceId, field, value) {
            const a = selectedAllocations.find(x => x.service_id === serviceId);
            if (!a) return;
            // Only allow 'assigned_to' and 'instructions' as keys for backend compatibility
            if (field === 'assigned_to' || field === 'instructions') {
                a[field] = value;
            }
            document.getElementById('selectedAllocations').value = JSON.stringify(selectedAllocations);
        }

        function updateSelectedAllocationsList() {
            const listDiv = document.getElementById('selectedAllocationsList');
            if (!listDiv) return;
            listDiv.innerHTML = '';
            if (selectedAllocations.length === 0) {
                listDiv.innerHTML = '<div class="text-gray-500 text-sm">No services selected</div>';
            } else {
                selectedAllocations.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'p-3 bg-white border rounded-md';
                    div.innerHTML = `
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <div class="font-medium">${item.service_name}</div>
                                <div class="text-xs text-gray-500">Service ID: ${item.service_id}</div>
                            </div>
                            <button type="button" onclick="removeAllocation(${item.service_id})" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs text-gray-600">Assign to (staff id)</label>
                                <input type="text" placeholder="Search staff by name or id" value="" oninput="fetchStaffSuggestions(this, ${item.service_id})" class="w-full px-2 py-1 border border-gray-300 rounded text-sm staff-search-input">
                                <div class="staff-suggestions mt-1 text-xs text-gray-600"></div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600">Instructions</label>
                                <input type="text" placeholder="e.g., start immediately" value="${item.instructions||''}" onchange="updateAllocationField(${item.service_id}, 'instructions', this.value)" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                            </div>
                        </div>
                    `;
                    listDiv.appendChild(div);
                });
            }
            document.getElementById('selectedAllocations').value = JSON.stringify(selectedAllocations);
        }

        function fetchStaffSuggestions(inputEl, serviceId) {
            const q = inputEl.value.trim();
            const suggestionsDiv = inputEl.closest('div').querySelector('.staff-suggestions');
            if (q.length < 2) { suggestionsDiv.innerHTML = ''; return; }
            fetch(`${BASE_PATH}/doctor/search_staff?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(list => {
                    suggestionsDiv.innerHTML = '';
                    list.slice(0,8).forEach(s => {
                        const node = document.createElement('div');
                        node.className = 'p-1 hover:bg-gray-100 cursor-pointer';
                        node.textContent = s.first_name + ' ' + s.last_name + ' (id:'+s.id+')';
                        node.addEventListener('click', () => {
                            updateAllocationField(serviceId, 'assigned_to', s.id);
                            inputEl.value = s.first_name + ' ' + s.last_name + ' (id:'+s.id+')';
                            suggestionsDiv.innerHTML = '';
                        });
                        suggestionsDiv.appendChild(node);
                    });
                }).catch(e => { console.error('staff search error', e); suggestionsDiv.innerHTML = ''; });
        }

        function clearServiceSearch() {
            document.getElementById('serviceSearchAlloc').value = '';
            document.getElementById('clearServiceSearch').classList.add('hidden');
            document.getElementById('serviceResultsAlloc').classList.add('hidden');
        }

        function syncSelectedAllocationsFromDOM() {
            try { document.getElementById('selectedAllocations').value = JSON.stringify(selectedAllocations); } catch (e) { console.warn(e); }
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
            // Go back to patient view page
            const patientId = document.querySelector('input[name="patient_id"]').value;
            if (patientId) {
                window.location.href = `${BASE_PATH}/doctor/view_patient/${patientId}`;
            } else {
                window.history.back();
            }
        }

        function toggleSection(section) {
            const labSection = document.getElementById('labSection');
            const medicineSection = document.getElementById('medicineSection');
            const allocationSection = document.getElementById('allocationSection');

            // Hide all sections first
            if (labSection) labSection.classList.add('hidden');
            if (medicineSection) medicineSection.classList.add('hidden');
            if (allocationSection) allocationSection.classList.add('hidden');

            // Show relevant sections based on selection
            if (section === 'lab_tests' || section === 'lab_medicine' || section === 'all') {
                if (labSection) labSection.classList.remove('hidden');
            }
            if (section === 'medicine' || section === 'lab_medicine' || section === 'all') {
                if (medicineSection) medicineSection.classList.remove('hidden');
            }
            if (section === 'allocation' || section === 'all') {
                if (allocationSection) allocationSection.classList.remove('hidden');
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
                fetch(`${BASE_PATH}/doctor/search_tests?q=${encodeURIComponent(query)}`)
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
                fetch(`${BASE_PATH}/doctor/search_medicines?q=${encodeURIComponent(query)}`)
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


        function handleFormSubmit(event) {
    console.log('=== FORM SUBMIT HANDLER STARTED ===');
    
    // Prevent default submission first
    event.preventDefault();
    
    // Step 1: Sync all hidden fields
    syncSelectedTestsFromDOM();
    syncSelectedMedicinesFromDOM();
    syncSelectedAllocationsFromDOM();
    
    // Step 2: Log what we're about to send
    console.log('Selected Tests:', document.getElementById('selectedTests').value);
    console.log('Selected Medicines:', document.getElementById('selectedMedicines').value);
    console.log('Selected Allocations:', document.getElementById('selectedAllocations').value);
    console.log('Next Step:', document.querySelector('input[name="next_step"]:checked')?.value);
    
    // Step 3: Validate
    if (!validateConsultationForm()) {
        console.log('❌ Validation failed');
        return false;
    }
    
    console.log('✅ Validation passed, submitting form...');
    
    // Step 4: Submit the form programmatically
    document.getElementById('attendForm').submit();
    
    return false; // Prevent default just in case
}


        function syncSelectedTestsFromDOM() {
    try {
        // Tests are simpler - just update the hidden field
        const testIds = selectedTests.map(test => test.id);
        document.getElementById('selectedTests').value = JSON.stringify(testIds);
        console.log('Synced tests:', JSON.stringify(testIds));
    } catch (e) {
        console.warn('syncSelectedTestsFromDOM failed', e);
    }
}
        
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
                    const stockBadge = medicine.stock_quantity === 0 ? '<span class="ml-2 text-xs bg-red-100 text-red-700 px-2 py-1 rounded">OUT OF STOCK</span>' : '';
                    div.innerHTML = `
                    <div class="font-medium">${medicine.name}</div>
                    <div class="text-sm text-gray-600">${medicine.generic_name} - Tsh ${parseFloat(medicine.unit_price).toLocaleString('en-US')}</div>
                    <div class="text-xs text-gray-500">Stock: ${medicine.stock_quantity} ${stockBadge}</div>
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
                // Parse integer - allow any positive quantity
                let quantity = parseInt(value, 10);
                const stock = Number(medicine.stock_quantity);
                if (isNaN(quantity) || quantity < 1) {
                    quantity = 1;
                    if (typeof elementOrValue === 'object') elementOrValue.value = quantity;
                }
                // Allow prescription even if stock is 0 - patient can get medicine elsewhere
                // Just warn if prescribing more than in stock
                if (quantity > stock && stock > 0) {
                    console.warn(`Prescribing ${quantity} but only ${stock} in stock`);
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-gray-600">Quantity</label>
                <input type="number" min="1" value="${medicine.quantity}" 
                    onchange="updateMedicineDetails(${medicine.id}, 'quantity', this)"
                                   class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">Dosage / Instructions</label>
                <input type="text" placeholder="e.g., 500mg, 1 tab twice daily after meals" value="${medicine.dosage}"
                    onchange="updateMedicineDetails(${medicine.id}, 'dosage', this.value)"
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

        // Diagnosis Search Functions
        let preliminaryDiagnosisSearchTimeout;
        let finalDiagnosisSearchTimeout;
        let selectedPreliminaryDiagnosis = null;
        let selectedFinalDiagnosis = null;

        // Preliminary Diagnosis Search
        const preliminaryDiagnosisSearchElement = document.getElementById('preliminaryDiagnosisSearch');
        if (preliminaryDiagnosisSearchElement) {
            preliminaryDiagnosisSearchElement.addEventListener('input', function() {
                clearTimeout(preliminaryDiagnosisSearchTimeout);
                const query = this.value.trim();

                const clearBtn = document.getElementById('clearPreliminaryDiagnosisSearch');
                if (query.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                    document.getElementById('preliminaryDiagnosisResults').classList.add('hidden');
                    return;
                }

                if (query.length < 2) {
                    document.getElementById('preliminaryDiagnosisResults').classList.add('hidden');
                    return;
                }

                preliminaryDiagnosisSearchTimeout = setTimeout(() => {
                    fetch(`${BASE_PATH}/doctor/search_diagnoses?q=${encodeURIComponent(query)}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(diagnoses => displayPreliminaryDiagnosisResults(diagnoses))
                        .catch(error => {
                            console.error('Error searching diagnoses:', error);
                            const resultsDiv = document.getElementById('preliminaryDiagnosisResults');
                            resultsDiv.innerHTML = '<div class="p-3 text-red-500"><i class="fas fa-exclamation-triangle mr-2"></i>Error loading diagnoses</div>';
                            resultsDiv.classList.remove('hidden');
                        });
                }, 300);
            });
        }

        function displayPreliminaryDiagnosisResults(diagnoses) {
            const resultsDiv = document.getElementById('preliminaryDiagnosisResults');
            resultsDiv.innerHTML = '';

            if (diagnoses.length === 0) {
                resultsDiv.innerHTML = '<div class="p-3 text-gray-500">No diagnosis codes found</div>';
            } else {
                diagnoses.forEach(diagnosis => {
                    const div = document.createElement('div');
                    div.className = 'p-3 hover:bg-blue-100 cursor-pointer border-b';
                    
                    // Create elements safely to prevent XSS
                    const titleDiv = document.createElement('div');
                    titleDiv.className = 'font-medium';
                    titleDiv.textContent = `${diagnosis.code} - ${diagnosis.name}`;
                    
                    const categoryDiv = document.createElement('div');
                    categoryDiv.className = 'text-sm text-gray-600';
                    categoryDiv.textContent = diagnosis.category || '';
                    
                    const descDiv = document.createElement('div');
                    descDiv.className = 'text-xs text-gray-500';
                    descDiv.textContent = diagnosis.description || '';
                    
                    div.appendChild(titleDiv);
                    div.appendChild(categoryDiv);
                    div.appendChild(descDiv);
                    div.addEventListener('click', () => selectPreliminaryDiagnosis(diagnosis));
                    resultsDiv.appendChild(div);
                });
            }

            resultsDiv.classList.remove('hidden');
        }

        function selectPreliminaryDiagnosis(diagnosis) {
            selectedPreliminaryDiagnosis = diagnosis;
            document.getElementById('preliminaryDiagnosisId').value = diagnosis.id;
            
            // Also populate the manual textarea with the diagnosis name
            const diagnosisText = diagnosis.code + ' - ' + diagnosis.name;
            document.getElementById('preliminaryDiagnosis').value = diagnosisText;
            
            const displayDiv = document.getElementById('selectedPreliminaryDiagnosis');
            displayDiv.innerHTML = ''; // Clear first
            
            const container = document.createElement('div');
            container.className = 'p-2 bg-blue-50 border border-blue-200 rounded-md text-sm flex justify-between items-start';
            
            const infoDiv = document.createElement('div');
            const codeText = document.createElement('strong');
            codeText.textContent = diagnosis.code;
            const nameText = document.createTextNode(' - ' + diagnosis.name);
            infoDiv.appendChild(codeText);
            infoDiv.appendChild(nameText);
            
            const categoryDiv = document.createElement('div');
            categoryDiv.className = 'text-xs text-gray-600';
            categoryDiv.textContent = diagnosis.category || '';
            infoDiv.appendChild(categoryDiv);
            
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'text-red-600 hover:text-red-800';
            closeBtn.onclick = clearSelectedPreliminaryDiagnosis;
            closeBtn.innerHTML = '<i class="fas fa-times"></i>';
            
            container.appendChild(infoDiv);
            container.appendChild(closeBtn);
            displayDiv.appendChild(container);
            
            document.getElementById('preliminaryDiagnosisResults').classList.add('hidden');
            document.getElementById('preliminaryDiagnosisSearch').value = '';
            document.getElementById('clearPreliminaryDiagnosisSearch').classList.add('hidden');
        }

        function clearSelectedPreliminaryDiagnosis() {
            selectedPreliminaryDiagnosis = null;
            document.getElementById('preliminaryDiagnosisId').value = '';
            // Clear the manual textarea too
            document.getElementById('preliminaryDiagnosis').value = '';
            document.getElementById('selectedPreliminaryDiagnosis').innerHTML = '';
        }

        function clearPreliminaryDiagnosisSearch() {
            document.getElementById('preliminaryDiagnosisSearch').value = '';
            document.getElementById('clearPreliminaryDiagnosisSearch').classList.add('hidden');
            document.getElementById('preliminaryDiagnosisResults').classList.add('hidden');
        }

        // Final Diagnosis Search
        const finalDiagnosisSearchElement = document.getElementById('finalDiagnosisSearch');
        if (finalDiagnosisSearchElement) {
            finalDiagnosisSearchElement.addEventListener('input', function() {
                clearTimeout(finalDiagnosisSearchTimeout);
                const query = this.value.trim();

                const clearBtn = document.getElementById('clearFinalDiagnosisSearch');
                if (query.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                    document.getElementById('finalDiagnosisResults').classList.add('hidden');
                    return;
                }

                if (query.length < 2) {
                    document.getElementById('finalDiagnosisResults').classList.add('hidden');
                    return;
                }

                finalDiagnosisSearchTimeout = setTimeout(() => {
                    fetch(`${BASE_PATH}/doctor/search_diagnoses?q=${encodeURIComponent(query)}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(diagnoses => displayFinalDiagnosisResults(diagnoses))
                        .catch(error => {
                            console.error('Error searching diagnoses:', error);
                            const resultsDiv = document.getElementById('finalDiagnosisResults');
                            resultsDiv.innerHTML = '<div class="p-3 text-red-500"><i class="fas fa-exclamation-triangle mr-2"></i>Error loading diagnoses</div>';
                            resultsDiv.classList.remove('hidden');
                        });
                }, 300);
            });
        }

        function displayFinalDiagnosisResults(diagnoses) {
            const resultsDiv = document.getElementById('finalDiagnosisResults');
            resultsDiv.innerHTML = '';

            if (diagnoses.length === 0) {
                resultsDiv.innerHTML = '<div class="p-3 text-gray-500">No diagnosis codes found</div>';
            } else {
                diagnoses.forEach(diagnosis => {
                    const div = document.createElement('div');
                    div.className = 'p-3 hover:bg-blue-100 cursor-pointer border-b';
                    
                    // Create elements safely to prevent XSS
                    const titleDiv = document.createElement('div');
                    titleDiv.className = 'font-medium';
                    titleDiv.textContent = `${diagnosis.code} - ${diagnosis.name}`;
                    
                    const categoryDiv = document.createElement('div');
                    categoryDiv.className = 'text-sm text-gray-600';
                    categoryDiv.textContent = diagnosis.category || '';
                    
                    const descDiv = document.createElement('div');
                    descDiv.className = 'text-xs text-gray-500';
                    descDiv.textContent = diagnosis.description || '';
                    
                    div.appendChild(titleDiv);
                    div.appendChild(categoryDiv);
                    div.appendChild(descDiv);
                    div.addEventListener('click', () => selectFinalDiagnosis(diagnosis));
                    resultsDiv.appendChild(div);
                });
            }

            resultsDiv.classList.remove('hidden');
        }

        function selectFinalDiagnosis(diagnosis) {
            selectedFinalDiagnosis = diagnosis;
            document.getElementById('finalDiagnosisId').value = diagnosis.id;
            
            // Also populate the manual textarea with the diagnosis name
            const diagnosisText = diagnosis.code + ' - ' + diagnosis.name;
            document.getElementById('finalDiagnosis').value = diagnosisText;
            
            const displayDiv = document.getElementById('selectedFinalDiagnosis');
            displayDiv.innerHTML = ''; // Clear first
            
            const container = document.createElement('div');
            container.className = 'p-2 bg-blue-50 border border-blue-200 rounded-md text-sm flex justify-between items-start';
            
            const infoDiv = document.createElement('div');
            const codeText = document.createElement('strong');
            codeText.textContent = diagnosis.code;
            const nameText = document.createTextNode(' - ' + diagnosis.name);
            infoDiv.appendChild(codeText);
            infoDiv.appendChild(nameText);
            
            const categoryDiv = document.createElement('div');
            categoryDiv.className = 'text-xs text-gray-600';
            categoryDiv.textContent = diagnosis.category || '';
            infoDiv.appendChild(categoryDiv);
            
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'text-red-600 hover:text-red-800';
            closeBtn.onclick = clearSelectedFinalDiagnosis;
            closeBtn.innerHTML = '<i class="fas fa-times"></i>';
            
            container.appendChild(infoDiv);
            container.appendChild(closeBtn);
            displayDiv.appendChild(container);
            
            document.getElementById('finalDiagnosisResults').classList.add('hidden');
            document.getElementById('finalDiagnosisSearch').value = '';
            document.getElementById('clearFinalDiagnosisSearch').classList.add('hidden');
        }

        function clearSelectedFinalDiagnosis() {
            selectedFinalDiagnosis = null;
            document.getElementById('finalDiagnosisId').value = '';
            // Clear the manual textarea too
            document.getElementById('finalDiagnosis').value = '';
            document.getElementById('selectedFinalDiagnosis').innerHTML = '';
        }

        function clearFinalDiagnosisSearch() {
            document.getElementById('finalDiagnosisSearch').value = '';
            document.getElementById('clearFinalDiagnosisSearch').classList.add('hidden');
            document.getElementById('finalDiagnosisResults').classList.add('hidden');
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
            if (!e.target.closest('#preliminaryDiagnosisSearch') && !e.target.closest('#preliminaryDiagnosisResults') && !e.target.closest('#clearPreliminaryDiagnosisSearch')) {
                const resultsDiv = document.getElementById('preliminaryDiagnosisResults');
                if (resultsDiv) resultsDiv.classList.add('hidden');
            }
            if (!e.target.closest('#finalDiagnosisSearch') && !e.target.closest('#finalDiagnosisResults') && !e.target.closest('#clearFinalDiagnosisSearch')) {
                const resultsDiv = document.getElementById('finalDiagnosisResults');
                if (resultsDiv) resultsDiv.classList.add('hidden');
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

        // Toggle complaints history visibility
        function toggleComplaintsHistory() {
            const history = document.getElementById('complaintsHistory');
            const icon = document.getElementById('complaintsToggleIcon');
            if (history.classList.contains('hidden')) {
                history.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                history.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }

        // Show all previous complaints
        function showAllComplaints() {
            const extras = document.querySelectorAll('.complaints-extra');
            const btn = document.getElementById('showMoreBtn');
            extras.forEach(el => el.classList.remove('hidden'));
            if (btn) btn.style.display = 'none';
        }
    </script>

</body>
</html>