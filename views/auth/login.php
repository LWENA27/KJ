<div class="glassmorphism p-8 rounded-xl shadow-2xl">
    <div class="text-center">
        <i class="fas fa-hospital-symbol text-4xl text-blue-600 mb-4"></i>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">KJ System</h2>
        <p class="text-gray-600 mb-8">Securely sign in to your account</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($BASE_PATH); ?>/auth/login" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-user mr-2"></i>Username
            </label>
            <input type="text" id="username" name="username" required autofocus
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-lock mr-2"></i>Password
            </label>
            <input type="password" id="password" name="password" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md transition duration-200 shadow-sm hover:shadow-md">
            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
        </button>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
        <p>Demo Accounts:</p>
        <p>Admin: admin / password</p>
        <p>Receptionist: receptionist1 / password</p>
        <p>Doctor: doctor1 / password</p>
        <p>Lab Tech: lab1 / password</p>
    </div>
</div>
