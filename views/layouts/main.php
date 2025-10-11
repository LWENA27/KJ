<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) . ' - ' : ''; ?>Healthcare Management System</title>
    <!-- Local CSS Files -->
    <link rel="stylesheet" href="/assets/css/tailwind.css">
    <link rel="stylesheet" href="/assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="/assets/css/fonts.css">
    <!-- additional awaresome links -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/KJ/assets/css/tailwind.css">
    <!-- additional awaresome links -->

    <!-- PWA Manifest - Optional -->
    <!-- <link rel="manifest" href="/manifest.json"> -->
    <meta name="theme-color" content="#4facfe">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="KJ Healthcare">
    <!-- <link rel="apple-touch-icon" href="/assets/icons/icon-192x192.png"> -->

    <!-- Service Worker Registration - Optional -->
    <!--
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
    -->
    <style>
        /* Healthcare Design System */
        :root {
            --primary-50: #eff6ff;
            --primary-100: #dbeafe;
            --primary-500: #3b82f6;
            --primary-600: #2563eb;
            --primary-700: #1d4ed8;
            --success-50: #f0fdf4;
            --success-500: #22c55e;
            --success-600: #16a34a;
            --warning-50: #fffbeb;
            --warning-500: #f59e0b;
            --warning-600: #d97706;
            --error-50: #fef2f2;
            --error-500: #ef4444;
            --error-600: #dc2626;
            --neutral-50: #f8fafc;
            --neutral-100: #f1f5f9;
            --neutral-200: #e2e8f0;
            --neutral-300: #cbd5e1;
            --neutral-400: #94a3b8;
            --neutral-500: #64748b;
            --neutral-600: #475569;
            --neutral-700: #334155;
            --neutral-800: #1e293b;
            --neutral-900: #0f172a;
            --medical-accent: #10b981;
            --medical-secondary: #8b5cf6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--neutral-50);
            color: var(--neutral-700);
            line-height: 1.6;
        }

        /* Mobile-first responsive sidebar */
        .sidebar {
            transition: transform 0.3s ease-in-out;
            z-index: 30;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 20;
            display: none;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            color: var(--neutral-600);
            transition: all 0.2s ease-in-out;
            position: relative;
            margin-bottom: 2px;
        }

        .sidebar-link:hover {
            background-color: var(--primary-50);
            color: var(--primary-700);
            transform: translateX(2px);
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: white;
            border-radius: 0 2px 2px 0;
        }

        .sidebar-link .link-icon {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 1rem;
            transition: all 0.2s ease-in-out;
            flex-shrink: 0;
        }

        .sidebar-link:hover .link-icon {
            color: var(--primary-700);
        }

        .sidebar-link.active .link-icon {
            color: white;
        }

        /* Professional card components */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid var(--neutral-200);
            transition: all 0.2s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.1), 0 2px 4px 0 rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }

        /* Enhanced form components */
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--neutral-200);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s ease-in-out;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-input.error {
            border-color: var(--error-500);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        /* Medical status indicators */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-pending {
            background: var(--warning-50);
            color: var(--warning-600);
        }

        .status-active {
            background: var(--primary-50);
            color: var(--primary-600);
        }

        .status-completed {
            background: var(--success-50);
            color: var(--success-600);
        }

        .status-critical {
            background: var(--error-50);
            color: var(--error-600);
        }

        /* Medical priority indicators */
        .priority-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .priority-high {
            background: var(--error-500);
        }

        .priority-medium {
            background: var(--warning-500);
        }

        .priority-low {
            background: var(--success-500);
        }

        /* Professional buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            color: white;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: white;
            color: var(--neutral-700);
            border: 1.5px solid var(--neutral-200);
        }

        .btn-secondary:hover {
            background: var(--neutral-50);
            border-color: var(--neutral-300);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-500), var(--success-600));
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-500), var(--warning-600));
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--error-500), var(--error-600));
            color: white;
        }

        /* Enhanced scrollbars */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: var(--neutral-300);
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--neutral-400);
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: var(--neutral-100);
            border-radius: 3px;
        }

        /* Dark Mode Support */
        [data-theme="dark"] {
            --primary-50: #1e3a8a;
            --primary-500: #60a5fa;
            --primary-600: #3b82f6;
            --neutral-50: #1f2937;
            --neutral-100: #374151;
            --neutral-200: #4b5563;
            --neutral-300: #6b7280;
            --neutral-600: #d1d5db;
            --neutral-700: #e5e7eb;
            --neutral-800: #f3f4f6;
            --neutral-900: #ffffff;
            --success-500: #10b981;
            --warning-500: #f59e0b;
            --error-500: #ef4444;
        }

        [data-theme="dark"] body {
            background-color: var(--neutral-50);
            color: var(--neutral-700);
        }

        [data-theme="dark"] .sidebar {
            background-color: var(--neutral-100);
            border-color: var(--neutral-200);
        }

        [data-theme="dark"] .card,
        [data-theme="dark"] .bg-white {
            background-color: var(--neutral-100);
            border-color: var(--neutral-200);
        }

        /* Advanced Animations */
        @keyframes slideInFromLeft {
            0% {
                transform: translateX(-100%);
                opacity: 0;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideInFromRight {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }

            50% {
                transform: scale(1.05);
            }

            70% {
                transform: scale(0.9);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .animate-slide-in-left {
            animation: slideInFromLeft 0.5s ease-out;
        }

        .animate-slide-in-right {
            animation: slideInFromRight 0.5s ease-out;
        }

        .animate-bounce-in {
            animation: bounceIn 0.6s ease-out;
        }

        .animate-pulse {
            animation: pulse 2s infinite;
        }

        /* Advanced hover effects */
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .hover-lift:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Glassmorphism effects */
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        /* Advanced focus states */
        .focus-ring:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 0 0 1px rgba(59, 130, 246, 0.5);
        }

        /* Loading states */
        .loading {
            position: relative;
            overflow: hidden;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                left: -100%;
            }

            100% {
                left: 100%;
            }
        }

        /* Medical data tables */
        .medical-table {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .medical-table th {
            background: var(--neutral-50);
            padding: 1rem;
            font-weight: 600;
            color: var(--neutral-700);
            border-bottom: 1px solid var(--neutral-200);
        }

        .medical-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--neutral-100);
        }

        .medical-table tr:hover td {
            background: var(--primary-50);
        }

        /* Mobile responsive utilities */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                height: 100vh;
                transform: translateX(-100%);
                z-index: 50;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-content,
            .main-content-area {
                margin-left: 0 !important;
                padding: 1rem !important;
            }

            .card {
                margin: 0.5rem;
                border-radius: 8px;
            }

            .btn {
                padding: 0.625rem 1rem;
                font-size: 13px;
            }
        }

        /* Tablet adjustments */
        @media (min-width: 769px) and (max-width: 1024px) {
            .sidebar {
                width: 200px;
            }
        }

        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2),
        (min-resolution: 192dpi) {
            .card {
                border-width: 0.5px;
            }
        }

        /* Medicine card specific styles */
        .medicine-card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            transition: all 0.2s;
            border: 1px solid rgba(229, 231, 235, 1);
        }

        .medicine-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }

        .category-badge {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        /* Tab styles */
        .tab-button {
            color: rgb(107, 114, 128);
            border-color: transparent;
            transition: all 0.2s;
        }

        .tab-button:hover {
            color: rgb(55, 65, 81);
            border-color: rgb(209, 213, 219);
        }

        .tab-button.active {
            color: var(--primary-600);
            border-color: var(--primary-600);
        }

        .tab-content {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Focus management for accessibility */
        .focus\\:ring-medical:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            border-color: var(--medical-accent);
        }

        /* Professional animations */
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-up {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Top Header Bar Styles */
        .top-header {
            position: sticky;
            top: 0;
            z-index: 40;
            background: white;
            border-bottom: 1px solid var(--neutral-200);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .header-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s ease-in-out;
            color: var(--neutral-600);
            background: transparent;
            border: none;
            cursor: pointer;
            position: relative;
        }

        .header-action-btn:hover {
            background: var(--neutral-100);
            color: var(--neutral-800);
            transform: translateY(-1px);
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: var(--error-500);
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 9999px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .language-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid var(--neutral-200);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0;
            min-width: 120px;
            z-index: 50;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease-in-out;
        }

        .language-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .language-option {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            color: var(--neutral-700);
            text-decoration: none;
            transition: background 0.2s ease-in-out;
            font-size: 14px;
        }

        .language-option:hover {
            background: var(--primary-50);
            color: var(--primary-700);
        }

        .language-option.active {
            background: var(--primary-100);
            color: var(--primary-700);
            font-weight: 500;
        }

        .user-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid var(--neutral-200);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0;
            min-width: 200px;
            z-index: 50;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease-in-out;
        }

        .user-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-menu-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--neutral-700);
            text-decoration: none;
            transition: background 0.2s ease-in-out;
            font-size: 14px;
        }

        .user-menu-item:hover {
            background: var(--neutral-50);
        }

        .user-menu-item i {
            width: 1rem;
            margin-right: 0.75rem;
            color: var(--neutral-500);
        }

        /* Breadcrumb styles */
        .breadcrumb {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: var(--neutral-600);
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
        }

        .breadcrumb-item:not(:last-child)::after {
            content: '>';
            margin: 0 0.5rem;
            color: var(--neutral-400);
        }

        .breadcrumb-link {
            color: var(--neutral-600);
            text-decoration: none;
            transition: color 0.2s ease-in-out;
        }

        .breadcrumb-link:hover {
            color: var(--primary-600);
        }

        /* Fix for overlapping content and z-index issues */
        .sidebar {
            z-index: 30;
        }

        .top-header {
            z-index: 40;
        }

        .sidebar-overlay {
            z-index: 25;
        }

        /* Modal z-index fix and display control */
        .modal {
            z-index: 60 !important;
            display: none !important;
            /* hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex !important;
            /* show when toggled */
        }

        /* Ensure only direct modal content is styled, not any element with 'modal' in its class */
        .modal>.modal-content {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            max-height: 90vh;
            overflow-y: auto;
            margin: 20px;
            width: 100%;
            max-width: 500px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #6b7280;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
        }

        .modal-close:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        /* Content area positioning and z-index management */
        .main-content-area {
            position: relative;
            z-index: 1;
            padding: 1rem 1.5rem 2rem !important;
            min-height: calc(100vh - 70px) !important;
            overflow-x: hidden;
        }

        /* Ensure no conflicting styles override content area */
        main.main-content-area {
            padding: 1rem 1.5rem 2rem !important;
            margin: 0 !important;
        }

        /* Fix for content bleeding */
        main {
            background: transparent;
            position: relative;
        }

        /* Dropdown z-index fixes */
        .language-dropdown,
        .user-menu {
            z-index: 50;
        }
    </style>
</head>

<body class="bg-gray-100">
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Mobile overlay -->
        <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <div id="sidebar" class="sidebar w-64 bg-white shadow-xl min-h-screen flex flex-col border-r border-neutral-200">
                <!-- Header -->
                <div class="p-6 border-b border-neutral-200">
                    <div class="flex items-center justify-between">
                        <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/" class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-hospital-symbol text-white text-xl"></i>
                            </div>
                            <div>
                                <span class="text-xl font-bold text-neutral-800"><?php echo htmlspecialchars($_ENV['APP_NAME'] ?? 'KJ'); ?></span>
                                <div class="text-xs text-neutral-500">Healthcare System</div>
                            </div>
                        </a>
                        <!-- Mobile close button -->
                        <button id="closeSidebar" class="md:hidden p-2 rounded-lg hover:bg-neutral-100" onclick="toggleSidebar()">
                            <i class="fas fa-times text-neutral-600"></i>
                        </button>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex-1 p-4 overflow-y-auto custom-scrollbar">
                    <nav class="space-y-1">
                        <?php
                        $role = $_SESSION['user_role'];
                        $menu_items = [];
                        $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
                        $base = defined('BASE_PATH') ? trim(BASE_PATH, '/') : basename(dirname(__DIR__, 2));
                        if (strpos($current_path, $base . '/') === 0) {
                            $current_path = substr($current_path, strlen($base) + 1);
                        }

                        if ($role === 'admin') {
                            $menu_items = [
                                ['url' => 'admin/dashboard', 'icon' => 'fas fa-chart-line', 'text' => 'Dashboard'],
                                ['url' => 'admin/users', 'icon' => 'fas fa-user-shield', 'text' => 'User Management'],
                                ['url' => 'admin/patients', 'icon' => 'fas fa-user-injured', 'text' => 'Patients'],
                                ['url' => 'admin/medicines', 'icon' => 'fas fa-pills', 'text' => 'Medicines'],
                                ['url' => 'admin/tests', 'icon' => 'fas fa-microscope', 'text' => 'Lab Tests'],
                            ];
                        } elseif ($role === 'receptionist') {
                            $menu_items = [
                                ['url' => 'receptionist/dashboard', 'icon' => 'fas fa-chart-line', 'text' => 'Dashboard', 'badge' => '', 'color' => 'blue'],
                                ['url' => 'receptionist/patients', 'icon' => 'fas fa-users', 'text' => 'Patients', 'badge' => isset($sidebar_data['pending_patients']) ? $sidebar_data['pending_patients'] : '0', 'color' => 'blue'],
                                ['url' => 'receptionist/appointments', 'icon' => 'fas fa-calendar-check', 'text' => 'Appointments', 'badge' => isset($sidebar_data['upcoming_appointments']) ? $sidebar_data['upcoming_appointments'] : '0', 'color' => 'green'],
                                ['url' => 'receptionist/payments', 'icon' => 'fas fa-exclamation-circle', 'text' => 'Pending Payments', 'badge' => '', 'color' => 'red'],
                                ['url' => 'receptionist/payment_history', 'icon' => 'fas fa-history', 'text' => 'Payment History', 'badge' => '', 'color' => 'purple'],
                                ['url' => 'receptionist/medicine', 'icon' => 'fas fa-pills', 'text' => 'Medicine', 'badge' => isset($sidebar_data['low_stock_medicines']) && $sidebar_data['low_stock_medicines'] > 0 ? '!' : '', 'color' => 'yellow'],
                                ['url' => 'receptionist/reports', 'icon' => 'fas fa-chart-bar', 'text' => 'Reports', 'badge' => '', 'color' => 'indigo'],
                            ];
                        } elseif ($role === 'doctor') {
                            $menu_items = [
                                ['url' => 'doctor/dashboard', 'icon' => 'fas fa-chart-line', 'text' => 'Dashboard'],
                                ['url' => 'doctor/consultations', 'icon' => 'fas fa-stethoscope', 'text' => 'Consultations'],
                                ['url' => 'doctor/patients', 'icon' => 'fas fa-user-injured', 'text' => 'My Patients'],
                                ['url' => 'doctor/lab_results', 'icon' => 'fas fa-flask', 'text' => 'Lab Results'],
                            ];
                        } elseif ($role === 'lab_technician') {
                            $menu_items = [
                                ['url' => 'lab/dashboard', 'icon' => 'fas fa-chart-line', 'text' => 'Dashboard', 'badge' => '', 'color' => 'blue'],
                                ['url' => 'lab/tests', 'icon' => 'fas fa-vial', 'text' => 'Test Queue', 'badge' => '3', 'color' => 'yellow'],
                                ['url' => 'lab/results', 'icon' => 'fas fa-clipboard-check', 'text' => 'Record Results', 'badge' => '', 'color' => 'green'],
                                ['url' => 'lab/samples', 'icon' => 'fas fa-test-tube', 'text' => 'Sample Collection', 'badge' => '2', 'color' => 'purple'],
                                ['url' => 'lab/equipment', 'icon' => 'fas fa-microscope', 'text' => 'Equipment', 'badge' => '1', 'color' => 'indigo'],
                                ['url' => 'lab/inventory', 'icon' => 'fas fa-boxes', 'text' => 'Inventory', 'badge' => '!', 'color' => 'orange'],
                                ['url' => 'lab/quality', 'icon' => 'fas fa-check-double', 'text' => 'Quality Control', 'badge' => '', 'color' => 'emerald'],
                                ['url' => 'lab/reports', 'icon' => 'fas fa-chart-bar', 'text' => 'Reports', 'badge' => '', 'color' => 'rose'],
                            ];
                        }
                        ?>

                        <!-- Role badge -->
                        <div class="mb-6 p-3 bg-gradient-to-r from-neutral-50 to-neutral-100 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-medical-accent to-medical-secondary rounded-lg flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-neutral-800"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                                    <div class="text-xs text-neutral-500 capitalize"><?php echo htmlspecialchars($role); ?></div>
                                </div>
                            </div>
                        </div>

                        <?php foreach ($menu_items as $item):
                            $is_active = (strpos($current_path, $item['url']) !== false);
                            $color = isset($item['color']) ? $item['color'] : 'blue';
                            $badge = isset($item['badge']) ? $item['badge'] : '';
                        ?>
                            <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/<?php echo $item['url']; ?>"
                                class="group flex items-center justify-between px-3 py-2.5 rounded-lg transition-all duration-200 <?php echo $is_active ? 'bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 text-blue-700 shadow-sm' : 'text-neutral-600 hover:bg-gradient-to-r hover:from-neutral-50 hover:to-neutral-100 hover:text-neutral-800'; ?>"
                                onclick="closeSidebarOnMobile()">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200 <?php echo $is_active ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-sm' : 'bg-neutral-100 text-neutral-500 group-hover:bg-gradient-to-br group-hover:from-' . $color . '-500 group-hover:to-' . $color . '-600 group-hover:text-white'; ?>">
                                        <i class="<?php echo $item['icon']; ?> text-sm"></i>
                                    </div>
                                    <span class="font-medium text-sm"><?php echo $item['text']; ?></span>
                                </div>
                                <?php if ($badge): ?>
                                    <div class="flex items-center">
                                        <span class="<?php echo $badge === '!' ? 'bg-red-500 text-white' : 'bg-' . $color . '-500 text-white'; ?> text-xs font-bold px-2 py-1 rounded-full min-w-[20px] h-5 flex items-center justify-center">
                                            <?php echo $badge; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>

                <!-- Footer -->
                <div class="p-4 border-t border-neutral-200">
                    <a href="<?php echo htmlspecialchars($BASE_PATH); ?>/auth/logout"
                        class="flex items-center space-x-3 text-neutral-600 hover:text-error-600 transition-colors">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="font-medium">Logout</span>
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-h-screen">
                <!-- Top Header Bar -->
                <div class="top-header">
                    <div class="px-4 md:px-6 py-3">
                        <div class="flex items-center justify-between">
                            <!-- Left: Breadcrumb or Page Title -->
                            <div class="flex items-center space-x-4">
                                <!-- Mobile menu button -->
                                <button onclick="toggleSidebar()" class="md:hidden header-action-btn">
                                    <i class="fas fa-bars text-lg"></i>
                                </button>

                                <!-- Breadcrumb -->
                                <nav class="breadcrumb hidden md:flex">
                                    <?php
                                    $current_page = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
                                    $base = defined('BASE_PATH') ? trim(BASE_PATH, '/') : basename(dirname(__DIR__, 2));
                                    if (strpos($current_page, $base . '/') === 0) {
                                        $current_page = substr($current_page, strlen($base) + 1);
                                    }

                                    $breadcrumbs = [];
                                    $parts = explode('/', $current_page);

                                    if (count($parts) >= 1 && $parts[0]) {
                                        $breadcrumbs[] = ['text' => ucfirst($parts[0]), 'url' => $parts[0]];
                                        if (count($parts) >= 2 && $parts[1]) {
                                            $breadcrumbs[] = ['text' => ucfirst(str_replace('_', ' ', $parts[1])), 'url' => null];
                                        }
                                    }

                                    if (empty($breadcrumbs)) {
                                        $breadcrumbs[] = ['text' => 'Dashboard', 'url' => null];
                                    }
                                    ?>

                                    <div class="breadcrumb-item">
                                        <i class="fas fa-home text-neutral-400 mr-2"></i>
                                        <span class="text-neutral-800 font-medium">KJ Healthcare</span>
                                    </div>

                                    <?php foreach ($breadcrumbs as $index => $crumb): ?>
                                        <div class="breadcrumb-item">
                                            <?php if ($crumb['url'] && $index < count($breadcrumbs) - 1): ?>
                                                <a href="<?= htmlspecialchars($BASE_PATH) ?>/<?= htmlspecialchars($crumb['url']) ?>"
                                                    class="breadcrumb-link"><?= htmlspecialchars($crumb['text']) ?></a>
                                            <?php else: ?>
                                                <span class="text-neutral-800 font-medium"><?= htmlspecialchars($crumb['text']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </nav>

                                <!-- Mobile page title -->
                                <h1 class="md:hidden font-semibold text-neutral-800">
                                    <?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'KJ Healthcare' ?>
                                </h1>
                            </div>

                            <!-- Right: Actions -->
                            <div class="flex items-center space-x-2">
                                <!-- Theme Toggle -->
                                <div class="relative" id="themeDropdown">
                                    <button class="header-action-btn" onclick="toggleTheme()" title="Toggle Theme">
                                        <i id="themeIcon" class="fas fa-moon text-lg"></i>
                                    </button>
                                </div>

                                <!-- Search (Enhanced) -->
                                <div class="relative hidden lg:flex" id="globalSearch">
                                    <input type="text" id="searchInput" placeholder="Search patients, tests..."
                                        class="w-64 px-4 py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    <div id="searchResults" class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden z-50"></div>
                                </div>

                                <!-- Language Switch -->
                                <div class="relative" id="languageDropdown">
                                    <button class="header-action-btn" onclick="toggleLanguageDropdown()" title="Language">
                                        <i class="fas fa-globe text-lg"></i>
                                        <span class="ml-1 text-sm font-medium hidden sm:inline" id="currentLanguage">EN</span>
                                    </button>

                                    <div class="language-dropdown" id="languageMenu">
                                        <a href="#" class="language-option active" onclick="switchLanguage('en')">
                                            <i class="fas fa-flag-usa mr-2"></i>
                                            <span>English</span>
                                        </a>
                                        <a href="#" class="language-option" onclick="switchLanguage('sw')">
                                            <i class="fas fa-flag mr-2" style="color: #009639;"></i>
                                            <span>Kiswahili</span>
                                        </a>
                                    </div>
                                </div>

                                <!-- Notifications -->
                                <div class="relative" id="notificationDropdown">
                                    <?php $notifCount = isset($notifications) ? count($notifications) : 0; ?>
                                    <button class="header-action-btn" onclick="toggleNotifications()" title="Notifications">
                                        <i class="fas fa-bell text-lg"></i>
                                        <?php if ($notifCount > 0): ?>
                                            <span class="notification-badge" id="notificationCount"><?= $notifCount ?></span>
                                        <?php endif; ?>
                                    </button>

                                    <div class="user-menu" id="notificationMenu" style="min-width: 300px;">
                                        <div class="px-3 py-2 border-b border-neutral-200">
                                            <h3 class="font-semibold text-neutral-800">Notifications</h3>
                                            <p class="text-xs text-neutral-500">
                                                <?= $notifCount > 0 ? ("You have $notifCount notification" . ($notifCount > 1 ? 's' : '')) : 'No notifications' ?>
                                            </p>
                                        </div>

                                        <div class="max-h-64 overflow-y-auto">
                                            <?php if (!empty($notifications)): ?>
                                                <?php foreach ($notifications as $n): ?>
                                                    <div class="user-menu-item">
                                                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-3"
                                                            style="background: <?= ($n['type'] ?? 'info') === 'error' ? '#fee2e2' : ((($n['type'] ?? 'info') === 'warning') ? '#fef3c7' : '#dbeafe') ?>;">
                                                            <i class="fas <?= htmlspecialchars($n['icon'] ?? 'fa-info-circle') ?> text-xs"
                                                                style="color: <?= ($n['type'] ?? 'info') === 'error' ? '#ef4444' : ((($n['type'] ?? 'info') === 'warning') ? '#f59e0b' : '#2563eb') ?>;"></i>
                                                        </div>
                                                        <div class="flex-1">
                                                            <p class="text-sm font-medium text-neutral-800"><?= htmlspecialchars($n['title'] ?? 'Notification') ?></p>
                                                            <p class="text-xs text-neutral-500"><?= htmlspecialchars($n['message'] ?? '') ?></p>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="px-3 py-4 text-xs text-neutral-500">All caught up.</div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="px-3 py-2 border-t border-neutral-200">
                                            <a href="#" class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                                                View all notifications
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Menu -->
                                <div class="relative" id="userDropdown">
                                    <button class="header-action-btn" onclick="toggleUserMenu()" title="User Menu">
                                        <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                        <span class="ml-2 text-sm font-medium hidden sm:inline text-neutral-700">
                                            <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>
                                        </span>
                                        <i class="fas fa-chevron-down text-xs text-neutral-500 ml-1 hidden sm:inline"></i>
                                    </button>

                                    <div class="user-menu" id="userMenu">
                                        <div class="px-3 py-2 border-b border-neutral-200">
                                            <p class="text-sm font-medium text-neutral-800"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></p>
                                            <p class="text-xs text-neutral-500 capitalize"><?= htmlspecialchars($_SESSION['user_role'] ?? 'user') ?></p>
                                        </div>

                                        <a href="#" class="user-menu-item">
                                            <i class="fas fa-user-circle"></i>
                                            <span>My Profile</span>
                                        </a>

                                        <a href="#" class="user-menu-item">
                                            <i class="fas fa-cog"></i>
                                            <span>Settings</span>
                                        </a>

                                        <a href="#" class="user-menu-item">
                                            <i class="fas fa-question-circle"></i>
                                            <span>Help & Support</span>
                                        </a>

                                        <div class="border-t border-neutral-200 my-1"></div>

                                        <a href="<?= htmlspecialchars($BASE_PATH) ?>/auth/logout" class="user-menu-item text-red-600 hover:bg-red-50">
                                            <i class="fas fa-sign-out-alt"></i>
                                            <span>Logout</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile header (remove this as it's now in top header) -->

                <!-- Page content -->
                <main class="flex-1 overflow-auto custom-scrollbar main-content-area">
                    <div class="fade-in">
                        <?php echo $content; ?>
                    </div>
                </main>
            </div>
        </div>
    <?php else: ?>
        <!-- Login Page -->
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
            <div class="max-w-md w-full space-y-8">
                <?php echo $content; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Mobile Navigation JavaScript -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (sidebar && overlay) {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            }
        }

        function closeSidebarOnMobile() {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');

                if (sidebar && overlay) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                }
            }
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const toggleBtn = event.target.closest('[onclick*="toggleSidebar"]');

                if (sidebar && !sidebar.contains(event.target) && !toggleBtn) {
                    sidebar.classList.remove('open');
                    document.getElementById('sidebarOverlay').classList.remove('show');
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');

                if (sidebar && overlay) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                }
            }
        });

        // Header dropdown functions
        function toggleLanguageDropdown() {
            const dropdown = document.getElementById('languageMenu');
            const userMenu = document.getElementById('userMenu');
            const notificationMenu = document.getElementById('notificationMenu');

            // Close other dropdowns
            if (userMenu) userMenu.classList.remove('show');
            if (notificationMenu) notificationMenu.classList.remove('show');

            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        function toggleNotifications() {
            const dropdown = document.getElementById('notificationMenu');
            const userMenu = document.getElementById('userMenu');
            const languageMenu = document.getElementById('languageMenu');

            // Close other dropdowns
            if (userMenu) userMenu.classList.remove('show');
            if (languageMenu) languageMenu.classList.remove('show');

            if (dropdown) {
                dropdown.classList.toggle('show');
            }

            // Mark notifications as read (update badge)
            const badge = document.getElementById('notificationCount');
            if (badge && dropdown && dropdown.classList.contains('show')) {
                setTimeout(() => {
                    badge.style.display = 'none';
                }, 1000);
            }
        }

        function toggleUserMenu() {
            const dropdown = document.getElementById('userMenu');
            const languageMenu = document.getElementById('languageMenu');
            const notificationMenu = document.getElementById('notificationMenu');

            // Close other dropdowns
            if (languageMenu) languageMenu.classList.remove('show');
            if (notificationMenu) notificationMenu.classList.remove('show');

            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        // Dark mode toggle functionality
        function toggleTheme() {
            const currentTheme = localStorage.getItem('theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';

            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            const themeIcon = document.getElementById('themeIcon');
            if (themeIcon) {
                themeIcon.className = newTheme === 'dark' ? 'fas fa-sun text-lg' : 'fas fa-moon text-lg';
            }

            showToast(`Switched to ${newTheme} mode`, 'success');
        }

        // Global search functionality
        function initializeGlobalSearch() {
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            let searchTimeout;

            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    const query = e.target.value.trim();

                    if (query.length < 2) {
                        searchResults.classList.add('hidden');
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        performGlobalSearch(query);
                    }, 300);
                });

                searchInput.addEventListener('focus', function() {
                    if (this.value.length >= 2) {
                        searchResults.classList.remove('hidden');
                    }
                });

                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.add('hidden');
                    }
                });
            }
        }

        function performGlobalSearch(query) {
            // Simulate search results (in real implementation, this would be an AJAX call)
            const searchResults = document.getElementById('searchResults');
            const mockResults = [{
                    type: 'patient',
                    name: 'John Doe',
                    id: 'P-001',
                    info: 'Age 45, Last visit: Today'
                },
                {
                    type: 'test',
                    name: 'Blood Sugar',
                    id: 'T-001',
                    info: 'Category: Blood Tests'
                },
                {
                    type: 'medicine',
                    name: 'Paracetamol',
                    id: 'M-001',
                    info: 'Stock: 500 tablets'
                }
            ].filter(item => item.name.toLowerCase().includes(query.toLowerCase()));

            let resultsHTML = '';
            if (mockResults.length > 0) {
                mockResults.forEach(result => {
                    const icon = result.type === 'patient' ? 'fa-user' :
                        result.type === 'test' ? 'fa-flask' : 'fa-pills';
                    resultsHTML += `
                        <div class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center space-x-3">
                                <i class="fas ${icon} text-gray-400"></i>
                                <div>
                                    <div class="font-medium text-gray-900">${result.name}</div>
                                    <div class="text-sm text-gray-500">${result.info}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } else {
                resultsHTML = '<div class="p-3 text-gray-500 text-center">No results found</div>';
            }

            searchResults.innerHTML = resultsHTML;
            searchResults.classList.remove('hidden');
        }

        // Enhanced notification system with action buttons
        function showAdvancedNotification(title, message, type = 'info', actions = []) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white max-w-sm animate-slide-in-right`;

            const bgColors = {
                success: 'bg-gradient-to-r from-green-500 to-green-600',
                error: 'bg-gradient-to-r from-red-500 to-red-600',
                warning: 'bg-gradient-to-r from-yellow-500 to-yellow-600',
                info: 'bg-gradient-to-r from-blue-500 to-blue-600'
            };

            notification.classList.add(bgColors[type] || bgColors.info);

            let actionsHTML = '';
            if (actions.length > 0) {
                actionsHTML = '<div class="mt-3 flex space-x-2">';
                actions.forEach(action => {
                    actionsHTML += `<button onclick="${action.onclick}" class="px-3 py-1 bg-white bg-opacity-20 rounded text-sm hover:bg-opacity-30">${action.text}</button>`;
                });
                actionsHTML += '</div>';
            }

            notification.innerHTML = `
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="font-medium">${title}</div>
                        <div class="text-sm opacity-90 mt-1">${message}</div>
                        ${actionsHTML}
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-3 opacity-70 hover:opacity-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => notification.remove(), 300);
                }
            }, 7000);
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K for global search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.focus();
                }
            }

            // Ctrl/Cmd + D for dark mode toggle
            if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
                e.preventDefault();
                toggleTheme();
            }
        });

        // Initialize enhanced features
        document.addEventListener('DOMContentLoaded', function() {
            // Load saved theme
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);

            const themeIcon = document.getElementById('themeIcon');
            if (themeIcon) {
                themeIcon.className = savedTheme === 'dark' ? 'fas fa-sun text-lg' : 'fas fa-moon text-lg';
            }

            // Initialize global search
            initializeGlobalSearch();

            // Add loading states to forms
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                        submitBtn.disabled = true;
                    }
                });
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const languageDropdown = document.getElementById('languageDropdown');
            const userDropdown = document.getElementById('userDropdown');
            const notificationDropdown = document.getElementById('notificationDropdown');

            if (languageDropdown && !languageDropdown.contains(event.target)) {
                document.getElementById('languageMenu').classList.remove('show');
            }

            if (userDropdown && !userDropdown.contains(event.target)) {
                document.getElementById('userMenu').classList.remove('show');
            }

            if (notificationDropdown && !notificationDropdown.contains(event.target)) {
                document.getElementById('notificationMenu').classList.remove('show');
            }

            // Close sidebar on mobile
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const toggleBtn = event.target.closest('[onclick*="toggleSidebar"]');

                if (sidebar && !sidebar.contains(event.target) && !toggleBtn) {
                    sidebar.classList.remove('open');
                    document.getElementById('sidebarOverlay').classList.remove('show');
                }
            }
        });

        // Language switcher function
        function switchLanguage(lang) {
            // Store preference
            localStorage.setItem('preferred_language', lang);
            
            // Update active state
            document.querySelectorAll('.language-option').forEach(option => {
                option.classList.remove('active');
                if (option.getAttribute('onclick').includes(`'${lang}'`)) {
                    option.classList.add('active');
                }
            });
            
            // Here you would apply translations
            console.log('Language switched to:', lang);
        }

        // Load saved language preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedLang = localStorage.getItem('preferred_language') || 'en';
            if (typeof switchLanguage === 'function') {
                switchLanguage(savedLang);
            }
        });

        // Toast notification system
        function showToast(message, type = 'info', duration = 5000) {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white max-w-sm slide-up`;

            const bgColors = {
                success: 'bg-gradient-to-r from-green-500 to-green-600',
                error: 'bg-gradient-to-r from-red-500 to-red-600',
                warning: 'bg-gradient-to-r from-yellow-500 to-yellow-600',
                info: 'bg-gradient-to-r from-blue-500 to-blue-600'
            };

            const icons = {
                success: 'fas fa-check-circle',
                error: 'fas fa-exclamation-triangle',
                warning: 'fas fa-exclamation-circle',
                info: 'fas fa-info-circle'
            };

            toast.classList.add(bgColors[type] || bgColors.info);
            toast.innerHTML = `
                <div class="flex items-center space-x-3">
                    <i class="${icons[type] || icons.info}"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-auto">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast.parentNode) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => toast.remove(), 300);
                }
            }, duration);
        }

        // Show flash messages as toasts
        <?php if (isset($_SESSION['success'])): ?>
            showToast('<?php echo addslashes($_SESSION['success']); ?>', 'success');
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            showToast('<?php echo addslashes($_SESSION['error']); ?>', 'error');
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['warning'])): ?>
            showToast('<?php echo addslashes($_SESSION['warning']); ?>', 'warning');
            <?php unset($_SESSION['warning']); ?>
        <?php endif; ?>

        // Loading state management
        function setLoading(element, isLoading = true) {
            if (isLoading) {
                element.classList.add('loading');
                element.disabled = true;
                const originalText = element.textContent;
                element.dataset.originalText = originalText;
                element.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
            } else {
                element.classList.remove('loading');
                element.disabled = false;
                element.textContent = element.dataset.originalText || 'Submit';
            }
        }

        // Form enhancement
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus first input on forms
            const firstInput = document.querySelector('form input:not([type="hidden"]):not([disabled])');
            if (firstInput) {
                firstInput.focus();
            }

            // Enhanced form submission
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn) {
                        setLoading(submitBtn, true);
                    }
                });
            });

            // Animate elements on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.card, .medical-table').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>

</html>