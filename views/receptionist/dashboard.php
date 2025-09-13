<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Receptionist Dashboard</h1>
        <div class="text-sm text-gray-500">
            <?php echo date('l, F j, Y'); ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="/KJ/receptionist/patients" class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg text-center transition duration-200">
            <i class="fas fa-user-plus text-2xl mb-2"></i>
            <div class="font-medium">Register Patient</div>
        </a>
        <a href="/KJ/receptionist/appointments" class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg text-center transition duration-200">
            <i class="fas fa-calendar-plus text-2xl mb-2"></i>
            <div class="font-medium">New Appointment</div>
        </a>
        <a href="/KJ/receptionist/payments" class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-lg text-center transition duration-200">
            <i class="fas fa-credit-card text-2xl mb-2"></i>
            <div class="font-medium">Process Payment</div>
        </a>
        <a href="/KJ/receptionist/patients" class="bg-yellow-500 hover:bg-yellow-600 text-white p-4 rounded-lg text-center transition duration-200">
            <i class="fas fa-search text-2xl mb-2"></i>
            <div class="font-medium">Search Patient</div>
        </a>
    </div>

    <!-- Today's Appointments -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Today's Appointments</h3>
        </div>
        <div class="p-6">
            <?php if (empty($appointments)): ?>
            <p class="text-gray-500 text-center py-4">No appointments scheduled for today</p>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($appointments as $appointment): ?>
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">
                                <?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                Dr. <?php echo htmlspecialchars($appointment['doctor_first'] . ' ' . $appointment['doctor_last']); ?>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">
                            <?php echo date('H:i', strtotime($appointment['appointment_date'])); ?>
                        </p>
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                            <?php
                            switch ($appointment['status']) {
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
                            <?php echo ucfirst($appointment['status']); ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Patients -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Patients</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <?php foreach ($recent_patients as $patient): ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">
                                <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                            </p>
                            <p class="text-xs text-gray-600"><?php echo htmlspecialchars($patient['phone']); ?></p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400">
                        <?php echo date('M j, Y', strtotime($patient['created_at'])); ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
