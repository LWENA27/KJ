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
                    <span class="text-xs text-green-600 font-medium"><?php echo count($tests); ?> Ready for Testing</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                    <span class="text-xs text-blue-600 font-medium">All tests are paid and ready</span>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/KJ/lab/tests" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-refresh mr-2"></i>Refresh
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Ready for Testing</p>
                    <p class="text-2xl font-bold"><?php echo count($tests); ?></p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-2">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Payment Status</p>
                    <p class="text-xl font-bold">All Paid</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-2">
                    <i class="fas fa-credit-card text-xl"></i>
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
                            <!-- Add payment status badge -->
                            <div class="mt-1">
                                <?php 
                                $stmt = $this->pdo->prepare("
                                    SELECT payment_status 
                                    FROM payments 
                                    WHERE visit_id = ? 
                                    AND payment_type = 'lab_test_fee' 
                                    AND item_id = ?
                                    ORDER BY payment_date DESC 
                                    LIMIT 1
                                ");
                                $stmt->execute([$test['visit_id'], $test['id']]);
                                $payment = $stmt->fetch();
                                
                                $badgeClass = $payment && $payment['payment_status'] === 'paid' 
                                    ? 'bg-green-100 text-green-800' 
                                    : 'bg-yellow-100 text-yellow-800';
                                $paymentStatus = $payment ? ucfirst($payment['payment_status']) : 'Pending';
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $badgeClass; ?>">
                                    <i class="fas <?php echo $payment && $payment['payment_status'] === 'paid' ? 'fa-check-circle' : 'fa-clock'; ?> mr-1"></i>
                                    <?php echo $paymentStatus; ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Paid & Ready
                            </span>
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
                                <button onclick="quickAddResult(<?php echo $test['id']; ?>, '<?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?>', '<?php echo htmlspecialchars($test['test_name']); ?>')" 
                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                    <i class="fas fa-clipboard-check mr-1"></i>Result
                                </button>
                                <a href="/KJ/lab/view_test/<?php echo $test['id']; ?>" 
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

// Quick Add Result function - opens a modal
function quickAddResult(testId, patientName, testName) {
    document.getElementById('quickResultTestId').value = testId;
    document.getElementById('quickResultPatientName').textContent = patientName;
    document.getElementById('quickResultTestName').textContent = testName;
    document.getElementById('quickResultModal').classList.remove('hidden');
    document.getElementById('quickResultModal').classList.add('flex');
}

function closeQuickResultModal() {
    document.getElementById('quickResultModal').classList.add('hidden');
    document.getElementById('quickResultModal').classList.remove('flex');
    document.getElementById('quickResultForm').reset();
}
</script>

<!-- Quick Add Result Modal -->
<div id="quickResultModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Quick Add Result</h3>
                <button onclick="closeQuickResultModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="quickResultForm" method="POST" action="/KJ/lab/add_result" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" id="quickResultTestId" name="test_order_id">
                
                <div class="bg-green-50 p-3 rounded-lg">
                    <p class="font-medium text-green-900">Patient: <span id="quickResultPatientName"></span></p>
                    <p class="text-sm text-green-700">Test: <span id="quickResultTestName"></span></p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Result Value *</label>
                        <input type="text" name="result_value" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <input type="text" name="unit" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="mg/dL, %, etc.">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="result_status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="normal">Normal</option>
                        <option value="abnormal">Abnormal</option>
                        <option value="borderline">Borderline</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                
                <input type="hidden" name="completion_time" id="quickCompletionTime">
                <input type="hidden" name="result_notes" value="Quick result entry">
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeQuickResultModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        <i class="fas fa-save mr-2"></i>Save Result
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Set current time when modal opens
document.getElementById('quickResultModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeQuickResultModal();
    }
});

// Auto-set completion time when form is submitted
document.getElementById('quickResultForm').addEventListener('submit', function() {
    document.getElementById('quickCompletionTime').value = new Date().toISOString().slice(0, 16);
});
</script>
