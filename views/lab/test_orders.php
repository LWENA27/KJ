<?php $title = "Lab Test Orders"; ?>
<?php include __DIR__ . '/../layouts/main.php'; ?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Lab Test Orders</h1>
        <a href="/lab/dashboard" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Test Orders Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Paid Test Orders Ready for Processing</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tests</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($test_orders)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                No pending test orders found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($test_orders as $order): ?>
                            <?php
                            $tests_requested = json_decode($order['tests_requested'], true);
                            $test_names = array_column($tests_requested, 'test_name');
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($order['phone']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Dr. <?php echo htmlspecialchars($order['doctor_first'] . ' ' . $order['doctor_last']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $priority_colors = [
                                        'normal' => 'bg-blue-100 text-blue-800',
                                        'urgent' => 'bg-yellow-100 text-yellow-800',
                                        'stat' => 'bg-red-100 text-red-800'
                                    ];
                                    $priority_class = $priority_colors[$order['priority']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $priority_class; ?>">
                                        <?php echo strtoupper($order['priority']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <?php echo htmlspecialchars(implode(', ', array_slice($test_names, 0, 2))); ?>
                                        <?php if (count($test_names) > 2): ?>
                                            <span class="text-gray-500">... +<?php echo count($test_names) - 2; ?> more</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    <?php echo number_format($order['total_amount'], 0); ?> TSH
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $status_colors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800'
                                    ];
                                    $status_class = $status_colors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="viewTestOrder(<?php echo $order['id']; ?>)" 
                                                class="text-blue-600 hover:text-blue-900" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <button onclick="startProcessing(<?php echo $order['id']; ?>)" 
                                                    class="text-green-600 hover:text-green-900" title="Start Processing">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($order['status'] === 'in_progress'): ?>
                                            <button onclick="enterResults(<?php echo $order['id']; ?>)" 
                                                    class="text-purple-600 hover:text-purple-900" title="Enter Results">
                                                <i class="fas fa-vial"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Test Order Details Modal -->
<div id="orderDetailsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Test Order Details</h3>
            <button onclick="closeOrderDetailsModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="orderDetailsContent">
            <!-- Order details will be loaded here -->
        </div>
    </div>
</div>

<!-- Start Processing Confirmation Modal -->
<div id="startProcessingModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Start Test Processing</h3>
            <button onclick="closeStartProcessingModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="/lab/start_test_processing" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="order_id" id="processingOrderId">
            
            <p class="text-gray-700">
                Are you ready to start processing this test order? This will mark the order as "In Progress" and assign it to you.
            </p>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeStartProcessingModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    <i class="fas fa-play mr-2"></i>Start Processing
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Test Results Entry Modal -->
<div id="resultsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Enter Test Results</h3>
            <button onclick="closeResultsModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="/lab/complete_test_order" class="space-y-6" id="resultsForm">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="order_id" id="resultsOrderId">
            
            <div id="testResultsFields">
                <!-- Test result fields will be dynamically generated here -->
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lab Notes</label>
                <textarea name="lab_notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Additional notes or observations (optional)"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeResultsModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                    <i class="fas fa-check mr-2"></i>Complete Tests
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentOrderData = null;

function viewTestOrder(orderId) {
    // In a real implementation, this would fetch order details via AJAX
    document.getElementById('orderDetailsModal').classList.remove('hidden');
}

function closeOrderDetailsModal() {
    document.getElementById('orderDetailsModal').classList.add('hidden');
}

function startProcessing(orderId) {
    document.getElementById('processingOrderId').value = orderId;
    document.getElementById('startProcessingModal').classList.remove('hidden');
}

function closeStartProcessingModal() {
    document.getElementById('startProcessingModal').classList.add('hidden');
}

function enterResults(orderId) {
    document.getElementById('resultsOrderId').value = orderId;
    
    // Find the order data from the table
    // In a real implementation, you would fetch this via AJAX
    const orderRow = document.querySelector(`tr:has(td:contains('#${String(orderId).padStart(6, '0')}')`);
    
    // Generate test result fields
    generateTestResultFields(orderId);
    
    document.getElementById('resultsModal').classList.remove('hidden');
}

function generateTestResultFields(orderId) {
    // This is a simplified version - in reality, you'd fetch the exact tests from the server
    const testFields = document.getElementById('testResultsFields');
    testFields.innerHTML = `
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Test Results</h4>
            <p class="text-sm text-gray-600 mb-4">Enter results for each test ordered. Use appropriate units and reference ranges.</p>
            
            <div id="dynamicTestFields">
                <!-- Test fields would be dynamically generated based on the actual order -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sample Test Result</label>
                        <input type="text" name="test_results[1]" placeholder="Enter result with units"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>
    `;
}

function closeResultsModal() {
    document.getElementById('resultsModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('orderDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) closeOrderDetailsModal();
});

document.getElementById('startProcessingModal').addEventListener('click', function(e) {
    if (e.target === this) closeStartProcessingModal();
});

document.getElementById('resultsModal').addEventListener('click', function(e) {
    if (e.target === this) closeResultsModal();
});
</script>
