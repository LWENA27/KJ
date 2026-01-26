<?php
// This is a simplified header for standalone pages
// For full application pages, use main.php layout with BaseController->render()

if (!defined('BASE_PATH')) {
    define('BASE_PATH', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>Healthcare Management System</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/tailwind.css">
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Top Navigation Bar -->
    <!-- <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-blue-600">Healthcare System</span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                    </span>
                    <a href="<?php echo BASE_PATH; ?>/auth/logout" class="text-sm text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div> -->
    </nav>
