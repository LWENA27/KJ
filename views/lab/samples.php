<div class="space-y-6">
    <!-- Enhanced Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-test-tube mr-3 text-purple-600"></i>
                Sample Collection Management
            </h1>
            <p class="text-gray-600 mt-1">Track and manage patient sample collection workflow</p>
            <div class="flex items-center mt-2 space-x-4">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-orange-500 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-xs text-orange-600 font-medium">8 Pending Collections</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-xs text-green-600 font-medium">24 Collected Today</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                    <span class="text-xs text-red-600 font-medium">2 Quality Issues</span>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="scanBarcode()" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-qrcode mr-2"></i>Scan Barcode
            </button>
            <button onclick="openCollectionModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-vial mr-2"></i>Collect Sample
            </button>
            <button onclick="printLabels()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-print mr-2"></i>Print Labels
            </button>
        </div>
    </div>

    <!-- Enhanced Collection Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <!-- Pending Collections -->
        <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer" onclick="filterSamples('pending')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Pending Collection</p>
                    <p class="text-3xl font-bold">8</p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-clock mr-2 text-orange-200"></i>
                        <span class="text-sm text-orange-200">Avg wait: 8 min</span>
                    </div>
                </div>
                <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-orange-100">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Requires immediate attention
            </div>
        </div>

        <!-- Collected Today -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer" onclick="filterSamples('collected')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Collected Today</p>
                    <p class="text-3xl font-bold">24</p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-chart-line mr-2 text-green-200"></i>
                        <span class="text-sm text-green-200">+18% from yesterday</span>
                    </div>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-green-100">
                <i class="fas fa-arrow-up mr-1"></i>
                Above daily target
            </div>
        </div>

        <!-- Processing -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer" onclick="filterSamples('processing')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">In Processing</p>
                    <p class="text-3xl font-bold">15</p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-stopwatch mr-2 text-blue-200"></i>
                        <span class="text-sm text-blue-200">Est. 45 min remaining</span>
                    </div>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-flask text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-blue-100">
                Currently being analyzed
            </div>
        </div>

        <!-- Sample Quality Issues -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-300 cursor-pointer" onclick="filterSamples('quality-issues')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Quality Issues</p>
                    <p class="text-3xl font-bold">2</p>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-redo mr-2 text-red-200"></i>
                        <span class="text-sm text-red-200">Recollection needed</span>
                    </div>
                </div>
                <div class="bg-red-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-red-100">
                Requires immediate action
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Sample Collection Queue -->
        <div class="lg:col-span-2">
            <!-- Pending Sample Collections -->
            <div class="bg-white rounded-lg shadow-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-red-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-list mr-3 text-orange-600"></i>
                            Pending Sample Collections
                        </h3>
                        <div class="flex items-center space-x-2">
                            <select class="px-3 py-1 border border-gray-300 rounded-md text-sm">
                                <option>All Priorities</option>
                                <option>Urgent</option>
                                <option>Normal</option>
                                <option>Routine</option>
                            </select>
                            <button onclick="refreshQueue()" class="text-orange-600 hover:text-orange-800">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Urgent Priority Sample -->
                        <div class="border border-red-200 rounded-lg p-4 bg-red-50 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                        <span class="font-bold text-red-600 text-sm">1</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">John Doe</h4>
                                        <p class="text-sm text-gray-600">ID: P-001 | Age: 45</p>
                                    </div>
                                </div>
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>URGENT
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-3">
                                <div>
                                    <p class="text-gray-500">Test Required</p>
                                    <p class="font-medium text-gray-900">Blood Sugar</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Sample Type</p>
                                    <p class="font-medium text-gray-900">Blood</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Ordered Time</p>
                                    <p class="font-medium text-gray-900">09:30 AM</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Doctor</p>
                                    <p class="font-medium text-gray-900">Dr. Smith</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                <div class="flex items-center space-x-2 text-sm text-red-600">
                                    <i class="fas fa-clock"></i>
                                    <span>Ordered 2 hours ago - Fasting required</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button onclick="collectSample('P-001', 'Blood Sugar')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-vial mr-1"></i>Collect Now
                                    </button>
                                    <button onclick="viewPatientDetails('P-001')" class="text-blue-600 hover:text-blue-800 text-sm">
                                        Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Normal Priority Sample -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                        <span class="font-bold text-blue-600 text-sm">2</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">Maria Garcia</h4>
                                        <p class="text-sm text-gray-600">ID: P-002 | Age: 32</p>
                                    </div>
                                </div>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fas fa-circle mr-1"></i>NORMAL
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-3">
                                <div>
                                    <p class="text-gray-500">Test Required</p>
                                    <p class="font-medium text-gray-900">Hemoglobin</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Sample Type</p>
                                    <p class="font-medium text-gray-900">Blood</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Ordered Time</p>
                                    <p class="font-medium text-gray-900">10:15 AM</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Doctor</p>
                                    <p class="font-medium text-gray-900">Dr. Johnson</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Routine check-up - No special preparation</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button onclick="collectSample('P-002', 'Hemoglobin')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-vial mr-1"></i>Collect
                                    </button>
                                    <button onclick="viewPatientDetails('P-002')" class="text-blue-600 hover:text-blue-800 text-sm">
                                        Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Multiple Tests Sample -->
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                        <span class="font-bold text-green-600 text-sm">3</span>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">Robert Wilson</h4>
                                        <p class="text-sm text-gray-600">ID: P-003 | Age: 58</p>
                                    </div>
                                </div>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fas fa-check mr-1"></i>ROUTINE
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-3">
                                <div>
                                    <p class="text-gray-500">Tests Required</p>
                                    <p class="font-medium text-gray-900">Lipid Panel, CBC</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Sample Type</p>
                                    <p class="font-medium text-gray-900">Blood (2 tubes)</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Ordered Time</p>
                                    <p class="font-medium text-gray-900">11:00 AM</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Doctor</p>
                                    <p class="font-medium text-gray-900">Dr. Brown</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <i class="fas fa-clipboard-list"></i>
                                    <span>Multiple tests - 12 hour fasting required</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button onclick="collectSample('P-003', 'Multiple')" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-vial mr-1"></i>Collect
                                    </button>
                                    <button onclick="viewPatientDetails('P-003')" class="text-blue-600 hover:text-blue-800 text-sm">
                                        Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Collections -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-check-circle mr-3 text-green-600"></i>
                        Recent Collections
                    </h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Test</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sample</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Collected</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Sarah Johnson</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Blood Sugar</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Blood - Tube #BS001</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">13:45 PM</td>
                                    <td class="px-4 py-3">
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">
                                            <i class="fas fa-check mr-1"></i>Collected
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Michael Brown</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Urine Analysis</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Urine - Container #UA002</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">13:30 PM</td>
                                    <td class="px-4 py-3">
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">
                                            <i class="fas fa-flask mr-1"></i>Processing
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Emma Davis</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">CBC</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">Blood - Tube #CBC003</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">13:15 PM</td>
                                    <td class="px-4 py-3">
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Quality Issue
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Tools & Information -->
        <div class="space-y-6">
            <!-- Sample Collection Tools -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-tools mr-3 text-blue-600"></i>
                        Collection Tools
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-3">
                        <button onclick="openCollectionModal()" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group w-full text-left">
                            <div class="bg-blue-500 group-hover:bg-blue-600 text-white rounded-lg p-2 mr-3">
                                <i class="fas fa-vial"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Collect Sample</div>
                                <div class="text-sm text-gray-600">Start collection process</div>
                            </div>
                        </button>
                        
                        <button onclick="printLabels()" class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group w-full text-left">
                            <div class="bg-purple-500 group-hover:bg-purple-600 text-white rounded-lg p-2 mr-3">
                                <i class="fas fa-print"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Print Labels</div>
                                <div class="text-sm text-gray-600">Generate sample labels</div>
                            </div>
                        </button>
                        
                        <button onclick="openQualityModal()" class="flex items-center p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors group w-full text-left">
                            <div class="bg-yellow-500 group-hover:bg-yellow-600 text-white rounded-lg p-2 mr-3">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Report Quality Issue</div>
                                <div class="text-sm text-gray-600">Flag sample problems</div>
                            </div>
                        </button>
                        
                        <button onclick="trackSample()" class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group w-full text-left">
                            <div class="bg-green-500 group-hover:bg-green-600 text-white rounded-lg p-2 mr-3">
                                <i class="fas fa-search"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Track Sample</div>
                                <div class="text-sm text-gray-600">Follow sample progress</div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Collection Guidelines -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-book mr-3 text-indigo-600"></i>
                        Collection Guidelines
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <h4 class="font-medium text-blue-900 mb-2">Blood Samples</h4>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li>• Verify patient fasting status</li>
                                <li>• Use appropriate tube colors</li>
                                <li>• Label immediately after collection</li>
                                <li>• Invert tubes 8-10 times</li>
                            </ul>
                        </div>
                        
                        <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                            <h4 class="font-medium text-green-900 mb-2">Urine Samples</h4>
                            <ul class="text-sm text-green-800 space-y-1">
                                <li>• Use midstream collection</li>
                                <li>• Sterile container required</li>
                                <li>• Process within 2 hours</li>
                                <li>• Refrigerate if delayed</li>
                            </ul>
                        </div>
                        
                        <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                            <h4 class="font-medium text-purple-900 mb-2">Special Tests</h4>
                            <ul class="text-sm text-purple-800 space-y-1">
                                <li>• Check specific requirements</li>
                                <li>• Note collection time</li>
                                <li>• Special handling instructions</li>
                                <li>• Temperature requirements</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Collection Alerts -->
            <div class="bg-white rounded-lg shadow-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-bell mr-3 text-red-600"></i>
                        Collection Alerts
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex items-start p-3 bg-red-50 rounded-lg">
                            <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center text-white mr-3 mt-0.5">
                                <i class="fas fa-clock text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Urgent Collection Overdue</p>
                                <p class="text-xs text-gray-600">John Doe - Blood Sugar</p>
                                <p class="text-xs text-red-600 mt-1">2 hours past ordered time</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-3 bg-yellow-50 rounded-lg">
                            <div class="w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center text-white mr-3 mt-0.5">
                                <i class="fas fa-exclamation-triangle text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Quality Issue</p>
                                <p class="text-xs text-gray-600">Emma Davis - CBC sample</p>
                                <p class="text-xs text-yellow-600 mt-1">Hemolyzed sample - recollect</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-3 bg-blue-50 rounded-lg">
                            <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white mr-3 mt-0.5">
                                <i class="fas fa-info text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Fasting Reminder</p>
                                <p class="text-xs text-gray-600">5 patients scheduled tomorrow</p>
                                <p class="text-xs text-blue-600 mt-1">Notify for fasting requirements</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sample Collection Modal -->
<div id="collectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-vial mr-3 text-blue-600"></i>
                    Sample Collection
                </h3>
                <button onclick="closeCollectionModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="collectionForm" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Patient ID</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="P-001">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Patient Name</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md" readonly placeholder="Auto-filled">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Ordered</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option>Blood Sugar</option>
                        <option>Hemoglobin</option>
                        <option>CBC</option>
                        <option>Lipid Panel</option>
                        <option>Urine Analysis</option>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sample Type</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option>Blood</option>
                            <option>Urine</option>
                            <option>Stool</option>
                            <option>Saliva</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Container Type</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option>Purple Top Tube</option>
                            <option>Red Top Tube</option>
                            <option>Blue Top Tube</option>
                            <option>Sterile Container</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Collection Time</label>
                        <input type="datetime-local" class="w-full px-3 py-2 border border-gray-300 rounded-md" value="<?php echo date('Y-m-d\TH:i'); ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sample ID</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Auto-generated">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Collection Notes</label>
                    <textarea rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Any special observations, patient condition, or collection notes..."></textarea>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="fastingStatus" class="mr-2">
                    <label for="fastingStatus" class="text-sm text-gray-700">Patient confirmed fasting status (if required)</label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeCollectionModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-check mr-2"></i>Confirm Collection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Collection Modal Functions
function openCollectionModal() {
    document.getElementById('collectionModal').classList.remove('hidden');
}

function closeCollectionModal() {
    document.getElementById('collectionModal').classList.add('hidden');
}

// Sample Collection Functions
function collectSample(patientId, testType) {
    alert('Collecting sample for patient: ' + patientId + ', Test: ' + testType);
    openCollectionModal();
}

function viewPatientDetails(patientId) {
    alert('View patient details for: ' + patientId);
}

function printLabels() {
    alert('Print labels functionality would be implemented here');
}

function refreshQueue() {
    alert('Refreshing collection queue...');
}

function openQualityModal() {
    alert('Report quality issue functionality would be implemented here');
}

function trackSample() {
    alert('Sample tracking functionality would be implemented here');
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    const modals = ['collectionModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});

// Form submission
document.getElementById('collectionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Sample collection recorded successfully!');
    closeCollectionModal();
});
</script>
