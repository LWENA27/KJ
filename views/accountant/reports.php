<?php
$pageTitle = 'Reports - Accountant';
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
                    <div class="p-3 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl shadow-lg">
                        <i class="fas fa-chart-bar text-white text-xl"></i>
                    </div>
                    Financial Reports
                </h1>
                <p class="text-gray-600 mt-2">Generate and analyze financial reports</p>
            </div>
            <a href="<?= $BASE_PATH ?>/accountant/dashboard" class="btn bg-gray-600 text-white hover:bg-gray-700 px-4 py-2 rounded-lg shadow-md transition-all">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>

        <!-- Report Types -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Daily Report Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-calendar-day"></i>
                        Daily Report
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Generate a detailed report of all financial transactions for a specific day.</p>
                    <form method="GET" action="<?= $BASE_PATH ?>/accountant/daily_summary">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                            <input type="date" name="date" value="<?= date('Y-m-d') ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-all">
                            <i class="fas fa-file-alt mr-2"></i>Generate Report
                        </button>
                    </form>
                </div>
            </div>

            <!-- Weekly Report Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-calendar-week"></i>
                        Weekly Report
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Generate a summary of weekly revenue, expenses, and service breakdown.</p>
                    <form method="GET" action="<?= $BASE_PATH ?>/accountant/export">
                        <input type="hidden" name="type" value="weekly">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Week Starting</label>
                            <input type="date" name="week_start" value="<?= date('Y-m-d', strtotime('monday this week')) ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <button type="submit" class="w-full bg-emerald-600 text-white py-2 rounded-lg hover:bg-emerald-700 transition-all">
                            <i class="fas fa-download mr-2"></i>Export Report
                        </button>
                    </form>
                </div>
            </div>

            <!-- Monthly Report Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-calendar-alt"></i>
                        Monthly Report
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Comprehensive monthly financial analysis with trends and comparisons.</p>
                    <form method="GET" action="<?= $BASE_PATH ?>/accountant/export">
                        <input type="hidden" name="type" value="monthly">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Month</label>
                            <input type="month" name="month" value="<?= date('Y-m') ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                        <button type="submit" class="w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition-all">
                            <i class="fas fa-download mr-2"></i>Export Report
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Stats Overview -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-chart-line text-indigo-600"></i>
                Current Period Summary
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-3xl font-bold text-blue-600">TZS <?= number_format($today_revenue ?? 0) ?></p>
                    <p class="text-sm text-gray-600 mt-1">Today's Revenue</p>
                </div>
                <div class="text-center p-4 bg-emerald-50 rounded-lg">
                    <p class="text-3xl font-bold text-emerald-600">TZS <?= number_format($week_revenue ?? 0) ?></p>
                    <p class="text-sm text-gray-600 mt-1">This Week</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-3xl font-bold text-purple-600">TZS <?= number_format($month_revenue ?? 0) ?></p>
                    <p class="text-sm text-gray-600 mt-1">This Month</p>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg">
                    <p class="text-3xl font-bold text-orange-600"><?= number_format($total_transactions ?? 0) ?></p>
                    <p class="text-sm text-gray-600 mt-1">Total Transactions</p>
                </div>
            </div>
        </div>

        <!-- Custom Report Builder -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-gray-700 to-gray-900 px-6 py-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-cogs"></i>
                    Custom Report Builder
                </h3>
            </div>
            <div class="p-6">
                <form method="POST" action="<?= $BASE_PATH ?>/accountant/export" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <input type="hidden" name="type" value="custom">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="date" name="start_date" value="<?= date('Y-m-01') ?>" 
                                   class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500" required>
                            <input type="date" name="end_date" value="<?= date('Y-m-d') ?>" 
                                   class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500" required>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                        <select name="report_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                            <option value="revenue">Revenue Summary</option>
                            <option value="services">Services Breakdown</option>
                            <option value="payment_methods">Payment Methods</option>
                            <option value="detailed">Detailed Transactions</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Export Format</label>
                        <div class="flex gap-2">
                            <select name="format" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500">
                                <option value="excel">Excel (.xlsx)</option>
                                <option value="csv">CSV</option>
                                <option value="pdf">PDF</option>
                            </select>
                            <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-all">
                                <i class="fas fa-download mr-2"></i>Export
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="mt-8 bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Reports Generated</h3>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if (empty($recent_reports ?? [])): ?>
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-file-invoice text-4xl mb-3 text-gray-300"></i>
                        <p>No reports generated yet. Use the options above to create your first report.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recent_reports as $report): ?>
                        <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center gap-4">
                                <div class="p-2 bg-indigo-100 rounded-lg">
                                    <i class="fas fa-file-alt text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"><?= htmlspecialchars($report['name'] ?? 'Report') ?></p>
                                    <p class="text-sm text-gray-500"><?= date('M d, Y H:i', strtotime($report['created_at'])) ?></p>
                                </div>
                            </div>
                            <a href="<?= $BASE_PATH ?>/accountant/download_report?id=<?= $report['id'] ?>" 
                               class="text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
