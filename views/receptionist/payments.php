<?php $title = "Pending Payments"; ?>

<!-- Force-hide the Amount Already Paid container to prevent other JS from showing it -->
<style>
    /* Keep the amount-paid panel hidden regardless of JS changes */
    #modal_amount_paid_container { display: none !important; }

    /* Ensure the payment modal overlays the page header and other content */
    #paymentModal { z-index: 99999 !important; }
    /* Ensure the modal dialog content creates its own stacking context above overlay */
    #paymentModal > .bg-white { position: relative; z-index: 100000; }
</style>

<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="bg-black rounded-lg shadow-xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="text-black">
                <h1 class="text-3xl font-bold flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-black-200"></i>
                    Pending Payments
                </h1>
                <p class="text-black-100 mt-2 text-lg">Process payments for lab tests and medicines</p>
            </div>
            <div class="flex space-x-3">
                <a href="/KJ/receptionist/payment_history" 
                   class="bg-white text-purple-700 hover:bg-purple-50 px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-lg flex items-center">
                    <i class="fas fa-history mr-2"></i>
                    Payment History
                </a>
                <a href="/KJ/receptionist/dashboard" 
                   class="bg-black-800 text-black hover:bg-purple-900 px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-lg flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <!-- Pending Lab Tests -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending Lab Tests</p>
                    <p class="text-3xl font-bold text-red-600"><?php echo count($pending_lab_payments); ?></p>
                    <p class="text-sm text-gray-500 mt-1">
                        Tsh <?php 
                        // Sum of remaining amounts for lab tests
                        $lab_total_pending = array_sum(array_column($pending_lab_payments, 'remaining_amount_to_pay')); 
                        echo number_format($lab_total_pending, 0);
                        ?>
                    </p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-vial text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Medicines -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending Medicines</p>
                    <p class="text-3xl font-bold text-orange-600"><?php echo count($pending_medicine_payments); ?></p>
                    <p class="text-sm text-gray-500 mt-1">
                        Tsh <?php 
                        // Sum of remaining amounts for medicines
                        $med_total_pending = array_sum(array_column($pending_medicine_payments, 'remaining_amount_to_pay')); 
                        echo number_format($med_total_pending, 0);
                        ?>
                    </p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-pills text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Pending Amount -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Pending</p>
                    <p class="text-3xl font-bold text-purple-600">
                        <?php echo count($pending_lab_payments) + count($pending_medicine_payments); ?>
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        Tsh <?php echo number_format($lab_total_pending + $med_total_pending, 0); ?>
                    </p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-money-check-alt text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($pending_lab_payments) && empty($pending_medicine_payments)): ?>
        <!-- No Pending Payments Message -->
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-4xl"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">All Caught Up!</h3>
            <p class="text-gray-600 text-lg">There are no pending payments at the moment.</p>
            <p class="text-gray-500 mt-2">New payments will appear here when doctors order lab tests or prescribe medicines.</p>
            <div class="mt-6">
                <a href="/KJ/receptionist/payment_history" 
                   class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-history mr-2"></i>
                    View Payment History
                </a>
            </div>
        </div>
    <?php else: ?>

        <!-- Pending Lab Test Payments -->
        <?php if (!empty($pending_lab_payments)): ?>
        <div class="bg-white rounded-lg shadow-lg mb-6 overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-vial mr-3"></i>
                        Pending Lab Test Payments
                    </h2>
                    <span class="bg-white text-red-600 px-3 py-1 rounded-full text-sm font-semibold">
                        <?php echo count($pending_lab_payments); ?> pending
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tests Ordered
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Visit Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount Due
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($pending_lab_payments as $payment): ?>
                            <tr class="hover:bg-red-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-red-600 flex items-center justify-center text-white font-semibold">
                                                <?php echo strtoupper(substr($payment['first_name'], 0, 1) . substr($payment['last_name'], 0, 1)); ?>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Reg: <?php echo htmlspecialchars($payment['registration_number']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <?php echo htmlspecialchars($payment['test_names']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo date('M d, Y', strtotime($payment['visit_date'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-red-600">
                                        Tsh <?php echo number_format($payment['remaining_amount_to_pay'], 0); ?>
                                    </div>
                                    <?php if ($payment['amount_already_paid'] > 0): ?>
                                        <div class="text-xs text-gray-500 mt-1">
                                            (Paid: Tsh <?php echo number_format($payment['amount_already_paid'], 0); ?> / Total: Tsh <?php echo number_format($payment['total_amount'], 0); ?>)
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openPaymentModal(
                                                <?php echo $payment['patient_id']; ?>, 
                                                <?php echo $payment['visit_id']; ?>, 
                                                'lab_test', 
                                                <?php echo $payment['total_amount']; ?>, 
                                                <?php echo $payment['amount_already_paid']; ?>, 
                                                '<?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name'], ENT_QUOTES); ?>', 
                                                <?php echo $payment['order_id']; ?>, 
                                                'lab_order'
                                            )"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                        <i class="fas fa-credit-card mr-2"></i>
                                        Record Payment
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pending Medicine Payments -->
        <?php if (!empty($pending_medicine_payments)): ?>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-pills mr-3"></i>
                        Pending Medicine Payments
                    </h2>
                    <span class="bg-white text-orange-600 px-3 py-1 rounded-full text-sm font-semibold">
                        <?php echo count($pending_medicine_payments); ?> pending
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Medicines Prescribed
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Visit Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount Due
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($pending_medicine_payments as $payment): ?>
                            <tr class="hover:bg-orange-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-orange-600 flex items-center justify-center text-white font-semibold">
                                                <?php echo strtoupper(substr($payment['first_name'], 0, 1) . substr($payment['last_name'], 0, 1)); ?>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Reg: <?php echo htmlspecialchars($payment['registration_number']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <?php echo htmlspecialchars($payment['medicine_names']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo date('M d, Y', strtotime($payment['visit_date'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-orange-600">
                                        Tsh <?php echo number_format($payment['remaining_amount_to_pay'], 0); ?>
                                    </div>
                                    <?php if ($payment['amount_already_paid'] > 0): ?>
                                        <div class="text-xs text-gray-500 mt-1">
                                            (Paid: Tsh <?php echo number_format($payment['amount_already_paid'], 0); ?> / Total: Tsh <?php echo number_format($payment['total_cost_of_prescription'], 0); ?>)
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openPaymentModal(
                                                <?php echo $payment['patient_id']; ?>, 
                                                <?php echo $payment['visit_id']; ?>, 
                                                'medicine', 
                                                <?php echo $payment['total_cost_of_prescription']; ?>, 
                                                <?php echo $payment['amount_already_paid']; ?>, 
                                                '<?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name'], ENT_QUOTES); ?>', 
                                                <?php echo $payment['prescription_id']; ?>, 
                                                'prescription'
                                            )"
                                            class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors">
                                        <i class="fas fa-credit-card mr-2"></i>
                                        Record Payment
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<!-- Payment Recording Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full m-4 transform transition-all">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 rounded-t-lg">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-cash-register mr-3"></i>
                Record Payment
            </h3>
        </div>

        <form id="paymentForm" method="POST" action="/KJ/receptionist/record_payment" class="p-6">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" id="modal_patient_id" name="patient_id">
            <input type="hidden" id="modal_visit_id" name="visit_id">
            <input type="hidden" id="modal_payment_type" name="payment_type">
            <!-- This hidden input will now store the actual amount entered by the receptionist -->
            <input type="hidden" id="modal_amount_hidden" name="amount"> 
            <input type="hidden" id="modal_item_id" name="item_id">
            <input type="hidden" id="modal_item_type" name="item_type">

            <div class="space-y-4">
                <!-- Patient Name Display -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Patient</label>
                    <p id="modal_patient_name" class="text-lg font-semibold text-gray-900"></p>
                </div>

                <!-- Payment Type Display -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment For</label>
                    <p id="modal_payment_type_display" class="text-lg font-semibold text-gray-900"></p>
                </div>

                <!-- New: Total Prescription Cost (visible for medicine payments) -->
                <div id="modal_total_cost_container" class="bg-blue-50 rounded-lg p-4 border-2 border-blue-200 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1 hidden">Total Prescription Cost</label>
                    <p id="modal_total_cost_display" class="text-xl font-bold text-blue-600 hidden"></p>
                </div>

                <!-- New: Amount Already Paid (kept hidden) -->
                <div id="modal_amount_paid_container" class="hidden bg-green-50 rounded-lg p-4 border-2 border-green-200">
                    <label class="hidden block text-sm font-medium text-gray-700 mb-1">Amount Already Paid</label>
                    <p id="modal_amount_paid_display" class="text-xl font-bold text-green-600"></p>
                </div>

                <!-- Remaining Balance Display -->
                <div id="modal_remaining_balance_container" class="bg-purple-50 rounded-lg p-4 border-2 border-purple-200">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remaining Balance</label>
                    <p id="modal_remaining_balance_display" class="text-2xl font-bold text-purple-600"></p>
                </div>

                <!-- Amount to Pay Input Field -->
                <div>
                    <label for="amount_to_pay_input" class="block text-sm font-medium text-gray-700 mb-2">
                        Amount Patient is Paying <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="amount_to_pay_input" name="amount_to_pay_input"
                           min="0" step="any" required
                           placeholder="Enter amount patient is paying"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           oninput="document.getElementById('modal_amount_hidden').value = this.value;">
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <select id="payment_method" name="payment_method" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Select payment method</option>
                        <option value="cash">💵 Cash</option>
                        <option value="card">💳 Card</option>
                        <option value="mobile_money">📱 Mobile Money</option>
                        <option value="insurance">🏥 Insurance</option>
                    </select>
                </div>

                <!-- Reference Number (Optional) -->
                <div>
                    <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Reference Number (Optional)
                    </label>
                    <input type="text" id="reference_number" name="reference_number"
                           placeholder="Transaction reference..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex space-x-3 mt-6">
                <button type="button" onclick="closePaymentModal()"
                        class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    <i class="fas fa-times mr-2"></i>Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all font-medium">
                    <i class="fas fa-check mr-2"></i>Confirm Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Updated openPaymentModal function to handle total cost, amount paid, and remaining balance
function openPaymentModal(patientId, visitId, paymentType, totalCost, amountPaid = 0, patientName, itemId, itemType) {
    document.getElementById('modal_patient_id').value = patientId;
    document.getElementById('modal_visit_id').value = visitId;
    document.getElementById('modal_payment_type').value = paymentType;
    // The hidden input for the actual amount being paid in this transaction
    document.getElementById('modal_amount_hidden').value = ''; // Clear previous value
    document.getElementById('modal_item_id').value = itemId || '';
    document.getElementById('modal_item_type').value = itemType || '';
    
    document.getElementById('modal_patient_name').textContent = patientName;
    
    const typeDisplay = paymentType === 'lab_test' ? 'Laboratory Tests' : 'Medicines';
    document.getElementById('modal_payment_type_display').textContent = typeDisplay;
    
    // Calculate remaining balance
    const remainingBalance = totalCost - amountPaid;

    // Update displays
    document.getElementById('modal_remaining_balance_display').textContent = 'Tsh ' + parseFloat(remainingBalance).toLocaleString('en-US');
    
    // Set the input field's value to the remaining balance by default, but allow editing
    const amountToPayInput = document.getElementById('amount_to_pay_input');
    amountToPayInput.value = remainingBalance > 0 ? remainingBalance.toFixed(0) : 0;
    amountToPayInput.max = remainingBalance > 0 ? remainingBalance.toFixed(0) : 0; // Max can be remaining balance
    // Also update the hidden input for form submission
    document.getElementById('modal_amount_hidden').value = amountToPayInput.value;

    // Show/hide specific fields for medicine payments
    const totalCostContainer = document.getElementById('modal_total_cost_container');
    const totalCostDisplay = document.getElementById('modal_total_cost_display');
    const amountPaidContainer = document.getElementById('modal_amount_paid_container');
    const amountPaidDisplay = document.getElementById('modal_amount_paid_display');

    if (paymentType === 'medicine') {
        totalCostContainer.classList.remove('hidden');
        amountPaidContainer.classList.remove('hidden');
        totalCostDisplay.textContent = 'Tsh ' + parseFloat(totalCost).toLocaleString('en-US');
        amountPaidDisplay.textContent = 'Tsh ' + parseFloat(amountPaid).toLocaleString('en-US');
    } else {
        totalCostContainer.classList.add('hidden');
        amountPaidContainer.classList.add('hidden');
    }

    const modal = document.getElementById('paymentModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // Reset form and hide conditional fields
    document.getElementById('paymentForm').reset();
    document.getElementById('modal_total_cost_container').classList.add('hidden');
    document.getElementById('modal_amount_paid_container').classList.add('hidden');
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePaymentModal();
    }
});

// Close modal on outside click
document.getElementById('paymentModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closePaymentModal();
    }
});
// Defensive re-hide: ensure the amount-paid container remains hidden even if other scripts try to show it
(function enforceAmountPaidHidden(){
    // Wait for DOM ready
    function hideNow(){
        var container = document.getElementById('modal_amount_paid_container');
        if (!container) return;
        // Immediately hide
        container.style.display = 'none';
        // Observe attribute changes to re-hide if another script toggles classes or styles
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(m) {
                if (m.type === 'attributes' && (m.attributeName === 'class' || m.attributeName === 'style')) {
                    container.style.display = 'none';
                }
            });
        });
        observer.observe(container, { attributes: true, attributeFilter: ['class', 'style'] });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hideNow);
    } else {
        hideNow();
    }
})();
</script>