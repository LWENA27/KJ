<?php
// Ensure user is logged in and is a receptionist
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'receptionist') {
    header('Location: /KJ/auth/login');
    exit;
}

$BASE_PATH = '/KJ';

// Patient data is passed from the controller
// $patient, $patient_id, and $next_visit_number are available from controller
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-green-700 to-emerald-800 rounded-lg shadow-xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="text-white">
                <h1 class="text-3xl font-bold flex items-center">
                    <i class="fas fa-user-check mr-3 text-green-200"></i>
                    Patient Revisit
                </h1>
                <p class="text-green-100 mt-2 text-lg">Create a new visit for existing patient</p>
            </div>
            <div class="text-right text-white">
                <div class="text-sm text-green-200">
                    <i class="fas fa-calendar text-green-300"></i>
                    <?php echo date('F j, Y'); ?>
                </div>
                <div class="text-lg font-semibold mt-1">
                    Receptionist: <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($patient): ?>
    <!-- Patient Information Display -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-user text-blue-600 mr-2"></i>
            Patient Information
        </h2>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Patient Name</label>
                    <div class="text-lg font-semibold text-gray-900">
                        <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Registration Number</label>
                    <div class="text-lg font-semibold text-blue-600">
                        <?php echo htmlspecialchars($patient['registration_number']); ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Next Visit Number</label>
                    <div class="text-lg font-semibold text-green-600">
                        Visit #<?php echo $next_visit_number; ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <div class="text-gray-900">
                        <?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Gender</label>
                    <div class="text-gray-900">
                        <?php echo htmlspecialchars(ucfirst($patient['gender'] ?? 'N/A')); ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Age</label>
                    <div class="text-gray-900">
                        <?php 
                        if ($patient['date_of_birth']) {
                            echo date_diff(date_create($patient['date_of_birth']), date_create('today'))->y . ' years';
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Revisit Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-plus-circle text-green-600 mr-2"></i>
            Create New Visit
        </h2>

        <form id="revisitForm" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            
            <?php if ($patient): ?>
                <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">
            <?php else: ?>
                <!-- Patient Selection via Global Search -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                        <p class="text-yellow-800">
                            <strong>No patient selected.</strong> Use the global search in the top navigation to find and select a patient, then access this page from their patient view.
                        </p>
                    </div>
                    <div class="mt-3">
                        <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/patients" 
                           class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Go to Patient List
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Visit Type Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-stethoscope text-blue-500 mr-1"></i>
                    Visit Type <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="visit_type" value="consultation" class="sr-only" required>
                        <div class="flex-1">
                            <div class="flex items-center">
                                <i class="fas fa-user-md text-blue-500 mr-3 text-xl"></i>
                                <div>
                                    <div class="text-lg font-medium text-gray-900">Consultation</div>
                                    <div class="text-sm text-gray-600">Full medical consultation with doctor</div>
                                </div>
                            </div>
                        </div>
                        <div class="visit-type-indicator"></div>
                    </label>

                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="visit_type" value="lab_only" class="sr-only">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <i class="fas fa-flask text-green-500 mr-3 text-xl"></i>
                                <div>
                                    <div class="text-lg font-medium text-gray-900">Lab Tests Only</div>
                                    <div class="text-sm text-gray-600">Laboratory tests without consultation</div>
                                </div>
                            </div>
                        </div>
                        <div class="visit-type-indicator"></div>
                    </label>

                    <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="visit_type" value="minor_service" class="sr-only">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <i class="fas fa-band-aid text-purple-500 mr-3 text-xl"></i>
                                <div>
                                    <div class="text-lg font-medium text-gray-900">Minor Service</div>
                                    <div class="text-sm text-gray-600">Injection, dressing, etc.</div>
                                </div>
                            </div>
                        </div>
                        <div class="visit-type-indicator"></div>
                    </label>
                </div>
            </div>

            <!-- Payment Information -->
            <div id="paymentSection" class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-credit-card text-green-600 mr-2"></i>
                    Payment Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Fee Amount (TSH)
                        </label>
                        <input type="number" name="consultation_fee" step="0.01" min="0"
                               placeholder="Enter fee amount"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Payment Method
                        </label>
                        <select name="payment_method" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                            <option value="">No Payment Now</option>
                            <option value="cash">Cash</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="insurance">Insurance</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <?php if ($patient): ?>
            <div class="flex space-x-4 pt-6 border-t border-gray-200">
                <button type="submit" 
                        class="bg-green-500 text-white px-8 py-3 rounded-md hover:bg-green-600 transition-colors font-medium text-lg">
                    <i class="fas fa-check mr-2"></i>Create Visit #<?php echo $next_visit_number; ?>
                </button>
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/view_patient?id=<?php echo $patient['id']; ?>" 
                   class="bg-gray-300 text-gray-700 px-6 py-3 rounded-md hover:bg-gray-400 transition-colors font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Patient
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
// Handle visit type selection styling
document.querySelectorAll('input[name="visit_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Reset all labels
        document.querySelectorAll('label').forEach(label => {
            label.classList.remove('border-green-500', 'bg-green-50');
            label.classList.add('border-gray-300');
            const indicator = label.querySelector('.visit-type-indicator');
            if (indicator) {
                indicator.innerHTML = '';
            }
        });
        
        // Style selected label
        const selectedLabel = this.closest('label');
        selectedLabel.classList.remove('border-gray-300');
        selectedLabel.classList.add('border-green-500', 'bg-green-50');
        const indicator = selectedLabel.querySelector('.visit-type-indicator');
        if (indicator) {
            indicator.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
        }
    });
});

// Handle form submission
document.getElementById('revisitForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Prevent double submission
    if (this.classList.contains('submitting')) {
        console.log('Form already being submitted, preventing duplicate');
        return false;
    }
    
    <?php if (!$patient): ?>
    showToast('Please select a patient first', 'error');
    return;
    <?php endif; ?>
    
    const formData = new FormData(this);
    
    // Debug: Log the CSRF token being sent
    console.log('CSRF Token being sent:', formData.get('csrf_token'));
    console.log('All form data:');
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    // Validate visit type is selected
    if (!formData.get('visit_type')) {
        showToast('Please select a visit type', 'error');
        return;
    }
    
    // Mark form as submitting and disable
    this.classList.add('submitting');
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating Visit...';
    submitBtn.disabled = true;
    
    // Disable all form inputs
    const inputs = this.querySelectorAll('input, select, button');
    inputs.forEach(input => input.disabled = true);
    
    fetch('<?php echo $BASE_PATH; ?>/receptionist/create_revisit', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Check if response is actually JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server did not return JSON response');
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response received:', data);
        
        // Always re-enable form first
        this.classList.remove('submitting');
        submitBtn.disabled = false;
        inputs.forEach(input => input.disabled = false);
        
        if (data.success) {
            // Update button to show success
            submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Visit Created!';
            submitBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
            submitBtn.classList.add('bg-green-600');
            
            showToast(data.message, 'success');
            
            // Show additional information about what happens next
            if (data.in_doctor_queue) {
                setTimeout(() => {
                    showToast('Patient is now available in the doctor\'s queue for consultation.', 'info');
                }, 2000);
            }
            
            // Redirect to patient view after successful revisit
            setTimeout(() => {
                console.log('Redirecting to patient view...');
                window.location.href = `<?php echo $BASE_PATH; ?>/receptionist/view_patient?id=<?php echo $patient_id; ?>`;
            }, 3000);
        } else {
            // Reset button on error
            submitBtn.innerHTML = originalText;
            throw new Error(data.message || 'Unknown error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast(error.message || 'Error creating revisit', 'error');
        
        // Re-enable form on error
        this.classList.remove('submitting');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        inputs.forEach(input => input.disabled = false);
    });
});

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white max-w-sm`;
    
    const bgColors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };
    
    toast.classList.add(bgColors[type] || bgColors.info);
    toast.innerHTML = `
        <div class="flex items-center space-x-3">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}
</script>

<style>
.visit-type-indicator {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

input[type="radio"]:checked + div .visit-type-indicator {
    color: #10b981;
}
</style>