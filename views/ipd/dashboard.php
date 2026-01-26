<?php
$pageTitle = "IPD Dashboard";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">IPD Dashboard</h1>
        <div class="space-x-2">
            <a href="<?php echo BASE_PATH; ?>/ipd/beds" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Bed Management</a>
            <a href="<?php echo BASE_PATH; ?>/ipd/admissions" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">View Admissions</a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Active Admissions</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $active_admissions; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Today's Admissions</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo count($todays_admissions); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Pending Medications</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $pending_medications; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ward Occupancy -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Ward Occupancy</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($wards as $ward): ?>
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-bold text-gray-800"><?php echo htmlspecialchars($ward['ward_name']); ?></h3>
                                <p class="text-sm text-gray-600"><?php echo ucfirst($ward['ward_type']); ?> Ward</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full
                                <?php 
                                $occupancy_rate = $ward['total_beds'] > 0 ? ($ward['occupied_beds'] / $ward['total_beds']) * 100 : 0;
                                echo $occupancy_rate > 80 ? 'bg-red-100 text-red-800' : 
                                    ($occupancy_rate > 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800');
                                ?>">
                                <?php echo round($occupancy_rate); ?>%
                            </span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Beds:</span>
                                <span class="font-semibold"><?php echo $ward['total_beds']; ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Occupied:</span>
                                <span class="font-semibold text-red-600"><?php echo $ward['occupied_beds']; ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Available:</span>
                                <span class="font-semibold text-green-600"><?php echo $ward['available_beds']; ?></span>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="<?php echo BASE_PATH; ?>/ipd/beds?ward_id=<?php echo $ward['id']; ?>" 
                               class="text-sm text-blue-600 hover:text-blue-800">View Beds →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Today's Admissions -->
    <?php if (!empty($todays_admissions)): ?>
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-800">Today's Admissions</h2>
        </div>
        <div class="p-6">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 text-sm font-medium text-gray-600">Admission #</th>
                        <th class="text-left py-2 text-sm font-medium text-gray-600">Patient</th>
                        <th class="text-left py-2 text-sm font-medium text-gray-600">Ward/Bed</th>
                        <th class="text-left py-2 text-sm font-medium text-gray-600">Type</th>
                        <th class="text-left py-2 text-sm font-medium text-gray-600">Time</th>
                        <th class="text-left py-2 text-sm font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($todays_admissions as $admission): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 text-sm"><?php echo htmlspecialchars($admission['admission_number']); ?></td>
                            <td class="py-3">
                                <div class="text-sm font-medium"><?php echo htmlspecialchars($admission['first_name'] . ' ' . $admission['last_name']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($admission['patient_number']); ?></div>
                            </td>
                            <td class="py-3 text-sm"><?php echo htmlspecialchars($admission['ward_name'] . ' - ' . $admission['bed_number']); ?></td>
                            <td class="py-3">
                                <span class="px-2 py-1 text-xs rounded-full
                                    <?php echo $admission['admission_type'] === 'emergency' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'; ?>">
                                    <?php echo ucfirst($admission['admission_type']); ?>
                                </span>
                            </td>
                            <td class="py-3 text-sm"><?php echo date('g:i A', strtotime($admission['admission_datetime'])); ?></td>
                            <td class="py-3">
                                <a href="<?php echo BASE_PATH; ?>/ipd/view_admission/<?php echo $admission['id']; ?>" 
                                   class="text-sm text-blue-600 hover:text-blue-800">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
