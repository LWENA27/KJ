<?php
$pageTitle = 'Dispense History - Pharmacist';
$userRole = 'pharmacist';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . ($BASE_PATH ?? '') . '/auth/login');
    exit;
}
?>

<div class="min-h-screen bg-gradient-to-br from-cyan-50 via-white to-blue-50">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="p-3 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-xl shadow-lg">
                        <i class="fas fa-history text-white text-xl"></i>
                    </div>
                    Dispense History
                </h1>
                <p class="text-gray-600 mt-2">View all dispensed medications and prescription history</p>
            </div>
            <div class="flex gap-3">
                <a href="<?= $BASE_PATH ?>/pharmacist/export_history" class="btn bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-lg shadow-md transition-all">
                    <i class="fas fa-file-excel mr-2"></i>Export
                </a>
                <a href="<?= $BASE_PATH ?>/pharmacist/dashboard" class="btn bg-gray-600 text-white hover:bg-gray-700 px-4 py-2 rounded-lg shadow-md transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-100">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                           placeholder="Patient name, medicine..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" name="from_date" value="<?= htmlspecialchars($_GET['from_date'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" name="to_date" value="<?= htmlspecialchars($_GET['to_date'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                        <option value="">All Status</option>
                        <option value="dispensed" <?= ($_GET['status'] ?? '') === 'dispensed' ? 'selected' : '' ?>>Dispensed</option>
                        <option value="partial" <?= ($_GET['status'] ?? '') === 'partial' ? 'selected' : '' ?>>Partial</option>
                        <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition-all">
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
                        <p class="text-sm font-medium text-gray-500">Total Dispensed</p>
                        <p class="text-2xl font-bold text-emerald-600"><?= number_format($stats['total_dispensed'] ?? 0) ?></p>
                    </div>
                    <div class="p-3 bg-emerald-100 rounded-full">
                        <i class="fas fa-pills text-emerald-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Today's Dispenses</p>
                        <p class="text-2xl font-bold text-blue-600"><?= number_format($stats['today_count'] ?? 0) ?></p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-calendar-day text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Patients Served</p>
                        <p class="text-2xl font-bold text-purple-600"><?= number_format($stats['patients_served'] ?? 0) ?></p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Value</p>
                        <p class="text-2xl font-bold text-orange-600">TZS <?= number_format($stats['total_value'] ?? 0) ?></p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i class="fas fa-money-bill-wave text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dispense History Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Medicine</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Prescribed By</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dispensed By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($dispense_history)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-history text-4xl mb-4 text-gray-300"></i>
                                    <p>No dispense history found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dispense_history as $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= date('M d, Y', strtotime($item['dispensed_at'] ?? $item['created_at'])) ?>
                                        <br><span class="text-gray-500"><?= date('H:i', strtotime($item['dispensed_at'] ?? $item['created_at'])) ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item['patient_name'] ?? 'Unknown') ?></div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($item['patient_id'] ?? '') ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item['medicine_name'] ?? 'N/A') ?></div>
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($item['dosage'] ?? '') ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-gray-900"><?= $item['quantity'] ?? 0 ?></span>
                                        <span class="text-sm text-gray-500"><?= $item['unit'] ?? 'units' ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        Dr. <?= htmlspecialchars($item['prescribed_by'] ?? 'Unknown') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $status = $item['status'] ?? 'dispensed';
                                        $statusColors = [
                                            'dispensed' => 'bg-green-100 text-green-800',
                                            'partial' => 'bg-yellow-100 text-yellow-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                        $color = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                        ?>
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $color ?>">
                                            <?= ucfirst($status) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?= htmlspecialchars($item['dispensed_by_name'] ?? 'System') ?>
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
                                <a href="?page=<?= ($current_page ?? 1) + 1 ?>" class="px-4 py-2 text-sm font-medium text-white bg-cyan-600 rounded-lg hover:bg-cyan-700">
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
