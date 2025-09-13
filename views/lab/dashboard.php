<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Lab Technician Dashboard</h1>
        <div class="text-sm text-gray-500">
            <?php echo date('l, F j, Y'); ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-flask text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Tests</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_tests']; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Tests</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['pending_tests']; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed Tests</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['completed_tests']; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-calendar-day text-purple-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed Today</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $completed_today; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Queue -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Pending Test Queue</h3>
        </div>
        <div class="p-6">
            <?php if (empty($pending_tests)): ?>
            <div class="text-center py-8">
                <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
                <p class="text-gray-500">No pending tests in queue</p>
                <p class="text-sm text-gray-400">All tests have been processed</p>
            </div>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($pending_tests as $test): ?>
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-flask text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">
                                <?php echo htmlspecialchars($test['test_name']); ?>
                            </p>
                            <p class="text-sm text-gray-600">
                                Patient: <?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?>
                            </p>
                            <p class="text-xs text-gray-400">
                                Requested: <?php echo date('M j, Y H:i', strtotime($test['created_at'])); ?>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                        <div class="mt-2">
                            <a href="/KJ/lab/view_test/<?php echo $test['id']; ?>"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium mr-3">
                                View Details →
                            </a>
                            <a href="/KJ/lab/results" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                Record Result →
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="/KJ/lab/tests" class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg text-center transition duration-200">
            <i class="fas fa-list text-2xl mb-2"></i>
            <div class="font-medium">View All Tests</div>
        </a>
        <a href="/KJ/lab/results" class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg text-center transition duration-200">
            <i class="fas fa-edit text-2xl mb-2"></i>
            <div class="font-medium">Record Results</div>
        </a>
        <a href="/KJ/lab/dashboard" class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-lg text-center transition duration-200">
            <i class="fas fa-chart-bar text-2xl mb-2"></i>
            <div class="font-medium">View Statistics</div>
        </a>
    </div>
</div>
