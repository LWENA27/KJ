<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Laboratory Reports</h1>
            <p class="text-gray-600 mt-1">Generate comprehensive reports and analytics</p>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="generateReport()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-chart-bar mr-2"></i>Generate Report
            </button>
            <button onclick="exportReport()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-download mr-2"></i>Export Data
            </button>
            <button onclick="scheduleReport()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-clock mr-2"></i>Schedule
            </button>
        </div>
    </div>

    <!-- Report Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Tests</p>
                    <p class="text-3xl font-bold"><?= $stats['total_tests'] ?></p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-vial text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-blue-100">
                <i class="fas fa-calendar mr-1"></i>
                All time total
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Completed</p>
                    <p class="text-3xl font-bold"><?= $stats['completed_tests'] ?></p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-green-100">
                <i class="fas fa-percentage mr-1"></i>
                <?= $stats['total_tests'] > 0 ? round(($stats['completed_tests'] / $stats['total_tests']) * 100, 1) : 0 ?>% completion rate
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Today's Tests</p>
                    <p class="text-3xl font-bold"><?= $stats['today_tests'] ?></p>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-calendar-day text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-yellow-100">
                <i class="fas fa-clock mr-1"></i>
                Current workload
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">This Month</p>
                    <p class="text-3xl font-bold"><?= $stats['month_tests'] ?></p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-purple-100">
                <i class="fas fa-trending-up mr-1"></i>
                Monthly performance
            </div>
        </div>
    </div>

    <!-- Report Tabs -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showReportTab('daily')" class="report-tab active border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600">
                    <i class="fas fa-calendar-day mr-2"></i>Daily Report
                </button>
                <button onclick="showReportTab('weekly')" class="report-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                    <i class="fas fa-calendar-week mr-2"></i>Weekly Report
                </button>
                <button onclick="showReportTab('monthly')" class="report-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                    <i class="fas fa-calendar-alt mr-2"></i>Monthly Report
                </button>
                <button onclick="showReportTab('analytics')" class="report-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                    <i class="fas fa-chart-pie mr-2"></i>Analytics
                </button>
            </nav>
        </div>

        <!-- Daily Report Tab -->
        <div id="daily-report" class="report-content p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Test Performance Chart -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-3 text-blue-600"></i>
                        Today's Test Performance
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-white rounded border">
                            <div class="flex items-center">
                                <i class="fas fa-microscope text-green-600 mr-3"></i>
                                <span class="font-medium">Microscopy Tests</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">8 completed</span>
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 80%"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-white rounded border">
                            <div class="flex items-center">
                                <i class="fas fa-flask text-blue-600 mr-3"></i>
                                <span class="font-medium">Chemistry Tests</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">12 completed</span>
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 90%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-white rounded border">
                            <div class="flex items-center">
                                <i class="fas fa-tint text-red-600 mr-3"></i>
                                <span class="font-medium">Hematology Tests</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">6 completed</span>
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: 60%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-white rounded border">
                            <div class="flex items-center">
                                <i class="fas fa-virus text-purple-600 mr-3"></i>
                                <span class="font-medium">Serology Tests</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">4 completed</span>
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: 70%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quality Metrics -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-award mr-3 text-yellow-600"></i>
                        Quality Metrics
                    </h3>
                    <div class="space-y-4">
                        <div class="bg-white rounded border p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Accuracy Rate</span>
                                <span class="text-lg font-bold text-green-600">99.2%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 99.2%"></div>
                            </div>
                        </div>

                        <div class="bg-white rounded border p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Turnaround Time</span>
                                <span class="text-lg font-bold text-blue-600">2.4 hrs</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 85%"></div>
                            </div>
                        </div>

                        <div class="bg-white rounded border p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">QC Pass Rate</span>
                                <span class="text-lg font-bold text-green-600">100%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>

                        <div class="bg-white rounded border p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Equipment Uptime</span>
                                <span class="text-lg font-bold text-green-600">98.5%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 98.5%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Test Results Table -->
            <div class="mt-6 bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-list mr-3 text-indigo-600"></i>
                    Recent Test Results
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">2:30 PM</td>
                                <td class="px-4 py-3 text-sm text-gray-900">John Doe</td>
                                <td class="px-4 py-3 text-sm text-gray-900">Blood Sugar</td>
                                <td class="px-4 py-3 text-sm text-gray-900">95 mg/dL</td>
                                <td class="px-4 py-3">
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">Normal</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">2:15 PM</td>
                                <td class="px-4 py-3 text-sm text-gray-900">Mary Smith</td>
                                <td class="px-4 py-3 text-sm text-gray-900">Malaria RDT</td>
                                <td class="px-4 py-3 text-sm text-gray-900">Negative</td>
                                <td class="px-4 py-3">
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">Normal</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">1:45 PM</td>
                                <td class="px-4 py-3 text-sm text-gray-900">Peter Johnson</td>
                                <td class="px-4 py-3 text-sm text-gray-900">Hemoglobin</td>
                                <td class="px-4 py-3 text-sm text-gray-900">13.2 g/dL</td>
                                <td class="px-4 py-3">
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">Normal</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">1:30 PM</td>
                                <td class="px-4 py-3 text-sm text-gray-900">Sarah Wilson</td>
                                <td class="px-4 py-3 text-sm text-gray-900">Urine Analysis</td>
                                <td class="px-4 py-3 text-sm text-gray-900">Normal</td>
                                <td class="px-4 py-3">
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">Normal</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Weekly Report Tab (Hidden by default) -->
        <div id="weekly-report" class="report-content p-6 hidden">
            <div class="text-center py-8">
                <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Weekly Report</h3>
                <p class="text-gray-500">Weekly analytics and trends will be displayed here.</p>
                <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Generate Weekly Report
                </button>
            </div>
        </div>

        <!-- Monthly Report Tab (Hidden by default) -->
        <div id="monthly-report" class="report-content p-6 hidden">
            <div class="text-center py-8">
                <i class="fas fa-chart-pie text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Monthly Report</h3>
                <p class="text-gray-500">Monthly performance metrics and analysis will be displayed here.</p>
                <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Generate Monthly Report
                </button>
            </div>
        </div>

        <!-- Analytics Tab (Hidden by default) -->
        <div id="analytics-report" class="report-content p-6 hidden">
            <div class="text-center py-8">
                <i class="fas fa-analytics text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Advanced Analytics</h3>
                <p class="text-gray-500">Deep analytics and predictive insights will be displayed here.</p>
                <button class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Run Analytics
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showReportTab(tabName) {
    // Hide all report content
    const contents = document.querySelectorAll('.report-content');
    contents.forEach(content => content.classList.add('hidden'));
    
    // Remove active class from all tabs
    const tabs = document.querySelectorAll('.report-tab');
    tabs.forEach(tab => {
        tab.classList.remove('active', 'border-blue-500', 'text-blue-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected content
    document.getElementById(tabName + '-report').classList.remove('hidden');
    
    // Mark selected tab as active
    event.target.classList.add('active', 'border-blue-500', 'text-blue-600');
    event.target.classList.remove('border-transparent', 'text-gray-500');
}

function generateReport() {
    alert('Report generation feature will be implemented here');
}

function exportReport() {
    alert('Export functionality will be implemented here');
}

function scheduleReport() {
    alert('Schedule report feature will be implemented here');
}
</script>
