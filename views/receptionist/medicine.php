<?php 
$pageTitle = 'Medicine Management';
$userRole = 'receptionist';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'receptionist') {
    header('Location: ' . ($BASE_PATH ?? '') . '/auth/login');
    exit;
}

// Current user info is not queried here; controller should provide any needed data.
// Avoid using $pdo in views.

// Get data passed from controller
$pendingPatients = $pending_patients ?? [];
$medicines = $medicines ?? [];
$categories = $categories ?? [];
$recentTransactions = $recent_transactions ?? [];
?>

<div class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-emerald-50">
    <div class="container mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-primary-900">Medicine Management</h1>
                    <p class="text-primary-600 mt-1">Comprehensive medicine inventory and patient dispensing</p>
                </div>
                <button onclick="showAddMedicineModal()" 
                        class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Add Medicine
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="stats-card">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users-injured text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Pending Patients</p>
                        <p class="text-2xl font-bold text-gray-900"><?= count($pendingPatients) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="stats-card">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-pills text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Medicines</p>
                        <p class="text-2xl font-bold text-gray-900"><?= count($medicines) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="stats-card">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Low Stock</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?= count(array_filter($medicines, function($m) { return $m['stock_quantity'] <= 10; })) ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="stats-card">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-list text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Categories</p>
                        <p class="text-2xl font-bold text-gray-900"><?= count($categories) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="card mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button onclick="switchTab('dispensing')" id="tab-dispensing" 
                            class="tab-button active py-2 px-1 border-b-2 font-medium text-sm">
                        <i class="fas fa-hand-holding-medical mr-2"></i>
                        Patient Dispensing
                    </button>
                    <button onclick="switchTab('inventory')" id="tab-inventory" 
                            class="tab-button py-2 px-1 border-b-2 font-medium text-sm">
                        <i class="fas fa-warehouse mr-2"></i>
                        Inventory Management
                    </button>
                    <button onclick="switchTab('transactions')" id="tab-transactions" 
                            class="tab-button py-2 px-1 border-b-2 font-medium text-sm">
                        <i class="fas fa-history mr-2"></i>
                        Transaction History
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Patient Dispensing Tab -->
                <div id="content-dispensing" class="tab-content">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Patients Requiring Medicine</h2>
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Patients who have completed payment for prescribed medicines
                        </div>
                    </div>

                    <?php if (empty($pendingPatients)): ?>
                        <div class="text-center py-12">
                            <div class="text-gray-500">
                                <i class="fas fa-check-circle text-4xl mb-4 text-green-500"></i>
                                <p class="text-lg font-medium">No pending medicine dispensing</p>
                                <p class="text-gray-400">All prescribed medicines have been dispensed</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="table-header">Patient</th>
                                        <th class="table-header">Prescribed Medicines</th>
                                        <th class="table-header">Total Cost (TSh)</th>
                                        <th class="table-header">Payment Status</th>
                                        <th class="table-header">Prescribed Date</th>
                                        <th class="table-header">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($pendingPatients as $patient): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="table-cell">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                                                        <i class="fas fa-user text-primary-600"></i>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            <?= htmlspecialchars(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? '')) ?>
                                                        </div>
                                                        <div class="text-sm text-gray-500">ID: <?= htmlspecialchars($patient['patient_id'] ?? '') ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="table-cell">
                                                <div class="text-sm text-gray-900">
                                                    <?= htmlspecialchars($patient['medicine_count'] ?? 0) ?> medicine(s)
                                                </div>
                                                <button onclick="viewPrescriptionDetails(<?= $patient['id'] ?>)" 
                                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                                    View details →
                                                </button>
                                            </td>
                                            <td class="table-cell">
                                                <span class="text-lg font-semibold text-gray-900">
                                                    <?= format_tsh($patient['total_cost'] ?? ($patient['total_amount'] ?? 0), 0) ?>
                                                </span>
                                            </td>
                                            <td class="table-cell">
                                                <span class="status-badge status-completed">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    Paid
                                                </span>
                                            </td>
                                            <td class="table-cell">
                                                <span class="text-sm text-gray-600">
                                                    <?= date('M j, Y', strtotime($patient['prescribed_at'])) ?>
                                                </span>
                                            </td>
                                            <td class="table-cell">
                                                <form method="POST" action="medicine" class="inline">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                                    <input type="hidden" name="action" value="dispense_patient_medicine">
                                                    <input type="hidden" name="patient_id" value="<?= $patient['id'] ?>">
                                                    <button type="submit" 
                                                            class="btn btn-success btn-sm"
                                                            onclick="return confirm('Confirm dispensing all prescribed medicines to this patient?')">
                                                        <i class="fas fa-hand-holding-medical mr-1"></i>
                                                        Dispense
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Inventory Management Tab -->
                <div id="content-inventory" class="tab-content" style="display: none;">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Medicine Inventory</h2>
                        <div class="flex gap-3">
                            <select onchange="filterByCategory(this.value)" class="form-select">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category ?? '') ?>"><?= htmlspecialchars($category ?? '') ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button onclick="showBulkUpdateModal()" class="btn btn-secondary btn-sm">
                                <i class="fas fa-edit mr-1"></i>Bulk Update
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($medicines as $medicine): ?>
                            <div class="medicine-card" data-category="<?= htmlspecialchars($medicine['category'] ?? '') ?>">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($medicine['name'] ?? '') ?></h3>
                                        <?php if (!empty($medicine['generic_name'])): ?>
                                            <p class="text-sm text-gray-600"><?= htmlspecialchars($medicine['generic_name'] ?? '') ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <span class="category-badge"><?= htmlspecialchars($medicine['category'] ?? '') ?></span>
                                </div>
                                
                                <div class="flex justify-between items-center mb-3">
                                    <div>
                                        <span class="text-2xl font-bold text-gray-900"><?= $medicine['stock_quantity'] ?></span>
                                        <span class="text-sm text-gray-600 ml-1">units</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-primary-600">
                                            <?= format_tsh($medicine['unit_price'] ?? 0, 0) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">per unit</div>
                                    </div>
                                </div>

                                    <?php if (!empty($medicine['expiry_date'])): ?>
                                        <?php 
                                            $expDate = new DateTime($medicine['expiry_date']);
                                            $today = new DateTime('today');
                                            $days = (int)$today->diff($expDate)->format('%r%a');
                                        ?>
                                        <div class="mb-3 flex items-center justify-between">
                                            <div class="text-sm text-gray-600">Expiry</div>
                                            <div class="text-sm font-medium <?= $days < 0 ? 'text-red-600' : ($days <= 30 ? 'text-yellow-600' : 'text-gray-800') ?>">
                                                <?= htmlspecialchars($medicine['expiry_date']) ?>
                                                <span class="ml-2 text-xs <?= $days < 0 ? 'text-red-600' : ($days <= 30 ? 'text-yellow-600' : 'text-gray-500') ?>">
                                                    (<?= $days < 0 ? ('expired ' . abs($days) . 'd ago') : ('in ' . $days . 'd') ?>)
                                                </span>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                <?php if ($medicine['stock_quantity'] <= 10): ?>
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-2 mb-3">
                                        <div class="flex items-center text-red-700">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <span class="text-sm font-medium">Low Stock Alert</span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="flex gap-2">
                                    <button onclick="showStockUpdateModal(<?= $medicine['id'] ?>, '<?= htmlspecialchars($medicine['name'] ?? '') ?>', <?= $medicine['stock_quantity'] ?>)" 
                                            class="btn btn-primary btn-sm flex-1">
                                        <i class="fas fa-plus mr-1"></i>Update Stock
                                    </button>
                                    <button onclick="editMedicine(<?= $medicine['id'] ?>)" 
                                            class="btn btn-secondary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Transaction History Tab -->
                <div id="content-transactions" class="tab-content" style="display: none;">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Medicine Transactions</h2>
                        <div class="flex gap-3">
                            <input type="date" class="form-input" placeholder="From Date">
                            <input type="date" class="form-input" placeholder="To Date">
                            <button class="btn btn-secondary btn-sm">Filter</button>
                        </div>
                    </div>

                    <?php if (empty($recentTransactions)): ?>
                        <div class="text-center py-12">
                            <div class="text-gray-500">
                                <i class="fas fa-history text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No recent transactions</p>
                                <p class="text-gray-400">Medicine dispensing history will appear here</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="table-header">Date & Time</th>
                                        <th class="table-header">Patient</th>
                                        <th class="table-header">Medicine</th>
                                        <th class="table-header">Quantity</th>
                                        <th class="table-header">Unit Price (TSh)</th>
                                        <th class="table-header">Total (TSh)</th>
                                        <th class="table-header">Dispensed By</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($recentTransactions as $transaction): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="table-cell">
                                                <?= date('M j, Y g:i A', strtotime($transaction['dispensed_at'])) ?>
                                            </td>
                                            <td class="table-cell">
                                                <?= htmlspecialchars($transaction['patient_name'] ?? '') ?>
                                            </td>
                                            <td class="table-cell">
                                                <?= htmlspecialchars($transaction['medicine_name'] ?? '') ?>
                                            </td>
                                            <td class="table-cell">
                                                <?= $transaction['quantity'] ?>
                                            </td>
                                            <td class="table-cell">
                                                <?= format_tsh($transaction['unit_price'] ?? 0, 0) ?>
                                            </td>
                                            <td class="table-cell">
                                                <span class="font-semibold">
                                                    <?= format_tsh($transaction['total_cost'] ?? 0, 0) ?>
                                                </span>
                                            </td>
                                            <td class="table-cell">
                                                <?= htmlspecialchars($transaction['dispensed_by'] ?? '') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Medicine Modal -->
<div id="addMedicineModal" class="modal">
    <div class="modal-content max-w-md">
        <div class="modal-header">
            <h3 class="modal-title">Add New Medicine</h3>
            <button onclick="hideModal('addMedicineModal')" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="medicine" class="modal-body">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" value="add_medicine">
            
            <div class="form-group">
                <label class="form-label">Medicine Name *</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Generic Name</label>
                <input type="text" name="generic_name" class="form-input">
            </div>
            
            <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category" class="form-select" required>
                    <option value="">Select Category</option>
                    <option value="Antibiotics">Antibiotics</option>
                    <option value="Pain Relief">Pain Relief</option>
                    <option value="Vitamins">Vitamins</option>
                    <option value="Cardiac">Cardiac</option>
                    <option value="Respiratory">Respiratory</option>
                    <option value="Digestive">Digestive</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-input" min="<?= date('Y-m-d') ?>">
                    <p class="text-xs text-gray-500 mt-1">Leave empty if unknown. You'll get alerts 60/30/7 days before expiry.</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Supplier</label>
                    <input type="text" name="supplier" class="form-input" placeholder="e.g., MediSupplies Ltd">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Unit Price (TSh) *</label>
                    <input type="number" name="unit_price" step="0.01" min="0" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Initial Stock *</label>
                    <input type="number" name="stock_quantity" min="0" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-textarea"></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="hideModal('addMedicineModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Medicine</button>
            </div>
        </form>
    </div>
</div>

<!-- Stock Update Modal -->
<div id="stockUpdateModal" class="modal">
    <div class="modal-content max-w-md">
        <div class="modal-header">
            <h3 class="modal-title">Update Stock</h3>
            <button onclick="hideModal('stockUpdateModal')" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="medicine" class="modal-body">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <input type="hidden" name="action" value="update_medicine_stock">
            <input type="hidden" name="medicine_id" id="stock_medicine_id">
            
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h4 class="font-medium text-gray-900" id="stock_medicine_name"></h4>
                <p class="text-sm text-gray-600">Current Stock: <span id="current_stock" class="font-medium"></span> units</p>
            </div>
            
            <div class="form-group">
                <label class="form-label">Action</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="action" value="add" checked class="mr-2">
                        Add to current stock
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="action" value="set" class="mr-2">
                        Set new stock level
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Quantity</label>
                <input type="number" name="new_quantity" min="0" class="form-input" required>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="hideModal('stockUpdateModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Stock</button>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected tab content and mark button as active
    document.getElementById('content-' + tabName).style.display = 'block';
    document.getElementById('tab-' + tabName).classList.add('active');
}

// Modal management functions
/**
 * Show modal while ensuring header/title remains visible when app has fixed top bars.
 * - Computes heights of any fixed top elements and applies padding-top to modal so it sits below them.
 * - Resets modal content scroll and focuses the first input for accessibility.
 */
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // Find all visible fixed elements anchored at the top and sum their heights
    let topOffset = 0;
    document.querySelectorAll('body *').forEach(el => {
        try {
            const style = window.getComputedStyle(el);
            if (style.position === 'fixed' && parseFloat(style.top || 0) === 0) {
                const rect = el.getBoundingClientRect();
                if (rect.width > 0 && rect.height > 0) topOffset += rect.height;
            }
        } catch (e) {
            // ignore
        }
    });

    // Responsive minimum safe offset so modal doesn't stick to the very top
    // on larger screens leave more space for the fixed header/breadcrumb bar.
    const minOffset = window.innerWidth >= 1024 ? 84 : 28;
    if (topOffset < minOffset) topOffset = minOffset;

    modal.style.display = 'flex';
    modal.classList.add('show');
    modal.style.alignItems = 'flex-start';
    modal.style.paddingTop = topOffset + 'px';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling

    // Reset inner scroll and focus first input for keyboard users
    const content = modal.querySelector('.modal-content');
    if (content) {
        const body = content.querySelector('.modal-body');
        if (body) body.scrollTop = 0;
        // focus first form control
        const firstInput = content.querySelector('input, select, textarea, button');
        if (firstInput) firstInput.focus();
    }
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('show');
    modal.style.display = 'none';
    modal.style.alignItems = '';
    modal.style.paddingTop = '';
    document.body.style.overflow = ''; // Restore scrolling
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        hideModal(event.target.id);
    }
}

function showAddMedicineModal() {
    showModal('addMedicineModal');
}

function showStockUpdateModal(medicineId, medicineName, currentStock) {
    document.getElementById('stock_medicine_id').value = medicineId;
    document.getElementById('stock_medicine_name').textContent = medicineName;
    document.getElementById('current_stock').textContent = currentStock;
    showModal('stockUpdateModal');
}

function filterByCategory(category) {
    const cards = document.querySelectorAll('.medicine-card');
    cards.forEach(card => {
        if (!category || card.dataset.category === category) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function viewPrescriptionDetails(patientId) {
    // Implementation for viewing prescription details modal
    alert('Prescription details modal - To be implemented');
}

function editMedicine(medicineId) {
    // Implementation for editing medicine modal
    alert('Edit medicine modal - To be implemented');
}

function showBulkUpdateModal() {
    // Implementation for bulk stock update modal
    alert('Bulk update modal - To be implemented');
}
</script>
