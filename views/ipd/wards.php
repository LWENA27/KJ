<?php
$pageTitle = "Ward Management";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Ward Management</h1>
        <div class="flex space-x-2">
            <a href="<?php echo BASE_PATH; ?>/ipd/add_ward" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>Add Ward
            </a>
            <a href="<?php echo BASE_PATH; ?>/ipd/beds" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-bed mr-2"></i>Manage Beds
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

    <!-- Wards Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ward Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Floor</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Beds</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Occupied</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($wards)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-building text-4xl mb-2"></i>
                            <p>No wards found. Click "Add Ward" to create one.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($wards as $ward): ?>
                        <?php 
                            $available = $ward['actual_beds'] - $ward['actual_occupied'];
                            $occupancy = $ward['actual_beds'] > 0 ? round(($ward['actual_occupied'] / $ward['actual_beds']) * 100) : 0;
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($ward['ward_name']); ?></div>
                                <?php if ($ward['description']): ?>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars(substr($ward['description'], 0, 50)); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($ward['ward_code']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full font-semibold
                                    <?php 
                                    echo match($ward['ward_type']) {
                                        'icu' => 'bg-red-100 text-red-800',
                                        'private' => 'bg-purple-100 text-purple-800',
                                        'maternity' => 'bg-pink-100 text-pink-800',
                                        'pediatric' => 'bg-blue-100 text-blue-800',
                                        'isolation' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-green-100 text-green-800'
                                    };
                                    ?>">
                                    <?php echo ucfirst($ward['ward_type']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Floor <?php echo $ward['floor_number'] ?? 1; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold">
                                <?php echo $ward['actual_beds']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-semibold <?php echo $ward['actual_occupied'] > 0 ? 'text-red-600' : 'text-gray-500'; ?>">
                                    <?php echo $ward['actual_occupied']; ?>
                                </span>
                                <?php if ($ward['actual_beds'] > 0): ?>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                        <div class="bg-red-500 h-1.5 rounded-full" style="width: <?php echo $occupancy; ?>%"></div>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-semibold <?php echo $available > 0 ? 'text-green-600' : 'text-gray-500'; ?>">
                                    <?php echo $available; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center space-x-3">
                                    <a href="<?php echo BASE_PATH; ?>/ipd/beds?ward_id=<?php echo $ward['id']; ?>" 
                                       class="text-gray-600 hover:text-gray-800" title="View Beds">
                                        <i class="fas fa-bed"></i>
                                    </a>
                                    <a href="<?php echo BASE_PATH; ?>/ipd/edit_ward/<?php echo $ward['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-800" title="Edit Ward">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($ward['actual_occupied'] == 0): ?>
                                        <a href="<?php echo BASE_PATH; ?>/ipd/delete_ward/<?php echo $ward['id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this ward? All beds in this ward will also be deleted.')"
                                           class="text-red-600 hover:text-red-800" title="Delete Ward">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400" title="Cannot delete ward with occupied beds">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <?php 
            $totalBeds = array_sum(array_column($wards, 'actual_beds'));
            $totalOccupied = array_sum(array_column($wards, 'actual_occupied'));
            $totalAvailable = $totalBeds - $totalOccupied;
            $overallOccupancy = $totalBeds > 0 ? round(($totalOccupied / $totalBeds) * 100) : 0;
        ?>
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <div class="text-blue-600 text-sm font-medium">Total Wards</div>
            <div class="text-3xl font-bold text-blue-800"><?php echo count($wards); ?></div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="text-gray-600 text-sm font-medium">Total Beds</div>
            <div class="text-3xl font-bold text-gray-800"><?php echo $totalBeds; ?></div>
        </div>
        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
            <div class="text-red-600 text-sm font-medium">Occupied</div>
            <div class="text-3xl font-bold text-red-800"><?php echo $totalOccupied; ?></div>
        </div>
        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <div class="text-green-600 text-sm font-medium">Available</div>
            <div class="text-3xl font-bold text-green-800"><?php echo $totalAvailable; ?></div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
