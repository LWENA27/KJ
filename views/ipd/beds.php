<?php
$pageTitle = "Bed Management";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Bed Management</h1>
        <div class="flex space-x-2">
            <a href="<?php echo BASE_PATH; ?>/ipd/wards" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                <i class="fas fa-building mr-2"></i>Manage Wards
            </a>
            <a href="<?php echo BASE_PATH; ?>/ipd/add_bed" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>Add Bed
            </a>
            <a href="<?php echo BASE_PATH; ?>/ipd/dashboard" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Back to Dashboard</a>
        </div>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Ward Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex items-end space-x-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Ward</label>
                <select name="ward_id" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">All Wards</option>
                    <?php foreach ($wards as $ward): ?>
                        <option value="<?php echo $ward['id']; ?>" <?php echo $selected_ward_id == $ward['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($ward['ward_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Filter</button>
        </form>
    </div>

    <!-- Beds Grid -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 p-6">
            <?php foreach ($beds as $bed): ?>
                <div class="border rounded-lg p-4 <?php echo $bed['status'] === 'occupied' ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200'; ?>">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-bold text-gray-800"><?php echo htmlspecialchars($bed['bed_number']); ?></h3>
                            <p class="text-xs text-gray-600"><?php echo htmlspecialchars($bed['ward_name']); ?></p>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full font-semibold
                            <?php 
                            echo $bed['status'] === 'available' ? 'bg-green-100 text-green-800' :
                                ($bed['status'] === 'occupied' ? 'bg-red-100 text-red-800' :
                                ($bed['status'] === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'));
                            ?>">
                            <?php echo ucfirst($bed['status']); ?>
                        </span>
                    </div>
                    <div class="space-y-1 text-sm">
                        <p class="text-gray-600">Type: <span class="font-semibold"><?php echo ucfirst($bed['bed_type']); ?></span></p>
                        <p class="text-gray-600">Rate: <span class="font-semibold"><?php echo number_format($bed['daily_rate']); ?> TZS/day</span></p>
                        <?php if ($bed['status'] === 'occupied' && $bed['patient_id']): ?>
                            <div class="mt-3 pt-3 border-t">
                                <p class="text-xs text-gray-600">Patient:</p>
                                <p class="font-semibold text-sm"><?php echo htmlspecialchars(($bed['first_name'] ?? '') . ' ' . ($bed['last_name'] ?? '')); ?></p>
                                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($bed['registration_number'] ?? ''); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Action Buttons -->
                    <div class="mt-4 pt-3 border-t flex justify-between">
                        <a href="<?php echo BASE_PATH; ?>/ipd/edit_bed/<?php echo $bed['id']; ?>" 
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <?php if ($bed['status'] !== 'occupied'): ?>
                            <a href="<?php echo BASE_PATH; ?>/ipd/delete_bed/<?php echo $bed['id']; ?>" 
                               onclick="return confirm('Are you sure you want to delete this bed?')"
                               class="text-red-600 hover:text-red-800 text-sm">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </a>
                        <?php else: ?>
                            <span class="text-gray-400 text-sm" title="Cannot delete occupied bed">
                                <i class="fas fa-lock mr-1"></i>In Use
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
