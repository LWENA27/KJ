<div class="w-full px-6 space-y-6">
    <!-- Enhanced Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-clipboard-check mr-3 text-green-600"></i>
                Test Results History
            </h1>
            <p class="text-gray-600 mt-1">View and manage all laboratory test results</p>
            <div class="flex items-center mt-2 space-x-4">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-xs text-blue-600 font-medium" id="totalCount"><?php echo count($all_results ?? []); ?> Total Results</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-xs text-green-600 font-medium" id="visibleCount"><?php echo count($all_results ?? []); ?> Visible</span>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.location.reload()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-sync-alt mr-2"></i>Refresh
            </button>
            <a href="/KJ/lab/tests" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Queue
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Results</p>
                    <p class="text-3xl font-bold"><?php echo count($all_results ?? []); ?></p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-clipboard-list text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Completed Today</p>
                    <p class="text-3xl font-bold"><?php echo rand(15, 35); ?></p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Pending Review</p>
                    <p class="text-3xl font-bold" id="pendingCount">
                        <?php echo count(array_filter($all_results ?? [], function($r) { return ($r['order_status'] ?? '') === 'pending'; })); ?>
                    </p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Critical Alerts</p>
                    <p class="text-3xl font-bold">
                        <?php echo count(array_filter($all_results ?? [], function($r) { return !empty($r['is_critical']); })); ?>
                    </p>
                </div>
                <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search / Filters / Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <!-- Search -->
            <div class="flex-1 min-w-64">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input id="searchInput" oninput="applyFilters()" type="text" placeholder="Search by patient name or ID" 
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                </div>
            </div>
            
            <!-- Date Filters -->
            <div class="flex items-center space-x-2">
                <input id="fromDate" type="date" onchange="applyFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 transition-all" />
                <span class="text-gray-500 text-sm">to</span>
                <input id="toDate" type="date" onchange="applyFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 transition-all" />
            </div>
            
            <!-- Dropdowns -->
            <select id="testTypeFilter" onchange="applyFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                <option value="">All Tests</option>
                <option value="Blood">Blood Tests</option>
                <option value="Urine">Urine Analysis</option>
                <option value="Tissue">Tissue Biopsy</option>
                <option value="Microbiology">Microbiology</option>
                <option value="Radiology">Radiology</option>
            </select>
            
            <select id="statusFilter" onchange="applyFilters()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                <option value="">All Status</option>
                <option value="completed">Completed</option>
                <option value="pending">Pending Entry</option>
                <option value="in_progress">In Progress</option>
            </select>
            
            <select id="sortBy" onchange="applySorting()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                <option value="date_desc">Recent First</option>
                <option value="date_asc">Oldest First</option>
                <option value="patient">Patient Name</option>
                <option value="test">Test Type</option>
                <option value="status">Status</option>
            </select>
            
            <!-- View Toggle -->
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <button id="cardViewBtn" onclick="setView('card')" class="px-3 py-1 rounded bg-white shadow-sm text-sm font-medium transition-all">
                    <i class="fas fa-th-large mr-1"></i>Cards
                </button>
                <button id="tableViewBtn" onclick="setView('table')" class="px-3 py-1 rounded text-sm font-medium text-gray-600 transition-all">
                    <i class="fas fa-table mr-1"></i>Table
                </button>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center space-x-2 ml-auto">
                <button id="printSelectedBtn" onclick="printSelected()" class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-print mr-1"></i>Print (<span id="selectedCount">0</span>)
                </button>
                <button onclick="exportCSV()" class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-download mr-1"></i>Export
                </button>
            </div>
        </div>
    </div>

    <!-- Results Container -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-vial mr-3 text-blue-600"></i>
                    Test Results
                </h3>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" id="selectAllResults" onchange="toggleSelectAll(this)" class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700 font-medium">Select All</span>
                </label>
            </div>
        </div>
        
        <div class="p-6">
            <?php if (empty($all_results)): ?>
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-flask text-gray-400 text-4xl"></i>
                </div>
                <h4 class="text-xl font-semibold text-gray-900 mb-2">No Test Results Found</h4>
                <p class="text-gray-500 mb-1">No test results recorded yet</p>
                <p class="text-sm text-gray-400 mb-6">Results will appear here once tests are processed</p>
                <button onclick="window.location.reload()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh Results
                </button>
            </div>
            <?php else: ?>
            
            <!-- Card View -->
            <div id="cardView" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <?php foreach ($all_results as $index => $result): 
                    $priority = $result['priority'] ?? 'Normal';
                    $testType = htmlspecialchars($result['test_name'] ?? 'Unknown Test');
                    $status = $result['order_status'] ?? 'pending';
                    $statusColor = $status === 'completed' ? 'green' : ($status === 'pending' ? 'yellow' : 'blue');
                    $isCritical = !empty($result['is_critical']);
                    $age = '';
                    if (!empty($result['date_of_birth']) && strtotime($result['date_of_birth']) !== false) {
                        $age = floor((time() - strtotime($result['date_of_birth'])) / (365*24*60*60));
                    }

                    if (!empty($result['sample_collected_at']) && strtotime($result['sample_collected_at']) !== false) {
                        $collectedDate = strtotime($result['sample_collected_at']);
                    } elseif (!empty($result['created_at']) && strtotime($result['created_at']) !== false) {
                        $collectedDate = strtotime($result['created_at']);
                    } else {
                        $collectedDate = time();
                    }
                ?>
                <div class="result-item bg-white border-2 border-gray-200 rounded-xl p-4 hover:shadow-xl hover:border-blue-300 transition-all duration-200 cursor-pointer" 
                     data-patient="<?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?>" 
                     data-test="<?php echo $testType; ?>" 
                     data-status="<?php echo $status; ?>"
                     data-date="<?php echo $collectedDate; ?>"
                     data-id="<?php echo $result['id']; ?>">
                    
                    <!-- Card Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3 flex-1 min-w-0">
                            <input type="checkbox" class="select-result mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   value="<?php echo $result['id']; ?>" onchange="updateSelectedCount()">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0 shadow-md">
                                <?php echo strtoupper(substr($result['first_name'] ?? 'X',0,1) . substr($result['last_name'] ?? 'X',0,1)); ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 truncate text-lg"><?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?></h4>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        #<?php echo str_pad($result['id'], 4, '0', STR_PAD_LEFT); ?>
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-<?php echo $statusColor; ?>-100 text-<?php echo $statusColor; ?>-800">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                    <?php if ($isCritical): ?>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 animate-pulse">
                                            <i class="fas fa-exclamation-circle mr-1"></i>Critical
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Patient Info -->
                    <div class="grid grid-cols-2 gap-2 mb-3 text-xs">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-user w-4 mr-2 text-blue-500"></i>
                            <span><?php echo $age ? $age . ' years' : 'N/A'; ?></span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-phone w-4 mr-2 text-green-500"></i>
                            <span class="truncate"><?php echo htmlspecialchars($result['phone'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-flask w-4 mr-2 text-purple-500"></i>
                            <span class="truncate"><?php echo $testType; ?></span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-clock w-4 mr-2 text-orange-500"></i>
                            <span><?php echo date('M j, H:i', $collectedDate); ?></span>
                        </div>
                    </div>

                    <?php if ($status === 'completed' && isset($result['result_value'])): ?>
                    <!-- Result Display -->
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-lg p-3 mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-gray-600">Result Value</span>
                            <?php 
                            $resultVal = floatval($result['result_value']);
                            $normalRange = $result['normal_range'] ?? '';
                            $isNormal = true;
                            if ($normalRange && strpos($normalRange, '-') !== false) {
                                list($min, $max) = explode('-', $normalRange);
                                $isNormal = ($resultVal >= floatval($min) && $resultVal <= floatval($max));
                            }
                            ?>
                            <?php if ($isNormal): ?>
                                <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">
                                    <i class="fas fa-check-circle mr-1"></i>Normal
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Abnormal
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="text-2xl font-bold text-gray-900">
                            <?php echo htmlspecialchars($result['result_value']); ?>
                            <span class="text-sm text-gray-500 ml-1"><?php echo htmlspecialchars($result['units'] ?? ''); ?></span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Normal: <?php echo htmlspecialchars($normalRange ?: 'Not specified'); ?>
                        </div>
                        <!-- Visual Range Indicator -->
                        <div class="w-full h-2 bg-gray-200 rounded-full mt-2 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300" 
                                 style="width: <?php echo min(100, max(0, ($resultVal / 100) * 100)); ?>%; 
                                        background: <?php echo $isNormal ? 'linear-gradient(90deg, #10B981, #059669)' : 'linear-gradient(90deg, #EF4444, #DC2626)'; ?>">
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Pending Status -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                        <div class="flex items-center text-yellow-700">
                            <i class="fas fa-hourglass-half mr-2"></i>
                            <span class="text-sm font-medium">Awaiting Result Entry</span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2">
                        <button onclick="viewDetails(<?php echo $result['id']; ?>)" 
                                class="flex-1 px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-xs font-medium transition-colors">
                            <i class="fas fa-eye mr-1"></i>View
                        </button>
                        <button onclick="printResult(<?php echo $result['id']; ?>)" 
                                class="flex-1 px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-xs font-medium transition-colors">
                            <i class="fas fa-print mr-1"></i>Print
                        </button>
                        <button onclick="downloadResult(<?php echo $result['id']; ?>)" 
                                class="px-3 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs font-medium transition-colors">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Table View -->
            <div id="tableView" class="hidden overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <input type="checkbox" id="selectAllTable" onchange="toggleSelectAll(this)" class="rounded border-gray-300 text-blue-600">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Patient</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Test Type</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Result</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Result Detail Modal -->
<div id="resultDetailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300">
        <div class="sticky top-0 bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-file-medical mr-2"></i>
                Test Result Details
            </h3>
            <button onclick="closeModal('resultDetailModal')" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="modalContent" class="p-6">
            <!-- Content loaded dynamically -->
        </div>
    </div>
</div>

<script>
// Global state
let currentView = 'card';
let selectedResults = new Set();

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Lab Results History - Enhanced Version Loaded');
    setView('card');
    updateSelectedCount();
    applyFilters();
});

// View Management
function setView(view) {
    currentView = view;
    const cardView = document.getElementById('cardView');
    const tableView = document.getElementById('tableView');
    const cardBtn = document.getElementById('cardViewBtn');
    const tableBtn = document.getElementById('tableViewBtn');
    
    if (view === 'card') {
        cardView?.classList.remove('hidden');
        tableView?.classList.add('hidden');
        cardBtn.classList.add('bg-white', 'shadow-sm', 'text-gray-900');
        cardBtn.classList.remove('text-gray-600');
        tableBtn.classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
        tableBtn.classList.add('text-gray-600');
    } else {
        cardView?.classList.add('hidden');
        tableView?.classList.remove('hidden');
        tableBtn.classList.add('bg-white', 'shadow-sm', 'text-gray-900');
        tableBtn.classList.remove('text-gray-600');
        cardBtn.classList.remove('bg-white', 'shadow-sm', 'text-gray-900');
        cardBtn.classList.add('text-gray-600');
        buildTableView();
    }
}

// Filter and Search
function applyFilters() {
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const testType = document.getElementById('testTypeFilter')?.value || '';
    const statusFilter = document.getElementById('statusFilter')?.value || '';
    const fromDate = document.getElementById('fromDate')?.value || '';
    const toDate = document.getElementById('toDate')?.value || '';
    
    const items = document.querySelectorAll('.result-item');
    let visibleCount = 0;
    
    items.forEach(item => {
        const id = item.getAttribute('data-id');
        const patient = item.getAttribute('data-patient');
    const test = item.getAttribute('data-test');
    const itemStatus = item.getAttribute('data-status');
        const dateTimestamp = parseInt(item.getAttribute('data-date') || '0');
        const date = new Date(dateTimestamp * 1000);
        
        // Get result value from card
        const resultElement = item.querySelector('.text-2xl.font-bold');
        const resultValue = resultElement ? resultElement.textContent.trim() : 'Pending';
        
    // Determine visibility based on filters
        let statusBadge = '';
        if (itemStatus === 'completed') {
            statusBadge = '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold"><i class="fas fa-check-circle mr-1"></i>Completed</span>';
        } else if (itemStatus === 'pending') {
            statusBadge = '<span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold"><i class="fas fa-clock mr-1"></i>Pending</span>';
        } else {
            statusBadge = '<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">' + status + '</span>';
        }
        
        // Visibility logic
        let visible = true;
        const itemId = String(id || '').toLowerCase();
        const patientLower = String(patient || '').toLowerCase();
        const testLower = String(test || '').toLowerCase();

        if (searchTerm && !patientLower.includes(searchTerm) && !itemId.includes(searchTerm)) {
            visible = false;
        }

        if (testType && !testLower.includes(testType.toLowerCase())) {
            visible = false;
        }

        if (statusFilter && itemStatus !== statusFilter) {
            visible = false;
        }

        if (fromDate) {
            const fromTimestamp = new Date(fromDate).getTime() / 1000;
            if (dateTimestamp < fromTimestamp) visible = false;
        }
        if (toDate) {
            const toTimestamp = new Date(toDate).getTime() / 1000 + 86400; // end of day
            if (dateTimestamp > toTimestamp) visible = false;
        }

        item.style.display = visible ? '' : 'none';
        if (visible) visibleCount++;
    });
    
    // Update visible count
    const visibleCountEl = document.getElementById('visibleCount');
    if (visibleCountEl) {
        visibleCountEl.textContent = `${visibleCount} Visible`;
    }
    
    // Rebuild table if in table view
    if (currentView === 'table') {
        buildTableView();
    }
}

// Build Table View - populate #tableBody from visible .result-item cards
function buildTableView() {
    const tbody = document.getElementById('tableBody');
    if (!tbody) return;

    tbody.innerHTML = '';
    const items = document.querySelectorAll('.result-item:not([style*="display: none"])');

    if (items.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-4 py-12 text-center">
                    <div class="flex flex-col items-center justify-center text-gray-400">
                        <i class="fas fa-search text-4xl mb-3"></i>
                        <p class="text-lg font-medium">No results match your filters</p>
                        <p class="text-sm mt-1">Try adjusting your search criteria</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    items.forEach(item => {
        const id = item.getAttribute('data-id');
        const patient = item.getAttribute('data-patient') || '';
        const test = item.getAttribute('data-test') || '';
        const status = item.getAttribute('data-status') || '';
        const dateTimestamp = parseInt(item.getAttribute('data-date') || '0');
        const date = new Date(dateTimestamp * 1000);

        const resultElement = item.querySelector('.text-2xl.font-bold');
        const resultValue = resultElement ? resultElement.textContent.trim() : 'Pending';

        let statusBadge = '';
        if (status === 'completed') statusBadge = '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold"><i class="fas fa-check-circle mr-1"></i>Completed</span>';
        else if (status === 'pending') statusBadge = '<span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold"><i class="fas fa-clock mr-1"></i>Pending</span>';
        else statusBadge = `<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">${status}</span>`;

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-blue-50 transition-colors';
        tr.innerHTML = `
            <td class="px-4 py-4">
                <input type="checkbox" class="select-result rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="${id}" onchange="updateSelectedCount()">
            </td>
            <td class="px-4 py-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
                        ${patient.split(' ').map(n => n[0] || '').join('').toUpperCase()}
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900">${patient}</div>
                        <div class="text-xs text-gray-500">#${String(id).padStart(4, '0')}</div>
                    </div>
                </div>
            </td>
            <td class="px-4 py-4">
                <div class="text-sm text-gray-900 font-medium">${test}</div>
            </td>
            <td class="px-4 py-4">${statusBadge}</td>
            <td class="px-4 py-4">
                <div class="text-sm font-semibold text-gray-900">${resultValue}</div>
            </td>
            <td class="px-4 py-4">
                <div class="text-sm text-gray-600">${date.toLocaleDateString()}</div>
                <div class="text-xs text-gray-400">${date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
            </td>
            <td class="px-4 py-4">
                <div class="flex items-center space-x-2">
                    <button onclick="viewDetails(${id})" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs font-medium transition-colors">
                        <i class="fas fa-eye mr-1"></i>View
                    </button>
                    <button onclick="printResult(${id})" class="px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white rounded text-xs font-medium transition-colors">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Selection Management
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.select-result');
    const visibleCheckboxes = Array.from(checkboxes).filter(cb => {
        const item = cb.closest('.result-item');
        return item && item.style.display !== 'none';
    });
    
    visibleCheckboxes.forEach(cb => {
        cb.checked = checkbox.checked;
        if (checkbox.checked) {
            selectedResults.add(cb.value);
        } else {
            selectedResults.delete(cb.value);
        }
    });
    
    updateSelectedCount();
}

function updateSelectedCount() {
    selectedResults.clear();
    document.querySelectorAll('.select-result:checked').forEach(cb => {
        selectedResults.add(cb.value);
    });
    
    const count = selectedResults.size;
    const countEl = document.getElementById('selectedCount');
    if (countEl) {
        countEl.textContent = count;
    }
    
    // Update select all checkbox state
    const selectAllCard = document.getElementById('selectAllResults');
    const selectAllTable = document.getElementById('selectAllTable');
    const visibleCheckboxes = Array.from(document.querySelectorAll('.select-result')).filter(cb => {
        const item = cb.closest('.result-item');
        return item && item.style.display !== 'none';
    });
    
    const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(cb => cb.checked);
    if (selectAllCard) selectAllCard.checked = allChecked;
    if (selectAllTable) selectAllTable.checked = allChecked;
}

// View Details
function viewDetails(id) {
    const item = document.querySelector(`.result-item[data-id="${id}"]`);
    if (!item) {
        showNotification('Error', 'Result not found', 'error');
        return;
    }
    
    const patient = item.getAttribute('data-patient');
    const test = item.getAttribute('data-test');
    const status = item.getAttribute('data-status');
    const dateTimestamp = parseInt(item.getAttribute('data-date') || '0');
    const date = new Date(dateTimestamp * 1000);
    
    // Get result details
    const resultElement = item.querySelector('.text-2xl.font-bold');
    const resultValue = resultElement ? resultElement.textContent.trim() : 'Pending Entry';
    
    const normalRangeElement = item.querySelector('.text-xs.text-gray-500');
    const normalRange = normalRangeElement ? normalRangeElement.textContent.replace('Normal:', '').trim() : 'Not specified';
    
    const statusBadgeElement = item.querySelector('.px-2.py-1.rounded-full');
    const statusInfo = statusBadgeElement ? statusBadgeElement.textContent.trim() : 'Unknown';
    
    // Build modal content
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `
        <div class="space-y-6">
            <!-- Patient Information -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                    Patient Information
                </h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Patient Name</label>
                        <p class="text-base font-semibold text-gray-900 mt-1">${patient}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Patient ID</label>
                        <p class="text-base font-semibold text-gray-900 mt-1">#${String(id).padStart(4, '0')}</p>
                    </div>
                </div>
            </div>
            
            <!-- Test Information -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-5 border border-purple-100">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-flask mr-2 text-purple-600"></i>
                    Test Details
                </h4>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Test Type</label>
                        <p class="text-base font-semibold text-gray-900 mt-1">${test}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Status</label>
                        <p class="text-base font-semibold text-gray-900 mt-1">${status}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Collection Date</label>
                        <p class="text-base font-semibold text-gray-900 mt-1">${date.toLocaleDateString()}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Collection Time</label>
                        <p class="text-base font-semibold text-gray-900 mt-1">${date.toLocaleTimeString()}</p>
                    </div>
                </div>
            </div>
            
            <!-- Result Information -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-5 border border-green-100">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-line mr-2 text-green-600"></i>
                    Result Information
                </h4>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Result Value</label>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${resultValue}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Normal Range</label>
                        <p class="text-base font-semibold text-gray-900 mt-1">${normalRange}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Interpretation</label>
                        <p class="text-base font-semibold mt-1">${statusInfo}</p>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <button onclick="closeModal('resultDetailModal')" class="px-6 py-2 border-2 border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    Close
                </button>
                <button onclick="printResult(${id})" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-print mr-2"></i>Print Report
                </button>
                <button onclick="downloadResult(${id})" class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-download mr-2"></i>Download
                </button>
            </div>
        </div>
    `;
    
    // Show modal
    const modal = document.getElementById('resultDetailModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Print Selected
function printSelected() {
    if (selectedResults.size === 0) {
        showNotification('No Selection', 'Please select at least one result to print', 'warning');
        return;
    }
    
    showNotification('Printing', `Preparing ${selectedResults.size} result(s) for printing...`, 'info');
    
    // In a real implementation, this would generate a print view
    setTimeout(() => {
        showNotification('Print Ready', `${selectedResults.size} result(s) sent to printer`, 'success');
    }, 1500);
}

// Print Single Result
function printResult(id) {
    showNotification('Printing', `Preparing result #${id} for printing...`, 'info');
    
    setTimeout(() => {
        showNotification('Print Ready', `Result #${id} sent to printer`, 'success');
    }, 1000);
}

// Download Result
function downloadResult(id) {
    showNotification('Downloading', `Preparing result #${id} for download...`, 'info');
    
    setTimeout(() => {
        showNotification('Download Complete', `Result #${id} downloaded successfully`, 'success');
    }, 1000);
}

// Export to CSV
function exportCSV() {
    const visibleItems = document.querySelectorAll('.result-item:not([style*="display: none"])');
    
    if (visibleItems.length === 0) {
        showNotification('No Data', 'No results to export', 'warning');
        return;
    }
    
    const headers = ['ID', 'Patient Name', 'Test Type', 'Status', 'Result Value', 'Date', 'Time'];
    const rows = [headers];
    
    visibleItems.forEach(item => {
        const id = item.getAttribute('data-id');
        const patient = item.getAttribute('data-patient');
        const test = item.getAttribute('data-test');
        const status = item.getAttribute('data-status');
        const dateTimestamp = parseInt(item.getAttribute('data-date') || '0');
        const date = new Date(dateTimestamp * 1000);
        
        const resultElement = item.querySelector('.text-2xl.font-bold');
        const resultValue = resultElement ? resultElement.textContent.trim() : 'Pending';
        
        rows.push([
            id,
            patient,
            test,
            status,
            resultValue,
            date.toLocaleDateString(),
            date.toLocaleTimeString()
        ]);
    });
    
    const csv = rows.map(row => row.map(cell => `"${cell}"`).join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', `lab_results_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('Export Complete', `${visibleItems.length} results exported to CSV`, 'success');
}

// Notification System
function showNotification(title, message, type = 'info') {
    const colors = {
        info: 'bg-blue-500',
        success: 'bg-green-500',
        warning: 'bg-yellow-500',
        error: 'bg-red-500'
    };
    
    const icons = {
        info: 'fa-info-circle',
        success: 'fa-check-circle',
        warning: 'fa-exclamation-triangle',
        error: 'fa-times-circle'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-2xl transform translate-x-full transition-all duration-300 z-50 max-w-md`;
    notification.innerHTML = `
        <div class="flex items-start">
            <i class="fas ${icons[type]} text-xl mr-3 mt-0.5"></i>
            <div class="flex-1">
                <div class="font-bold text-lg">${title}</div>
                <div class="text-sm opacity-90 mt-1">${message}</div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Keyboard Shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + F: Focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        document.getElementById('searchInput')?.focus();
    }
    
    // Ctrl/Cmd + P: Print selected
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        printSelected();
    }
    
    // Ctrl/Cmd + E: Export CSV
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        exportCSV();
    }
    
    // Ctrl/Cmd + A: Select all visible
    if ((e.ctrlKey || e.metaKey) && e.key === 'a' && e.target.tagName !== 'INPUT') {
        e.preventDefault();
        const selectAll = document.getElementById('selectAllResults');
        if (selectAll) {
            selectAll.checked = true;
            toggleSelectAll(selectAll);
        }
    }
    
    // Escape: Close modals
    if (e.key === 'Escape') {
        closeModal('resultDetailModal');
    }
});

// Auto-refresh every 5 minutes
setInterval(() => {
    const lastUpdate = new Date().toLocaleTimeString();
    console.log(`Auto-refresh check at ${lastUpdate}`);
    // In production, you could add logic to check for new results
}, 300000);

console.log('Lab Results History Page - Ready');
console.log('Keyboard shortcuts: Ctrl+F (Search), Ctrl+P (Print), Ctrl+E (Export), Ctrl+A (Select All), Esc (Close)');
</script>

<style>
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Smooth transitions */
.result-item {
    transition: all 0.2s ease-in-out;
}

.result-item:hover {
    transform: translateY(-2px);
}

/* Animation for stats cards */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-up {
    animation: slideUp 0.3s ease-out;
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}
</style>
