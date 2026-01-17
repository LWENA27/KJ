<?php $title = "Register New Patient"; ?>

<div class="max-w-6xl mx-auto">
    <!-- Header with Back Button -->
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Register New Patient</h1>
    <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/patients" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Patients
        </a>
    </div>

    <!-- Registration Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-400 text-green-700">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form id="patientRegistrationForm" method="POST" action="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/register_patient" class="space-y-6">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

            <!-- Patient Information Section -->
            <div class="border-b pb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user mr-3 text-blue-600"></i>Patient Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="last_name" name="last_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                            Date of Birth
                        </label>
                        <input type="date" id="date_of_birth" name="date_of_birth"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                            Gender
                        </label>
                        <select id="gender" name="gender"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number
                        </label>
                        <input type="tel" id="phone" name="phone" placeholder="0712345678"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" placeholder="patient@example.com"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                    
                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                            Address
                        </label>
               <input type="text" id="address" name="address" placeholder="Patient address (optional)"
                   value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>

                    <!-- Occupation -->
                    <div>
                        <label for="occupation" class="block text-sm font-medium text-gray-700 mb-1">
                            Occupation
                        </label>
               <input type="text" id="occupation" name="occupation" placeholder="Occupation (optional)"
                   value="<?php echo htmlspecialchars($_POST['occupation'] ?? ''); ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                </div>
            </div>


                             <!-- Emergency Contact Section -->
            <div class="border-b pb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-phone mr-3 text-red-600"></i>Emergency Contact(Gurdian)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Contact Name
                        </label>
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                    </div>

                    <div>
                        <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Contact Phone
                        </label>
                        <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                    </div>
                </div>
            </div>


            <!-- Visit Type Section -->
            <div class="border-b pb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-calendar-check mr-3 text-indigo-600"></i>Visit Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="visit_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Type of Visit <span class="text-red-500">*</span>
                        </label>
                        <select id="visit_type" name="visit_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Select Visit Type</option>
                            <option value="consultation" <?php echo (isset($_POST['visit_type']) && $_POST['visit_type']==='consultation') ? 'selected' : ''; ?>>Doctor Consultation</option>
                            <option value="lab_test" <?php echo (isset($_POST['visit_type']) && $_POST['visit_type']==='lab_test') ? 'selected' : ''; ?>>Laboratory Test Only</option>
                            <option value="medicine_pickup" <?php echo (isset($_POST['visit_type']) && $_POST['visit_type']==='medicine_pickup') ? 'selected' : ''; ?>>Medicine Collection</option>
                            <option value="minor_service" <?php echo (isset($_POST['visit_type']) && $_POST['visit_type']==='minor_service') ? 'selected' : ''; ?>>Minor Service (Injection, etc.)</option>
                            <option value="ipd" <?php echo (isset($_POST['visit_type']) && $_POST['visit_type']==='ipd') ? 'selected' : ''; ?>>IPD / Ward (Admission / Nursing)</option>
                        </select>
                        <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                    </div>
                </div>

                <!-- Visit Type Info Badge -->
                <div id="visitBadge" class="mt-4 hidden">
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                            <p class="ml-3 text-sm text-blue-700" id="visitBadgeText">
                                Please select a visit type
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment Notice -->
                <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
                        <p class="ml-3 text-sm text-yellow-700">
                            <strong>Note:</strong> After registration, please direct the patient to the <strong>Accountant</strong> to pay the consultation/service fee before seeing the doctor.
                        </p>
                    </div>
                </div>
            </div>

            

            <!-- Vital Signs Section (Hidden by default) -->
            <div id="vitalSignsSection" class="border-b pb-6 hidden">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-heartbeat mr-3 text-red-600"></i>Vital Signs
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Temperature -->
                    <div>
                        <label for="temperature" class="block text-sm font-medium text-gray-700 mb-1">
                            Temperature (°C)
                        </label>
               <input type="number" id="temperature" name="temperature" 
                   step="0.1" min="35" max="42"
                   value="<?php echo htmlspecialchars($_POST['temperature'] ?? ''); ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <!-- Blood Pressure -->
                    <div>
                        <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-1">
                            Blood Pressure (mmHg)
                        </label>
                        <input type="text" id="blood_pressure" name="blood_pressure" 
                               placeholder="120/80"
                               pattern="\d{2,3}\/\d{2,3}"
                               title="Please enter blood pressure in the format '120/80'"
                               maxlength="7"
                               value="<?php echo htmlspecialchars($_POST['blood_pressure'] ?? ''); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <!-- Pulse Rate -->
                    <div>
                        <label for="pulse_rate" class="block text-sm font-medium text-gray-700 mb-1">
                            Pulse Rate (bpm)
                        </label>
               <input type="number" id="pulse_rate" name="pulse_rate" 
                   min="40" max="200"
                   value="<?php echo htmlspecialchars($_POST['pulse_rate'] ?? ''); ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <!-- Weight -->
                    <div>
                        <label for="body_weight" class="block text-sm font-medium text-gray-700 mb-1">
                            Weight (kg)
                        </label>
               <input type="number" id="body_weight" name="body_weight" 
                   step="0.1" min="0" max="300"
                   value="<?php echo htmlspecialchars($_POST['body_weight'] ?? ''); ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <!-- Height -->
                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 mb-1">
                            Height (cm)
                        </label>
               <input type="number" id="height" name="height" 
                   step="0.1" min="0" max="300"
                   value="<?php echo htmlspecialchars($_POST['height'] ?? ''); ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
            </div>

            <!-- Lab Tests Selection Section (Hidden by default) -->
            <div id="labTestsSection" class="border-b pb-6 hidden">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-vial mr-3 text-yellow-600"></i>Lab Tests
                </h3>

                <div class="mb-4 text-sm text-gray-700">
                    Select one or more lab tests for the patient. Prices are shown next to each test.
                </div>

                <div class="mb-4">
                    <label for="lab_search" class="block text-sm font-medium text-gray-700 mb-1">
                        Search Lab Tests
                        <span class="text-xs text-gray-500 font-normal">(Type at least 2 characters to search)</span>
                    </label>
                    <input id="lab_search" type="text" placeholder="Type to search tests (name or code)..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                    <div id="lab_search_results" class="mt-2 bg-white border rounded-md max-h-40 overflow-auto hidden"></div>
                    <div id="lab_selected" class="mt-3 space-y-2">
                        <div id="no_tests_selected" class="text-sm text-gray-500 italic">No tests selected yet. Search and click to add tests.</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="lab_total_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Estimated Lab Amount (TZS)
                            <span class="text-xs text-blue-600 font-normal">• Payment will be collected by Accountant</span>
                        </label>
                        <input type="number" id="lab_total_amount" name="lab_total_amount" readonly value="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-lg font-semibold text-blue-700">
                    </div>

                    <div class="flex items-end">
                        <p class="text-sm text-gray-500 italic">
                            <i class="fas fa-info-circle mr-1"></i>Direct patient to Accountant for payment after registration.
                        </p>
                    </div>
                </div>
            </div>

           
            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6">
                <button type="button" onclick="window.location.href='<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/patients'"
                        class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="submitBtn"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    <span id="submitText">Register Patient</span>
                </button>
            </div>

        </form>
    </div>
</div>

<script>

// Patient Registration JavaScript
(function() {
    'use strict';

    let elements = {
        visitType: null,
        visitBadge: null,
        visitBadgeText: null,
        vitalSignsSection: null
    };

    function initElements() {
        elements.visitType = document.getElementById('visit_type');
        elements.visitBadge = document.getElementById('visitBadge');
        elements.visitBadgeText = document.getElementById('visitBadgeText');
        elements.vitalSignsSection = document.getElementById('vitalSignsSection');
    }

    function toggleVisitSections() {
        const visitType = elements.visitType ? elements.visitType.value : '';

        // Hide all optional sections initially
        if (elements.vitalSignsSection) {
            elements.vitalSignsSection.classList.add('hidden');
        }
        if (elements.visitBadge) {
            elements.visitBadge.classList.add('hidden');
        }

        // Remove required from vital signs by default
        const tempField = document.getElementById('temperature');
        const bpField = document.getElementById('blood_pressure');
        const pulseField = document.getElementById('pulse_rate');
        if (tempField) tempField.removeAttribute('required');
        if (bpField) bpField.removeAttribute('required');
        if (pulseField) pulseField.removeAttribute('required');

        // Show relevant sections based on visit type
        switch(visitType) {
            case 'consultation':
                // Show vital signs for doctor consultation
                if (elements.vitalSignsSection) {
                    elements.vitalSignsSection.classList.remove('hidden');
                }
                if (elements.visitBadge && elements.visitBadgeText) {
                    elements.visitBadge.classList.remove('hidden');
                    elements.visitBadgeText.textContent = 'Patient will need to pay consultation fee at Accountant desk';
                }
                // Make vital signs required for consultations
                if (tempField) tempField.setAttribute('required', 'required');
                if (bpField) bpField.setAttribute('required', 'required');
                if (pulseField) pulseField.setAttribute('required', 'required');
                break;
                
            case 'lab_test':
                // Do NOT show vital signs for lab-only visits
                // Show lab tests selection instead
                const labSection = document.getElementById('labTestsSection');
                if (labSection) labSection.classList.remove('hidden');
                // Do not show the visit badge for lab-only registrations
                break;
                
            case 'medicine_pickup':
                // Hide vital signs for medicine pickup - only show badge
                if (elements.visitBadge && elements.visitBadgeText) {
                    elements.visitBadge.classList.remove('hidden');
                    elements.visitBadgeText.textContent = 'Medicine collection - prescription required';
                }
                break;
                
            case 'minor_service':
                // Only show vital signs for minor services
                if (elements.vitalSignsSection) {
                    elements.vitalSignsSection.classList.remove('hidden');
                }
                if (elements.visitBadge && elements.visitBadgeText) {
                    elements.visitBadge.classList.remove('hidden');
                    elements.visitBadgeText.textContent = 'Minor service fees may apply';
                }
                break;
                case 'ipd':
                // Show vital signs and IPD info for ward admissions
                if (elements.vitalSignsSection) {
                    elements.vitalSignsSection.classList.remove('hidden');
                }
                if (elements.visitBadge && elements.visitBadgeText) {
                    elements.visitBadge.classList.remove('hidden');
                    elements.visitBadgeText.textContent = 'IPD/Ward admission — nursing services (e.g. wound dressing) will be handled by reception/ward';
                }
                // Make vital signs recommended but not strictly required
                break;
                
            default:
                // No visit type selected - hide all optional sections
                if (elements.vitalSignsSection) {
                    elements.vitalSignsSection.classList.add('hidden');
                }
                if (elements.visitBadge) {
                    elements.visitBadge.classList.add('hidden');
                }
                break;
        }
    }

    function init() {
        initElements();
        
        // Set up event listeners
        if (elements.visitType) {
            elements.visitType.addEventListener('change', toggleVisitSections);
        }
        
        // Initial setup
        toggleVisitSections();
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();

// Global function for updating lab totals (needs to be accessible from other scripts)
function updateLabTotal() {
    // Calculate total from selected tests
    const selectedTests = document.querySelectorAll('#lab_selected [data-price]');
    let total = 0;
    
    selectedTests.forEach(testItem => {
        const price = parseFloat(testItem.dataset.price) || 0;
        total += price;
    });
    
    // Update the lab total amount input
    const totalInput = document.getElementById('lab_total_amount');
    if (totalInput) {
        totalInput.value = total;
    }
}

// Global function for removing selected tests (called from onclick handlers)
function removeSelectedTest(testId) {
    const testElement = document.getElementById('selected_test_' + testId);
    if (testElement) {
        testElement.remove();
        
        // Show "no tests selected" message if no tests remain
        const selectedTests = document.querySelectorAll('#lab_selected [data-price]');
        const noTestsMsg = document.getElementById('no_tests_selected');
        if (selectedTests.length === 0 && noTestsMsg) {
            noTestsMsg.style.display = 'block';
        }
        
        updateLabTotal();
    }
}
</script>

<script>
// AJAX search for lab tests and multi-select handling
(function(){
    const searchInput = document.getElementById('lab_search');
    const resultsDiv = document.getElementById('lab_search_results');
    const selectedDiv = document.getElementById('lab_selected');

    if (!searchInput) return;

    let debounce;

    function renderResultRow(test) {
        const row = document.createElement('div');
        row.className = 'p-2 hover:bg-gray-50 cursor-pointer flex justify-between items-center';
        row.dataset.id = test.id;
        row.innerHTML = `<span class="text-sm">${test.test_name} <span class="text-xs text-gray-500">(${test.test_code})</span></span><span class="text-sm font-medium">TZS ${Number(test.price).toLocaleString('en-US')}</span>`;
        row.addEventListener('click', () => addSelectedTest(test));
        return row;
    }

    function addSelectedTest(test) {
        // Prevent duplicates
        if (document.getElementById('selected_test_' + test.id)) return;

        // Hide "no tests selected" message
        const noTestsMsg = document.getElementById('no_tests_selected');
        if (noTestsMsg) noTestsMsg.style.display = 'none';

        const item = document.createElement('div');
        item.className = 'flex items-center justify-between p-2 border rounded-md';
        item.id = 'selected_test_' + test.id;
        item.dataset.price = test.price; // Store price for calculation
        item.innerHTML = `
            <div class="flex items-center space-x-3">
                <input type="hidden" name="selected_tests[]" value="${test.id}">
                <span class="text-sm">${test.test_name} <span class="text-xs text-gray-500">(${test.test_code})</span></span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm font-medium">TZS ${Number(test.price).toLocaleString('en-US')}</span>
                <button type="button" class="px-2 py-1 text-xs bg-red-100 text-red-600 hover:bg-red-200 rounded" onclick="removeSelectedTest('${test.id}')">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>
        `;
        selectedDiv.appendChild(item);
        
        // Clear search input and hide results after selection
        searchInput.value = '';
        resultsDiv.classList.add('hidden');
        
        updateLabTotal();
    }

    function showResults(items) {
        resultsDiv.innerHTML = '';
        if (!items || items.length === 0) {
            resultsDiv.classList.add('hidden');
            return;
        }
        items.forEach(it => resultsDiv.appendChild(renderResultRow(it)));
        resultsDiv.classList.remove('hidden');
    }

    function searchTests(q) {
        if (!q || q.length < 2) { 
            showResults([]); 
            return; 
        }
        console.log('Searching tests for:', q);
    const searchUrl = '<?php echo $BASE_PATH; ?>/lab/search_tests?q=' + encodeURIComponent(q);
        console.log('Search URL:', searchUrl);
    // Use receptionist search endpoint (no lab role required)
    const recipSearchUrl = '<?php echo $BASE_PATH; ?>/receptionist/search_lab_tests?q=' + encodeURIComponent(q);
    console.log('Search URL:', recipSearchUrl);
    fetch(recipSearchUrl, { credentials: 'same-origin' })
            .then(r => {
                console.log('Search response status:', r.status, 'headers:', r.headers.get('content-type'));
                const ct = r.headers.get('content-type') || '';
                if (!ct.includes('application/json')) {
                    // Likely redirected to login or returned HTML
                    return r.text().then(text => { throw new Error('Non-JSON response: ' + (text.slice(0,200)) ); });
                }
                return r.json();
            })
            .then(data => {
                console.log('Search response data:', data);
                if (!data || (Array.isArray(data) && data.length === 0)) {
                    // show a friendly no-results row
                    showResults([]);
                    resultsDiv.innerHTML = '<div class="p-2 text-sm text-gray-500">No matching tests found</div>';
                    resultsDiv.classList.remove('hidden');
                    return;
                }
                showResults(data);
            })
            .catch(err => {
                console.error('Search request failed:', err);
                let msg = 'Search failed';
                if (err && err.message && err.message.startsWith('Non-JSON response')) {
                    msg = 'Search returned non-JSON response (likely you are logged out). Please refresh and login again.';
                }
                resultsDiv.innerHTML = '<div class="p-2 text-sm text-red-500">' + msg + '</div>';
                resultsDiv.classList.remove('hidden');
            });
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(debounce);
        const q = this.value.trim();
        debounce = setTimeout(() => searchTests(q), 250);
    });

    // hide results when clicking outside
    document.addEventListener('click', function(e){
        if (!resultsDiv.contains(e.target) && e.target !== searchInput) {
            resultsDiv.classList.add('hidden');
        }
    });

})();
</script>

<script>
// Prevent double submission and show user feedback
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('patientRegistrationForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        var btn = document.getElementById('submitBtn');
        var txt = document.getElementById('submitText');
        if (btn) {
            btn.disabled = true;
            if (txt) txt.textContent = 'Registering...';
            btn.classList.add('opacity-70', 'cursor-not-allowed');
        }
    });
});
</script>
