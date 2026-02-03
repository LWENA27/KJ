<style>
/* Mobile-first responsive design for users table */
.users-table { 
    overflow-x: auto; 
    -webkit-overflow-scrolling: touch; 
}

.users-table table { 
    box-sizing: border-box; 
    width: 100%; 
    table-layout: fixed; 
}

.users-table th, .users-table td { 
    overflow-wrap: break-word;
    word-break: break-word;
}

/* Mobile card-based layout for small screens */
@media (max-width: 768px) {
    .users-table {
        overflow-x: visible;
    }

    .users-table table,
    .users-table thead,
    .users-table tbody,
    .users-table tr,
    .users-table th,
    .users-table td {
        display: block;
        width: 100%;
    }

    .users-table thead {
        display: none; /* Hide table header on mobile */
    }

    .users-table tbody {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .users-table tr {
        display: block;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 0.5rem;
    }

    .users-table tr:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .users-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border: none;
    }

    .users-table td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #6b7280;
        flex: 0 0 40%;
        font-size: 0.875rem;
    }

    .users-table td > * {
        flex: 1;
        text-align: right;
    }

    /* First cell (user info) special styling */
    .users-table tr > td:first-child {
        display: block;
        padding: 0;
        margin-bottom: 0.75rem;
        border-bottom: 1px solid #f3f4f6;
        padding-bottom: 0.75rem;
    }

    .users-table tr > td:first-child::before {
        display: none;
    }

    .user-name-block {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        flex-shrink: 0;
    }

    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-info .name {
        font-weight: 600;
        color: #111827;
        word-break: break-word;
    }

    .user-info .username {
        font-size: 0.875rem;
        color: #6b7280;
        word-break: break-all;
    }

    /* Actions column styling */
    .users-table tr > td:last-child {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding: 0.75rem 0;
        border-top: 1px solid #f3f4f6;
        padding-top: 0.75rem;
    }

    .users-table tr > td:last-child::before {
        display: none;
    }

    .users-table tr > td:last-child a,
    .users-table tr > td:last-child button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.5rem;
        border-radius: 6px;
        font-size: 0.875rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .users-table tr > td:last-child a {
        background: #eff6ff;
        color: #1d4ed8;
        text-decoration: none;
    }

    .users-table tr > td:last-child a:hover {
        background: #dbeafe;
    }

    .users-table tr > td:last-child button {
        background: #fef2f2;
        color: #991b1b;
    }

    .users-table tr > td:last-child button:hover {
        background: #fee2e2;
    }

    .users-table tr > td:last-child i {
        margin: 0;
    }
}

/* Tablet adjustments */
@media (min-width: 769px) and (max-width: 1024px) {
    .users-table table { min-width: 0 !important; }

    .users-table th, .users-table td {
        padding: 0.75rem !important;
        font-size: 0.875rem;
    }

    .users-table th {
        font-size: 0.75rem;
    }

    /* Hide less important columns on tablet */
    .users-table th:nth-child(4),
    .users-table td:nth-child(4) {
        display: none; /* Hide phone */
    }

    .users-table th:nth-child(6),
    .users-table td:nth-child(6) {
        display: none; /* Hide created date */
    }
}

/* Desktop - full table display */
@media (min-width: 1025px) {
    .users-table th, .users-table td {
        padding: 1rem 0.75rem;
    }
}
</style>

<div class="space-y-4 md:space-y-6">
    <!-- Header - stacks on mobile -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">User Management</h1>
        <a href="<?= $BASE_PATH ?>/admin/add_user" class="bg-blue-500 hover:bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-md text-sm sm:text-base whitespace-nowrap inline-flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i>Add New User
        </a>
    </div>

    <!-- Users Card/Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
            <h3 class="text-base sm:text-lg font-medium text-gray-900">System Users</h3>
        </div>
        
        <div class="users-table">
            <table class="w-full">
                <thead class="bg-gray-50 hidden md:table-header-group">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Email</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Phone</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Created</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white md:divide-y md:divide-gray-200">
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-gray-50 md:bg-white md:border-b md:border-gray-200">
                        <!-- User: shows name/username and email on mobile -->
                        <td class="px-4 sm:px-6 py-4" data-label="User">
                            <div class="user-name-block">
                                <div class="user-avatar w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-user text-blue-600 text-xs sm:text-sm"></i>
                                </div>
                                <div class="user-info min-w-0">
                                    <div class="name text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                    </div>
                                    <div class="username text-xs text-gray-500">
                                        @<?php echo htmlspecialchars($user['username']); ?>
                                    </div>
                                    <div class="email md:hidden text-xs text-gray-500 mt-1">
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Role -->
                        <td class="px-4 sm:px-6 py-4 hidden md:table-cell" data-label="Role">
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
                                    default:
                                        echo 'bg-gray-100 text-gray-800';
                                }
                                ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?>
                            </span>
                            <!-- Show role on mobile in user card -->
                            <span class="inline-flex md:hidden px-2 py-1 text-xs font-medium rounded-full mt-2
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
                                    default:
                                        echo 'bg-gray-100 text-gray-800';
                                }
                                ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?>
                            </span>
                        </td>

                        <!-- Email: hidden on mobile, visible on md+ -->
                        <td class="px-4 sm:px-6 py-4 hidden lg:table-cell text-sm text-gray-900" data-label="Email">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </td>

                        <!-- Phone: hidden on tablet and mobile -->
                        <td class="px-4 sm:px-6 py-4 hidden lg:table-cell text-sm text-gray-900" data-label="Phone">
                            <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?>
                        </td>

                        <!-- Status -->
                        <td class="px-4 sm:px-6 py-4" data-label="Status">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                <?php echo $user['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>

                        <!-- Created: hidden on small screens -->
                        <td class="px-4 sm:px-6 py-4 hidden lg:table-cell text-sm text-gray-500" data-label="Created">
                            <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                        </td>

                        <!-- Actions -->
                        <td class="px-4 sm:px-6 py-4" data-label="Actions">
                            <div class="flex flex-col md:flex-row gap-2">
                                <a href="<?= $BASE_PATH ?>/admin/edit_user?id=<?= $user['id'] ?>" class="text-blue-600 hover:text-blue-900 md:text-xs inline-flex items-center justify-center md:mr-2">
                                    <i class="fas fa-edit"></i>
                                    <span class="md:hidden ml-1">Edit</span>
                                </a>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <button onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')" 
                                            class="text-red-600 hover:text-red-900 md:text-xs inline-flex items-center justify-center">
                                        <i class="fas fa-trash"></i>
                                        <span class="md:hidden ml-1">Delete</span>
                                    </button>
                                <?php endif; ?>
                            </div>
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
