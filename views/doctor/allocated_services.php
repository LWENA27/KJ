<?php $title = "Allocated Services"; ?>

<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="text-white">
                <h1 class="text-3xl font-bold flex items-center">
                    <i class="fas fa-tasks mr-3"></i>
                    Allocated Services
                </h1>
                <p class="mt-2 text-indigo-100 text-lg">Monitor services you've allocated to staff members</p>
            </div>
            <div class="flex space-x-3">
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/dashboard" 
                   class="bg-white text-indigo-700 hover:bg-indigo-50 px-6 py-3 rounded-lg font-medium transition-all duration-300 shadow-lg flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Allocated -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Allocated</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo count($allocations); ?></p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-clipboard-list text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600"><?php echo $pending_count; ?></p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">In Progress</p>
                    <p class="text-3xl font-bold text-blue-600"><?php echo $in_progress_count; ?></p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-spinner text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Completed</p>
                    <p class="text-3xl font-bold text-green-600"><?php echo $completed_count; ?></p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-lg shadow-lg mb-6 overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-4 px-6" aria-label="Tabs">
                <button onclick="filterByStatus('all')" 
                        class="filter-tab active py-4 px-3 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600">
                    All Services (<?php echo count($allocations); ?>)
                </button>
                <button onclick="filterByStatus('pending')" 
                        class="filter-tab py-4 px-3 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Pending (<?php echo $pending_count; ?>)
                </button>
                <button onclick="filterByStatus('in_progress')" 
                        class="filter-tab py-4 px-3 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    In Progress (<?php echo $in_progress_count; ?>)
                </button>
                <button onclick="filterByStatus('completed')" 
                        class="filter-tab py-4 px-3 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Completed (<?php echo $completed_count; ?>)
                </button>
                <button onclick="filterByStatus('unpaid')" 
                        class="filter-tab py-4 px-3 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Unpaid (<?php echo $unpaid_count; ?>)
                </button>
            </nav>
        </div>
    </div>

    <!-- Allocations Table -->
    <?php if (empty($allocations)): ?>
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-tasks text-gray-400 text-4xl"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">No Services Allocated</h3>
            <p class="text-gray-600 text-lg">Services you allocate to staff will appear here.</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Service
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assigned To
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Visit Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payment
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Allocated
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($allocations as $allocation): ?>
                            <tr class="hover:bg-gray-50 transition-colors service-row" 
                                data-status="<?php echo htmlspecialchars($allocation['status']); ?>"
                                data-payment="<?php echo $allocation['payment_count'] > 0 ? 'paid' : 'unpaid'; ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
                                                <?php echo strtoupper(substr($allocation['first_name'], 0, 1) . substr($allocation['last_name'], 0, 1)); ?>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($allocation['first_name'] . ' ' . $allocation['last_name']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($allocation['registration_number']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($allocation['service_name']); ?>
                                    </div>
                                    <?php if ($allocation['service_description']): ?>
                                        <div class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars($allocation['service_description']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="text-sm text-indigo-600 font-semibold mt-1">
                                        Tsh <?php echo number_format($allocation['price'], 0); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($allocation['staff_first']): ?>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($allocation['staff_first'] . ' ' . $allocation['staff_last']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars(ucfirst($allocation['staff_role'])); ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400 italic">Not assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo date('M d, Y', strtotime($allocation['visit_date'])); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo date('h:i A', strtotime($allocation['visit_date'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $status_colors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                    $status_color = $status_colors[$allocation['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $status_color; ?>">
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $allocation['status']))); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($allocation['payment_count'] > 0): ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Paid
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-circle mr-1"></i>Unpaid
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo date('M d, Y', strtotime($allocation['created_at'])); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo date('h:i A', strtotime($allocation['created_at'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_patient/<?php echo $allocation['patient_id']; ?>" 
                                       class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Patient
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function filterByStatus(status) {
    const rows = document.querySelectorAll('.service-row');
    const tabs = document.querySelectorAll('.filter-tab');
    
    // Update tab styles
    tabs.forEach(tab => {
        tab.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    event.target.classList.add('active', 'border-indigo-500', 'text-indigo-600');
    event.target.classList.remove('border-transparent', 'text-gray-500');
    
    // Filter rows
    rows.forEach(row => {
        const rowStatus = row.dataset.status;
        const rowPayment = row.dataset.payment;
        
        if (status === 'all') {
            row.style.display = '';
        } else if (status === 'unpaid') {
            row.style.display = rowPayment === 'unpaid' ? '' : 'none';
        } else {
            row.style.display = rowStatus === status ? '' : 'none';
        }
    });
}
</script>

<style>
.filter-tab.active {
    border-color: rgb(99 102 241) !important;
    color: rgb(79 70 229) !important;
}
</style>
