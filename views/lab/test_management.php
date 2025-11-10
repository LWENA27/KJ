<?php
$this->layout('layouts/main');
?>

<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-flask mr-3 text-blue-600"></i>
                        Test Management
                    </h1>
                    <p class="text-gray-600 mt-1">Manage laboratory tests, add/edit/delete tests, and link required items</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="openAddTestModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 flex items-center">
                        <i class="fas fa-plus mr-2"></i>Add Test
                    </button>
                    <button onclick="exportTests()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 flex items-center">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-flask text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Tests</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($tests); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Tests</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count(array_filter($tests, fn($t) => $t['is_active'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-tags text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Categories</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($categories); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-boxes text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Inventory Items</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo count($inventory_items); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tests Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Laboratory Tests</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Required Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($tests as $test): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($test['test_name']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($test['test_code']); ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?php echo htmlspecialchars($test['category_name']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                TSh <?php echo number_format($test['price'], 0); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                <div class="truncate" title="<?php echo htmlspecialchars($test['required_items'] ?: 'None specified'); ?>">
                                    <?php echo htmlspecialchars($test['required_items'] ?: 'None specified'); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $test['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $test['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="editTest(<?php echo $test['id']; ?>)" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="manageItems(<?php echo $test['id']; ?>)" class="text-purple-600 hover:text-purple-900">
                                        <i class="fas fa-link"></i>
                                    </button>
                                    <button onclick="toggleTestStatus(<?php echo $test['id']; ?>, <?php echo $test['is_active']; ?>)" class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-<?php echo $test['is_active'] ? 'pause' : 'play'; ?>"></i>
                                    </button>
                                    <button onclick="deleteTest(<?php echo $test['id']; ?>)" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Test Modal -->
<div id="testModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modalTitle" class="text-lg font-medium text-gray-900">Add New Test</h3>
                <button onclick="closeTestModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="testForm" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="test_id" id="test_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Test Name *</label>
                        <input type="text" name="test_name" id="test_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Test Code *</label>
                        <input type="text" name="test_code" id="test_code" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select name="category_id" id="category_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price (TSh) *</label>
                        <input type="number" name="price" id="price" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Normal Range</label>
                    <input type="text" name="normal_range" id="normal_range" placeholder="e.g., 70-100 mg/dL" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                    <input type="text" name="unit" id="unit" placeholder="e.g., mg/dL, mmol/L" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Test description and methodology..."></textarea>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" checked class="rounded">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeTestModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-save mr-2"></i>Save Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Required Items Modal -->
<div id="itemsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Manage Required Items</h3>
                <button onclick="closeItemsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mb-4">
                <h4 id="testName" class="text-md font-medium text-gray-900"></h4>
                <p class="text-sm text-gray-600">Select items required for this test</p>
            </div>

            <form id="itemsForm" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="test_id" id="items_test_id">

                <div class="max-h-60 overflow-y-auto border rounded-md p-4">
                    <?php foreach ($inventory_items as $item): ?>
                    <div class="flex items-center mb-2">
                        <input type="checkbox" name="required_items[]" value="<?php echo $item['id']; ?>" id="item_<?php echo $item['id']; ?>" class="rounded">
                        <label for="item_<?php echo $item['id']; ?>" class="ml-2 text-sm text-gray-700">
                            <?php echo htmlspecialchars($item['item_name']); ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeItemsModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-save mr-2"></i>Save Items
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Modal functions
function openAddTestModal() {
    document.getElementById('modalTitle').textContent = 'Add New Test';
    document.getElementById('testForm').reset();
    document.getElementById('test_id').value = '';
    document.getElementById('testModal').classList.remove('hidden');
}

function closeTestModal() {
    document.getElementById('testModal').classList.add('hidden');
}

function closeItemsModal() {
    document.getElementById('itemsModal').classList.add('hidden');
}

function editTest(testId) {
    // Fetch test data and populate modal
    fetch(`/KJ/lab/get_test/${testId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalTitle').textContent = 'Edit Test';
            document.getElementById('test_id').value = data.id;
            document.getElementById('test_name').value = data.test_name;
            document.getElementById('test_code').value = data.test_code;
            document.getElementById('category_id').value = data.category_id;
            document.getElementById('price').value = data.price;
            document.getElementById('normal_range').value = data.normal_range || '';
            document.getElementById('unit').value = data.unit || '';
            document.getElementById('description').value = data.description || '';
            document.getElementById('is_active').checked = data.is_active == 1;
            document.getElementById('testModal').classList.remove('hidden');
        })
        .catch(error => {
            alert('Error loading test data');
            console.error(error);
        });
}

function manageItems(testId) {
    // Fetch test name and current required items
    fetch(`/KJ/lab/get_test/${testId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('testName').textContent = data.test_name;
            document.getElementById('items_test_id').value = testId;

            // Reset all checkboxes
            document.querySelectorAll('#itemsForm input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
            });

            // Check current required items
            if (data.required_items && data.required_items.length > 0) {
                data.required_items.forEach(itemId => {
                    const checkbox = document.getElementById(`item_${itemId}`);
                    if (checkbox) checkbox.checked = true;
                });
            }

            document.getElementById('itemsModal').classList.remove('hidden');
        })
        .catch(error => {
            alert('Error loading test items');
            console.error(error);
        });
}

function toggleTestStatus(testId, currentStatus) {
    if (!confirm(`Are you sure you want to ${currentStatus ? 'deactivate' : 'activate'} this test?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('csrf_token', '<?php echo $csrf_token; ?>');
    formData.append('test_id', testId);
    formData.append('is_active', currentStatus ? 0 : 1);

    fetch('/KJ/lab/toggle_test_status', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating test status');
        }
    })
    .catch(error => {
        alert('Error updating test status');
        console.error(error);
    });
}

function deleteTest(testId) {
    if (!confirm('Are you sure you want to delete this test? This action cannot be undone.')) {
        return;
    }

    const formData = new FormData();
    formData.append('csrf_token', '<?php echo $csrf_token; ?>');
    formData.append('test_id', testId);

    fetch('/KJ/lab/delete_test', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error deleting test');
        }
    })
    .catch(error => {
        alert('Error deleting test');
        console.error(error);
    });
}

function exportTests() {
    window.open('/KJ/lab/export_tests', '_blank');
}

// Form submissions
document.getElementById('testForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/KJ/lab/save_test', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error saving test: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error saving test');
        console.error(error);
    });
});

document.getElementById('itemsForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/KJ/lab/save_test_items', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error saving test items');
        }
    })
    .catch(error => {
        alert('Error saving test items');
        console.error(error);
    });
});

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    const modals = ['testModal', 'itemsModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>