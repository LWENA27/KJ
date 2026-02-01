<!-- Page Header with Professional Gradient -->
<div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-800 rounded-lg shadow-xl p-4 sm:p-6 mb-4 sm:mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="text-white">
            <h1 class="text-2xl sm:text-3xl font-bold flex items-center">
                <i class="fas fa-chart-bar mr-2 sm:mr-3 text-indigo-200"></i>
                Reports & Analytics
            </h1>
            <p class="text-indigo-100 mt-1 sm:mt-2 text-sm sm:text-lg">Comprehensive healthcare data insights and reports</p>
        </div>
        <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
            <button class="bg-white text-indigo-700 hover:bg-indigo-50 px-3 sm:px-6 py-2 sm:py-3 rounded-lg font-medium transition-all duration-300 shadow-lg text-sm sm:text-base flex-1 sm:flex-none">
                <i class="fas fa-download mr-1 sm:mr-2"></i><span class="hidden sm:inline">Export</span><span class="sm:hidden">Export</span>
            </button>
            <button class="bg-indigo-500 bg-opacity-30 hover:bg-opacity-50 text-white px-3 sm:px-6 py-2 sm:py-3 rounded-lg font-medium transition-all duration-300 backdrop-blur-sm text-sm sm:text-base flex-1 sm:flex-none">
                <i class="fas fa-print mr-1 sm:mr-2"></i><span class="hidden sm:inline">Print</span><span class="sm:hidden">Print</span>
            </button>
        </div>
    </div>
</div>

<!-- Key Metrics Overview -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-4 sm:mb-6">
    <!-- Patient Statistics -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 truncate">Total Patients</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo number_format($patient_stats['total_patients']); ?></p>
                <div class="flex items-center mt-1 sm:mt-2">
                    <i class="fas fa-arrow-up text-xs mr-1 text-green-600"></i>
                    <span class="text-xs font-medium text-green-600 truncate"><?php echo $patient_stats['new_month']; ?> this month</span>
                </div>
            </div>
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0 ml-2">
                <i class="fas fa-users text-white text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Appointment Statistics -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 truncate">Total Appointments</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo number_format($appointment_stats['total_appointments']); ?></p>
                <div class="flex items-center mt-1 sm:mt-2">
                    <i class="fas fa-calendar text-xs mr-1 text-blue-600"></i>
                    <span class="text-xs font-medium text-blue-600 truncate"><?php echo $appointment_stats['today']; ?> today</span>
                </div>
            </div>
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0 ml-2">
                <i class="fas fa-calendar-check text-white text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Revenue Statistics -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 truncate">Monthly Revenue</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">
                    Tsh <?php 
                    $monthly_total = array_sum(array_column($daily_revenue, 'total_amount'));
                    echo number_format($monthly_total, 0, '.', ','); 
                    ?>
                </p>
                <div class="flex items-center mt-1 sm:mt-2">
                    <i class="fas fa-money-bill-wave text-xs mr-1 text-green-600"></i>
                    <span class="text-xs font-medium text-green-600 truncate">
                        <?php echo array_sum(array_column($daily_revenue, 'payment_count')); ?> transactions
                    </span>
                </div>
            </div>
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0 ml-2">
                <i class="fas fa-chart-line text-white text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Medicine Inventory -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 truncate">Medicine Inventory</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">
                    Tsh <?php echo number_format($medicine_stats['total_inventory_value'], 0, '.', ','); ?>
                </p>
                <div class="flex items-center mt-1 sm:mt-2">
                    <i class="fas fa-exclamation-triangle text-xs mr-1 text-red-600"></i>
                    <span class="text-xs font-medium text-red-600 truncate"><?php echo $medicine_stats['low_stock']; ?> low stock</span>
                </div>
            </div>
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0 ml-2">
                <i class="fas fa-pills text-white text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Detailed Reports Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
    <!-- Daily Revenue Chart -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 sm:mb-6 gap-2">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-chart-line mr-2 sm:mr-3 text-blue-600"></i>
                <span class="hidden sm:inline">Daily Revenue (Last 30 Days)</span>
                <span class="sm:hidden">Daily Revenue</span>
            </h3>
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                <?php echo count($daily_revenue); ?> days
            </span>
        </div>
        
        <div class="space-y-3 sm:space-y-4 max-h-80 overflow-y-auto">
            <?php foreach (array_slice($daily_revenue, 0, 10) as $day): ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex-1 min-w-0 mr-2">
                    <div class="font-medium text-gray-900 text-sm sm:text-base truncate"><?php echo date('M j, Y', strtotime($day['date'])); ?></div>
                    <div class="text-xs sm:text-sm text-gray-600"><?php echo $day['payment_count']; ?> transactions</div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-base sm:text-lg font-bold text-green-600 truncate">
                        Tsh <?php echo number_format($day['total_amount'], 0, '.', ','); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Payment Methods Breakdown -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 sm:mb-6 gap-2">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-credit-card mr-2 sm:mr-3 text-purple-600"></i>
                Payment Methods
            </h3>
            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                Last 30 days
            </span>
        </div>
        
        <div class="space-y-3 sm:space-y-4">
            <?php foreach ($payment_methods as $method): 
                $percentage = $monthly_total > 0 ? round(($method['total_amount'] / $monthly_total) * 100, 1) : 0;
                $methodIcons = [
                    'cash' => 'fas fa-money-bill-alt text-green-500',
                    'card' => 'fas fa-credit-card text-blue-500',
                    'insurance' => 'fas fa-shield-alt text-purple-500',
                    'other' => 'fas fa-ellipsis-h text-gray-500'
                ];
                $icon = $methodIcons[$method['payment_method']] ?? $methodIcons['other'];
            ?>
            <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center flex-1 min-w-0 mr-2">
                    <i class="<?php echo $icon; ?> mr-2 sm:mr-3 text-lg sm:text-xl flex-shrink-0"></i>
                    <div class="min-w-0">
                        <div class="font-medium text-gray-900 text-sm sm:text-base truncate"><?php echo ucfirst($method['payment_method']); ?></div>
                        <div class="text-xs sm:text-sm text-gray-600"><?php echo $method['count']; ?> transactions</div>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-base sm:text-lg font-bold text-gray-900 truncate">
                        Tsh <?php echo number_format($method['total_amount'], 0, '.', ','); ?>
                    </div>
                    <div class="text-xs sm:text-sm text-gray-600"><?php echo $percentage; ?>%</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Additional Reports Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
    <!-- Top Doctors Performance -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 sm:mb-6 gap-2">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-user-md mr-2 sm:mr-3 text-green-600"></i>
                Doctor Performance
            </h3>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                Top 5 Doctors
            </span>
        </div>
        
        <div class="space-y-3 sm:space-y-4">
            <?php foreach ($top_doctors as $index => $doctor): 
                $completion_rate = $doctor['appointment_count'] > 0 ? round(($doctor['completed_count'] / $doctor['appointment_count']) * 100, 1) : 0;
            ?>
            <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center flex-1 min-w-0 mr-2">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold mr-2 sm:mr-3 flex-shrink-0 text-sm sm:text-base">
                        <?php echo $index + 1; ?>
                    </div>
                    <div class="min-w-0">
                        <div class="font-medium text-gray-900 text-sm sm:text-base truncate">Dr. <?php echo htmlspecialchars($doctor['doctor_name']); ?></div>
                        <div class="text-xs sm:text-sm text-gray-600"><?php echo $completion_rate; ?>% completion</div>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-base sm:text-lg font-bold text-gray-900"><?php echo $doctor['appointment_count']; ?></div>
                    <div class="text-xs sm:text-sm text-gray-600">appointments</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Quick Statistics Summary -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 sm:mb-6 gap-2">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-clipboard-list mr-2 sm:mr-3 text-indigo-600"></i>
                Quick Statistics
            </h3>
        </div>
        
        <div class="space-y-6">
            <!-- Patient Growth -->
            <div class="border-l-4 border-blue-500 pl-4">
                <h4 class="font-semibold text-gray-900 mb-2">Patient Growth</h4>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div class="text-center">
                        <div class="text-xl font-bold text-blue-600"><?php echo $patient_stats['new_today']; ?></div>
                        <div class="text-gray-600">Today</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-blue-600"><?php echo $patient_stats['new_week']; ?></div>
                        <div class="text-gray-600">This Week</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-blue-600"><?php echo $patient_stats['new_month']; ?></div>
                        <div class="text-gray-600">This Month</div>
                    </div>
                </div>
            </div>

            <!-- Appointment Success Rate -->
            <div class="border-l-4 border-green-500 pl-4">
                <h4 class="font-semibold text-gray-900 mb-2">Appointment Analytics</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="text-center">
                        <div class="text-xl font-bold text-green-600">
                            <?php echo round(($appointment_stats['completed'] / max($appointment_stats['total_appointments'], 1)) * 100, 1); ?>%
                        </div>
                        <div class="text-gray-600">Completion Rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-red-600">
                            <?php echo round(($appointment_stats['cancelled'] / max($appointment_stats['total_appointments'], 1)) * 100, 1); ?>%
                        </div>
                        <div class="text-gray-600">Cancellation Rate</div>
                    </div>
                </div>
            </div>

            <!-- Medicine Inventory Alert -->
            <div class="border-l-4 border-yellow-500 pl-4">
                <h4 class="font-semibold text-gray-900 mb-2">Inventory Alerts</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="text-center">
                        <div class="text-xl font-bold text-yellow-600"><?php echo $medicine_stats['low_stock']; ?></div>
                        <div class="text-gray-600">Low Stock</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-red-600"><?php echo $medicine_stats['expiring_soon']; ?></div>
                        <div class="text-gray-600">Expiring Soon</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced JavaScript for Reports -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add professional hover effects to cards
    document.querySelectorAll('.transform').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
        });
    });
    
    // Add loading states to buttons
    document.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', function() {
            this.style.opacity = '0.7';
            setTimeout(() => {
                this.style.opacity = '1';
            }, 1000);
        });
    });
    
    // Auto-refresh data every 5 minutes
    setInterval(function() {
        // Add a subtle indicator that data is being refreshed
        console.log('Auto-refreshing report data...');
    }, 300000); // 5 minutes
});

// Export functionality - generates CSV of report data
function exportReport() {
    const reportType = document.getElementById('reportType')?.value || 'daily';
    const startDate = document.getElementById('startDate')?.value;
    const endDate = document.getElementById('endDate')?.value;
    
    let csv = 'Receptionist Report Export\n';
    csv += `Generated on: ${new Date().toLocaleString()}\n`;
    csv += `Report Type: ${reportType}\n`;
    if (startDate) csv += `Start Date: ${startDate}\n`;
    if (endDate) csv += `End Date: ${endDate}\n\n`;
    
    // Get visible data from tables
    const tables = document.querySelectorAll('table');
    
    tables.forEach((table, tableIndex) => {
        if (tableIndex > 0) csv += '\n\n';
        
        // Extract headers
        const headers = [];
        table.querySelectorAll('thead th').forEach(th => {
            headers.push(th.textContent.trim());
        });
        csv += headers.join(',') + '\n';
        
        // Extract rows
        table.querySelectorAll('tbody tr:not([style*="display: none"])').forEach(row => {
            const cells = [];
            row.querySelectorAll('td').forEach(td => {
                cells.push(`"${td.textContent.trim()}"`);
            });
            if (cells.length > 0) csv += cells.join(',') + '\n';
        });
    });
    
    if (csv.split('\n').length <= 5) {
        alert('No data to export');
        return;
    }
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `receptionist_report_${reportType}_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
}

// Print functionality
function printReport() {
    window.print();
}
</script>
