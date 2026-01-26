<?php $title = "Pending Payments"; ?>

<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-purple-600 via-purple-700 to-indigo-800 rounded-lg shadow-xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="text-white">
                <h1 class="text-3xl font-bold flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-purple-200"></i>
                    Pending Payments
                </h1>
                <p class="text-purple-100 mt-2 text-lg">Process payments for lab tests and medicines</p>
            </div>
            <div class="flex space-x-3">
                <a href="<?php echo BASE_PATH; ?>/receptionist/payment_history" 
                   class="bg-white text-purple-700 hover:bg-purple-50 px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-lg flex items-center">
                    <i class="fas fa-history mr-2"></i>
                    Payment History
                </a>
                <a href="<?php echo BASE_PATH; ?>/receptionist/dashboard" 
                   class="bg-purple-800 text-white hover:bg-purple-900 px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-lg flex items-center">
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
                        $lab_total = array_sum(array_column($pending_lab_payments, 'total_amount')); 
                        echo number_format($lab_total, 0);
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
                        $med_total = array_sum(array_column($pending_medicine_payments, 'total_amount')); 
                        echo number_format($med_total, 0);
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
                        Tsh <?php echo number_format($lab_total + $med_total, 0); ?>
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
                <a href="<?php echo BASE_PATH; ?>/receptionist/payment_history" 
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
                                Amount
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
                                        Tsh <?php echo number_format($payment['total_amount'], 0); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openPaymentModal(<?php echo $payment['patient_id']; ?>, <?php echo $payment['visit_id']; ?>, 'lab_test', <?php echo $payment['total_amount']; ?>, '<?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name'], ENT_QUOTES); ?>')"
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
                                Amount
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
                                        Tsh <?php echo number_format($payment['total_amount'], 0); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openPaymentModal(<?php echo $payment['patient_id']; ?>, <?php echo $payment['visit_id']; ?>, 'medicine', <?php echo $payment['total_amount']; ?>, '<?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name'], ENT_QUOTES); ?>')"
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
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center" style="z-index:2147483646;">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full m-4 transform transition-all" style="z-index:2147483647;">
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
            <input type="hidden" id="modal_amount" name="amount">

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

                <!-- Amount Display -->
                <div class="bg-purple-50 rounded-lg p-4 border-2 border-purple-200">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount to Pay</label>
                    <p id="modal_amount_display" class="text-2xl font-bold text-purple-600"></p>
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
function openPaymentModal(patientId, visitId, paymentType, amount, patientName) {
    document.getElementById('modal_patient_id').value = patientId;
    document.getElementById('modal_visit_id').value = visitId;
    document.getElementById('modal_payment_type').value = paymentType;
    document.getElementById('modal_amount').value = amount;
    
    document.getElementById('modal_patient_name').textContent = patientName;
    
    const typeDisplay = paymentType === 'lab_test' ? 'Laboratory Tests' : 'Medicines';
    document.getElementById('modal_payment_type_display').textContent = typeDisplay;
    
    document.getElementById('modal_amount_display').textContent = 'Tsh ' + parseFloat(amount).toLocaleString('en-US');
    
    const modal = document.getElementById('paymentModal');

    // Move modal to document.body to escape any stacking-context created by parent containers
    if (modal.parentNode !== document.body) {
        modal.__originalParent = modal.parentNode;
        modal.__originalNextSibling = modal.nextSibling;
        document.body.appendChild(modal);
    }

    // Ensure modal is visible and uses flex to center
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // Reset form
    document.getElementById('paymentForm').reset();

    // If modal was moved to body, restore it to its original place in the DOM
    if (modal.__originalParent) {
        if (modal.__originalNextSibling) {
            modal.__originalParent.insertBefore(modal, modal.__originalNextSibling);
        } else {
            modal.__originalParent.appendChild(modal);
        }
        delete modal.__originalParent;
        delete modal.__originalNextSibling;
    }
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
</script>
