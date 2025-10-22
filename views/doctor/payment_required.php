<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900">Payment Required</h1>
        <a href="/KJ/doctor/patients" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
            <i class="fas fa-arrow-left mr-2"></i>Back to Patients
        </a>
    </div>

    <!-- Payment Required Alert -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                <i class="fas fa-lock text-yellow-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-medium text-yellow-800">Payment Required</h3>
                <p class="text-yellow-700 mt-1"><?php echo htmlspecialchars($message); ?></p>
            </div>
        </div>
    </div>

    <!-- Payment Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Process <?php echo ucfirst(str_replace('_', ' ', $step)); ?> Payment</h3>

        <form method="POST" action="/KJ/doctor/process_consultation_payment" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Payment Amount (TZS)</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select id="payment_method" name="payment_method" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="insurance">Insurance</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="/KJ/doctor/patients" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
                    <i class="fas fa-credit-card mr-2"></i>Process Payment
                </button>
            </div>
        </form>
    </div>

    <!-- Workflow Status -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Workflow Progress</h3>
        <div class="space-y-3">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <span class="text-sm text-gray-600">Patient Registration - Completed</span>
            </div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <span class="text-sm font-medium text-gray-900">Consultation Payment - Pending</span>
            </div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <span class="text-sm text-gray-400">Lab Tests - Locked</span>
            </div>
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <span class="text-sm text-gray-400">Results Review - Locked</span>
            </div>
        </div>
    </div>
</div>
