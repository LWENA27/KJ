<!-- Print Medical Record Form -->
<div class="mb-6 no-print">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Patient Medical Record</h1>
        <div class="flex space-x-3">
            <button onclick="printMedicalRecord()"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-print mr-2"></i>Print Record
            </button>
            <!-- Change this button -->
            <button onclick="window.location.href='/KJ/doctor/attend_patient/<?php echo $patient['id']; ?>'"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-stethoscope mr-2"></i>Attend Patient
            </button>
            <a href="/KJ/doctor/view_lab_results/<?php echo $patient['id']; ?>"
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-vial mr-2"></i>View Lab Results
            </a>
            <a href="/KJ/doctor/patient_journey/<?php echo $patient['id']; ?>"
               class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md">
                <i class="fas fa-route mr-2"></i>View Journey
            </a>
            <a href="/KJ/doctor/patients" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
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
                    <?php $apt = $latest_consultation['appointment_date'] ?? $latest_consultation['visit_date'] ?? $latest_consultation['created_at'] ?? null; echo $apt ? date('d/m/Y', strtotime($apt)) : ''; ?>
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

<style>