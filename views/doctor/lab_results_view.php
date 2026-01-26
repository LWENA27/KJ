<?php
// Display single patient's lab results
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Lab Results</h1>
            <?php if ($patient): ?>
            <p class="text-gray-600 mt-2">Patient: <strong><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></strong></p>
            <?php endif; ?>
        </div>
        <a href="<?= $BASE_PATH ?>/doctor/lab_results" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            <i class="fas fa-arrow-left mr-2"></i>Back to Results
        </a>
    </div>

    <!-- Lab Results Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Test Results for <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Normal Range</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($lab_results)): ?>
                        <?php foreach ($lab_results as $result): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($result['test_name']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php if ($result['result_value']): ?>
                                    <span class="font-medium"><?php echo htmlspecialchars($result['result_value']); ?></span>
                                    <?php if ($result['result_text']): ?>
                                        <br><span class="text-xs text-gray-600"><?php echo htmlspecialchars($result['result_text']); ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-500">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?php echo htmlspecialchars($result['unit'] ?? 'N/A'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?php echo htmlspecialchars($result['normal_range'] ?? 'N/A'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo safe_date('M j, Y H:i', $result['completed_at'], 'N/A'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    <?php
                                    switch ($result['status'] ?? 'pending') {
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
                                    <?php echo ucfirst($result['status'] ?? 'pending'); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button 
                                    data-test-id="<?php echo $result['id']; ?>"
                                    data-patient-id="<?php echo $result['patient_id'] ?? $patient['id']; ?>"
                                    data-test-name="<?php echo htmlspecialchars($result['test_name']); ?>"
                                    data-result-value="<?php echo htmlspecialchars($result['result_value'] ?? ''); ?>"
                                    data-result-text="<?php echo htmlspecialchars($result['result_text'] ?? ''); ?>"
                                    data-result-unit="<?php echo htmlspecialchars($result['unit'] ?? ''); ?>"
                                    data-completed-at="<?php echo $result['completed_at'] ? date('M j, Y H:i', strtotime($result['completed_at'])) : ''; ?>"
                                    class="view-details-btn text-blue-600 hover:text-blue-900 focus:outline-none">
                                    <i class="fas fa-eye mr-1"></i> View Details
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No completed lab results found for this patient.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900" id="modalTestName"></h3>
                <p class="text-sm text-gray-600" id="modalTestDate"></p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Result Value</label>
                    <p class="text-lg font-semibold text-gray-900" id="modalResultValue"></p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Unit</label>
                    <p class="text-lg text-gray-900" id="modalResultUnit"></p>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-500 uppercase">Normal Range</label>
                    <p class="text-sm text-gray-900" id="modalNormalRange"></p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase">Notes</label>
                <p class="text-sm text-gray-900" id="modalResultText"></p>
            </div>

            <div class="pt-4 border-t">
                <p class="text-xs text-gray-500">Status: <span id="modalStatus" class="font-semibold"></span></p>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <button onclick="closeModal()" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                Close
            </button>
        </div>
    </div>
</div>

<script>
// View details button handler
document.querySelectorAll('.view-details-btn').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('modalTestName').textContent = this.dataset.testName;
        document.getElementById('modalResultValue').textContent = this.dataset.resultValue || 'N/A';
        document.getElementById('modalResultUnit').textContent = this.dataset.resultUnit || 'N/A';
        document.getElementById('modalNormalRange').textContent = this.dataset.normalRange || 'N/A';
        document.getElementById('modalResultText').textContent = this.dataset.resultText || 'No additional notes';
        document.getElementById('modalTestDate').textContent = this.dataset.completedAt || 'Date N/A';
        document.getElementById('modalStatus').textContent = this.dataset.status || 'pending';
        
        document.getElementById('detailsModal').classList.remove('hidden');
    });
});

// Close modal
function closeModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('detailsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
