<?php 
$pageTitle = 'Add New User';
$userRole = 'admin';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . ($BASE_PATH ?? '') . '/auth/login');
    exit;
}
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-6">
                <a href="<?= $BASE_PATH ?>/admin/users" 
                   class="inline-flex items-center px-4 py-2 bg-white text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 hover:shadow-md transition-all duration-200 border border-gray-200">
                    <i class="fas fa-arrow-left mr-2 text-gray-500"></i>
                    <span class="font-medium">Back to Users</span>
                </a>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Add New User</h1>
                    <p class="text-gray-600 text-lg">Create a new system user account</p>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <span class="text-red-800"><?= htmlspecialchars($_SESSION['error']) ?></span>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-green-800"><?= htmlspecialchars($_SESSION['success']) ?></span>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Add User Form -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                    <div class="flex items-center">
                        <div class="bg-white/20 rounded-lg p-3 mr-4">
                            <i class="fas fa-user-plus text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">User Information</h2>
                            <p class="text-blue-100 mt-1">Fill in the details to create a new user account</p>
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="add_user" class="p-8">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    
                    <!-- Personal Information Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user text-indigo-500 mr-2"></i>
                            Personal Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">First Name *</label>
                                <input type="text" name="first_name" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-gray-50 focus:bg-white" 
                                       required placeholder="Enter first name">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Last Name *</label>
                                <input type="text" name="last_name" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-gray-50 focus:bg-white" 
                                       required placeholder="Enter last name">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Information Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-envelope text-indigo-500 mr-2"></i>
                            Contact Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Email Address *</label>
                                <input type="email" name="email" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-gray-50 focus:bg-white" 
                                       required placeholder="Enter email address">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Phone Number</label>
                                <input type="tel" name="phone" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-gray-50 focus:bg-white" 
                                       placeholder="Enter phone number">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Account Information Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-key text-indigo-500 mr-2"></i>
                            Account Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Username *</label>
                                <input type="text" name="username" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-gray-50 focus:bg-white" 
                                       required placeholder="Enter username">
                                <p class="text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Used for login. Must be unique.
                                </p>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Role *</label>
                                <select name="role" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-gray-50 focus:bg-white" 
                                        required>
                                    <option value="">Select Role</option>
                                    <option value="admin">🔐 Administrator</option>
                                    <option value="doctor">👨‍⚕️ Doctor</option>
                                    <option value="receptionist">📋 Receptionist</option>
                                    <option value="lab_technician">🔬 Lab Technician</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-lock text-indigo-500 mr-2"></i>
                            Security
                        </h3>
                        <div class="max-w-md">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">Password *</label>
                                <div class="relative">
                                    <input type="password" name="password" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 bg-gray-50 focus:bg-white pr-10" 
                                           required minlength="6" placeholder="Enter password">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <i class="fas fa-eye-slash text-gray-400 cursor-pointer hover:text-gray-600" 
                                           onclick="togglePassword(this)"></i>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 flex items-center">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    Minimum 6 characters required.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-8 border-t border-gray-200">
                        <button type="submit" 
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:from-indigo-700 hover:to-purple-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200">
                            <i class="fas fa-user-plus mr-2"></i>
                            Create User
                        </button>
                        <a href="<?= $BASE_PATH ?>/admin/users" 
                           class="inline-flex items-center px-8 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg shadow-md hover:bg-gray-300 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(icon) {
    const input = icon.closest('.relative').querySelector('input');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

// Add form validation styling
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                this.classList.remove('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');
            } else {
                this.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                this.classList.add('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                this.classList.add('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');
            }
        });
    });
    
    // Email validation
    const emailInput = form.querySelector('input[type="email"]');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                this.classList.remove('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');
            }
        });
    }
    
    // Password strength indicator
    const passwordInput = form.querySelector('input[type="password"]');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            updatePasswordStrength(strength);
        });
    }
});

function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    return strength;
}

function updatePasswordStrength(strength) {
    // This could be expanded to show a visual strength indicator
    // For now, just change border color based on strength
    const passwordInput = document.querySelector('input[type="password"]');
    if (strength < 2) {
        passwordInput.classList.add('border-red-300');
        passwordInput.classList.remove('border-yellow-300', 'border-green-300');
    } else if (strength < 4) {
        passwordInput.classList.add('border-yellow-300');
        passwordInput.classList.remove('border-red-300', 'border-green-300');
    } else {
        passwordInput.classList.add('border-green-300');
        passwordInput.classList.remove('border-red-300', 'border-yellow-300');
    }
}
</script>
