<!-- Page Header with Professional Gradient -->
<div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-purple-800 rounded-lg shadow-xl p-4 sm:p-6 mb-4 sm:mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="text-white">
            <h1 class="text-2xl sm:text-3xl font-bold flex items-center">
                <i class="fas fa-chart-bar mr-2 sm:mr-3 text-indigo-200"></i>
                My Reports & Performance
            </h1>
            <p class="text-indigo-100 mt-1 sm:mt-2 text-sm sm:text-lg">Your personal activity and performance metrics</p>
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

<!-- Performance Summary -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-4 sm:mb-6">
    <!-- Total Patients Registered -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 truncate">Patients Registered</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo number_format($performance['total_patients_registered']); ?></p>
                <div class="flex items-center mt-1 sm:mt-2">
                    <i class="fas fa-arrow-up text-xs mr-1 text-green-600"></i>
                    <span class="text-xs font-medium text-green-600 truncate"><?php echo $patient_stats['new_month']; ?> this month</span>
                </div>
            </div>
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0 ml-2">
                <i class="fas fa-user-plus text-white text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Visits Checked In -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 truncate">Visits Checked In</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo number_format($performance['total_visits_checked_in']); ?></p>
                <div class="flex items-center mt-1 sm:mt-2">
                    <i class="fas fa-calendar text-xs mr-1 text-blue-600"></i>
                    <span class="text-xs font-medium text-blue-600 truncate"><?php echo $visit_stats['today']; ?> today</span>
                </div>
            </div>
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0 ml-2">
                <i class="fas fa-clipboard-check text-white text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Payments Processed -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 truncate">Payments Processed</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900"><?php echo number_format($performance['total_payments_processed']); ?></p>
                <div class="flex items-center mt-1 sm:mt-2">
                    <i class="fas fa-money-bill-wave text-xs mr-1 text-green-600"></i>
                    <span class="text-xs font-medium text-green-600 truncate">
                        <?php echo array_sum(array_column($daily_revenue, 'payment_count')); ?> this month
                    </span>
                </div>
            </div>
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0 ml-2">
                <i class="fas fa-cash-register text-white text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Revenue Collected -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 truncate">Revenue Collected</p>
                <p class="text-xl sm:text-2xl font-bold text-gray-900 truncate">
                    Tsh <?php echo number_format($performance['total_revenue_collected'], 0, '.', ','); ?>
                </p>
                <div class="flex items-center mt-1 sm:mt-2">
                    <i class="fas fa-chart-line text-xs mr-1 text-green-600"></i>
                    <span class="text-xs font-medium text-green-600 truncate">All time</span>
                </div>
            </div>
            <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0 ml-2">
                <i class="fas fa-chart-line text-white text-lg sm:text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Patient & Visit Statistics -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
    <!-- Patient Growth -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-users mr-2 sm:mr-3 text-blue-600"></i>
                Patient Registrations
            </h3>
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                Your Activity
            </span>
        </div>
        
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-2xl sm:text-3xl font-bold text-blue-600"><?php echo $patient_stats['new_today']; ?></p>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Today</p>
            </div>
            <div class="text-center p-4 bg-indigo-50 rounded-lg">
                <p class="text-2xl sm:text-3xl font-bold text-indigo-600"><?php echo $patient_stats['new_week']; ?></p>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">This Week</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-2xl sm:text-3xl font-bold text-purple-600"><?php echo $patient_stats['new_month']; ?></p>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">This Month</p>
            </div>
        </div>
    </div>

    <!-- Visit Statistics -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-clipboard-check mr-2 sm:mr-3 text-green-600"></i>
                Visit Check-Ins
            </h3>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                Your Activity
            </span>
        </div>
        
        <div class="space-y-3">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">Today</span>
                <span class="text-lg font-bold text-gray-900"><?php echo $visit_stats['today']; ?> visits</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">This Week</span>
                <span class="text-lg font-bold text-gray-900"><?php echo $visit_stats['this_week']; ?> visits</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">Consultations</span>
                <span class="text-lg font-bold text-blue-600"><?php echo $visit_stats['consultation_visits']; ?></span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">Lab Visits</span>
                <span class="text-lg font-bold text-green-600"><?php echo $visit_stats['lab_visits']; ?></span>
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
                <span class="hidden sm:inline">My Daily Revenue (Last 30 Days)</span>
                <span class="sm:hidden">My Daily Revenue</span>
            </h3>
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                <?php echo count($daily_revenue); ?> days
            </span>
        </div>
        
        <div class="space-y-3 sm:space-y-4 max-h-80 overflow-y-auto">
            <?php if (count($daily_revenue) > 0): ?>
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
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-line text-4xl mb-2"></i>
                    <p>No revenue data for the last 30 days</p>
                </div>
            <?php endif; ?>
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
            <?php 
            $monthly_total = array_sum(array_column($daily_revenue, 'total_amount'));
            if (count($payment_methods) > 0): 
                foreach ($payment_methods as $method): 
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
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-credit-card text-4xl mb-2"></i>
                    <p>No payment method data for the last 30 days</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Activity & Quick Summary (Receptionist-specific) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
    <!-- Recent Patients Registered -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 sm:mb-6 gap-2">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-user-plus mr-2 sm:mr-3 text-green-600"></i>
                Recent Patients Registered
            </h3>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                Last 10
            </span>
        </div>

        <div class="space-y-3 sm:space-y-4 max-h-96 overflow-y-auto">
            <?php if (!empty($recent_patients)): ?>
                <?php foreach ($recent_patients as $patient): ?>
                <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center flex-1 min-w-0 mr-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold mr-3 flex-shrink-0">
                            <?php echo strtoupper(substr($patient['patient_name'], 0, 1)); ?>
                        </div>
                        <div class="min-w-0">
                            <div class="font-medium text-gray-900 text-sm sm:text-base truncate"><?php echo htmlspecialchars($patient['patient_name']); ?></div>
                            <div class="text-xs sm:text-sm text-gray-600">
                                <?php echo htmlspecialchars($patient['registration_number']); ?> • <?php echo htmlspecialchars($patient['phone']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-sm font-medium text-gray-900"><?php echo $patient['total_visits']; ?> visits</div>
                        <div class="text-xs text-gray-600"><?php echo date('M j, Y', strtotime($patient['created_at'])); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-user-plus text-4xl mb-2"></i>
                    <p>No patients registered yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Payments Processed -->
    <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 sm:mb-6 gap-2">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-money-bill-wave mr-2 sm:mr-3 text-yellow-600"></i>
                Recent Payments Processed
            </h3>
            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                Last 10
            </span>
        </div>

        <div class="space-y-3 sm:space-y-4 max-h-96 overflow-y-auto">
            <?php if (!empty($recent_payments)): ?>
                <?php foreach ($recent_payments as $payment): 
                    $paymentTypeColors = [
                        'consultation' => 'bg-blue-100 text-blue-800',
                        'medicine' => 'bg-purple-100 text-purple-800',
                        'lab' => 'bg-green-100 text-green-800',
                        'radiology' => 'bg-indigo-100 text-indigo-800',
                        'ipd' => 'bg-pink-100 text-pink-800',
                        'other' => 'bg-gray-100 text-gray-800'
                    ];
                    $colorClass = $paymentTypeColors[$payment['payment_type']] ?? $paymentTypeColors['other'];
                ?>
                <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center flex-1 min-w-0 mr-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center text-white font-bold mr-3 flex-shrink-0">
                            <i class="fas fa-dollar-sign text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="font-medium text-gray-900 text-sm sm:text-base truncate"><?php echo htmlspecialchars($payment['patient_name']); ?></div>
                            <div class="flex items-center gap-2 text-xs sm:text-sm">
                                <span class="text-gray-600"><?php echo htmlspecialchars($payment['registration_number']); ?></span>
                                <span class="<?php echo $colorClass; ?> px-2 py-0.5 rounded text-xs font-medium truncate">
                                    <?php echo ucfirst($payment['payment_type']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-sm sm:text-base font-bold text-green-600">
                            Tsh <?php echo number_format($payment['amount'], 0, '.', ','); ?>
                        </div>
                        <div class="text-xs sm:text-sm text-gray-600"><?php echo ucfirst($payment['payment_method']); ?></div>
                        <div class="text-xs text-gray-500"><?php echo date('M j, h:i A', strtotime($payment['payment_date'])); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-money-bill-wave text-4xl mb-2"></i>
                    <p>No payments processed yet</p>
                </div>
            <?php endif; ?>
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
