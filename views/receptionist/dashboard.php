<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-hospital-user mr-3 text-blue-600"></i>
                Receptionist Dashboard
            </h1>
            <p class="text-gray-600 mt-1">Manage patients, appointments, and front desk operations</p>
            <!-- Live Status Bar -->
            <div class="flex items-center mt-2 space-x-4">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-xs text-green-600 font-medium">System Online</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                    <span class="text-xs text-blue-600 font-medium">
                        <?php echo isset($sidebar_data['pending_patients']) ? $sidebar_data['pending_patients'] : 0; ?> Pending Patients
                    </span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                    <span class="text-xs text-yellow-600 font-medium">
                        <?php echo isset($sidebar_data['upcoming_appointments']) ? $sidebar_data['upcoming_appointments'] : 0; ?> Today's Appointments
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-right">
                <div class="text-sm text-gray-500"><?php echo date('l, F j, Y'); ?></div>
                <div class="text-xs text-gray-400" id="current-time"><?php echo date('h:i A'); ?></div>
            </div>
            <!-- Quick Action Buttons -->
            <button onclick="openEmergencyModal()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-exclamation-triangle mr-2"></i>Emergency
            </button>
            <button onclick="openReportsModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-chart-bar mr-2"></i>Reports
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Pending Patients -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer group" onclick="window.location.href='<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/patients'">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Pending Patients</p>
                    <p class="text-3xl font-bold"><?php echo isset($sidebar_data['pending_patients']) ? $sidebar_data['pending_patients'] : 0; ?></p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-clock mr-2 text-blue-200"></i>
                        <span class="text-sm text-blue-200">Requires attention</span>
                    </div>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3 group-hover:bg-opacity-50 transition-all">
                    <i class="fas fa-user-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Today's Appointments -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer group" onclick="window.location.href='<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/appointments'">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Today's Appointments</p>
                    <p class="text-3xl font-bold"><?php echo isset($sidebar_data['upcoming_appointments']) ? $sidebar_data['upcoming_appointments'] : 0; ?></p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-calendar-check mr-2 text-green-200"></i>
                        <span class="text-sm text-green-200">Scheduled</span>
                    </div>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3 group-hover:bg-opacity-50 transition-all">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Low Stock Medicines -->
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer group" onclick="window.location.href='<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/medicine'">>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Low Stock Alert</p>
                    <p class="text-3xl font-bold"><?php echo isset($sidebar_data['low_stock_medicines']) ? $sidebar_data['low_stock_medicines'] : 0; ?></p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-exclamation-triangle mr-2 text-yellow-200"></i>
                        <span class="text-sm text-yellow-200">Needs reorder</span>
                    </div>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3 group-hover:bg-opacity-50 transition-all">
                    <i class="fas fa-pills text-2xl"></i>
                </div>
            </div>
            <?php if (isset($sidebar_data['low_stock_medicines']) && $sidebar_data['low_stock_medicines'] > 0): ?>
            <div class="mt-2 text-xs text-yellow-100">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Requires immediate attention
            </div>
            <?php endif; ?>
        </div>

        <!-- Payment Collection -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer group" onclick="window.location.href='/KJ/receptionist/payments'">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Payments Today</p>
                    <p class="text-3xl font-bold"><?php 
                        $amount = $payments_today['total_today'] ?: 0;
                        echo 'Tsh ' . number_format($amount, 0, '.', ','); 
                    ?></p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-money-bill-wave mr-2 text-purple-200"></i>
                        <span class="text-sm text-purple-200">
                            <?php 
                            $change = round($percentage_change, 1);
                            if ($change > 0) {
                                echo "+{$change}% vs yesterday";
                            } elseif ($change < 0) {
                                echo "{$change}% vs yesterday";
                            } else {
                                echo "No change vs yesterday";
                            }
                            ?>
                        </span>
                    </div>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3 group-hover:bg-opacity-50 transition-all">
                    <i class="fas fa-credit-card text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Panel -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-bolt mr-3 text-yellow-500"></i>
            Quick Actions
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="/KJ/receptionist/register_patient" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white p-4 rounded-lg text-center transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-user-plus text-2xl mb-2"></i>
                <div class="font-medium">Register Patient</div>
                <div class="text-xs mt-1 opacity-80">Add new patient</div>
            </a>
            <a href="/KJ/receptionist/appointments" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white p-4 rounded-lg text-center transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-calendar-plus text-2xl mb-2"></i>
                <div class="font-medium">New Appointment</div>
                <div class="text-xs mt-1 opacity-80">Schedule visit</div>
            </a>
            <a href="/KJ/receptionist/payments" class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white p-4 rounded-lg text-center transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-credit-card text-2xl mb-2"></i>
                <div class="font-medium">Process Payment</div>
                <div class="text-xs mt-1 opacity-80">Collect fees</div>
            </a>
            <a href="/KJ/receptionist/patients" class="bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white p-4 rounded-lg text-center transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-search text-2xl mb-2"></i>
                <div class="font-medium">Search Patient</div>
                <div class="text-xs mt-1 opacity-80">Find records</div>
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Today's Appointments (Left Column - 2/3 width) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-calendar-day mr-3 text-green-600"></i>
                            Today's Appointments
                        </h3>
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                            <?php echo count($appointments); ?> Scheduled
                        </span>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (empty($appointments)): ?>
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">No appointments scheduled for today</p>
                        <p class="text-gray-400 text-sm mt-2">Schedule new appointments to get started</p>
                        <a href="/KJ/receptionist/appointments" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>Schedule Appointment
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($appointments as $appointment): ?>
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-4 shadow-lg">
                                        <i class="fas fa-user text-white text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-lg">
                                            <?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>
                                        </p>
                                        <p class="text-sm text-gray-600 flex items-center">
                                            <i class="fas fa-user-md mr-2 text-blue-500"></i>
                                            Dr. <?php echo htmlspecialchars($appointment['doctor_first'] . ' ' . $appointment['doctor_last']); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="flex items-center mb-2">
                                        <i class="fas fa-clock mr-2 text-gray-500"></i>
                                        <p class="text-lg font-bold text-gray-900">
                                                <?php $apt = $appointment['appointment_date'] ?? $appointment['visit_date'] ?? $appointment['created_at']; echo date('H:i', strtotime($apt)); ?>
                                            </p>
                                    </div>
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                        <?php
                                        switch ($appointment['status']) {
                                            case 'scheduled':
                                                echo 'bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-300';
                                                break;
                                            case 'in_progress':
                                                echo 'bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300';
                                                break;
                                            case 'completed':
                                                echo 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300';
                                                break;
                                            case 'cancelled':
                                                echo 'bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300';
                                                break;
                                        }
                                        ?>">
                                        <i class="fas fa-circle mr-1 text-xs"></i>
                                        <?php echo ucfirst($appointment['status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Patients & Quick Stats (Right Column - 1/3 width) -->
        <div class="space-y-6">
            <!-- Recent Patients -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-users mr-3 text-purple-600"></i>
                        Recent Patients
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach ($recent_patients as $patient): ?>
                        <div class="flex items-center justify-between p-3 bg-gradient-to-r from-gray-50 to-purple-50 rounded-lg hover:shadow-md transition-all duration-300">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center mr-3 shadow-lg">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                    </p>
                                    <p class="text-xs text-gray-600 flex items-center">
                                        <i class="fas fa-phone mr-1"></i>
                                        <?php echo htmlspecialchars($patient['phone']); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <?php echo date('M j, Y', strtotime($patient['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Info Panel -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg text-white p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-3"></i>
                    Today's Summary
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-indigo-100">Total Patients:</span>
                        <span class="font-bold"><?php echo count($recent_patients); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-indigo-100">Appointments:</span>
                        <span class="font-bold"><?php echo count($appointments); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-indigo-100">Payments:</span>
                        <span class="font-bold"><?php 
                            $amount = $payments_today['total_today'] ?: 0;
                            echo 'Tsh ' . number_format($amount, 0, '.', ','); 
                        ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-indigo-100">Payment Count:</span>
                        <span class="font-bold"><?php echo $payments_today['payment_count'] ?: 0; ?> transactions</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-indigo-400">
                    <p class="text-xs text-indigo-100 text-center">
                        <i class="fas fa-clock mr-1"></i>
                        Last updated: <?php echo date('H:i'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for enhanced interactivity -->
<script>
// Update current time every minute
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: true 
    });
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}

// Update time immediately and then every minute
updateTime();
setInterval(updateTime, 60000);

// Emergency modal
function openEmergencyModal() {
    alert('Emergency protocols activated!\n\nContact:\n- Security: Ext. 911\n- Medical Emergency: Ext. 999\n- Admin: Ext. 101');
}

// Reports modal
function openReportsModal() {
    alert('Reports feature coming soon!\n\nAvailable reports:\n- Daily Summary\n- Patient Statistics\n- Payment Records\n- Appointment Analytics');
}

// Add some interactive feedback
document.querySelectorAll('.transform').forEach(element => {
    element.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.02)';
    });
    element.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
});
</script>
