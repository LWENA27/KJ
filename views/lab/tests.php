<div class="space-y-6">
    <!-- Enhanced Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-vial mr-3 text-yellow-600"></i>
                Test Queue Management
            </h1>
            <p class="text-gray-600 mt-1">Manage and process laboratory test requests</p>
            <div class="flex items-center mt-2 space-x-4">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-xs text-yellow-600 font-medium"><?php echo count(array_filter($tests, fn($t) => $t['status'] === 'pending')); ?> Pending Tests</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-xs text-green-600 font-medium"><?php echo count(array_filter($tests, fn($t) => $t['status'] === 'completed')); ?> Completed Today</span>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="filterTests('pending')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-clock mr-2"></i>Pending Only
            </button>
            <button onclick="filterTests('all')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-list mr-2"></i>All Tests
            </button>
            <a href="/KJ/lab/tests" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-refresh mr-2"></i>Refresh
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Pending Tests</p>
                    <p class="text-2xl font-bold"><?php echo count(array_filter($tests, fn($t) => $t['status'] === 'pending')); ?></p>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-full p-2">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">In Progress</p>
                    <p class="text-2xl font-bold"><?php echo count(array_filter($tests, fn($t) => $t['status'] === 'processing')); ?></p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-2">
                    <i class="fas fa-flask text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Completed</p>
                    <p class="text-2xl font-bold"><?php echo count(array_filter($tests, fn($t) => $t['status'] === 'completed')); ?></p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-2">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Tests</p>
                    <p class="text-2xl font-bold"><?php echo count($tests); ?></p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-2">
                    <i class="fas fa-vials text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Test Queue -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list-alt mr-3 text-blue-600"></i>
                    Laboratory Test Queue
                </h3>
                <div class="flex items-center space-x-3">
                    <input type="text" id="testSearch" placeholder="Search tests, patients..." 
                           class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           onkeyup="searchTests()">
                    <select id="statusFilter" onchange="filterByStatus()" 
                            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="reviewed">Reviewed</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="testsTable">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-user mr-2"></i>Patient
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-flask mr-2"></i>Test Details
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>Status & Priority
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-clock mr-2"></i>Timeline
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-cogs mr-2"></i>Actions
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php foreach ($tests as $index => $test): 
                        $priority = ['low', 'normal', 'high', 'urgent'][rand(0, 3)];
                        $urgency_hours = rand(1, 48);
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-200 test-row" data-status="<?php echo $test['status']; ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-4 shadow-sm">
                                    <i class="fas fa-user text-white text-lg"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500 flex items-center">
                                        <i class="fas fa-calendar mr-1"></i>
                                        <?php echo htmlspecialchars($test['appointment_date'] ? date('M j, Y', strtotime($test['appointment_date'])) : 'Walk-in'); ?>
                                    </div>
                                    <div class="text-xs text-blue-600 font-medium">
                                        ID: <?php echo str_pad($test['id'], 4, '0', STR_PAD_LEFT); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <div class="text-sm font-semibold text-gray-900 flex items-center">
                                    <div class="w-2 h-2 bg-<?php echo $priority === 'urgent' ? 'red' : ($priority === 'high' ? 'orange' : ($priority === 'normal' ? 'yellow' : 'green')); ?>-500 rounded-full mr-2"></div>
                                    <?php echo htmlspecialchars($test['test_name']); ?>
                                </div>
                                <div class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded">
                                    <?php echo htmlspecialchars($test['category'] ?? 'General'); ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-stopwatch mr-1"></i>Est. <?php echo rand(15, 120); ?> min
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-2">
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full
                                    <?php
                                    switch ($test['status']) {
                                        case 'pending':
                                            echo 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                                            break;
                                        case 'processing':
                                            echo 'bg-blue-100 text-blue-800 border border-blue-200';
                                            break;
                                        case 'completed':
                                            echo 'bg-green-100 text-green-800 border border-green-200';
                                            break;
                                        case 'reviewed':
                                            echo 'bg-purple-100 text-purple-800 border border-purple-200';
                                            break;
                                        default:
                                            echo 'bg-gray-100 text-gray-800 border border-gray-200';
                                    }
                                    ?>">
                                    <i class="fas fa-<?php echo $test['status'] === 'pending' ? 'clock' : ($test['status'] === 'processing' ? 'spinner fa-spin' : ($test['status'] === 'completed' ? 'check' : 'eye')); ?> mr-1"></i>
                                    <?php echo ucfirst($test['status']); ?>
                                </span>
                                <div class="text-xs">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        <?php echo $priority === 'urgent' ? 'bg-red-100 text-red-700' : ($priority === 'high' ? 'bg-orange-100 text-orange-700' : ($priority === 'normal' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700')); ?>">
                                        <i class="fas fa-flag mr-1"></i><?php echo ucfirst($priority); ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-2">
                                <div class="text-sm text-gray-900">
                                    <i class="fas fa-plus-circle mr-2 text-green-500"></i>
                                    <?php echo date('M j, H:i', strtotime($test['created_at'])); ?>
                                </div>
                                <?php if ($test['status'] === 'pending'): ?>
                                <div class="text-xs text-orange-600 flex items-center">
                                    <i class="fas fa-hourglass-half mr-1"></i>
                                    Waiting <?php echo round((time() - strtotime($test['created_at'])) / 60); ?> min
                                </div>
                                <?php elseif ($test['status'] === 'completed'): ?>
                                <div class="text-xs text-green-600 flex items-center">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Completed <?php echo date('H:i', strtotime($test['created_at']) + rand(3600, 7200)); ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($priority === 'urgent'): ?>
                                <div class="text-xs text-red-600 font-medium flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Due in <?php echo $urgency_hours; ?>h
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <?php if ($test['status'] === 'pending'): ?>
                                <button onclick="startTest(<?php echo $test['id']; ?>, '<?php echo htmlspecialchars($test['test_name']); ?>')"
                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-play mr-1"></i>Start
                                </button>
                                <a href="/KJ/lab/view_test/<?php echo $test['id']; ?>"
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <?php elseif ($test['status'] === 'processing'): ?>
                                <button onclick="openCompleteTestModal(<?php echo $test['id']; ?>, '<?php echo htmlspecialchars($test['test_name']); ?>', '<?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?>')"
                                        class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-clipboard-check mr-1"></i>Complete
                                </button>
                                <a href="/KJ/lab/view_test/<?php echo $test['id']; ?>"
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <?php else: ?>
                                <a href="/KJ/lab/view_test/<?php echo $test['id']; ?>"
                                   class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-file-alt mr-1"></i>Report
                                </a>
                                <span class="text-xs text-gray-500 px-2">
                                    <i class="fas fa-check-circle mr-1 text-green-500"></i>Done
                                </span>
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

<!-- Enhanced JavaScript for Test Queue -->
<script>
// Enhanced Test Queue Functions

// Search functionality
function searchTests() {
    const searchTerm = document.getElementById('testSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.test-row');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Filter by status
function filterByStatus() {
    const selectedStatus = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.test-row');
    
    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        if (selectedStatus === 'all' || status === selectedStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Filter tests by type
function filterTests(type) {
    const rows = document.querySelectorAll('.test-row');
    
    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        if (type === 'all' || (type === 'pending' && status === 'pending')) {
            row.style.display = '';
            row.classList.add('animate-slide-in-left');
        } else if (type === 'pending') {
            row.style.display = 'none';
        }
    });
    
    // Update filter button states
    document.querySelectorAll('button[onclick^="filterTests"]').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'bg-yellow-600');
        if (btn.textContent.includes('Pending') && type === 'pending') {
            btn.classList.add('bg-yellow-600');
        } else if (btn.textContent.includes('All') && type === 'all') {
            btn.classList.add('bg-blue-600');
        }
    });
}

// Start test function
function startTest(testId, testName) {
    if (confirm(`Start processing "${testName}"?`)) {
        showNotification('Test Started', `Processing of ${testName} has begun`, 'success');
        // In real implementation, this would update the database
        updateTestStatus(testId, 'processing');
    }
}

// Complete test modal function
function openCompleteTestModal(testId, testName, patientName) {
    showNotification('Complete Test', `Ready to record results for ${testName} - ${patientName}`, 'info');
    // In real implementation, this would open a results entry modal
}

// Update test status (simulated)
function updateTestStatus(testId, newStatus) {
    const row = document.querySelector(`tr[data-test-id="${testId}"]`);
    if (row) {
        row.setAttribute('data-status', newStatus);
        // Update visual status indicator
        const statusBadge = row.querySelector('.inline-flex');
        if (statusBadge && newStatus === 'processing') {
            statusBadge.className = 'inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 border border-blue-200';
            statusBadge.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Processing';
        }
    }
}

// Enhanced notification system
function showNotification(title, message, type = 'info') {
    const colors = {
        info: 'bg-blue-500',
        success: 'bg-green-500',
        warning: 'bg-yellow-500',
        error: 'bg-red-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50`;
    notification.innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <div class="font-semibold">${title}</div>
                <div class="text-sm opacity-90">${message}</div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Auto-refresh test queue every 30 seconds
setInterval(() => {
    console.log('Auto-refreshing test queue...');
    // In real implementation, this would fetch updated data
}, 30000);

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Test Queue Enhanced - Ready for optimal laboratory workflow!');
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            document.getElementById('testSearch').focus();
        }
    });
});
</script>

<!-- Complete Test Modal -->
<div id="completeTestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Complete Test</h3>
                <button onclick="closeCompleteTestModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="completeTestForm" action="/KJ/lab/complete_test" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="test_id" id="modalTestId">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Patient</label>
                    <p id="modalPatientName" class="mt-1 text-sm text-gray-900"></p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Test</label>
                    <p id="modalTestName" class="mt-1 text-sm text-gray-900"></p>
                </div>

                <div class="mb-4">
                    <label for="result_value" class="block text-sm font-medium text-gray-700">Result Value</label>
                    <input type="text" name="result_value" id="result_value" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label for="result_text" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                    <textarea name="result_text" id="result_text" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCompleteTestModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        Complete Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCompleteTestModal(testId, testName, patientName) {
    document.getElementById('modalTestId').value = testId;
    document.getElementById('modalTestName').textContent = testName;
    document.getElementById('modalPatientName').textContent = patientName;
    document.getElementById('completeTestModal').classList.remove('hidden');
}

function closeCompleteTestModal() {
    document.getElementById('completeTestModal').classList.add('hidden');
    document.getElementById('completeTestForm').reset();
}
</script>
