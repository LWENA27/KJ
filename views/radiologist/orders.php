<?php
$pageTitle = "Radiology Orders";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Radiology Orders</h1>
        <a href="<?php echo BASE_PATH; ?>/radiologist/dashboard" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            Back to Dashboard
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All</option>
                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="scheduled" <?php echo $status === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                    <option value="in_progress" <?php echo $status === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                <select name="priority" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="all" <?php echo $priority === 'all' ? 'selected' : ''; ?>>All</option>
                    <option value="stat" <?php echo $priority === 'stat' ? 'selected' : ''; ?>>STAT</option>
                    <option value="urgent" <?php echo $priority === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                    <option value="normal" <?php echo $priority === 'normal' ? 'selected' : ''; ?>>Normal</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>" 
                       placeholder="Patient name or number" 
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 w-full">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Test</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ordered By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No orders found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #<?php echo $order['id']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?>
                                </div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($order['patient_number'] ?? ''); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($order['test_name']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($order['test_code']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Dr. <?php echo htmlspecialchars($order['doctor_first_name'] . ' ' . $order['doctor_last_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    <?php 
                                    echo $order['priority'] === 'stat' ? 'bg-red-100 text-red-800' : 
                                        ($order['priority'] === 'urgent' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800');
                                    ?>">
                                    <?php echo strtoupper($order['priority']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    <?php 
                                    echo $order['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                                        ($order['status'] === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 
                                        ($order['status'] === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'));
                                    ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if ($order['status'] === 'pending' || $order['status'] === 'scheduled'): ?>
                                    <a href="<?php echo BASE_PATH; ?>/radiologist/perform_test/<?php echo $order['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-900">Start</a>
                                <?php elseif ($order['status'] === 'in_progress'): ?>
                                    <a href="<?php echo BASE_PATH; ?>/radiologist/record_result/<?php echo $order['id']; ?>" 
                                       class="text-green-600 hover:text-green-900">Complete</a>
                                <?php else: ?>
                                    <a href="<?php echo BASE_PATH; ?>/radiologist/view_result/<?php echo $order['id']; ?>" 
                                       class="text-purple-600 hover:text-purple-900">View</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
