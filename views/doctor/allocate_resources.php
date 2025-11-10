<!-- Service Allocation Page -->
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Allocate Services</h1>
        <p class="text-gray-600">Delegate services to staff members for patient <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></p>
    </div>

    <!-- Patient Information Card -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 mb-8 text-white">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-blue-100 text-sm">Patient Name</p>
                <p class="text-xl font-bold"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></p>
            </div>
            <div>
                <p class="text-blue-100 text-sm">Registration #</p>
                <p class="text-xl font-bold"><?php echo htmlspecialchars($patient['registration_number']); ?></p>
            </div>
            <div>
                <p class="text-blue-100 text-sm">Phone</p>
                <p class="text-xl font-bold"><?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?></p>
            </div>
            <div>
                <p class="text-blue-100 text-sm">Age</p>
                <p class="text-xl font-bold">
                    <?php 
                    $dob = $patient['date_of_birth'] ?? null;
                    if (!empty($dob)) {
                        echo date_diff(date_create($dob), date_create('today'))->y;
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Check for Active Visit -->
    <?php if (!$active_visit): ?>
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 flex items-start">
        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
        <div>
            <h4 class="font-semibold text-yellow-900">No Active Visit</h4>
            <p class="text-yellow-700 text-sm">This patient does not have an active visit. Please create a visit before allocating services.</p>
        </div>
    </div>
    <?php else: ?>

    <!-- Main Allocation Form -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Form Header -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-tasks text-blue-600 mr-2"></i>Service Allocation Form
            </h2>
            <p class="text-gray-600 text-sm mt-1">Active Visit: <strong><?php echo date('d/m/Y', strtotime($active_visit['visit_date'])); ?></strong></p>
        </div>

        <!-- Form Body -->
        <div class="p-6">
            <form id="allocationForm" method="POST" action="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/save_allocation">
                <!-- Hidden Fields -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patient['id']); ?>">
                <input type="hidden" name="visit_id" value="<?php echo htmlspecialchars($active_visit['id']); ?>">

                <!-- Services Selection Section -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-flask text-blue-600 mr-2"></i>Select Services
                        </h3>
                        <button type="button" onclick="showAddServiceModal()" class="text-sm bg-green-50 text-green-700 hover:bg-green-100 px-3 py-2 rounded-lg transition border border-green-200">
                            <i class="fas fa-plus mr-1"></i>Add More Services
                        </button>
                    </div>

                    <!-- Service Filter & Search Section -->
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Search Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-search mr-2"></i>Search Services
                                </label>
                                <input type="text" 
                                       id="serviceSearch" 
                                       placeholder="Search by name, code, or description..."
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <!-- Category Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-filter mr-2"></i>Filter by Category
                                </label>
                                <select id="serviceCategory" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Categories</option>
                                    <option value="clinical">Clinical Services</option>
                                    <option value="lab">Laboratory Tests</option>
                                    <option value="imaging">Imaging Services</option>
                                    <option value="procedure">Procedures</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3" id="servicesContainer">
                        <?php if (!empty($available_services)): ?>
                            <?php foreach ($available_services as $service): 
                                // Determine service category
                                $category = 'clinical'; // default
                                $service_name_lower = strtolower($service['service_name']);
                                if (strpos($service_name_lower, 'lab') !== false || strpos($service_name_lower, 'test') !== false) {
                                    $category = 'lab';
                                } elseif (strpos($service_name_lower, 'imaging') !== false || strpos($service_name_lower, 'ultrasound') !== false || strpos($service_name_lower, 'x-ray') !== false) {
                                    $category = 'imaging';
                                } elseif (strpos($service_name_lower, 'procedure') !== false || strpos($service_name_lower, 'surgery') !== false) {
                                    $category = 'procedure';
                                }
                                $requires_payment = $service['price'] > 0;
                            ?>
                                <div class="service-item border border-gray-200 rounded-lg p-4 hover:bg-blue-50 transition" 
                                     data-service-id="<?php echo htmlspecialchars($service['id']); ?>"
                                     data-category="<?php echo htmlspecialchars($category); ?>"
                                     data-search-text="<?php echo strtolower(htmlspecialchars($service['service_name'] . ' ' . $service['service_code'] . ' ' . ($service['description'] ?? ''))); ?>">
                                    <div class="flex items-start">
                                        <input type="checkbox" 
                                               class="service-checkbox w-5 h-5 text-blue-600 rounded mt-1 cursor-pointer" 
                                               name="service_ids[]"
                                               value="<?php echo htmlspecialchars($service['id']); ?>"
                                               data-service-name="<?php echo htmlspecialchars($service['service_name']); ?>"
                                               data-requires-payment="<?php echo $requires_payment ? '1' : '0'; ?>"
                                               data-price="<?php echo htmlspecialchars($service['price']); ?>">
                                        <div class="ml-4 flex-1">
                                            <label class="cursor-pointer block">
                                                <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($service['service_name']); ?></p>
                                                <p class="text-sm text-gray-600">Code: <?php echo htmlspecialchars($service['service_code']); ?></p>
                                            </label>
                                            <?php if (!empty($service['description'])): ?>
                                                <p class="text-sm text-gray-500 mt-1"><?php echo htmlspecialchars($service['description']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">TSH <?php echo number_format($service['price'], 2); ?></p>
                                        </div>
                                    </div>

                                    <!-- Staff Assignment for this Service -->
                                    <div class="mt-4 ml-9 staff-assignment hidden" data-service-id="<?php echo htmlspecialchars($service['id']); ?>">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign to Staff Member:</label>
                    <select name="allocations[<?php echo htmlspecialchars($service['id']); ?>][performed_by]" 
                        class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                            <option value="">-- Select Staff --</option>
                                            <?php foreach ($available_staff as $staff): ?>
                                                <option value="<?php echo htmlspecialchars($staff['id']); ?>">
                                                    <?php echo htmlspecialchars($staff['staff_name'] . ' (' . strtoupper($staff['role']) . ')'); ?>
                                                    <?php if (!empty($staff['specialization'])): ?>
                                                        - <?php echo htmlspecialchars($staff['specialization']); ?>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <!-- Notes for this Service -->
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional):</label>
                                            <textarea name="allocations[<?php echo htmlspecialchars($service['id']); ?>][notes]"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                      rows="2"
                                                      placeholder="Enter any specific instructions..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-600">No services available.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Error Message for No Services Selected -->
                    <div id="noServicesError" class="mt-4 text-red-600 text-sm hidden flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Please select at least one service to allocate.
                    </div>
                </div>

                <!-- General Notes Section -->
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-file-alt text-blue-600 mr-2"></i>General Notes
                    </h3>
                    <textarea id="generalNotes"
                              name="notes"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              rows="4"
                              placeholder="Add any general notes or instructions for all services..."></textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-between gap-4">
                    <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_patient/<?php echo htmlspecialchars($patient['id']); ?>"
                       class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold rounded-lg transition">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                    <button type="submit" 
                            id="submitBtn"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition flex items-center">
                        <i class="fas fa-check mr-2"></i>Allocate Services
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pending Allocations Section -->
    <?php if (!empty($pending_orders)): ?>
    <div class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Pending Header -->
        <div class="bg-blue-50 px-6 py-4 border-b border-blue-200">
            <h2 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-clock text-blue-600 mr-2"></i>Pending Allocations
            </h2>
            <p class="text-gray-600 text-sm mt-1"><?php echo count($pending_orders); ?> pending order(s)</p>
        </div>

        <!-- Pending Orders Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Service</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Assigned to</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Allocated</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($pending_orders as $order): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($order['service_name']); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-700"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                                <p class="text-sm text-gray-500"><?php echo strtoupper($order['role']); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    <?php echo $order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($order['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                                'bg-green-100 text-green-800'); ?>">
                                    <i class="fas fa-circle text-current text-xs mr-2"></i>
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button type="button" 
                                        class="cancel-btn px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 rounded text-sm font-medium transition"
                                        data-order-id="<?php echo htmlspecialchars($order['id']); ?>"
                                        data-service-name="<?php echo htmlspecialchars($order['service_name']); ?>">
                                    <i class="fas fa-times mr-1"></i>Cancel
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>

<!-- Success Message Modal -->
<div id="successModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md">
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full mb-4">
            <i class="fas fa-check text-green-600 text-xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Success!</h3>
        <p id="successMessage" class="text-gray-600 text-center mb-6">Services allocated successfully.</p>
        <button type="button" class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
                onclick="location.reload()">
            Refresh Page
        </button>
    </div>
</div>

<!-- Error Message Modal -->
<div id="errorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md">
        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
            <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Error</h3>
        <p id="errorMessage" class="text-gray-600 text-center mb-6">An error occurred. Please try again.</p>
        <button type="button" class="w-full px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition"
                onclick="document.getElementById('errorModal').classList.add('hidden')">
            Close
        </button>
    </div>
</div>

<!-- Confirmation Modal for Cancellation -->
<div id="confirmCancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Cancel Allocation?</h3>
        <p class="text-gray-600 mb-6">Are you sure you want to cancel this allocation for <strong id="cancelServiceName"></strong>?</p>
        <textarea id="cancellationReason" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md mb-4"
                  rows="3"
                  placeholder="Reason for cancellation (optional)"></textarea>
        <div class="flex gap-3">
            <button type="button" 
                    class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold rounded-lg transition"
                    onclick="document.getElementById('confirmCancelModal').classList.add('hidden')">
                No, Keep It
            </button>
            <button type="button" 
                    id="confirmCancelBtn"
                    class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                Yes, Cancel It
            </button>
        </div>
    </div>
</div>

<!-- Add More Services Modal (create new service only) -->
<div id="addServiceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-plus-circle text-green-600 mr-2"></i>Add Service
            </h3>
            <button type="button" onclick="closeAddServiceModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p class="text-sm text-gray-600 mb-6">Create a new service. This will add the service to the system and show it in the list below (no allocation will be performed).</p>

        <div class="space-y-4 mb-6">
            <!-- Service Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Service Name *</label>
                <input id="newServiceName" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="e.g. Advanced ECG">
            </div>

            <!-- Service Code -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Service Code</label>
                <input id="newServiceCode" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="e.g. ECG001">
            </div>

            <!-- Category (select or add) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select id="newServiceCategory" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="clinical">Clinical</option>
                    <option value="lab">Laboratory</option>
                    <option value="imaging">Imaging</option>
                    <option value="procedure">Procedure</option>
                    <option value="other">Other (add below)</option>
                </select>
                <input id="newServiceCategoryCustom" type="text" class="w-full mt-2 px-4 py-2 border border-gray-300 rounded-lg hidden" placeholder="Enter custom category">
            </div>

            <!-- Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price (TSH)</label>
                <input id="newServicePrice" type="number" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="0.00">
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="newServiceDescription" class="w-full px-4 py-2 border border-gray-300 rounded-lg" rows="3" placeholder="Optional description..."></textarea>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">
            <button type="button" onclick="addNewService()" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Create Service
            </button>
            <button type="button" onclick="closeAddServiceModal()" class="flex-1 px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-900 font-semibold rounded-lg transition">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const BASE_PATH = '<?php echo htmlspecialchars($BASE_PATH); ?>';
    const csrfToken = '<?php echo htmlspecialchars($csrf_token); ?>';
    let currentOrderIdToCancel = null;

    // Handle Service Selection - Show/Hide Staff Assignment
    document.querySelectorAll('.service-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const serviceId = this.value;
            // Target the staff-assignment block specifically (child element), not the whole service-item
            const staffAssignment = document.querySelector(`.staff-assignment[data-service-id="${serviceId}"]`);
            if (!staffAssignment) return; // nothing to toggle

            const staffSelect = staffAssignment.querySelector('select');
            if (this.checked) {
                staffAssignment.classList.remove('hidden');
                // focus first input for faster workflow
                if (staffSelect) staffSelect.focus();
            } else {
                staffAssignment.classList.add('hidden');
            }
        });
    });

    // Handle Form Submission
    document.getElementById('allocationForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Check if at least one service is selected
        const selectedServices = document.querySelectorAll('.service-checkbox:checked');
        if (selectedServices.length === 0) {
            document.getElementById('noServicesError').classList.remove('hidden');
            return;
        }

        document.getElementById('noServicesError').classList.add('hidden');

        // Build allocations array from checked services - validate synchronously
        const allocations = [];
        for (let i = 0; i < selectedServices.length; i++) {
            const checkbox = selectedServices[i];
            const serviceId = checkbox.value;
            const staffElement = document.querySelector(`.staff-assignment[data-service-id="${serviceId}"] select`);
            const notesElement = document.querySelector(`.staff-assignment[data-service-id="${serviceId}"] textarea`);

            if (!staffElement || !staffElement.value) {
                // bring attention to the missing assignment
                alert('Please assign a staff member for: ' + (checkbox.dataset.serviceName || 'selected service'));
                // open the assignment block if hidden
                const sa = document.querySelector(`.staff-assignment[data-service-id="${serviceId}"]`);
                if (sa) sa.classList.remove('hidden');
                return; // abort submission
            }

            allocations.push({
                service_id: parseInt(serviceId),
                performed_by: parseInt(staffElement.value),
                notes: notesElement ? notesElement.value : ''
            });
        }

    console.log('Allocations to send:', allocations);
    // Prepare form data
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        formData.append('patient_id', '<?php echo htmlspecialchars($patient['id']); ?>');
        formData.append('visit_id', '<?php echo htmlspecialchars($active_visit['id']); ?>');
        formData.append('allocations', JSON.stringify(allocations));
        formData.append('notes', document.getElementById('generalNotes').value);

        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;

        try {
            const response = await fetch(BASE_PATH + '/doctor/save_allocation', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Show success message
                alert(result.message);
                window.location.href = BASE_PATH + '/doctor/view_patient/' + result.patient_id;
            } else {
                alert('Error: ' + (result.error || result.message || 'Unknown error'));
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to allocate services: ' + error.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Handle service search and category filtering
    const serviceSearch = document.getElementById('serviceSearch');
    const serviceCategory = document.getElementById('serviceCategory');
    const servicesContainer = document.getElementById('servicesContainer');

    function filterServices() {
        const searchText = serviceSearch.value.toLowerCase();
        const selectedCategory = serviceCategory.value;
        const serviceItems = servicesContainer.querySelectorAll('.service-item');

        serviceItems.forEach(item => {
            const itemSearchText = item.dataset.searchText || '';
            const itemCategory = item.dataset.category || '';
            
            const matchesSearch = itemSearchText.includes(searchText);
            const matchesCategory = !selectedCategory || itemCategory === selectedCategory;
            
            item.style.display = (matchesSearch && matchesCategory) ? 'block' : 'none';
        });
    }

    if (serviceSearch) serviceSearch.addEventListener('input', filterServices);
    if (serviceCategory) serviceCategory.addEventListener('change', filterServices);

    // Initialize services in modal dropdown if the element exists (guard against missing markup)
    const services = <?php echo json_encode($available_services); ?>;
    const selectEl = document.getElementById('additionalServiceSelect');
    if (selectEl) {
        services.forEach(service => {
            const option = document.createElement('option');
            option.value = service.id;
            option.textContent = `${service.service_name} (TSH ${service.price})`;
            selectEl.appendChild(option);
        });
    }
});

// Global functions (outside DOMContentLoaded) for onclick handlers

// Show Add Service Modal
window.showAddServiceModal = function() {
    document.getElementById('addServiceModal').classList.remove('hidden');
}

// Close Add Service Modal
window.closeAddServiceModal = function() {
    document.getElementById('addServiceModal').classList.add('hidden');
    // reset create-service fields
    const name = document.getElementById('newServiceName');
    if (name) name.value = '';
    const code = document.getElementById('newServiceCode');
    if (code) code.value = '';
    const cat = document.getElementById('newServiceCategory');
    if (cat) cat.value = 'clinical';
    const catc = document.getElementById('newServiceCategoryCustom');
    if (catc) { catc.value = ''; catc.classList.add('hidden'); }
    const price = document.getElementById('newServicePrice');
    if (price) price.value = '';
    const desc = document.getElementById('newServiceDescription');
    if (desc) desc.value = '';
}

// Add New Service (create in DB) - modal is create-only
window.addNewService = async function() {
    const nameEl = document.getElementById('newServiceName');
    const codeEl = document.getElementById('newServiceCode');
    const catEl = document.getElementById('newServiceCategory');
    const catCustomEl = document.getElementById('newServiceCategoryCustom');
    const priceEl = document.getElementById('newServicePrice');
    const descEl = document.getElementById('newServiceDescription');

    const name = nameEl ? nameEl.value.trim() : '';
    const code = codeEl ? codeEl.value.trim() : '';
    let category = catEl ? catEl.value : '';
    if (category === 'other' && catCustomEl) {
        category = catCustomEl.value.trim();
    }
    const price = priceEl ? priceEl.value.trim() : '0';
    const description = descEl ? descEl.value.trim() : '';

    if (!name) { alert('Service name is required'); return; }

    // send to server to create
    try {
        const fd = new FormData();
        // CSRF token from hidden input
        const csrf = document.querySelector('input[name="csrf_token"]');
        if (csrf) fd.append('csrf_token', csrf.value);
        fd.append('service_name', name);
        fd.append('service_code', code);
        fd.append('service_category', category);
        fd.append('service_price', price || '0');
        fd.append('service_description', description);

        const base = '<?php echo htmlspecialchars($BASE_PATH); ?>';
        const res = await fetch(base + '/doctor/create_service', { method: 'POST', body: fd });
        const json = await res.json();
        if (!json || !json.success) {
            alert(json.error || 'Failed to create service');
            return;
        }

        const svc = json.service;
        // insert into main list (unchecked)
        const servicesContainer = document.getElementById('servicesContainer');
        const allStaff = <?php echo json_encode($available_staff); ?>;

        const categoryNormalized = svc.category || 'clinical';
        const searchText = `${svc.service_name} ${svc.service_code || ''} ${svc.description || ''}`.toLowerCase();

        const staffOptionsHtml = allStaff.map(s => {
            const spec = s.specialization ? ' - ' + s.specialization : '';
            return `<option value="${s.id}">${s.staff_name} (${String(s.role).toUpperCase()})${spec}</option>`;
        }).join('');

        const newId = svc.id;
        const newDiv = document.createElement('div');
        newDiv.className = 'service-item border border-gray-200 rounded-lg p-4 hover:bg-blue-50 transition';
        newDiv.setAttribute('data-service-id', newId);
        newDiv.setAttribute('data-category', categoryNormalized);
        newDiv.setAttribute('data-search-text', searchText);
        newDiv.setAttribute('data-dynamic', 'true');

        newDiv.innerHTML = `
            <div class="flex items-start">
                <input type="checkbox" 
                       class="service-checkbox w-5 h-5 text-blue-600 rounded mt-1 cursor-pointer" 
                       name="service_ids[]"
                       value="${newId}"
                       data-service-name="${svc.service_name}"
                       data-requires-payment="${svc.price > 0 ? 1 : 0}"
                       data-price="${svc.price}">
                <div class="ml-4 flex-1">
                    <label class="cursor-pointer block">
                        <p class="font-semibold text-gray-900">${svc.service_name} <span class="text-xs bg-green-600 text-white px-2 py-1 rounded">NEW</span></p>
                        <p class="text-sm text-gray-600">Code: ${svc.service_code || 'N/A'}</p>
                    </label>
                    ${svc.description ? `<p class="text-sm text-gray-500 mt-1">${svc.description}</p>` : ''}
                </div>
                <div class="text-right">
                    <p class="font-semibold text-gray-900">TSH ${parseFloat(svc.price).toFixed(2)}</p>
                    <button type="button" onclick="removeService(${newId})" class="text-red-600 hover:text-red-700 text-sm mt-2">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>

            <div class="mt-4 ml-9 staff-assignment hidden" data-service-id="${newId}">
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign to Staff Member:</label>
                <select name="allocations[${newId}][performed_by]" 
                        class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 cursor-pointer"
                        required>
                    <option value="">-- Select Staff --</option>
                    ${staffOptionsHtml}
                </select>

                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional):</label>
                    <textarea name="allocations[${newId}][notes]"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              rows="2"
                              placeholder="Enter any specific instructions..."></textarea>
                </div>
            </div>
        `;

        servicesContainer.insertBefore(newDiv, servicesContainer.firstChild);

        // wire checkbox behavior
        const cb = newDiv.querySelector('.service-checkbox');
        cb.addEventListener('change', function() {
            const sa = newDiv.querySelector('.staff-assignment');
            if (!sa) return;
            if (this.checked) {
                sa.classList.remove('hidden');
                const sel = sa.querySelector('select'); if (sel) sel.focus();
            } else {
                sa.classList.add('hidden');
            }
        });

        closeAddServiceModal();
        showToast('Service created and added to list');
    } catch (err) {
        console.error(err);
        alert('Failed to create service');
    }
}
// Helper function to show toast
function showToast(message) {
    const toast = document.createElement('div');
    const classes = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50'.split(' ');
    toast.classList.add(...classes);
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Remove dynamically added service
window.removeService = function(serviceId) {
    if (confirm('Are you sure you want to remove this service?')) {
        const serviceItem = document.querySelector(`.service-item[data-service-id="${serviceId}"][data-dynamic="true"]`);
        if (serviceItem) {
            serviceItem.remove();
            showToast('Service removed');
        }
    }
}


</script>

<style>
    .service-item {
        transition: all 0.2s ease;
    }

    .service-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .staff-assignment {
        padding: 1rem;
        background-color: #f3f4f6;
        border-radius: 0.5rem;
        margin-top: 1rem;
    }
</style>
