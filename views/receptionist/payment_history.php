<?php $title = "Payment History"; ?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Payment History</h1>
                <p class="text-gray-600 mt-1">View all recorded payment transactions</p>
            </div>
            <div class="flex space-x-3">
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/payments" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Pending Payments
                </a>
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/dashboard" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Total Payments -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Payments</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo count($payments); ?></p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-receipt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">
                        Tsh <?php 
                        $total = array_sum(array_column($payments, 'amount')); 
                        echo number_format($total, 0);
                        ?>
                    </p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Today's Payments -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Today's Payments</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        <?php 
                        $today = array_filter($payments, function($p) {
                            return safe_date('Y-m-d', $p['payment_date']) === date('Y-m-d');
                        });
                        echo count($today);
                        ?>
                    </p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <i class="fas fa-calendar-day text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        <?php 
                        $completed = array_filter($payments, function($p) {
                            return $p['payment_status'] === 'paid';
                        });
                        echo count($completed);
                        ?>
                    </p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" action="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/payment_history" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Patient</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                       placeholder="Patient name..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Type</label>
                <select name="payment_type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="registration" <?php echo ($_GET['payment_type'] ?? '') === 'registration' ? 'selected' : ''; ?>>Registration</option>
                    <option value="lab_test" <?php echo ($_GET['payment_type'] ?? '') === 'lab_test' ? 'selected' : ''; ?>>Lab Tests</option>
                    <option value="medicine" <?php echo ($_GET['payment_type'] ?? '') === 'medicine' ? 'selected' : ''; ?>>Medicine</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <select name="payment_method" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Methods</option>
                    <option value="cash" <?php echo ($_GET['payment_method'] ?? '') === 'cash' ? 'selected' : ''; ?>>Cash</option>
                    <option value="card" <?php echo ($_GET['payment_method'] ?? '') === 'card' ? 'selected' : ''; ?>>Card</option>
                    <option value="mobile_money" <?php echo ($_GET['payment_method'] ?? '') === 'mobile_money' ? 'selected' : ''; ?>>Mobile Money</option>
                    <option value="insurance" <?php echo ($_GET['payment_method'] ?? '') === 'insurance' ? 'selected' : ''; ?>>Insurance</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/payment_history" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Payment Records Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-history text-blue-600 mr-2"></i>
                    Payment Records
                </h2>
                <span class="text-sm text-gray-600"><?php echo count($payments); ?> payments</span>
            </div>
        </div>

        <?php if (empty($payments)): ?>
            <div class="p-8 text-center">
                <i class="fas fa-inbox text-gray-400 text-4xl mb-3"></i>
                <p class="text-gray-600">No payment records found</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Method
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($payments as $payment): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-purple-600 flex items-center justify-center text-white font-semibold">
                                                <?php echo strtoupper(substr($payment['patient_name'], 0, 2)); ?>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($payment['patient_name']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-green-600">
                                        Tsh <?php echo number_format($payment['amount'], 0); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center text-sm text-gray-900">
                                        <?php
                                        $icons = [
                                            'cash' => 'fa-money-bill-wave',
                                            'card' => 'fa-credit-card',
                                            'mobile_money' => 'fa-mobile-alt',
                                            'insurance' => 'fa-shield-alt'
                                        ];
                                        $icon = $icons[$payment['payment_method']] ?? 'fa-money-bill-wave';
                                        ?>
                                        <i class="fas <?php echo $icon; ?> text-gray-400 mr-2"></i>
                                        <?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $type_colors = [
                                        'registration' => 'bg-blue-100 text-blue-800',
                                        'lab_test' => 'bg-purple-100 text-purple-800',
                                        'medicine' => 'bg-green-100 text-green-800'
                                    ];
                                    $color = $type_colors[$payment['payment_type']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $color; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $payment['payment_type'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div><?php echo safe_date('M d, Y', $payment['payment_date'], 'N/A'); ?></div>
                                    <div class="text-xs text-gray-500">
                                        <i class="far fa-clock mr-1"></i>
                                        <?php echo safe_date('h:i A', $payment['payment_date'], ''); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Paid
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button onclick="viewPayment(<?php echo $payment['id']; ?>)"
                                            class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="printReceipt(<?php echo $payment['id']; ?>)"
                                            class="text-green-600 hover:text-green-900" title="Print Receipt">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function viewPayment(paymentId) {
    // TODO: Implement view payment details modal
    alert('View payment details for payment ID: ' + paymentId);
}

function printReceipt(paymentId) {
    // TODO: Implement print receipt functionality
    window.open('<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/print_receipt/' + paymentId, '_blank');
}
</script>
