    <?php include __DIR__ . '/../../includes/workflow_status.php'; ?>

    <!-- Patient Management Header -->
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-neutral-900">Patient Management</h1>
                <p class="text-neutral-600 mt-1">Manage patient records, registrations, and workflows</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/register_patient" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Register New Patient
                </a>
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/dispense_medicines" class="btn btn-secondary">
                    <i class="fas fa-pills mr-2"></i>Dispense Medicines
                </a>
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/appointments" class="btn btn-secondary">
                    <i class="fas fa-calendar-check mr-2"></i>Appointments
                </a>
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/payments" class="btn btn-secondary">
                    <i class="fas fa-credit-card mr-2"></i>Payments
                </a>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="card p-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               id="patientSearch"
                               placeholder="Search patients by name, phone, or email..."
                               class="form-input pl-10">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-neutral-400"></i>
                    </div>
                </div>
                <div class="flex gap-2">
                    <select class="form-input w-auto">
                        <option>All Status</option>
                        <option>Pending</option>
                        <option>In Progress</option>
                        <option>Completed</option>
                    </select>
                    <button class="btn btn-secondary">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $cards = [
                [
                    'label' => 'Total Patients', 
                    'icon' => 'fas fa-users-medical', 
                    'count' => count($patients), 
                    'color' => 'primary',
                    'trend' => '+12%'
                ],
                [
                    'label' => 'Consultation Paid', 
                    'icon' => 'fas fa-check-circle', 
                    'count' => count(array_filter($patients, fn($p) => $p['consultation_registration_paid'])), 
                    'color' => 'success',
                    'trend' => '+8%'
                ],
                [
                    'label' => 'Awaiting Lab Tests', 
                    'icon' => 'fas fa-clock', 
                    'count' => count(array_filter($patients, fn($p) => $p['consultation_registration_paid'] && !$p['lab_tests_paid'])), 
                    'color' => 'warning',
                    'trend' => '-5%'
                ],
                [
                    'label' => 'Medicine Dispensing', 
                    'icon' => 'fas fa-capsules', 
                    'count' => count(array_filter($patients, fn($p) => $p['current_step'] === 'medicine_dispensing' && !$p['medicine_dispensed'])), 
                    'color' => 'medical',
                    'trend' => '+3%'
                ]
            ];
            
            foreach ($cards as $card): 
                $colorClasses = [
                    'primary' => 'from-primary-500 to-primary-600',
                    'success' => 'from-success-500 to-success-600',
                    'warning' => 'from-warning-500 to-warning-600',
                    'medical' => 'from-medical-accent to-medical-secondary'
                ];
            ?>
                <div class="card p-6 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-600 mb-1"><?php echo $card['label']; ?></p>
                            <p class="text-3xl font-bold text-neutral-900"><?php echo number_format($card['count']); ?></p>
                            <div class="flex items-center mt-2">
                                <span class="text-xs font-medium text-success-600"><?php echo $card['trend']; ?></span>
                                <span class="text-xs text-neutral-500 ml-1">vs last month</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br <?php echo $colorClasses[$card['color']]; ?> rounded-xl flex items-center justify-center">
                            <i class="<?php echo $card['icon']; ?> text-white text-xl"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Patients Table -->
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-neutral-900">Patient Records</h2>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-neutral-600"><?php echo count($patients); ?> patients</span>
                        <button class="btn btn-sm btn-secondary">
                            <i class="fas fa-download mr-1"></i>Export
                        </button>
                    </div>
                </div>
            </div>
            
            <?php if (empty($patients)): ?>
                <div class="p-12 text-center">
                    <div class="w-24 h-24 mx-auto mb-4 bg-neutral-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users-medical text-3xl text-neutral-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-neutral-900 mb-2">No patients registered yet</h3>
                    <p class="text-neutral-600 mb-6">Get started by registering your first patient</p>
                    <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/register_patient" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i>Register First Patient
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Patient</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Workflow Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Payment Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-neutral-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            <?php foreach ($patients as $patient): ?>
                            <tr class="hover:bg-neutral-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-medium">
                                            <?php echo strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)); ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-neutral-900">
                                                <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                            </div>
                                            <div class="text-sm text-neutral-600">
                                                ID: #<?php echo str_pad($patient['id'], 4, '0', STR_PAD_LEFT); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-neutral-900">
                                        <div class="flex items-center mb-1">
                                            <i class="fas fa-phone text-neutral-400 mr-2"></i>
                                            <?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?>
                                        </div>
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope text-neutral-400 mr-2"></i>
                                            <?php echo htmlspecialchars($patient['email'] ?? 'N/A'); ?>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php 
                                    $statusConfig = [
                                        'registration' => ['class' => 'status-pending', 'icon' => 'fas fa-clipboard-list', 'text' => 'Registration'],
                                        'consultation' => ['class' => 'status-progress', 'icon' => 'fas fa-user-md', 'text' => 'Consultation'],
                                        'consultation_registration' => ['class' => 'status-progress', 'icon' => 'fas fa-user-md', 'text' => 'Consultation'],
                                        'lab_tests' => ['class' => 'status-warning', 'icon' => 'fas fa-vial', 'text' => 'Lab Tests'],
                                        'results_review' => ['class' => 'status-info', 'icon' => 'fas fa-clipboard-check', 'text' => 'Results Review'],
                                        'medicine_dispensing' => ['class' => 'status-info', 'icon' => 'fas fa-pills', 'text' => 'Medicine'],
                                        'completed' => ['class' => 'status-success', 'icon' => 'fas fa-check-circle', 'text' => 'Completed']
                                    ];
                                    $currentStatus = $statusConfig[$patient['current_step']] ?? $statusConfig['registration'];
                                    ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $currentStatus['class']; ?>">
                                        <i class="<?php echo $currentStatus['icon']; ?> mr-2"></i>
                                        <?php echo $currentStatus['text']; ?>
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <div class="flex items-center text-xs">
                                            <span class="w-2 h-2 rounded-full mr-2 <?php echo $patient['consultation_registration_paid'] ? 'bg-success-500' : 'bg-neutral-300'; ?>"></span>
                                            Consultation: <?php echo $patient['consultation_registration_paid'] ? 'Paid' : 'Pending'; ?>
                                        </div>
                                        <?php if (isset($patient['lab_tests_required']) && $patient['lab_tests_required']): ?>
                                        <div class="flex items-center text-xs">
                                            <span class="w-2 h-2 rounded-full mr-2 <?php echo $patient['lab_tests_paid'] ? 'bg-success-500' : 'bg-warning-500'; ?>"></span>
                                            Lab Tests: <?php echo $patient['lab_tests_paid'] ? 'Paid' : 'Required'; ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (isset($patient['final_payment_collected'])): ?>
                                        <div class="flex items-center text-xs">
                                            <span class="w-2 h-2 rounded-full mr-2 <?php echo $patient['final_payment_collected'] ? 'bg-success-500' : 'bg-danger-500'; ?>"></span>
                                            Final Payment: <?php echo $patient['final_payment_collected'] ? 'Collected' : 'Pending'; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_patient?id=<?php echo $patient['id']; ?>" 
                                           class="btn btn-sm btn-secondary" title="View Patient">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php if (!$patient['consultation_registration_paid']): ?>
                                        <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/payments?patient_id=<?php echo $patient['id']; ?>&step=consultation" 
                                           class="btn btn-sm btn-warning" title="Process Payment">
                                            <i class="fas fa-credit-card"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($patient['current_step'] === 'medicine_dispensing' && isset($patient['medicine_prescribed']) && $patient['medicine_prescribed'] && (!isset($patient['medicine_dispensed']) || !$patient['medicine_dispensed'])): ?>
                                        <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/dispense_medicines?patient_id=<?php echo $patient['id']; ?>" 
                                           class="btn btn-sm btn-medical" title="Dispense Medicine">
                                            <i class="fas fa-pills"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <div class="relative inline-block">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" data-dropdown="actions-<?php echo $patient['id']; ?>">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu" id="actions-<?php echo $patient['id']; ?>">
                                                <a href="#" class="dropdown-item" onclick="editPatient(<?php echo $patient['id']; ?>)">
                                                    <i class="fas fa-edit mr-2"></i>Edit Patient
                                                </a>
                                                <a href="#" class="dropdown-item">
                                                    <i class="fas fa-history mr-2"></i>View History
                                                </a>
                                                <a href="#" class="dropdown-item" onclick="scheduleAppointment(<?php echo $patient['id']; ?>)">
                                                    <i class="fas fa-calendar-plus mr-2"></i>Schedule Appointment
                                                </a>
                                                <?php if (isset($patient['final_payment_collected']) && !$patient['final_payment_collected'] && isset($patient['medicine_dispensed']) && $patient['medicine_dispensed']): ?>
                                                <a href="#" class="dropdown-item" onclick="processFinalPayment(<?php echo $patient['id']; ?>, '<?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>')">
                                                    <i class="fas fa-credit-card mr-2"></i>Final Payment
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-neutral-200 bg-neutral-50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-neutral-600">
                            Showing 1 to <?php echo count($patients); ?> of <?php echo count($patients); ?> patients
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="btn btn-sm btn-secondary" disabled>
                                <i class="fas fa-chevron-left mr-1"></i>Previous
                            </button>
                            <button class="btn btn-sm btn-secondary" disabled>
                                Next<i class="fas fa-chevron-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

<!-- Patient Modal (Hidden by default) -->
<div id="patientModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Register New Patient</h3>
                <button onclick="closePatientModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="patientForm" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($this->generateCSRF()); ?>">
                <input type="hidden" id="patientId" name="patient_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" id="firstName" name="first_name" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" id="lastName" name="last_name" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input type="date" id="dateOfBirth" name="date_of_birth" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Gender</label>
                        <select id="gender" name="gender" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" id="phone" name="phone"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea id="address" name="address" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Emergency Contact Name</label>
                        <input type="text" id="emergencyName" name="emergency_contact_name"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Emergency Contact Phone</label>
                        <input type="tel" id="emergencyPhone" name="emergency_contact_phone"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closePatientModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        Save Patient
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Search functionality
document.getElementById('patientSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Dropdown functionality
document.addEventListener('click', function(e) {
    if (e.target.matches('.dropdown-toggle')) {
        e.preventDefault();
        const dropdownId = e.target.getAttribute('data-dropdown');
        const dropdown = document.getElementById(dropdownId);
        
        // Close all other dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu.id !== dropdownId) {
                menu.style.display = 'none';
            }
        });
        
        // Toggle current dropdown
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    } else {
        // Close all dropdowns when clicking outside
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});

function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Register New Patient';
    document.getElementById('patientForm').reset();
    document.getElementById('patientId').value = '';
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function viewPatient(id) {
    // Navigate to patient view page
    window.location.href = `<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_patient?id=${id}`;
}

function editPatient(id) {
    // For now, show alert - implement modal editing later
    showToast('Patient editing functionality coming soon!', 'info');
}

function scheduleAppointment(id) {
    // Navigate to appointments with pre-selected patient
    window.location.href = `<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/appointments?patient_id=${id}`;
}

function processFinalPayment(patientId, patientName) {
    if (document.getElementById('finalPaymentModal')) {
        document.getElementById('finalPaymentPatientId').value = patientId;
        document.getElementById('finalPaymentPatientName').textContent = patientName;
        document.getElementById('finalPaymentModal').classList.remove('hidden');
        document.getElementById('finalPaymentForm').reset();
        document.getElementById('finalPaymentPatientId').value = patientId; // Reset after form reset
    } else {
        // Fallback to direct navigation
        window.location.href = `<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/payments?patient_id=${patientId}&type=final`;
    }
}

function closeFinalPaymentModal() {
    if (document.getElementById('finalPaymentModal')) {
        document.getElementById('finalPaymentModal').classList.add('hidden');
    }
}

// Close modal when clicking outside
document.getElementById('finalPaymentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeFinalPaymentModal();
    }
});

// Initialize any tooltips or other interactive elements
document.addEventListener('DOMContentLoaded', function() {
    // Add loading states to buttons when clicked
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.type === 'submit' || this.tagName === 'BUTTON') {
                this.style.opacity = '0.7';
                setTimeout(() => {
                    this.style.opacity = '1';
                }, 1000);
            }
        });
    });
});
</script>

<!-- Final Payment Modal -->
<div id="finalPaymentModal" class="modal-overlay hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Process Final Payment</h3>
            <button onclick="closeFinalPaymentModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="finalPaymentForm" method="POST" action="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/process_final_payment" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
            <input type="hidden" id="finalPaymentPatientId" name="patient_id">

            <div class="space-y-4">
                <div>
                    <label class="form-label">Patient</label>
                    <div id="finalPaymentPatientName" class="form-input bg-neutral-50 text-neutral-900"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="finalAmount" class="form-label">Amount (TZS)</label>
                        <input type="number" id="finalAmount" name="amount" step="0.01" min="0" required
                               class="form-input" placeholder="Enter final payment amount">
                    </div>
                    <div>
                        <label for="finalPaymentMethod" class="form-label">Payment Method</label>
                        <select id="finalPaymentMethod" name="payment_method" required class="form-input">
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="insurance">Insurance</option>
                        </select>
                    </div>
                </div>

                <div class="card p-4 bg-primary-50 border-primary-200">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-primary-600 mt-1 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-primary-900 mb-1">Payment Summary</h4>
                            <p class="text-sm text-primary-700">
                                This payment covers all remaining services including lab tests, medicine dispensing, and consultation fees.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeFinalPaymentModal()" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-credit-card mr-2"></i>Process Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function closeFinalPaymentModal() {
    document.getElementById('finalPaymentModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('finalPaymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFinalPaymentModal();
    }
});
</script>
