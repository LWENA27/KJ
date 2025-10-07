<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-microscope mr-3 text-blue-600"></i>
                Lab Technician Dashboard
            </h1>
            <p class="text-gray-600 mt-1">Manage tests, equipment, and patient results</p>
            <!-- Live Status Bar -->
            <div class="flex items-center mt-2 space-x-4">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-xs text-green-600 font-medium">System Online</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                    <span class="text-xs text-blue-600 font-medium" id="active-tests">
                        <?php echo $stats['pending_tests']; ?> Active Tests
                    </span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                    <span class="text-xs text-yellow-600 font-medium">1 Alert</span>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-right">
                <div class="text-sm text-gray-500"><?php echo date('l, F j, Y'); ?></div>
                <div class="text-xs text-gray-400" id="current-time"><?php echo date('h:i A'); ?></div>
            </div>
            <!-- Emergency Alert Button -->
            <button onclick="triggerEmergencyAlert()" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-exclamation-triangle mr-2"></i>Emergency
            </button>
            <button onclick="openEquipmentModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-tools mr-2"></i>Equipment Status
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- Total Tests -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer group" onclick="showTestDetails('total')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Tests</p>
                    <p class="text-3xl font-bold"><?php echo $stats['total_tests']; ?></p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-chart-line mr-2 text-blue-200"></i>
                        <span class="text-sm text-blue-200">+12% from yesterday</span>
                    </div>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3 group-hover:bg-opacity-50 transition-all">
                    <i class="fas fa-flask text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Tests -->
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer group" onclick="showTestDetails('pending')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Pending Tests</p>
                    <p class="text-3xl font-bold"><?php echo $stats['pending_tests']; ?></p>
                    <div class="flex items-center mt-1">
                        <i class="fas fa-clock mr-2 text-yellow-200"></i>
                        <span class="text-sm text-yellow-200">Avg wait: 15 min</span>
                    </div>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3 group-hover:bg-opacity-50 transition-all">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-yellow-100">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Requires immediate attention
            </div>
        </div>

        <!-- Completed Tests -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer group" onclick="showTestDetails('completed')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Completed Tests</p>
                    <p class="text-3xl font-bold"><?php echo $stats['completed_tests']; ?></p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-check-double mr-2 text-green-200"></i>
                        <span class="text-sm text-green-200">100% accuracy</span>
                    </div>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3 group-hover:bg-opacity-50 transition-all">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Today's Work -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer group" onclick="showTestDetails('today')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Completed Today</p>
                    <p class="text-3xl font-bold"><?php echo $completed_today; ?></p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-trophy mr-2 text-purple-200"></i>
                        <span class="text-sm text-purple-200">Goal: 25 tests</span>
                    </div>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3 group-hover:bg-opacity-50 transition-all">
                    <i class="fas fa-calendar-day text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Equipment Status -->
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer group" onclick="openEquipmentModal()">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm font-medium">Equipment</p>
                    <p class="text-3xl font-bold">8/10</p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-tools mr-2 text-indigo-200"></i>
                        <span class="text-sm text-indigo-200">Next maintenance: 3 days</span>
                    </div>
                </div>
                <div class="bg-indigo-400 bg-opacity-30 rounded-full p-3 group-hover:bg-opacity-50 transition-all">
                    <i class="fas fa-microscope text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-indigo-100">
                <i class="fas fa-check mr-1"></i>
                Operational
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Pending Test Queue (Left Column - 2/3 width) --> <!-- Pending Test Queue (Left Column - 2/3 width) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-orange-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-clipboard-list mr-3 text-yellow-600"></i>
                            Patients Ready for Lab Testing
                        </h3>
                        <div class="flex items-center space-x-3">
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                                <?php echo count($pending_tests); ?> patients waiting
                            </span>
                            <select id="queueFilter" class="text-sm border-gray-300 rounded-md">
                                <option value="all">All Patients</option>
                                <option value="urgent">Urgent</option>
                                <option value="routine">Routine</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="p-6 max-h-96 overflow-y-auto">
                    <?php if (empty($pending_tests)): ?>
                        <div class="text-center py-12">
                            <div class="w-20 h-20 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Patients Waiting!</h4>
                            <p class="text-gray-500">All lab test patients processed</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($pending_tests as $index => $patient): ?>
                                <div class="group border-l-4 <?php echo $patient['priority'] === 'urgent' ? 'border-red-400' : 'border-yellow-400'; ?> 
                                  bg-white hover:bg-gray-50 rounded-lg p-4 transition-all duration-200 hover:shadow-md">
                                    <div class="flex items-center justify-between">
                                        <!-- Queue Number -->
                                        <div class="flex flex-col items-center">
                                            <div class="w-8 h-8 <?php echo $patient['priority'] === 'urgent' ? 'bg-red-500' : 'bg-yellow-500'; ?> 
                                          text-white rounded-full flex items-center justify-center text-sm font-bold">
                                                <?php echo $index + 1; ?>
                                            </div>
                                            <span class="text-xs <?php echo $patient['priority'] === 'urgent' ? 'text-red-600' : 'text-yellow-600'; ?> mt-1">
                                                <?php echo $patient['priority'] === 'urgent' ? 'URGENT' : 'Queue'; ?>
                                            </span>
                                        </div>

                                        <!-- Patient Info (Simplified: Name, Reg Number only) -->
                                        <div class="flex-1 ml-4">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <h4 class="font-semibold text-gray-900">
                                                    <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                                </h4>
                                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">
                                                    <?php echo htmlspecialchars($patient['registration_number'] ?? str_pad($patient['registration_number'], STR_PAD_LEFT)); ?>
                                                </span>
                                               
                                            </div>
                                        </div>

                                        <!-- Action Button (Start Testing Form) -->
                                        <div class="flex items-center space-x-2">
                                            
                                            <a href="/KJ/lab/view_test/<?php echo $patient['id']; ?>"
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                                <i class="fas fa-eye mr-1"></i>Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column - Equipment & Quick Actions -->
        <div class="space-y-6">
            <!-- Equipment Status Panel -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-microscope mr-3 text-indigo-600"></i>
                        Equipment Status
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <!-- Microscope -->
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-microscope text-green-600 mr-3"></i>
                                <span class="font-medium text-gray-900">Microscope</span>
                            </div>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                                <i class="fas fa-check mr-1"></i>Operational
                            </span>
                        </div>

                        <!-- Chemistry Analyzer -->
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-vial text-green-600 mr-3"></i>
                                <span class="font-medium text-gray-900">Chemistry Analyzer</span>
                            </div>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                                <i class="fas fa-check mr-1"></i>Operational
                            </span>
                        </div>

                        <!-- Hematology Analyzer -->
                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-tint text-yellow-600 mr-3"></i>
                                <span class="font-medium text-gray-900">Hematology Analyzer</span>
                            </div>
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Maintenance
                            </span>
                        </div>

                        <!-- Centrifuge -->
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-sync-alt text-green-600 mr-3"></i>
                                <span class="font-medium text-gray-900">Centrifuge</span>
                            </div>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                                <i class="fas fa-check mr-1"></i>Operational
                            </span>
                        </div>

                        <!-- Rapid Test Kit -->
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-kit-medical text-red-600 mr-3"></i>
                                <span class="font-medium text-gray-900">Rapid Test Kits</span>
                            </div>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">
                                <i class="fas fa-times mr-1"></i>Low Stock
                            </span>
                        </div>
                    </div>

                    <button onclick="openEquipmentModal()" class="w-full mt-4 bg-indigo-500 hover:bg-indigo-600 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-cog mr-2"></i>Manage Equipment
                    </button>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-bolt mr-3 text-yellow-600"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-3">
                        <a href="/KJ/lab/tests" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                            <div class="bg-blue-500 group-hover:bg-blue-600 text-white rounded-lg p-2 mr-3">
                                <i class="fas fa-list"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">View All Tests</div>
                                <div class="text-sm text-gray-600">Complete test history</div>
                            </div>
                        </a>

                        <a href="/KJ/lab/results" class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                            <div class="bg-green-500 group-hover:bg-green-600 text-white rounded-lg p-2 mr-3">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Record Results</div>
                                <div class="text-sm text-gray-600">Enter test results</div>
                            </div>
                        </a>

                        <button onclick="openReportModal()" class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group w-full text-left">
                            <div class="bg-purple-500 group-hover:bg-purple-600 text-white rounded-lg p-2 mr-3">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Generate Reports</div>
                                <div class="text-sm text-gray-600">Daily/weekly reports</div>
                            </div>
                        </button>

                        <button onclick="openInventoryModal()" class="flex items-center p-3 bg-orange-50 hover:bg-orange-100 rounded-lg transition-colors group w-full text-left">
                            <div class="bg-orange-500 group-hover:bg-orange-600 text-white rounded-lg p-2 mr-3">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Check Inventory</div>
                                <div class="text-sm text-gray-600">Reagents & supplies</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-history mr-3 text-gray-600"></i>
                Recent Activity
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <div class="flex items-center p-3 bg-green-50 rounded-lg">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white mr-3">
                        <i class="fas fa-check text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Blood Sugar test completed for John Doe</p>
                        <p class="text-xs text-gray-500">2 minutes ago</p>
                    </div>
                </div>

                <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white mr-3">
                        <i class="fas fa-play text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Started Hemoglobin test for Maria Smith</p>
                        <p class="text-xs text-gray-500">15 minutes ago</p>
                    </div>
                </div>

                <div class="flex items-center p-3 bg-yellow-50 rounded-lg">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center text-white mr-3">
                        <i class="fas fa-tools text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Equipment maintenance scheduled for tomorrow</p>
                        <p class="text-xs text-gray-500">1 hour ago</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Equipment Management Modal -->
<div id="equipmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-tools mr-3 text-indigo-600"></i>
                    Equipment Management
                </h3>
                <button onclick="closeEquipmentModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-4">
                <!-- Equipment List -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900">Microscope #001</h4>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Operational</span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><strong>Last Maintenance:</strong> Sep 1, 2025</p>
                            <p><strong>Next Service:</strong> Dec 1, 2025</p>
                            <p><strong>Usage Today:</strong> 5 tests</p>
                        </div>
                        <button class="mt-3 w-full bg-blue-500 text-white py-2 rounded text-sm">
                            Schedule Maintenance
                        </button>
                    </div>

                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900">Chemistry Analyzer</h4>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Operational</span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><strong>Last Calibration:</strong> Sep 10, 2025</p>
                            <p><strong>Next Service:</strong> Oct 10, 2025</p>
                            <p><strong>Usage Today:</strong> 12 tests</p>
                        </div>
                        <button class="mt-3 w-full bg-blue-500 text-white py-2 rounded text-sm">
                            Run Calibration
                        </button>
                    </div>

                    <div class="border rounded-lg p-4 bg-yellow-50">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900">Hematology Analyzer</h4>
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Maintenance</span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><strong>Issue:</strong> Requires cleaning</p>
                            <p><strong>Reported:</strong> Sep 13, 2025</p>
                            <p><strong>Priority:</strong> Medium</p>
                        </div>
                        <button class="mt-3 w-full bg-yellow-500 text-white py-2 rounded text-sm">
                            Mark as Fixed
                        </button>
                    </div>

                    <div class="border rounded-lg p-4 bg-red-50">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900">Rapid Test Kits</h4>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Low Stock</span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><strong>Current Stock:</strong> 15 kits</p>
                            <p><strong>Minimum Required:</strong> 50 kits</p>
                            <p><strong>Last Order:</strong> Aug 20, 2025</p>
                        </div>
                        <button class="mt-3 w-full bg-red-500 text-white py-2 rounded text-sm">
                            Request Restock
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Start Modal -->
<div id="testStartModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-play mr-3 text-green-600"></i>
                    Start Test
                </h3>
                <button onclick="closeTestStartModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="startTestForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Name</label>
                    <input type="text" id="testName" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Equipment Required</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option>Microscope #001</option>
                        <option>Chemistry Analyzer</option>
                        <option>Hematology Analyzer</option>
                        <option>Centrifuge</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Duration</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option>15 minutes</option>
                        <option>30 minutes</option>
                        <option>1 hour</option>
                        <option>2 hours</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        placeholder="Any special instructions or observations..."></textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeTestStartModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        <i class="fas fa-play mr-2"></i>Start Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reports Modal -->
<div id="reportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-chart-bar mr-3 text-purple-600"></i>
                    Generate Reports
                </h3>
                <button onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-colors">
                        <i class="fas fa-calendar-day text-2xl text-purple-600 mb-2"></i>
                        <div class="font-medium">Daily Report</div>
                        <div class="text-sm text-gray-600">Today's activities</div>
                    </button>

                    <button class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-colors">
                        <i class="fas fa-calendar-week text-2xl text-purple-600 mb-2"></i>
                        <div class="font-medium">Weekly Report</div>
                        <div class="text-sm text-gray-600">Last 7 days</div>
                    </button>

                    <button class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-colors">
                        <i class="fas fa-flask text-2xl text-purple-600 mb-2"></i>
                        <div class="font-medium">Test Summary</div>
                        <div class="text-sm text-gray-600">By test type</div>
                    </button>

                    <button class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-colors">
                        <i class="fas fa-tools text-2xl text-purple-600 mb-2"></i>
                        <div class="font-medium">Equipment Report</div>
                        <div class="text-sm text-gray-600">Usage & maintenance</div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Modal -->
<div id="inventoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-boxes mr-3 text-orange-600"></i>
                    Lab Inventory
                </h3>
                <button onclick="closeInventoryModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-4">
                <!-- Inventory Categories -->
                <div class="flex space-x-4 border-b">
                    <button class="px-4 py-2 border-b-2 border-orange-500 text-orange-600 font-medium">Reagents</button>
                    <button class="px-4 py-2 text-gray-600 hover:text-orange-600">Consumables</button>
                    <button class="px-4 py-2 text-gray-600 hover:text-orange-600">Test Kits</button>
                </div>

                <!-- Inventory Items -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-64 overflow-y-auto">
                    <div class="border rounded-lg p-3">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium">Blood Sugar Strips</h4>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">In Stock</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p>Current: 150 strips</p>
                            <p>Minimum: 50 strips</p>
                            <p>Expires: Dec 2025</p>
                        </div>
                    </div>

                    <div class="border rounded-lg p-3 bg-yellow-50">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium">Hemoglobin Reagent</h4>
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Low</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p>Current: 25 ml</p>
                            <p>Minimum: 100 ml</p>
                            <p>Expires: Jan 2026</p>
                        </div>
                    </div>

                    <div class="border rounded-lg p-3 bg-red-50">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium">Urine Test Strips</h4>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Critical</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p>Current: 10 strips</p>
                            <p>Minimum: 100 strips</p>
                            <p>Expires: Nov 2025</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600">
                        <i class="fas fa-download mr-2"></i>Export Inventory
                    </button>
                    <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-plus mr-2"></i>Request Supplies
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Enhanced Dashboard Functions

    // Real-time Clock Update
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
        document.getElementById('current-time').textContent = timeString;
    }

    // Update clock every minute
    setInterval(updateClock, 60000);

    // Equipment Modal Functions
    function openEquipmentModal() {
        document.getElementById('equipmentModal').classList.remove('hidden');
    }

    function closeEquipmentModal() {
        document.getElementById('equipmentModal').classList.add('hidden');
    }

    // Test Start Modal Functions
    function startTest(testId, testName) {
        document.getElementById('testName').value = testName;
        document.getElementById('testStartModal').classList.remove('hidden');
    }

    function closeTestStartModal() {
        document.getElementById('testStartModal').classList.add('hidden');
    }

    // Enhanced Statistics Functions
    function showTestDetails(type) {
        const details = {
            total: {
                title: 'Total Tests Overview',
                content: 'View complete testing history and analytics'
            },
            pending: {
                title: 'Pending Tests Queue',
                content: 'Manage and prioritize pending laboratory tests'
            },
            completed: {
                title: 'Completed Tests',
                content: 'Review completed tests and results'
            },
            today: {
                title: 'Today\'s Performance',
                content: 'Track daily productivity and goals'
            }
        };

        if (details[type]) {
            showNotification(details[type].title, details[type].content, 'info');
        }
    }

    // Emergency Alert System
    function triggerEmergencyAlert() {
        if (confirm('Are you sure you want to trigger an emergency alert? This will notify all medical staff.')) {
            showNotification('Emergency Alert Triggered', 'All medical staff have been notified', 'warning');
            // In real implementation, this would send actual alerts
        }
    }

    // Enhanced Notification System
    function showNotification(title, message, type = 'info') {
        const colors = {
            info: 'bg-blue-500',
            success: 'bg-green-500',
            warning: 'bg-yellow-500',
            error: 'bg-red-500'
        };

        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50`;
        notification.innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <div class="font-semibold">${title}</div>
                <div class="text-sm opacity-90">${message}</div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

        document.body.appendChild(notification);

        // Slide in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Auto-refresh pending tests count (simulated)
    function refreshTestCounts() {
        // In real implementation, this would fetch from API
        console.log('Refreshing test counts...');
    }

    // Refresh every 30 seconds
    setInterval(refreshTestCounts, 30000);

    // Check for new patients
    function checkNewPatients() {
        fetch('/KJ/lab/check_new_patients', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.new_patients > 0) {
                    showNotification(
                        'New Patients Waiting',
                        `${data.new_patients} patient(s) waiting for lab tests`,
                        'info'
                    );
                    // Refresh the pending patients list
                    location.reload();
                }
            });
    }

    // Check every 30 seconds for new patients
    setInterval(checkNewPatients, 30000);

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Alt + E for Equipment
        if (e.altKey && e.key === 'e') {
            e.preventDefault();
            openEquipmentModal();
        }

        // Alt + R for Reports
        if (e.altKey && e.key === 'r') {
            e.preventDefault();
            openReportModal();
        }

        // Alt + I for Inventory
        if (e.altKey && e.key === 'i') {
            e.preventDefault();
            openInventoryModal();
        }
    });

    // Show keyboard shortcuts help
    function showKeyboardShortcuts() {
        const shortcuts = `
        <div class="space-y-2">
            <div><kbd class="px-2 py-1 bg-gray-100 rounded text-sm">Alt + E</kbd> - Equipment Status</div>
            <div><kbd class="px-2 py-1 bg-gray-100 rounded text-sm">Alt + R</kbd> - Generate Reports</div>
            <div><kbd class="px-2 py-1 bg-gray-100 rounded text-sm">Alt + I</kbd> - Check Inventory</div>
        </div>
    `;
        showNotification('Keyboard Shortcuts', shortcuts, 'info');
    }

    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', function() {
        updateClock();
        console.log('Lab Dashboard Enhanced - Ready for optimal workflow!');
    });

    // Report Modal Functions
    function openReportModal() {
        document.getElementById('reportModal').classList.remove('hidden');
    }

    function closeReportModal() {
        document.getElementById('reportModal').classList.add('hidden');
    }

    // Inventory Modal Functions
    function openInventoryModal() {
        document.getElementById('inventoryModal').classList.remove('hidden');
    }

    function closeInventoryModal() {
        document.getElementById('inventoryModal').classList.add('hidden');
    }

    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        const modals = ['equipmentModal', 'testStartModal', 'reportModal', 'inventoryModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });

    // Start Test Form Submission
    document.getElementById('startTestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // Add your test start logic here
        alert('Test started successfully!');
        closeTestStartModal();
    });

    // Auto-refresh dashboard every 30 seconds
    setInterval(function() {
        // You can add AJAX call here to refresh pending tests
        console.log('Auto-refreshing dashboard...');
    }, 30000);
</script>