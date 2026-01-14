<?php
$title = 'Medicine Inventory - Pharmacy';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800">Medicine Inventory</h1>
            <p class="text-neutral-500 mt-1">Manage stock levels and batches</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openStockModal()" 
                    class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all shadow-sm">
                <i class="fas fa-plus mr-2"></i>Add Stock
            </button>
        </div>
    </div>

    <!-- Notifications -->
    <?php if (!empty($notifications)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-4">
        <h3 class="font-semibold text-neutral-800 mb-3">
            <i class="fas fa-bell text-orange-500 mr-2"></i>Alerts (<?= count($notifications) ?>)
        </h3>
        <div class="flex flex-wrap gap-2">
            <?php foreach (array_slice($notifications, 0, 5) as $notif): ?>
            <span class="px-3 py-1 rounded-full text-sm
                <?php if ($notif['type'] === 'expired'): ?>
                    bg-red-100 text-red-700
                <?php elseif ($notif['type'] === 'low_stock'): ?>
                    bg-orange-100 text-orange-700
                <?php else: ?>
                    bg-yellow-100 text-yellow-700
                <?php endif; ?>">
                <?= htmlspecialchars($notif['message']) ?>
            </span>
            <?php endforeach; ?>
            <?php if (count($notifications) > 5): ?>
            <span class="px-3 py-1 bg-neutral-100 text-neutral-600 rounded-full text-sm">
                +<?= count($notifications) - 5 ?> more
            </span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-4">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" id="searchInput" placeholder="Search medicines..." 
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       onkeyup="filterMedicines()">
            </div>
            <select id="categoryFilter" onchange="filterMedicines()"
                    class="px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                <?php endforeach; ?>
            </select>
            <select id="stockFilter" onchange="filterMedicines()"
                    class="px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="">All Stock Levels</option>
                <option value="low">Low Stock (≤10)</option>
                <option value="out">Out of Stock</option>
                <option value="expiring">Expiring Soon</option>
            </select>
        </div>
    </div>

    <!-- Medicines Table -->
    <div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="medicinesTable">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Medicine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Unit Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Batches</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Nearest Expiry</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200">
                    <?php if (empty($medicines)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-neutral-500">
                            <i class="fas fa-pills text-5xl text-neutral-300 mb-4"></i>
                            <p>No medicines in inventory</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($medicines as $med): ?>
                    <tr class="hover:bg-neutral-50 medicine-row" 
                        data-name="<?= htmlspecialchars(strtolower($med['name'] . ' ' . ($med['generic_name'] ?? ''))) ?>"
                        data-category="<?= htmlspecialchars($med['category'] ?? '') ?>"
                        data-stock="<?= $med['stock_quantity'] ?>"
                        data-expiry="<?= $med['nearest_expiry'] ?? '' ?>">
                        <td class="px-6 py-4">
                            <p class="font-medium text-neutral-800"><?= htmlspecialchars($med['name']) ?></p>
                            <p class="text-xs text-neutral-500"><?= htmlspecialchars($med['generic_name'] ?? '') ?></p>
                            <?php if ($med['strength']): ?>
                            <p class="text-xs text-blue-600"><?= htmlspecialchars($med['strength']) ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-neutral-600">
                            <?= htmlspecialchars($med['category'] ?? 'N/A') ?>
                        </td>
                        <td class="px-6 py-4 text-neutral-800">
                            TZS <?= number_format($med['unit_price'], 0) ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($med['stock_quantity'] <= 0): ?>
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium">Out of Stock</span>
                            <?php elseif ($med['stock_quantity'] <= 10): ?>
                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-medium"><?= $med['stock_quantity'] ?> units</span>
                            <?php else: ?>
                            <span class="text-green-600 font-medium"><?= $med['stock_quantity'] ?> units</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-neutral-600">
                            <?= $med['batch_count'] ?> batch(es)
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($med['nearest_expiry']): ?>
                                <?php 
                                $expiry = new DateTime($med['nearest_expiry']);
                                $today = new DateTime('today');
                                $diff = $today->diff($expiry);
                                ?>
                                <?php if ($expiry < $today): ?>
                                <span class="text-red-600 font-medium">EXPIRED</span>
                                <?php elseif ($diff->days <= 30): ?>
                                <span class="text-red-600"><?= date('M d, Y', strtotime($med['nearest_expiry'])) ?></span>
                                <?php elseif ($diff->days <= 60): ?>
                                <span class="text-yellow-600"><?= date('M d, Y', strtotime($med['nearest_expiry'])) ?></span>
                                <?php else: ?>
                                <span class="text-neutral-600"><?= date('M d, Y', strtotime($med['nearest_expiry'])) ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                            <span class="text-neutral-400">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button onclick="openStockModal(<?= $med['id'] ?>, '<?= htmlspecialchars(addslashes($med['name'])) ?>')"
                                        class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Add Stock">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <a href="<?= htmlspecialchars($BASE_PATH) ?>/pharmacist/batches?medicine_id=<?= $med['id'] ?>"
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="View Batches">
                                    <i class="fas fa-boxes"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Stock Modal -->
<div id="stockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-neutral-200">
            <h3 class="text-lg font-semibold text-neutral-800">Add Stock</h3>
        </div>
        <form action="<?= htmlspecialchars($BASE_PATH) ?>/pharmacist/update_stock" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="action" value="add">

            <div>
                <label for="medicine_id" class="block text-sm font-medium text-neutral-700 mb-1">Medicine</label>
                <select name="medicine_id" id="modal_medicine_id" required
                        class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">Select Medicine</option>
                    <?php foreach ($medicines as $med): ?>
                    <option value="<?= $med['id'] ?>"><?= htmlspecialchars($med['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="quantity" class="block text-sm font-medium text-neutral-700 mb-1">Quantity</label>
                <input type="number" name="quantity" id="quantity" required min="1"
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label for="batch_number" class="block text-sm font-medium text-neutral-700 mb-1">Batch Number (Optional)</label>
                <input type="text" name="batch_number" id="batch_number"
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label for="expiry_date" class="block text-sm font-medium text-neutral-700 mb-1">Expiry Date</label>
                <input type="date" name="expiry_date" id="expiry_date"
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label for="supplier" class="block text-sm font-medium text-neutral-700 mb-1">Supplier (Optional)</label>
                <input type="text" name="supplier" id="supplier"
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label for="cost_price" class="block text-sm font-medium text-neutral-700 mb-1">Cost Price (Optional)</label>
                <input type="number" name="cost_price" id="cost_price" step="0.01" min="0"
                       class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeStockModal()"
                        class="flex-1 px-4 py-2 border border-neutral-300 text-neutral-700 rounded-lg hover:bg-neutral-50">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    Add Stock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openStockModal(medicineId = null, medicineName = null) {
    if (medicineId) {
        document.getElementById('modal_medicine_id').value = medicineId;
    }
    document.getElementById('stockModal').classList.remove('hidden');
}

function closeStockModal() {
    document.getElementById('stockModal').classList.add('hidden');
}

function filterMedicines() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value.toLowerCase();
    const stockFilter = document.getElementById('stockFilter').value;
    const today = new Date();
    const sixtyDaysLater = new Date(today.getTime() + 60 * 24 * 60 * 60 * 1000);

    document.querySelectorAll('.medicine-row').forEach(row => {
        const name = row.dataset.name;
        const rowCategory = row.dataset.category.toLowerCase();
        const stock = parseInt(row.dataset.stock);
        const expiry = row.dataset.expiry ? new Date(row.dataset.expiry) : null;

        let show = true;

        // Search filter
        if (search && !name.includes(search)) {
            show = false;
        }

        // Category filter
        if (category && rowCategory !== category) {
            show = false;
        }

        // Stock filter
        if (stockFilter === 'low' && stock > 10) {
            show = false;
        } else if (stockFilter === 'out' && stock > 0) {
            show = false;
        } else if (stockFilter === 'expiring' && (!expiry || expiry > sixtyDaysLater)) {
            show = false;
        }

        row.style.display = show ? '' : 'none';
    });
}
</script>
