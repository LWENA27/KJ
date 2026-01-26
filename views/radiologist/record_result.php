<?php
$pageTitle = "Record Radiology Result";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Record Test Result</h1>
        <a href="<?php echo BASE_PATH; ?>/radiologist/orders" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            Back to Orders
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Patient Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Patient Information</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Patient Name</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Patient Number</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($order['patient_number'] ?? ''); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Age / Gender</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($order['age'] ?? ''); ?> years / <?php echo htmlspecialchars(ucfirst($order['gender'] ?? '')); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Test</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($order['test_name']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Test Code</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($order['test_code']); ?></p>
                    </div>
                    <?php if (!empty($order['clinical_notes'])): ?>
                    <div>
                        <p class="text-sm text-gray-600">Clinical Notes</p>
                        <p class="text-sm"><?php echo nl2br(htmlspecialchars($order['clinical_notes'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Result Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Test Results</h2>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Findings <span class="text-red-500">*</span>
                        </label>
                        <textarea name="findings" rows="5" required
                                  class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Describe detailed findings..."><?php echo htmlspecialchars($existing_result['findings'] ?? ''); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Impression <span class="text-red-500">*</span>
                        </label>
                        <textarea name="impression" rows="4" required
                                  class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Summarize clinical impression..."><?php echo htmlspecialchars($existing_result['impression'] ?? ''); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Recommendations
                        </label>
                        <textarea name="recommendations" rows="3"
                                  class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Any follow-up recommendations..."><?php echo htmlspecialchars($existing_result['recommendations'] ?? ''); ?></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_normal" value="1" 
                                       <?php echo ($existing_result['is_normal'] ?? 1) ? 'checked' : ''; ?>
                                       class="rounded border-gray-300 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">Normal Result</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_critical" value="1"
                                       <?php echo ($existing_result['is_critical'] ?? 0) ? 'checked' : ''; ?>
                                       class="rounded border-gray-300 text-red-600">
                                <span class="ml-2 text-sm text-gray-700">Critical Finding</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Upload Images (Optional)
                        </label>
                        <input type="file" name="images" accept="image/*,.dcm"
                               class="w-full border border-gray-300 rounded px-3 py-2">
                        <p class="text-xs text-gray-500 mt-1">Accepted: JPG, PNG, DICOM files</p>
                        <?php if (!empty($existing_result['images_path'])): ?>
                            <p class="text-sm text-green-600 mt-2">✓ Images already uploaded</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Radiologist Notes (Internal)
                        </label>
                        <textarea name="radiologist_notes" rows="2"
                                  class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Internal notes..."><?php echo htmlspecialchars($existing_result['radiologist_notes'] ?? ''); ?></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="<?php echo BASE_PATH; ?>/radiologist/orders" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                            Cancel
                        </a>
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                            <?php echo $existing_result ? 'Update Result' : 'Save Result'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
