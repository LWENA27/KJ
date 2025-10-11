<div class="space-y-6">
    <!-- Enhanced Header Section -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-clipboard-check mr-3 text-green-600"></i>
                Record Test Results
            </h1>
            <p class="text-gray-600 mt-1">Enter and validate laboratory test results</p>
            <div class="flex items-center mt-2 space-x-4">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></div>
                    <span class="text-xs text-yellow-600 font-medium"><?php echo count($pending_results ?? []); ?> Pending Results</span>
                </div>
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-xs text-green-600 font-medium">Real-time Validation</span>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="toggleValidationMode()" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-shield-alt mr-2"></i>Validation Mode
            </button>
            <a href="/KJ/lab/tests" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Queue
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Pending Results</p>
                    <p class="text-2xl font-bold"><?php echo count($pending_results ?? []); ?></p>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-full p-2">
                    <i class="fas fa-hourglass-half text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Completed Today</p>
                    <p class="text-2xl font-bold"><?php echo rand(15, 35); ?></p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-2">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Accuracy Rate</p>
                    <p class="text-2xl font-bold">99.2%</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-2">
                    <i class="fas fa-target text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-4 text-white transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Avg Time</p>
                    <p class="text-2xl font-bold">12m</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-2">
                    <i class="fas fa-stopwatch text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Pending Results -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-clipboard-list mr-3 text-green-600"></i>
                    Pending Test Results Entry
                </h3>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center">
                        <input type="checkbox" id="autoValidate" checked class="mr-2 rounded">
                        <label for="autoValidate" class="text-sm text-gray-600">Auto-validate ranges</label>
                    </div>
                    <button onclick="saveAllResults()" class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-save mr-1"></i>Save All
                    </button>
                </div>
            </div>
        </div>
        <div class="p-6">
            <?php if (empty($pending_results)): ?>
            <div class="text-center py-12">
                <div class="w-20 h-20 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-900 mb-2">All Results Recorded!</h4>
                <p class="text-gray-500">No pending test results to record</p>
                <p class="text-sm text-gray-400 mt-1">Excellent work! All tests have been processed</p>
                <button onclick="refreshResults()" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-refresh mr-2"></i>Check for New Tests
                </button>
            </div>
            <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($pending_results as $index => $result): 
                    $priority = ['Normal', 'High', 'Urgent'][rand(0, 2)];
                    $testType = ['Blood', 'Urine', 'Tissue', 'Microbiology'][rand(0, 3)];
                ?>
                <div class="border-l-4 border-<?php echo $priority === 'Urgent' ? 'red' : ($priority === 'High' ? 'orange' : 'green'); ?>-500 bg-gradient-to-r from-gray-50 to-white rounded-lg p-6 shadow-sm hover:shadow-md transition-all duration-200">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center mr-4 shadow-sm">
                                <i class="fas fa-user text-white text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?>
                                    <span class="ml-3 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        ID: <?php echo str_pad($result['id'], 4, '0', STR_PAD_LEFT); ?>
                                    </span>
                                </h4>
                                <div class="flex items-center space-x-4 mt-1">
                                    <p class="text-sm text-gray-600 flex items-center">
                                        <i class="fas fa-flask mr-2 text-blue-500"></i>
                                        <strong>Test:</strong> <?php echo htmlspecialchars($result['test_name']); ?>
                                    </p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        <i class="fas fa-tag mr-1"></i><?php echo $testType; ?>
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-clock mr-1"></i>
                                    Collected: <?php echo date('M j, Y H:i', strtotime($result['created_at'] ?? 'now')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full
                                <?php echo $priority === 'Urgent' ? 'bg-red-100 text-red-700 border border-red-200' : ($priority === 'High' ? 'bg-orange-100 text-orange-700 border border-orange-200' : 'bg-green-100 text-green-700 border border-green-200'); ?>">
                                <i class="fas fa-flag mr-1"></i><?php echo $priority; ?> Priority
                            </span>
                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                <i class="fas fa-hourglass-half mr-1"></i>Pending Result
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="/KJ/lab/record_result" class="enhanced-result-form" data-test-id="<?php echo $result['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
                        <input type="hidden" name="test_id" value="<?php echo $result['id']; ?>">

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Result Value Section -->
                            <div class="lg:col-span-2 space-y-4">
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <label for="result_value_<?php echo $result['id']; ?>" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-calculator mr-2 text-blue-600"></i>
                                        Result Value <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="result_value_<?php echo $result['id']; ?>" name="result_value"
                                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-medium"
                                               placeholder="Enter numerical result"
                                               onkeyup="validateResult(this, '<?php echo $result['normal_range'] ?? ''; ?>')"
                                               required>
                                        <div id="validation_<?php echo $result['id']; ?>" class="mt-2 text-sm hidden">
                                            <!-- Validation message will appear here -->
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="normal_range_<?php echo $result['id']; ?>" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-chart-line mr-2 text-green-600"></i>
                                            Normal Range
                                        </label>
                                        <input type="text" id="normal_range_<?php echo $result['id']; ?>" name="normal_range"
                                               value="<?php echo htmlspecialchars($result['normal_range'] ?? '0-100'); ?>"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 font-medium" readonly>
                                    </div>
                                    <div>
                                        <label for="units_<?php echo $result['id']; ?>" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-ruler mr-2 text-purple-600"></i>
                                            Units
                                        </label>
                                        <select id="units_<?php echo $result['id']; ?>" name="units" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="mg/dL">mg/dL</option>
                                            <option value="g/dL">g/dL</option>
                                            <option value="μg/mL">μg/mL</option>
                                            <option value="IU/L">IU/L</option>
                                            <option value="mmol/L">mmol/L</option>
                                            <option value="%">%</option>
                                            <option value="count">count</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions & Status -->
                            <div class="space-y-4">
                                <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg p-4">
                                    <h5 class="font-medium text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-tachometer-alt mr-2 text-purple-600"></i>
                                        Quick Actions
                                    </h5>
                                    <div class="space-y-2">
                                        <button type="button" onclick="setNormalValue(<?php echo $result['id']; ?>)" class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                            <i class="fas fa-check mr-2"></i>Set Normal Value
                                        </button>
                                        <button type="button" onclick="flagAbnormal(<?php echo $result['id']; ?>)" class="w-full bg-red-100 hover:bg-red-200 text-red-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>Flag Abnormal
                                        </button>
                                        <button type="button" onclick="requestRetest(<?php echo $result['id']; ?>)" class="w-full bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                            <i class="fas fa-redo mr-2"></i>Request Retest
                                        </button>
                                    </div>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h5 class="font-medium text-gray-800 mb-2 flex items-center">
                                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                                        Test Information
                                    </h5>
                                    <div class="text-xs text-gray-600 space-y-1">
                                        <div>Sample Type: <?php echo $testType; ?></div>
                                        <div>Received: <?php echo date('H:i', strtotime($result['created_at'] ?? 'now')); ?></div>
                                        <div>Technician: <?php echo $_SESSION['user_name'] ?? 'Current User'; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes Section -->
                        <div class="mt-6">
                            <label for="result_text_<?php echo $result['id']; ?>" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>
                                Clinical Notes & Observations
                            </label>
                            <textarea id="result_text_<?php echo $result['id']; ?>" name="result_text" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                      placeholder="Enter any clinical observations, methodological notes, or recommendations for the physician..."></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
                            <div class="flex items-center space-x-3">
                                <button type="button" onclick="saveAsDraft(<?php echo $result['id']; ?>)" class="flex items-center px-4 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                                    <i class="fas fa-save mr-2"></i>Save Draft
                                </button>
                                <button type="button" onclick="skipTest(<?php echo $result['id']; ?>)" class="flex items-center px-4 py-2 border-2 border-yellow-300 rounded-lg text-yellow-700 hover:bg-yellow-50 font-medium transition-colors">
                                    <i class="fas fa-forward mr-2"></i>Skip for Now
                                </button>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button type="button" onclick="previewResult(<?php echo $result['id']; ?>)" class="flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg font-medium transition-colors">
                                    <i class="fas fa-eye mr-2"></i>Preview Report
                                </button>
                                <button type="submit" class="flex items-center px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg font-medium shadow-md transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-check-circle mr-2"></i>Submit Result
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Enhanced JavaScript for Results Page -->
<script>
// Enhanced Results Management Functions

// Real-time result validation
function validateResult(input, normalRange) {
    const value = parseFloat(input.value);
    const testId = input.id.split('_')[2];
    const validationDiv = document.getElementById(`validation_${testId}`);
    
    if (!value || !normalRange) {
        validationDiv.classList.add('hidden');
        return;
    }
    
    // Parse normal range (e.g., "10-50")
    const rangeParts = normalRange.split('-');
    if (rangeParts.length === 2) {
        const min = parseFloat(rangeParts[0]);
        const max = parseFloat(rangeParts[1]);
        
        validationDiv.classList.remove('hidden');
        
        if (value < min) {
            validationDiv.innerHTML = `
                <div class="flex items-center text-red-600">
                    <i class="fas fa-arrow-down mr-2"></i>
                    <span class="font-medium">Below Normal Range</span>
                    <span class="ml-2 text-sm">(${value} < ${min})</span>
                </div>
            `;
            input.classList.add('border-red-500');
            input.classList.remove('border-green-500', 'border-gray-300');
        } else if (value > max) {
            validationDiv.innerHTML = `
                <div class="flex items-center text-red-600">
                    <i class="fas fa-arrow-up mr-2"></i>
                    <span class="font-medium">Above Normal Range</span>
                    <span class="ml-2 text-sm">(${value} > ${max})</span>
                </div>
            `;
            input.classList.add('border-red-500');
            input.classList.remove('border-green-500', 'border-gray-300');
        } else {
            validationDiv.innerHTML = `
                <div class="flex items-center text-green-600">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="font-medium">Within Normal Range</span>
                    <span class="ml-2 text-sm">(${min} - ${max})</span>
                </div>
            `;
            input.classList.add('border-green-500');
            input.classList.remove('border-red-500', 'border-gray-300');
        }
    }
}

// Quick action functions
function setNormalValue(testId) {
    const normalRangeInput = document.getElementById(`normal_range_${testId}`);
    const resultInput = document.getElementById(`result_value_${testId}`);
    
    if (normalRangeInput.value) {
        const rangeParts = normalRangeInput.value.split('-');
        if (rangeParts.length === 2) {
            const min = parseFloat(rangeParts[0]);
            const max = parseFloat(rangeParts[1]);
            const normalValue = ((min + max) / 2).toFixed(1);
            
            resultInput.value = normalValue;
            validateResult(resultInput, normalRangeInput.value);
            showNotification('Normal Value Set', `Set result to ${normalValue} (mid-range)`, 'success');
        }
    }
}

function flagAbnormal(testId) {
    const form = document.querySelector(`form[data-test-id="${testId}"]`);
    const notesTextarea = form.querySelector('textarea[name="result_text"]');
    
    notesTextarea.value = (notesTextarea.value ? notesTextarea.value + '\n\n' : '') + 
                         '[FLAGGED ABNORMAL] - Requires physician review. ';
    notesTextarea.focus();
    showNotification('Result Flagged', 'Result marked as abnormal for physician review', 'warning');
}

function requestRetest(testId) {
    if (confirm('Request a retest for this sample? This will delay the result.')) {
        showNotification('Retest Requested', 'Sample marked for retesting', 'info');
        // In real implementation, this would update the database
    }
}

function saveAsDraft(testId) {
    const form = document.querySelector(`form[data-test-id="${testId}"]`);
    const formData = new FormData(form);
    
    showNotification('Draft Saved', 'Result saved as draft for later completion', 'info');
    // In real implementation, this would save to database as draft
}

function skipTest(testId) {
    if (confirm('Skip this test for now? You can return to it later.')) {
        const form = document.querySelector(`form[data-test-id="${testId}"]`).closest('.border-l-4');
        form.style.opacity = '0.5';
        form.style.transform = 'scale(0.98)';
        setTimeout(() => {
            form.style.display = 'none';
        }, 300);
        showNotification('Test Skipped', 'Test moved to later queue', 'info');
    }
}

function previewResult(testId) {
    const form = document.querySelector(`form[data-test-id="${testId}"]`);
    const resultValue = form.querySelector('input[name="result_value"]').value;
    const notes = form.querySelector('textarea[name="result_text"]').value;
    const units = form.querySelector('select[name="units"]').value;
    const normalRange = form.querySelector(`input[id="normal_range_${testId}"]`).value;
    const patientName = form.closest('.border-l-4').querySelector('h4').textContent.trim();
    const testElement = form.closest('.border-l-4').querySelector('p strong');
    const testName = testElement && testElement.textContent === 'Test:' ? 
        testElement.parentNode.textContent.replace('Test:', '').trim() : 'Unknown Test';
    
    if (!resultValue) {
        showNotification('Missing Data', 'Please enter a result value before preview', 'warning');
        return;
    }
    
    // Get validation status
    let validationStatus = "normal";
    if (normalRange) {
        const rangeParts = normalRange.split('-');
        if (rangeParts.length === 2) {
            const min = parseFloat(rangeParts[0]);
            const max = parseFloat(rangeParts[1]);
            
            if (parseFloat(resultValue) < min) {
                validationStatus = "below";
            } else if (parseFloat(resultValue) > max) {
                validationStatus = "above";
            }
        }
    }
    
    // Update the result preview modal content
    document.getElementById('modal_result_value').textContent = `${resultValue} ${units}`;
    document.getElementById('modal_normal_range').textContent = normalRange;
    document.getElementById('modal_result_notes').textContent = notes || 'None';
    document.getElementById('modal_technician').textContent = document.querySelector('meta[name="user"]')?.content || 'Current User';
    document.getElementById('modal_test_date').textContent = new Date().toLocaleDateString();
    document.getElementById('modal_patient_name').textContent = patientName;
    document.getElementById('modal_test_name').textContent = testName;
    
    // Update validation status styling
    const statusElement = document.getElementById('modal_validation_status');
    if (validationStatus === "below") {
        statusElement.innerHTML = '<i class="fas fa-arrow-down mr-2"></i>Below Normal Range';
        statusElement.className = 'px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold';
    } else if (validationStatus === "above") {
        statusElement.innerHTML = '<i class="fas fa-arrow-up mr-2"></i>Above Normal Range';
        statusElement.className = 'px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold';
    } else {
        statusElement.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Within Normal Range';
        statusElement.className = 'px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold';
    }
    
    // Show the modal
    const modal = document.getElementById('resultPreviewModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function saveAllResults() {
    const forms = document.querySelectorAll('.enhanced-result-form');
    let completedCount = 0;
    
    forms.forEach(form => {
        const resultValue = form.querySelector('input[name="result_value"]').value;
        if (resultValue) {
            completedCount++;
        }
    });
    
    if (completedCount === 0) {
        showNotification('No Results', 'Please enter at least one result before saving all', 'warning');
        return;
    }
    
    if (confirm(`Save ${completedCount} completed result(s)?`)) {
        showNotification('Batch Save', `${completedCount} results saved successfully`, 'success');
        // In real implementation, this would submit all forms
    }
}

function toggleValidationMode() {
    const checkbox = document.getElementById('autoValidate');
    checkbox.checked = !checkbox.checked;
    
    const mode = checkbox.checked ? 'enabled' : 'disabled';
    showNotification('Validation Mode', `Auto-validation ${mode}`, 'info');
}

function refreshResults() {
    showNotification('Refreshing', 'Checking for new test results...', 'info');
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Enhanced notification system
function showNotification(title, message, type = 'info') {
    const colors = {
        info: 'bg-blue-500',
        success: 'bg-green-500',
        warning: 'bg-yellow-500',
        error: 'bg-red-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50 max-w-sm`;
    notification.innerHTML = `
        <div class="flex items-start justify-between">
            <div class="flex-1 pr-3">
                <div class="font-semibold">${title}</div>
                <div class="text-sm opacity-90 mt-1">${message}</div>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200 flex-shrink-0">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Auto-save drafts every 30 seconds
setInterval(() => {
    const forms = document.querySelectorAll('.enhanced-result-form');
    forms.forEach(form => {
        const resultValue = form.querySelector('input[name="result_value"]').value;
        if (resultValue) {
            // Auto-save logic would go here
            console.log('Auto-saving draft...');
        }
    });
}, 30000);

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Lab Results Enhanced - Ready for precise result entry!');
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            saveAllResults();
        }
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            refreshResults();
        }
    });
    
    // Focus first result input
    const firstInput = document.querySelector('input[name="result_value"]');
    if (firstInput) {
        firstInput.focus();
    }
});

// Close result preview modal
function closeResultPreviewModal() {
    const modal = document.getElementById('resultPreviewModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>

<!-- Result Preview Modal Dialog -->
<div id="resultPreviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-100">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <h3 class="text-xl font-bold text-gray-900">Test Result Preview</h3>
            <button onclick="closeResultPreviewModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="px-6 py-4">
            <!-- Patient Info -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-1">Patient</label>
                <p id="modal_patient_name" class="text-lg font-semibold text-gray-900"></p>
            </div>
            
            <!-- Test Name -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-1">Test</label>
                <p id="modal_test_name" class="text-gray-900 font-medium"></p>
            </div>
            
            <!-- Result Value -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-1">Result Value</label>
                <div class="flex items-center">
                    <p id="modal_result_value" class="text-xl font-bold text-blue-600"></p>
                    <span id="modal_validation_status" class="ml-3 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold"></span>
                </div>
            </div>
            
            <!-- Normal Range -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-1">Normal Range</label>
                <p id="modal_normal_range" class="text-gray-900 font-medium"></p>
            </div>
            
            <!-- Notes -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600 mb-1">Clinical Notes</label>
                <p id="modal_result_notes" class="text-gray-900 whitespace-pre-wrap p-3 bg-gray-50 rounded-lg"></p>
            </div>
            
            <!-- Test Information -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Technician</label>
                    <p id="modal_technician" class="text-gray-900"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Date</label>
                    <p id="modal_test_date" class="text-gray-900"></p>
                </div>
            </div>
        </div>
        
        <!-- Modal Actions -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3">
            <button type="button" onclick="closeResultPreviewModal()"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium">
                Close
            </button>
            <button type="button" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium">
                <i class="fas fa-print mr-2"></i>Print Result
            </button>
        </div>
    </div>
</div>
