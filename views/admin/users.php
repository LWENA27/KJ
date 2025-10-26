<style>
/* Users table responsive fixes: ensure table fits available space and allow wrapping on smaller screens */
.users-table { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.users-table table { box-sizing: border-box; width: 100%; min-width: 100%; table-layout: fixed; }
.users-table th, .users-table td { overflow-wrap: anywhere; word-break: break-word; }

@media (max-width: 1024px) {
    /* Allow table cells to wrap on tablets and smaller so page doesn't overflow */
    .users-table table, .users-table thead, .users-table tbody, .users-table tr, .users-table th, .users-table td {
        display: table; /* preserve table layout for accessibility but allow cell wrapping */
    }

    .users-table th, .users-table td {
        white-space: normal !important;
        word-break: break-word;
    }

    .users-table table { min-width: 0 !important; }
}
</style>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
        <a href="<?= $BASE_PATH ?>/admin/add_user" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            <i class="fas fa-plus mr-2"></i>Add New User
        </a>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">System Users</h3>
        </div>
    <div class="overflow-x-auto users-table">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        @<?php echo htmlspecialchars($user['username']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                <?php
                                switch ($user['role']) {
                                    case 'admin':
                                        echo 'bg-red-100 text-red-800';
                                        break;
                                    case 'doctor':
                                        echo 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'receptionist':
                                        echo 'bg-green-100 text-green-800';
                                        break;
                                    case 'lab_technician':
                                        echo 'bg-purple-100 text-purple-800';
                                        break;
                                }
                                ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                <?php echo $user['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="<?= $BASE_PATH ?>/admin/edit_user?id=<?= $user['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <button onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')" 
                                        class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div id="deleteUserModal" class="modal">
    <div class="modal-content max-w-md">
        <div class="modal-header">
            <h3 class="modal-title">Delete User</h3>
            <button onclick="hideModal('deleteUserModal')" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="<?= $BASE_PATH ?>/admin/delete_user" class="modal-body">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="user_id" id="delete_user_id">
            
            <div class="text-center mb-6">
                <div class="text-red-500 text-4xl mb-4">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <p class="text-gray-900 font-medium">Are you sure you want to delete this user?</p>
                <p class="text-gray-600 mt-2">Username: <span id="delete_username" class="font-medium"></span></p>
                <p class="text-red-600 text-sm mt-3">This action cannot be undone.</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="hideModal('deleteUserModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash mr-2"></i>Delete User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function deleteUser(userId, username) {
    document.getElementById('delete_user_id').value = userId;
    document.getElementById('delete_username').textContent = username;
    showModal('deleteUserModal');
}
</script>
