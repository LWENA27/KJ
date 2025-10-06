<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Test Details</h1>
        <div class="flex space-x-3">
            <a href="/KJ/lab/tests" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i>Back to Tests
            </a>
            <?php if ($test['status'] === 'pending'): ?>
            <button onclick="startTest(<?php echo $test['id']; ?>)"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-play mr-2"></i>Start Test
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Patient Workflow Status -->
    <div class="bg-blue-50 p-4 rounded-lg mb-6">
        <h4 class="font-medium text-blue-800 mb-2">Patient Workflow Status</h4>
        <div class="flex items-center space-x-8">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                    <i class="fas fa-check"></i>
                </div>
                <div class="ml-2">
                    <div class="text-sm font-medium">Reception</div>
                    <div class="text-xs text-gray-500">Completed</div>
                </div>
            </div>
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white">
                    <i class="fas fa-check"></i>
                </div>
                <div class="ml-2">
                    <div class="text-sm font-medium">Payment</div>
                    <div class="text-xs text-gray-500">Verified</div>
                </div>
            </div>
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white">
                    <i class="fas fa-flask"></i>
                </div>
                <div class="ml-2">
                    <div class="text-sm font-medium">Lab Test</div>
                    <div class="text-xs text-gray-500">In Progress</div>
                </div>
            </div>
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-white">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div class="ml-2">
                    <div class="text-sm font-medium">Results</div>
                    <div class="text-xs text-gray-500">Pending</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Information -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-flask mr-3 text-blue-600"></i>Test Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Test Name</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo htmlspecialchars($test['test_name']); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md"><?php echo htmlspecialchars($test['category']); ?></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                    <?php
                    switch ($test['status']) {
                        case 'pending':
                            echo 'bg-yellow-100 text-yellow-800';
                            break;
                        case 'processing':
                            echo 'bg-blue-100 text-blue-800';
                            break;
                        case 'completed':
                            echo 'bg-green-100 text-green-800';
                            break;
                        case 'reviewed':
                            echo 'bg-purple-100 text-purple-800';
                            break;
                    }
                    ?>">
                    <?php echo ucfirst($test['status']); ?>
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Patient</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    <?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Requested Date</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    <?php echo date('M j, Y H:i', strtotime($test['created_at'])); ?>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Normal Range</label>
                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                    <?php echo htmlspecialchars($test['normal_range'] ?? 'Not specified'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Test Progress -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-tasks mr-3 text-green-600"></i>Test Progress
        </h3>
        <div class="space-y-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-sm"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Test Ordered</p>
                    <p class="text-xs text-gray-500"><?php echo date('M j, Y H:i', strtotime($test['created_at'])); ?></p>
                </div>
            </div>

            <?php if ($test['sample_date']): ?>
            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-sm"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Sample Collected</p>
                    <p class="text-xs text-gray-500"><?php echo date('M j, Y H:i', strtotime($test['sample_date'])); ?></p>
                </div>
            </div>
            <?php else: ?>
            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-gray-600 text-sm"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Sample Collection</p>
                    <p class="text-xs text-gray-500">Pending</p>
                    <?php if ($test['status'] === 'pending'): ?>
                    <button onclick="collectSample(<?php echo $test['id']; ?>)"
                            class="mt-2 text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                        Mark as Collected
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($test['status'] === 'processing' || $test['status'] === 'completed' || $test['status'] === 'reviewed'): ?>
            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-sm"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Test Started</p>
                    <p class="text-xs text-gray-500">Processing in lab</p>
                </div>
            </div>
            <?php else: ?>
            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-gray-600 text-sm"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Test Processing</p>
                    <p class="text-xs text-gray-500">Waiting to start</p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($test['status'] === 'completed' || $test['status'] === 'reviewed'): ?>
            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-sm"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Results Recorded</p>
                    <p class="text-xs text-gray-500"><?php echo $test['result_date'] ? date('M j, Y H:i', strtotime($test['result_date'])) : 'Date not available'; ?></p>
                </div>
            </div>
            <?php else: ?>
            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-gray-600 text-sm"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Results Recording</p>
                    <p class="text-xs text-gray-500">Pending</p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($test['status'] === 'reviewed'): ?>
            <div class="flex items-center">
                <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-sm"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Results Reviewed</p>
                    <p class="text-xs text-gray-500">By doctor</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Test Result Form -->
    <?php if ($test['status'] === 'processing' || $test['status'] === 'completed'): ?>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-edit mr-3 text-purple-600"></i>Record Test Results
        </h3>

        <?php if ($test['status'] === 'completed'): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                <div>
                    <h4 class="text-sm font-medium text-green-800">Results Already Recorded</h4>
                    <p class="text-sm text-green-700">Result: <?php echo htmlspecialchars($test['result_value'] ?? 'N/A'); ?></p>
                    <?php if (!empty($test['result_text'])): ?>
                    <p class="text-sm text-green-700">Notes: <?php echo htmlspecialchars($test['result_text']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <form method="POST" action="/KJ/lab/record_result" class="space-y-4" id="resultForm">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="result_value" class="block text-sm font-medium text-gray-700 mb-2">
                        Result Value <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="result_value" name="result_value" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Enter the test result value">
                </div>
                <div>
                    <label for="normal_range" class="block text-sm font-medium text-gray-700 mb-2">
                        Normal Range
                    </label>
                    <input type="text" id="normal_range" name="normal_range"
                           value="<?php echo htmlspecialchars($test['normal_range'] ?? ''); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                </div>
            </div>

            <div>
                <label for="result_text" class="block text-sm font-medium text-gray-700 mb-2">
                    Additional Notes
                </label>
                <textarea id="result_text" name="result_text" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                          placeholder="Enter any additional observations, methodology, or notes about the test"></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="window.history.back()"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" id="submitBtn"
                        class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save mr-2"></i>Save Result
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function startTest(testId) {
    if (confirm('Are you sure you want to start processing this test?')) {
        fetch('/KJ/lab/start_test', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'csrf_token=<?php echo urlencode($csrf_token); ?>&test_id=' + testId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to start test'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while starting the test');
        });
    }
}

function collectSample(testId) {
    if (confirm('Mark sample as collected?')) {
        fetch('/KJ/lab/collect_sample', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'csrf_token=<?php echo urlencode($csrf_token); ?>&test_id=' + testId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to collect sample'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while collecting sample');
        });
    }
}

// Form validation and submission
document.getElementById('resultForm').addEventListener('submit', function(e) {
    const resultValue = document.getElementById('result_value').value.trim();
    const submitBtn = document.getElementById('submitBtn');

    if (!resultValue) {
        e.preventDefault();
        alert('Please enter a result value');
        return false;
    }

    // Disable button to prevent double submission
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
});
</script>
