<?php
$pageTitle = "Add New Bed";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Add New Bed</h1>
        <a href="<?php echo BASE_PATH; ?>/ipd/beds" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Beds
        </a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form method="POST" action="<?php echo BASE_PATH; ?>/ipd/add_bed">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Ward -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ward <span class="text-red-500">*</span></label>
                    <select name="ward_id" required class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Ward</option>
                        <?php foreach ($wards as $ward): ?>
                            <option value="<?php echo $ward['id']; ?>">
                                <?php echo htmlspecialchars($ward['ward_name']); ?> (<?php echo ucfirst($ward['ward_type']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Bed Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bed Number <span class="text-red-500">*</span></label>
                    <input type="text" name="bed_number" required placeholder="e.g., A-01"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Bed Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bed Type</label>
                    <select name="bed_type" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php foreach ($bed_types as $type): ?>
                            <option value="<?php echo $type; ?>"><?php echo ucfirst($type); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Daily Rate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Daily Rate (TZS)</label>
                    <input type="number" name="daily_rate" min="0" step="100" value="15000"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" placeholder="Optional notes about this bed..."
                              class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="<?php echo BASE_PATH; ?>/ipd/beds" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">Cancel</a>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>Add Bed
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
