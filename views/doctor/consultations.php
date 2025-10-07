<div class="space-y-6">
    <!-- Header -->
    <!-- <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
        <?php $title = "Patient Consultation"; ?>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Consultations</h1>
            <p class="text-sm text-gray-600 mt-1">View and manage patient consultations</p>
        </div>
        <a href="/KJ/doctor/dashboard" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg text-center font-medium shadow-sm transition duration-150">
            <i class="fas fa-plus mr-2"></i>New Consultation
        </a>
    </div> -->

    <!-- Patients Registered for Consultation -->
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Patients Registered for Consultation</h2>
                <p class="text-sm text-gray-600">List of patients currently registered and awaiting consultation.</p>
            </div>
            <div class="text-sm text-gray-500">
                <?php echo count($consultations); ?> patient(s)
            </div>
        </div>

        <?php if (!empty($consultations)): ?>
            <!-- Compact list view so patients are visible regardless of table styles -->
            <div class="mb-3">
                <ul class="space-y-2">
                    <?php foreach ($consultations as $c):
                        $cf = trim($c['patient_first'] ?? $c['first_name'] ?? '');
                        $cl = trim($c['patient_last'] ?? $c['last_name'] ?? '');
                        $cname = trim($cf . ' ' . $cl);
                        if ($cname === '') { $cname = 'Patient #' . ($c['patient_id'] ?? 'N/A'); }
                        $cphone = $c['patient_phone'] ?? $c['phone'] ?? 'N/A';
                    ?>
                    <li class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($cname); ?></div>
                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($cphone); ?></div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="/KJ/doctor/view_patient/<?php echo $c['patient_id']; ?>" class="text-blue-600 hover:text-blue-900 text-sm"><i class="fas fa-eye mr-1"></i>View</a>
                            <?php if (($c['status'] ?? 'pending') !== 'completed'): ?>
                            <button type="button" onclick="attendPatient(<?php echo $c['patient_id']; ?>)" class="text-green-600 hover:text-green-900 text-sm bg-transparent border-none p-0"><i class="fas fa-user-md mr-1"></i>Attend</button>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($consultations)): ?>
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($consultations as $consultation): ?>
                    <?php
                        $status = $consultation['status'] ?? 'pending';
                        $apt_date = $consultation['appointment_date'] ?? $consultation['visit_date'] ?? $consultation['created_at'];
                        $registered = date('M j, Y H:i', strtotime($apt_date));
                        // Safe name fallback (try aliased fields first)
                        $first = trim($consultation['patient_first'] ?? $consultation['first_name'] ?? '');
                        $last = trim($consultation['patient_last'] ?? $consultation['last_name'] ?? '');
                        $name = trim($first . ' ' . $last);
                        if ($name === '') {
                            $name = 'Patient #' . ($consultation['patient_id'] ?? 'N/A');
                        }
                    ?>
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($name); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($consultation['patient_phone'] ?? $consultation['phone'] ?? 'N/A'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo $registered; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $status))); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="/KJ/doctor/view_patient/<?php echo $consultation['patient_id']; ?>" class="text-blue-600 hover:text-blue-900 transition duration-150"><i class="fas fa-eye mr-1"></i>View</a>
                                <?php if ($status !== 'completed'): ?>
                                <button type="button" onclick="attendPatient(<?php echo $consultation['patient_id']; ?>)" class="text-green-600 hover:text-green-900 transition duration-150 bg-transparent border-none p-0"><i class="fas fa-user-md mr-1"></i>Attend</button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile List -->
        <div class="md:hidden divide-y divide-gray-200">
        <?php foreach ($consultations as $consultation): ?>
        <?php $apt_date = $consultation['appointment_date'] ?? $consultation['visit_date'] ?? $consultation['created_at'];
            $mfirst = trim($consultation['patient_first'] ?? $consultation['first_name'] ?? '');
            $mlast = trim($consultation['patient_last'] ?? $consultation['last_name'] ?? '');
            $mname = trim($mfirst . ' ' . $mlast);
            if ($mname === '') { $mname = 'Patient #' . ($consultation['patient_id'] ?? 'N/A'); }
        ?>
            <div class="p-4 hover:bg-gray-50 transition duration-150">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($mname); ?></div>
                        <div class="text-xs text-gray-500"><?php echo htmlspecialchars($consultation['patient_phone'] ?? $consultation['phone'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="text-xs text-gray-500"><?php echo date('M j, Y H:i', strtotime($apt_date)); ?></div>
                </div>
                <div class="flex space-x-2 mt-3">
                    <a href="/KJ/doctor/view_patient/<?php echo $consultation['patient_id']; ?>" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center px-3 py-2 rounded-lg font-medium transition duration-150"><i class="fas fa-eye mr-1"></i>View</a>
                    <?php if (($consultation['status'] ?? 'pending') !== 'completed'): ?>
                    <button type="button" onclick="attendPatient(<?php echo $consultation['patient_id']; ?>)" class="flex-1 bg-green-500 hover:bg-green-600 text-white text-center px-3 py-2 rounded-lg font-medium transition duration-150"><i class="fas fa-user-md mr-1"></i>Attend</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <div class="text-sm text-gray-600">No patients currently registered for consultation.</div>
        <?php endif; ?>
    </div>

    <!-- Consultations List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Consultation History</h3>
        </div>
        
        <?php if (!empty($consultations)): ?>
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Symptoms</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnosis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($consultations as $index => $consultation): ?>
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
                        
                        // Get main complaint
                        $symptoms = $consultation['main_complaint'] ?? $consultation['symptoms'] ?? 'N/A';
                        
                        // Get diagnosis
                        $diagnosis = $consultation['final_diagnosis'] ?? $consultation['diagnosis'] ?? 'Pending';
                        
                        // Format appointment date
                        $apt_date = $consultation['appointment_date'] ?? $consultation['visit_date'] ?? $consultation['created_at'];
                        $formatted_date = date('M j, Y', strtotime($apt_date));
                        $formatted_time = date('H:i', strtotime($apt_date));

                        // Safe name fallback
                        $first = trim($consultation['first_name'] ?? '');
                        $last = trim($consultation['last_name'] ?? '');
                        $name = trim($first . ' ' . $last);
                        if ($name === '') {
                            $name = 'Patient #' . ($consultation['patient_id'] ?? 'N/A');
                        }
                    ?>
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($name); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?php echo $age !== 'N/A' ? $age . ' years' : 'Age: N/A'; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo $formatted_date; ?></div>
                            <div class="text-xs text-gray-500"><?php echo $formatted_time; ?></div>
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
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_classes; ?>">
                                <i class="fas <?php echo $status_icon; ?> mr-1"></i>
                                <?php echo $status_display; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="/KJ/doctor/view_patient/<?php echo $consultation['patient_id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900 transition duration-150">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <?php if ($status !== 'completed'): ?>
                                <a href="/KJ/doctor/view_patient/<?php echo $consultation['patient_id']; ?>" 
                                   class="text-green-600 hover:text-green-900 transition duration-150">
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
        
        <!-- Mobile Cards View -->
        <div class="md:hidden divide-y divide-gray-200">
            <?php foreach ($consultations as $index => $consultation): ?>
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
                
                // Get data
                $symptoms = $consultation['main_complaint'] ?? $consultation['symptoms'] ?? 'N/A';
                $diagnosis = $consultation['final_diagnosis'] ?? $consultation['diagnosis'] ?? 'Pending';
                $apt_date = $consultation['appointment_date'] ?? $consultation['visit_date'] ?? $consultation['created_at'];
                $formatted_date = date('M j, Y', strtotime($apt_date));
                $formatted_time = date('H:i', strtotime($apt_date));
            ?>
            <div class="p-4 hover:bg-gray-50 transition duration-150">
                <!-- Patient Header -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-user text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">
                                <?php echo htmlspecialchars($consultation['first_name'] . ' ' . $consultation['last_name']); ?>
                            </h3>
                            <p class="text-sm text-gray-600">
                                <?php echo $age !== 'N/A' ? $age . ' years old' : 'Age: N/A'; ?>
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?php echo $status_classes; ?>">
                        <i class="fas <?php echo $status_icon; ?> mr-1"></i>
                        <?php echo $status_display; ?>
                    </span>
                </div>

                <!-- Consultation Details -->
                <div class="space-y-2 mb-4">
                    <div class="flex items-start">
                        <i class="fas fa-calendar-alt text-gray-400 mt-1 mr-2 w-4"></i>
                        <div>
                            <p class="text-sm text-gray-600">Date & Time</p>
                            <p class="text-sm font-medium text-gray-900"><?php echo $formatted_date; ?> at <?php echo $formatted_time; ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <i class="fas fa-stethoscope text-gray-400 mt-1 mr-2 w-4"></i>
                        <div class="flex-1">
                            <p class="text-sm text-gray-600">Symptoms</p>
                            <p class="text-sm font-medium text-gray-900 break-words"><?php echo htmlspecialchars($symptoms); ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <i class="fas fa-diagnoses text-gray-400 mt-1 mr-2 w-4"></i>
                        <div class="flex-1">
                            <p class="text-sm text-gray-600">Diagnosis</p>
                            <p class="text-sm font-medium text-gray-900 break-words"><?php echo htmlspecialchars($diagnosis); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <a href="/KJ/doctor/view_patient/<?php echo $consultation['patient_id']; ?>" 
                       class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center px-4 py-2.5 rounded-lg font-medium transition duration-150">
                        <i class="fas fa-eye mr-2"></i>View Details
                    </a>
                    <?php if ($status !== 'completed'): ?>
                    <a href="/KJ/doctor/view_patient/<?php echo $consultation['patient_id']; ?>" 
                       class="flex-1 bg-green-500 hover:bg-green-600 text-white text-center px-4 py-2.5 rounded-lg font-medium transition duration-150">
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
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-clipboard-list text-gray-400 text-5xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Consultations Yet</h3>
                <p class="text-gray-600 mb-6 max-w-md">
                    You haven't conducted any consultations yet. Start by viewing available patients on your dashboard.
                </p>
                <a href="/KJ/doctor/dashboard" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>Go to Dashboard
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
