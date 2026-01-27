<?php
$pageTitle = "View Radiology Result";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Radiology Result</h1>
        <div class="space-x-2">
            <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Print Report
            </button>
            <a href="<?php echo BASE_PATH; ?>/radiologist/orders" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Back to Orders
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <!-- Header -->
        <div class="text-center mb-8 border-b pb-6">
            <h2 class="text-2xl font-bold text-gray-800">RADIOLOGY REPORT</h2>
            <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($result['test_name'] ?? ''); ?></p>
        </div>

        <!-- Patient Info -->
        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Patient Information</h3>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-2 text-gray-600">Name:</td>
                        <td class="py-2 font-semibold"><?php echo htmlspecialchars(($result['first_name'] ?? '') . ' ' . ($result['last_name'] ?? '')); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-600">Patient Number:</td>
                        <td class="py-2 font-semibold"><?php echo htmlspecialchars($result['patient_number'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-600">Age / Gender:</td>
                        <td class="py-2 font-semibold"><?php echo ($result['age'] ?? ''); ?> years / <?php echo ucfirst($result['gender'] ?? ''); ?></td>
                    </tr>
                </table>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Test Information</h3>
                <table class="w-full text-sm">
                    <tr>
                        <td class="py-2 text-gray-600">Test Code:</td>
                        <td class="py-2 font-semibold"><?php echo htmlspecialchars($result['test_code'] ?? ''); ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-600">Completed:</td>
                        <td class="py-2 font-semibold"><?php echo isset($result['completed_at']) && $result['completed_at'] ? date('M d, Y g:i A', strtotime($result['completed_at'])) : ''; ?></td>
                    </tr>
                    <tr>
                        <td class="py-2 text-gray-600">Radiologist:</td>
                        <td class="py-2 font-semibold">Dr. <?php echo htmlspecialchars(($result['radiologist_first_name'] ?? '') . ' ' . ($result['radiologist_last_name'] ?? '')); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Results Status Badges -->
        <div class="flex space-x-4 mb-6">
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                <?php echo (!empty($result['is_normal'])) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                <?php echo (!empty($result['is_normal'])) ? '✓ Normal' : '⚠ Abnormal'; ?>
            </span>
            <?php if (!empty($result['is_critical'])): ?>
                <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                    ⚠ CRITICAL FINDING
                </span>
            <?php endif; ?>
        </div>

        <!-- Findings -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">FINDINGS</h3>
            <div class="bg-gray-50 rounded p-4">
                <p class="text-gray-800 whitespace-pre-line"><?php echo htmlspecialchars($result['findings'] ?? ''); ?></p>
            </div>
        </div>

        <!-- Impression -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">IMPRESSION</h3>
            <div class="bg-gray-50 rounded p-4">
                <p class="text-gray-800 whitespace-pre-line"><?php echo htmlspecialchars($result['impression'] ?? ''); ?></p>
            </div>
        </div>

        <!-- Recommendations -->
    <?php if (!empty($result['recommendations'])): ?>
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">RECOMMENDATIONS</h3>
            <div class="bg-gray-50 rounded p-4">
                <p class="text-gray-800 whitespace-pre-line"><?php echo htmlspecialchars($result['recommendations'] ?? ''); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Images -->
    <?php if (!empty($result['images_path'])): ?>
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">IMAGES</h3>
            <div class="bg-gray-50 rounded p-4">
                <img src="<?php echo htmlspecialchars($BASE_PATH . '/' . ($result['images_path'] ?? '')); ?>" 
                     alt="Radiology Image" 
                     class="max-w-full h-auto rounded border cursor-pointer"
                     onclick="this.classList.toggle('max-w-full'); this.classList.toggle('w-auto');">
            </div>
        </div>
        <?php endif; ?>

        <!-- Clinical Notes -->
    <?php if (!empty($result['clinical_notes'])): ?>
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">CLINICAL NOTES</h3>
            <div class="bg-blue-50 rounded p-4">
                <p class="text-gray-700 text-sm whitespace-pre-line"><?php echo htmlspecialchars($result['clinical_notes'] ?? ''); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Signature -->
        <div class="mt-8 pt-6 border-t">
            <div class="text-right">
                <p class="font-semibold text-gray-800">
                    Dr. <?php echo htmlspecialchars(($result['radiologist_first_name'] ?? '') . ' ' . ($result['radiologist_last_name'] ?? '')); ?>
                </p>
                <p class="text-sm text-gray-600">Radiologist</p>
                <p class="text-xs text-gray-500 mt-2">
                    Report generated: <?php echo isset($result['completed_at']) && $result['completed_at'] ? date('M d, Y g:i A', strtotime($result['completed_at'])) : ''; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
