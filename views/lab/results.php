<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Record Test Results</h1>
        <a href="/KJ/lab/tests" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
            <i class="fas fa-arrow-left mr-2"></i>Back to Tests
        </a>
    </div>

    <!-- Pending Results -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Pending Test Results</h3>
        </div>
        <div class="p-6">
            <?php if (empty($pending_results)): ?>
            <div class="text-center py-8">
                <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
                <p class="text-gray-500">No pending test results to record</p>
                <p class="text-sm text-gray-400">All tests have been processed</p>
            </div>
            <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($pending_results as $result): ?>
                <div class="border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">
                                    <?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?>
                                </h4>
                                <p class="text-sm text-gray-600">
                                    Test: <?php echo htmlspecialchars($result['test_name']); ?>
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">
                            Pending Result
                        </span>
                    </div>

                    <form method="POST" action="/KJ/lab/record_result" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="test_id" value="<?php echo $result['id']; ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="result_value_<?php echo $result['id']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                                    Result Value
                                </label>
                                <input type="text" id="result_value_<?php echo $result['id']; ?>" name="result_value"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Enter result value">
                            </div>
                            <div>
                                <label for="normal_range_<?php echo $result['id']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                                    Normal Range
                                </label>
                                <input type="text" id="normal_range_<?php echo $result['id']; ?>" name="normal_range"
                                       value="<?php echo htmlspecialchars($result['normal_range']); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" readonly>
                            </div>
                        </div>

                        <div>
                            <label for="result_text_<?php echo $result['id']; ?>" class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Notes
                            </label>
                            <textarea id="result_text_<?php echo $result['id']; ?>" name="result_text" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Enter any additional notes or observations"></textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Skip
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                                Save Result
                            </button>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
