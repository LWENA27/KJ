<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Lab Results</h1>
    <a href="<?= $BASE_PATH ?>/doctor/lab_results" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            <i class="fas fa-search mr-2"></i>Search Results
        </a>
    </div>

    <!-- Lab Results Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Test Results</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Result</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($results as $result): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-green-600"></i>
                                </div>
                                <div>
                                        <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php $apt = $result['appointment_date'] ?? $result['visit_date'] ?? $result['created_at']; echo safe_date('M j, Y', $apt, 'N/A'); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                <?php
                                switch ($result['status']) {
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
                                <?php echo ucfirst($result['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo safe_date('M j, Y', $result['created_at'], 'N/A'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button 
                                data-test-id="<?php echo $result['id']; ?>"
                                data-patient-name="<?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?>"
                                data-test-name="<?php echo htmlspecialchars($result['test_name']); ?>"
                                data-result-value="<?php echo htmlspecialchars($result['result_value'] ?? ''); ?>"
                                data-result-text="<?php echo htmlspecialchars($result['result_text'] ?? ''); ?>"
                                data-result-unit="<?php echo htmlspecialchars($result['result_unit'] ?? ''); ?>"
                                data-completed-at="<?php echo $result['completed_at'] ? date('M j, Y H:i', strtotime($result['completed_at'])) : ''; ?>"
                                data-status="<?php echo htmlspecialchars($result['status']); ?>"
                                class="view-details-btn text-blue-600 hover:text-blue-900 mr-3 focus:outline-none">
                                <i class="fas fa-eye mr-1"></i> View Details
                            </button>
                            <?php if ($result['status'] === 'completed'): ?>
                            <button 
                                data-patient-id="<?php echo $result['patient_id']; ?>"
                                class="prescribe-btn text-green-600 hover:text-green-900 focus:outline-none">
                                <i class="fas fa-prescription mr-1"></i> Prescribe Medicine
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Test Details Modal -->
<div id="testDetailsModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop with blur effect -->
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm" id="modalBackdrop"></div>
    
    <!-- Modal Content -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-2xl rounded-xl shadow-2xl transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-flask text-blue-600 mr-3"></i>
                    Lab Test Result Details
                </h3>
                <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Body -->
            <div class="px-6 py-4">
                <!-- Patient & Test Info -->
                <div class="bg-blue-50 p-4 rounded-lg mb-5">
                    <div class="flex items-center mb-2">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900" id="patientName"></h4>
                            <p class="text-sm text-gray-600" id="testName"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Result Details -->
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Result Value</label>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <div class="text-lg font-medium" id="resultValue"></div>
                                <div class="text-sm text-gray-500" id="resultUnit"></div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <span id="statusBadge" class="inline-flex px-2 py-1 text-xs font-medium rounded-full"></span>
                                <div class="text-sm text-gray-500 mt-1" id="completedAt"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 min-h-[80px]" id="resultNotes">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                <button type="button" id="closeModalBtn"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 shadow-sm hover:bg-gray-50">
                    Close
                </button>
                <button type="button" id="printResultBtn"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i>Print Result
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Prescription Modal -->
<div id="prescriptionModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
    
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <!--
            Centered modal box with reduced width and 100px left margin.
        -->
        <div class="bg-white rounded-xl shadow-2xl transform transition-all w-full mx-4
                    md:min-w-[720px] lg:min-w-[880px] max-w-4xl min-h-[560px] overflow-y-auto"
             style="margin-left:100px;">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-prescription text-green-600 mr-3"></i>
                    Prescribe Medicine
                </h3>
                <button type="button" id="closePrescriptionModal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Body -->
            <form id="prescriptionForm" method="POST" action="<?= $BASE_PATH ?>/doctor/prescribe_medicine">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="patient_id" id="prescriptionPatientId">
                
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        <!-- Medicine Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search Medicine</label>
                            <div class="relative">
                                <input type="text" id="medicineSearch" 
                                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Type to search medicines...">
                                <div id="medicineSearchResults" class="absolute z-10 w-full mt-1 bg-white shadow-lg rounded-lg border border-gray-200 hidden">
                                </div>
                            </div>
                        </div>

                        <!-- Selected Medicines -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selected Medicines</label>
                            <div id="selectedMedicines" class="space-y-2">
                                <!-- Selected medicines will be added here dynamically -->
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prescription Notes</label>
                            <textarea name="notes" rows="3" 
                                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter prescription notes..."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                    <button type="button" id="cancelPrescription"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 shadow-sm hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg shadow-sm hover:bg-green-700">
                        <i class="fas fa-check mr-2"></i>Submit Prescription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Initialize the Details Modal functionality when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    const testDetailsModal = document.getElementById('testDetailsModal');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const closeModal = document.getElementById('closeModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const printResultBtn = document.getElementById('printResultBtn');
    
    // Get all view details buttons
    const viewDetailsButtons = document.querySelectorAll('.view-details-btn');
    
    // Function to open modal
    function openModal(testData) {
        // Populate modal with test data
        document.getElementById('patientName').textContent = testData.patientName;
        document.getElementById('testName').textContent = testData.testName;
        document.getElementById('resultValue').textContent = testData.resultValue || 'N/A';
        document.getElementById('resultUnit').textContent = testData.resultUnit || '';
        document.getElementById('resultNotes').textContent = testData.resultText || 'No notes available';
        document.getElementById('completedAt').textContent = testData.completedAt ? `Completed: ${testData.completedAt}` : '';
        
        // Set status badge color
        const statusBadge = document.getElementById('statusBadge');
        statusBadge.textContent = testData.status.charAt(0).toUpperCase() + testData.status.slice(1);
        
        switch (testData.status) {
            case 'pending':
                statusBadge.className = 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800';
                break;
            case 'completed':
                statusBadge.className = 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800';
                break;
            case 'reviewed':
                statusBadge.className = 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800';
                break;
            default:
                statusBadge.className = 'inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800';
        }
        
        // Show the modal
        testDetailsModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        
        // Animate in
        setTimeout(() => {
            modalBackdrop.classList.add('opacity-100');
        }, 50);
    }
    
    // Function to close modal
    function closeModalHandler() {
        // Hide the modal
        testDetailsModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
    
    // Add click event to all view details buttons
    viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function() {
            const testData = {
                testId: this.getAttribute('data-test-id'),
                patientName: this.getAttribute('data-patient-name'),
                testName: this.getAttribute('data-test-name'),
                resultValue: this.getAttribute('data-result-value'),
                resultUnit: this.getAttribute('data-result-unit'),
                resultText: this.getAttribute('data-result-text'),
                completedAt: this.getAttribute('data-completed-at'),
                status: this.getAttribute('data-status')
            };
            
            openModal(testData);
        });
    });
    
    // Close modal events
    closeModal.addEventListener('click', closeModalHandler);
    closeModalBtn.addEventListener('click', closeModalHandler);
    modalBackdrop.addEventListener('click', closeModalHandler);
    
    // Print result
    printResultBtn.addEventListener('click', function() {
        // Create a printable version of the test details
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Lab Test Result</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        margin: 20px;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    .header h1 {
                        margin-bottom: 5px;
                    }
                    .details {
                        margin-bottom: 20px;
                    }
                    .result-box {
                        border: 1px solid #ddd;
                        padding: 15px;
                        margin-bottom: 20px;
                    }
                    .footer {
                        margin-top: 40px;
                        border-top: 1px solid #ddd;
                        padding-top: 10px;
                        font-size: 12px;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Laboratory Test Result</h1>
                    <p>HOSPITALI Medical Center</p>
                </div>
                <div class="details">
                    <p><strong>Patient Name:</strong> ${document.getElementById('patientName').textContent}</p>
                    <p><strong>Test Name:</strong> ${document.getElementById('testName').textContent}</p>
                    <p><strong>Date:</strong> ${document.getElementById('completedAt').textContent.replace('Completed: ', '')}</p>
                </div>
                <div class="result-box">
                    <h3>Test Results</h3>
                    <p><strong>Result Value:</strong> ${document.getElementById('resultValue').textContent} ${document.getElementById('resultUnit').textContent}</p>
                    <p><strong>Status:</strong> ${document.getElementById('statusBadge').textContent}</p>
                    <p><strong>Notes:</strong> ${document.getElementById('resultNotes').textContent}</p>
                </div>
                <div class="footer">
                    <p>This is an automatically generated report. Please consult with your physician to interpret these results.</p>
                    <p>Printed on: ${new Date().toLocaleString()}</p>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        setTimeout(() => {
            printWindow.print();
        }, 500);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const prescriptionModal = document.getElementById('prescriptionModal');
    const prescribeButtons = document.querySelectorAll('.prescribe-btn');
    const closePrescriptionModal = document.getElementById('closePrescriptionModal');
    const cancelPrescription = document.getElementById('cancelPrescription');
    const medicineSearch = document.getElementById('medicineSearch');
    const medicineSearchResults = document.getElementById('medicineSearchResults');
    const selectedMedicines = document.getElementById('selectedMedicines');
    const prescriptionForm = document.getElementById('prescriptionForm');
    
    let searchTimeout;
    
    // Open prescription modal
    prescribeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const patientId = this.getAttribute('data-patient-id');
            document.getElementById('prescriptionPatientId').value = patientId;
            prescriptionModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });
    });
    
    // Close modal handlers
    [closePrescriptionModal, cancelPrescription].forEach(button => {
        button.addEventListener('click', () => {
            prescriptionModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });
    });
    
    // Medicine search handler
    medicineSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            medicineSearchResults.classList.add('hidden');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetch(`${BASE_PATH}/doctor/search_medicines?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(medicines => {
                    medicineSearchResults.innerHTML = '';
                    
                    medicines.forEach(medicine => {
                        const div = document.createElement('div');
                        div.className = 'p-2 hover:bg-gray-100 cursor-pointer';
                        div.innerHTML = `
                            <div class="font-medium">${medicine.name}</div>
                            <div class="text-sm text-gray-600">Stock: ${medicine.stock_quantity}</div>
                        `;
                        div.addEventListener('click', () => selectMedicine(medicine));
                        medicineSearchResults.appendChild(div);
                    });
                    
                    medicineSearchResults.classList.remove('hidden');
                });
        }, 300);
    });
    
    function selectMedicine(medicine) {
        const medicineId = `medicine-${medicine.id}`;
        
        if (!document.getElementById(medicineId)) {
            const div = document.createElement('div');
            div.id = medicineId;
            div.className = 'bg-gray-50 rounded-lg p-3 border border-gray-200';
            div.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium">${medicine.name}</div>
                        <div class="text-sm text-gray-600">Available: ${medicine.stock_quantity}</div>
                    </div>
                    <button type="button" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mt-2 grid grid-cols-2 gap-2">
                    <input type="number" name="medicines[${medicine.id}][quantity]" 
                           class="rounded border-gray-300" placeholder="Quantity" min="1" max="${medicine.stock_quantity}">
                    <input type="text" name="medicines[${medicine.id}][dosage]" 
                           class="rounded border-gray-300" placeholder="Dosage">
                </div>
            `;
            
            div.querySelector('button').addEventListener('click', () => div.remove());
            selectedMedicines.appendChild(div);
        }
        
        medicineSearch.value = '';
        medicineSearchResults.classList.add('hidden');
    }
    
    // Form submission handler
    prescriptionForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        const selectedMeds = document.querySelectorAll('[name^="medicines["]');
        if (selectedMeds.length === 0) {
            alert('Please select at least one medicine');
            return;
        }
        
        // Submit form
        this.submit();
    });
});
</script>
