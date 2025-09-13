<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Quality Control</h1>
            <p class="text-gray-600 mt-1">Ensure accuracy and reliability of laboratory results</p>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="openQCTestModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>Run QC Test
            </button>
            <button onclick="openCalibrationModal()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-balance-scale mr-2"></i>Calibration
            </button>
        </div>
    </div>

    <!-- QC Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Passed Tests</p>
                    <p class="text-3xl font-bold">94</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-check text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-green-100">
                <i class="fas fa-arrow-up mr-1"></i>
                98.9% success rate
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Failed Tests</p>
                    <p class="text-3xl font-bold">1</p>
                </div>
                <div class="bg-red-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-times text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-red-100">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Requires attention
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">This Month</p>
                    <p class="text-3xl font-bold">95</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-calendar text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-blue-100">
                <i class="fas fa-chart-line mr-1"></i>
                +12% from last month
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Calibrations</p>
                    <p class="text-3xl font-bold">8</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-balance-scale text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-purple-100">
                <i class="fas fa-clock mr-1"></i>
                2 due this week
            </div>
        </div>
    </div>

    <!-- QC Status Dashboard -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent QC Tests -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-vial mr-3 text-blue-600"></i>
                    Recent QC Tests
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Glucose QC -->
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Glucose Control Level 1</h4>
                                <p class="text-sm text-gray-600">Expected: 90-110 mg/dL | Result: 102 mg/dL</p>
                                <p class="text-xs text-gray-500">Sep 13, 2025 - 08:30 AM</p>
                            </div>
                        </div>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">PASS</span>
                    </div>

                    <!-- Hemoglobin QC -->
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Hemoglobin Control</h4>
                                <p class="text-sm text-gray-600">Expected: 12.0-14.0 g/dL | Result: 13.2 g/dL</p>
                                <p class="text-xs text-gray-500">Sep 13, 2025 - 07:45 AM</p>
                            </div>
                        </div>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">PASS</span>
                    </div>

                    <!-- Failed QC -->
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border-l-4 border-red-500">
                        <div class="flex items-center">
                            <i class="fas fa-times-circle text-red-600 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Cholesterol Control Level 2</h4>
                                <p class="text-sm text-gray-600">Expected: 180-220 mg/dL | Result: 165 mg/dL</p>
                                <p class="text-xs text-gray-500">Sep 12, 2025 - 02:15 PM</p>
                            </div>
                        </div>
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">FAIL</span>
                    </div>

                    <!-- Pending QC -->
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-yellow-600 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Protein Control</h4>
                                <p class="text-sm text-gray-600">Due: Daily QC Test</p>
                                <p class="text-xs text-gray-500">Last run: Sep 11, 2025</p>
                            </div>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">PENDING</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calibration Status -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-balance-scale mr-3 text-purple-600"></i>
                    Calibration Status
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Chemistry Analyzer -->
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                        <div class="flex items-center">
                            <i class="fas fa-flask text-green-600 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Chemistry Analyzer</h4>
                                <p class="text-sm text-gray-600">Last Calibration: Sep 10, 2025</p>
                                <p class="text-xs text-gray-500">Next due: Oct 10, 2025</p>
                            </div>
                        </div>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">CURRENT</span>
                    </div>

                    <!-- Hematology Analyzer -->
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                        <div class="flex items-center">
                            <i class="fas fa-tint text-yellow-600 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Hematology Analyzer</h4>
                                <p class="text-sm text-gray-600">Last Calibration: Aug 20, 2025</p>
                                <p class="text-xs text-gray-500">Next due: Sep 20, 2025</p>
                            </div>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">DUE SOON</span>
                    </div>

                    <!-- Autoclave -->
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border-l-4 border-red-500">
                        <div class="flex items-center">
                            <i class="fas fa-fire text-red-600 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Autoclave</h4>
                                <p class="text-sm text-gray-600">Last Calibration: Jun 13, 2025</p>
                                <p class="text-xs text-gray-500">Overdue since: Sep 13, 2025</p>
                            </div>
                        </div>
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">OVERDUE</span>
                    </div>

                    <!-- Microscope -->
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                        <div class="flex items-center">
                            <i class="fas fa-microscope text-blue-600 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Microscope</h4>
                                <p class="text-sm text-gray-600">Last Maintenance: Sep 1, 2025</p>
                                <p class="text-xs text-gray-500">Next service: Dec 1, 2025</p>
                            </div>
                        </div>
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">SCHEDULED</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QC Control Materials -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-clipboard-list mr-3 text-indigo-600"></i>
                QC Control Materials
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Glucose Control -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900">Glucose Control Level 1</h4>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Active</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Lot Number:</span>
                            <span class="font-medium">GLC-L1-2025-045</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expected Range:</span>
                            <span class="font-medium">90-110 mg/dL</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">Dec 2025</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Last QC:</span>
                            <span class="font-medium">Sep 13, 2025</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Status:</span>
                            <span class="font-medium text-green-600">Within Range</span>
                        </div>
                    </div>
                    <button class="w-full mt-3 bg-blue-500 text-white py-2 rounded text-sm hover:bg-blue-600">
                        <i class="fas fa-play mr-1"></i>Run QC Test
                    </button>
                </div>

                <!-- Hemoglobin Control -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900">Hemoglobin Control</h4>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Active</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Lot Number:</span>
                            <span class="font-medium">HGB-2025-078</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expected Range:</span>
                            <span class="font-medium">12.0-14.0 g/dL</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">Nov 2025</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Last QC:</span>
                            <span class="font-medium">Sep 13, 2025</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Status:</span>
                            <span class="font-medium text-green-600">Within Range</span>
                        </div>
                    </div>
                    <button class="w-full mt-3 bg-blue-500 text-white py-2 rounded text-sm hover:bg-blue-600">
                        <i class="fas fa-play mr-1"></i>Run QC Test
                    </button>
                </div>

                <!-- Cholesterol Control -->
                <div class="border rounded-lg p-4 bg-red-50">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900">Cholesterol Control L2</h4>
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Failed</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Lot Number:</span>
                            <span class="font-medium">CHOL-L2-2025-032</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expected Range:</span>
                            <span class="font-medium">180-220 mg/dL</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">Jan 2026</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Last QC:</span>
                            <span class="font-medium">Sep 12, 2025</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Status:</span>
                            <span class="font-medium text-red-600">Out of Range</span>
                        </div>
                    </div>
                    <button class="w-full mt-3 bg-red-500 text-white py-2 rounded text-sm hover:bg-red-600">
                        <i class="fas fa-redo mr-1"></i>Rerun QC Test
                    </button>
                </div>

                <!-- Protein Control -->
                <div class="border rounded-lg p-4 bg-yellow-50">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900">Protein Control</h4>
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Lot Number:</span>
                            <span class="font-medium">PROT-2025-089</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expected Range:</span>
                            <span class="font-medium">6.5-8.5 g/dL</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">Feb 2026</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Last QC:</span>
                            <span class="font-medium">Sep 11, 2025</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Status:</span>
                            <span class="font-medium text-yellow-600">QC Due</span>
                        </div>
                    </div>
                    <button class="w-full mt-3 bg-yellow-500 text-white py-2 rounded text-sm hover:bg-yellow-600">
                        <i class="fas fa-clock mr-1"></i>Run Due QC
                    </button>
                </div>

                <!-- Urea Control -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900">Urea Control</h4>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Active</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Lot Number:</span>
                            <span class="font-medium">UREA-2025-067</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expected Range:</span>
                            <span class="font-medium">25-35 mg/dL</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">Apr 2026</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Last QC:</span>
                            <span class="font-medium">Sep 12, 2025</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Status:</span>
                            <span class="font-medium text-green-600">Within Range</span>
                        </div>
                    </div>
                    <button class="w-full mt-3 bg-blue-500 text-white py-2 rounded text-sm hover:bg-blue-600">
                        <i class="fas fa-play mr-1"></i>Run QC Test
                    </button>
                </div>

                <!-- Creatinine Control -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900">Creatinine Control</h4>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Active</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Lot Number:</span>
                            <span class="font-medium">CREAT-2025-054</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expected Range:</span>
                            <span class="font-medium">1.0-1.4 mg/dL</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">Mar 2026</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Last QC:</span>
                            <span class="font-medium">Sep 11, 2025</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Status:</span>
                            <span class="font-medium text-green-600">Within Range</span>
                        </div>
                    </div>
                    <button class="w-full mt-3 bg-blue-500 text-white py-2 rounded text-sm hover:bg-blue-600">
                        <i class="fas fa-play mr-1"></i>Run QC Test
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Run QC Test Modal -->
<div id="qcTestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Run Quality Control Test</h3>
                <button onclick="closeQCTestModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Control Material</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option>Glucose Control Level 1</option>
                            <option>Hemoglobin Control</option>
                            <option>Cholesterol Control L2</option>
                            <option>Protein Control</option>
                            <option>Urea Control</option>
                            <option>Creatinine Control</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Test Type</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option>Daily QC</option>
                            <option>Weekly QC</option>
                            <option>New Lot QC</option>
                            <option>Post-Maintenance QC</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expected Value</label>
                        <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Acceptable Range</label>
                        <input type="text" placeholder="e.g., ±10%" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Actual Result</label>
                        <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Technician</label>
                        <input type="text" value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Comments</label>
                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Additional observations or notes..."></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeQCTestModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-save mr-2"></i>Record QC Result
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Calibration Modal -->
<div id="calibrationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Equipment Calibration</h3>
                <button onclick="closeCalibrationModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Equipment</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option>Chemistry Analyzer</option>
                            <option>Hematology Analyzer</option>
                            <option>Autoclave</option>
                            <option>Microscope</option>
                            <option>Centrifuge</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Calibration Type</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option>Full Calibration</option>
                            <option>Single Point Calibration</option>
                            <option>Two Point Calibration</option>
                            <option>Temperature Calibration</option>
                            <option>Pressure Calibration</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Standard Used</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reference Value</label>
                        <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Measured Value</label>
                        <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Next Calibration Due</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Calibration Results</label>
                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Record calibration results and any adjustments made..."></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeCalibrationModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-purple-500 text-white rounded-md hover:bg-purple-600">
                        <i class="fas fa-balance-scale mr-2"></i>Record Calibration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openQCTestModal() {
    document.getElementById('qcTestModal').classList.remove('hidden');
}

function closeQCTestModal() {
    document.getElementById('qcTestModal').classList.add('hidden');
}

function openCalibrationModal() {
    document.getElementById('calibrationModal').classList.remove('hidden');
}

function closeCalibrationModal() {
    document.getElementById('calibrationModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    const modals = ['qcTestModal', 'calibrationModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>
