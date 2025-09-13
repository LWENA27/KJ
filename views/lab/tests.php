<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Test Queue</h1>
        <a href="/KJ/lab/tests" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            <i class="fas fa-refresh mr-2"></i>Refresh
        </a>
    </div>

    <!-- Test Queue Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Pending & Completed Tests</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($tests as $test): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($test['appointment_date'] ? date('M j, Y', strtotime($test['appointment_date'])) : 'N/A'); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="font-medium"><?php echo htmlspecialchars($test['test_name']); ?></div>
                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($test['category']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                <?php
                                switch ($test['status']) {
                                    case 'pending':
                                        echo 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'completed':
                                        echo 'bg-green-100 text-green-800';
                                        break;
                                    case 'reviewed':
                                        echo 'bg-blue-100 text-blue-800';
                                        break;
                                }
                                ?>">
                                <?php echo ucfirst($test['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo date('M j, Y H:i', strtotime($test['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <?php if ($test['status'] === 'pending'): ?>
                            <a href="/KJ/lab/view_test/<?php echo $test['id']; ?>"
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <button onclick="openCompleteTestModal(<?php echo $test['id']; ?>, '<?php echo htmlspecialchars($test['test_name']); ?>', '<?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?>')"
                                    class="text-green-600 hover:text-green-900 mr-3">
                                <i class="fas fa-check mr-1"></i>Complete
                            </button>
                            <?php elseif ($test['status'] === 'processing'): ?>
                            <a href="/KJ/lab/view_test/<?php echo $test['id']; ?>"
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <button onclick="openCompleteTestModal(<?php echo $test['id']; ?>, '<?php echo htmlspecialchars($test['test_name']); ?>', '<?php echo htmlspecialchars($test['first_name'] . ' ' . $test['last_name']); ?>')"
                                    class="text-green-600 hover:text-green-900 mr-3">
                                <i class="fas fa-check mr-1"></i>Record Result
                            </button>
                            <?php else: ?>
                            <a href="/KJ/lab/view_test/<?php echo $test['id']; ?>"
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye mr-1"></i>View Result
                            </a>
                            <span class="text-gray-500">Completed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Complete Test Modal -->
<div id="completeTestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Complete Test</h3>
                <button onclick="closeCompleteTestModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="completeTestForm" action="/KJ/lab/complete_test" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="test_id" id="modalTestId">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Patient</label>
                    <p id="modalPatientName" class="mt-1 text-sm text-gray-900"></p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Test</label>
                    <p id="modalTestName" class="mt-1 text-sm text-gray-900"></p>
                </div>

                <div class="mb-4">
                    <label for="result_value" class="block text-sm font-medium text-gray-700">Result Value</label>
                    <input type="text" name="result_value" id="result_value" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label for="result_text" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                    <textarea name="result_text" id="result_text" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCompleteTestModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                        Complete Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCompleteTestModal(testId, testName, patientName) {
    document.getElementById('modalTestId').value = testId;
    document.getElementById('modalTestName').textContent = testName;
    document.getElementById('modalPatientName').textContent = patientName;
    document.getElementById('completeTestModal').classList.remove('hidden');
}

function closeCompleteTestModal() {
    document.getElementById('completeTestModal').classList.add('hidden');
    document.getElementById('completeTestForm').reset();
}
</script>
