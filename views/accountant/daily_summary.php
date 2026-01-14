<?php
$pageTitle = 'Daily Summary - Accountant';
$userRole = 'accountant';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . ($BASE_PATH ?? '') . '/auth/login');
    exit;
}

$report_date = $_GET['date'] ?? date('Y-m-d');
?>

<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="p-3 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl shadow-lg">
                        <i class="fas fa-calendar-day text-white text-xl"></i>
                    </div>
                    Daily Summary
                </h1>
                <p class="text-gray-600 mt-2">Financial summary for <?= date('F d, Y', strtotime($report_date)) ?></p>
            </div>
            <div class="flex gap-3">
                <a href="<?= $BASE_PATH ?>/accountant/export?type=daily&date=<?= $report_date ?>" 
                   class="btn bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-lg shadow-md transition-all">
                    <i class="fas fa-file-excel mr-2"></i>Export
                </a>
                <a href="<?= $BASE_PATH ?>/accountant/print_daily?date=<?= $report_date ?>" 
                   class="btn bg-gray-600 text-white hover:bg-gray-700 px-4 py-2 rounded-lg shadow-md transition-all" target="_blank">
                    <i class="fas fa-print mr-2"></i>Print
                </a>
                <a href="<?= $BASE_PATH ?>/accountant/reports" class="btn bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-lg shadow-md transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Reports
                </a>
            </div>
        </div>

        <!-- Date Selector -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-8 border border-gray-100">
            <form method="GET" class="flex items-center gap-4">
                <label class="text-sm font-medium text-gray-700">Select Date:</label>
                <input type="date" name="date" value="<?= $report_date ?>" 
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       onchange="this.form.submit()">
                <div class="flex gap-2 ml-auto">
                    <a href="?date=<?= date('Y-m-d', strtotime($report_date . ' -1 day')) ?>" 
                       class="px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <a href="?date=<?= date('Y-m-d') ?>" 
                       class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-all">
                        Today
                    </a>
                    <a href="?date=<?= date('Y-m-d', strtotime($report_date . ' +1 day')) ?>" 
                       class="px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all <?= $report_date >= date('Y-m-d') ? 'opacity-50 pointer-events-none' : '' ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                        <p class="text-2xl font-bold text-emerald-600">TZS <?= number_format($summary['total_revenue'] ?? 0) ?></p>
                    </div>
                    <div class="p-3 bg-emerald-100 rounded-full">
                        <i class="fas fa-money-bill-wave text-emerald-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Transactions</p>
                        <p class="text-2xl font-bold text-blue-600"><?= number_format($summary['total_transactions'] ?? 0) ?></p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-receipt text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Patients Served</p>
                        <p class="text-2xl font-bold text-purple-600"><?= number_format($summary['patients_served'] ?? 0) ?></p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pending Amount</p>
                        <p class="text-2xl font-bold text-orange-600">TZS <?= number_format($summary['pending_amount'] ?? 0) ?></p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i class="fas fa-clock text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue by Service -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-stethoscope text-indigo-600"></i>
                        Revenue by Service
                    </h3>
                </div>
                <div class="p-6">
                    <?php if (empty($revenue_by_service ?? [])): ?>
                        <p class="text-center text-gray-500 py-8">No service data for this date</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($revenue_by_service as $service): ?>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-3 h-3 rounded-full bg-indigo-500"></div>
                                        <span class="text-gray-700"><?= htmlspecialchars($service['name'] ?? 'Service') ?></span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-semibold text-gray-900">TZS <?= number_format($service['amount'] ?? 0) ?></span>
                                        <span class="text-sm text-gray-500 ml-2">(<?= $service['count'] ?? 0 ?>)</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Payment Methods Breakdown -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-credit-card text-emerald-600"></i>
                        Payment Methods
                    </h3>
                </div>
                <div class="p-6">
                    <?php if (empty($payment_methods ?? [])): ?>
                        <p class="text-center text-gray-500 py-8">No payment data for this date</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php 
                            $colors = ['cash' => 'bg-green-500', 'card' => 'bg-blue-500', 'mobile' => 'bg-purple-500', 'insurance' => 'bg-orange-500'];
                            $total = array_sum(array_column($payment_methods, 'amount'));
                            foreach ($payment_methods as $method): 
                                $percentage = $total > 0 ? round(($method['amount'] / $total) * 100) : 0;
                                $color = $colors[$method['method']] ?? 'bg-gray-500';
                            ?>
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700"><?= ucfirst($method['method'] ?? 'Unknown') ?></span>
                                        <span class="text-gray-600">TZS <?= number_format($method['amount'] ?? 0) ?> (<?= $percentage ?>%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="<?= $color ?> h-2 rounded-full" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Detailed Transactions -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-list text-blue-600"></i>
                    Detailed Transactions
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Receipt #</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Received By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($transactions ?? [])): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-calendar-times text-4xl mb-4 text-gray-300"></i>
                                    <p>No transactions recorded for this date</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transactions as $tx): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?= date('H:i', strtotime($tx['created_at'] ?? '')) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-semibold text-indigo-600">
                                        <?= htmlspecialchars($tx['receipt_number'] ?? 'N/A') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($tx['patient_name'] ?? 'Unknown') ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <?= htmlspecialchars($tx['service_name'] ?? $tx['description'] ?? 'Service') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600">
                                        TZS <?= number_format($tx['amount'] ?? 0) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $method = $tx['payment_method'] ?? 'cash';
                                        $methodColors = [
                                            'cash' => 'bg-green-100 text-green-800',
                                            'card' => 'bg-blue-100 text-blue-800',
                                            'mobile' => 'bg-purple-100 text-purple-800',
                                            'insurance' => 'bg-orange-100 text-orange-800'
                                        ];
                                        $color = $methodColors[$method] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $color ?>">
                                            <?= ucfirst($method) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?= htmlspecialchars($tx['received_by_name'] ?? 'System') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($transactions)): ?>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right text-sm font-semibold text-gray-700">
                                    Daily Total:
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-emerald-600">
                                    TZS <?= number_format($summary['total_revenue'] ?? 0) ?>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>
