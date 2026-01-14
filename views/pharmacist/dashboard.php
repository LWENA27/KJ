<?php
$title = 'Pharmacy Dashboard';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800">Pharmacy Dashboard</h1>
            <p class="text-neutral-500 mt-1">Prescription dispensing and inventory management</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= htmlspecialchars($BASE_PATH) ?>/pharmacist/prescriptions" 
               class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all shadow-sm">
                <i class="fas fa-pills mr-2"></i>View Prescriptions
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Pending Prescriptions -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-500">Pending Prescriptions</p>
                    <p class="text-2xl font-bold text-neutral-800 mt-1"><?= $pending_stats['pending_prescriptions'] ?? 0 ?></p>
                    <p class="text-xs text-blue-600 mt-2">
                        <i class="fas fa-user mr-1"></i><?= $pending_stats['pending_patients'] ?? 0 ?> patient(s)
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-prescription text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Dispensed Today -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-500">Dispensed Today</p>
                    <p class="text-2xl font-bold text-neutral-800 mt-1"><?= $today_stats['dispensed_today'] ?? 0 ?></p>
                    <p class="text-xs text-green-600 mt-2">
                        <i class="fas fa-users mr-1"></i><?= $today_stats['patients_served'] ?? 0 ?> patient(s) served
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-500">Low Stock Items</p>
                    <p class="text-2xl font-bold text-neutral-800 mt-1"><?= count($low_stock_medicines ?? []) ?></p>
                    <p class="text-xs text-orange-600 mt-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Needs restocking
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-boxes text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Expiring Soon -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-500">Expiring Soon</p>
                    <p class="text-2xl font-bold text-neutral-800 mt-1"><?= count($expiring_medicines ?? []) ?></p>
                    <p class="text-xs text-red-600 mt-2">
                        <i class="fas fa-clock mr-1"></i>Within 60 days
                    </p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-calendar-times text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Medicines -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200">
            <div class="p-6 border-b border-neutral-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-neutral-800">
                        <i class="fas fa-exclamation-circle text-orange-500 mr-2"></i>Low Stock Alert
                    </h2>
                    <a href="<?= htmlspecialchars($BASE_PATH) ?>/pharmacist/inventory" class="text-sm text-blue-600 hover:text-blue-700">
                        View Inventory <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($low_stock_medicines)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-4xl text-green-400 mb-2"></i>
                    <p class="text-neutral-500">All medicines are adequately stocked</p>
                </div>
                <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($low_stock_medicines as $med): ?>
                    <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                        <div>
                            <p class="font-medium text-neutral-800"><?= htmlspecialchars($med['name']) ?></p>
                            <p class="text-xs text-neutral-500"><?= htmlspecialchars($med['generic_name'] ?? '') ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold <?= $med['total_stock'] <= 5 ? 'text-red-600' : 'text-orange-600' ?>">
                                <?= $med['total_stock'] ?> units
                            </p>
                            <?php if ($med['nearest_expiry']): ?>
                            <p class="text-xs text-neutral-500">Exp: <?= date('M Y', strtotime($med['nearest_expiry'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Expiring Medicines -->
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200">
            <div class="p-6 border-b border-neutral-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-neutral-800">
                        <i class="fas fa-calendar-times text-red-500 mr-2"></i>Expiring Soon
                    </h2>
                    <a href="<?= htmlspecialchars($BASE_PATH) ?>/pharmacist/inventory" class="text-sm text-blue-600 hover:text-blue-700">
                        Manage <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <?php if (empty($expiring_medicines)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-4xl text-green-400 mb-2"></i>
                    <p class="text-neutral-500">No medicines expiring soon</p>
                </div>
                <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($expiring_medicines as $med): ?>
                    <div class="flex items-center justify-between p-3 <?= $med['days_until_expiry'] < 0 ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200' ?> rounded-lg border">
                        <div>
                            <p class="font-medium text-neutral-800"><?= htmlspecialchars($med['name']) ?></p>
                            <p class="text-xs text-neutral-500">Batch: <?= htmlspecialchars($med['batch_number']) ?></p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold <?= $med['days_until_expiry'] < 0 ? 'text-red-600' : 'text-yellow-600' ?>">
                                <?php if ($med['days_until_expiry'] < 0): ?>
                                    EXPIRED
                                <?php else: ?>
                                    <?= $med['days_until_expiry'] ?> days
                                <?php endif; ?>
                            </p>
                            <p class="text-xs text-neutral-500"><?= $med['quantity_remaining'] ?> units</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Dispensing Activity -->
    <div class="bg-white rounded-xl shadow-sm border border-neutral-200">
        <div class="p-6 border-b border-neutral-200">
            <h2 class="text-lg font-semibold text-neutral-800">Recent Dispensing Activity</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Medicine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Dispensed By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                    <?php if (empty($recent_dispensing)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-neutral-500">
                            <i class="fas fa-pills text-4xl text-neutral-300 mb-2"></i>
                            <p>No recent dispensing activity</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($recent_dispensing as $item): ?>
                    <tr class="hover:bg-neutral-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-neutral-800"><?= htmlspecialchars($item['patient_name']) ?></p>
                        </td>
                        <td class="px-6 py-4 text-neutral-600">
                            <?= htmlspecialchars($item['medicine_name']) ?>
                        </td>
                        <td class="px-6 py-4 text-neutral-600">
                            <?= htmlspecialchars($item['dispensed_by_name'] ?? 'Unknown') ?>
                        </td>
                        <td class="px-6 py-4 text-neutral-500 text-sm">
                            <?= $item['dispensed_at'] ? date('M d, H:i', strtotime($item['dispensed_at'])) : 'N/A' ?>
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
        <a href="<?= htmlspecialchars($BASE_PATH) ?>/pharmacist/prescriptions" 
           class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <i class="fas fa-pills text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-neutral-800">Dispense Medicines</h3>
                    <p class="text-sm text-neutral-500">Process prescriptions</p>
                </div>
            </div>
        </a>

        <a href="<?= htmlspecialchars($BASE_PATH) ?>/pharmacist/inventory" 
           class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <i class="fas fa-boxes text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-neutral-800">Manage Inventory</h3>
                    <p class="text-sm text-neutral-500">Stock & batches</p>
                </div>
            </div>
        </a>

        <a href="<?= htmlspecialchars($BASE_PATH) ?>/pharmacist/inventory?filter=low_stock" 
           class="bg-white rounded-xl shadow-sm border border-neutral-200 p-6 hover:shadow-md transition-shadow group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <i class="fas fa-clipboard-list text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-neutral-800">Restock List</h3>
                    <p class="text-sm text-neutral-500">Items to reorder</p>
                </div>
            </div>
        </a>
    </div>
</div>
