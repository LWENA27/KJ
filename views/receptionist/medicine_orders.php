<?php $title = "Medicine Orders Management"; ?>
<?php include __DIR__ . '/../layouts/main.php'; ?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Medicine Orders Management</h1>
        <a href="/receptionist/dashboard" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
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

    <!-- Medicine Orders Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Pending Medicine Orders</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dispensing Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($medicine_orders)): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                No pending medicine orders found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($medicine_orders as $order): ?>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    <?php echo number_format($order['total_amount'], 0); ?> TSH
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo number_format($order['paid_amount'], 0); ?> TSH
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $payment_status_colors = [
                                        'pending' => 'bg-red-100 text-red-800',
                                        'partial' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800'
                                    ];
                                    $color_class = $payment_status_colors[$order['payment_status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $color_class; ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $dispensing_status_colors = [
                                        'pending' => 'bg-red-100 text-red-800',
                                        'partial' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800'
                                    ];
                                    $color_class = $dispensing_status_colors[$order['dispensing_status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $color_class; ?>">
                                        <?php echo ucfirst($order['dispensing_status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="viewMedicineOrder(<?php echo $order['id']; ?>)" 
                                                class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if ($order['payment_status'] !== 'completed'): ?>
                                            <button onclick="recordPayment(<?php echo $order['id']; ?>, <?php echo $order['total_amount'] - $order['paid_amount']; ?>)" 
                                                    class="text-green-600 hover:text-green-900" title="Record Payment">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($order['payment_status'] !== 'pending' && $order['dispensing_status'] !== 'completed'): ?>
                                            <button onclick="dispenseMedicines(<?php echo $order['id']; ?>)" 
                                                    class="text-purple-600 hover:text-purple-900" title="Dispense Medicines">
                                                <i class="fas fa-pills"></i>
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

<!-- Medicine Order Details Modal -->
<div id="orderDetailsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Medicine Order Details</h3>
            <button onclick="closeOrderDetailsModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="orderDetailsContent">
            <!-- Order details will be loaded here -->
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Record Payment</h3>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="/receptionist/process_medicine_payment" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="order_id" id="paymentOrderId">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Amount (TSH)</label>
                <input type="number" name="payment_amount" id="paymentAmount" step="0.01" min="0" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="cash">Cash</option>
                    <option value="mobile_money">Mobile Money</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closePaymentModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Dispensing Modal -->
<div id="dispensingModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Dispense Medicines</h3>
            <button onclick="closeDispensingModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form method="POST" action="/receptionist/dispense_medicines" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="order_id" id="dispensingOrderId">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dispensing Notes</label>
                <textarea name="dispensing_notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Notes about dispensing (optional)"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeDispensingModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                    <i class="fas fa-pills mr-2"></i>Dispense Medicines
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function viewMedicineOrder(orderId) {
    // In a real implementation, this would fetch order details via AJAX
    document.getElementById('orderDetailsModal').classList.remove('hidden');
}

function closeOrderDetailsModal() {
    document.getElementById('orderDetailsModal').classList.add('hidden');
}

function recordPayment(orderId, remainingAmount) {
    document.getElementById('paymentOrderId').value = orderId;
    document.getElementById('paymentAmount').value = remainingAmount;
    document.getElementById('paymentAmount').max = remainingAmount;
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function dispenseMedicines(orderId) {
    document.getElementById('dispensingOrderId').value = orderId;
    document.getElementById('dispensingModal').classList.remove('hidden');
}

function closeDispensingModal() {
    document.getElementById('dispensingModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('orderDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) closeOrderDetailsModal();
});

document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) closePaymentModal();
});

document.getElementById('dispensingModal').addEventListener('click', function(e) {
    if (e.target === this) closeDispensingModal();
});
</script>
