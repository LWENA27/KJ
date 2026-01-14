<?php
$title = 'Prescriptions - Pharmacy';
?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-neutral-800">Prescriptions</h1>
            <p class="text-neutral-500 mt-1">Dispense medicines to patients</p>
        </div>
    </div>

    <!-- Pending Prescriptions by Patient -->
    <?php if (empty($pending_patients)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-12 text-center">
        <i class="fas fa-check-circle text-6xl text-green-400 mb-4"></i>
        <h3 class="text-xl font-semibold text-neutral-800 mb-2">No Pending Prescriptions</h3>
        <p class="text-neutral-500">All prescriptions have been dispensed</p>
    </div>
    <?php else: ?>
    <div class="grid gap-6">
        <?php foreach ($pending_patients as $patient): ?>
        <div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden">
            <!-- Patient Header -->
            <div class="p-6 border-b border-neutral-200 bg-gradient-to-r from-neutral-50 to-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            <?= strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-neutral-800">
                                <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?>
                            </h3>
                            <p class="text-sm text-neutral-500"><?= htmlspecialchars($patient['registration_number']) ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm text-neutral-500"><?= $patient['medicine_count'] ?> medicine(s)</p>
                            <p class="text-lg font-bold text-neutral-800">TZS <?= number_format($patient['total_cost'], 0) ?></p>
                        </div>
                        <?php if ($patient['is_paid']): ?>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                            <i class="fas fa-check-circle mr-1"></i>Paid
                        </span>
                        <?php else: ?>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">
                            <i class="fas fa-clock mr-1"></i>Payment Pending
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Prescriptions List -->
            <form action="<?= htmlspecialchars($BASE_PATH) ?>/pharmacist/dispense" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="patient_id" value="<?= $patient['patient_id'] ?>">
                
                <div class="p-6">
                    <table class="w-full">
                        <thead class="bg-neutral-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Medicine</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Dosage</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Prescribed</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Dispensed</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-neutral-500 uppercase">Dispense</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            <?php foreach ($patient['prescriptions'] as $rx): ?>
                            <tr class="hover:bg-neutral-50">
                                <td class="px-4 py-4">
                                    <p class="font-medium text-neutral-800"><?= htmlspecialchars($rx['medicine_name']) ?></p>
                                    <p class="text-xs text-neutral-500"><?= htmlspecialchars($rx['generic_name'] ?? '') ?></p>
                                </td>
                                <td class="px-4 py-4 text-neutral-600 text-sm">
                                    <?= htmlspecialchars($rx['dosage'] ?? '-') ?>
                                    <?php if ($rx['frequency']): ?>
                                    <br><span class="text-xs text-neutral-400"><?= htmlspecialchars($rx['frequency']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-neutral-800 font-medium">
                                    <?= $rx['quantity_prescribed'] ?>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="<?= $rx['quantity_dispensed'] > 0 ? 'text-green-600' : 'text-neutral-400' ?>">
                                        <?= $rx['quantity_dispensed'] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <?php $remaining = $rx['quantity_prescribed'] - $rx['quantity_dispensed']; ?>
                                    <?php if ($rx['stock_available'] >= $remaining): ?>
                                    <span class="text-green-600"><?= $rx['stock_available'] ?></span>
                                    <?php elseif ($rx['stock_available'] > 0): ?>
                                    <span class="text-yellow-600"><?= $rx['stock_available'] ?></span>
                                    <?php else: ?>
                                    <span class="text-red-600">Out of stock</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4">
                                    <?php $max_dispense = min($remaining, $rx['stock_available']); ?>
                                    <input type="number" 
                                           name="dispensed_items[<?= $rx['prescription_id'] ?>]" 
                                           value="<?= $max_dispense ?>"
                                           min="0" 
                                           max="<?= $max_dispense ?>"
                                           class="w-20 px-2 py-1 border border-neutral-300 rounded text-center focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                           <?= $max_dispense <= 0 ? 'disabled' : '' ?>>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Actions -->
                <div class="px-6 py-4 bg-neutral-50 border-t border-neutral-200 flex justify-end gap-3">
                    <?php if (!$patient['is_paid']): ?>
                    <p class="text-sm text-yellow-600 mr-auto">
                        <i class="fas fa-info-circle mr-1"></i>
                        Payment required before dispensing (or dispense with override)
                    </p>
                    <?php endif; ?>
                    <button type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all shadow-sm">
                        <i class="fas fa-pills mr-2"></i>Dispense Medicines
                    </button>
                </div>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
