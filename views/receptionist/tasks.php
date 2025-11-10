<!-- Tasks header -->
<div class="bg-green-50 rounded-lg shadow-xl p-6 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center"><i class="fas fa-tasks mr-3 text-green-600"></i> My Tasks</h1>
            <p class="text-gray-600 mt-2">Tasks assigned to you. Process them here until completion.</p>
        </div>
        <div>
            <button id="refreshTasks" class="px-4 py-2 bg-white border rounded shadow hover:bg-gray-50">Refresh</button>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-medium text-gray-900">Assigned Tasks</h3>
    </div>

    <div id="tasksContainer" class="p-6">
        <?php if (empty($tasks)): ?>
            <div class="text-center py-16">
                <i class="fas fa-check-circle text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900">No tasks assigned</h3>
                <p class="text-gray-600">When tasks are assigned to you, they will appear here.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Patient</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Visit Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Task</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Assigned At</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($tasks as $i => $t): ?>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo $i + 1; ?></td>
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    <div class="font-medium"><?php echo htmlspecialchars($t['first_name'] . ' ' . $t['last_name']); ?></div>
                                    <div class="text-xs text-gray-500">Patient ID: #<?php echo $t['patient_id']; ?></div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo $t['visit_date'] ? date('M j, Y H:i', strtotime($t['visit_date'])) : 'N/A'; ?></td>
                                <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($t['workflow_step']); ?><br><span class="text-xs text-gray-500"><?php echo nl2br(htmlspecialchars($t['notes'] ?? '')); ?></span></td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo ($t['status'] === 'pending') ? 'bg-yellow-100 text-yellow-800' : (($t['status'] === 'in_progress') ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); ?>"><?php echo ucfirst($t['status']); ?></span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700"><?php echo $t['created_at'] ? date('M j, Y H:i', strtotime($t['created_at'])) : ''; ?></td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center gap-2">
                                        <?php if ($t['status'] !== 'in_progress'): ?>
                                            <button data-task-id="<?php echo $t['id']; ?>" data-action="start" class="start-btn px-3 py-1 bg-blue-600 text-white rounded text-sm">Start</button>
                                        <?php endif; ?>
                                        <?php if ($t['status'] !== 'completed'): ?>
                                            <button data-task-id="<?php echo $t['id']; ?>" data-action="complete" class="complete-btn px-3 py-1 bg-green-600 text-white rounded text-sm">Complete</button>
                                        <?php endif; ?>
                                        <a href="<?= htmlspecialchars($BASE_PATH) ?>/receptionist/view_patient?id=<?= $t['patient_id'] ?>" class="px-3 py-1 bg-gray-100 rounded text-sm text-gray-700">View</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrf = '<?php echo $csrf_token; ?>';

    function postStatus(taskId, status) {
        const notes = prompt('Optional note to add (leave empty to skip):', '');
        const fd = new FormData();
        fd.append('csrf_token', csrf);
        fd.append('task_id', taskId);
        fd.append('status', status);
        fd.append('notes', notes || '');

        fetch(window.location.origin + '/KJ/receptionist/update_task_status', {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
        }).then(r => r.json()).then(j => {
            if (j.ok) {
                location.reload();
            } else {
                alert('Failed to update task: ' + (j.error || 'unknown'));
            }
        }).catch(e => {
            console.error(e);
            alert('Request failed');
        });
    }

    document.querySelectorAll('.start-btn').forEach(b => {
        b.addEventListener('click', function() {
            const id = this.dataset.taskId;
            postStatus(id, 'in_progress');
        });
    });

    document.querySelectorAll('.complete-btn').forEach(b => {
        b.addEventListener('click', function() {
            const id = this.dataset.taskId;
            if (!confirm('Mark this task as completed?')) return;
            postStatus(id, 'completed');
        });
    });

    document.getElementById('refreshTasks')?.addEventListener('click', function() { location.reload(); });
});
</script>
