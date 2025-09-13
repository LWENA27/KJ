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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">Username *</label>
                            <input type="text" name="username" class="form-input" 
                                   value="<?= htmlspecialchars($user['username']) ?>" required>
                            <p class="text-sm text-gray-500 mt-1">Used for login. Must be unique.</p>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Role *</label>
                            <select name="role" class="form-select" required>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                                <option value="doctor" <?= $user['role'] === 'doctor' ? 'selected' : '' ?>>Doctor</option>
                                <option value="receptionist" <?= $user['role'] === 'receptionist' ? 'selected' : '' ?>>Receptionist</option>
                                <option value="lab_technician" <?= $user['role'] === 'lab_technician' ? 'selected' : '' ?>>Lab Technician</option>
                            </select>
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
