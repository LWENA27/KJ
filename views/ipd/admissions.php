<?php
$pageTitle = "IPD Admissions";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">IPD Admissions</h1>
        <a href="<?php echo BASE_PATH; ?>/ipd/admit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">New Admission</a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="discharged" <?php echo $status === 'discharged' ? 'selected' : ''; ?>>Discharged</option>
                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input type="text" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>" 
                       placeholder="Patient name or admission #" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 w-full">Filter</button>
            </div>
        </form>
    </div>

    <!-- Admissions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Admission #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ward/Bed</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Admitted</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($admissions)): ?>
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No admissions found</td></tr>
                <?php else: ?>
                    <?php foreach ($admissions as $admission): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($admission['admission_number'] ?? ''); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars(($admission['first_name'] ?? '') . ' ' . ($admission['last_name'] ?? '')); ?>
                                </div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($admission['patient_number'] ?? ''); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars((($admission['ward_name'] ?? '') !== '' ? $admission['ward_name'] : '') . (isset($admission['bed_number']) && $admission['bed_number'] !== null ? ' - ' . $admission['bed_number'] : '')); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo isset($admission['admission_datetime']) && $admission['admission_datetime'] ? date('M d, Y', strtotime($admission['admission_datetime'])) : ''; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo isset($admission['total_days']) ? (int)$admission['total_days'] : 0; ?> days
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    <?php echo (($admission['status'] ?? '') === 'active') ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($admission['status'] ?? '')); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?php echo BASE_PATH; ?>/ipd/view_admission/<?php echo $admission['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
