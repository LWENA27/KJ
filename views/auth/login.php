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
            <div class="relative">
                <input type="password" id="password" name="password" required 
                       class="w-full px-3 py-2 pr-12 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center justify-center w-12 h-full text-gray-500 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-r-md transition-colors duration-200 bg-gray-50 hover:bg-blue-50">
                    <i id="eyeIcon" class="fas fa-eye text-lg"></i>
                    <span id="eyeFallback" style="display: none;">👁️</span>
                </button>
            </div>
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

<script>
// Password visibility toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeFallback = document.getElementById('eyeFallback');

    if (togglePassword && passwordField) {
        // Check if Font Awesome is loaded, if not use emoji fallback
        const checkFontAwesome = () => {
            const testIcon = document.createElement('i');
            testIcon.className = 'fas fa-eye';
            testIcon.style.position = 'absolute';
            testIcon.style.left = '-9999px';
            document.body.appendChild(testIcon);
            
            const computedStyle = window.getComputedStyle(testIcon, ':before');
            const isLoaded = computedStyle.getPropertyValue('content') !== 'none' && computedStyle.getPropertyValue('content') !== '';
            
            document.body.removeChild(testIcon);
            return isLoaded;
        };

        // Set up icon display
        setTimeout(() => {
            if (!checkFontAwesome() && eyeFallback) {
                eyeIcon.style.display = 'none';
                eyeFallback.style.display = 'inline';
            }
        }, 100);

        // Add CSS for smooth transitions
        const style = document.createElement('style');
        style.textContent = `
            #togglePassword {
                transition: all 0.2s ease;
                border-left: 1px solid #e5e7eb;
            }
            #togglePassword:hover {
                background-color: rgba(59, 130, 246, 0.1) !important;
                color: #2563eb !important;
            }
            #eyeIcon, #eyeFallback {
                transition: transform 0.2s ease;
            }
        `;
        document.head.appendChild(style);

        togglePassword.addEventListener('click', function() {
            // Toggle the password field type
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            // Toggle the icon with animation
            const activeIcon = eyeIcon.style.display !== 'none' ? eyeIcon : eyeFallback;
            activeIcon.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                if (eyeIcon.style.display !== 'none') {
                    // Using Font Awesome icons
                    if (type === 'password') {
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                        togglePassword.setAttribute('title', 'Show password');
                    } else {
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                        togglePassword.setAttribute('title', 'Hide password');
                    }
                } else {
                    // Using emoji fallback
                    if (type === 'password') {
                        eyeFallback.textContent = '👁️';
                        togglePassword.setAttribute('title', 'Show password');
                    } else {
                        eyeFallback.textContent = '🙈';
                        togglePassword.setAttribute('title', 'Hide password');
                    }
                }
                activeIcon.style.transform = 'scale(1)';
            }, 100);
        });

        // Set initial tooltip
        togglePassword.setAttribute('title', 'Show password');
        
        // Add click feedback
        togglePassword.addEventListener('mousedown', function() {
            const activeIcon = eyeIcon.style.display !== 'none' ? eyeIcon : eyeFallback;
            activeIcon.style.transform = 'scale(0.9)';
        });
        
        togglePassword.addEventListener('mouseup', function() {
            const activeIcon = eyeIcon.style.display !== 'none' ? eyeIcon : eyeFallback;
            activeIcon.style.transform = 'scale(1)';
        });
    }
});
</script>
