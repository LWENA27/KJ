<?php
$title = 'Pending Payments';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800">Pending Payments</h1>
            <p class="text-neutral-500 mt-1">Collect payments for lab tests, medicines, and services</p>
        </div>
    </div>

    <!-- Payment Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-neutral-200">
        <div class="border-b border-neutral-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('lab')" id="tab-lab" 
                        class="tab-btn py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                    Lab Tests
                    <?php if (!empty($pending_lab_payments)): ?>
                    <span class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2 rounded-full text-xs">
                        <?= count($pending_lab_payments) ?>
                    </span>
                    <?php endif; ?>
                </button>
                <button onclick="showTab('medicine')" id="tab-medicine" 
                        class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-sm text-neutral-500 hover:text-neutral-700 hover:border-neutral-300">
                    Medicines
                    <?php if (!empty($pending_medicine_payments)): ?>
                    <span class="ml-2 bg-neutral-100 text-neutral-600 py-0.5 px-2 rounded-full text-xs">
                        <?= count($pending_medicine_payments) ?>
                    </span>
                    <?php endif; ?>
                </button>
                <button onclick="showTab('service')" id="tab-service" 
                        class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-sm text-neutral-500 hover:text-neutral-700 hover:border-neutral-300">
                    Services
                    <?php if (!empty($pending_service_payments)): ?>
                    <span class="ml-2 bg-neutral-100 text-neutral-600 py-0.5 px-2 rounded-full text-xs">
                        <?= count($pending_service_payments) ?>
                    </span>
                    <?php endif; ?>
                </button>
            </nav>
        </div>

        <!-- Lab Tests Tab -->
        <div id="content-lab" class="tab-content p-6">
            <?php if (empty($pending_lab_payments)): ?>
            <div class="text-center py-12">
                <i class="fas fa-check-circle text-5xl text-green-400 mb-4"></i>
                <p class="text-neutral-600">No pending lab test payments</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-neutral-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Patient</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Tests</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Visit Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                        <?php foreach ($pending_lab_payments as $payment): ?>
                        <tr class="hover:bg-neutral-50">
                            <td class="px-4 py-4">
                                <p class="font-medium text-neutral-800"><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></p>
                                <p class="text-xs text-neutral-500"><?= htmlspecialchars($payment['registration_number']) ?></p>
                            </td>
                            <td class="px-4 py-4 text-neutral-600">
                                <?= $payment['test_count'] ?> test(s)
                            </td>
                            <td class="px-4 py-4 font-medium text-neutral-800">
                                TZS <?= number_format($payment['remaining_amount_to_pay'], 0) ?>
                            </td>
                            <td class="px-4 py-4 text-neutral-500 text-sm">
                                <?= date('M d, Y', strtotime($payment['visit_date'])) ?>
                            </td>
                            <td class="px-4 py-4">
                                <button onclick="openPaymentModal('lab_test', <?= htmlspecialchars(json_encode($payment)) ?>)"
                                        class="px-3 py-1.5 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-money-bill-wave mr-1"></i>Collect
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Medicines Tab -->
        <div id="content-medicine" class="tab-content p-6 hidden">
            <?php if (empty($pending_medicine_payments)): ?>
            <div class="text-center py-12">
                <i class="fas fa-check-circle text-5xl text-green-400 mb-4"></i>
                <p class="text-neutral-600">No pending medicine payments</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-neutral-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Patient</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Medicine</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Visit Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                        <?php foreach ($pending_medicine_payments as $payment): ?>
                        <tr class="hover:bg-neutral-50">
                            <td class="px-4 py-4">
                                <p class="font-medium text-neutral-800"><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></p>
                                <p class="text-xs text-neutral-500"><?= htmlspecialchars($payment['registration_number']) ?></p>
                            </td>
                            <td class="px-4 py-4 text-neutral-600">
                                <?= htmlspecialchars($payment['medicine_name']) ?>
                            </td>
                            <td class="px-4 py-4 font-medium text-neutral-800">
                                TZS <?= number_format($payment['remaining_amount_to_pay'], 0) ?>
                            </td>
                            <td class="px-4 py-4 text-neutral-500 text-sm">
                                <?= date('M d, Y', strtotime($payment['visit_date'])) ?>
                            </td>
                            <td class="px-4 py-4">
                                <button onclick="openPaymentModal('medicine', <?= htmlspecialchars(json_encode($payment)) ?>)"
                                        class="px-3 py-1.5 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition-colors">
                                    <i class="fas fa-money-bill-wave mr-1"></i>Collect
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Services Tab -->
        <div id="content-service" class="tab-content p-6 hidden">
            <?php if (empty($pending_service_payments)): ?>
            <div class="text-center py-12">
                <i class="fas fa-check-circle text-5xl text-green-400 mb-4"></i>
                <p class="text-neutral-600">No pending service payments</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-neutral-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Patient</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Service</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Visit Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                        <?php foreach ($pending_service_payments as $payment): ?>
                        <tr class="hover:bg-neutral-50">
                            <td class="px-4 py-4">
                                <p class="font-medium text-neutral-800"><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></p>
                                <p class="text-xs text-neutral-500"><?= htmlspecialchars($payment['registration_number']) ?></p>
                            </td>
                            <td class="px-4 py-4 text-neutral-600">
                                <?= htmlspecialchars($payment['service_name']) ?>
                            </td>
                            <td class="px-4 py-4 font-medium text-neutral-800">
                                TZS <?= number_format($payment['amount'], 0) ?>
                            </td>
                            <td class="px-4 py-4 text-neutral-500 text-sm">
                                <?= $payment['visit_date'] ? date('M d, Y', strtotime($payment['visit_date'])) : 'N/A' ?>
                            </td>
                            <td class="px-4 py-4">
                                <button onclick="openPaymentModal('service', <?= htmlspecialchars(json_encode($payment)) ?>)"
                                        class="px-3 py-1.5 bg-purple-500 text-white text-sm rounded-lg hover:bg-purple-600 transition-colors">
                                    <i class="fas fa-money-bill-wave mr-1"></i>Collect
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-neutral-200">
            <h3 class="text-lg font-semibold text-neutral-800">Record Payment</h3>
        </div>
        <form action="<?= htmlspecialchars($BASE_PATH) ?>/accountant/record_payment" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="patient_id" id="modal_patient_id">
            <input type="hidden" name="visit_id" id="modal_visit_id">
            <input type="hidden" name="payment_type" id="modal_payment_type">
            <input type="hidden" name="item_id" id="modal_item_id">
            <input type="hidden" name="item_type" id="modal_item_type">

            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-1">Patient</label>
                <p id="modal_patient_name" class="text-neutral-800 font-medium"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-1">Payment For</label>
                <p id="modal_payment_for" class="text-neutral-800"></p>
            </div>

            <div>
                <label for="modal_amount" class="block text-sm font-medium text-neutral-700 mb-1">Amount (TZS)</label>
                <input type="number" name="amount" id="modal_amount" required step="0.01" min="0"
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="modal_payment_method" class="block text-sm font-medium text-neutral-700 mb-1">Payment Method</label>
                <select name="payment_method" id="modal_payment_method" required
                        class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="cash">Cash</option>
                    <option value="mobile_money">Mobile Money</option>
                    <option value="card">Card</option>
                    <option value="insurance">Insurance</option>
                </select>
            </div>

            <div>
                <label for="modal_reference" class="block text-sm font-medium text-neutral-700 mb-1">Reference Number (Optional)</label>
                <input type="text" name="reference_number" id="modal_reference"
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closePaymentModal()"
                        class="flex-1 px-4 py-2 border border-neutral-300 text-neutral-700 rounded-lg hover:bg-neutral-50">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(tab) {
    // Hide all content
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    // Reset all tabs
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-blue-500', 'text-blue-600');
        el.classList.add('border-transparent', 'text-neutral-500');
    });
    
    // Show selected content
    document.getElementById('content-' + tab).classList.remove('hidden');
    // Activate selected tab
    const tabBtn = document.getElementById('tab-' + tab);
    tabBtn.classList.remove('border-transparent', 'text-neutral-500');
    tabBtn.classList.add('border-blue-500', 'text-blue-600');
}

function openPaymentModal(type, data) {
    document.getElementById('modal_patient_id').value = data.patient_id;
    document.getElementById('modal_visit_id').value = data.visit_id;
    document.getElementById('modal_payment_type').value = type;
    document.getElementById('modal_patient_name').textContent = data.first_name + ' ' + data.last_name;
    
    if (type === 'lab_test') {
        document.getElementById('modal_payment_for').textContent = data.test_count + ' Lab Test(s)';
        document.getElementById('modal_amount').value = data.remaining_amount_to_pay;
    } else if (type === 'medicine') {
        document.getElementById('modal_payment_for').textContent = 'Medicine: ' + data.medicine_name;
        document.getElementById('modal_amount').value = data.remaining_amount_to_pay;
        document.getElementById('modal_item_id').value = data.prescription_id;
        document.getElementById('modal_item_type').value = 'prescription';
    } else if (type === 'service') {
        document.getElementById('modal_payment_for').textContent = 'Service: ' + data.service_name;
        document.getElementById('modal_amount').value = data.amount;
        document.getElementById('modal_item_id').value = data.order_id;
        document.getElementById('modal_item_type').value = 'service_order';
    }
    
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}
</script>
