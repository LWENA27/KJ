<?php
$pageTitle = "Start Radiology Test";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <!-- Payment Required Modal -->
    <?php if (isset($access_check) && !$access_check['access']): ?>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="bg-red-50 border-b border-red-200 px-6 py-4">
                <h2 class="text-xl font-bold text-red-800">Payment Required</h2>
            </div>
            
            <div class="px-6 py-4 space-y-4">
                <p class="text-gray-700">
                    This patient has not paid for the radiology test.
                </p>
                
                <!-- Patient Information -->
                <div class="bg-gray-50 rounded p-3 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Patient:</span>
                        <span class="font-semibold"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Registration:</span>
                        <span class="font-semibold"><?php echo htmlspecialchars($order['registration_number']); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Test:</span>
                        <span class="font-semibold"><?php echo htmlspecialchars($order['test_name']); ?></span>
                    </div>
                    <?php if (isset($visit)): ?>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Visit Date:</span>
                        <span class="font-semibold"><?php echo date('M d, Y', strtotime($visit['visit_date'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Payment Status -->
                <div class="border-l-4 border-red-500 bg-red-50 p-3">
                    <p class="text-sm text-red-700">
                        <strong>Status:</strong> Not Paid
                    </p>
                </div>
                
                <!-- Workflow Progress -->
                <div class="space-y-2">
                    <p class="text-sm font-semibold text-gray-700">Workflow Progress:</p>
                    <div class="text-sm space-y-1">
                        <div class="flex items-center">
                            <span class="text-red-600 font-bold mr-2">✗</span>
                            <span class="text-gray-600">Radiology Payment - <span class="text-red-600">Locked</span></span>
                        </div>
                    </div>
                </div>
                
                <!-- Emergency Override Section -->
                <form method="POST" class="space-y-3 border-t pt-4">
                    <?php echo csrf_field(); ?>
                    
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-2">Proceed with Override?</p>
                        <p class="text-xs text-gray-500 mb-2">
                            <strong>Note:</strong> All overrides are logged for audit purposes
                        </p>
                        
                        <select name="override_reason" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                            <option value="">Select emergency reason...</option>
                            <option value="Emergency">Emergency Case</option>
                            <option value="Urgent">Urgent Medical Need</option>
                            <option value="Critical">Critical Patient Condition</option>
                            <option value="Insurance">Insurance Processing Delay</option>
                            <option value="Payment_Plan">Payment Plan Approved</option>
                            <option value="Other">Other Approved Reason</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-3 pt-2">
                        <a href="<?php echo BASE_PATH; ?>/radiologist/orders" class="flex-1 bg-gray-300 text-gray-700 py-2 rounded hover:bg-gray-400 text-center text-sm font-semibold">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 bg-red-600 text-white py-2 rounded hover:bg-red-700 text-sm font-semibold">
                            Proceed (Override)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Normal Test Start View (Payment Received) -->
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
                        <p class="text-sm text-gray-600">Registration</p>
                        <p class="font-semibold"><?php echo htmlspecialchars($order['registration_number']); ?></p>
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
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
