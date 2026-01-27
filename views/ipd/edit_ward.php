<?php
$pageTitle = "Edit Ward";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Ward: <?php echo htmlspecialchars($ward['ward_name']); ?></h1>
        <a href="<?php echo BASE_PATH; ?>/ipd/wards" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Wards
        </a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form method="POST" action="<?php echo BASE_PATH; ?>/ipd/edit_ward/<?php echo $ward['id']; ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Ward Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ward Name <span class="text-red-500">*</span></label>
                    <input type="text" name="ward_name" required value="<?php echo htmlspecialchars($ward['ward_name']); ?>"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Ward Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ward Code <span class="text-red-500">*</span></label>
                    <input type="text" name="ward_code" required value="<?php echo htmlspecialchars($ward['ward_code']); ?>"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Unique short code for the ward</p>
                </div>

                <!-- Ward Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ward Type</label>
                    <select name="ward_type" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php foreach ($ward_types as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo $ward['ward_type'] == $type ? 'selected' : ''; ?>>
                                <?php echo ucfirst($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Floor Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Floor Number</label>
                    <input type="number" name="floor_number" min="0" value="<?php echo $ward['floor_number'] ?? 1; ?>"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" placeholder="Optional description of this ward..."
                              class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($ward['description'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Ward Statistics -->
            <div class="mt-6 bg-gray-50 border border-gray-200 rounded p-4">
                <h3 class="font-medium text-gray-700 mb-2">Ward Statistics</h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-gray-800"><?php echo $ward['total_beds']; ?></div>
                        <div class="text-xs text-gray-500">Total Beds</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-red-600"><?php echo $ward['occupied_beds']; ?></div>
                        <div class="text-xs text-gray-500">Occupied</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600"><?php echo $ward['total_beds'] - $ward['occupied_beds']; ?></div>
                        <div class="text-xs text-gray-500">Available</div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="<?php echo BASE_PATH; ?>/ipd/wards" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">Cancel</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
