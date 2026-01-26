<?php
$pageTitle = "Radiology Dashboard";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Radiology Dashboard</h1>
        <a href="<?php echo BASE_PATH; ?>/radiologist/orders" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            View All Orders
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Pending Orders</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $pending_count; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">In Progress</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo count($in_progress); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Today Completed</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['today_completed'] ?? 0; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">This Week</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['week_completed'] ?? 0; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's Schedule -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold text-gray-800">Today's Schedule</h2>
            </div>
            <div class="p-6">
                <?php if (empty($todays_schedule)): ?>
                    <p class="text-gray-500 text-center py-4">No tests scheduled for today</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($todays_schedule as $test): ?>
                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            <?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?>
                                        </p>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($test['test_name']); ?></p>
                                        <p class="text-xs text-gray-500">
                                            Patient #: <?php echo htmlspecialchars($test['patient_number'] ?? ''); ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-800">
                                            <?php echo date('g:i A', strtotime($test['scheduled_datetime'])); ?>
                                        </p>
                                        <span class="inline-block px-2 py-1 text-xs rounded-full
                                            <?php 
                                            echo $test['priority'] === 'stat' ? 'bg-red-100 text-red-800' : 
                                                ($test['priority'] === 'urgent' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800');
                                            ?>">
                                            <?php echo strtoupper($test['priority']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- In Progress Tests -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold text-gray-800">In Progress</h2>
            </div>
            <div class="p-6">
                <?php if (empty($in_progress)): ?>
                    <p class="text-gray-500 text-center py-4">No tests in progress</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($in_progress as $test): ?>
                            <div class="border-l-4 border-yellow-500 pl-4 py-2">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            <?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?>
                                        </p>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($test['test_name']); ?></p>
                                        <p class="text-xs text-gray-500">
                                            Patient #: <?php echo htmlspecialchars($test['patient_number'] ?? ''); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <a href="<?php echo BASE_PATH; ?>/radiologist/record_result/<?php echo $test['id']; ?>" 
                                           class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                            Complete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
