<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Consultations</h1>
        <a href="/KJ/doctor/consultations" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            <i class="fas fa-plus mr-2"></i>New Consultation
        </a>
    </div>

    <!-- Consultations Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Patient Consultations</h3>
        </div>
        <div class="overflow-x-auto">
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
                    <?php foreach ($consultations as $consultation): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($consultation['first_name'] . ' ' . $consultation['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Age: <?php 
                                        if (!empty($consultation['date_of_birth'])) {
                                            echo date_diff(date_create($consultation['date_of_birth']), date_create('today'))->y;
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo date('M j, Y H:i', strtotime($consultation['appointment_date'])); ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                            <?php echo htmlspecialchars($consultation['symptoms'] ?? 'N/A'); ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                            <?php echo htmlspecialchars($consultation['diagnosis'] ?? 'Pending'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                <?php
                                switch ($consultation['status']) {
                                    case 'scheduled':
                                        echo 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'in_progress':
                                        echo 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'completed':
                                        echo 'bg-green-100 text-green-800';
                                        break;
                                    case 'cancelled':
                                        echo 'bg-red-100 text-red-800';
                                        break;
                                }
                                ?>">
                                <?php echo ucfirst($consultation['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="#" class="text-green-600 hover:text-green-900">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
