<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <div class="text-sm text-gray-500">
            <?php echo date('l, F j, Y'); ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
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
                    <i class="fas fa-user-injured text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Patients</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_patients']; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-calendar-check text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Today's Consultations</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['today_consultations']; ?></p>
                </div>
            </div>
        </div>

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
    </div>

    <!-- Alerts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                    <p class="text-sm text-gray-600">New patient registered: John Doe</p>
                    <span class="text-xs text-gray-400 ml-auto">2 hours ago</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                    <p class="text-sm text-gray-600">Consultation completed by Dr. John Doe</p>
                    <span class="text-xs text-gray-400 ml-auto">4 hours ago</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                    <p class="text-sm text-gray-600">Medicine stock updated: Paracetamol</p>
                    <span class="text-xs text-gray-400 ml-auto">6 hours ago</span>
                </div>
            </div>
        </div>
    </div>
</div>
