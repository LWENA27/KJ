<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <div class="text-sm text-gray-500">
            <?php echo date('l, F j, Y'); ?>
        </div>
    </div>

    <!-- Financial Overview Section -->
    <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">💰 Financial Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Dispensary Income Today -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i class="fas fa-prescription-bottle text-emerald-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Dispensary Today</p>
                        <p class="text-2xl font-bold text-gray-900">Tsh <?php echo number_format($stats['dispensary_income_today'], 0); ?></p>
                    </div>
                </div>
            </div>

            <!-- Dispensary Income Month -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i class="fas fa-calendar-alt text-emerald-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Dispensary This Month</p>
                        <p class="text-2xl font-bold text-gray-900">Tsh <?php echo number_format($stats['dispensary_income_month'], 0); ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Revenue Today -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-dollar-sign text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Revenue Today</p>
                        <p class="text-2xl font-bold text-gray-900">Tsh <?php echo number_format($stats['revenue_today'], 0); ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Revenue Month -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Revenue This Month</p>
                        <p class="text-2xl font-bold text-gray-900">Tsh <?php echo number_format($stats['revenue_month'], 0); ?></p>
                    </div>
                </div>
            </div>

            <!-- Completed Payments -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-check-circle text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completed Payments</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['completed_payments']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Revenue Breakdown (Consultation) -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <i class="fas fa-stethoscope text-indigo-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs font-medium text-gray-600">Consultation (Month)</p>
                        <p class="text-lg font-bold text-gray-900">Tsh <?php echo number_format($stats['revenue_consultation'], 0); ?></p>
                    </div>
                </div>
            </div>

            <!-- Revenue Breakdown (Lab) -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-flask text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs font-medium text-gray-600">Lab Tests (Month)</p>
                        <p class="text-lg font-bold text-gray-900">Tsh <?php echo number_format($stats['revenue_lab'], 0); ?></p>
                    </div>
                </div>
            </div>

            <!-- Revenue Breakdown (Radiology) -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-pink-100 rounded-lg">
                        <i class="fas fa-x-ray text-pink-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs font-medium text-gray-600">Radiology (Month)</p>
                        <p class="text-lg font-bold text-gray-900">Tsh <?php echo number_format($stats['revenue_radiology'], 0); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Care Section -->
    <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">🏥 Patient Care</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Patients -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-user-injured text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Patients</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_patients']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Today's Consultations -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i class="fas fa-user-md text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Today's Consultations</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['today_consultations']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Today's Appointments -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-calendar-day text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Today's Appointments</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['appointments_today']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Pending Appointments -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i class="fas fa-clock text-orange-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Appointments</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['pending_appointments']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Section -->
    <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">🏢 Departments</h2>
        
        <!-- Pharmacy -->
        <h3 class="text-lg font-medium text-gray-700 mb-3 mt-4">💊 Pharmacy</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-pills text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Medicines</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_medicines']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i class="fas fa-file-prescription text-orange-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Prescriptions</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['pending_prescriptions']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Dispensed Today</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['dispensed_today']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-emerald-100 rounded-lg">
                        <i class="fas fa-dollar-sign text-emerald-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Dispensary Income</p>
                        <p class="text-2xl font-bold text-gray-900">Tsh <?php echo number_format($stats['dispensary_income_total'], 0); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lab Department -->
        <h3 class="text-lg font-medium text-gray-700 mb-3 mt-6">🔬 Laboratory</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-vial text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Lab Orders</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['pending_lab_orders']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tests Completed Today</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['lab_completed_today']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-chart-bar text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Lab Revenue (Month)</p>
                        <p class="text-2xl font-bold text-gray-900">Tsh <?php echo number_format($stats['revenue_lab'], 0); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Radiology Department -->
        <h3 class="text-lg font-medium text-gray-700 mb-3 mt-6">🩻 Radiology</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-pink-100 rounded-lg">
                        <i class="fas fa-x-ray text-pink-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Radiology Orders</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['pending_radiology_orders']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Tests Completed Today</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['radiology_completed_today']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-pink-100 rounded-lg">
                        <i class="fas fa-chart-bar text-pink-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Radiology Revenue (Month)</p>
                        <p class="text-2xl font-bold text-gray-900">Tsh <?php echo number_format($stats['revenue_radiology'], 0); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- IPD Department -->
        <h3 class="text-lg font-medium text-gray-700 mb-3 mt-6">🛏️ In-Patient Department (IPD)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <i class="fas fa-bed text-red-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Admissions</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['active_admissions']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i class="fas fa-user-plus text-orange-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">New Admissions Today</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['admissions_today']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-procedures text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Beds (Occupied/Total)</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['occupied_beds']; ?>/<?php echo $stats['total_beds']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-teal-100 rounded-lg">
                        <i class="fas fa-percentage text-teal-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Bed Occupancy</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['bed_occupancy_percent']; ?>%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">⚠️ Alerts</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($stats['low_stock'] > 0): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Low Stock Alert</h3>
                        <p class="text-sm text-red-700"><?php echo $stats['low_stock']; ?> medicines are running low on stock</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($stats['pending_payments'] > 0): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-clock text-yellow-500 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800">Pending Payments</h3>
                        <p class="text-sm text-yellow-700"><?php echo $stats['pending_payments']; ?> payments are pending</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($stats['pending_lab_orders'] > 0): ?>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-flask text-purple-500 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-purple-800">Pending Lab Tests</h3>
                        <p class="text-sm text-purple-700"><?php echo $stats['pending_lab_orders']; ?> lab test orders awaiting processing</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($stats['pending_prescriptions'] > 0): ?>
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-file-prescription text-orange-500 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-orange-800">Pending Prescriptions</h3>
                        <p class="text-sm text-orange-700"><?php echo $stats['pending_prescriptions']; ?> prescriptions awaiting dispensing</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($stats['pending_radiology_orders'] > 0): ?>
            <div class="bg-pink-50 border border-pink-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-x-ray text-pink-500 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-pink-800">Pending Radiology Tests</h3>
                        <p class="text-sm text-pink-700"><?php echo $stats['pending_radiology_orders']; ?> radiology test orders pending</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($stats['bed_occupancy_percent'] >= 90): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-bed text-red-500 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">High Bed Occupancy</h3>
                        <p class="text-sm text-red-700">IPD beds are <?php echo $stats['bed_occupancy_percent']; ?>% occupied</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- System Info -->
    <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">👥 System</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_users']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check-double text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Completed Payments</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['completed_payments']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">📋 Recent Activity</h3>
        </div>
        <div class="p-6">
            <?php if (!empty($recentActivity)): ?>
            <div class="space-y-4">
                <?php foreach ($recentActivity as $activity): 
                    // Determine color based on activity type
                    $colorClass = 'bg-gray-500';
                    if ($activity['activity_type'] == 'patient_registration') {
                        $colorClass = 'bg-green-500';
                    } elseif ($activity['activity_type'] == 'consultation') {
                        $colorClass = 'bg-blue-500';
                    } elseif ($activity['activity_type'] == 'stock_update') {
                        $colorClass = 'bg-purple-500';
                    } elseif ($activity['activity_type'] == 'payment') {
                        $colorClass = 'bg-emerald-500';
                    }
                    
                    // Calculate time ago
                    $timestamp = strtotime($activity['timestamp']);
                    $now = time();
                    $diff = $now - $timestamp;
                    
                    if ($diff < 60) {
                        $timeAgo = 'just now';
                    } elseif ($diff < 3600) {
                        $minutes = floor($diff / 60);
                        $timeAgo = $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
                    } elseif ($diff < 86400) {
                        $hours = floor($diff / 3600);
                        $timeAgo = $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
                    } else {
                        $days = floor($diff / 86400);
                        $timeAgo = $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
                    }
                ?>
                <div class="flex items-center">
                    <div class="w-2 h-2 <?php echo $colorClass; ?> rounded-full mr-3"></div>
                    <p class="text-sm text-gray-600 flex-1"><?php echo htmlspecialchars($activity['description']); ?></p>
                    <span class="text-xs text-gray-400 ml-auto"><?php echo $timeAgo; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-sm text-gray-500 text-center py-4">No recent activity to display</p>
            <?php endif; ?>
        </div>
    </div>
</div>
