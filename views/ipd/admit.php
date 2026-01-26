<?php
$pageTitle = "Admit Patient to IPD";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Admit Patient to IPD</h1>
            
            <form method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                
                <?php if ($patient): ?>
                    <input type="hidden" name="patient_id" value="<?php echo $patient['patient_id']; ?>">
                    <input type="hidden" name="visit_id" value="<?php echo $visit['id'] ?? ''; ?>">
                    
                    <div class="bg-blue-50 rounded p-4 mb-4">
                        <h3 class="font-bold text-blue-900 mb-2">Patient Information</h3>
                        <p><span class="text-blue-700">Name:</span> <strong><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></strong></p>
                        <p><span class="text-blue-700">Patient #:</span> <strong><?php echo htmlspecialchars($patient['patient_number']); ?></strong></p>
                        <p><span class="text-blue-700">Age/Gender:</span> <strong><?php echo $patient['age']; ?> years / <?php echo ucfirst($patient['gender']); ?></strong></p>
                    </div>
                <?php else: ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Patient Number <span class="text-red-500">*</span></label>
                        <input type="text" name="patient_number" required class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                <?php endif; ?>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Bed <span class="text-red-500">*</span></label>
                    <select name="bed_id" required class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">-- Select Bed --</option>
                        <?php 
                        $current_ward = '';
                        foreach ($available_beds as $bed): 
                            if ($current_ward !== $bed['ward_name']): 
                                $current_ward = $bed['ward_name'];
                                if ($current_ward !== $bed['ward_name'] && $current_ward !== '') echo '</optgroup>';
                                echo '<optgroup label="' . htmlspecialchars($bed['ward_name']) . '">';
                            endif;
                        ?>
                            <option value="<?php echo $bed['id']; ?>">
                                <?php echo htmlspecialchars($bed['bed_number'] . ' (' . ucfirst($bed['bed_type']) . ') - ' . number_format($bed['daily_rate']) . ' TZS/day'); ?>
                            </option>
                        <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Admission Type <span class="text-red-500">*</span></label>
                    <select name="admission_type" required class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="planned">Planned</option>
                        <option value="emergency">Emergency</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Admission Diagnosis <span class="text-red-500">*</span></label>
                    <textarea name="admission_diagnosis" rows="4" required class="w-full border border-gray-300 rounded px-3 py-2"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Attending Doctor</label>
                    <select name="attending_doctor" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">-- Select Doctor --</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo $doctor['id']; ?>">
                                Dr. <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <a href="<?php echo BASE_PATH; ?>/ipd/dashboard" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">Cancel</a>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Admit Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
