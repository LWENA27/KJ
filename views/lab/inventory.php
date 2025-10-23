<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Equipment Inventory</h1>
            <p class="text-gray-600 mt-1">Monitor, maintain, and manage laboratory equipment</p>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="openAddEquipmentModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>Add Equipment
            </button>
            <button onclick="openMaintenanceModal()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-wrench mr-2"></i>Schedule Maintenance
            </button>
        </div>
    </div>

    <!-- Inventory Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Operational</p>
                    <p class="text-3xl font-bold">8</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-check text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Maintenance</p>
                    <p class="text-3xl font-bold">2</p>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-wrench text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Out of Order</p>
                    <p class="text-3xl font-bold">1</p>
                </div>
                <div class="bg-red-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-times text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Calibration Due</p>
                    <p class="text-3xl font-bold">3</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-calibrate text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">

        <!-- Microscope -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-microscope mr-3 text-green-600"></i>
                        Microscope #001
                    </h3>
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                        <i class="fas fa-check mr-1"></i>Operational
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Model:</span>
                        <span class="font-medium">Olympus CX23</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Serial:</span>
                        <span class="font-medium">OLY-2023-001</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Last Maintenance:</span>
                        <span class="font-medium">Sep 1, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Next Service:</span>
                        <span class="font-medium">Dec 1, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Usage Today:</span>
                        <span class="font-medium">5 tests</span>
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <button class="flex-1 bg-blue-500 text-white py-2 rounded text-sm hover:bg-blue-600">
                        <i class="fas fa-wrench mr-1"></i>Maintenance
                    </button>
                    <button class="flex-1 bg-gray-500 text-white py-2 rounded text-sm hover:bg-gray-600">
                        <i class="fas fa-history mr-1"></i>History
                    </button>
                </div>
            </div>
        </div>

        <!-- Chemistry Analyzer -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-flask mr-3 text-green-600"></i>
                        Chemistry Analyzer
                    </h3>
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                        <i class="fas fa-check mr-1"></i>Operational
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Model:</span>
                        <span class="font-medium">Abbott Architect c4000</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Serial:</span>
                        <span class="font-medium">ABT-2023-002</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Last Calibration:</span>
                        <span class="font-medium">Sep 10, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Next Service:</span>
                        <span class="font-medium">Oct 10, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Usage Today:</span>
                        <span class="font-medium">12 tests</span>
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <button class="flex-1 bg-purple-500 text-white py-2 rounded text-sm hover:bg-purple-600">
                        <i class="fas fa-balance-scale mr-1"></i>Calibrate
                    </button>
                    <button class="flex-1 bg-gray-500 text-white py-2 rounded text-sm hover:bg-gray-600">
                        <i class="fas fa-history mr-1"></i>History
                    </button>
                </div>
            </div>
        </div>

        <!-- Hematology Analyzer -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-tint mr-3 text-yellow-600"></i>
                        Hematology Analyzer
                    </h3>
                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Maintenance
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Model:</span>
                        <span class="font-medium">Sysmex XN-1000</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Serial:</span>
                        <span class="font-medium">SYS-2023-003</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Issue:</span>
                        <span class="font-medium text-yellow-600">Requires cleaning</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Reported:</span>
                        <span class="font-medium">Sep 13, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Priority:</span>
                        <span class="font-medium">Medium</span>
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <button class="flex-1 bg-yellow-500 text-white py-2 rounded text-sm hover:bg-yellow-600">
                        <i class="fas fa-check mr-1"></i>Mark Fixed
                    </button>
                    <button class="flex-1 bg-gray-500 text-white py-2 rounded text-sm hover:bg-gray-600">
                        <i class="fas fa-history mr-1"></i>History
                    </button>
                </div>
            </div>
        </div>

        <!-- Centrifuge -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-sync-alt mr-3 text-blue-600"></i>
                        Centrifuge
                    </h3>
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                        <i class="fas fa-check-circle mr-1"></i>Operational
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Model:</span>
                        <span class="font-medium">Eppendorf 5810R</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Serial:</span>
                        <span class="font-medium">EPP-2023-004</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Last Service:</span>
                        <span class="font-medium">Aug 15, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Next Service:</span>
                        <span class="font-medium">Feb 15, 2026</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Location:</span>
                        <span class="font-medium">Lab Room 2</span>
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <button class="flex-1 bg-blue-500 text-white py-2 rounded text-sm hover:bg-blue-600">
                        <i class="fas fa-calendar-alt mr-1"></i>Schedule Service
                    </button>
                    <button class="flex-1 bg-gray-500 text-white py-2 rounded text-sm hover:bg-gray-600">
                        <i class="fas fa-history mr-1"></i>History
                    </button>
                </div>
            </div>
        </div>

        <!-- Incubator -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-thermometer-half mr-3 text-green-600"></i>
                        Incubator
                    </h3>
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                        <i class="fas fa-check-circle mr-1"></i>Operational
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Model:</span>
                        <span class="font-medium">Thermo Fisher Heracell</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Serial:</span>
                        <span class="font-medium">TF-2023-005</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Temperature Range:</span>
                        <span class="font-medium">5°C - 60°C</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Last Calibration:</span>
                        <span class="font-medium">Jul 20, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Location:</span>
                        <span class="font-medium">Lab Room 3</span>
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <button class="flex-1 bg-blue-500 text-white py-2 rounded text-sm hover:bg-blue-600">
                        <i class="fas fa-calendar-alt mr-1"></i>Schedule Calibration
                    </button>
                    <button class="flex-1 bg-gray-500 text-white py-2 rounded text-sm hover:bg-gray-600">
                        <i class="fas fa-history mr-1"></i>History
                    </button>
                </div>
            </div>
        </div>

        <!-- Autoclave -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-fire mr-3 text-purple-600"></i>
                        Autoclave
                    </h3>
                    <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-medium">
                        <i class="fas fa-clock mr-1"></i>Calibration Due
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Model:</span>
                        <span class="font-medium">Tuttnauer 3870ELP</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Serial:</span>
                        <span class="font-medium">TTN-2023-006</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Chamber Size:</span>
                        <span class="font-medium">85L</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Last Calibration:</span>
                        <span class="font-medium">Jun 10, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Due Date:</span>
                        <span class="font-medium text-orange-600">Dec 10, 2025</span>
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <button class="flex-1 bg-orange-500 text-white py-2 rounded text-sm hover:bg-orange-600">
                        <i class="fas fa-calendar-check mr-1"></i>Schedule Calibration
                    </button>
                    <button class="flex-1 bg-gray-500 text-white py-2 rounded text-sm hover:bg-gray-600">
                        <i class="fas fa-history mr-1"></i>History
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Schedule -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-calendar-alt mr-3 text-blue-600"></i>
            Upcoming Maintenance Schedule
        </h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-tint text-yellow-600"></i>
                    <div>
                        <p class="font-medium text-gray-900">Hematology Analyzer Cleaning</p>
                        <p class="text-sm text-gray-600">Sysmex XN-1000 - Requires routine cleaning</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-medium text-gray-900">Sep 15, 2025</p>
                    <p class="text-sm text-yellow-600">Medium Priority</p>
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-fire text-orange-600"></i>
                    <div>
                        <p class="font-medium text-gray-900">Autoclave Calibration</p>
                        <p class="text-sm text-gray-600">Tuttnauer 3870ELP - Annual calibration due</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-medium text-gray-900">Dec 10, 2025</p>
                    <p class="text-sm text-orange-600">High Priority</p>
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-sync-alt text-blue-600"></i>
                    <div>
                        <p class="font-medium text-gray-900">Centrifuge Service</p>
                        <p class="text-sm text-gray-600">Eppendorf 5810R - Routine maintenance</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-medium text-gray-900">Feb 15, 2026</p>
                    <p class="text-sm text-blue-600">Low Priority</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Equipment Modal -->
<div id="addItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add New Equipment</h3>
                <button onclick="closeAddItemModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Equipment Name</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Equipment Type</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option>Microscope</option>
                            <option>Analyzer</option>
                            <option>Centrifuge</option>
                            <option>Incubator</option>
                            <option>Autoclave</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Manufacturer</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Date</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <input type="text" placeholder="Lab Room, Building" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option>Operational</option>
                            <option>Maintenance</option>
                            <option>Out of Order</option>
                            <option>Calibration Due</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specifications</label>
                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Technical specifications, capacity, etc."></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeAddItemModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-save mr-2"></i>Add Equipment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Schedule Maintenance Modal -->
<div id="reorderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Schedule Maintenance</h3>
                <button onclick="closeReorderModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <h4 class="font-medium text-gray-900 mb-2">Select Equipment for Maintenance</h4>
                    <div class="space-y-2">
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="microscope" class="rounded">
                            <label for="microscope" class="text-sm">Microscope #001 - Olympus CX23</label>
                        </div>
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="chemistry" class="rounded">
                            <label for="chemistry" class="text-sm">Chemistry Analyzer - Roche Cobas c311</label>
                        </div>
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="hematology" class="rounded">
                            <label for="hematology" class="text-sm">Hematology Analyzer - Sysmex XN-1000</label>
                        </div>
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="centrifuge" class="rounded">
                            <label for="centrifuge" class="text-sm">Centrifuge - Eppendorf 5810R</label>
                        </div>
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="incubator" class="rounded">
                            <label for="incubator" class="text-sm">Incubator - Thermo Fisher Heracell</label>
                        </div>
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" id="autoclave" class="rounded">
                            <label for="autoclave" class="text-sm">Autoclave - Tuttnauer 3870ELP</label>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance Type</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option>Routine Service</option>
                            <option>Calibration</option>
                            <option>Repair</option>
                            <option>Cleaning</option>
                            <option>Software Update</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option>Low Priority</option>
                            <option>Medium Priority</option>
                            <option>High Priority</option>
                            <option>Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Duration</label>
                        <input type="text" placeholder="e.g., 2 hours, 1 day" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance Notes</label>
                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Describe the maintenance required, parts needed, etc."></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeReorderModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-calendar-check mr-2"></i>Schedule Maintenance
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Category switching
function showCategory(category) {
    // Hide all content
    document.querySelectorAll('.category-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active from all tabs
    document.querySelectorAll('.category-tab').forEach(tab => {
        tab.classList.remove('active', 'border-blue-500', 'text-blue-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected content
    document.getElementById(category).classList.remove('hidden');
    
    // Add active to selected tab
    event.target.classList.add('active', 'border-blue-500', 'text-blue-600');
    event.target.classList.remove('border-transparent', 'text-gray-500');
}

// Modal functions
function openAddItemModal() {
    document.getElementById('addItemModal').classList.remove('hidden');
}

function closeAddItemModal() {
    document.getElementById('addItemModal').classList.add('hidden');
}

function openReorderModal() {
    document.getElementById('reorderModal').classList.remove('hidden');
}

function closeReorderModal() {
    document.getElementById('reorderModal').classList.add('hidden');
}

function exportInventory() {
    alert('Equipment inventory export feature would be implemented here');
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    const modals = ['addItemModal', 'reorderModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>
