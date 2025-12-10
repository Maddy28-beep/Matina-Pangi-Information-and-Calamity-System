<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Barangay Matina Pangi Information and Calamity System'); ?></title>
    
    <!-- Google Fonts - Inter (Professional & Clean) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons - Updated Version -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>?v=<?php echo e(time()); ?>">
    
    <style>
        /* Select2 Custom Styling */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px !important;
            border: 1px solid #dee2e6 !important;
            font-size: 1rem !important;
        }
        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: #dee2e6 !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
            border: 1px solid #dee2e6 !important;
            padding: 0.5rem !important;
        }
        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: var(--color-primary, #4A6F52) !important;
            color: white !important;
        }
        
        /* Clean Modal-style Select2 Dropdown */
        .select2-dropdown-modal {
            position: fixed !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            width: 90% !important;
            max-width: 900px !important;
            max-height: 80vh !important;
            z-index: 9999 !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 1.5rem 4rem rgba(0, 0, 0, 0.4) !important;
            border: none !important;
            overflow: hidden !important;
        }
        
        /* Search Header - Clean & Professional */
        .select2-dropdown-modal .select2-search--dropdown {
            padding: 1.5rem !important;
            background: linear-gradient(135deg, #4A6F52 0%, #3d5a43 100%) !important;
            border-bottom: none !important;
        }
        
        .select2-dropdown-modal .select2-search__field {
            height: 50px !important;
            font-size: 1.05rem !important;
            border-radius: 0.5rem !important;
            border: 2px solid rgba(255, 255, 255, 0.3) !important;
            background: white !important;
            padding: 0.75rem 1rem !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
        }
        
        .select2-dropdown-modal .select2-search__field::placeholder {
            color: #6c757d !important;
            font-weight: 400 !important;
        }
        
        .select2-dropdown-modal .select2-search__field:focus {
            border-color: #fff !important;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2) !important;
        }
        
        /* Results Container - Clean List */
        .select2-dropdown-modal .select2-results {
            max-height: calc(80vh - 120px) !important;
            overflow-y: auto !important;
            background: #fff !important;
        }
        
        .select2-dropdown-modal .select2-results__options {
            padding: 0.5rem !important;
        }
        
        .select2-dropdown-modal .select2-results__option {
            padding: 1rem 1.25rem !important;
            font-size: 0.95rem !important;
            border-bottom: 1px solid #f0f0f0 !important;
            border-radius: 0.375rem !important;
            margin-bottom: 0.25rem !important;
            transition: all 0.15s ease !important;
            line-height: 1.6 !important;
            color: #2c3e50 !important;
        }
        
        .select2-dropdown-modal .select2-results__option:hover {
            background: #f8f9fa !important;
            border-left: 3px solid #4A6F52 !important;
            padding-left: 1.5rem !important;
        }
        
        .select2-dropdown-modal .select2-results__option--highlighted {
            background: #4A6F52 !important;
            color: white !important;
            border-left: 3px solid #2d4532 !important;
            font-weight: 500 !important;
        }
        
        .select2-dropdown-modal .select2-results__option[aria-selected="true"] {
            background: #e8f3ed !important;
            color: #2d4532 !important;
            font-weight: 600 !important;
            border-left: 3px solid #4A6F52 !important;
        }
        
        /* Loading & No Results - Clean Messages */
        .select2-dropdown-modal .select2-results__message {
            padding: 2rem !important;
            text-align: center !important;
            color: #6c757d !important;
            font-size: 1rem !important;
        }
        
        /* Backdrop - Transparent */
        .select2-container--open + .select2-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: transparent;
            z-index: 9998;
        }
        
        /* Scrollbar Styling */
        .select2-dropdown-modal .select2-results::-webkit-scrollbar {
            width: 8px;
        }
        
        .select2-dropdown-modal .select2-results::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .select2-dropdown-modal .select2-results::-webkit-scrollbar-thumb {
            background: #4A6F52;
            border-radius: 10px;
        }
        
        .select2-dropdown-modal .select2-results::-webkit-scrollbar-thumb:hover {
            background: #3d5a43;
        }
    </style>

    <style>
        /* Global Required Field Asterisk Styling */
        .text-danger, span.text-danger {
            color: #dc3545 !important;
            font-weight: bold;
        }
        
        :root {
            --color-primary: #4A6F52;
            --color-primary-light: #e8f3ed;
            --color-primary-hover: #3d5a43;
            --color-bg: #f5f7fb;
            --color-border: #e5e7eb;
            --color-text: #1f2937;
            --color-text-muted: #6b7280;
            --color-success: #10b981;
            --color-warning: #f59e0b;
            --color-danger: #dc3545;
            --spacing-xs: 0.5rem;
            --spacing-sm: 0.75rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --spacing-2xl: 3rem;
            --shadow-sm: 0 1px 2px 0 rgba(15, 23, 42, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.1), 0 2px 4px -1px rgba(15, 23, 42, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(15, 23, 42, 0.08), 0 4px 6px -4px rgba(15, 23, 42, 0.03);
            --shadow-xl: 0 20px 25px -5px rgba(15, 23, 42, 0.15), 0 10px 10px -5px rgba(15, 23, 42, 0.04);
            --transition-fast: all 0.15s ease;
            --transition-base: all 0.2s ease;
            --transition-slow: all 0.3s ease;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --navbar-height: 64px;
            --sidebar-collapsed: 4rem;
            --sidebar-expanded: 15rem;
            --sidebar-transition: width 0.28s ease;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            padding: 12px var(--spacing-md) !important;
            margin: 4px 0;
            color: var(--color-text) !important;
            font-weight: 500;
            font-size: 0.9rem;
            border-radius: 10px;
            transition: var(--transition-fast) !important;
            text-decoration: none;
            position: relative;
            width: 100%;
            overflow: visible;
        }

        .sidebar .nav-link:hover {
            background: rgba(74, 111, 82, 0.08);
            color: #3d5a43;
        }

        .sidebar .nav-link.active {
            background: #4A6F52;
            color: #FFFFFF !important;
            font-weight: 600;
        }

        .sidebar .nav-link.active i,
        .sidebar .nav-link.active span,
        .sidebar .nav-link.active .bi-chevron-right,
        .sidebar .nav-link.active .nav-link-text {
            color: #FFFFFF !important;
        }

        .sidebar .nav-link.active .bi-chevron-right {
            opacity: 1;
        }

        .sidebar .nav-link .badge {
            margin-left: auto;
            margin-right: 0.5rem;
        }

        /* Improved text readability with world-class typography */
        body { 
            color: var(--color-text) !important; 
            font-weight: 500 !important;
            background-color: var(--color-bg) !important;
            min-height: 100vh;
            font-family: 'Inter', 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Status Badges */
        .badge.bg-success,
        .badge.text-bg-success {
            font-weight: 600;
        }
        
        .badge.bg-warning,
        .badge.text-bg-warning {
            font-weight: 600;
        }
        
        .badge.bg-danger,
        .badge.text-bg-danger {
            font-weight: 600;
        }
        
        .badge.bg-dark,
        .badge.text-bg-dark {
            font-weight: 600;
        }
        
        .badge.bg-info,
        .badge.text-bg-info {
            font-weight: 600;
        }
        
        .badge.bg-secondary,
        .badge.text-bg-secondary {
            font-weight: 600;
        }
        
        .badge.bg-primary,
        .badge.text-bg-primary {
            font-weight: 600;
        }
        
        /* Toast Success Message - Light Green */
        .toast-success {
            background-color: #dcfce7 !important; /* light green */
            border: 1px solid #a7f3d0 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
            min-width: 320px;
        }
        
        .toast-success .toast-header {
            background-color: #dcfce7 !important; /* light green */
            border-bottom: none !important;
            color: #047857 !important; /* dark green text */
            font-weight: 600 !important;
            padding: 1rem 1rem 0.5rem 1rem !important;
        }
        
        .toast-success .toast-header i {
            color: #10B981 !important;
            font-size: 1.25rem !important;
        }
        
        .toast-success .toast-body {
            background-color: #dcfce7 !important; /* light green */
            color: #065f46 !important;
            font-weight: 500 !important;
            font-size: 0.875rem !important;
            padding: 0.5rem 1rem 1rem 1rem !important;
        }
        
        .toast-success .btn-close {
            font-size: 0.75rem !important;
        }
        }
        
        .toast-success .bi-check-circle-fill {
            color: #10B981 !important;
        }
        
        .badge-role-secretary,
        .badge-role-staff {
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.75rem;
        }
        
        /* Old navbar styles removed - now using professional admin design */
        /* Old sidebar styles removed - now using professional admin design */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: #4A6F52;
            border-radius: 3px;
        }
        
        /* Icon styling */
        .sidebar .nav-link i {
            font-size: 22px;
            width: 26px;
            height: 26px;
            text-align: center;
            color: #4A6F52;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            position: relative;
        }
        
        /* Remove default focus outline for sidebar links */
        .sidebar .nav-link,
        .sidebar .nav-link:link,
        .sidebar .nav-link:visited,
        .sidebar .nav-link:hover,
        .sidebar .nav-link:active,
        .sidebar .nav-link:focus,
        .sidebar .nav-link:focus-visible,
        .sidebar a,
        .sidebar a:link,
        .sidebar a:visited,
        .sidebar a:hover,
        .sidebar a:active,
        .sidebar a:focus,
        .sidebar a:focus-visible {
            outline: none !important;
            box-shadow: none !important;
        }

        .sidebar nav {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .sidebar nav .collapse {
            border-left: 2px solid rgba(63, 111, 89, 0.15);
            margin-left: 1.75rem;
            margin-top: 0.35rem;
            padding-left: 0.75rem;
            overflow: visible;
        }

        .sidebar nav .collapse.show {
            display: flex !important;
            flex-direction: column;
            gap: 4px;
        }

        .sidebar nav .collapse .nav-link {
            font-size: 0.85rem;
            padding: 10px 12px !important;
        }

        .sidebar nav .collapse .nav-link i {
            font-size: 18px;
            margin-right: 10px;
        }

        .sidebar nav .collapse .nav-link .badge {
            margin-right: 0;
        }

        .sidebar nav .collapse .nav-link:hover {
            transform: translateX(4px);
        }

        .sidebar nav .collapse .nav-link.active {
            box-shadow: inset 3px 0 0 #4A6F52;
        }

        .health-management-parent .collapse .nav-link span {
            color: #374151 !important;
        }
        
        /* Force text visibility for all submenu items */
        #healthManagementSubmenu .nav-link {
            background: #ffffff !important;
            color: #374151 !important;
        }
        
        #healthManagementSubmenu .nav-link:hover {
            background: #f0f7f4 !important;
            color: #3d5a43 !important;
        }
        
        #healthManagementSubmenu .nav-link span {
            color: #374151 !important;
        }
        
        /* ========================================
           HEALTH MODULE CONTENT SPACING - Universal Fix
           ======================================== */
        
        /* Apply to all health module pages */
        .health-module-content {
            margin-left: 4rem !important;
            padding-right: 2rem !important;
            margin-top: -1rem !important;
            padding-top: 2rem !important;
            padding-left: 1rem !important;
        }
        
        /* Responsive spacing for health modules */
        @media (max-width: 768px) {
            .health-module-content {
                margin-left: 2.5rem !important;
                padding-right: 1rem !important;
                padding-left: 0.5rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .health-module-content {
                margin-left: 1.5rem !important;
                padding-right: 0.5rem !important;
                padding-left: 0.25rem !important;
            }
        }

        /* ========================================
           FIX TRANSPARENT BUTTON ISSUE GLOBALLY
           ======================================== */
        
        /* Override Bootstrap's default button opacity changes */
        .health-module-content .btn:hover,
        .health-module-content .btn:focus,
        .health-module-content .btn:active,
        .health-module-content .btn.active,
        .health-module-content .btn:focus-visible {
            opacity: 1 !important;
        }

        .health-module-content .btn {
            opacity: 1 !important;
        }

        /* Enhanced button interactions without transparency */
        .health-module-content .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .health-module-content .btn:active {
            transform: translateY(0px);
        }

        /* NUCLEAR OPTION - Remove ALL white effects globally */
        .health-module-content .btn:active,
        .health-module-content .btn:focus,
        .health-module-content .btn:hover,
        .health-module-content .btn.active {
            background-image: none !important;
            background-blend-mode: normal !important;
            filter: none !important;
            box-shadow: none !important;
            outline: none !important;
        }

        /* Force specific button colors when active */
        .health-module-content .btn-primary:active,
        .health-module-content .btn-primary:focus {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
        }

        .health-module-content .btn-warning:active,
        .health-module-content .btn-warning:focus {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
        }

        .health-module-content .btn-success:active,
        .health-module-content .btn-success:focus {
            background-color: #198754 !important;
            border-color: #198754 !important;
        }

        .health-module-content .btn-info:active,
        .health-module-content .btn-info:focus {
            background-color: #0dcaf0 !important;
            border-color: #0dcaf0 !important;
        }

        /* Remove any transitions that cause flashing */
        .health-module-content .btn,
        .health-module-content .btn * {
            transition: none !important;
        }
        .table-hover > tbody > tr:hover > * {
            background-color: transparent !important;
        }
        .table tbody tr:hover > * {
            background-color: inherit !important;
        }
        .theme-black-red .table tbody tr:hover > * {
            background-color: transparent !important;
            background: transparent !important;
        }
        .theme-black-red .table tbody tr:hover {
            transform: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        .table, .table * {
            transition: none !important;
        }
        .theme-black-red .table, .theme-black-red .table * {
            transition: none !important;
        }
        .table-hover tbody tr:hover,
        .table tbody tr:hover {
            transform: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        .content-container { max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: var(--spacing-lg); padding-right: var(--spacing-lg); }
        .content-full { max-width: none; margin: 0; padding-left: 0; padding-right: 0; position: relative; left: -1.5rem; width: calc(100% + 3rem); }
        
        /* ========================================
           STICKY TABLE HEADERS
           ======================================== */
        .table-responsive {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .table-sticky thead th {
            position: sticky;
            top: 0;
            background-color: var(--color-primary) !important;
            color: white !important;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* ========================================
           FLOATING ACTION BUTTON (FAB)
           ======================================== */
        .fab {
            position: fixed;
            bottom: var(--spacing-xl);
            right: var(--spacing-xl);
            width: 56px;
            height: 56px;
            background-color: var(--color-primary) !important;
            border-radius: 50% !important;
            box-shadow: var(--shadow-lg) !important;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white !important;
            font-size: 24px;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border: none !important;
            padding: 0 !important;
        }
        
        .fab:hover {
            background-color: var(--color-primary-hover) !important;
            transform: scale(1.1) translateY(-4px) !important;
            box-shadow: var(--shadow-xl) !important;
        }
        
        .fab:active {
            transform: scale(0.95) !important;
        }
        
        /* ========================================
           BREADCRUMBS
           ======================================== */
        .breadcrumb-custom {
            background: transparent;
            padding: var(--spacing-sm) 0;
            margin-bottom: var(--spacing-md);
            font-size: 0.875rem;
        }
        
        .breadcrumb-custom .breadcrumb-item {
            color: var(--color-text-muted);
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--color-primary);
            font-weight: 600;
        }
        
        .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            font-size: 1.2rem;
            color: var(--color-text-muted);
        }
        
        .breadcrumb-custom a {
            color: var(--color-text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }
        
        .breadcrumb-custom a:hover {
            color: var(--color-primary);
        }
        
        /* ========================================
           EMPTY STATE SCREENS
           ======================================== */
        .empty-state {
            text-align: center;
            padding: var(--spacing-2xl) var(--spacing-lg);
            background: white;
            border-radius: var(--spacing-md);
            border: 2px dashed var(--color-border);
        }
        
        .empty-state-icon {
            font-size: 4rem;
            color: #D1D5DB;
            margin-bottom: var(--spacing-lg);
            opacity: 0.5;
        }
        
        .empty-state-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--color-text);
            margin-bottom: var(--spacing-xs);
        }
        
        .empty-state-description {
            color: var(--color-text-muted);
            margin-bottom: var(--spacing-lg);
            font-size: 0.9375rem;
        }
        
        /* ========================================
           SKELETON LOADER
           ======================================== */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: var(--spacing-xs);
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        .skeleton-text {
            height: 1rem;
            margin-bottom: var(--spacing-xs);
        }
        
        .skeleton-title {
            height: 1.5rem;
            width: 60%;
            margin-bottom: var(--spacing-md);
        }
        
        .skeleton-card {
            height: 200px;
            margin-bottom: var(--spacing-md);
        }
        
        /* ========================================
           IMPROVED FORM STYLING
           ======================================== */
        .form-control, .form-select {
            border: 1px solid var(--color-border) !important;
            border-radius: var(--spacing-xs) !important;
            padding: var(--spacing-sm) var(--spacing-md) !important;
            font-size: 0.9375rem !important;
            transition: all 0.2s ease !important;
            box-shadow: var(--shadow-sm) !important;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--color-primary) !important;
            box-shadow: 0 0 0 3px rgba(74, 111, 82, 0.1) !important;
        }
        
        .form-group {
            margin-bottom: var(--spacing-md);
        }
        
        /* Two-column form layout */
        .form-row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-md);
        }
        
        @media (max-width: 768px) {
            .form-row-2col {
                grid-template-columns: 1fr;
            }
              text-decoration: none;
              position: relative;
              overflow: visible;
           IMPROVED MODALS
           ======================================== */
        .modal-content {
            border-radius: var(--spacing-md) !important;
            border: none !important;
            box-shadow: var(--shadow-xl) !important;
            animation: modalSlideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .modal-header {
            border-bottom: 1px solid var(--color-border) !important;
            padding: var(--spacing-lg) !important;
            background-color: var(--color-primary-light);
        }
        
        .modal-title {
            font-weight: 700;
            color: var(--color-primary);
        }
        
        .modal-body {
            padding: var(--spacing-lg) !important;
        }
        
        .modal-footer {
            border-top: 1px solid var(--color-border) !important;
            padding: var(--spacing-md) var(--spacing-lg) !important;
            gap: var(--spacing-sm);
        }
        
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.6) !important;
        }
        
        /* ========================================
           CARD IMPROVEMENTS
           ======================================== */
        .card {
            border: none !important;
            border-radius: var(--spacing-md) !important;
            box-shadow: var(--shadow-sm) !important;
            transition: all 0.3s ease !important;
            overflow: visible;
        }
        
        .card:hover {
            box-shadow: var(--shadow-md) !important;
            transform: translateY(-2px);
        }
        
        .card-header {
            background-color: var(--color-primary-light) !important;
            border-bottom: 1px solid var(--color-border) !important;
            padding: var(--spacing-md) var(--spacing-lg) !important;
            font-weight: 600;
        }
        
        .card-body {
            padding: var(--spacing-lg) !important;
        }
        
        /* ========================================
           TABLE IMPROVEMENTS
           ======================================== */
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table thead th {
            background-color: var(--color-primary) !important;
            color: white !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            font-size: 0.8125rem;
            letter-spacing: 0.5px;
            padding: var(--spacing-md) var(--spacing-md) !important;
            border: none !important;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid var(--color-border);
        }
        
        .table tbody tr:hover {
            background-color: var(--color-primary-light) !important;
        }
        .table-clickable tbody tr { cursor: pointer; }
        .table-clickable tbody tr:hover > * { background-color: var(--color-primary-light) !important; }
        .table-clickable tbody tr:focus { outline: 2px solid var(--color-primary); outline-offset: -2px; }
        
        .table tbody td {
            padding: var(--spacing-md) !important;
            vertical-align: middle;
        }
        
        .content-full {
            max-width: none;
            width: 100%;
            margin: 0;
            padding-left: 0;
            padding-right: 0;
            position: static;
        }
        .form-offset-right { padding-left: 0 !important; padding-right: 0 !important; }
        .table tbody tr:active,
        .table tbody tr:focus,
        .table-hover tbody tr:active,
        .table-hover tbody tr:focus {
            transform: none !important;
            box-shadow: none !important;
            background: transparent !important;
            outline: none !important;
        }
        .theme-black-red .table tbody tr:active,
        .theme-black-red .table tbody tr:focus {
            transform: none !important;
            box-shadow: none !important;
            background: transparent !important;
            outline: none !important;
        }

        /* ========================================
           LAYOUT OVERRIDES - HEADER, SIDEBAR, CONTENT
           ======================================== */

        .app-shell {
            min-height: 100vh;
            background: var(--color-bg);
        }

        /* Professional Header Styling */
        header {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        
        header .btn:hover {
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        /* Simplified Header Elements */
        .app-header .navbar-brand {
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .app-header .navbar-brand:hover {
            opacity: 0.9;
        }

        /* Simple header buttons */
        .app-header .btn {
            transition: all 0.2s ease;
        }

        .app-header .btn:hover {
            transform: translateY(-1px);
        }



        /* Main layout styles */
        .sidebar {
            position: fixed;
            top: 64px;
            left: 0;
            width: var(--sidebar-expanded);
            height: calc(100vh - 64px);
            overflow-y: auto;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            transition: var(--sidebar-transition);
        }
        
        .main-content {
            margin-left: var(--sidebar-expanded);
            margin-top: 8px !important;
            min-height: calc(100vh - 64px);
            padding: 0rem 1rem;
            transition: margin-left 0.28s ease;
        }

        /* Calendar Modal Styling */
        .calendar-container {
            max-width: 100%;
        }

        .calendar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding: 0 0.5rem;
        }

        .calendar-month-year {
            font-weight: 600;
            font-size: 1.25rem;
            color: var(--color-primary);
            margin: 0;
        }

        .page-header {
            position: relative;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 12px;
            background: #ffffff;
            border-bottom: 1px solid var(--color-border);
            margin-bottom: 8px !important;
        }
        .page-header__title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
            white-space: nowrap;
        }
        .page-header__meta {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            white-space: nowrap;
            color: #6B7280;
            font-size: 0.95rem;
        }
        .page-header__spacer { flex: 1 1 auto; min-width: 0; }
        .page-header__actions { flex: 0 0 auto; display: inline-flex; gap: 8px; }
        .page-header, .page-header * { white-space: nowrap; }
        .page-header .truncate { overflow: hidden; text-overflow: ellipsis; display: inline-block; max-width: 34vw; }

        .navbar { position: fixed; z-index: 1000; top: 0; left: 0; right: 0; width: 100%; }
        .sidebar { position: fixed; z-index: 1000; top: 64px; left: 0; width: 260px; height: calc(100vh - 64px); }
        .main-content { position: relative; z-index: 1; margin-left: 260px; margin-top: 8px !important; }
        .main-content .container,
        .main-content .ds-page,
        .main-content .section-offset,
        .main-content .content-container { max-width: 1200px; margin-left: auto; margin-right: auto; padding-left: 16px; padding-right: 16px; }
        .ds-page, .section-offset { position: relative; }
        @media (max-width: 991.98px) { .main-content { margin-left: 0; } }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .calendar-day-header {
            background: var(--color-primary);
            color: white;
            padding: 0.875rem 0.5rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .calendar-day {
            background: white;
            padding: 1rem 0.5rem;
            text-align: center;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            font-weight: 500;
        }

        .calendar-day:hover {
            background: var(--color-primary-light);
            color: var(--color-primary);
        }

        .calendar-day.today {
            background: var(--color-primary);
            color: white;
            font-weight: 700;
        }

        .calendar-day.other-month {
            background: #f9fafb;
            color: #9ca3af;
        }

        .calendar-day.other-month:hover {
            background: #f3f4f6;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                padding: 0 1rem !important;
            }
            header > div {
                gap: 0.5rem !important;
            }
        }

        .sidebar {
            position: sticky;
            top: var(--navbar-height);
            height: calc(100vh - var(--navbar-height));
            width: var(--sidebar-collapsed);
            background: #ffffff;
            border-right: 1px solid var(--color-border);
            padding: var(--spacing-lg) 0;
            overflow-y: auto;
            overflow-x: hidden;
            transition: var(--sidebar-transition);
            box-shadow: none;
        }

        .sidebar:hover {
            width: var(--sidebar-expanded);
        }

        .sidebar nav .nav-link {
            padding-left: var(--spacing-md) !important;
            padding-right: var(--spacing-md) !important;
        }

        .sidebar .nav-link-text {
            white-space: nowrap;
            transition: opacity 0.2s ease;
        }

        .sidebar-group-title {
            padding: 0.5rem var(--spacing-md);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: .04em;
            color: #6b7280;
            text-transform: uppercase;
        }
        .sidebar-divider {
            height: 1px;
            background: var(--color-border);
            margin: 0.25rem 0.75rem;
        }

        .sidebar:not(:hover) .nav-link-text {
            opacity: 0;
        }

        .sidebar:hover .nav-link-text {
            opacity: 1;
        }

        .main-content {
            flex: 1;
            width: 100%;
            max-width: 100%;
            padding: 0.5rem 1rem 1rem 1rem !important;
            padding-top: var(--navbar-height) !important;
            background: transparent;
            transition: padding 0.28s ease;
            min-height: calc(100vh - var(--navbar-height));
        }

        .content-container {
            max-width: none !important;
            width: 100% !important;
            margin: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* Maximize space utilization */
        .card {
            margin-bottom: 1rem !important;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        /* Alert message styling - ensure full readability */
        .alert {
            word-wrap: break-word !important;
            white-space: normal !important;
            overflow-wrap: break-word !important;
        }
        
        .mb-4, .my-4 {
            margin-bottom: 1rem !important;
        }
        
        .mb-5, .my-5 {
            margin-bottom: 1.25rem !important;
        }
        
        .p-4 {
            padding: 1rem !important;
        }
        
        .p-5 {
            padding: 1.25rem !important;
        }
        
        /* Reduce table padding */
        .table th, .table td {
            padding: 0.5rem !important;
        }
        
        /* Compact form spacing */
        .form-group, .mb-3 {
            margin-bottom: 0.75rem !important;
        }
        
        /* Reduce row gaps */
        .row {
            margin-left: -0.5rem !important;
            margin-right: -0.5rem !important;
        }
        
        .row > * {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        @media (max-width: 992px) {
            .app-body {
                flex-direction: column;
            }

            .sidebar {
                position: relative;
                top: 0;
                height: auto;
                width: 100%;
                border-right: none;
                border-bottom: 1px solid var(--color-border);
                padding: var(--spacing-lg);
            }

            .sidebar:not(:hover) .nav-link-text {
                opacity: 1;
            }

            .main-content {
                padding: 0.5rem !important;
            }
            
            /* Keep header horizontal on mobile */
            header {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                padding: var(--spacing-md);
            }
        }

        /* ========================================
           FOOTER - MATTE GREEN THEME
           ======================================== */
        .footer {
            background: linear-gradient(135deg, #3d5a43 0%, #4A6F52 100%);
            color: #FFFFFF;
            padding: 2rem 0 1.5rem 0;
            margin-top: 4rem;
            border-top: 3px solid #5a9275;
        }

        .footer .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .footer .footer-text {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .footer .text-muted {
            color: rgba(255, 255, 255, 0.7) !important;
            font-size: 0.875rem;
        }

        /* ========================================
           USER DROPDOWN MENU - CLEAN DESIGN
           ======================================== */
        .user-dropdown .dropdown-toggle {
            background: transparent !important;
            border: none !important;
            transition: all 0.2s ease;
            box-shadow: none !important;
        }

        .user-dropdown .dropdown-toggle:hover {
            background: #f3f4f6 !important;
        }

        .user-dropdown .dropdown-toggle::after {
            display: none !important;
        }

        .user-dropdown-menu {
            min-width: 240px !important;
            border-radius: 10px !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
            padding: 0 !important;
            margin-top: 0.5rem !important;
        }

        .user-dropdown-menu .dropdown-item {
            padding: 0.75rem 1rem !important;
            transition: all 0.15s ease;
            color: #4b5563 !important;
            font-size: 0.875rem;
        }

        .user-dropdown-menu .dropdown-item:hover {
            background: #f3f4f6 !important;
            color: #1f2937 !important;
        }

        .user-dropdown-menu .dropdown-item i {
            color: #6b7280;
        }

        .user-dropdown-menu .dropdown-item:hover i {
            color: #374151;
        }

        .footer .text-danger {
            color: #ff6b6b !important;
        }

        @media (max-width: 768px) {
            .footer {
                padding: 1.5rem 0 1rem 0;
            }
        }
    </style>
    
    <script>
        // Restore scroll position after page load
        window.addEventListener('load', function() {
            const savedPosition = sessionStorage.getItem('scrollPosition');
            if (savedPosition) {
                window.scrollTo(0, parseInt(savedPosition));
                sessionStorage.removeItem('scrollPosition');
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('form.js-reject-form').forEach(function(form){
                const modalEl = form.closest('.modal');
                const submitBtn = form.querySelector('[data-submit]');
                const spinner = submitBtn ? submitBtn.querySelector('.spinner-border') : null;
                const errorBox = form.querySelector('.js-reject-error');
                const textarea = form.querySelector('textarea[name="remarks"]');
                const bsModal = modalEl ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;

                form.addEventListener('submit', async function(e){
                    e.preventDefault();
                    if(!textarea) return form.submit();
                    const val = (textarea.value || '').trim();
                    if(val.length < 3){
                        textarea.classList.add('is-invalid');
                        textarea.focus();
                        return;
                    }
                    textarea.classList.remove('is-invalid');
                    if(!confirm('Confirm rejection? This action will mark the request as rejected.')) return;

                    if(submitBtn){ submitBtn.disabled = true; }
                    if(spinner){ spinner.classList.remove('d-none'); }
                    if(errorBox){ errorBox.classList.add('d-none'); errorBox.textContent = ''; }

                    try{
                        const token = form.querySelector('input[name="_token"]').value;
                        const resp = await fetch(form.action, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                            body: new FormData(form)
                        });

                        if(resp.ok){
                            if(bsModal){ bsModal.hide(); }
                            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                            if(window.location && window.location.reload){ window.location.reload(); }
                        } else {
                            const msg = 'Rejection failed. Please check your connection and try again.';
                            if(errorBox){ errorBox.textContent = msg; errorBox.classList.remove('d-none'); }
                            if(submitBtn){ submitBtn.disabled = false; }
                            if(spinner){ spinner.classList.add('d-none'); }
                        }
                    } catch(err){
                        const msg = 'Network error. Please retry.';
                        if(errorBox){ errorBox.textContent = msg; errorBox.classList.remove('d-none'); }
                        if(submitBtn){ submitBtn.disabled = false; }
                        if(spinner){ spinner.classList.add('d-none'); }
                    }
                });
            });
        });
    </script>
    
    <style>
        /* ========================================
           BACK BUTTON FIX - Ensure visibility
           ======================================== */
        
        /* Fix for all back buttons */
        .btn-secondary,
        .btn-outline-secondary,
        .btn-outline-light {
            white-space: nowrap !important;
            overflow: visible !important;
            text-overflow: clip !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }
        
        /* Ensure back button text is always visible */
        .btn-secondary i,
        .btn-outline-secondary i,
        .btn-outline-light i {
            flex-shrink: 0;
        }
        
        /* Fix secondary button text color */
        .btn-secondary {
            color: #ffffff !important;
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
        }
        
        .btn-secondary:hover {
            color: #ffffff !important;
            background-color: #5a6268 !important;
            border-color: #545b62 !important;
        }
        
        /* Fix outline-light button for dark/colored backgrounds */
        .btn-outline-light,
        .btn-outline-light:link,
        .btn-outline-light:visited {
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.5) !important;
            background-color: rgba(255, 255, 255, 0.1) !important;
            font-weight: 500 !important;
        }
        
        .btn-outline-light:hover,
        .btn-outline-light:focus,
        .btn-outline-light:active {
            color: #212529 !important;
            background-color: #ffffff !important;
            border-color: #ffffff !important;
        }
        
        /* Specific fix for buttons on gradient backgrounds */
        .card .btn-outline-light,
        [style*="gradient"] .btn-outline-light {
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.8) !important;
            background-color: rgba(255, 255, 255, 0.15) !important;
            backdrop-filter: blur(10px);
        }
        
        /* Ensure button group doesn't cause overflow */
        .btn-group {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        /* Fix for buttons in header sections */
        .d-flex .btn {
            flex-shrink: 0;
        }
        
        /* Ensure all buttons have proper padding */
        .btn {
            padding: 0.5rem 1rem !important;
            min-width: fit-content !important;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loading-screen" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #ffffff; z-index: 9999; display: flex; align-items: center; justify-content: center; transition: opacity 0.3s ease;">
        <div style="text-align: center;">
            <div style="font-size: 2rem; color: #4A6F52; margin-bottom: 1rem;">
                <i class="bi bi-house-heart"></i>
            </div>
            <div style="font-weight: 600; color: #374151;">Barangay Matina Pangi</div>
        </div>
    </div>
    
    <script>
        (function(){
            var el = document.getElementById('loading-screen');
            function hide(){ if (!el) return; el.style.opacity='0'; setTimeout(function(){ el.style.display='none'; }, 300); }
            window.addEventListener('load', function(){ setTimeout(hide, 200); });
            document.addEventListener('DOMContentLoaded', function(){ setTimeout(hide, 600); });
            setTimeout(hide, 2000);
        })();
    </script>

    <!-- Professional Admin Layout -->
    <div class="min-h-screen bg-gray-50">
        <!-- Professional Global Header -->
        <header style="position: fixed; top: 0; left: 0; right: 0; z-index: 1200; height: 64px; display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; background: #ffffff; border-bottom: 1px solid #e5e7eb;">
            <!-- Left Section: Logo + System Name -->
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <!-- Logo & System Title -->
                <a href="<?php echo e(auth()->check() && auth()->user()->isCalamityHead() ? route('calamities.dashboard') : route('dashboard')); ?>" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                    <img src="<?php echo e(asset('logo.png')); ?>" alt="Logo" style="width: 36px; height: 36px; object-fit: contain;" onerror="this.style.display='none'">
                    <div style="display: flex; flex-direction: column;">
                        <div style="font-weight: 700; font-size: 1.1rem; color: var(--color-primary); line-height: 1;">
                            Barangay Matina Pangi
                        </div>
                        <div style="font-size: 0.7rem; color: #6b7280; line-height: 1;">
                            Information and Calamity System
                        </div>
                    </div>
                </a>
            </div>

            <!-- Right Section: Date + Calendar + Notifications + User -->
            <div style="display: flex; align-items: center; gap: 1rem;">
                <!-- Current Date with Calendar Modal -->
                <button class="btn d-none d-md-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#calendarModal" style="background: #ffffff; border: 1px solid #e5e7eb; color: #374151; border-radius: 8px; padding: 0.5rem 1rem; font-size: 0.85rem; font-weight: 600; box-shadow: none;">
                    <i class="bi bi-calendar3"></i>
                    <span><?php echo e(now()->format('F d, Y')); ?></span>
                </button>
                
                <?php if(auth()->guard()->check()): ?>
                    <!-- Notifications -->
                    <?php if(auth()->user()->isSecretary()): ?>
                    <?php
                        $annCount = \App\Models\Announcement::where('status','sent')
                            ->whereNotNull('sent_at')
                            ->count();
                        $latestAnnouncements = \App\Models\Announcement::where('status','sent')
                            ->whereNotNull('sent_at')
                            ->latest('sent_at')
                            ->take(5)
                            ->get();
                    ?>
                    <div class="dropdown">
                        <button class="btn position-relative" type="button" data-bs-toggle="dropdown" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 0.5rem; color: #6b7280;">
                            <i class="bi bi-bell" style="font-size: 1.1rem;"></i>
                            <?php if($annCount > 0): ?>
                                <span class="position-absolute badge rounded-pill bg-danger" style="top: -2px; right: -2px; font-size: 0.6rem; padding: 0.2em 0.4em;">
                                    <?php echo e($annCount > 99 ? '99+' : $annCount); ?>

                                </span>
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow" style="min-width: 300px; border-radius: 12px;">
                            <div class="dropdown-header d-flex justify-content-between align-items-center">
                                <strong style="color: var(--color-primary);">Notifications</strong>
                                <?php if($annCount > 0): ?>
                                    <span class="badge" style="background: var(--color-primary);"><?php echo e($annCount); ?> new</span>
                                <?php endif; ?>
                            </div>
                            <?php $__empty_1 = true; $__currentLoopData = $latestAnnouncements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <a class="dropdown-item py-2" href="<?php echo e(route('announcements.show', $a)); ?>">
                                    <div class="d-flex">
                                        <i class="bi bi-megaphone me-2 mt-1" style="color: var(--color-primary);"></i>
                                        <div>
                                            <div style="font-size: 0.85rem; font-weight: 500;">
                                                <?php echo e(\Illuminate\Support\Str::limit($a->title, 35)); ?>

                                            </div>
                                            <small class="text-muted"><?php echo e(optional($a->sent_at)->diffForHumans()); ?></small>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="dropdown-item text-muted text-center py-3">
                                    <i class="bi bi-bell-slash d-block mb-1"></i>
                                    No recent notifications
                                </div>
                            <?php endif; ?>
                            <?php if($annCount > 0): ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-center" href="<?php echo e(route('announcements.index')); ?>" style="color: var(--color-primary); font-weight: 500;">
                                    View All Notifications
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 0.4rem 0.6rem;">
                            <i class="bi bi-person-circle" style="font-size: 1.4rem; color: var(--color-primary);"></i>
                            <div class="d-none d-sm-block text-start" style="line-height: 1.1;">
                                <div style="font-size: 0.8rem; font-weight: 600; color: #374151;"><?php echo e(auth()->user()->name); ?></div>
                                <div style="font-size: 0.7rem; color: #6b7280;">
                                    <?php if(auth()->user()->isSecretary()): ?>
                                        Secretary
                                    <?php else: ?>
                                        User
                                    <?php endif; ?>
                                </div>
                            </div>
                            <i class="bi bi-chevron-down d-none d-sm-inline" style="font-size: 0.7rem; color: #9ca3af;"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow" style="min-width: 220px; border-radius: 12px;">
                            <div class="dropdown-header">
                                <div style="font-weight: 600;"><?php echo e(auth()->user()->name); ?></div>
                                <small class="text-muted">
                                    <?php if(auth()->user()->isSecretary()): ?>
                                        Barangay Secretary
                                    <?php else: ?>
                                        System User
                                    <?php endif; ?>
                                </small>
                            </div>
                            <?php if(auth()->user()->isSecretary()): ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo e(route('settings.users.index')); ?>">
                                <i class="bi bi-gear me-2"></i>Settings
                            </a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <form action="<?php echo e(route('logout')); ?>" method="POST" class="m-0">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <!-- Calendar Modal -->
        <div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                    <div class="modal-header" style="border-bottom: 1px solid #e5e7eb; padding: 1.25rem 1.5rem;">
                        <h5 class="modal-title fw-semibold" id="calendarModalLabel">
                            <i class="bi bi-calendar3 me-2" style="color: #4A6F52;"></i>Calendar
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="padding: 1.5rem;">
                        <div class="calendar-wrapper">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <button class="btn btn-sm" id="prevMonth" style="background: #f3f4f6; border: none; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <h5 class="mb-0 fw-bold" id="monthYear" style="color: #4A6F52;">December 2025</h5>
                                <button class="btn btn-sm" id="nextMonth" style="background: #f3f4f6; border: none; width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                            
                            <!-- Calendar Grid -->
                            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px;">
                                <!-- Day Headers -->
                                <div style="text-align: center; font-weight: 600; font-size: 0.75rem; color: #6b7280; padding: 8px 0;">Sun</div>
                                <div style="text-align: center; font-weight: 600; font-size: 0.75rem; color: #6b7280; padding: 8px 0;">Mon</div>
                                <div style="text-align: center; font-weight: 600; font-size: 0.75rem; color: #6b7280; padding: 8px 0;">Tue</div>
                                <div style="text-align: center; font-weight: 600; font-size: 0.75rem; color: #6b7280; padding: 8px 0;">Wed</div>
                                <div style="text-align: center; font-weight: 600; font-size: 0.75rem; color: #6b7280; padding: 8px 0;">Thu</div>
                                <div style="text-align: center; font-weight: 600; font-size: 0.75rem; color: #6b7280; padding: 8px 0;">Fri</div>
                                <div style="text-align: center; font-weight: 600; font-size: 0.75rem; color: #6b7280; padding: 8px 0;">Sat</div>
                                
                                <!-- Calendar Days Container -->
                                <div id="calendarDays" style="grid-column: 1 / -1; display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Layout Container -->
        <div class="flex" style="padding-top: 64px;">
            <!-- Professional Sidebar -->
            <aside class="sidebar">
                <nav class="nav flex-column">
                <!-- Dashboard -->
                <a class="nav-link <?php echo e((auth()->check() && auth()->user()->isCalamityHead() ? (request()->routeIs('calamities.dashboard') ? 'active' : '') : (request()->routeIs('dashboard') ? 'active' : ''))); ?>" 
                   href="<?php echo e(auth()->check() && auth()->user()->isCalamityHead() ? route('calamities.dashboard') : route('dashboard')); ?>" 
                   data-tooltip="Dashboard">
                    <i class="bi bi-speedometer2"></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>


                <?php if(auth()->user()->isSecretary()): ?>
                    <!-- Resident Profiling System (Secretary/SuperAdmin only) -->
                    <a class="nav-link" 
                       data-bs-toggle="collapse" 
                       href="#profilingSubmenu" 
                       role="button" 
                       aria-expanded="<?php echo e(request()->routeIs('residents.*', 'households.*', 'census.*', 'puroks.*', 'resident-transfers.*', 'certificates.*', 'approvals.*', 'archives.*') ? 'true' : 'false'); ?>" 
                       aria-controls="profilingSubmenu"
                       data-tooltip="Resident Profiling">
                        <i class="bi bi-person-vcard"></i>
                        <span class="nav-link-text">Resident Profiling</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse <?php echo e(request()->routeIs('residents.*', 'households.*', 'census.*', 'puroks.*', 'resident-transfers.*', 'certificates.*', 'approvals.*', 'archives.*') ? 'show' : ''); ?>" id="profilingSubmenu">
                        <nav class="nav flex-column ms-3">
                            <a class="nav-link <?php echo e(request()->routeIs('residents.*') ? 'active' : ''); ?>" 
                               href="<?php echo e(route('residents.index')); ?>"
                               data-tooltip="Residents">
                                <i class="bi bi-people"></i>
                                <span class="nav-link-text">Residents</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('households.*') ? 'active' : ''); ?>" 
                               href="<?php echo e(route('households.index')); ?>"
                               data-tooltip="Households">
                                <i class="bi bi-house"></i>
                                <span class="nav-link-text">Households</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('puroks.*') ? 'active' : ''); ?>" 
                               href="<?php echo e(route('puroks.index')); ?>"
                               data-tooltip="Puroks">
                                <i class="bi bi-geo-alt"></i>
                                <span class="nav-link-text">Puroks</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('census.*') ? 'active' : ''); ?>" 
                               href="<?php echo e(route('census.index')); ?>"
                               data-tooltip="Census">
                                <i class="bi bi-bar-chart"></i>
                                <span class="nav-link-text">Census</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('resident-transfers.*') ? 'active' : ''); ?>" 
                               href="<?php echo e(route('resident-transfers.index')); ?>"
                               data-tooltip="Resident Transfers">
                                <i class="bi bi-arrow-left-right"></i>
                                <span class="nav-link-text">Resident Transfers</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('certificates.*') ? 'active' : ''); ?>" 
                               href="<?php echo e(route('certificates.index')); ?>"
                               data-tooltip="Certificates">
                                <i class="bi bi-file-earmark-text"></i>
                                <span class="nav-link-text">Certificates</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('approvals.*') ? 'active' : ''); ?>" 
                               href="<?php echo e(route('approvals.index')); ?>"
                               data-tooltip="Approvals">
                                <i class="bi bi-clock-history"></i>
                                <span class="nav-link-text">Approvals</span>
                            </a>
                        </nav>
                    </div>

                    <!-- Calamity Management System -->
                    <?php if(config('app.calamity_secretary_enabled')): ?>
                    <a class="nav-link" 
                       data-bs-toggle="collapse" 
                       href="#calamitySubmenu" 
                       role="button" 
                       aria-expanded="<?php echo e(request()->routeIs('calamities.*', 'web.calamity-*', 'web.evacuation-*', 'web.relief-*', 'web.damage-*', 'web.notifications.*', 'notifications.*', 'web.response-*', 'response-team-members.*', 'web.calamity-reports.*') ? 'true' : 'false'); ?>" 
                       aria-controls="calamitySubmenu"
                       data-tooltip="Calamity Management">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span class="nav-link-text">Calamity Management</span>
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse <?php echo e(request()->routeIs('calamities.*', 'web.calamity-*', 'web.evacuation-*', 'web.relief-*', 'web.damage-*', 'web.notifications.*', 'notifications.*', 'web.response-*', 'response-team-members.*', 'web.calamity-reports.*') ? 'show' : ''); ?>" id="calamitySubmenu">
                        <nav class="nav flex-column ms-3">
                            <a class="nav-link <?php echo e((request()->routeIs('calamities.*') && !request()->routeIs('calamities.dashboard')) ? 'active' : ''); ?>"
                               href="<?php echo e(route('calamities.index')); ?>"
                               data-tooltip="Calamity Incidents">
                                <i class="bi bi-lightning"></i>
                                <span class="nav-link-text">Calamity Incidents</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('web.calamity-affected-households.*') ? 'active' : ''); ?>"
                               href="<?php echo e(route('web.calamity-affected-households.index')); ?>"
                               data-tooltip="Affected Households">
                                <i class="bi bi-people"></i>
                                <span class="nav-link-text">Affected Households</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('web.evacuation-centers.*') ? 'active' : ''); ?>"
                               href="<?php echo e(route('web.evacuation-centers.index')); ?>"
                               data-tooltip="Evacuation Centers">
                                <i class="bi bi-building"></i>
                                <span class="nav-link-text">Evacuation Centers</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('web.relief-items.*') ? 'active' : ''); ?>"
                               href="<?php echo e(route('web.relief-items.index')); ?>"
                               data-tooltip="Relief Goods">
                                <i class="bi bi-box-seam"></i>
                                <span class="nav-link-text">Relief Goods</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('web.relief-distributions.*') ? 'active' : ''); ?>"
                               href="<?php echo e(route('web.relief-distributions.index')); ?>"
                               data-tooltip="Relief Distribution">
                                <i class="bi bi-truck"></i>
                                <span class="nav-link-text">Relief Distribution</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('web.damage-assessments.*') ? 'active' : ''); ?>"
                               href="<?php echo e(route('web.damage-assessments.index')); ?>"
                               data-tooltip="Damage Assessment">
                                <i class="bi bi-clipboard-check"></i>
                                <span class="nav-link-text">Damage Assessment</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('web.calamity-reports.*') ? 'active' : ''); ?>"
                               href="<?php echo e(route('web.calamity-reports.index')); ?>"
                               data-tooltip="Reports">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                                <span class="nav-link-text">Reports</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('web.notifications.*') ? 'active' : ''); ?>"
                               href="<?php echo e(route('web.notifications.index')); ?>"
                               data-tooltip="Emergency Notifications">
                                <i class="bi bi-megaphone"></i>
                                <span class="nav-link-text">Emergency Notifications</span>
                            </a>
                            <a class="nav-link <?php echo e(request()->routeIs('web.response-team-members.*') ? 'active' : ''); ?>"
                               href="<?php echo e(route('web.response-team-members.index')); ?>"
                               data-tooltip="Response Team">
                                <i class="bi bi-people-fill"></i>
                                <span class="nav-link-text">Response Team</span>
                            </a>
                        </nav>
                    </div>
                    <?php endif; ?>
                <?php elseif(auth()->user()->isCalamityHead()): ?>
                    <a class="nav-link <?php echo e((request()->routeIs('calamities.*') && !request()->routeIs('calamities.dashboard')) ? 'active' : ''); ?>"
                       href="<?php echo e(route('calamities.index')); ?>"
                       data-tooltip="Calamity Incidents">
                        <i class="bi bi-lightning"></i>
                        <span class="nav-link-text">Calamity Incidents</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('web.calamity-affected-households.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('web.calamity-affected-households.index')); ?>"
                       data-tooltip="Affected Households">
                        <i class="bi bi-people"></i>
                        <span class="nav-link-text">Affected Households</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('web.evacuation-centers.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('web.evacuation-centers.index')); ?>"
                       data-tooltip="Evacuation Centers">
                        <i class="bi bi-building"></i>
                        <span class="nav-link-text">Evacuation Centers</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('web.relief-items.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('web.relief-items.index')); ?>"
                       data-tooltip="Relief Goods">
                        <i class="bi bi-box-seam"></i>
                        <span class="nav-link-text">Relief Goods</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('web.relief-distributions.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('web.relief-distributions.index')); ?>"
                       data-tooltip="Relief Distribution">
                        <i class="bi bi-truck"></i>
                        <span class="nav-link-text">Relief Distribution</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('web.damage-assessments.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('web.damage-assessments.index')); ?>"
                       data-tooltip="Damage Assessment">
                        <i class="bi bi-clipboard-check"></i>
                        <span class="nav-link-text">Damage Assessment</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('web.notifications.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('web.notifications.index')); ?>"
                       data-tooltip="Emergency Notifications">
                        <i class="bi bi-megaphone"></i>
                        <span class="nav-link-text">Emergency Notifications</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('web.response-team-members.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('web.response-team-members.index')); ?>"
                       data-tooltip="Response Team">
                        <i class="bi bi-people-fill"></i>
                        <span class="nav-link-text">Response Team</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('web.calamity-reports.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('web.calamity-reports.index')); ?>"
                       data-tooltip="Reports">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span class="nav-link-text">Reports</span>
                    </a>
                <?php endif; ?>

                <?php if(auth()->user()->isStaff()): ?>
                    <a class="nav-link <?php echo e(request()->routeIs('staff.residents.index') ? 'active' : ''); ?>" 
                       href="<?php echo e(route('staff.residents.index')); ?>"
                       data-tooltip="Residents">
                        <i class="bi bi-people"></i>
                        <span class="nav-link-text">Residents</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('staff.households.*') ? 'active' : ''); ?>" 
                       href="<?php echo e(route('staff.households.index')); ?>"
                       data-tooltip="Households">
                        <i class="bi bi-house"></i>
                        <span class="nav-link-text">Households</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('staff.census.index') ? 'active' : ''); ?>" 
                       href="<?php echo e(route('staff.census.index')); ?>"
                       data-tooltip="Census">
                        <i class="bi bi-bar-chart"></i>
                        <span class="nav-link-text">Census</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('staff.puroks.index') ? 'active' : ''); ?>" 
                       href="<?php echo e(route('staff.puroks.index')); ?>"
                       data-tooltip="Puroks">
                        <i class="bi bi-geo-alt"></i>
                        <span class="nav-link-text">Puroks</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('staff.submissions.index') ? 'active' : ''); ?>" 
                       href="<?php echo e(route('staff.submissions.index')); ?>"
                       data-tooltip="My Submissions">
                        <i class="bi bi-file-earmark-text"></i>
                        <span class="nav-link-text">My Submissions</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('certificates.*') ? 'active' : ''); ?>" 
                       href="<?php echo e(route('certificates.index')); ?>"
                       data-tooltip="Certificates">
                        <i class="bi bi-file-earmark-text"></i>
                        <span class="nav-link-text">Certificates</span>
                    </a>
                    <a class="nav-link <?php echo e(request()->routeIs('staff.resident-transfers.*') ? 'active' : ''); ?>" 
                       href="<?php echo e(route('staff.resident-transfers.index')); ?>"
                       data-tooltip="Resident Transfers">
                        <i class="bi bi-arrow-left-right"></i>
                        <span class="nav-link-text">Resident Transfers</span>
                    </a>
                    <div class="sidebar-divider"></div>
                <?php endif; ?>

                <?php if(auth()->user()->isSecretary()): ?>
                <!-- Announcements -->
                <a class="nav-link <?php echo e(request()->routeIs('announcements.*') ? 'active' : ''); ?>" 
                   href="<?php echo e(route('announcements.index')); ?>"
                   data-tooltip="Announcements">
                    <i class="bi bi-megaphone"></i>
                    <span class="nav-link-text">Announcements</span>
                </a>

                <!-- Global Archives -->
                <a class="nav-link <?php echo e(request()->routeIs('archives.*') ? 'active' : ''); ?>" 
                   href="<?php echo e(route('archives.index')); ?>"
                   data-tooltip="Archives">
                    <i class="bi bi-archive"></i>
                    <span class="nav-link-text">Archives</span>
                </a>
                <?php endif; ?>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <main class="main-content">
                <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert" style="margin-left: auto; max-width: fit-content;">
                        <i class="bi bi-exclamation-triangle me-2"></i> 
                        <span><?php echo e(session('error')); ?></span>
                        <button type="button" class="btn-close ms-3" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-left: auto; max-width: fit-content;">
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1">
                                <strong>Errors:</strong>
                                <ul class="mb-0 mt-1" style="padding-left: 1.2rem;">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li style="margin-bottom: 0.25rem;"><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                            <button type="button" class="btn-close ms-3" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (! empty(trim($__env->yieldContent('full_width')))): ?>
                    <div class="content-full">
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                <?php else: ?>
                    <div class="content-container">
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                <?php endif; ?>

                <footer class="footer mt-5">
                    <div class="container-fluid">
                        <p class="footer-text mb-0">
                            <i class="bi bi-heart-fill text-danger"></i>
                            Building a connected community — one record at a time.
                        </p>
                        <small class="text-muted">© <?php echo e(date('Y')); ?> Barangay Matina Pangi. All rights reserved.</small>
                    </div>
                </footer>
            </main>
        </div>
    </div>


    <script>
        (function(){
            const bellCountEl = document.getElementById('announcementBellCount');
            const bellMenuEl = document.getElementById('announcementBellMenu');
            const bellWrapEl = document.getElementById('announcementBell');
            async function refreshAnnouncementBell(){
                try {
                    const res = await fetch('<?php echo e(route('announcements.bell')); ?>', { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) return;
                    const data = await res.json();
                    if (bellCountEl) {
                        bellCountEl.textContent = data.count || 0;
                        bellCountEl.style.display = (data.count && data.count > 0) ? 'inline-block' : 'none';
                    }
                    if (bellMenuEl) {
                        let html = '<li class="dropdown-header">Announcements</li>';
                        if (Array.isArray(data.items) && data.items.length){
                            data.items.forEach(function(it){
                                html += '<li><a class="dropdown-item" href="'+it.url+'">'
                                    +'<div class="d-flex align-items-start">'
                                    +'<div class="me-2"><i class="bi bi-megaphone"></i></div>'
                                    +'<div><div class="fw-semibold">'+escapeHtml(it.title)+'</div>'
                                    +'<small class="text-muted">'+(it.sent_at_human || '')+'</small></div>'
                                    +'</div>'
                                    +'</a></li>';
                            });
                        } else {
                            html += '<li><span class="dropdown-item text-muted">No recent announcements</span></li>';
                        }
                        html += '<li><hr class="dropdown-divider"></li>';
                        html += '<li><a class="dropdown-item" href="<?php echo e(route('announcements.index')); ?>">View all</a></li>';
                        bellMenuEl.innerHTML = html;
                    }
                } catch (e) { /* ignore */ }
            }
            function escapeHtml(s){
                return String(s).replace(/[&<>"']/g, function (m) {
                    return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'})[m];
                });
            }
            // Poll periodically to reflect new announcements without reload
            setInterval(refreshAnnouncementBell, 5000);
            // Initial fetch
            refreshAnnouncementBell();
            // Optional: refresh when page becomes visible again
            document.addEventListener('visibilitychange', function(){ if (!document.hidden) refreshAnnouncementBell(); });
        })();
    </script>
    <script>
        (function(){
            function textOfLabel(el){
                var id = el.getAttribute('id');
                if (id) {
                    var lbl = document.querySelector('label[for="'+id+'"]');
                    if (lbl) return lbl.textContent.trim().replace(/\s*\*\s*$/,'');
                }
                var p = el.closest('.col, .col-12, .col-md-12, .col-md-6, .col-md-4, .mb-3');
                if (p) {
                    var l = p.querySelector('.form-label');
                    if (l) return l.textContent.trim().replace(/\s*\*\s*$/,'');
                }
                return '';
            }
            function makePlaceholder(el){
                var label = textOfLabel(el);
                if (!label) return '';
                if (el.type === 'email') return 'name@example.com';
                if (el.tagName.toLowerCase() === 'textarea') return 'Enter '+label.toLowerCase();
                return 'Enter '+label.toLowerCase();
            }
            function ensureSelectDefault(sel){
                var label = textOfLabel(sel);
                if (!label) label = 'option';
                var first = sel.querySelector('option');
                var hasDefault = first && (first.value === '' || first.disabled);
                if (!hasDefault) {
                    var opt = document.createElement('option');
                    opt.value = '';
                    opt.disabled = true;
                    opt.selected = !sel.value;
                    opt.textContent = 'Select '+label.toLowerCase();
                    sel.insertBefore(opt, first);
                }
            }
            function process(){
                document.querySelectorAll('input.form-control, textarea.form-control').forEach(function(el){
                    if (el.type === 'hidden') return;
                    if (el.hasAttribute('placeholder')) return;
                    var ph = makePlaceholder(el);
                    if (ph) el.setAttribute('placeholder', ph);
                });
                document.querySelectorAll('select.form-select').forEach(function(sel){
                    ensureSelectDefault(sel);
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', process);
            } else {
                process();
            }
        })();
    </script>
    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="successToast" class="toast toast-success" role="alert" data-bs-autohide="true" data-bs-delay="3000">
            <div class="toast-header">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong class="me-auto">success</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <?php echo e(session('success') ?? 'successful transaction'); ?>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Toast Notification Script -->
    <script>
        <?php if(session('success')): ?>
            (function(){
                var el = document.getElementById('successToast');
                var body = el ? el.querySelector('.toast-body') : null;
                if (body) { body.textContent = <?php echo json_encode(session('success'), 15, 512) ?>; }
                var t = new bootstrap.Toast(el, { autohide: true, delay: 3000 });
                t.show();
            })();
        <?php endif; ?>
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
                var label = link.querySelector('.nav-link-text');
                var tooltip = label ? label.textContent.trim() : link.textContent.trim();
                if (tooltip && !link.getAttribute('title')) {
                    link.setAttribute('title', tooltip);
                }
            });
        });
    </script>
    
    <!-- Auto-Capitalize Names Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to capitalize first letter of each word
            function capitalizeWords(str) {
                return str.replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
            }
            
            // Select all name-related input fields
            const nameFields = document.querySelectorAll(
                'input[name*="first_name"], ' +
                'input[name*="middle_name"], ' +
                'input[name*="last_name"], ' +
                'input[name*="suffix"], ' +
                'input[name*="place_of_birth"], ' +
                'input[name*="nationality"], ' +
                'input[name*="religion"], ' +
                'input[name*="purok"], ' +
                'input[name*="barangay"], ' +
                'input[name*="city"], ' +
                'input[name*="province"], ' +
                'input[name*="municipality"], ' +
                'input[name*="caregiver_name"], ' +
                'input[name*="employer_name"], ' +
                'input[name*="school_name"], ' +
                'input[name*="mother_name"], ' +
                'input[name*="father_name"], ' +
                'input[name*="guardian_name"], ' +
                'input[name*="emergency_contact_name"]'
            );
            
            // Add input event listener to each field
            nameFields.forEach(function(field) {
                field.addEventListener('input', function(e) {
                    const cursorPosition = this.selectionStart;
                    const oldValue = this.value;
                    const newValue = capitalizeWords(oldValue);
                    
                    if (oldValue !== newValue) {
                        this.value = newValue;
                        // Restore cursor position
                        this.setSelectionRange(cursorPosition, cursorPosition);
                    }
                });
                
                // Also capitalize on blur (when leaving the field)
                field.addEventListener('blur', function() {
                    this.value = capitalizeWords(this.value);
                });
            });
        });
    </script>
    
    <!-- Live Search, Density Preference, Skeletons -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const liveSearchInputs = document.querySelectorAll('.live-search-input');
            
            liveSearchInputs.forEach(function(searchInput, index) {
                const targetGrid = searchInput.getAttribute('data-target-grid');
                const targetTable = searchInput.getAttribute('data-target-table');
                const emptyStateSelector = searchInput.getAttribute('data-empty-state');
                const resultCountSelector = searchInput.getAttribute('data-result-count');
                const scopeElement = searchInput.closest('[data-search-scope]');
                const emptyState = emptyStateSelector
                    ? document.querySelector(emptyStateSelector)
                    : scopeElement ? scopeElement.querySelector('.search-empty-state') : null;
                const resultCount = resultCountSelector
                    ? document.querySelector(resultCountSelector)
                    : scopeElement ? scopeElement.querySelector('.search-result-count') : null;
                
                // Handle grid-based search
                if (targetGrid) {
                    const gridElement = document.querySelector(targetGrid);
                    if (!gridElement) {
                        return;
                    }
                    
                    const gridItems = Array.from(gridElement.children);
                    let searchTimeout;
                    
                    searchInput.addEventListener('input', function(e) {
                        clearTimeout(searchTimeout);
                        
                        searchTimeout = setTimeout(function() {
                            const searchTerm = searchInput.value.toLowerCase().trim();
                            let visibleCount = 0;
                            
                            gridItems.forEach(function(item) {
                                const searchableText = item.getAttribute('data-search-text') || item.textContent;
                                const matches = searchableText.toLowerCase().includes(searchTerm);
                                
                                if (matches || searchTerm === '') {
                                    item.hidden = false;
                                    visibleCount++;
                                } else {
                                    item.hidden = true;
                                }
                            });
                            
                            if (emptyState) {
                                emptyState.hidden = !(visibleCount === 0 && searchTerm !== '');
                            }
                            
                            if (resultCount) {
                                resultCount.textContent = searchTerm !== ''
                                    ? `${visibleCount} result${visibleCount !== 1 ? 's' : ''}`
                                    : '';
                            }
                        }, 150);
                    });
                }
                
                // Handle table-based search
                if (targetTable) {
                    const tableBody = document.querySelector(targetTable);
                    if (!tableBody) {
                        return;
                    }
                    
                    const tableRows = Array.from(tableBody.querySelectorAll('tr'));
                    let searchTimeout;
                    
                    searchInput.addEventListener('input', function(e) {
                        clearTimeout(searchTimeout);
                        
                        searchTimeout = setTimeout(function() {
                            const searchTerm = searchInput.value.toLowerCase().trim();
                            let visibleCount = 0;
                            
                            tableRows.forEach(function(row) {
                                const searchableText = row.getAttribute('data-search-text') || row.textContent;
                                const matches = searchableText.toLowerCase().includes(searchTerm);
                                
                                if (matches || searchTerm === '') {
                                    row.hidden = false;
                                    visibleCount++;
                                } else {
                                    row.hidden = true;
                                }
                            });
                            
                            if (emptyState) {
                                emptyState.hidden = !(visibleCount === 0 && searchTerm !== '');
                            }
                            
                            if (resultCount) {
                                resultCount.textContent = searchTerm !== ''
                                    ? `${visibleCount} result${visibleCount !== 1 ? 's' : ''}`
                                    : '';
                            }
                        }, 150);
                    });
                }
                
                // Clear search button - find it properly
                const inputGroup = searchInput.closest('.input-group');
                if (inputGroup) {
                    const clearBtn = inputGroup.querySelector('.clear-search-btn');
                    if (clearBtn) {
                        clearBtn.addEventListener('click', function() {
                            searchInput.value = '';
                            searchInput.dispatchEvent(new Event('input'));
                            searchInput.focus();
                        });
                    }
                }
            });
            
            // Clickable table rows
            document.querySelectorAll('.clickable-row').forEach(function(row) {
                row.addEventListener('click', function(e) {
                    if (!e.target.closest('a, button')) {
                        const href = this.getAttribute('data-href');
                        if (href) {
                            window.location.href = href;
                        }
                    }

                    // Table density preference handling
                    const densityPreferenceKey = 'ui.tableDensity';
                    const storedDensity = localStorage.getItem(densityPreferenceKey) || 'comfortable';

                    function applyTableDensity(table, density) {
                        if (!table) return;
                        const targetTable = table.tagName === 'TABLE' ? table : table.querySelector('table');
                        if (!targetTable) return;
                        if (density === 'compact') {
                            targetTable.classList.add('ds-table--compact');
                        } else {
                            targetTable.classList.remove('ds-table--compact');
                        }
                    }

                    // Apply stored density to any table that opts in
                    document.querySelectorAll('table[data-density-sync]').forEach(function(table) {
                        applyTableDensity(table, storedDensity);
                    });

                    document.querySelectorAll('[data-density-control]').forEach(function(control) {
                        const targetSelector = control.getAttribute('data-density-target');
                        const buttons = control.querySelectorAll('[data-density]');

                        function setActiveButton(density) {
                            buttons.forEach(function(btn) {
                                const isActive = btn.getAttribute('data-density') === density;
                                btn.classList.toggle('is-active', isActive);
                                btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                            });
                        }

                        function updateAllTargets(density) {
                            if (!targetSelector) return;
                            document.querySelectorAll(targetSelector).forEach(function(el) {
                                applyTableDensity(el, density);
                            });
                            document.querySelectorAll('table[data-density-sync]').forEach(function(table) {
                                applyTableDensity(table, density);
                            });
                        }

                        // Initialize state
                        setActiveButton(storedDensity);
                        updateAllTargets(storedDensity);

                        buttons.forEach(function(btn) {
                            btn.addEventListener('click', function() {
                                const density = btn.getAttribute('data-density');
                                localStorage.setItem(densityPreferenceKey, density);
                                setActiveButton(density);
                                updateAllTargets(density);
                            });
                        });
                    });

                    // Skeleton loaders
                    const skeletons = document.querySelectorAll('[data-skeleton]');
                    skeletons.forEach(function(skeleton) {
                        const targetSelector = skeleton.getAttribute('data-skeleton-target');
                        const target = targetSelector ? document.querySelector(targetSelector) : null;
                        if (!target) {
                            skeleton.hidden = true;
                            return;
                        }

                        skeleton.hidden = false;
                        target.classList.add('is-loading');

                        window.addEventListener('load', function handleLoad() {
                            skeleton.hidden = true;
                            target.classList.remove('is-loading');
                        }, { once: true });
                    });
                });
            });
        });
        

        

        
        // Add fade-in animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: scale(0.95); }
                to { opacity: 1; transform: scale(1); }
            }
        `;
        document.head.appendChild(style);

        // Enhanced Header Functionality
        
        // Update current time every second
        function updateTime() {
            const now = new Date();
            const timeElement = document.getElementById('currentTime');
            if (timeElement) {
                timeElement.textContent = now.toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });
            }
        }

        // Update date display
        function updateDate() {
            const now = new Date();
            const dateElement = document.getElementById('currentDate');
            if (dateElement) {
                dateElement.textContent = now.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
            
            // Update calendar button day
            const dayElement = document.querySelector('.calendar-day');
            if (dayElement) {
                dayElement.textContent = now.getDate();
            }
        }

        // Calendar functionality
        let currentDate = new Date();

        function renderCalendar() {
            const calendarDays = document.getElementById('calendarDays');
            const monthYear = document.getElementById('monthYear');
            
            if (!calendarDays || !monthYear) {
                console.log('Calendar elements not found');
                return;
            }

            // Update month/year display
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            monthYear.textContent = months[currentDate.getMonth()] + ' ' + currentDate.getFullYear();

            // Clear existing days
            calendarDays.innerHTML = '';

            // Get first day of month and total days
            const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
            const prevLastDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0);
            
            const firstDayIndex = firstDay.getDay();
            const lastDayDate = lastDay.getDate();
            const prevLastDayDate = prevLastDay.getDate();
            
            const today = new Date();
            const isCurrentMonth = today.getMonth() === currentDate.getMonth() && today.getFullYear() === currentDate.getFullYear();

            // Previous month's days
            for (let i = firstDayIndex; i > 0; i--) {
                const day = document.createElement('div');
                day.textContent = prevLastDayDate - i + 1;
                day.style.cssText = 'text-align: center; padding: 10px; color: #d1d5db; font-size: 0.875rem; border-radius: 8px;';
                calendarDays.appendChild(day);
            }

            // Current month's days
            for (let i = 1; i <= lastDayDate; i++) {
                const day = document.createElement('div');
                day.textContent = i;
                
                const isToday = isCurrentMonth && i === today.getDate();
                
                if (isToday) {
                    day.style.cssText = 'text-align: center; padding: 10px; background: linear-gradient(135deg, #4A6F52 0%, #5a9275 100%); color: white; font-weight: 700; border-radius: 8px; cursor: pointer; font-size: 0.875rem;';
                } else {
                    day.style.cssText = 'text-align: center; padding: 10px; color: #374151; font-weight: 500; border-radius: 8px; cursor: pointer; transition: all 0.2s; font-size: 0.875rem;';
                    day.onmouseover = function() { this.style.background = '#f3f4f6'; };
                    day.onmouseout = function() { this.style.background = 'transparent'; };
                }
                
                calendarDays.appendChild(day);
            }

            // Next month's days
            const totalCells = firstDayIndex + lastDayDate;
            const remainingCells = totalCells > 35 ? 42 - totalCells : 35 - totalCells;
            
            for (let i = 1; i <= remainingCells; i++) {
                const day = document.createElement('div');
                day.textContent = i;
                day.style.cssText = 'text-align: center; padding: 10px; color: #d1d5db; font-size: 0.875rem; border-radius: 8px;';
                calendarDays.appendChild(day);
            }
        }

        // Calendar navigation
        document.addEventListener('DOMContentLoaded', function() {
            const prevMonthBtn = document.getElementById('prevMonth');
            const nextMonthBtn = document.getElementById('nextMonth');
            const calendarModal = document.getElementById('calendarModal');
            
            if (prevMonthBtn) {
                prevMonthBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    renderCalendar();
                });
            }
            
            if (nextMonthBtn) {
                nextMonthBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    renderCalendar();
                });
            }

            // Render calendar when modal is shown
            if (calendarModal) {
                calendarModal.addEventListener('shown.bs.modal', function() {
                    currentDate = new Date(); // Reset to current month
                    renderCalendar();
                });
            }

            // Initialize time and date updates
            updateTime();
            updateDate();
            setInterval(updateTime, 1000);
            setInterval(updateDate, 60000);
        });

        // Notification system enhancements
        document.addEventListener('DOMContentLoaded', function() {
            const notificationDropdown = document.querySelector('.notification-dropdown');
            if (notificationDropdown) {
                // Add notification sound (optional)
                const playNotificationSound = () => {
                    // Uncomment to enable notification sound
                    // const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10...');
                    // audio.play().catch(() => {}); // Silently fail if autoplay blocked
                };

                // Auto-refresh notifications every 5 minutes
                setInterval(function() {
                    // Implement AJAX call to refresh notifications if needed
                    // This would require additional backend endpoint
                }, 300000);
            }
        });

        // Simple header functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize time and date updates
            updateTime();
            updateDate();
            setInterval(updateTime, 1000);
            setInterval(updateDate, 60000);
        });
        document.addEventListener('DOMContentLoaded', function(){
            document.querySelectorAll('table.table-clickable tbody tr').forEach(function(row){
                const href = row.getAttribute('data-href');
                if(!href) return;
                row.setAttribute('role','link');
                row.setAttribute('tabindex','0');
                row.addEventListener('click', function(e){
                    const t = e.target;
                    if(t.closest('a, button, input, textarea, select, label')) return;
                    window.location.assign(href);
                });
                row.addEventListener('keydown', function(e){
                    if(e.key === 'Enter' || e.key === ' '){
                        e.preventDefault();
                        window.location.assign(href);
                    }
                });
            });
        });
    </script>
    
    <!-- Collapsible Sidebar JavaScript -->
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\matina_final\resources\views/layouts/app.blade.php ENDPATH**/ ?>