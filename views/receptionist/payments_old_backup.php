<!-- Page Header with Professional Gradient -->
<div class="bg-gradient-to-r from-purple-600 via-purple-700 to-indigo-800 rounded-lg shadow-xl p-6 mb-6">
    <div class="flex items-center justify-between">
        <div class="text-white">
            <h1 class="text-3xl font-bold flex items-center">
                <i class="fas fa-credit-card mr-3 text-purple-200"></i>
                Payment Management
            </h1>
            <p class="text-purple-100 mt-2 text-lg">Process and track patient payments</p>
        </div>
        <a href="<?php echo BASE_PATH; ?>/receptionist/payments" class="bg-white text-purple-700 hover:bg-purple-50 px-6 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
            <i class="fas fa-plus mr-2"></i>New Payment
        </a>
    </div>
</div>

<!-- Payment Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php
    $totalPayments = count($payments);
    $totalAmount = array_sum(array_column($payments, 'amount'));
    $todayPayments = count(array_filter($payments, fn($p) => date('Y-m-d', strtotime($p['payment_date'])) === date('Y-m-d')));
    $completedPayments = count(array_filter($payments, fn($p) => ($p['status'] ?? $p['payment_status'] ?? '') === 'paid'));
    
    $cards = [
        ['label' => 'Total Payments', 'count' => $totalPayments, 'color' => 'from-blue-500 to-blue-600', 'icon' => 'fas fa-receipt'],
        ['label' => 'Total Revenue', 'count' => 'Tsh ' . number_format($totalAmount, 0, '.', ','), 'color' => 'from-green-500 to-green-600', 'icon' => 'fas fa-money-bill-wave'],
        ['label' => 'Today\'s Payments', 'count' => $todayPayments, 'color' => 'from-yellow-500 to-yellow-600', 'icon' => 'fas fa-calendar-day'],
        ['label' => 'Completed', 'count' => $completedPayments, 'color' => 'from-purple-500 to-purple-600', 'icon' => 'fas fa-check-circle']
    ];
    
    foreach ($cards as $card): ?>
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1"><?php echo $card['label']; ?></p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $card['count']; ?></p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br <?php echo $card['color']; ?> rounded-xl flex items-center justify-center shadow-lg">
                    <i class="<?php echo $card['icon']; ?> text-white text-xl"></i>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Pending Payments Section -->
<?php if (!empty($pending_lab_payments) || !empty($pending_medicine_payments)): ?>
<div class="bg-red-50 border-l-4 border-red-500 rounded-lg shadow-lg p-6 mb-6">
    <div class="flex items-center mb-4">
        <i class="fas fa-exclamation-triangle text-red-600 text-2xl mr-3"></i>
        <h2 class="text-2xl font-bold text-red-900">Pending Payments Requiring Action</h2>
    </div>
    
    <!-- Pending Lab Test Payments -->
    <?php if (!empty($pending_lab_payments)): ?>
    <div class="bg-white rounded-lg shadow-md mb-4 overflow-hidden">
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-3">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-flask mr-2"></i>
                Pending Lab Test Payments (<?php echo count($pending_lab_payments); ?>)
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Tests</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Ordered Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($pending_lab_payments as $pending): ?>
                    <tr class="hover:bg-red-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                    <?php echo strtoupper(substr($pending['first_name'], 0, 1) . substr($pending['last_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900"><?php echo htmlspecialchars($pending['first_name'] . ' ' . $pending['last_name']); ?></div>
                                    <div class="text-sm text-gray-600"><?php echo htmlspecialchars($pending['registration_number']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($pending['test_names']); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-lg font-bold text-red-600">Tsh <?php echo number_format($pending['total_amount'], 0, '.', ','); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900"><?php echo date('M j, Y', strtotime($pending['created_at'])); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="openPaymentModal(<?php echo $pending['patient_id']; ?>, <?php echo $pending['visit_id']; ?>, 'lab_test', <?php echo $pending['total_amount']; ?>, '<?php echo htmlspecialchars($pending['first_name'] . ' ' . $pending['last_name']); ?>')" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-all">
                                <i class="fas fa-credit-card mr-2"></i>Record Payment
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
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-3">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-pills mr-2"></i>
                Pending Medicine Payments (<?php echo count($pending_medicine_payments); ?>)
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Medicines</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Prescribed Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($pending_medicine_payments as $pending): ?>
                    <tr class="hover:bg-orange-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                    <?php echo strtoupper(substr($pending['first_name'], 0, 1) . substr($pending['last_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900"><?php echo htmlspecialchars($pending['first_name'] . ' ' . $pending['last_name']); ?></div>
                                    <div class="text-sm text-gray-600"><?php echo htmlspecialchars($pending['registration_number']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($pending['medicine_names']); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-lg font-bold text-orange-600">Tsh <?php echo number_format($pending['total_amount'], 0, '.', ','); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900"><?php echo date('M j, Y', strtotime($pending['created_at'])); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="openPaymentModal(<?php echo $pending['patient_id']; ?>, <?php echo $pending['visit_id']; ?>, 'medicine', <?php echo $pending['total_amount']; ?>, '<?php echo htmlspecialchars($pending['first_name'] . ' ' . $pending['last_name']); ?>')" 
                                    class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition-all">
                                <i class="fas fa-credit-card mr-2"></i>Record Payment
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Payments Table with Professional Design -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-purple-50">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-table mr-3 text-purple-600"></i>
                Payment Records
            </h3>
            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                <?php echo count($payments); ?> payments
            </span>
        </div>
    </div>
    
    <?php if (empty($payments)): ?>
        <div class="p-12 text-center">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center shadow-lg">
                <i class="fas fa-receipt text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">No payments recorded</h3>
            <p class="text-gray-600 mb-8 text-lg">Start by processing your first payment</p>
            <a href="<?php echo BASE_PATH; ?>/receptionist/payments" class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-plus mr-2"></i>Process First Payment
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($payments as $payment): ?>
                    <tr class="hover:bg-purple-50 transition-all duration-300">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg mr-4">
                                    <?php echo strtoupper(substr($payment['first_name'], 0, 1) . substr($payment['last_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']); ?>
                                    </div>
                                    <?php if ($payment['consultation_id'] ?? false): ?>
                                    <div class="text-sm text-gray-600 flex items-center">
                                        <i class="fas fa-stethoscope mr-1 text-gray-400"></i>
                                        Consultation ID: #<?php echo str_pad($payment['consultation_id'], 4, '0', STR_PAD_LEFT); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-bold text-green-600">
                                Tsh <?php echo number_format($payment['amount'], 0, '.', ','); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <?php
                                $methodIcons = [
                                    'cash' => 'fas fa-money-bill-alt text-green-500',
                                    'card' => 'fas fa-credit-card text-blue-500',
                                    'insurance' => 'fas fa-shield-alt text-purple-500',
                                    'other' => 'fas fa-ellipsis-h text-gray-500'
                                ];
                                $icon = $methodIcons[$payment['payment_method']] ?? $methodIcons['other'];
                                ?>
                                <i class="<?php echo $icon; ?> mr-2"></i>
                                <span class="text-sm font-medium text-gray-900"><?php echo ucfirst($payment['payment_method']); ?></span>
                            </div>
                        </td>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="font-medium"><?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></div>
                                <div class="text-gray-600 flex items-center">
                                    <i class="fas fa-clock mr-1 text-gray-400"></i>
                                    <?php echo date('H:i', strtotime($payment['payment_date'])); ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php $status = $payment['status'] ?? $payment['payment_status'] ?? 'pending'; ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-sm
                                <?php
                                switch ($status) {
                                    case 'pending':
                                        echo 'bg-yellow-100 text-yellow-800 border border-yellow-300';
                                        $icon = 'fas fa-clock';
                                        break;
                                    case 'paid':
                                        echo 'bg-green-100 text-green-800 border border-green-300';
                                        $icon = 'fas fa-check-circle';
                                        break;
                                    case 'cancelled':
                                        echo 'bg-red-100 text-red-800 border border-red-300';
                                        $icon = 'fas fa-times-circle';
                                        break;
                                    case 'refunded':
                                        echo 'bg-blue-100 text-blue-800 border border-blue-300';
                                        $icon = 'fas fa-undo';
                                        break;
                                    default:
                                        echo 'bg-gray-100 text-gray-800 border border-gray-300';
                                        $icon = 'fas fa-question-circle';
                                }
                                ?>">
                                <i class="<?php echo $icon; ?> mr-2"></i>
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <a href="#" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105" title="View Payment">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="#" class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105" title="Print Receipt">
                                    <i class="fas fa-print"></i>
                                </a>
                                <?php if (($payment['status'] ?? $payment['payment_status'] ?? '') === 'pending'): ?>
                                <a href="#" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105" title="Process Payment">
                                    <i class="fas fa-credit-card"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full mx-4 transform transition-all">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 rounded-t-lg">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-credit-card mr-3"></i>
                Record Payment
            </h3>
        </div>
        
        <form method="POST" action="/KJ/receptionist/record_payment" class="p-6">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">
            <input type="hidden" name="patient_id" id="modal_patient_id">
            <input type="hidden" name="visit_id" id="modal_visit_id">
            <input type="hidden" name="payment_type" id="modal_payment_type">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Patient Name</label>
                <div class="px-4 py-3 bg-gray-100 rounded-lg">
                    <span id="modal_patient_name" class="font-semibold text-gray-900"></span>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Type</label>
                <div class="px-4 py-3 bg-gray-100 rounded-lg">
                    <span id="modal_payment_type_display" class="font-semibold text-gray-900"></span>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (TSH)</label>
                <input type="number" 
                       name="amount" 
                       id="modal_amount" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                       required 
                       readonly>
            </div>
            
            <div class="mb-4">
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <select name="payment_method" 
                        id="modal_payment_method" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                        required>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="mobile_money">Mobile Money</option>
                    <option value="insurance">Insurance</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">Reference Number (Optional)</label>
                <input type="text" 
                       name="reference_number" 
                       id="modal_reference_number" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                       placeholder="Enter transaction reference">
            </div>
            
            <div class="flex gap-3">
                <button type="button" 
                        onclick="closePaymentModal()" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-3 rounded-lg font-medium transition-all">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-4 py-3 rounded-lg font-medium transition-all">
                    <i class="fas fa-check mr-2"></i>Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Enhanced JavaScript for Payments -->
<script>
function openPaymentModal(patientId, visitId, paymentType, amount, patientName) {
    document.getElementById('modal_patient_id').value = patientId;
    document.getElementById('modal_visit_id').value = visitId;
    document.getElementById('modal_payment_type').value = paymentType;
    document.getElementById('modal_amount').value = amount;
    document.getElementById('modal_patient_name').textContent = patientName;
    
    const typeDisplay = {
        'lab_test': 'Lab Test Payment',
        'medicine': 'Medicine Payment'
    };
    document.getElementById('modal_payment_type_display').textContent = typeDisplay[paymentType] || paymentType;
    
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    // Add professional hover effects to stat cards
    document.querySelectorAll('.transform').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
        });
    });
    
    // Add loading states to action buttons
    document.querySelectorAll('a[class*="bg-"]').forEach(btn => {
        btn.addEventListener('click', function() {
            this.style.opacity = '0.7';
            setTimeout(() => {
                this.style.opacity = '1';
            }, 1000);
        });
    });
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePaymentModal();
        }
    });
    
    // Close modal when clicking outside
    document.getElementById('paymentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePaymentModal();
        }
    });
});
</script>
