<?php
$pageTitle = 'Payment History - Accountant';
$userRole = 'accountant';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . ($BASE_PATH ?? '') . '/auth/login');
    exit;
}
?>

<div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-teal-50">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="p-3 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl shadow-lg">
                        <i class="fas fa-history text-white text-xl"></i>
                    </div>
                    Payment History
                </h1>
                <p class="text-gray-600 mt-2">View and search all completed payment transactions</p>
            </div>
            <div class="flex gap-3">
                <a href="<?= $BASE_PATH ?>/accountant/export?type=history" class="btn bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-lg shadow-md transition-all">
                    <i class="fas fa-file-excel mr-2"></i>Export to Excel
                </a>
                <a href="<?= $BASE_PATH ?>/accountant/reports" class="btn bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-lg shadow-md transition-all">
                    <i class="fas fa-chart-bar mr-2"></i>Reports
                </a>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-100">
            <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                           placeholder="Patient name, receipt #..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" name="from_date" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" name="to_date" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">All Methods</option>
                        <option value="cash" <?= ($_GET['payment_method'] ?? '') === 'cash' ? 'selected' : '' ?>>Cash</option>
                        <option value="card" <?= ($_GET['payment_method'] ?? '') === 'card' ? 'selected' : '' ?>>Card</option>
                        <option value="mobile" <?= ($_GET['payment_method'] ?? '') === 'mobile' ? 'selected' : '' ?>>Mobile Money</option>
                        <option value="insurance" <?= ($_GET['payment_method'] ?? '') === 'insurance' ? 'selected' : '' ?>>Insurance</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Transactions</p>
                        <p class="text-2xl font-bold text-gray-900"><?= number_format($total_transactions ?? 0) ?></p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-receipt text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Amount</p>
                        <p class="text-2xl font-bold text-emerald-600">TZS <?= number_format($total_amount ?? 0) ?></p>
                    </div>
                    <div class="p-3 bg-emerald-100 rounded-full">
                        <i class="fas fa-money-bill-wave text-emerald-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Cash Payments</p>
                        <p class="text-2xl font-bold text-blue-600">TZS <?= number_format($cash_total ?? 0) ?></p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-coins text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Other Methods</p>
                        <p class="text-2xl font-bold text-purple-600">TZS <?= number_format($other_total ?? 0) ?></p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-credit-card text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Receipt #</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Received By</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-history text-4xl mb-4 text-gray-300"></i>
                                    <p>No payment history found for the selected criteria</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payments as $payment): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-mono font-semibold text-indigo-600"><?= htmlspecialchars($payment['receipt_number'] ?? 'N/A') ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= date('M d, Y', strtotime($payment['payment_date'] ?? $payment['created_at'])) ?>
                                        <br><span class="text-gray-500"><?= date('H:i', strtotime($payment['payment_date'] ?? $payment['created_at'])) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($payment['patient_name'] ?? 'Unknown') ?></div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($payment['patient_id'] ?? '') ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <?= htmlspecialchars($payment['service_name'] ?? $payment['description'] ?? 'Service') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-emerald-600">TZS <?= number_format($payment['amount'] ?? 0) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $method = $payment['payment_method'] ?? 'cash';
                                        $methodColors = [
                                            'cash' => 'bg-green-100 text-green-800',
                                            'card' => 'bg-blue-100 text-blue-800',
                                            'mobile' => 'bg-purple-100 text-purple-800',
                                            'insurance' => 'bg-orange-100 text-orange-800'
                                        ];
                                        $color = $methodColors[$method] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $color ?>">
                                            <?= ucfirst($method) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?= htmlspecialchars($payment['received_by_name'] ?? 'System') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="<?= $BASE_PATH ?>/accountant/view_receipt?id=<?= $payment['id'] ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-3" title="View Receipt">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= $BASE_PATH ?>/accountant/print_receipt?id=<?= $payment['id'] ?>" 
                                           class="text-gray-600 hover:text-gray-900" title="Print Receipt" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($total_pages) && $total_pages > 1): ?>
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing page <?= $current_page ?? 1 ?> of <?= $total_pages ?>
                        </div>
                        <div class="flex gap-2">
                            <?php if (($current_page ?? 1) > 1): ?>
                                <a href="?page=<?= ($current_page ?? 1) - 1 ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php endif; ?>
                            <?php if (($current_page ?? 1) < $total_pages): ?>
                                <a href="?page=<?= ($current_page ?? 1) + 1 ?>" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">
                                    Next
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
