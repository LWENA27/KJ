<div class="space-y-4 sm:space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <div class="text-xs sm:text-sm text-gray-500">
            <?php echo date('l, F j, Y'); ?>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center mb-2">
                        <div class="p-2 bg-blue-100 rounded-lg mr-2 sm:mr-3">
                            <i class="fas fa-users text-blue-600 text-lg sm:text-2xl"></i>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Active Users</p>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_users']); ?></p>
                    <?php if ($stats['inactive_users'] > 0): ?>
                    <p class="text-xs text-gray-500 mt-1"><?php echo $stats['inactive_users']; ?> inactive</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Total Patients -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center mb-2">
                        <div class="p-2 bg-green-100 rounded-lg mr-2 sm:mr-3">
                            <i class="fas fa-user-injured text-green-600 text-lg sm:text-2xl"></i>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Patients</p>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_patients']); ?></p>
                    <p class="text-xs text-green-600 mt-1">+<?php echo $stats['today_patients']; ?> today</p>
                </div>
            </div>
        </div>

        <!-- Today's Consultations -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center mb-2">
                        <div class="p-2 bg-yellow-100 rounded-lg mr-2 sm:mr-3">
                            <i class="fas fa-calendar-check text-yellow-600 text-lg sm:text-2xl"></i>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Consultations</p>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900"><?php echo number_format($stats['today_consultations']); ?></p>
                    <p class="text-xs text-gray-500 mt-1">Today</p>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow p-4 sm:p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center mb-2">
                        <div class="p-2 bg-purple-100 rounded-lg mr-2 sm:mr-3">
                            <i class="fas fa-money-bill-wave text-purple-600 text-lg sm:text-2xl"></i>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Revenue</p>
                    </div>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900">Tsh <?php echo number_format($stats['today_revenue'], 0); ?></p>
                    <p class="text-xs text-gray-500 mt-1">Today</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue & Growth Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <!-- Month Revenue -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-4 sm:p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-chart-line text-2xl sm:text-3xl opacity-80"></i>
                <span class="text-xs sm:text-sm opacity-90">This Month</span>
            </div>
            <p class="text-xl sm:text-2xl font-bold mb-1">Tsh <?php echo number_format($stats['month_revenue'], 0); ?></p>
            <p class="text-xs opacity-90">Monthly Revenue</p>
        </div>

        <!-- Patient Growth -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 sm:p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-user-plus text-2xl sm:text-3xl opacity-80"></i>
                <span class="text-xs sm:text-sm opacity-90">Growth</span>
            </div>
            <p class="text-xl sm:text-2xl font-bold mb-1">+<?php echo number_format($stats['month_patients']); ?></p>
            <p class="text-xs opacity-90">New Patients This Month</p>
        </div>

        <!-- Medicine Stock Value -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-lg p-4 sm:p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-pills text-2xl sm:text-3xl opacity-80"></i>
                <span class="text-xs sm:text-sm opacity-90">Inventory</span>
            </div>
            <p class="text-xl sm:text-2xl font-bold mb-1">Tsh <?php echo number_format($stats['medicine_stock_value'], 0); ?></p>
            <p class="text-xs opacity-90"><?php echo $stats['total_medicines']; ?> Medicines</p>
        </div>

        <!-- IPD Occupancy -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-4 sm:p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <i class="fas fa-bed text-2xl sm:text-3xl opacity-80"></i>
                <span class="text-xs sm:text-sm opacity-90">IPD</span>
            </div>
            <p class="text-xl sm:text-2xl font-bold mb-1"><?php echo number_format($stats['active_ipd']); ?></p>
            <p class="text-xs opacity-90"><?php echo $stats['available_beds']; ?> Beds Available</p>
        </div>
    </div>

    <!-- System Status & Alerts -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
        <!-- Low Stock Alert -->
        <?php if ($stats['low_stock'] > 0): ?>
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-3 sm:p-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 text-lg sm:text-xl mr-2 sm:mr-3 flex-shrink-0"></i>
                <div class="min-w-0">
                    <h3 class="text-xs sm:text-sm font-medium text-red-800">Low Stock Alert</h3>
                    <p class="text-xs sm:text-sm text-red-700 mt-1"><?php echo $stats['low_stock']; ?> medicines low</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Expired Medicines -->
        <?php if ($stats['expired_medicines'] > 0): ?>
        <div class="bg-orange-50 border-l-4 border-orange-500 rounded-lg p-3 sm:p-4">
            <div class="flex items-center">
                <i class="fas fa-calendar-times text-orange-500 text-lg sm:text-xl mr-2 sm:mr-3 flex-shrink-0"></i>
                <div class="min-w-0">
                    <h3 class="text-xs sm:text-sm font-medium text-orange-800">Expired Medicines</h3>
                    <p class="text-xs sm:text-sm text-orange-700 mt-1"><?php echo $stats['expired_medicines']; ?> items expired</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pending Payments -->
        <?php if ($stats['pending_payments'] > 0): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-3 sm:p-4">
            <div class="flex items-center">
                <i class="fas fa-clock text-yellow-500 text-lg sm:text-xl mr-2 sm:mr-3 flex-shrink-0"></i>
                <div class="min-w-0">
                    <h3 class="text-xs sm:text-sm font-medium text-yellow-800">Pending Payments</h3>
                    <p class="text-xs sm:text-sm text-yellow-700 mt-1"><?php echo $stats['pending_payments']; ?> payments pending</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Today's Activity Summary -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-3 sm:p-4">
            <div class="flex items-center">
                <i class="fas fa-chart-bar text-blue-500 text-lg sm:text-xl mr-2 sm:mr-3 flex-shrink-0"></i>
                <div class="min-w-0">
                    <h3 class="text-xs sm:text-sm font-medium text-blue-800">Today's Activity</h3>
                    <p class="text-xs sm:text-sm text-blue-700 mt-1">
                        <?php echo $stats['today_lab_tests']; ?> Lab | <?php echo $stats['today_radiology']; ?> X-Ray
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- User Role Breakdown & Stats Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- User Role Breakdown -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-users-cog mr-2 text-blue-600"></i>
                    Staff Distribution
                </h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="space-y-3 sm:space-y-4">
                    <?php 
                    $roleIcons = [
                        'admin' => ['icon' => 'fas fa-user-shield', 'color' => 'blue'],
                        'doctor' => ['icon' => 'fas fa-user-md', 'color' => 'green'],
                        'nurse' => ['icon' => 'fas fa-user-nurse', 'color' => 'pink'],
                        'receptionist' => ['icon' => 'fas fa-user-tie', 'color' => 'purple'],
                        'pharmacist' => ['icon' => 'fas fa-pills', 'color' => 'indigo'],
                        'lab_technician' => ['icon' => 'fas fa-flask', 'color' => 'yellow'],
                        'radiologist' => ['icon' => 'fas fa-x-ray', 'color' => 'gray'],
                        'accountant' => ['icon' => 'fas fa-calculator', 'color' => 'orange']
                    ];
                    
                    foreach ($stats['users_by_role'] as $role_stat): 
                        $roleInfo = $roleIcons[$role_stat['role']] ?? ['icon' => 'fas fa-user', 'color' => 'gray'];
                        $percentage = $stats['total_users'] > 0 ? round(($role_stat['count'] / $stats['total_users']) * 100, 1) : 0;
                    ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center flex-1 min-w-0 mr-2">
                            <i class="<?php echo $roleInfo['icon']; ?> text-<?php echo $roleInfo['color']; ?>-500 text-lg sm:text-xl mr-2 sm:mr-3 flex-shrink-0"></i>
                            <span class="text-sm sm:text-base font-medium text-gray-900 capitalize truncate">
                                <?php echo str_replace('_', ' ', $role_stat['role']); ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                            <div class="text-right">
                                <div class="text-base sm:text-lg font-bold text-gray-900"><?php echo $role_stat['count']; ?></div>
                                <div class="text-xs text-gray-600"><?php echo $percentage; ?>%</div>
                            </div>
                            <div class="w-12 sm:w-16 bg-gray-200 rounded-full h-2">
                                <div class="bg-<?php echo $roleInfo['color']; ?>-500 h-2 rounded-full" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Quick Stats Summary -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-green-50">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-chart-pie mr-2 text-green-600"></i>
                    Quick Statistics
                </h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="space-y-4 sm:space-y-6">
                    <!-- Patient Growth -->
                    <div class="border-l-4 border-blue-500 pl-3 sm:pl-4">
                        <h4 class="text-sm sm:text-base font-semibold text-gray-900 mb-2">Patient Growth</h4>
                        <div class="grid grid-cols-3 gap-2 sm:gap-4 text-xs sm:text-sm">
                            <div class="text-center p-2 bg-blue-50 rounded-lg">
                                <div class="text-lg sm:text-xl font-bold text-blue-600"><?php echo $stats['today_patients']; ?></div>
                                <div class="text-gray-600 truncate">Today</div>
                            </div>
                            <div class="text-center p-2 bg-blue-50 rounded-lg">
                                <div class="text-lg sm:text-xl font-bold text-blue-600"><?php echo $stats['week_patients']; ?></div>
                                <div class="text-gray-600 truncate">This Week</div>
                            </div>
                            <div class="text-center p-2 bg-blue-50 rounded-lg">
                                <div class="text-lg sm:text-xl font-bold text-blue-600"><?php echo $stats['month_patients']; ?></div>
                                <div class="text-gray-600 truncate">This Month</div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Overview -->
                    <div class="border-l-4 border-green-500 pl-3 sm:pl-4">
                        <h4 class="text-sm sm:text-base font-semibold text-gray-900 mb-2">Revenue Overview</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center p-2 bg-green-50 rounded-lg">
                                <span class="text-xs sm:text-sm text-gray-700">Today</span>
                                <span class="text-sm sm:text-base font-bold text-green-600">Tsh <?php echo number_format($stats['today_revenue'], 0); ?></span>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-green-50 rounded-lg">
                                <span class="text-xs sm:text-sm text-gray-700">This Month</span>
                                <span class="text-sm sm:text-base font-bold text-green-600">Tsh <?php echo number_format($stats['month_revenue'], 0); ?></span>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-green-50 rounded-lg">
                                <span class="text-xs sm:text-sm text-gray-700">All Time</span>
                                <span class="text-sm sm:text-base font-bold text-green-600">Tsh <?php echo number_format($stats['total_revenue'], 0); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- System Health -->
                    <div class="border-l-4 border-purple-500 pl-3 sm:pl-4">
                        <h4 class="text-sm sm:text-base font-semibold text-gray-900 mb-2">System Health</h4>
                        <div class="grid grid-cols-2 gap-2 text-xs sm:text-sm">
                            <div class="p-2 bg-gray-50 rounded-lg">
                                <div class="text-gray-600 mb-1">Total Consultations</div>
                                <div class="text-base sm:text-lg font-bold text-gray-900"><?php echo number_format($stats['total_consultations']); ?></div>
                            </div>
                            <div class="p-2 bg-gray-50 rounded-lg">
                                <div class="text-gray-600 mb-1">Active IPD</div>
                                <div class="text-base sm:text-lg font-bold text-gray-900"><?php echo $stats['active_ipd']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Recent Patients -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-green-50">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-user-plus mr-2 text-green-600"></i>
                    Recent Patients
                </h3>
            </div>
            <div class="p-3 sm:p-4 max-h-96 overflow-y-auto">
                <?php if (empty($stats['recent_patients'])): ?>
                    <p class="text-xs sm:text-sm text-gray-500 text-center py-4">No recent patients</p>
                <?php else: ?>
                    <div class="space-y-2 sm:space-y-3">
                        <?php foreach ($stats['recent_patients'] as $patient): ?>
                        <div class="flex items-center p-2 sm:p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-xs sm:text-sm mr-2 sm:mr-3 flex-shrink-0">
                                <?php echo strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)); ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs sm:text-sm font-semibold text-gray-900 truncate">
                                    <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                </div>
                                <div class="text-xs text-gray-600 truncate">
                                    <i class="fas fa-id-badge mr-1"></i><?php echo htmlspecialchars($patient['registration_number']); ?>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 ml-2 flex-shrink-0">
                                <?php echo date('M j', strtotime($patient['created_at'])); ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Consultations -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-stethoscope mr-2 text-blue-600"></i>
                    Recent Consultations
                </h3>
            </div>
            <div class="p-3 sm:p-4 max-h-96 overflow-y-auto">
                <?php if (empty($stats['recent_consultations'])): ?>
                    <p class="text-xs sm:text-sm text-gray-500 text-center py-4">No recent consultations</p>
                <?php else: ?>
                    <div class="space-y-2 sm:space-y-3">
                        <?php foreach ($stats['recent_consultations'] as $consultation): ?>
                        <div class="p-2 sm:p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center justify-between mb-1">
                                <div class="text-xs sm:text-sm font-semibold text-gray-900 truncate flex-1 min-w-0 mr-2">
                                    <?php echo htmlspecialchars(($consultation['patient_first'] ?? 'Unknown') . ' ' . ($consultation['patient_last'] ?? '')); ?>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium flex-shrink-0
                                    <?php
                                    switch ($consultation['status']) {
                                        case 'completed':
                                            echo 'bg-green-100 text-green-800';
                                            break;
                                        case 'in_progress':
                                            echo 'bg-blue-100 text-blue-800';
                                            break;
                                        default:
                                            echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?php echo ucfirst($consultation['status'] ?? 'pending'); ?>
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-600">
                                <span class="truncate flex-1 min-w-0 mr-2">
                                    <i class="fas fa-user-md mr-1"></i>
                                    Dr. <?php echo htmlspecialchars(($consultation['doctor_first'] ?? 'N/A') . ' ' . ($consultation['doctor_last'] ?? '')); ?>
                                </span>
                                <span class="flex-shrink-0">
                                    <?php echo date('M j, H:i', strtotime($consultation['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-purple-50">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-money-bill-wave mr-2 text-purple-600"></i>
                    Recent Payments
                </h3>
            </div>
            <div class="p-3 sm:p-4 max-h-96 overflow-y-auto">
                <?php if (empty($stats['recent_payments'])): ?>
                    <p class="text-xs sm:text-sm text-gray-500 text-center py-4">No recent payments</p>
                <?php else: ?>
                    <div class="space-y-2 sm:space-y-3">
                        <?php foreach ($stats['recent_payments'] as $payment): ?>
                        <div class="flex items-center justify-between p-2 sm:p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1 min-w-0 mr-2">
                                <div class="text-xs sm:text-sm font-semibold text-gray-900 truncate">
                                    <?php echo htmlspecialchars(($payment['first_name'] ?? 'Unknown') . ' ' . ($payment['last_name'] ?? '')); ?>
                                </div>
                                <div class="flex items-center text-xs text-gray-600 mt-1">
                                    <i class="fas fa-credit-card mr-1 text-gray-400"></i>
                                    <span class="capitalize"><?php echo htmlspecialchars($payment['payment_method'] ?? 'cash'); ?></span>
                                    <span class="mx-1">•</span>
                                    <span class="capitalize"><?php echo htmlspecialchars($payment['payment_type'] ?? 'general'); ?></span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="text-xs sm:text-sm font-bold text-green-600">
                                    Tsh <?php echo number_format($payment['amount'], 0); ?>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <?php echo date('M j', strtotime($payment['payment_date'])); ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
