<?php
$pageTitle = "Start Radiology Test";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Start Test Procedure</h1>
            
            <div class="space-y-4 mb-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Patient</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Patient Number</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($order['patient_number'] ?? ''); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Test</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($order['test_name']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Priority</p>
                        <p class="font-semibold text-<?php echo $order['priority'] === 'stat' ? 'red' : ($order['priority'] === 'urgent' ? 'orange' : 'gray'); ?>-600">
                            <?php echo strtoupper($order['priority']); ?>
                        </p>
                    </div>
                </div>
                
                <?php if (!empty($order['preparation_instructions'])): ?>
                <div class="bg-blue-50 border border-blue-200 rounded p-4">
                    <p class="text-sm font-semibold text-blue-800 mb-2">Preparation Instructions:</p>
                    <p class="text-sm text-blue-700"><?php echo nl2br(htmlspecialchars($order['preparation_instructions'] ?? '')); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($order['requires_contrast'])): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                    <p class="text-sm font-semibold text-yellow-800">⚠ This test requires contrast</p>
                </div>
                <?php endif; ?>
            </div>
            
            <form method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                
                <div class="bg-gray-50 rounded p-4">
                    <p class="text-sm text-gray-700">
                        By clicking "Start Test", you confirm that:
                    </p>
                    <ul class="list-disc list-inside text-sm text-gray-600 mt-2 space-y-1">
                        <li>Patient identity has been verified</li>
                        <li>Patient preparation is adequate</li>
                        <li>Equipment is ready and calibrated</li>
                        <li>Safety protocols have been followed</li>
                    </ul>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="<?php echo BASE_PATH; ?>/radiologist/orders" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        Start Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
