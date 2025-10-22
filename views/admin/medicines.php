<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Medicine Inventory</h1>
    <a href="<?= $BASE_PATH ?>/admin/medicines" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            <i class="fas fa-plus mr-2"></i>Add New Medicine
        </a>
    </div>

    <!-- Medicines Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Medicine Stock</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicine</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generic Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($medicines as $medicine): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-pills text-purple-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($medicine['name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($medicine['description'] ?? 'No description'); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo htmlspecialchars($medicine['generic_name'] ?? 'N/A'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                <?php
                                if ($medicine['stock_quantity'] <= 5) {
                                    echo 'bg-red-100 text-red-800';
                                } elseif ($medicine['stock_quantity'] <= 20) {
                                    echo 'bg-yellow-100 text-yellow-800';
                                } else {
                                    echo 'bg-green-100 text-green-800';
                                }
                                ?>">
                                <?php echo $medicine['stock_quantity']; ?> units
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Tsh <?php echo number_format($medicine['unit_price'], 0, '.', ','); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php
                            if ($medicine['expiry_date']) {
                                $expiry = strtotime($medicine['expiry_date']);
                                $today = strtotime('today');
                                $days_until_expiry = floor(($expiry - $today) / (60*60*24));

                                if ($days_until_expiry < 0) {
                                    echo '<span class="text-red-600">Expired</span>';
                                } elseif ($days_until_expiry <= 30) {
                                    echo '<span class="text-yellow-600">' . date('M j, Y', $expiry) . '</span>';
                                } else {
                                    echo date('M j, Y', $expiry);
                                }
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php
                            $status = 'Active';
                            $status_class = 'bg-green-100 text-green-800';

                            if ($medicine['stock_quantity'] <= 5) {
                                $status = 'Low Stock';
                                $status_class = 'bg-red-100 text-red-800';
                            } elseif ($medicine['expiry_date'] && strtotime($medicine['expiry_date']) < strtotime('+30 days')) {
                                $status = 'Expiring Soon';
                                $status_class = 'bg-yellow-100 text-yellow-800';
                            }

                            echo '<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full ' . $status_class . '">' . $status . '</span>';
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <a href="#" class="text-green-600 hover:text-green-900 mr-3">Restock</a>
                            <a href="#" class="text-red-600 hover:text-red-900">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
