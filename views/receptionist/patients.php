<?php include __DIR__ . '/../../includes/workflow_status.php'; ?>

<!-- Page Header with Professional Gradient -->
<div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-lg shadow-xl p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="text-white">
            <h1 class="text-3xl font-bold flex items-center">
                <i class="fas fa-users-medical mr-3 text-blue-200"></i>
                Patient Management
            </h1>
            <p class="text-blue-100 mt-2 text-lg">Manage patient records, registrations, and workflows</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/register_patient" class="bg-white text-blue-700 hover:bg-blue-50 px-4 py-2 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-plus mr-2"></i>Register New Patient
            </a>
            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/create_revisit" class="bg-green-500 bg-opacity-90 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-user-check mr-2"></i>Patient Revisit
            </a>
            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/dispense_medicines" class="bg-blue-500 bg-opacity-30 hover:bg-opacity-50 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300 backdrop-blur-sm">
                <i class="fas fa-pills mr-2"></i>Dispense Medicines
            </a>
            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/appointments" class="bg-blue-500 bg-opacity-30 hover:bg-opacity-50 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300 backdrop-blur-sm">
                <i class="fas fa-calendar-check mr-2"></i>Appointments
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards with Professional Design -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php
    $totalPatients = count($patients);
    $consultationPaid = count(array_filter($patients, fn($p) => $p['consultation_registration_paid']));
    $awaitingLabTests = count(array_filter($patients, fn($p) => $p['consultation_registration_paid'] && !$p['lab_tests_paid'] && $p['lab_tests_required']));
    $medicineDispensing = count(array_filter($patients, fn($p) => $p['medicine_prescribed'] && !$p['medicine_dispensed']));
    
    $cards = [
        [
            'label' => 'Total Patients', 
            'icon' => 'fas fa-users-medical', 
            'count' => $totalPatients, 
            'color' => 'from-blue-500 to-blue-600',
            'trend' => '+12%',
            'trend_icon' => 'fas fa-arrow-up',
            'trend_color' => 'text-green-600'
        ],
        [
            'label' => 'Consultation Paid', 
            'icon' => 'fas fa-check-circle', 
            'count' => $consultationPaid, 
            'color' => 'from-green-500 to-green-600',
            'trend' => '+8%',
            'trend_icon' => 'fas fa-arrow-up', 
            'trend_color' => 'text-green-600'
        ],
        [
            'label' => 'Awaiting Lab Tests', 
            'icon' => 'fas fa-clock', 
            'count' => $awaitingLabTests, 
            'color' => 'from-yellow-500 to-yellow-600',
            'trend' => '-5%',
            'trend_icon' => 'fas fa-arrow-down',
            'trend_color' => 'text-red-600'
        ],
        [
            'label' => 'Medicine Dispensing', 
            'icon' => 'fas fa-capsules', 
            'count' => $medicineDispensing, 
            'color' => 'from-purple-500 to-purple-600',
            'trend' => '+3%',
            'trend_icon' => 'fas fa-arrow-up',
            'trend_color' => 'text-green-600'
        ]
    ];
    
    foreach ($cards as $card): ?>
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:scale-105 cursor-pointer">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1"><?php echo $card['label']; ?></p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo number_format($card['count']); ?></p>
                    <div class="flex items-center mt-2">
                        <i class="<?php echo $card['trend_icon']; ?> text-xs mr-1 <?php echo $card['trend_color']; ?>"></i>
                        <span class="text-xs font-medium <?php echo $card['trend_color']; ?>"><?php echo $card['trend']; ?></span>
                        <span class="text-xs text-gray-500 ml-1">vs last month</span>
                    </div>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br <?php echo $card['color']; ?> rounded-xl flex items-center justify-center shadow-lg">
                    <i class="<?php echo $card['icon']; ?> text-white text-xl"></i>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Patients Table with Professional Design -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-table mr-3 text-blue-600"></i>
                Patient Records
            </h2>
            <div class="flex items-center gap-3">
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium mr-2">
                    <?php echo count($patients); ?> patients
                </span>

                <!-- Compact search moved next to count/export -->
                <div class="compact-search hidden sm:flex items-center bg-gray-50 border border-gray-200 rounded-lg px-3 py-1">
                    <i class="fas fa-search text-gray-400 mr-2"></i>
                    <input id="compactPatientSearch" type="text" placeholder="Search patients..." class="compact-search-input" />
                </div>

                <button class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
            </div>
        </div>
    </div>
    <!-- Search and Filters with Professional Styling -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6 max-w-4xl mx-auto">
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="text-sm text-gray-600">Filter patients by status</div>
        <div class="flex gap-3 items-center">
            <select id="statusFilter" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="consultation_paid">Consultation Paid</option>
                <option value="lab_tests">Lab Tests</option>
                <option value="medicine_dispensing">Medicine Dispensing</option>
                <option value="completed">Completed</option>
            </select>
            <button class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </div>
    </div>
</div>

    
    <?php if (empty($patients)): ?>
        <div class="p-12 text-center">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center shadow-lg">
                <i class="fas fa-users-medical text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">No patients registered yet</h3>
            <p class="text-gray-600 mb-8 text-lg">Get started by registering your first patient to begin managing healthcare records</p>
            <a href="<?php echo BASE_PATH; ?>/receptionist/register_patient" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-plus mr-2"></i>Register First Patient
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Workflow Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Payment Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($patients as $patient): ?>
                    <tr class="hover:bg-blue-50 transition-all duration-300 cursor-pointer">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                                    <?php echo strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)); ?>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-600 flex items-center">
                                        <i class="fas fa-id-badge mr-1 text-gray-400"></i>
                                        RegNo: <?php echo htmlspecialchars($patient['registration_number']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-phone text-blue-500 mr-2"></i>
                                    <span class="font-medium"><?php echo htmlspecialchars($patient['phone'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-blue-500 mr-2"></i>
                                    <span class="text-gray-600"><?php echo htmlspecialchars($patient['email'] ?? 'N/A'); ?></span>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                            $statusConfig = [
                                'registration' => ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-300', 'icon' => 'fas fa-clipboard-list', 'text' => 'Registration'],
                                'consultation' => ['class' => 'bg-blue-100 text-blue-800 border-blue-300', 'icon' => 'fas fa-user-md', 'text' => 'Consultation'],
                'consultation_registration' => ['class' => 'bg-blue-100 text-blue-800 border border-blue-300', 'icon' => 'fas fa-user-md', 'text' => 'Consultation'],
                'lab_tests' => ['class' => 'bg-yellow-100 text-yellow-800 border border-yellow-300', 'icon' => 'fas fa-vial', 'text' => 'Lab Tests'],
                'results_review' => ['class' => 'bg-purple-100 text-purple-800 border border-purple-300', 'icon' => 'fas fa-clipboard-check', 'text' => 'Results Review'],
                'medicine_dispensing' => ['class' => 'bg-indigo-100 text-indigo-800 border border-indigo-300', 'icon' => 'fas fa-pills', 'text' => 'Medicine'],
                'completed' => ['class' => 'bg-green-100 text-green-800 border border-green-300', 'icon' => 'fas fa-check-circle', 'text' => 'Completed']
            ];
            // Guard against undefined index: use a local step variable with sensible default
            $step = $patient['current_step'] ?? 'registration';
            $currentStatus = $statusConfig[$step] ?? $statusConfig['registration'];
            ?>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $currentStatus['class']; ?> shadow-sm">
                <i class="<?php echo $currentStatus['icon']; ?> mr-2"></i>
                <?php echo $currentStatus['text']; ?>
            </span>
        </td>
        
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="space-y-1">
                <div class="flex items-center text-xs">
                    <span class="w-2 h-2 rounded-full mr-2 <?php echo $patient['consultation_registration_paid'] ? 'bg-green-500' : 'bg-gray-300'; ?>"></span>
                    <span class="<?php echo $patient['consultation_registration_paid'] ? 'text-green-700' : 'text-gray-600'; ?> font-medium">
                        Consultation: <?php echo $patient['consultation_registration_paid'] ? 'Paid' : 'Pending'; ?>
                    </span>
                </div>
                <?php if (isset($patient['lab_tests_required']) && $patient['lab_tests_required']): ?>
                <div class="flex items-center text-xs">
                    <span class="w-2 h-2 rounded-full mr-2 <?php echo $patient['lab_tests_paid'] ? 'bg-green-500' : 'bg-yellow-500'; ?>"></span>
                    <span class="<?php echo $patient['lab_tests_paid'] ? 'text-green-700' : 'text-yellow-700'; ?> font-medium">
                        Lab Tests: <?php echo $patient['lab_tests_paid'] ? 'Paid' : 'Required'; ?>
                    </span>
                </div>
                <?php endif; ?>
                <?php if (isset($patient['final_payment_collected'])): ?>
                <div class="flex items-center text-xs">
                    <span class="w-2 h-2 rounded-full mr-2 <?php echo $patient['final_payment_collected'] ? 'bg-green-500' : 'bg-red-500'; ?>"></span>
                    <span class="<?php echo $patient['final_payment_collected'] ? 'text-green-700' : 'text-red-700'; ?> font-medium">
                        Final Payment: <?php echo $patient['final_payment_collected'] ? 'Collected' : 'Pending'; ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </td>
        
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center gap-2">
                <?php 
                $userRole = $_SESSION['role'] ?? '';
                // Debug: Let's see what role is detected
                echo "<!-- DEBUG: User role = '$userRole' -->";
                $viewUrl = ($userRole === 'receptionist') 
                    ? htmlspecialchars($BASE_PATH) . "/receptionist/view_patient?id=" . $patient['id']
                   : htmlspecialchars($BASE_PATH) . "/receptionist/view_patient?id=" . $patient['id'];
              echo "<!-- DEBUG: Generated URL = '$viewUrl' -->";
                ?>
                <a href="<?php echo $viewUrl; ?>" 
                   class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105" title="View Patient">
                    <i class="fas fa-eye"></i>
                </a>
                
                <?php if (!$patient['consultation_registration_paid']): ?>
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/payments?patient_id=<?php echo $patient['id']; ?>&+step=consultation" 
                   class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105" title="Process Payment">
                    <i class="fas fa-credit-card"></i>
                </a>
                <?php endif; ?>
                
                <?php if ($step === 'medicine_dispensing' && isset($patient['medicine_prescribed']) && $patient['medicine_prescribed'] && (!isset($patient['medicine_dispensed']) || !$patient['medicine_dispensed'])): ?>
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/dispense_medicines?patient_id=<?php echo $patient['id']; ?>" 
                   class="bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105" title="Dispense Medicine">
                    <i class="fas fa-pills"></i>
                </a>
                <?php endif; ?>
                
                <div class="relative inline-block">
                    <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-300" onclick="toggleDropdown('actions-<?php echo $patient['id']; ?>')">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10 hidden" id="actions-<?php echo $patient['id']; ?>">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors" onclick="editPatient(<?php echo $patient['id']; ?>)">
                                <i class="fas fa-edit mr-2 text-blue-500"></i>Edit Patient
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition-colors">
                                <i class="fas fa-history mr-2 text-green-500"></i>View History
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors" onclick="scheduleAppointment(<?php echo $patient['id']; ?>)">
                                <i class="fas fa-calendar-plus mr-2 text-purple-500"></i>Schedule Appointment
                            </a>
                            <?php if (isset($patient['final_payment_collected']) && !$patient['final_payment_collected'] && isset($patient['medicine_dispensed']) && $patient['medicine_dispensed']): ?>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 transition-colors" onclick="processFinalPayment(<?php echo $patient['id']; ?>, '<?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>')">
                                <i class="fas fa-credit-card mr-2 text-yellow-500"></i>Final Payment
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>
</div>

<!-- Professional Pagination -->
<div class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-600 font-medium">
            Showing 1 to <?php echo count($patients); ?> of <?php echo count($patients); ?> patients
        </div>
        <div class="flex items-center gap-2">
            <button class="bg-gray-100 text-gray-400 px-3 py-1 rounded-lg text-sm font-medium cursor-not-allowed" disabled>
                <i class="fas fa-chevron-left mr-1"></i>Previous
            </button>
            <button class="bg-gray-100 text-gray-400 px-3 py-1 rounded-lg text-sm font-medium cursor-not-allowed" disabled>
                Next<i class="fas fa-chevron-right ml-1"></i>
            </button>
        </div>
    </div>
</div>
<?php endif; ?>
</div>

<!-- Professional Patient Modal -->
<div id="patientModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-2xl rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center" id="modalTitle">
                    <i class="fas fa-user-plus mr-3 text-blue-600"></i>
                    Register New Patient
                </h3>
                <button onclick="closePatientModal()" class="text-gray-400 hover:text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <form id="patientForm" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" id="patientId" name="patient_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                        <input type="text" id="firstName" name="first_name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                        <input type="text" id="lastName" name="last_name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" id="dateOfBirth" name="date_of_birth" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Gender</label>
                        <select id="gender" name="gender" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <!-- 'Other' option removed per project requirement (Tanzania system) -->
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                    <input type="tel" id="phone" name="phone"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                <textarea id="address" name="address" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 resize-none"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Emergency Contact Name</label>
                    <input type="text" id="emergencyName" name="emergency_contact_name"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Emergency Contact Phone</label>
                    <input type="tel" id="emergencyPhone" name="emergency_contact_phone"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <button type="button" onclick="closePatientModal()"
                        class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-300 transform hover:scale-105">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-save mr-2"></i>Save Patient
                </button>
            </div>
        </form>
    </div>
</div>
</div>

<!-- Enhanced JavaScript with Professional Features -->
<script>
// Search functionality with enhanced UX
document.getElementById('compactPatientSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const isVisible = text.includes(searchTerm);
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount++;
    });
    
    // Update results count if needed
    console.log(`Showing ${visibleCount} of ${rows.length} patients`);
});

// Enhanced dropdown functionality
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('[id^="actions-"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(menu => {
        if (menu.id !== dropdownId) {
            menu.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.relative')) {
        document.querySelectorAll('[id^="actions-"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }
});

function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-plus mr-3 text-blue-600"></i>Register New Patient';
    document.getElementById('patientForm').reset();
    document.getElementById('patientId').value = '';
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function viewPatient(id) {
    // Delegate to global viewPatient (defined in layout) which is role-aware
    if (typeof window.viewPatient === 'function') {
        window.viewPatient(id);
        return;
    }

    // Fallback: navigate to doctor view if global function isn't available
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
                            <?php 
                            // Use the same session key used elsewhere: 'user_role'
                            $userRole = $_SESSION['user_role'] ?? $_SESSION['role'] ?? '';
                            // Debug: Let's see what role is detected
                            echo "<!-- DEBUG: User role = '$userRole' -->";

                            // Build a role-appropriate URL (use query param format to match routing elsewhere)
                            if ($userRole === 'receptionist') {
                                $viewUrl = htmlspecialchars($BASE_PATH) . "/receptionist/view_patient?id=" . $patient['id'];
                            } elseif ($userRole === 'admin') {
                                $viewUrl = htmlspecialchars($BASE_PATH) . "/admin/view_patient?id=" . $patient['id'];
                            } else {
                                // default to doctor view
                                $viewUrl = htmlspecialchars($BASE_PATH) . "/doctor/view_patient?id=" . $patient['id'];
                            }

                            echo "<!-- DEBUG: Generated URL = '$viewUrl' -->";
                            ?>
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

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function editPatient(patientId) {
    // Implementation for editing patient
    console.log('Edit patient:', patientId);
    // You can add edit functionality here
}

function scheduleAppointment(patientId) {
    // Implementation for scheduling appointment
    console.log('Schedule appointment for patient:', patientId);
    window.location.href = `${BASE_PATH}/receptionist/appointments?patient_id=${patientId}`;
}

function processFinalPayment(patientId, patientName) {
    // Implementation for processing final payment
    console.log('Process final payment for patient:', patientId);
    // You can add payment processing functionality here
}

// Status filter functionality
document.getElementById('statusFilter')?.addEventListener('change', function(e) {
    const selectedStatus = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        if (!selectedStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            const statusText = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const isVisible = statusText.includes(selectedStatus);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        }
    });
});

// Close modal when clicking outside
document.getElementById('finalPaymentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeFinalPaymentModal();
    }
});

// Enhanced hover effects and interactivity
document.addEventListener('DOMContentLoaded', function() {
    // Add professional hover effects to stat cards
    document.querySelectorAll('.transform').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
        });
    });
    
    // Add loading states to buttons
    document.querySelectorAll('a[class*="bg-"]').forEach(btn => {
        btn.addEventListener('click', function() {
            this.style.opacity = '0.7';
            setTimeout(() => {
                this.style.opacity = '1';
            }, 1000);
        });
    });

    // Make table rows clickable to view patient (role-aware)
    document.querySelectorAll('tbody tr').forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on action buttons/links
            if (e.target.closest('a') || e.target.closest('button')) {
                return;
            }
            
            // Find the patient ID from the view patient link in this row
            const viewLink = this.querySelector('a[href*="view_patient"]');
            if (viewLink) {
                // Use the existing role-aware link that's already generated in PHP
                const href = viewLink.getAttribute('href');
                window.location.href = href;
            }
        });
    });
});
</script>
