<?php
$pageTitle = "Radiology Results";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-x-ray text-purple-600 mr-2"></i>Radiology Results
        </h1>
        <a href="<?= $BASE_PATH ?>/doctor/radiology_results" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            <i class="fas fa-search mr-2"></i>Refresh
        </a>
    </div>

    <!-- Radiology Results Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Test Results</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Radiologist</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($results)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-4 opacity-30"></i>
                            <p class="text-lg font-medium">No radiology results found</p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($results as $result): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-purple-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars($result['registration_number']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($result['test_name']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                    <?php
                                    $priority = strtolower($result['priority'] ?? 'routine');
                                    switch ($priority) {
                                        case 'stat':
                                            echo 'bg-red-100 text-red-800';
                                            break;
                                        case 'urgent':
                                            echo 'bg-orange-100 text-orange-800';
                                            break;
                                        default:
                                            echo 'bg-blue-100 text-blue-800';
                                    }
                                    ?>">
                                    <?php echo ucfirst($priority); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php 
                                $radiologist = ($result['radiologist_first'] ?? '') . ' ' . ($result['radiologist_last'] ?? '');
                                echo htmlspecialchars(trim($radiologist)) ?: 'N/A';
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $result['completed_at'] ? date('M j, Y', strtotime($result['completed_at'])) : 'N/A'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button 
                                    class="view-radiology-details-btn text-blue-600 hover:text-blue-900 mr-3"
                                    data-result-id="<?php echo $result['id']; ?>"
                                    data-patient-id="<?php echo $result['patient_id']; ?>"
                                    data-patient-name="<?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?>"
                                    data-test-name="<?php echo htmlspecialchars($result['test_name']); ?>"
                                    data-findings="<?php echo htmlspecialchars($result['findings'] ?? ''); ?>"
                                    data-impression="<?php echo htmlspecialchars($result['impression'] ?? ''); ?>"
                                    data-recommendations="<?php echo htmlspecialchars($result['recommendations'] ?? ''); ?>"
                                    data-images-path="<?php echo htmlspecialchars($result['images_path'] ?? ''); ?>"
                                    data-completed-at="<?php echo $result['completed_at'] ? date('M j, Y H:i', strtotime($result['completed_at'])) : ''; ?>">
                                    <i class="fas fa-eye mr-1"></i> View
                                </button>
                                <button 
                                    class="send-to-action-btn text-green-600 hover:text-green-900"
                                    data-result-id="<?php echo $result['id']; ?>"
                                    data-patient-id="<?php echo $result['patient_id']; ?>"
                                    data-patient-name="<?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?>">
                                    <i class="fas fa-arrow-right mr-1"></i> Actions
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Radiology Result Details Modal -->
<div id="radiologyDetailsModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
    
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-xl shadow-2xl transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-x-ray text-purple-600 mr-3"></i>
                    Radiology Result Details
                </h3>
                <button type="button" class="close-details-modal text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Body -->
            <div class="px-6 py-4">
                <!-- Patient & Test Info -->
                <div class="bg-purple-50 p-4 rounded-lg mb-5">
                    <div class="flex items-center mb-2">
                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900" id="detailPatientName"></h4>
                            <p class="text-sm text-gray-600" id="detailTestName"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Result Details -->
                <div class="space-y-4 max-h-[60vh] overflow-y-auto">
                    <!-- Images Section - Eye Icon Button -->
                    <div id="detailImagesSection" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Radiology Images</label>
                        <button type="button" id="viewImageBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-eye mr-2"></i>View Full Image
                        </button>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Findings</label>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 min-h-[80px]">
                            <p id="detailFindings" class="text-sm text-gray-700"></p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Impression</label>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 min-h-[80px]">
                            <p id="detailImpression" class="text-sm text-gray-700"></p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recommendations</label>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 min-h-[80px]">
                            <p id="detailRecommendations" class="text-sm text-gray-700"></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                <button type="button" class="close-details-modal px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Close
                </button>
                <button type="button" id="printRadiologyBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Action Selection Modal -->
<div id="actionSelectionModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
    
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-xl shadow-2xl transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-directions text-green-600 mr-2"></i>
                    Next Steps for <span id="actionPatientName"></span>
                </h3>
                <button type="button" class="close-action-modal text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Body -->
            <div class="px-6 py-4">
                <p class="text-gray-700 mb-6">Select an action to take based on the radiology results:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Send to Lab -->
                    <button class="action-option p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition" data-action="lab">
                        <i class="fas fa-flask text-blue-600 text-2xl mb-2"></i>
                        <h4 class="font-semibold text-gray-900">Send to Lab</h4>
                        <p class="text-sm text-gray-600">Order additional lab tests</p>
                    </button>
                    
                    <!-- Send to Ward -->
                    <button class="action-option p-4 border-2 border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition" data-action="ward">
                        <i class="fas fa-hospital-user text-orange-600 text-2xl mb-2"></i>
                        <h4 class="font-semibold text-gray-900">Send to Ward</h4>
                        <p class="text-sm text-gray-600">Admit to IPD/Ward</p>
                    </button>
                    
                    <!-- Send to Services -->
                    <button class="action-option p-4 border-2 border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition" data-action="services">
                        <i class="fas fa-cogs text-purple-600 text-2xl mb-2"></i>
                        <h4 class="font-semibold text-gray-900">Send to Services</h4>
                        <p class="text-sm text-gray-600">Order additional services</p>
                    </button>
                    
                    <!-- Prescribe Medicine -->
                    <button class="action-option p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition" data-action="medicine">
                        <i class="fas fa-prescription-bottle text-green-600 text-2xl mb-2"></i>
                        <h4 class="font-semibold text-gray-900">Prescribe Medicine</h4>
                        <p class="text-sm text-gray-600">Write prescription for patient</p>
                    </button>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end">
                <button type="button" class="close-action-modal px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Image Modal - Click anywhere to close -->
<div id="fullscreenImageModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-95 cursor-pointer" onclick="this.classList.add('hidden');">
    <div class="w-full h-full flex flex-col items-center justify-center p-4">
        <!-- Click to close hint -->
        <p class="text-white text-lg mb-4 opacity-75"><i class="fas fa-times-circle mr-2"></i>Click anywhere to close</p>
        
        <!-- Image -->
        <img id="fullscreenImage" src="" alt="Radiology Image" class="max-w-full max-h-[85vh] object-contain rounded shadow-2xl">
    </div>
</div>

<script>
// Get the BASE_PATH for constructing correct URLs
const BASE_PATH = '<?php echo $BASE_PATH; ?>';

document.addEventListener('DOMContentLoaded', function() {
    // Store current image path for fullscreen viewing
    let currentImagePath = '';
    
    // View Details Button
    document.querySelectorAll('.view-radiology-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = document.getElementById('radiologyDetailsModal');
            document.getElementById('detailPatientName').textContent = this.dataset.patientName;
            document.getElementById('detailTestName').textContent = this.dataset.testName;
            document.getElementById('detailFindings').textContent = this.dataset.findings || 'No findings recorded';
            document.getElementById('detailImpression').textContent = this.dataset.impression || 'No impression recorded';
            document.getElementById('detailRecommendations').textContent = this.dataset.recommendations || 'No recommendations';
            
            // Handle images
            const imagesPath = this.dataset.imagesPath;
            const imagesSection = document.getElementById('detailImagesSection');
            if (imagesPath && imagesPath.trim()) {
                currentImagePath = BASE_PATH + '/' + imagesPath;
                imagesSection.classList.remove('hidden');
            } else {
                currentImagePath = '';
                imagesSection.classList.add('hidden');
            }
            
            modal.classList.remove('hidden');
        });
    });
    
    // View Full Image Button
    document.getElementById('viewImageBtn').addEventListener('click', function(e) {
        e.preventDefault();
        if (currentImagePath) {
            const fullscreenModal = document.getElementById('fullscreenImageModal');
            document.getElementById('fullscreenImage').src = currentImagePath;
            fullscreenModal.classList.remove('hidden');
        }
    });
    
    // Close Fullscreen Image Modal
    // Close Details Modal
    document.querySelectorAll('.close-details-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('radiologyDetailsModal').classList.add('hidden');
        });
    });
    
    // Send to Action Button
    document.querySelectorAll('.send-to-action-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = document.getElementById('actionSelectionModal');
            document.getElementById('actionPatientName').textContent = this.dataset.patientName;
            modal.dataset.patientId = this.dataset.patientId;
            modal.classList.remove('hidden');
        });
    });
    
    // Close Action Modal
    document.querySelectorAll('.close-action-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('actionSelectionModal').classList.add('hidden');
        });
    });
    
    // Action Options
    document.querySelectorAll('.action-option').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            const patientId = document.getElementById('actionSelectionModal').dataset.patientId;
            
            // Map action buttons to attend_patient with pre-selected section
            const actions = {
                'lab': () => window.location.href = `<?php echo $BASE_PATH; ?>/doctor/attend_patient/${patientId}?action=lab_tests`,
                'ward': () => window.location.href = `<?php echo $BASE_PATH; ?>/doctor/attend_patient/${patientId}?action=ipd`,
                'services': () => window.location.href = `<?php echo $BASE_PATH; ?>/doctor/attend_patient/${patientId}?action=allocation`,
                'medicine': () => window.location.href = `<?php echo $BASE_PATH; ?>/doctor/attend_patient/${patientId}?action=medicine`
            };
            
            if (actions[action]) {
                actions[action]();
            }
        });
    });
    
    // Print Radiology Result
    document.getElementById('printRadiologyBtn').addEventListener('click', function() {
        window.print();
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
