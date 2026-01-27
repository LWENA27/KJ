<?php
$pageTitle = "Bed Management";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Bed Management</h1>
        <a href="<?php echo BASE_PATH; ?>/ipd/dashboard" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Back to Dashboard</a>
    </div>

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
                                <p class="font-semibold text-sm"><?php echo htmlspecialchars($bed['first_name'] . ' ' . $bed['last_name']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($bed['patient_number'] ?? ''); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
