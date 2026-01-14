<?php
$title = 'Accountant Dashboard';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800">Accountant Dashboard</h1>
            <p class="text-neutral-500 mt-1">Financial overview and payment management</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= htmlspecialchars($BASE_PATH) ?>/accountant/payments" 
               class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all shadow-sm">
                <i class="fas fa-plus mr-2"></i>Record Payment
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today's Revenue -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-500">Today's Revenue</p>
                    <p class="text-2xl font-bold text-neutral-800 mt-1">
                        TZS <?= number_format($payments_today['total_today'] ?? 0, 0) ?>
                    </p>
                    <p class="text-xs mt-2 <?= ($percentage_change ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                        <i class="fas fa-<?= ($percentage_change ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' ?> mr-1"></i>
                        <?= abs(round($percentage_change ?? 0, 1)) ?>% from yesterday
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-500">Pending Payments</p>
                    <p class="text-2xl font-bold text-neutral-800 mt-1"><?= $pending_count ?? 0 ?></p>
                    <p class="text-xs text-yellow-600 mt-2">
                        <i class="fas fa-exclamation-circle mr-1"></i>Requires attention
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Weekly Revenue -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-500">Weekly Revenue</p>
                    <p class="text-2xl font-bold text-neutral-800 mt-1">
                        TZS <?= number_format($weekly_revenue ?? 0, 0) ?>
                    </p>
                    <p class="text-xs text-neutral-500 mt-2">Last 7 days</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-500">Monthly Revenue</p>
                    <p class="text-2xl font-bold text-neutral-800 mt-1">
                        TZS <?= number_format($monthly_revenue ?? 0, 0) ?>
                    </p>
                    <p class="text-xs text-neutral-500 mt-2">Last 30 days</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue by Type -->
    <?php if (!empty($revenue_by_type)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
        <h2 class="text-lg font-semibold text-neutral-800 mb-4">Today's Revenue by Type</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php foreach ($revenue_by_type as $item): ?>
            <div class="p-4 bg-neutral-50 rounded-lg">
                <p class="text-sm text-neutral-500 capitalize"><?= htmlspecialchars(str_replace('_', ' ', $item['payment_type'])) ?></p>
                <p class="text-xl font-bold text-neutral-800">TZS <?= number_format($item['total'], 0) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Payments -->
    <div class="bg-white rounded-xl shadow-sm border border-neutral-200">
        <div class="p-6 border-b border-neutral-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-neutral-800">Recent Payments</h2>
                <a href="<?= htmlspecialchars($BASE_PATH) ?>/accountant/payment_history" class="text-sm text-blue-600 hover:text-blue-700">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                    <?php if (empty($recent_payments)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-neutral-500">
                            <i class="fas fa-receipt text-4xl text-neutral-300 mb-2"></i>
                            <p>No payments recorded today</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recent_payments as $payment): ?>
                    <tr class="hover:bg-neutral-50">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-neutral-800"><?= htmlspecialchars($payment['patient_name']) ?></p>
                                <p class="text-xs text-neutral-500"><?= htmlspecialchars($payment['registration_number']) ?></p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700 capitalize">
                                <?= htmlspecialchars(str_replace('_', ' ', $payment['payment_type'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 font-medium text-neutral-800">
                            TZS <?= number_format($payment['amount'], 0) ?>
                        </td>
                        <td class="px-6 py-4 text-neutral-600 capitalize">
                            <?= htmlspecialchars($payment['payment_method']) ?>
                        </td>
                        <td class="px-6 py-4 text-neutral-500 text-sm">
                            <?= date('H:i', strtotime($payment['payment_date'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="<?= htmlspecialchars($BASE_PATH) ?>/accountant/payments" 
           class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <i class="fas fa-hand-holding-usd text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-neutral-800">Collect Payments</h3>
                    <p class="text-sm text-neutral-500">Record patient payments</p>
                </div>
            </div>
        </a>

        <a href="<?= htmlspecialchars($BASE_PATH) ?>/accountant/payment_history" 
           class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <i class="fas fa-history text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-neutral-800">Payment History</h3>
                    <p class="text-sm text-neutral-500">View all transactions</p>
                </div>
            </div>
        </a>

        <a href="<?= htmlspecialchars($BASE_PATH) ?>/accountant/reports" 
           class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <i class="fas fa-chart-bar text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-neutral-800">Financial Reports</h3>
                    <p class="text-sm text-neutral-500">Analytics & summaries</p>
                </div>
            </div>
        </a>
    </div>
</div>
