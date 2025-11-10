<?php $title = "My Tasks"; ?>

<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-cyan-500 to-blue-600 rounded-lg shadow-xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="text-white">
                <h1 class="text-3xl font-bold flex items-center">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    My Tasks
                </h1>
                <p class="mt-2 text-cyan-100 text-lg">Services and procedures assigned to you</p>
            </div>
            <div class="flex space-x-3">
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/lab/dashboard" 
                   class="bg-white text-cyan-700 hover:bg-cyan-50 px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-lg flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <!-- Total Tasks -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Tasks</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo count($tasks); ?></p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-tasks text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">
                        <?php echo count(array_filter($tasks, fn($t) => $t['status'] === 'pending')); ?>
                    </p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">In Progress</p>
                    <p class="text-3xl font-bold text-blue-600">
                        <?php echo count(array_filter($tasks, fn($t) => $t['status'] === 'in_progress')); ?>
                    </p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-spinner text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks List -->
    <?php if (empty($tasks)): ?>
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-gray-400 text-4xl"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">No Tasks Assigned</h3>
            <p class="text-gray-600 text-lg">You don't have any tasks assigned at the moment.</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Assigned Tasks</h2>
                <p class="text-sm text-gray-600 mt-1">Complete the services assigned to you</p>
            </div>

            <div class="divide-y divide-gray-200">
                <?php foreach ($tasks as $task): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <!-- Task Info -->
                            <div class="flex-1">
                                <div class="flex items-start gap-4">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white shadow-lg">
                                            <i class="fas fa-flask text-lg"></i>
                                        </div>
                                    </div>

                                    <!-- Details -->
                                    <div class="flex-1 min-w-0">
                                        <!-- Service Name -->
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">
                                            <?php echo htmlspecialchars($task['service_name']); ?>
                                        </h3>

                                        <!-- Patient Info -->
                                        <div class="flex items-center text-sm text-gray-600 mb-2">
                                            <i class="fas fa-user mr-2 text-cyan-600"></i>
                                            <span class="font-medium">
                                                <?php echo htmlspecialchars($task['first_name'] . ' ' . $task['last_name']); ?>
                                            </span>
                                            <span class="mx-2">•</span>
                                            <span class="text-gray-500">
                                                <?php echo htmlspecialchars($task['registration_number']); ?>
                                            </span>
                                        </div>

                                        <!-- Description -->
                                        <?php if (!empty($task['service_description'])): ?>
                                            <p class="text-sm text-gray-600 mb-3">
                                                <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                                                <?php echo htmlspecialchars($task['service_description']); ?>
                                            </p>
                                        <?php endif; ?>

                                        <!-- Meta Info -->
                                        <div class="flex flex-wrap gap-4 text-xs text-gray-500">
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar mr-1"></i>
                                                Assigned: <?php echo date('M d, Y H:i', strtotime($task['created_at'])); ?>
                                            </span>
                                            <?php if (!empty($task['ordered_by_first'])): ?>
                                                <span class="flex items-center">
                                                    <i class="fas fa-user-md mr-1"></i>
                                                    By: <?php echo htmlspecialchars($task['ordered_by_first'] . ' ' . $task['ordered_by_last']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($task['price'])): ?>
                                                <span class="flex items-center font-semibold text-cyan-700">
                                                    <i class="fas fa-money-bill mr-1"></i>
                                                    Tsh <?php echo number_format($task['price'], 0); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Notes -->
                                        <?php if (!empty($task['notes'])): ?>
                                            <div class="mt-3 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                                                <p class="text-xs font-semibold text-yellow-800 mb-1">Notes:</p>
                                                <p class="text-xs text-yellow-700 whitespace-pre-line">
                                                    <?php echo htmlspecialchars($task['notes']); ?>
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Status & Actions -->
                            <div class="flex flex-col items-end gap-3 lg:w-64">
                                <!-- Status Badge -->
                                <?php
                                $status_colors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                    'in_progress' => 'bg-blue-100 text-blue-800 border-blue-300',
                                    'completed' => 'bg-green-100 text-green-800 border-green-300',
                                    'cancelled' => 'bg-red-100 text-red-800 border-red-300'
                                ];
                                $status_color = $status_colors[$task['status']] ?? 'bg-gray-100 text-gray-800 border-gray-300';
                                ?>
                                <span class="px-3 py-1 text-xs font-bold rounded-full border-2 <?php echo $status_color; ?>">
                                    <?php echo strtoupper(str_replace('_', ' ', $task['status'])); ?>
                                </span>

                                <!-- Action Buttons -->
                                <div class="flex flex-col gap-2 w-full">
                                    <?php if ($task['status'] === 'pending'): ?>
                                        <button onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'in_progress')"
                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-md">
                                            <i class="fas fa-play mr-2"></i>Start Task
                                        </button>
                                    <?php elseif ($task['status'] === 'in_progress'): ?>
                                        <button onclick="showCompleteModal(<?php echo $task['id']; ?>, '<?php echo htmlspecialchars($task['service_name'], ENT_QUOTES); ?>')"
                                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-md">
                                            <i class="fas fa-check mr-2"></i>Complete
                                        </button>
                                        <button onclick="updateTaskStatus(<?php echo $task['id']; ?>, 'pending')"
                                                class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                            <i class="fas fa-pause mr-2"></i>Pause
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button onclick="showCancelModal(<?php echo $task['id']; ?>)"
                                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-times mr-2"></i>Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Complete Task Modal -->
<div id="completeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Complete Task</h3>
                <button onclick="closeCompleteModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <p class="text-gray-600 mb-4">
                Mark <strong id="completeTaskName"></strong> as completed?
            </p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Completion Notes (Optional)
                </label>
                <textarea id="completeNotes" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="Add any notes about the completed task..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button onclick="closeCompleteModal()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button onclick="confirmComplete()"
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-check mr-2"></i>Complete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Task Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Cancel Task</h3>
                <button onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <p class="text-gray-600 mb-4">
                Are you sure you want to cancel this task? Please provide a reason.
            </p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Cancellation Reason <span class="text-red-500">*</span>
                </label>
                <textarea id="cancelNotes" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                          placeholder="Why is this task being cancelled?"></textarea>
            </div>
            
            <div class="flex gap-3">
                <button onclick="closeCancelModal()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Keep Task
                </button>
                <button onclick="confirmCancel()"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancel Task
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentTaskId = null;

function updateTaskStatus(taskId, status, notes = '') {
    const formData = new FormData();
    formData.append('csrf_token', '<?php echo $csrf_token; ?>');
    formData.append('task_id', taskId);
    formData.append('status', status);
    formData.append('notes', notes);

    fetch('<?php echo $BASE_PATH; ?>/lab/update_task_status', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to update task'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update task status');
    });
}

function showCompleteModal(taskId, taskName) {
    currentTaskId = taskId;
    document.getElementById('completeTaskName').textContent = taskName;
    document.getElementById('completeNotes').value = '';
    document.getElementById('completeModal').classList.remove('hidden');
}

function closeCompleteModal() {
    document.getElementById('completeModal').classList.add('hidden');
    currentTaskId = null;
}

function confirmComplete() {
    const notes = document.getElementById('completeNotes').value;
    updateTaskStatus(currentTaskId, 'completed', notes);
    closeCompleteModal();
}

function showCancelModal(taskId) {
    currentTaskId = taskId;
    document.getElementById('cancelNotes').value = '';
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    currentTaskId = null;
}

function confirmCancel() {
    const notes = document.getElementById('cancelNotes').value.trim();
    if (!notes) {
        alert('Please provide a reason for cancellation');
        return;
    }
    updateTaskStatus(currentTaskId, 'cancelled', notes);
    closeCancelModal();
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCompleteModal();
        closeCancelModal();
    }
});

// Close modals when clicking outside
document.getElementById('completeModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCompleteModal();
});

document.getElementById('cancelModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
</script>
