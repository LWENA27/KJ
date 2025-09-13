<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Equipment Management</h1>
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

    <!-- Equipment Statistics -->
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
            <div class="bg-gradient-to-r from-green-50 to-green-100 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-sync-alt mr-3 text-green-600"></i>
                        Centrifuge
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
                        <span class="font-medium">Eppendorf 5424R</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Serial:</span>
                        <span class="font-medium">EPP-2023-004</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Last Maintenance:</span>
                        <span class="font-medium">Aug 15, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Next Service:</span>
                        <span class="font-medium">Nov 15, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Usage Today:</span>
                        <span class="font-medium">8 cycles</span>
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

        <!-- Incubator -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-red-50 to-red-100 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-thermometer-half mr-3 text-red-600"></i>
                        Incubator
                    </h3>
                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">
                        <i class="fas fa-times mr-1"></i>Out of Order
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Model:</span>
                        <span class="font-medium">Thermo Heratherm</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Serial:</span>
                        <span class="font-medium">THM-2023-005</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Issue:</span>
                        <span class="font-medium text-red-600">Temperature control failure</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Reported:</span>
                        <span class="font-medium">Sep 12, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Priority:</span>
                        <span class="font-medium text-red-600">High</span>
                    </div>
                </div>
                <div class="mt-4 flex space-x-2">
                    <button class="flex-1 bg-red-500 text-white py-2 rounded text-sm hover:bg-red-600">
                        <i class="fas fa-phone mr-1"></i>Call Service
                    </button>
                    <button class="flex-1 bg-gray-500 text-white py-2 rounded text-sm hover:bg-gray-600">
                        <i class="fas fa-history mr-1"></i>History
                    </button>
                </div>
            </div>
        </div>

        <!-- Autoclave -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-fire mr-3 text-blue-600"></i>
                        Autoclave
                    </h3>
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">
                        <i class="fas fa-balance-scale mr-1"></i>Calibration Due
                    </span>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Model:</span>
                        <span class="font-medium">Tuttnauer 2540E</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Serial:</span>
                        <span class="font-medium">TUT-2023-006</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Last Calibration:</span>
                        <span class="font-medium">Jun 13, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Calibration Due:</span>
                        <span class="font-medium text-blue-600">Sep 13, 2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Usage Today:</span>
                        <span class="font-medium">3 cycles</span>
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
    </div>

    <!-- Maintenance Schedule -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-calendar-alt mr-3 text-indigo-600"></i>
                Upcoming Maintenance Schedule
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border-l-4 border-red-500">
                    <div class="flex items-center">
                        <i class="fas fa-fire text-red-600 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">Autoclave - Calibration</h4>
                            <p class="text-sm text-gray-600">Due: Today (Sep 13, 2025)</p>
                        </div>
                    </div>
                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">Overdue</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <i class="fas fa-flask text-yellow-600 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">Chemistry Analyzer - Service</h4>
                            <p class="text-sm text-gray-600">Due: Oct 10, 2025 (27 days)</p>
                        </div>
                    </div>
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">Upcoming</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <i class="fas fa-sync-alt text-blue-600 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">Centrifuge - Service</h4>
                            <p class="text-sm text-gray-600">Due: Nov 15, 2025 (63 days)</p>
                        </div>
                    </div>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">Scheduled</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border-l-4 border-green-500">
                    <div class="flex items-center">
                        <i class="fas fa-microscope text-green-600 mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">Microscope - Service</h4>
                            <p class="text-sm text-gray-600">Due: Dec 1, 2025 (79 days)</p>
                        </div>
                    </div>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">Scheduled</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Equipment Modal -->
<div id="addEquipmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add New Equipment</h3>
                <button onclick="closeAddEquipmentModal()" class="text-gray-400 hover:text-gray-600">
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warranty Expires</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeAddEquipmentModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-save mr-2"></i>Save Equipment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Schedule Maintenance Modal -->
<div id="maintenanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Schedule Maintenance</h3>
                <button onclick="closeMaintenanceModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Equipment</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option>Select Equipment</option>
                        <option>Microscope #001</option>
                        <option>Chemistry Analyzer</option>
                        <option>Hematology Analyzer</option>
                        <option>Centrifuge</option>
                        <option>Autoclave</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maintenance Type</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option>Routine Maintenance</option>
                            <option>Calibration</option>
                            <option>Repair</option>
                            <option>Deep Cleaning</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Additional maintenance notes..."></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeMaintenanceModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        <i class="fas fa-calendar-plus mr-2"></i>Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddEquipmentModal() {
    document.getElementById('addEquipmentModal').classList.remove('hidden');
}

function closeAddEquipmentModal() {
    document.getElementById('addEquipmentModal').classList.add('hidden');
}

function openMaintenanceModal() {
    document.getElementById('maintenanceModal').classList.remove('hidden');
}

function closeMaintenanceModal() {
    document.getElementById('maintenanceModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    const modals = ['addEquipmentModal', 'maintenanceModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>
