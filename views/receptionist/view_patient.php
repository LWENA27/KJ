<!-- Print Medical Record Form -->
<div class="mb-6 no-print">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Patient Information</h1>
        <div class="flex space-x-3">
            <button onclick="printMedicalRecord()"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-print mr-2"></i>Print Record
            </button>
            <!-- Receptionist-specific actions -->
            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/payments?patient_id=<?php echo $patient['id']; ?>&step=consultation"
               class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-credit-card mr-2"></i>Process Payment
            </a>
            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/appointments?patient_id=<?php echo $patient['id']; ?>"
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-calendar-plus mr-2"></i>Schedule Appointment
            </a>
            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/dispense_medicines?patient_id=<?php echo $patient['id']; ?>"
               class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-pills mr-2"></i>Dispense Medicine
            </a>
            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/receptionist/patients" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-arrow-left mr-2"></i>Back to Patients
            </a>
        </div>
    </div>
</div>

<!-- Medical Record Form (Printable) -->
<div id="medicalRecord" class="bg-white border-2 border-gray-300 p-8 max-w-5xl mx-auto print:border-none print:p-4">
    <!-- Header -->
    <div class="text-center mb-6 border-b-2 border-gray-400 pb-4">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">KJ DISPENSARY</h1>
        <p class="text-sm text-gray-700">P.O.BOX 149, MBEYA</p>
        <p class="text-sm text-gray-700">PHONE 0776992746; centidispensary@gmail.com</p>
        <div class="flex justify-between mt-4 text-sm">
            <div>TOTAL………………………………………</div>
            <div>CASH PAID………….……………………….</div>
            <div>DEBIT………………………………………….</div>
        </div>
    </div>

    <!-- Patient Record Header -->
    <div class="mb-6">
        <h2 class="text-xl font-bold text-center mb-4 underline">PATIENT RECORD</h2>
        
        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
            <div class="flex items-center">
                <span class="font-medium mr-2">DATE:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo date('d/m/Y'); ?></span>
            </div>
            <div class="flex items-center">
                <span class="font-medium mr-2">REG NO:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo ($patient['registration_number']) ?></span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4 text-sm">
            <div class="col-span-2 flex items-center">
                <span class="font-medium mr-2">PATIENT NAME:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></span>
            </div>
            <div class="flex items-center">
                <span class="font-medium mr-2">AGE:</span>
                <span class="border-b border-gray-400 flex-1 px-2 mr-4">
                    <?php
                    $dob = $patient['date_of_birth'] ?? null;
                    if (!empty($dob)) {
                        $age = date_diff(date_create($dob), date_create('today'))->y;
                        echo $age;
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </span>
                <span class="font-medium mr-2">SEX:</span>
                <span class="border-b border-gray-400 px-2"><?php echo strtoupper(substr($patient['gender'] ?? 'U', 0, 1)); ?></span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6 text-sm">
            <div class="flex items-center">
                <span class="font-medium mr-2">ADDRESS:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo htmlspecialchars($patient['address'] ?? ''); ?></span>
            </div>
            <div class="flex items-center">
                <span class="font-medium mr-2">OCCUPATION:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo htmlspecialchars($patient['occupation'] ?? ''); ?></span>
            </div>
            <div class="flex items-center">
                <span class="font-medium mr-2">PHONE NO:</span>
                <span class="border-b border-gray-400 flex-1 px-2"><?php echo htmlspecialchars($patient['phone'] ?? ''); ?></span>
            </div>
        </div>
    </div>

    <!-- Vital Signs -->
    <div class="mb-6">
        <div class="grid grid-cols-5 gap-4 text-sm">
            <div class="text-center">
                <div class="font-medium mb-1">Temperature</div>
                <div class="border border-gray-400 h-20 p-2 text-center">
                    <?php 
                    if (!empty($vital_signs['temperature'])) {
                        echo htmlspecialchars($vital_signs['temperature']) . '°C';
                    }
                    ?>
                </div>
            </div>
            <div class="text-center">
                <div class="font-medium mb-1">Blood Pressure</div>
                <div class="border border-gray-400 h-20 p-2 text-center">
                    <?php 
                    if (!empty($vital_signs['blood_pressure_systolic']) && !empty($vital_signs['blood_pressure_diastolic'])) {
                        echo htmlspecialchars($vital_signs['blood_pressure_systolic']) . '/' . htmlspecialchars($vital_signs['blood_pressure_diastolic']);
                    }
                    ?>
                </div>
            </div>
            <div class="text-center">
                <div class="font-medium mb-1">Pulse Rate</div>
                <div class="border border-gray-400 h-20 p-2 text-center">
                    <?php 
                    if (!empty($vital_signs['pulse_rate'])) {
                        echo htmlspecialchars($vital_signs['pulse_rate']) . ' bpm';
                    }
                    ?>
                </div>
            </div>
            <div class="text-center">
                <div class="font-medium mb-1">Body Weight</div>
                <div class="border border-gray-400 h-20 p-2 text-center">
                    <?php 
                    if (!empty($vital_signs['weight'])) {
                        echo htmlspecialchars($vital_signs['weight']) . ' kg';
                    }
                    ?>
                </div>
            </div>
            <div class="text-center">
                <div class="font-medium mb-1">Height</div>
                <div class="border border-gray-400 h-20 p-2 text-center">
                    <?php 
                    if (!empty($vital_signs['height'])) {
                        echo htmlspecialchars($vital_signs['height']) . ' cm';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Clinical Examination -->
    <div class="mb-6">
        <div class="grid grid-cols-1 gap-4 text-sm">
            <?php
            // Get latest consultation for this patient
            $latest_consultation = null;
            if (!empty($consultations)) {
                $latest_consultation = $consultations[0];
            }
            ?>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="font-medium mb-1">M/C</div>
                    <div class="border border-gray-400 h-16 p-2">
                        <?php echo htmlspecialchars($latest_consultation['main_complaint'] ?? ''); ?>
                    </div>
                </div>
                <div>
                    <div class="font-medium mb-1">O/E</div>
                    <div class="border border-gray-400 h-16 p-2">
                        <?php echo htmlspecialchars($latest_consultation['on_examination'] ?? ''); ?>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="font-medium mb-1">Preliminary Dx</div>
                    <div class="border border-gray-400 h-16 p-2">
                        <?php echo htmlspecialchars($latest_consultation['preliminary_diagnosis'] ?? ''); ?>
                    </div>
                </div>
                <div>
                    <div class="font-medium mb-1">Final Dx</div>
                    <div class="border border-gray-400 h-16 p-2">
                        <?php echo htmlspecialchars($latest_consultation['final_diagnosis'] ?? ''); ?>
                    </div>
                </div>
            </div>
            
            <div>
                <div class="font-medium mb-1">Lab Investigation</div>
                <div class="border border-gray-400 h-16 p-2">
                    <?php echo htmlspecialchars($latest_consultation['lab_investigation'] ?? ''); ?>
                </div>
            </div>
            
            <div>
                <div class="font-medium mb-1">RX</div>
                <div class="border border-gray-400 h-20 p-2">
                    <?php echo htmlspecialchars($latest_consultation['prescription'] ?? ''); ?>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
            <div class="flex items-center">
                <span class="font-medium mr-2">DATE:</span>
                <span class="border-b border-gray-400 flex-1 px-2">
                    <?php $apt = $latest_consultation['appointment_date'] ?? $latest_consultation['visit_date'] ?? $latest_consultation['created_at'] ?? null; echo $apt ? safe_date('d/m/Y', $apt, '') : ''; ?>
                </span>
            </div>
            <div class="flex items-center">
                <span class="font-medium mr-2">Dr Signature:</span>
                <span class="border-b border-gray-400 flex-1 px-2"></span>
            </div>
        </div>
    </div>

    <!-- Laboratory Results Grid -->
    <div class="mb-6 text-xs">
        <div class="grid grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <!-- Parasitology -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Parasitology</h4>
                    <div class="space-y-1">
                        <div>• mRDT…………………………………………………</div>
                        <div>• Blood Slide Smear……………………………….</div>
                        <div>……………………………………………………………………….</div>
                        <div>• Urine sedimentary</div>
                        <div>Urine appearance……………………………………………</div>
                        <div>Urine microscopic report………………………………</div>
                        <div>……………………………………………………………………….</div>
                        <div>• Urine Chemistry</div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>Leucocytes…………………….</div>
                            <div>PH………………………….</div>
                            <div>Protein………………………….</div>
                            <div>Blood……………………...</div>
                        </div>
                        <div>• Stool analysis</div>
                        <div>Stool appearance……………………………………………</div>
                        <div>Stool microscopic report…………………………………</div>
                        <div>………………………………………………………………………</div>
                    </div>
                </div>

                <!-- Hematology -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Hematology</h4>
                    <div class="space-y-1">
                        <div>• Hemoglobin……………………………….g/dL</div>
                        <div>• ESR…………………………………………………….</div>
                        <div>• Full blood picture……………………………….</div>
                        <div>Others………………………………………………………</div>
                    </div>
                </div>

                <!-- Clinical Chemistry -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Clinical chemistry</h4>
                    <div class="space-y-1">
                        <div>• Blood sugar………………………….…mmol/L</div>
                        <div>• Blood uric acid……………………………………</div>
                        <div>• Rheumatoid factor………………………………</div>
                        <div>• Others……………………………………………….</div>
                        <div>…………………………………………………………..</div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <!-- Serology -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Serology</h4>
                    <div class="space-y-1">
                        <div>• H.Pylori antigen………………………………….</div>
                        <div>• H.Pylori antibody.……………………………….</div>
                        <div>• RPP/Syphilis……………………………………...</div>
                        <div>• UPT……………………………………………………</div>
                        <div>• Salmonella typhi/parathyphiantigen…..</div>
                        <div>…………………………………………………………..</div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>• STO …………………………..</div>
                            <div>STH………………...</div>
                        </div>
                        <div>• Rheumatoid Factor…………………………….</div>
                        <div>Others…………………………………………………….</div>
                        <div>……………………………………………………………….</div>
                    </div>
                </div>

                <!-- Blood Transfusion -->
                <div class="border border-gray-400 p-3">
                    <h4 class="font-bold mb-2">Blood transfusion</h4>
                    <div class="space-y-1">
                        <div class="grid grid-cols-2 gap-2">
                            <div>• Blood group……………</div>
                            <div>Rhesus…………….</div>
                        </div>
                        <div>Others…………………………………………………….</div>
                    </div>
                </div>

                <!-- Test Signature -->
                <div class="border border-gray-400 p-3">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>Test performed by ……………………………………………………</div>
                        <div>Signature ……………………………..……………</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment History Section (Receptionist-focused) -->
<div class="mb-6 no-print">
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-credit-card mr-3 text-green-600"></i>
            Payment History
        </h2>
        
        <?php if (!empty($payments)): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($payments as $payment): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <?php echo safe_date('d/m/Y', $payment['payment_date']); ?>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $payment['payment_type']))); ?>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900 font-medium">
                                TZS <?php echo number_format($payment['amount'], 2); ?>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-900">
                                <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $payment['payment_method']))); ?>
                            </td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php echo $payment['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($payment['payment_status'])); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-8">
                <i class="fas fa-credit-card text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">No payment history found for this patient.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>