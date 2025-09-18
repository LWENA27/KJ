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
        <a href="/KJ/receptionist/payments" class="bg-white text-purple-700 hover:bg-purple-50 px-6 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
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
    $completedPayments = count(array_filter($payments, fn($p) => $p['status'] === 'completed'));
    
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
            <a href="/KJ/receptionist/payments" class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
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
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-sm
                                <?php
                                switch ($payment['status']) {
                                    case 'pending':
                                        echo 'bg-yellow-100 text-yellow-800 border border-yellow-300';
                                        $icon = 'fas fa-clock';
                                        break;
                                    case 'completed':
                                        echo 'bg-green-100 text-green-800 border border-green-300';
                                        $icon = 'fas fa-check-circle';
                                        break;
                                    case 'failed':
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
                                <?php echo ucfirst($payment['status']); ?>
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
                                <?php if ($payment['status'] === 'pending'): ?>
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

<!-- Enhanced JavaScript for Payments -->
<script>
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
});
</script>
