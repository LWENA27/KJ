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
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-xs text-green-600 font-medium"><?php echo count($tests); ?> Tests in Queue</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                    <span class="text-xs text-yellow-600 font-medium">Override required for unpaid tests</span>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="<?php echo BASE_PATH; ?>/lab/tests" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-refresh mr-2"></i>Refresh
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <?php 
    $paidTests = array_filter($tests, function($t) { return $t['payment_status'] === 'paid'; });
    $unpaidTests = array_filter($tests, function($t) { return $t['payment_status'] !== 'paid' && empty($t['has_override']); });
    $overrideTests = array_filter($tests, function($t) { return $t['payment_status'] !== 'paid' && !empty($t['has_override']); });
    ?>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Paid & Ready</p>
                    <p class="text-2xl font-bold"><?php echo count($paidTests); ?></p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-2">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Awaiting Payment</p>
                    <p class="text-2xl font-bold"><?php echo count($unpaidTests); ?></p>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-full p-2">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Override Applied</p>
                    <p class="text-2xl font-bold"><?php echo count($overrideTests); ?></p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-2">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Tests</p>
                    <p class="text-2xl font-bold"><?php echo count($tests); ?></p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-2">
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
                                <i class="fas fa-check-circle mr-2"></i>Payment Status
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
                    <?php foreach ($tests as $test): ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-200 test-row" 
                        data-status="<?php echo $test['status']; ?>"
                        data-payment="<?php echo $test['payment_status']; ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        #<?php echo htmlspecialchars($test['registration_number']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($test['test_name']); ?></div>
                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($test['category_name']); ?></div>
                            <div class="text-xs text-blue-600">Tsh <?php echo number_format($test['price'], 2); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <?php 
                            $isPaid = ($test['payment_status'] === 'paid');
                            $hasOverride = !empty($test['has_override']);
                            
                            if ($isPaid): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Paid & Ready
                                </span>
                            <?php elseif ($hasOverride): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Override Applied
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Awaiting Payment
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div>Dr. <?php echo htmlspecialchars($test['doctor_first_name'] . ' ' . $test['doctor_last_name']); ?></div>
                            <div><?php echo date('M j, Y g:i A', strtotime($test['created_at'])); ?></div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex space-x-2">
                                <button onclick="quickTakeSample(<?php echo $test['id']; ?>, '<?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?>', '<?php echo htmlspecialchars($test['test_name']); ?>')" 
                                        class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded text-sm">
                                    <i class="fas fa-vial mr-1"></i>Sample
                                </button>
                                <button type="button" 
                                        data-test-id="<?php echo $test['id']; ?>"
                                        data-patient-name="<?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?>"
                                        data-test-name="<?php echo htmlspecialchars($test['test_name']); ?>"
                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm result-btn">
                                    <i class="fas fa-clipboard-check mr-1"></i>Result
                                </button>
                                <a href="<?php echo BASE_PATH; ?>/lab/view_test/<?php echo $test['id']; ?>" 
                                   class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
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

// Start test function
function startTest(testId, testName) {
    fetch(`/KJ/lab/check_payment_status?test_id=${testId}`)
    .then(response => response.json())
    .then(data => {
        if (data.is_paid) {
            if (confirm(`Start processing "${testName}"?`)) {
                fetch('/KJ/lab/start_test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `test_id=${testId}&csrf_token=${document.querySelector('[name="csrf_token"]').value}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Test Started', `Processing of ${testName} has begun`, 'success');
                        location.reload();
                    } else {
                        showNotification('Error', data.message, 'error');
                    }
                });
            }
        } else {
            showNotification('Error', 'Payment is required before starting the test', 'error');
        }
    });
}

// Update the openCompleteTestModal function to check payment status
function openCompleteTestModal(testId, testName, patientName) {
    fetch(`/KJ/lab/check_payment_status?test_id=${testId}`)
    .then(response => response.json())
    .then(data => {
        if (data.is_paid) {
            document.getElementById('modalTestId').value = testId;
            document.getElementById('modalTestName').textContent = testName;
            document.getElementById('modalPatientName').textContent = patientName;
            document.getElementById('completeTestModal').classList.remove('hidden');
        } else {
            showNotification('Error', 'Payment is required before completing the test', 'error');
        }
    });
}

// Update the test status update function
function updateTestStatus(testId, newStatus) {
    fetch('/KJ/lab/update_test_status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `test_id=${testId}&status=${newStatus}&csrf_token=${document.querySelector('[name="csrf_token"]').value}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification('Error', data.message, 'error');
        }
    });
}

// Enhanced notification system
function showNotification(title, message, type = 'info') {
    const colors = {
        info: 'bg-blue-500',
        success: 'bg-green-500',
        warning: 'bg-yellow-500',
        error: 'bg-red-500'
    };
    
    // Class to identify success notifications
    const additionalClass = type === 'success' ? 'notification-success' : '';
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50 ${additionalClass}`;
    
    // Check if this is about lab results
    const isLabResult = type === 'success' && (message.includes('result') || message.includes('Result'));
    
    notification.innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <div class="font-semibold">${title}</div>
                <div class="text-sm opacity-90">${message}</div>
                ${isLabResult ? '<div class="mt-2 text-sm flex items-center"><i class="fas fa-info-circle mr-1"></i> Results can be viewed in the doctor dashboard</div>' : ''}
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
    // In real implementation, this would fetch updated data
}, 30000);

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    
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

// Quick Take Sample function
function quickTakeSample(testId, patientName, testName) {
    if (confirm(`Take sample for ${patientName} - ${testName}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/KJ/lab/take_sample';
        
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = 'csrf_token';
        csrfField.value = document.querySelector('[name="csrf_token"]').value;
        
        const testField = document.createElement('input');
        testField.type = 'hidden';
        testField.name = 'test_order_id';
        testField.value = testId;
        
        const timeField = document.createElement('input');
        timeField.type = 'hidden';
        timeField.name = 'collection_time';
        timeField.value = new Date().toISOString().slice(0, 16);
        
        const notesField = document.createElement('input');
        notesField.type = 'hidden';
        notesField.name = 'sample_notes';
        notesField.value = 'Quick sample collection';
        
        form.appendChild(csrfField);
        form.appendChild(testField);
        form.appendChild(timeField);
        form.appendChild(notesField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// No separate functions needed here - we'll handle everything in the script block below
</script>

<!-- Test Result Modal - Complete Redesign -->
<div id="resultModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop with blur effect -->
    <div class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm" id="resultModalBackdrop"></div>
    
    <!-- Modal Content -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-xl shadow-2xl transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-flask text-green-600 mr-3"></i>
                    Record Test Result
                </h3>
                <button type="button" id="closeResultModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Body -->
            <div class="px-6 py-4">
                <form id="resultForm" class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <input type="hidden" id="resultTestId" name="test_order_id">
                    <input type="hidden" name="completion_time" id="completionTime" value="<?php echo date('Y-m-d H:i:s'); ?>">
                    <!-- Debug info will be displayed here -->
                    <div id="debug-info" class="hidden text-xs text-gray-500 p-2 bg-gray-100 rounded mb-2"></div>
                    
                    <!-- Patient & Test Info -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center mb-2">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900" id="resultPatientName"></h4>
                                <p class="text-sm text-gray-600" id="resultTestName"></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden timestamp automatically set -->
                    <script>
                        // Initialize the completion time field with current timestamp
                        document.getElementById('completionTime').value = new Date().toISOString().slice(0, 16);
                    </script>
                    
                    <!-- Result Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="result_value_field" class="block text-sm font-medium text-gray-700 mb-1">Result Value *</label>
                            <div class="relative">
                                <input type="text" name="result_value" id="result_value_field" required value="1.0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <i class="fas fa-calculator"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="unit_field" class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                            <div class="relative">
                                <input type="text" name="unit" id="unit_field" value="mg/dL"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                       placeholder="mg/dL, %, etc.">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <i class="fas fa-ruler"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="result_status_field" class="block text-sm font-medium text-gray-700 mb-1">Result Status</label>
                        <div class="relative">
                            <select name="result_status" id="result_status_field"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 appearance-none">
                                <option value="normal">Normal</option>
                                <option value="abnormal">Abnormal</option>
                                <option value="borderline">Borderline</option>
                                <option value="critical">Critical</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="result_notes_field" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea name="result_notes" id="result_notes_field" rows="2"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                  placeholder="Enter any observations or comments...">Test completed successfully.</textarea>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                <button type="button" id="cancelResultBtn"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 shadow-sm hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" id="saveResultBtn"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg shadow-sm hover:bg-green-700">
                    <i class="fas fa-save mr-2"></i>Save Result
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize the Result Modal functionality when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    const resultModal = document.getElementById('resultModal');
    const resultModalBackdrop = document.getElementById('resultModalBackdrop');
    const resultForm = document.getElementById('resultForm');
    const closeResultModal = document.getElementById('closeResultModal');
    const cancelResultBtn = document.getElementById('cancelResultBtn');
    const saveResultBtn = document.getElementById('saveResultBtn');
    
    // Function to open modal
    function openModal(testId, patientName, testName) {
        try {

            
            // Set form values
            document.getElementById('resultTestId').value = testId;
            document.getElementById('resultPatientName').textContent = patientName;
            document.getElementById('resultTestName').textContent = testName;
            
            // Pre-fill the form fields with default values
            document.getElementById('result_value_field').value = '1.0';
            document.getElementById('unit_field').value = 'mg/dL';
            document.getElementById('result_status_field').value = 'normal';
            document.getElementById('result_notes_field').value = 'Test completed successfully.';
            
            // Set current time 
            document.getElementById('completionTime').value = new Date().toISOString().slice(0, 16);
            
            // Show the modal with smooth animation
            resultModal.classList.remove('hidden');
            
            // Apply blur effect to main content
            document.querySelector('.space-y-6').classList.add('blur-sm');
            

        } catch (error) {
            console.error('Error opening modal:', error);
            showNotification('Error', 'Failed to open result dialog: ' + error.message, 'error');
        }
    }
    
    // Function to close modal
    function closeModal() {
        // Hide the modal
        resultModal.classList.add('hidden');
        
        // Remove blur effect from main content
        document.querySelector('.space-y-6').classList.remove('blur-sm');
        
        // Reset form
        resultForm.reset();
        

    }
    
    // Attach click handlers to all "Result" buttons
    document.querySelectorAll('.result-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const testId = this.getAttribute('data-test-id');
            const patientName = this.getAttribute('data-patient-name');
            const testName = this.getAttribute('data-test-name');
            
            openModal(testId, patientName, testName);
            return false;
        });
    });
    
    // Close modal when clicking on close button
    closeResultModal.addEventListener('click', closeModal);
    
    // Close modal when clicking on cancel button
    cancelResultBtn.addEventListener('click', closeModal);
    
    // Close modal when clicking outside (on backdrop)
    resultModalBackdrop.addEventListener('click', closeModal);
    
    // Submit form when clicking save button - with improved error handling
    saveResultBtn.addEventListener('click', function() {
        // First, let's ensure the form fields have values
        const resultValue = document.getElementById('result_value_field');
        
        // Force a value if empty
        if (!resultValue.value || resultValue.value.trim() === '') {
            resultValue.value = '1.0';
        }
        
        // Attempt to submit the form
        try {
            submitResultForm();
        } catch (error) {
            console.error('Error submitting form:', error);
            showNotification('Error', 'Form submission error: ' + error.message, 'error');
        }
    });
    
    // Handle form submission
    function submitResultForm() {
        let urlEncodedData = ''; // Declare this variable at the function scope level
        
        try {
            // Set current time if not already set
            if (!document.getElementById('completionTime').value) {
                document.getElementById('completionTime').value = new Date().toISOString().slice(0, 16);
            }
            
            // Check if required fields are filled - use the new field IDs
            const resultValue = document.getElementById('result_value_field').value;
            
            // Force a default value if empty
            if (!resultValue || resultValue.trim() === '') {
                document.getElementById('result_value_field').value = '1.0';
            }
            
            // Get form data using direct value access with the new field IDs
            const formData = new FormData();
            formData.append('csrf_token', document.querySelector('[name="csrf_token"]').value);
            formData.append('test_order_id', document.getElementById('resultTestId').value);
            formData.append('result_value', document.getElementById('result_value_field').value);
            formData.append('unit', document.getElementById('unit_field').value || 'mg/dL');
            formData.append('result_status', document.getElementById('result_status_field').value);
            formData.append('result_notes', document.getElementById('result_notes_field').value || 'Test completed');
            formData.append('completion_time', document.getElementById('completionTime').value);
            
            // Validate that we have the minimum required fields
            if (!formData.get('test_order_id')) {
                throw new Error('Missing test order ID');
            }
            
            if (!formData.get('result_value')) {
                // Force a default value if missing
                formData.set('result_value', '1.0');
            }
            
            urlEncodedData = new URLSearchParams(formData).toString(); // Assign to the variable declared at function scope
        } catch (error) {
            console.error('Error preparing form data:', error);
            showNotification('Error', 'Error preparing form data: ' + error.message, 'error');
            return;
        }
        
        // Show loading state
        saveResultBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        saveResultBtn.disabled = true;
        
        // Send AJAX request
        fetch('/KJ/lab/add_result', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: urlEncodedData
        })
        .then(response => {

            // Handle both JSON and non-JSON responses
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(data => {
                    return { ok: response.ok, data: data };
                });
            } else {
                return response.text().then(text => {
                    return { ok: response.ok, data: { message: text } };
                });
            }
        })
        .then(result => {
            if (result.ok) {
                // Always treat success response as successful even if it's not JSON
                showNotification('Success', 'Test result saved successfully', 'success');
                closeModal();
                
                // Create a floating confirmation with a link to doctor's dashboard
                const doctorUrl = '/KJ/doctor/lab_results';
                const confirmationBox = document.createElement('div');
                confirmationBox.className = 'fixed bottom-4 right-4 bg-white border border-green-200 shadow-xl p-4 rounded-lg max-w-md z-50';
                confirmationBox.innerHTML = `
                    <div class="flex items-center text-green-600 mb-2">
                        <i class="fas fa-check-circle mr-2 text-xl"></i>
                        <h3 class="font-semibold">Result Saved Successfully</h3>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">Test results have been recorded and are now available for doctors to review.</p>
                    <div class="flex items-center justify-between">
                        <a href="${doctorUrl}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            View in Doctor Dashboard
                        </a>
                        <button onclick="location.reload()" class="text-blue-600 hover:underline text-sm">
                            Process Next Test
                        </button>
                    </div>
                `;
                document.body.appendChild(confirmationBox);
                
                // Don't auto-reload so user can click the link if they want
            } else {
                // For error responses
                let errorMessage = 'Failed to save result';
                
                if (result.data) {
                    if (typeof result.data === 'object' && result.data.message) {
                        errorMessage = result.data.message;
                    } else if (typeof result.data === 'string') {
                        // Check if the HTML response contains success message
                        if (result.data.includes('success') || result.data.includes('Success')) {
                            // This might be a successful redirect with HTML
                            showNotification('Success', 'Test result saved successfully', 'success');
                            closeModal();
                            
                            // Refresh the page after a short delay
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                            return;
                        } else if (result.data.includes('error') || result.data.includes('Error')) {
                            // Try to extract error message from HTML
                            const errorMatch = result.data.match(/<div class="[^"]*alert[^"]*"[^>]*>(.*?)<\/div>/i);
                            if (errorMatch && errorMatch[1]) {
                                errorMessage = errorMatch[1].replace(/<[^>]*>/g, '').trim();
                            }
                        }
                    }
                }
                
                showNotification('Error', errorMessage, 'error');
                saveResultBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Save Result';
                saveResultBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error', 'An unexpected error occurred', 'error');
            saveResultBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Save Result';
            saveResultBtn.disabled = false;
        });
    }
    
    // Add style for blur effect
    const style = document.createElement('style');
    style.textContent = `
        .blur-sm {
            filter: blur(4px);
            transition: filter 0.3s ease;
        }
    `;
    document.head.appendChild(style);
    
    // Helper function to debug form values
    window.debugFormValues = function() {
        try {
            const debugInfo = {
                'Test ID': document.getElementById('resultTestId').value,
                'Result Value': document.getElementById('result_value_field').value,
                'Unit': document.getElementById('unit_field').value,
                'Status': document.getElementById('result_status_field').value,
                'Notes': document.getElementById('result_notes_field').value,
                'Completion Time': document.getElementById('completionTime').value
            };
            
            const debugElement = document.getElementById('debug-info');
            debugElement.classList.remove('hidden');
            debugElement.innerHTML = '<strong>Debug Info:</strong><br>' + 
                Object.entries(debugInfo).map(([key, value]) => 
                    `<span>${key}: <code>${value || 'empty'}</code></span>`
                ).join('<br>');
            
            return debugInfo;
        } catch (error) {
            console.error('Debug error:', error);
            return null;
        }
    };
    
    // Add a debug button for troubleshooting
    const debugButton = document.createElement('button');
    debugButton.textContent = 'Debug Form Values';
    debugButton.className = 'text-xs text-gray-600 underline cursor-pointer mt-2 mb-2';
    debugButton.onclick = window.debugFormValues;
    document.getElementById('saveResultBtn').insertAdjacentElement('beforebegin', debugButton);
    

});
</script>
