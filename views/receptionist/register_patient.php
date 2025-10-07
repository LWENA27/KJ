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
                            <option value="consultation">Doctor Consultation</option>
                            <option value="lab_test">Laboratory Test Only</option>
                            <option value="medicine_pickup">Medicine Collection</option>
                            <option value="minor_service">Minor Service (Injection, etc.)</option>
                        </select>
                        <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                    </div>
                </div>
                
                <!-- Visit Type Badge -->
                <div id="visitBadge" class="mt-4 hidden">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <i class="fas fa-info-circle text-yellow-400 mt-1"></i>
                            <p class="ml-3 text-sm text-yellow-700" id="visitBadgeText">
                                Please select a visit type
                            </p>
                        </div>
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
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <!-- Blood Pressure -->
                    <div>
                        <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-1">
                            Blood Pressure (mmHg)
                        </label>
                        <input type="text" id="blood_pressure" name="blood_pressure" 
                               placeholder="120/80"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <!-- Pulse Rate -->
                    <div>
                        <label for="pulse_rate" class="block text-sm font-medium text-gray-700 mb-1">
                            Pulse Rate (bpm)
                        </label>
                        <input type="number" id="pulse_rate" name="pulse_rate" 
                               min="40" max="200"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <!-- Weight -->
                    <div>
                        <label for="body_weight" class="block text-sm font-medium text-gray-700 mb-1">
                            Weight (kg)
                        </label>
                        <input type="number" id="body_weight" name="body_weight" 
                               step="0.1" min="0" max="300"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <!-- Height -->
                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 mb-1">
                            Height (cm)
                        </label>
                        <input type="number" id="height" name="height" 
                               step="0.1" min="0" max="300"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
            </div>

            <!-- Consultation Payment Section (Hidden by default) -->
            <div id="consultationPaymentSection" class="border-b pb-6 hidden">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-credit-card mr-3 text-green-600"></i>Consultation Payment
                </h3>
                
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                        <p class="ml-3 text-sm text-blue-700">
                            Consultation fee must be paid before seeing the doctor. Standard fee: TZS 3,000
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="consultation_fee" class="block text-sm font-medium text-gray-700 mb-1">
                            Consultation Fee (TZS) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="consultation_fee" name="consultation_fee" 
                               min="0" step="100" value="3000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <select id="payment_method" name="payment_method"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash Payment</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="mobile_money">Mobile Money (M-Pesa, Tigo Pesa, etc.)</option>
                            <option value="insurance">Insurance Coverage</option>
                        </select>
                        <span class="error-message text-red-500 text-xs mt-1 hidden"></span>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-gray-50 rounded-md border">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Total Payment:</span>
                        <span id="totalAmount" class="text-2xl font-bold text-green-600">TZS 3,000</span>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact Section -->
            <div class="border-b pb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-phone mr-3 text-red-600"></i>Emergency Contact
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

    const CONFIG = {
        CONSULTATION_FEE: 3000
    };

    let elements = {
        visitType: null,
        consultationSection: null,
        consultationFee: null,
        paymentMethod: null,
        totalAmount: null,
        visitBadge: null,
        visitBadgeText: null,
        vitalSignsSection: null
    };

    function initElements() {
        elements.visitType = document.getElementById('visit_type');
        elements.consultationSection = document.getElementById('consultationPaymentSection');
        elements.consultationFee = document.getElementById('consultation_fee');
        elements.paymentMethod = document.getElementById('payment_method');
        elements.totalAmount = document.getElementById('totalAmount');
        elements.visitBadge = document.getElementById('visitBadge');
        elements.visitBadgeText = document.getElementById('visitBadgeText');
        elements.vitalSignsSection = document.getElementById('vitalSignsSection');
    }

    function toggleVisitSections() {
        const visitType = elements.visitType ? elements.visitType.value : '';

        // Hide all optional sections initially
        if (elements.consultationSection) {
            elements.consultationSection.classList.add('hidden');
        }
        if (elements.vitalSignsSection) {
            elements.vitalSignsSection.classList.add('hidden');
        }
        if (elements.visitBadge) {
            elements.visitBadge.classList.add('hidden');
        }

        // Clear required attributes
        if (elements.consultationFee) elements.consultationFee.removeAttribute('required');
        if (elements.paymentMethod) elements.paymentMethod.removeAttribute('required');

        // Show relevant sections based on visit type
        switch(visitType) {
            case 'consultation':
                // Show consultation payment and vital signs for doctor consultation
                if (elements.consultationSection) {
                    elements.consultationSection.classList.remove('hidden');
                }
                if (elements.vitalSignsSection) {
                    elements.vitalSignsSection.classList.remove('hidden');
                }
                if (elements.consultationFee) {
                    elements.consultationFee.setAttribute('required', 'required');
                    if (!elements.consultationFee.value) {
                        elements.consultationFee.value = CONFIG.CONSULTATION_FEE;
                    }
                }
                if (elements.paymentMethod) {
                    elements.paymentMethod.setAttribute('required', 'required');
                }
                if (elements.visitBadge && elements.visitBadgeText) {
                    elements.visitBadge.classList.remove('hidden');
                    elements.visitBadgeText.textContent = 'Payment required for consultation';
                }
                updateTotalAmount();
                break;
                
            case 'lab_test':
                // Only show vital signs for lab tests
                if (elements.vitalSignsSection) {
                    elements.vitalSignsSection.classList.remove('hidden');
                }
                if (elements.visitBadge && elements.visitBadgeText) {
                    elements.visitBadge.classList.remove('hidden');
                    elements.visitBadgeText.textContent = 'Lab tests will be selected by medical staff';
                }
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
                
            default:
                // No visit type selected - hide all optional sections
                if (elements.consultationSection) {
                    elements.consultationSection.classList.add('hidden');
                }
                if (elements.vitalSignsSection) {
                    elements.vitalSignsSection.classList.add('hidden');
                }
                if (elements.visitBadge) {
                    elements.visitBadge.classList.add('hidden');
                }
                break;
        }
    }

    function updateTotalAmount() {
        if (!elements.totalAmount || !elements.consultationFee) return;
        
        const fee = parseFloat(elements.consultationFee.value) || 0;
        elements.totalAmount.textContent = `TZS ${fee.toLocaleString('en-US')}`;
    }

    function init() {
        initElements();
        
        // Set up event listeners
        if (elements.visitType) {
            elements.visitType.addEventListener('change', toggleVisitSections);
        }

        if (elements.consultationFee) {
            elements.consultationFee.addEventListener('input', updateTotalAmount);
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
