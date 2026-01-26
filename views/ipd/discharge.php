<?php
$pageTitle = "Discharge Patient";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Discharge Patient</h1>
            <a href="<?php echo BASE_PATH; ?>/ipd/view_admission/<?php echo $admission['id']; ?>" class="text-blue-600 hover:text-blue-800">← Back to Admission</a>
        </div>

        <!-- Patient Summary Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Patient Summary</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-600">Patient Name</p>
                    <p class="font-semibold text-lg"><?php echo htmlspecialchars($admission['first_name'] . ' ' . $admission['last_name']); ?></p>
                </div>
                <div>
                    <p class="text-gray-600">Patient Number</p>
                    <p class="font-semibold"><?php echo htmlspecialchars($admission['patient_number']); ?></p>
                </div>
                <div>
                    <p class="text-gray-600">Admission Number</p>
                    <p class="font-semibold"><?php echo htmlspecialchars($admission['admission_number']); ?></p>
                </div>
                <div>
                    <p class="text-gray-600">Ward / Bed</p>
                    <p class="font-semibold"><?php echo htmlspecialchars($admission['ward_name'] . ' - ' . $admission['bed_number']); ?></p>
                </div>
                <div>
                    <p class="text-gray-600">Admission Date</p>
                    <p class="font-semibold"><?php echo date('M d, Y g:i A', strtotime($admission['admission_datetime'])); ?></p>
                </div>
                <div>
                    <p class="text-gray-600">Length of Stay</p>
                    <p class="font-semibold text-blue-600"><?php echo $admission['total_days']; ?> days</p>
                </div>
                <div class="col-span-2">
                    <p class="text-gray-600">Admission Diagnosis</p>
                    <p class="font-semibold"><?php echo nl2br(htmlspecialchars($admission['admission_diagnosis'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Discharge Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Discharge Details</h2>
            <form method="POST" action="/ipd/process_discharge/<?php echo $admission['id']; ?>">
                <?php echo csrf_field(); ?>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Discharge Date & Time *
                        </label>
                        <input type="datetime-local" name="discharge_datetime" required 
                               value="<?php echo date('Y-m-d\TH:i'); ?>"
                               class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Discharge Diagnosis *
                        </label>
                        <textarea name="discharge_diagnosis" rows="4" required
                                  class="w-full border border-gray-300 rounded px-3 py-2"
                                  placeholder="Final diagnosis at discharge"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Enter the final confirmed diagnosis</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Condition at Discharge *
                        </label>
                        <select name="discharge_condition" required class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="">Select condition...</option>
                            <option value="improved">Improved</option>
                            <option value="cured">Cured</option>
                            <option value="stable">Stable</option>
                            <option value="deteriorated">Deteriorated</option>
                            <option value="deceased">Deceased</option>
                            <option value="absconded">Absconded</option>
                            <option value="transferred">Transferred</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Discharge Summary *
                        </label>
                        <textarea name="discharge_summary" rows="6" required
                                  class="w-full border border-gray-300 rounded px-3 py-2"
                                  placeholder="Brief summary of hospital course, treatment provided, and outcome"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Include hospital course, procedures, treatments, and complications if any</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Discharge Instructions
                        </label>
                        <textarea name="discharge_instructions" rows="4"
                                  class="w-full border border-gray-300 rounded px-3 py-2"
                                  placeholder="Instructions for patient care at home, medications, follow-up"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Home care instructions, medication schedule, activity restrictions</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Follow-up Required
                        </label>
                        <select name="followup_required" class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="no">No Follow-up</option>
                            <option value="yes">Yes - Schedule Follow-up</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Follow-up Date
                        </label>
                        <input type="date" name="followup_date" 
                               class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Notes
                        </label>
                        <textarea name="discharge_notes" rows="3"
                                  class="w-full border border-gray-300 rounded px-3 py-2"
                                  placeholder="Any additional notes or observations"></textarea>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                        <p class="text-sm text-yellow-800">
                            <strong>Note:</strong> Discharging this patient will release the bed (<?php echo htmlspecialchars($admission['bed_number']); ?>) 
                            and mark it as available for new admissions. This action cannot be undone.
                        </p>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="<?php echo BASE_PATH; ?>/ipd/view_admission/<?php echo $admission['id']; ?>" 
                           class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-orange-600 text-white px-6 py-2 rounded hover:bg-orange-700 font-semibold">
                            Confirm Discharge
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
