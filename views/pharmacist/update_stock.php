<?php
$pageTitle = 'Update Stock - Pharmacist';
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
                    <div class="p-3 bg-gradient-to-r from-teal-500 to-cyan-500 rounded-xl shadow-lg">
                        <i class="fas fa-boxes text-white text-xl"></i>
                    </div>
                    Update Medicine Stock
                </h1>
                <p class="text-gray-600 mt-2">Add stock to existing medicines or update quantities</p>
            </div>
            <a href="<?= $BASE_PATH ?>/pharmacist/inventory" class="btn bg-gray-600 text-white hover:bg-gray-700 px-4 py-2 rounded-lg shadow-md transition-all">
                <i class="fas fa-arrow-left mr-2"></i>Back to Inventory
            </a>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-green-800"><?= htmlspecialchars($_SESSION['success']) ?></span>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <span class="text-red-800"><?= htmlspecialchars($_SESSION['error']) ?></span>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Update Stock Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-teal-600 to-cyan-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i>
                            Add Stock to Medicine
                        </h2>
                    </div>
                    <form method="POST" action="<?= $BASE_PATH ?>/pharmacist/update_stock" class="p-6 space-y-6">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        
                        <!-- Medicine Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-pills text-teal-500 mr-2"></i>Select Medicine *
                            </label>
                            <select name="medicine_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-gray-50 focus:bg-white">
                                <option value="">-- Choose a medicine --</option>
                                <?php foreach ($medicines ?? [] as $med): ?>
                                    <option value="<?= $med['id'] ?>" 
                                            data-stock="<?= $med['stock_quantity'] ?? 0 ?>"
                                            data-unit="<?= htmlspecialchars($med['unit'] ?? 'units') ?>">
                                        <?= htmlspecialchars($med['name']) ?> 
                                        (Current: <?= $med['stock_quantity'] ?? 0 ?> <?= htmlspecialchars($med['unit'] ?? 'units') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Only predefined medicines can be stocked. Contact admin to add new medicines.
                            </p>
                        </div>

                        <!-- Quantity and Operation -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-hashtag text-teal-500 mr-2"></i>Quantity to Add *
                                </label>
                                <input type="number" name="quantity" min="1" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-gray-50 focus:bg-white"
                                       placeholder="Enter quantity">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-exchange-alt text-teal-500 mr-2"></i>Operation Type
                                </label>
                                <select name="operation" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-gray-50 focus:bg-white">
                                    <option value="add">Add to Stock (Received)</option>
                                    <option value="adjust">Adjust Stock (Correction)</option>
                                    <option value="remove">Remove from Stock (Expired/Damaged)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Batch and Expiry -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-barcode text-teal-500 mr-2"></i>Batch Number
                                </label>
                                <input type="text" name="batch_number"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-gray-50 focus:bg-white"
                                       placeholder="e.g., BTH-2024-001">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar-alt text-teal-500 mr-2"></i>Expiry Date
                                </label>
                                <input type="date" name="expiry_date"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-gray-50 focus:bg-white"
                                       min="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <!-- Purchase Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-truck text-teal-500 mr-2"></i>Supplier
                                </label>
                                <input type="text" name="supplier"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-gray-50 focus:bg-white"
                                       placeholder="Supplier name">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-money-bill text-teal-500 mr-2"></i>Purchase Price (per unit)
                                </label>
                                <input type="number" name="purchase_price" step="0.01" min="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-gray-50 focus:bg-white"
                                       placeholder="TZS 0.00">
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-sticky-note text-teal-500 mr-2"></i>Notes
                            </label>
                            <textarea name="notes" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-gray-50 focus:bg-white"
                                      placeholder="Optional notes about this stock update"></textarea>
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" class="flex-1 bg-teal-600 text-white py-3 rounded-lg hover:bg-teal-700 transition-all font-semibold">
                                <i class="fas fa-save mr-2"></i>Update Stock
                            </button>
                            <button type="reset" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all">
                                <i class="fas fa-undo mr-2"></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Reference -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Low Stock Alert -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-red-500 to-orange-500 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            Low Stock Alert
                        </h3>
                    </div>
                    <div class="p-4 max-h-64 overflow-y-auto">
                        <?php if (empty($low_stock_medicines ?? [])): ?>
                            <div class="text-center py-4 text-gray-500">
                                <i class="fas fa-check-circle text-2xl text-green-500 mb-2"></i>
                                <p>All medicines are well-stocked!</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($low_stock_medicines as $med): ?>
                                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900"><?= htmlspecialchars($med['name']) ?></p>
                                            <p class="text-sm text-red-600">Only <?= $med['stock_quantity'] ?> <?= $med['unit'] ?? 'units' ?> left</p>
                                        </div>
                                        <button onclick="selectMedicine(<?= $med['id'] ?>)" 
                                                class="px-3 py-1 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                                            Restock
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Stock Updates -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="bg-gradient-to-r from-gray-600 to-gray-800 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-clock"></i>
                            Recent Updates
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-200 max-h-64 overflow-y-auto">
                        <?php if (empty($recent_updates ?? [])): ?>
                            <div class="p-4 text-center text-gray-500">
                                <p>No recent stock updates</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_updates as $update): ?>
                                <div class="p-4 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900"><?= htmlspecialchars($update['medicine_name']) ?></p>
                                            <p class="text-sm text-gray-500">
                                                <?= $update['operation'] === 'add' ? '+' : ($update['operation'] === 'remove' ? '-' : '±') ?>
                                                <?= $update['quantity'] ?> <?= $update['unit'] ?? 'units' ?>
                                            </p>
                                        </div>
                                        <span class="text-xs text-gray-400">
                                            <?= date('M d, H:i', strtotime($update['created_at'])) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                    <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        Important Notes
                    </h3>
                    <ul class="space-y-2 text-sm text-blue-800">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-blue-500 mt-1"></i>
                            <span>Only stock updates are allowed. New medicines must be added by admin.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-blue-500 mt-1"></i>
                            <span>Always record batch number and expiry date for tracking.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-blue-500 mt-1"></i>
                            <span>Stock removals should include reason in notes.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-blue-500 mt-1"></i>
                            <span>All stock changes are logged for audit purposes.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectMedicine(medicineId) {
    const select = document.querySelector('select[name="medicine_id"]');
    if (select) {
        select.value = medicineId;
        select.scrollIntoView({ behavior: 'smooth', block: 'center' });
        select.focus();
    }
}
</script>
