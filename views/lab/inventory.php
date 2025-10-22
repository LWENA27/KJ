<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Inventory Management</h1>
            <p class="text-gray-600 mt-1">Track reagents, supplies, and consumables</p>
        </div>
        <div class="flex items-center space-x-4">
            <button onclick="openAddItemModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>Add Item
            </button>
            <button onclick="openReorderModal()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-shopping-cart mr-2"></i>Reorder
            </button>
            <button onclick="exportInventory()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-download mr-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Inventory Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Items</p>
                    <p class="text-3xl font-bold">247</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-boxes text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">In Stock</p>
                    <p class="text-3xl font-bold">189</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-check text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Low Stock</p>
                    <p class="text-3xl font-bold">32</p>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Out of Stock</p>
                    <p class="text-3xl font-bold">26</p>
                </div>
                <div class="bg-red-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-times text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Tabs -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showCategory('reagents')" class="category-tab active border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600">
                    <i class="fas fa-flask mr-2"></i>Reagents
                </button>
                <button onclick="showCategory('consumables')" class="category-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                    <i class="fas fa-syringe mr-2"></i>Consumables
                </button>
                <button onclick="showCategory('test-kits')" class="category-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                    <i class="fas fa-kit-medical mr-2"></i>Test Kits
                </button>
                <button onclick="showCategory('expired')" class="category-tab border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                    <i class="fas fa-clock mr-2"></i>Expiring Soon
                </button>
            </nav>
        </div>

        <!-- Reagents Tab -->
        <div id="reagents" class="category-content p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Blood Sugar Reagent -->
                <div class="border rounded-lg p-4 bg-green-50">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-medium text-gray-900">Blood Sugar Reagent</h4>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">In Stock</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Current Stock:</span>
                            <span class="font-medium">450 ml</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Minimum Required:</span>
                            <span class="font-medium">100 ml</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Lot Number:</span>
                            <span class="font-medium">BS2025-089</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">Mar 2026</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Cost per Unit:</span>
                            <span class="font-medium">$2.50/ml</span>
                        </div>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <button class="flex-1 bg-blue-500 text-white py-1 rounded text-sm hover:bg-blue-600">
                            <i class="fas fa-edit mr-1"></i>Update
                        </button>
                        <button class="flex-1 bg-gray-500 text-white py-1 rounded text-sm hover:bg-gray-600">
                            <i class="fas fa-history mr-1"></i>History
                        </button>
                    </div>
                </div>

                <!-- Hemoglobin Reagent -->
                <div class="border rounded-lg p-4 bg-yellow-50">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-medium text-gray-900">Hemoglobin Reagent</h4>
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">Low Stock</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Current Stock:</span>
                            <span class="font-medium text-yellow-600">25 ml</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Minimum Required:</span>
                            <span class="font-medium">100 ml</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Lot Number:</span>
                            <span class="font-medium">HB2025-067</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">Jan 2026</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Cost per Unit:</span>
                            <span class="font-medium">$3.20/ml</span>
                        </div>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <button class="flex-1 bg-red-500 text-white py-1 rounded text-sm hover:bg-red-600">
                            <i class="fas fa-shopping-cart mr-1"></i>Reorder
                        </button>
                        <button class="flex-1 bg-gray-500 text-white py-1 rounded text-sm hover:bg-gray-600">
                            <i class="fas fa-history mr-1"></i>History
                        </button>
                    </div>
                </div>

                <!-- Cholesterol Reagent -->
                <div class="border rounded-lg p-4 bg-green-50">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-medium text-gray-900">Cholesterol Reagent</h4>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">In Stock</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Current Stock:</span>
                            <span class="font-medium">320 ml</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Minimum Required:</span>
                            <span class="font-medium">80 ml</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Lot Number:</span>
                            <span class="font-medium">CH2025-094</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">May 2026</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Cost per Unit:</span>
                            <span class="font-medium">$4.10/ml</span>
                        </div>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <button class="flex-1 bg-blue-500 text-white py-1 rounded text-sm hover:bg-blue-600">
                            <i class="fas fa-edit mr-1"></i>Update
                        </button>
                        <button class="flex-1 bg-gray-500 text-white py-1 rounded text-sm hover:bg-gray-600">
                            <i class="fas fa-history mr-1"></i>History
                        </button>
                    </div>
                </div>

                <!-- Protein Reagent -->
                <div class="border rounded-lg p-4 bg-red-50">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-medium text-gray-900">Protein Reagent</h4>
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">Out of Stock</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Current Stock:</span>
                            <span class="font-medium text-red-600">0 ml</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Minimum Required:</span>
                            <span class="font-medium">50 ml</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Last Lot:</span>
                            <span class="font-medium">PR2025-052</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Last Used:</span>
                            <span class="font-medium">Sep 10, 2025</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Cost per Unit:</span>
                            <span class="font-medium">$5.80/ml</span>
                        </div>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <button class="flex-1 bg-red-500 text-white py-1 rounded text-sm hover:bg-red-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Urgent Order
                        </button>
                        <button class="flex-1 bg-gray-500 text-white py-1 rounded text-sm hover:bg-gray-600">
                            <i class="fas fa-history mr-1"></i>History
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consumables Tab -->
        <div id="consumables" class="category-content p-6 hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Syringes -->
                <div class="border rounded-lg p-4 bg-green-50">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-medium text-gray-900">Disposable Syringes (5ml)</h4>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">In Stock</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Current Stock:</span>
                            <span class="font-medium">2,500 pcs</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Minimum Required:</span>
                            <span class="font-medium">500 pcs</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Package Size:</span>
                            <span class="font-medium">100 pcs/box</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">Dec 2027</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Cost per Unit:</span>
                            <span class="font-medium">$0.15/pc</span>
                        </div>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <button class="flex-1 bg-blue-500 text-white py-1 rounded text-sm hover:bg-blue-600">
                            <i class="fas fa-edit mr-1"></i>Update
                        </button>
                        <button class="flex-1 bg-gray-500 text-white py-1 rounded text-sm hover:bg-gray-600">
                            <i class="fas fa-history mr-1"></i>History
                        </button>
                    </div>
                </div>

                <!-- Gloves -->
                <div class="border rounded-lg p-4 bg-yellow-50">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-medium text-gray-900">Latex Gloves (Medium)</h4>
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">Low Stock</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Current Stock:</span>
                            <span class="font-medium text-yellow-600">3 boxes</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Minimum Required:</span>
                            <span class="font-medium">10 boxes</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Package Size:</span>
                            <span class="font-medium">100 pcs/box</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">Jun 2026</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Cost per Box:</span>
                            <span class="font-medium">$12.50/box</span>
                        </div>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <button class="flex-1 bg-red-500 text-white py-1 rounded text-sm hover:bg-red-600">
                            <i class="fas fa-shopping-cart mr-1"></i>Reorder
                        </button>
                        <button class="flex-1 bg-gray-500 text-white py-1 rounded text-sm hover:bg-gray-600">
                            <i class="fas fa-history mr-1"></i>History
                        </button>
                    </div>
                </div>

                <!-- Pipette Tips -->
                <div class="border rounded-lg p-4 bg-green-50">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-medium text-gray-900">Pipette Tips (200μl)</h4>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">In Stock</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Current Stock:</span>
                            <span class="font-medium">15 racks</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Minimum Required:</span>
                            <span class="font-medium">5 racks</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Package Size:</span>
                            <span class="font-medium">96 tips/rack</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Sterilized:</span>
                            <span class="font-medium">Yes</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Cost per Rack:</span>
                            <span class="font-medium">$8.90/rack</span>
                        </div>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <button class="flex-1 bg-blue-500 text-white py-1 rounded text-sm hover:bg-blue-600">
                            <i class="fas fa-edit mr-1"></i>Update
                        </button>
                        <button class="flex-1 bg-gray-500 text-white py-1 rounded text-sm hover:bg-gray-600">
                            <i class="fas fa-history mr-1"></i>History
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Kits Tab -->
        <div id="test-kits" class="category-content p-6 hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Pregnancy Test -->
                <div class="border rounded-lg p-4 bg-green-50">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-medium text-gray-900">Pregnancy Test Kit</h4>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">In Stock</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Current Stock:</span>
                            <span class="font-medium">150 tests</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Minimum Required:</span>
                            <span class="font-medium">50 tests</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Brand:</span>
                            <span class="font-medium">QuickTest Pro</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">Nov 2025</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Cost per Test:</span>
                            <span class="font-medium">$1.20/test</span>
                        </div>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <button class="flex-1 bg-blue-500 text-white py-1 rounded text-sm hover:bg-blue-600">
                            <i class="fas fa-edit mr-1"></i>Update
                        </button>
                        <button class="flex-1 bg-gray-500 text-white py-1 rounded text-sm hover:bg-gray-600">
                            <i class="fas fa-history mr-1"></i>History
                        </button>
                    </div>
                </div>

                <!-- Malaria Test -->
                <div class="border rounded-lg p-4 bg-red-50">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-medium text-gray-900">Malaria Rapid Test</h4>
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">Critical Low</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Current Stock:</span>
                            <span class="font-medium text-red-600">10 tests</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Minimum Required:</span>
                            <span class="font-medium">100 tests</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Brand:</span>
                            <span class="font-medium">CareStart</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">Nov 2025</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Cost per Test:</span>
                            <span class="font-medium">$0.95/test</span>
                        </div>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <button class="flex-1 bg-red-500 text-white py-1 rounded text-sm hover:bg-red-600">
                            <i class="fas fa-exclamation-triangle mr-1"></i>Urgent Order
                        </button>
                        <button class="flex-1 bg-gray-500 text-white py-1 rounded text-sm hover:bg-gray-600">
                            <i class="fas fa-history mr-1"></i>History
                        </button>
                    </div>
                </div>

                <!-- HIV Test -->
                <div class="border rounded-lg p-4 bg-yellow-50">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-medium text-gray-900">HIV Rapid Test</h4>
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">Low Stock</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Current Stock:</span>
                            <span class="font-medium text-yellow-600">25 tests</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Minimum Required:</span>
                            <span class="font-medium">75 tests</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Brand:</span>
                            <span class="font-medium">Determine HIV</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Expires:</span>
                            <span class="font-medium">Dec 2025</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Cost per Test:</span>
                            <span class="font-medium">$2.30/test</span>
                        </div>
                    </div>
                    <div class="mt-3 flex space-x-2">
                        <button class="flex-1 bg-red-500 text-white py-1 rounded text-sm hover:bg-red-600">
                            <i class="fas fa-shopping-cart mr-1"></i>Reorder
                        </button>
                        <button class="flex-1 bg-gray-500 text-white py-1 rounded text-sm hover:bg-gray-600">
                            <i class="fas fa-history mr-1"></i>History
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expiring Soon Tab -->
        <div id="expired" class="category-content p-6 hidden">
            <div class="space-y-4">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Pregnancy Test Kit</h4>
                                <p class="text-sm text-gray-600">Expires: Nov 15, 2025 (2 months)</p>
                                <p class="text-xs text-gray-500">Stock: 150 tests</p>
                            </div>
                        </div>
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">Expiring Soon</span>
                    </div>
                </div>

                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Malaria Rapid Test</h4>
                                <p class="text-sm text-gray-600">Expires: Nov 20, 2025 (2 months)</p>
                                <p class="text-xs text-gray-500">Stock: 10 tests</p>
                            </div>
                        </div>
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">Expiring Soon</span>
                    </div>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-yellow-600 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">HIV Rapid Test</h4>
                                <p class="text-sm text-gray-600">Expires: Dec 10, 2025 (3 months)</p>
                                <p class="text-xs text-gray-500">Stock: 25 tests</p>
                            </div>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">Monitor</span>
                    </div>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-yellow-600 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-gray-900">Latex Gloves (Medium)</h4>
                                <p class="text-sm text-gray-600">Expires: Jun 30, 2026 (9 months)</p>
                                <p class="text-xs text-gray-500">Stock: 3 boxes</p>
                            </div>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">Monitor</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div id="addItemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add New Inventory Item</h3>
                <button onclick="closeAddItemModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Item Name</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option>Reagents</option>
                            <option>Consumables</option>
                            <option>Test Kits</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Stock</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                        <input type="text" placeholder="ml, pcs, boxes" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Stock</label>
                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cost per Unit</label>
                        <input type="number" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lot/Batch Number</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeAddItemModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-save mr-2"></i>Add Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reorder Modal -->
<div id="reorderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Create Reorder Request</h3>
                <button onclick="closeReorderModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                    <h4 class="font-medium text-gray-900 mb-2">Items Requiring Reorder</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Hemoglobin Reagent (25 ml)</span>
                            <input type="number" value="500" class="w-20 px-2 py-1 text-sm border rounded">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Latex Gloves Medium (3 boxes)</span>
                            <input type="number" value="20" class="w-20 px-2 py-1 text-sm border rounded">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm">Malaria Rapid Test (10 tests)</span>
                            <input type="number" value="200" class="w-20 px-2 py-1 text-sm border rounded">
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option>High Priority</option>
                        <option>Medium Priority</option>
                        <option>Low Priority</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Additional notes for the reorder request..."></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeReorderModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                        <i class="fas fa-shopping-cart mr-2"></i>Submit Request
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
    alert('Inventory export feature would be implemented here');
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
