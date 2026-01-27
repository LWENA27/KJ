<?php
$pageTitle = "Edit Bed";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Bed: <?php echo htmlspecialchars($bed['bed_number']); ?></h1>
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
        <form method="POST" action="<?php echo BASE_PATH; ?>/ipd/edit_bed/<?php echo $bed['id']; ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Ward -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ward <span class="text-red-500">*</span></label>
                    <select name="ward_id" required class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Ward</option>
                        <?php foreach ($wards as $ward): ?>
                            <option value="<?php echo $ward['id']; ?>" <?php echo $bed['ward_id'] == $ward['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ward['ward_name']); ?> (<?php echo ucfirst($ward['ward_type']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Bed Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bed Number <span class="text-red-500">*</span></label>
                    <input type="text" name="bed_number" required value="<?php echo htmlspecialchars($bed['bed_number']); ?>"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Bed Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bed Type</label>
                    <select name="bed_type" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php foreach ($bed_types as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo $bed['bed_type'] == $type ? 'selected' : ''; ?>>
                                <?php echo ucfirst($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                            <?php echo $bed['status'] === 'occupied' ? 'disabled' : ''; ?>>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?php echo $status; ?>" <?php echo $bed['status'] == $status ? 'selected' : ''; ?>>
                                <?php echo ucfirst($status); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($bed['status'] === 'occupied'): ?>
                        <p class="text-xs text-yellow-600 mt-1">
                            <i class="fas fa-info-circle"></i> Status cannot be changed while bed is occupied
                        </p>
                        <input type="hidden" name="status" value="occupied">
                    <?php endif; ?>
                </div>

                <!-- Daily Rate -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Daily Rate (TZS)</label>
                    <input type="number" name="daily_rate" min="0" step="100" value="<?php echo $bed['daily_rate']; ?>"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" placeholder="Optional notes about this bed..."
                              class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($bed['notes'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="<?php echo BASE_PATH; ?>/ipd/beds" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
