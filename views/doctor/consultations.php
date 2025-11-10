<?php $title = "Patient Consultations"; ?>

<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h1 class="text-3xl font-bold mb-2">Patient Consultations</h1>
                <p class="text-blue-100">View and manage all patient consultations efficiently</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-3">
                    <div class="text-sm text-blue-100">Total Consultations</div>
                    <div class="text-2xl font-bold"><?php echo count($consultations ?? []); ?></div>
                </div>
                <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-3">
                    <div class="text-sm text-blue-100">Pending</div>
                    <div class="text-2xl font-bold"><?php echo count($pending_consultations ?? []); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search Section -->
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search patients by name or phone..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <button id="toggleView" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-150">
                    <i class="fas fa-th-list"></i> <span id="viewToggleLabel">Table</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Pending Consultations - Priority Section -->
    <?php if (!empty($pending_consultations)): ?>
    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg shadow-md border-l-4 border-yellow-500 overflow-hidden">
        <div class="px-6 py-4 bg-yellow-100/50 border-b border-yellow-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-circle text-yellow-600 text-xl"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Patients Awaiting Consultation</h2>
                </div>
                <span class="bg-yellow-500 text-white text-sm font-bold px-3 py-1 rounded-full">
                    <?php echo count($pending_consultations); ?> Patient(s)
                </span>
            </div>
            <p class="text-sm text-gray-600 mt-1">These patients require immediate attention</p>
        </div>

        <div class="p-4 pending-cards-view" id="pendingCardsView">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($pending_consultations as $c):
                    $cf = trim($c['patient_first'] ?? $c['first_name'] ?? '');
                    $cl = trim($c['patient_last'] ?? $c['last_name'] ?? '');
                    $cname = trim($cf . ' ' . $cl);
                    if ($cname === '') { $cname = 'Patient #' . ($c['patient_id'] ?? 'N/A'); }
                    $cphone = $c['patient_phone'] ?? $c['phone'] ?? 'N/A';
                ?>
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden border border-yellow-200">
                    <div class="p-4">
                        <div class="flex items-start space-x-3 mb-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-white text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate"><?php echo htmlspecialchars($cname); ?></h3>
                                <p class="text-sm text-gray-500 flex items-center mt-1">
                                    <i class="fas fa-phone text-xs mr-1"></i>
                                    <?php echo htmlspecialchars($cphone); ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_patient/<?php echo $c['patient_id']; ?>" 
                               class="flex-1 text-center bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg text-sm font-medium transition duration-150">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <?php if (($c['status'] ?? 'pending') !== 'completed'): ?>
                            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/attend_patient/<?php echo htmlspecialchars($c['patient_id']); ?>" 
                               class="flex-1 text-center bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg text-sm font-medium transition duration-150">
                                <i class="fas fa-user-md mr-1"></i>Attend
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Pending Patients Table View -->
        <div class="p-4 pending-table-view hidden" id="pendingTableView">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($pending_consultations as $c): ?>
                        <?php
                            $cf = trim($c['patient_first'] ?? $c['first_name'] ?? '');
                            $cl = trim($c['patient_last'] ?? $c['last_name'] ?? '');
                            $cname = trim($cf . ' ' . $cl);
                            if ($cname === '') { $cname = 'Patient #' . ($c['patient_id'] ?? 'N/A'); }
                            $cphone = $c['patient_phone'] ?? $c['phone'] ?? 'N/A';
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($cname); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($cphone); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_patient/<?php echo $c['patient_id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <?php if (($c['status'] ?? 'pending') !== 'completed'): ?>
                                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/attend_patient/<?php echo htmlspecialchars($c['patient_id']); ?>" class="text-green-600 hover:text-green-900">Attend</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Consultation History -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Consultation History</h3>
                    <p class="text-sm text-gray-600 mt-1">Complete record of all consultations</p>
                </div>
                <span class="text-sm text-gray-500">
                    <?php echo count($consultations ?? []); ?> consultation(s)
                </span>
            </div>
        </div>
        
        <?php if (!empty($consultations)): ?>
        <!-- History Table View -->
        <div id="historyTableView" class="history-table-view overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symptoms</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnosis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="consultationTableBody">
                    <?php foreach ($consultations as $consultation): ?>
                    <?php
                        // Calculate age safely
                        $age = 'N/A';
                        if (!empty($consultation['date_of_birth'])) {
                            $dob_timestamp = strtotime($consultation['date_of_birth']);
                            if ($dob_timestamp !== false) {
                                $dob = date_create($consultation['date_of_birth']);
                                $today = date_create('today');
                                $age_diff = date_diff($dob, $today);
                                $age = $age_diff->y;
                            }
                        }
                        
                        // Status handling
                        $status = $consultation['status'] ?? 'pending';
                        $status_classes = 'bg-gray-100 text-gray-800';
                        $status_icon = 'fa-clock';
                        
                        switch ($status) {
                            case 'scheduled':
                                $status_classes = 'bg-yellow-100 text-yellow-800';
                                $status_icon = 'fa-calendar-check';
                                break;
                            case 'in_progress':
                                $status_classes = 'bg-blue-100 text-blue-800';
                                $status_icon = 'fa-spinner';
                                break;
                            case 'completed':
                                $status_classes = 'bg-green-100 text-green-800';
                                $status_icon = 'fa-check-circle';
                                break;
                            case 'cancelled':
                                $status_classes = 'bg-red-100 text-red-800';
                                $status_icon = 'fa-times-circle';
                                break;
                            case 'pending_lab_results':
                                $status_classes = 'bg-purple-100 text-purple-800';
                                $status_icon = 'fa-flask';
                                break;
                        }
                        
                        $status_display = ucwords(str_replace('_', ' ', $status));
                        $symptoms = $consultation['main_complaint'] ?? $consultation['symptoms'] ?? 'N/A';
                        $diagnosis = $consultation['final_diagnosis'] ?? $consultation['diagnosis'] ?? 'Pending';
                        $apt_date = $consultation['appointment_date'] ?? $consultation['visit_date'] ?? $consultation['created_at'];
                        $formatted_date = safe_date('M j, Y', $apt_date, 'N/A');
                        $formatted_time = safe_date('H:i', $apt_date, 'N/A');

                        $first = trim($consultation['first_name'] ?? '');
                        $last = trim($consultation['last_name'] ?? '');
                        $name = trim($first . ' ' . $last);
                        if ($name === '') {
                            $name = 'Patient #' . ($consultation['patient_id'] ?? 'N/A');
                        }
                    ?>
                    <tr class="hover:bg-blue-50 transition duration-150 consultation-row" 
                        data-status="<?php echo $status; ?>"
                        data-name="<?php echo htmlspecialchars(strtolower($name)); ?>"
                        data-phone="<?php echo htmlspecialchars($consultation['patient_phone'] ?? $consultation['phone'] ?? ''); ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0 shadow-sm">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($name); ?></div>
                                    <div class="text-xs text-gray-500">
                                        <i class="fas fa-birthday-cake mr-1"></i>
                                        <?php echo $age !== 'N/A' ? $age . ' years' : 'Age: N/A'; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <i class="fas fa-calendar text-gray-400 mr-1"></i>
                                <?php echo $formatted_date; ?>
                            </div>
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-clock text-gray-400 mr-1"></i>
                                <?php echo $formatted_time; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            <div class="text-sm text-gray-900 truncate" title="<?php echo htmlspecialchars($symptoms); ?>">
                                <?php echo htmlspecialchars(strlen($symptoms) > 50 ? substr($symptoms, 0, 50) . '...' : $symptoms); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            <div class="text-sm text-gray-900 truncate" title="<?php echo htmlspecialchars($diagnosis); ?>">
                                <?php echo htmlspecialchars(strlen($diagnosis) > 50 ? substr($diagnosis, 0, 50) . '...' : $diagnosis); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium shadow-sm <?php echo $status_classes; ?>">
                                <i class="fas <?php echo $status_icon; ?> mr-1"></i>
                                <?php echo $status_display; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_patient/<?php echo $consultation['patient_id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800 hover:underline transition duration-150">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <?php if ($status !== 'completed'): ?>
                                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_patient/<?php echo $consultation['patient_id']; ?>" 
                                   class="text-green-600 hover:text-green-800 hover:underline transition duration-150">
                                    <i class="fas fa-edit mr-1"></i>Continue
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
    <!-- History Card View -->
    <div id="historyCardView" class="history-cards-view hidden divide-y divide-gray-200">
            <?php foreach ($consultations as $consultation): ?>
            <?php
                $age = 'N/A';
                if (!empty($consultation['date_of_birth'])) {
                    $dob_timestamp = strtotime($consultation['date_of_birth']);
                    if ($dob_timestamp !== false) {
                        $dob = date_create($consultation['date_of_birth']);
                        $today = date_create('today');
                        $age_diff = date_diff($dob, $today);
                        $age = $age_diff->y;
                    }
                }
                
                $status = $consultation['status'] ?? 'pending';
                $status_classes = 'bg-gray-100 text-gray-800';
                $status_icon = 'fa-clock';
                
                switch ($status) {
                    case 'scheduled':
                        $status_classes = 'bg-yellow-100 text-yellow-800';
                        $status_icon = 'fa-calendar-check';
                        break;
                    case 'in_progress':
                        $status_classes = 'bg-blue-100 text-blue-800';
                        $status_icon = 'fa-spinner';
                        break;
                    case 'completed':
                        $status_classes = 'bg-green-100 text-green-800';
                        $status_icon = 'fa-check-circle';
                        break;
                    case 'cancelled':
                        $status_classes = 'bg-red-100 text-red-800';
                        $status_icon = 'fa-times-circle';
                        break;
                    case 'pending_lab_results':
                        $status_classes = 'bg-purple-100 text-purple-800';
                        $status_icon = 'fa-flask';
                        break;
                }
                
                $status_display = ucwords(str_replace('_', ' ', $status));
                $symptoms = $consultation['main_complaint'] ?? $consultation['symptoms'] ?? 'N/A';
                $diagnosis = $consultation['final_diagnosis'] ?? $consultation['diagnosis'] ?? 'Pending';
                $apt_date = $consultation['appointment_date'] ?? $consultation['visit_date'] ?? $consultation['created_at'];
                $formatted_date = safe_date('M j, Y', $apt_date, 'N/A');
                $formatted_time = safe_date('H:i', $apt_date, 'N/A');
                
                $first = trim($consultation['first_name'] ?? '');
                $last = trim($consultation['last_name'] ?? '');
                $name = trim($first . ' ' . $last);
                if ($name === '') {
                    $name = 'Patient #' . ($consultation['patient_id'] ?? 'N/A');
                }
            ?>
            <div class="p-4 hover:bg-blue-50 transition duration-150 consultation-card"
                 data-status="<?php echo $status; ?>"
                 data-name="<?php echo htmlspecialchars(strtolower($name)); ?>"
                 data-phone="<?php echo htmlspecialchars($consultation['patient_phone'] ?? $consultation['phone'] ?? ''); ?>">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0 shadow-md">
                            <i class="fas fa-user text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900"><?php echo htmlspecialchars($name); ?></h3>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-birthday-cake text-xs mr-1"></i>
                                <?php echo $age !== 'N/A' ? $age . ' years old' : 'Age: N/A'; ?>
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium shadow-sm <?php echo $status_classes; ?>">
                        <i class="fas <?php echo $status_icon; ?> mr-1"></i>
                        <?php echo $status_display; ?>
                    </span>
                </div>

                <div class="space-y-2 mb-4 bg-gray-50 rounded-lg p-3">
                    <div class="flex items-start">
                        <i class="fas fa-calendar-alt text-blue-500 mt-1 mr-2 w-5"></i>
                        <div>
                            <p class="text-xs text-gray-600">Date & Time</p>
                            <p class="text-sm font-medium text-gray-900"><?php echo $formatted_date; ?> at <?php echo $formatted_time; ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <i class="fas fa-stethoscope text-blue-500 mt-1 mr-2 w-5"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-600">Symptoms</p>
                            <p class="text-sm font-medium text-gray-900 break-words"><?php echo htmlspecialchars($symptoms); ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <i class="fas fa-diagnoses text-blue-500 mt-1 mr-2 w-5"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-600">Diagnosis</p>
                            <p class="text-sm font-medium text-gray-900 break-words"><?php echo htmlspecialchars($diagnosis); ?></p>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-2">
                    <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_patient/<?php echo $consultation['patient_id']; ?>" 
                       class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center px-4 py-2.5 rounded-lg font-medium transition duration-150 shadow-sm">
                        <i class="fas fa-eye mr-2"></i>View Details
                    </a>
                    <?php if ($status !== 'completed'): ?>
                    <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/view_patient/<?php echo $consultation['patient_id']; ?>" 
                       class="flex-1 bg-green-500 hover:bg-green-600 text-white text-center px-4 py-2.5 rounded-lg font-medium transition duration-150 shadow-sm">
                        <i class="fas fa-edit mr-2"></i>Continue
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php else: ?>
        <!-- Empty State -->
        <div class="px-6 py-16 text-center">
            <div class="flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6 shadow-inner">
                    <i class="fas fa-clipboard-list text-gray-400 text-5xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Consultations Yet</h3>
                <p class="text-gray-600 mb-6 max-w-md">
                    You haven't conducted any consultations yet. Start by viewing available patients on your dashboard.
                </p>
                <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/doctor/dashboard" 
                   class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-lg font-medium transition duration-150 shadow-md hover:shadow-lg">
                    <i class="fas fa-arrow-left mr-2"></i>Go to Dashboard
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Enhanced Attend Patient Modal -->
<div id="attendModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 backdrop-blur-sm">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-2xl rounded-lg bg-white mb-10">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Patient Consultation</h3>
                    <p class="text-sm text-gray-600 mt-1">Complete the consultation details below</p>
                </div>
                <button onclick="closeAttendModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-2 transition duration-150">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="attendForm" method="POST" action="<?= $BASE_PATH ?>/doctor/start_consultation" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" id="attendPatientId" name="patient_id">

                <!-- M/C (Main Complaint) -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <label for="mainComplaint" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment-medical text-blue-600 mr-2"></i>
                        M/C - Main Complaint *
                    </label>
                    <textarea id="mainComplaint" name="main_complaint" rows="3" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150"
                              placeholder="Describe the patient's primary complaint and symptoms..."></textarea>
                </div>

                <!-- O/E (On Examination) -->
                <div class="bg-green-50 rounded-lg p-4">
                    <label for="onExamination" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-stethoscope text-green-600 mr-2"></i>
                        O/E - On Examination *
                    </label>
                    <textarea id="onExamination" name="on_examination" rows="4" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-150"
                              placeholder="Document your physical examination findings..."></textarea>
                </div>

                <!-- Preliminary Diagnosis -->
                <div class="bg-yellow-50 rounded-lg p-4">
                    <label for="preliminaryDiagnosis" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-file-medical text-yellow-600 mr-2"></i>
                        Preliminary Diagnosis
                    </label>
                    <textarea id="preliminaryDiagnosis" name="preliminary_diagnosis" rows="2"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition duration-150"
                              placeholder="Enter your initial working diagnosis..."></textarea>
                </div>

                <!-- Final Diagnosis -->
                <div class="bg-purple-50 rounded-lg p-4">
                    <label for="finalDiagnosis" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-check-circle text-purple-600 mr-2"></i>
                        Final Diagnosis
                    </label>
                    <textarea id="finalDiagnosis" name="final_diagnosis" rows="2"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-150"
                              placeholder="Enter the final confirmed diagnosis..."></textarea>
                </div>

                <!-- Treatment Decision -->
                <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-lg p-5 border border-orange-200">
                    <h4 class="flex items-center text-base font-semibold text-gray-800 mb-4">
                        <i class="fas fa-procedures text-orange-600 mr-2"></i>
                        Treatment Decision *
                    </h4>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition duration-150">
                            <input type="radio" name="treatment_decision" value="lab_tests" 
                                   class="mr-3 text-blue-600 w-4 h-4" required>
                            <div class="flex items-center">
                                <i class="fas fa-flask text-blue-600 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">Send for Lab Tests</span>
                            </div>
                        </label>
                        <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-green-300 hover:bg-green-50 cursor-pointer transition duration-150">
                            <input type="radio" name="treatment_decision" value="prescribe_medicine" 
                                   class="mr-3 text-green-600 w-4 h-4" required>
                            <div class="flex items-center">
                                <i class="fas fa-pills text-green-600 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">Prescribe Medicine</span>
                            </div>
                        </label>
                        <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-purple-300 hover:bg-purple-50 cursor-pointer transition duration-150">
                            <input type="radio" name="treatment_decision" value="both" 
                                   class="mr-3 text-purple-600 w-4 h-4" required>
                            <div class="flex items-center">
                                <i class="fas fa-notes-medical text-purple-600 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">Both Lab Tests & Medicine</span>
                            </div>
                        </label>
                        <label class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-gray-400 hover:bg-gray-50 cursor-pointer transition duration-150">
                            <input type="radio" name="treatment_decision" value="discharge" 
                                   class="mr-3 text-gray-600 w-4 h-4" required>
                            <div class="flex items-center">
                                <i class="fas fa-sign-out-alt text-gray-600 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700">Discharge Patient</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeAttendModal()"
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-150 font-medium">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition duration-150 font-medium shadow-md hover:shadow-lg">
                        <i class="fas fa-save mr-2"></i>Complete Consultation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    
    function filterConsultations() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        
        // Filter table rows
        const tableRows = document.querySelectorAll('.consultation-row');
        tableRows.forEach(row => {
            const name = row.dataset.name || '';
            const phone = row.dataset.phone || '';
            const status = row.dataset.status || '';
            
            const matchesSearch = name.includes(searchTerm) || phone.includes(searchTerm);
            const matchesStatus = statusValue === 'all' || status === statusValue;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Filter mobile cards
        const cards = document.querySelectorAll('.consultation-card');
        cards.forEach(card => {
            const name = card.dataset.name || '';
            const phone = card.dataset.phone || '';
            const status = card.dataset.status || '';
            
            const matchesSearch = name.includes(searchTerm) || phone.includes(searchTerm);
            const matchesStatus = statusValue === 'all' || status === statusValue;
            
            if (matchesSearch && matchesStatus) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', filterConsultations);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', filterConsultations);
    }
});

// Modal Functions
function attendPatient(patientId) {
    document.getElementById('attendPatientId').value = patientId;
    document.getElementById('attendModal').classList.remove('hidden');
    document.getElementById('attendForm').reset();
    document.getElementById('attendPatientId').value = patientId;
    document.body.style.overflow = 'hidden';
}

function closeAttendModal() {
    document.getElementById('attendModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modal when clicking outside
document.getElementById('attendModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAttendModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('attendModal').classList.contains('hidden')) {
        closeAttendModal();
    }
});

// Form validation and submission feedback
document.getElementById('attendForm')?.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    }
});

// Add animation to newly loaded content
window.addEventListener('load', function() {
    const rows = document.querySelectorAll('.consultation-row, .consultation-card');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(10px)';
        setTimeout(() => {
            row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 30);
    });
});

// Toggle View Functionality (Table vs Card view for both pending and history)
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleView');
    const toggleLabel = document.getElementById('viewToggleLabel');
    
    // View keys
    const PENDING_CARDS = 'pendingCardsView';
    const PENDING_TABLE = 'pendingTableView';
    const HISTORY_CARDS = 'historyCardView';
    const HISTORY_TABLE = 'historyTableView';
    
    // Load saved view preference from localStorage
    let viewMode = localStorage.getItem('consultationViewMode') || 'cards'; // 'cards' or 'table'
    
    // Function to update view
    function updateView(mode) {
        if (mode === 'table') {
            // Show table views, hide card views
            document.getElementById(PENDING_TABLE)?.classList.remove('hidden');
            document.getElementById(PENDING_CARDS)?.classList.add('hidden');
            document.getElementById(HISTORY_TABLE)?.classList.remove('hidden');
            document.getElementById(HISTORY_CARDS)?.classList.add('hidden');
            toggleLabel.textContent = 'Cards';
        } else {
            // Show card views, hide table views
            document.getElementById(PENDING_TABLE)?.classList.add('hidden');
            document.getElementById(PENDING_CARDS)?.classList.remove('hidden');
            document.getElementById(HISTORY_TABLE)?.classList.add('hidden');
            document.getElementById(HISTORY_CARDS)?.classList.remove('hidden');
            toggleLabel.textContent = 'Table';
        }
        localStorage.setItem('consultationViewMode', mode);
    }
    
    // Initialize view based on saved preference
    updateView(viewMode);
    
    // Toggle button click handler
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            viewMode = viewMode === 'cards' ? 'table' : 'cards';
            updateView(viewMode);
        });
    }
});
</script>

<style>
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Utility class for hiding elements */
.hidden {
    display: none !important;
}

/* Smooth transitions */
* {
    transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Radio button custom styling */
input[type="radio"]:checked {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e");
}

/* Hover effects for cards */
.hover\:shadow-md:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .bg-gradient-to-r,
    .bg-gradient-to-br {
        background: white !important;
        color: black !important;
    }
}

/* Loading animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

/* Status badge pulse animation for pending items */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.bg-yellow-100 {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>