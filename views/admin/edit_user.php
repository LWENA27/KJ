<?php 
$pageTitle = 'Edit User';
$userRole = 'admin';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ' . ($BASE_PATH ?? '') . '/auth/login');
    exit;
}
?>

<div class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-emerald-50">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4">
                <a href="<?= $BASE_PATH ?>/admin/users" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Users
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-primary-900">Edit User</h1>
                    <p class="text-primary-600 mt-1">Update user account information</p>
                </div>
            </div>
        </div>

        <!-- Edit User Form -->
        <div class="max-w-2xl">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-xl font-semibold text-gray-900">User Information</h2>
                    <p class="text-gray-600">Update the user details below</p>
                </div>
                
                <form method="POST" action="edit_user?id=<?= $user['id'] ?>" class="card-body space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    
                    <!-- Personal Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">First Name *</label>
                            <input type="text" name="first_name" class="form-input" 
                                   value="<?= htmlspecialchars($user['first_name']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Last Name *</label>
                            <input type="text" name="last_name" class="form-input" 
                                   value="<?= htmlspecialchars($user['last_name']) ?>" required>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-input" 
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-input" 
                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <!-- Account Information -->
                    <div class="space-y-6">
                        <div class="form-group">
                            <label class="form-label">Username *</label>
                            <input type="text" name="username" class="form-input" 
                                   value="<?= htmlspecialchars($user['username']) ?>" required>
                            <p class="text-sm text-gray-500 mt-1">Used for login. Must be unique.</p>
                        </div>
                        
                        <!-- Current Roles Display -->
                        <?php if (!empty($user_roles)): ?>
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-user-tag text-blue-600 mr-2"></i>
                                <h4 class="text-sm font-semibold text-blue-900">Current Assigned Roles</h4>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($user_roles as $role): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $role === $primary_role ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-blue-100 text-blue-800' ?>">
                                        <?php if ($role === $primary_role): ?>
                                            <i class="fas fa-star text-yellow-600 mr-1 text-xs"></i>
                                        <?php endif; ?>
                                        <?= ucwords(str_replace('_', ' ', $role)) ?>
                                        <?php if ($role === $primary_role): ?>
                                            <span class="ml-1 text-xs">(Primary)</span>
                                        <?php endif; ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Role Selection -->
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-700">User Roles *</label>
                            <p class="text-sm text-gray-500 flex items-center mb-3">
                                <i class="fas fa-info-circle mr-1"></i>
                                Select one or more roles for this user. At least one role is required.
                            </p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="roleCheckboxes">
                                <?php 
                                $roles = [
                                    'admin' => ['icon' => '🔐', 'label' => 'Administrator'],
                                    'doctor' => ['icon' => '👨‍⚕️', 'label' => 'Doctor'],
                                    'receptionist' => ['icon' => '📋', 'label' => 'Receptionist'],
                                    'nurse' => ['icon' => '👩‍⚕️', 'label' => 'Nurse'],
                                    'accountant' => ['icon' => '�', 'label' => 'Accountant'],
                                    'pharmacist' => ['icon' => '💊', 'label' => 'Pharmacist'],
                                    'lab_technician' => ['icon' => '🔬', 'label' => 'Lab Technician'],
                                    'radiologist' => ['icon' => '🩻', 'label' => 'Radiologist']
                                ];
                                
                                $current_roles = $user_roles ?? [$user['role']];
                                
                                foreach ($roles as $role_value => $role_info): 
                                    $is_checked = in_array($role_value, $current_roles);
                                ?>
                                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-indigo-50 hover:border-indigo-300 transition-all duration-200 role-checkbox-wrapper <?= $is_checked ? 'bg-indigo-50 border-indigo-400' : '' ?>">
                                        <input type="checkbox" name="roles[]" value="<?= $role_value ?>" 
                                               class="form-checkbox h-5 w-5 text-indigo-600 rounded focus:ring-2 focus:ring-indigo-500 mr-3 role-checkbox"
                                               onchange="handleRoleChange()"
                                               <?= $is_checked ? 'checked' : '' ?>>
                                        <span class="text-sm font-medium text-gray-700">
                                            <span class="mr-1"><?= $role_info['icon'] ?></span>
                                            <?= $role_info['label'] ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div id="roleError" class="hidden text-red-600 text-sm mt-2 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Please select at least one role.
                            </div>
                        </div>
                        
                        <!-- Primary Role Selection -->
                        <div class="space-y-3" id="primaryRoleSection">
                            <label class="block text-sm font-semibold text-gray-700">Primary Role *</label>
                            <p class="text-sm text-gray-500 flex items-center mb-3">
                                <i class="fas fa-star mr-1 text-yellow-500"></i>
                                Select which role should be the primary role. This determines the default dashboard and permissions.
                            </p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="primaryRoleRadios">
                                <!-- Primary role radio buttons will be dynamically generated -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Account Status -->
                    <div class="form-group">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" class="form-checkbox mr-3" 
                                   <?= $user['is_active'] ? 'checked' : '' ?>>
                            <span class="form-label mb-0">Account is active</span>
                        </label>
                        <p class="text-sm text-gray-500 mt-1">Inactive users cannot log in to the system.</p>
                    </div>
                    
                    <!-- Password Change -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Change Password (Optional)</h3>
                        <div class="form-group mb-0">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-input" minlength="6">
                            <p class="text-sm text-gray-500 mt-1">Leave blank to keep current password. Minimum 6 characters if changing.</p>
                        </div>
                    </div>
                    
                    <!-- Submit Buttons -->
                    <div class="flex gap-4 pt-6">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-2"></i>Update User
                        </button>
                        <a href="<?= $BASE_PATH ?>/admin/users" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Role management
const roleData = {
    'admin': { icon: '🔐', label: 'Administrator' },
    'doctor': { icon: '👨‍⚕️', label: 'Doctor' },
    'receptionist': { icon: '📋', label: 'Receptionist' },
    'nurse': { icon: '👩‍⚕️', label: 'Nurse' },
    'accountant': { icon: '💰', label: 'Accountant' },
    'pharmacist': { icon: '💊', label: 'Pharmacist' },
    'lab_technician': { icon: '🔬', label: 'Lab Technician' },
    'radiologist': { icon: '🩻', label: 'Radiologist' }
};

const currentPrimaryRole = '<?= $primary_role ?? $user['role'] ?>';

function handleRoleChange() {
    const checkboxes = document.querySelectorAll('.role-checkbox');
    const selectedRoles = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
    const primaryRoleSection = document.getElementById('primaryRoleSection');
    const primaryRoleRadios = document.getElementById('primaryRoleRadios');
    const roleError = document.getElementById('roleError');
    
    // Hide error if roles are selected
    if (selectedRoles.length > 0) {
        roleError.classList.add('hidden');
    }
    
    // Update checkbox wrapper styling
    checkboxes.forEach(cb => {
        const wrapper = cb.closest('.role-checkbox-wrapper');
        if (cb.checked) {
            wrapper.classList.add('bg-indigo-50', 'border-indigo-400');
        } else {
            wrapper.classList.remove('bg-indigo-50', 'border-indigo-400');
        }
    });
    
    // Show/hide primary role section based on selection
    if (selectedRoles.length === 0) {
        primaryRoleSection.style.display = 'none';
    } else if (selectedRoles.length === 1) {
        // Auto-select primary role if only one role is selected
        primaryRoleSection.style.display = 'none';
        // We'll handle this in form submission
    } else {
        // Show primary role selection for multiple roles
        primaryRoleSection.style.display = 'block';
        
        // Generate radio buttons for selected roles
        primaryRoleRadios.innerHTML = '';
        selectedRoles.forEach((role) => {
            const roleInfo = roleData[role];
            const radioWrapper = document.createElement('label');
            radioWrapper.className = 'flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-yellow-50 hover:border-yellow-300 transition-all duration-200';
            
            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'primary_role';
            radio.value = role;
            radio.className = 'form-radio h-5 w-5 text-yellow-500 focus:ring-2 focus:ring-yellow-500 mr-3';
            radio.required = true;
            
            // Select current primary role or first role
            if (role === currentPrimaryRole || (currentPrimaryRole === '' && selectedRoles[0] === role)) {
                radio.checked = true;
            }
            
            const label = document.createElement('span');
            label.className = 'text-sm font-medium text-gray-700';
            label.innerHTML = `<span class="mr-1">${roleInfo.icon}</span>${roleInfo.label}`;
            
            radioWrapper.appendChild(radio);
            radioWrapper.appendChild(label);
            primaryRoleRadios.appendChild(radioWrapper);
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Trigger role change to set up initial state
    handleRoleChange();
    
    const form = document.querySelector('form');
    
    // Validate on submit
    form.addEventListener('submit', function(e) {
        const checkboxes = document.querySelectorAll('.role-checkbox');
        const selectedRoles = Array.from(checkboxes).filter(cb => cb.checked);
        const roleError = document.getElementById('roleError');
        
        if (selectedRoles.length === 0) {
            e.preventDefault();
            roleError.classList.remove('hidden');
            roleError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
        
        // If only one role selected, automatically set it as primary
        if (selectedRoles.length === 1) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'primary_role';
            hiddenInput.value = selectedRoles[0].value;
            form.appendChild(hiddenInput);
        }
    });
});
</script>
