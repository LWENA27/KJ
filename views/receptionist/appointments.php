<!-- Page Header with Professional Gradient -->
<div class="bg-gradient-to-r from-green-600 via-green-700 to-emerald-800 rounded-lg shadow-xl p-6 mb-6">
    <div class="flex items-center justify-between">
        <div class="text-white">
            <h1 class="text-3xl font-bold flex items-center">
                <i class="fas fa-calendar-check mr-3 text-green-200"></i>
                Appointments Management
            </h1>
            <p class="text-green-100 mt-2 text-lg">Schedule and manage patient appointments</p>
        </div>
        <a href="/KJ/receptionist/appointments" class="bg-white text-green-700 hover:bg-green-50 px-6 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
            <i class="fas fa-plus mr-2"></i>New Appointment
        </a>
    </div>
</div>

<!-- Quick Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <?php
    $totalAppointments = count($appointments);
    $todayAppointments = count(array_filter($appointments, fn($a) => date('Y-m-d', strtotime($a['appointment_date'] ?? $a['visit_date'] ?? $a['created_at'])) === date('Y-m-d')));
    $scheduledAppointments = count(array_filter($appointments, fn($a) => $a['status'] === 'scheduled'));
    $completedAppointments = count(array_filter($appointments, fn($a) => $a['status'] === 'completed'));
    
    $cards = [
        ['label' => 'Total Appointments', 'count' => $totalAppointments, 'color' => 'from-blue-500 to-blue-600', 'icon' => 'fas fa-calendar-alt'],
        ['label' => 'Today\'s Appointments', 'count' => $todayAppointments, 'color' => 'from-green-500 to-green-600', 'icon' => 'fas fa-calendar-day'],
        ['label' => 'Scheduled', 'count' => $scheduledAppointments, 'color' => 'from-yellow-500 to-yellow-600', 'icon' => 'fas fa-clock'],
        ['label' => 'Completed', 'count' => $completedAppointments, 'color' => 'from-purple-500 to-purple-600', 'icon' => 'fas fa-check-circle']
    ];
    
    foreach ($cards as $card): ?>
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1"><?php echo $card['label']; ?></p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo $card['count']; ?></p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br <?php echo $card['color']; ?> rounded-xl flex items-center justify-center shadow-lg">
                    <i class="<?php echo $card['icon']; ?> text-white text-xl"></i>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Appointments Table with Professional Design -->
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-green-50">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                <i class="fas fa-list mr-3 text-green-600"></i>
                All Appointments
            </h3>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                <?php echo count($appointments); ?> appointments
            </span>
        </div>
    </div>
    
    <?php if (empty($appointments)): ?>
        <div class="p-12 text-center">
            <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center shadow-lg">
                <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">No appointments scheduled</h3>
            <p class="text-gray-600 mb-8 text-lg">Start by scheduling your first appointment</p>
            <a href="/KJ/receptionist/appointments" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-lg">
                <i class="fas fa-plus mr-2"></i>Schedule First Appointment
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($appointments as $appointment): ?>
                    <tr class="hover:bg-green-50 transition-all duration-300">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg mr-4">
                                    <?php echo strtoupper(substr($appointment['first_name'], 0, 1) . substr($appointment['last_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <i class="fas fa-id-badge mr-1 text-gray-400"></i>
                                        ID: #<?php echo str_pad($appointment['patient_id'], 4, '0', STR_PAD_LEFT); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-user-md text-blue-500 mr-2"></i>
                                <span class="text-sm font-medium text-gray-900">
                                    Dr. <?php echo htmlspecialchars($appointment['doctor_first'] . ' ' . $appointment['doctor_last']); ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <?php $apt = $appointment['appointment_date'] ?? $appointment['visit_date'] ?? $appointment['created_at']; ?>
                                <div class="font-medium"><?php echo date('M j, Y', strtotime($apt)); ?></div>
                                <div class="text-gray-600 flex items-center">
                                    <i class="fas fa-clock mr-1 text-gray-400"></i>
                                    <?php echo date('H:i', strtotime($apt)); ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium shadow-sm
                                <?php
                                switch ($appointment['status']) {
                                    case 'scheduled':
                                        echo 'bg-yellow-100 text-yellow-800 border border-yellow-300';
                                        $icon = 'fas fa-clock';
                                        break;
                                    case 'in_progress':
                                        echo 'bg-blue-100 text-blue-800 border border-blue-300';
                                        $icon = 'fas fa-play-circle';
                                        break;
                                    case 'completed':
                                        echo 'bg-green-100 text-green-800 border border-green-300';
                                        $icon = 'fas fa-check-circle';
                                        break;
                                    case 'cancelled':
                                        echo 'bg-red-100 text-red-800 border border-red-300';
                                        $icon = 'fas fa-times-circle';
                                        break;
                                    default:
                                        echo 'bg-gray-100 text-gray-800 border border-gray-300';
                                        $icon = 'fas fa-question-circle';
                                }
                                ?>">
                                <i class="<?php echo $icon; ?> mr-2"></i>
                                <?php echo ucfirst(str_replace('_', ' ', $appointment['status'])); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <a href="#" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105" title="Edit Appointment">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($appointment['status'] === 'scheduled'): ?>
                                <a href="#" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 rounded-lg text-sm font-medium transition-all duration-300 transform hover:scale-105" title="Cancel Appointment">
                                    <i class="fas fa-times"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Enhanced JavaScript for Appointments -->
<script>
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
    
    // Add loading states to action buttons
    document.querySelectorAll('a[class*="bg-"]').forEach(btn => {
        btn.addEventListener('click', function() {
            this.style.opacity = '0.7';
            setTimeout(() => {
                this.style.opacity = '1';
            }, 1000);
        });
    });
});
</script>
