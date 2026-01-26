<?php
$pageTitle = "View Admission - " . $admission['admission_number'];
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Admission Details</h1>
        <div class="space-x-2">
            <?php if ($admission['status'] === 'active'): ?>
                <button onclick="document.getElementById('dischargeModal').classList.remove('hidden')" 
                        class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700">
                    Discharge Patient
                </button>
            <?php endif; ?>
            <a href="<?php echo BASE_PATH; ?>/ipd/admissions" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Patient & Admission Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Patient Information</h2>
                <div class="space-y-3 text-sm">
                    <div><p class="text-gray-600">Name</p><p class="font-semibold"><?php echo htmlspecialchars($admission['first_name'] . ' ' . $admission['last_name']); ?></p></div>
                    <div><p class="text-gray-600">Patient #</p><p class="font-semibold"><?php echo htmlspecialchars($admission['patient_number']); ?></p></div>
                    <div><p class="text-gray-600">Age/Gender</p><p class="font-semibold"><?php echo $admission['age']; ?> years / <?php echo ucfirst($admission['gender']); ?></p></div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Admission Info</h2>
                <div class="space-y-3 text-sm">
                    <div><p class="text-gray-600">Admission #</p><p class="font-semibold"><?php echo htmlspecialchars($admission['admission_number']); ?></p></div>
                    <div><p class="text-gray-600">Ward/Bed</p><p class="font-semibold"><?php echo htmlspecialchars($admission['ward_name'] . ' - ' . $admission['bed_number']); ?></p></div>
                    <div><p class="text-gray-600">Admitted</p><p class="font-semibold"><?php echo date('M d, Y g:i A', strtotime($admission['admission_datetime'])); ?></p></div>
                    <div><p class="text-gray-600">Days in IPD</p><p class="font-semibold"><?php echo $admission['total_days']; ?> days</p></div>
                    <div><p class="text-gray-600">Doctor</p><p class="font-semibold">Dr. <?php echo htmlspecialchars($admission['doctor_first_name'] . ' ' . $admission['doctor_last_name']); ?></p></div>
                    <div><p class="text-gray-600">Status</p><p class="font-semibold"><?php echo ucfirst($admission['status']); ?></p></div>
                </div>
            </div>
        </div>

        <!-- Progress Notes & Medications -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Progress Notes -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">Progress Notes</h2>
                    <button onclick="document.getElementById('noteModal').classList.remove('hidden')" 
                            class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                        Add Note
                    </button>
                </div>
                <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
                    <?php if (empty($progress_notes)): ?>
                        <p class="text-gray-500 text-center py-4">No progress notes yet</p>
                    <?php else: ?>
                        <?php foreach ($progress_notes as $note): ?>
                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                <div class="flex justify-between">
                                    <p class="text-sm font-semibold text-gray-800">
                                        <?php echo ucfirst($note['note_type']); ?> Note - 
                                        <?php echo htmlspecialchars($note['first_name'] . ' ' . $note['last_name']); ?>
                                    </p>
                                    <p class="text-xs text-gray-500"><?php echo date('M d, Y g:i A', strtotime($note['note_datetime'])); ?></p>
                                </div>
                                <?php if ($note['temperature'] || $note['blood_pressure_systolic']): ?>
                                    <div class="text-xs text-gray-600 mt-1">
                                        <?php if ($note['temperature']): ?>Temp: <?php echo $note['temperature']; ?>°C | <?php endif; ?>
                                        <?php if ($note['blood_pressure_systolic']): ?>BP: <?php echo $note['blood_pressure_systolic']; ?>/<?php echo $note['blood_pressure_diastolic']; ?> | <?php endif; ?>
                                        <?php if ($note['pulse_rate']): ?>Pulse: <?php echo $note['pulse_rate']; ?> bpm<?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <p class="text-sm text-gray-700 mt-2"><?php echo nl2br(htmlspecialchars($note['progress_note'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Medications -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-800">Medication Schedule</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($medications)): ?>
                        <p class="text-gray-500 text-center py-4">No medications scheduled</p>
                    <?php else: ?>
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left">Medication</th>
                                    <th class="px-4 py-2 text-left">Dose</th>
                                    <th class="px-4 py-2 text-left">Route</th>
                                    <th class="px-4 py-2 text-left">Scheduled</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($medications as $med): ?>
                                    <tr class="border-b">
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($med['medicine_name']); ?></td>
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($med['dose']); ?></td>
                                        <td class="px-4 py-2"><?php echo strtoupper($med['route']); ?></td>
                                        <td class="px-4 py-2"><?php echo date('M d g:i A', strtotime($med['scheduled_datetime'])); ?></td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                <?php echo $med['status'] === 'administered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                <?php echo ucfirst($med['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">
                                            <?php if ($med['status'] === 'scheduled'): ?>
                                                <form method="POST" action="/ipd/administer_medication/<?php echo $med['id']; ?>" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="text-blue-600 hover:text-blue-800 text-xs">Administer</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress Note Modal -->
<div id="noteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Progress Note</h3>
        <form method="POST" action="/ipd/record_progress_note/<?php echo $admission['id']; ?>">
            <?php echo csrf_field(); ?>
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-sm">Temperature (°C)</label><input type="number" step="0.1" name="temperature" class="w-full border rounded px-2 py-1 text-sm"></div>
                    <div><label class="text-sm">Pulse (bpm)</label><input type="number" name="pulse_rate" class="w-full border rounded px-2 py-1 text-sm"></div>
                    <div><label class="text-sm">BP Systolic</label><input type="number" name="bp_systolic" class="w-full border rounded px-2 py-1 text-sm"></div>
                    <div><label class="text-sm">BP Diastolic</label><input type="number" name="bp_diastolic" class="w-full border rounded px-2 py-1 text-sm"></div>
                </div>
                <div><label class="text-sm">Progress Note</label><textarea name="progress_note" rows="4" class="w-full border rounded px-2 py-1 text-sm"></textarea></div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('noteModal').classList.add('hidden')" 
                            class="bg-gray-300 px-4 py-2 rounded text-sm">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Discharge Modal -->
<div id="dischargeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Discharge Patient</h3>
        <form method="POST" action="/ipd/discharge/<?php echo $admission['id']; ?>">
            <?php echo csrf_field(); ?>
            <div class="space-y-3">
                <div><label class="text-sm">Discharge Diagnosis *</label><textarea name="discharge_diagnosis" rows="3" required class="w-full border rounded px-2 py-1 text-sm"></textarea></div>
                <div><label class="text-sm">Discharge Summary</label><textarea name="discharge_summary" rows="4" class="w-full border rounded px-2 py-1 text-sm"></textarea></div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('dischargeModal').classList.add('hidden')" 
                            class="bg-gray-300 px-4 py-2 rounded text-sm">Cancel</button>
                    <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded text-sm">Discharge</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
