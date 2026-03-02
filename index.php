<?php
/**
 * SGBD – Web-Based Database Management System
 * 
 * Main entry point / front controller.
 * Routes requests to the appropriate controller based on the "page" query parameter.
 * All pages (except login) require authentication.
 */

require_once __DIR__ . '/helpers/functions.php';

init_session();

// Determine the requested page
$page = current_page();

// Pages that do not require authentication
$public_pages = ['login'];

// Require login for protected pages
if (!in_array($page, $public_pages)) {
    require_login();
}

// Route to the appropriate controller
switch ($page) {
    // --- Authentication ---
    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        break;

    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        break;

    // --- Dashboard ---
    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        break;

    // --- Database operations ---
    case 'databases':
    case 'create_database':
    case 'drop_database':
    case 'rename_database':
        require_once __DIR__ . '/controllers/DatabaseController.php';
        break;

    // --- Table operations ---
    case 'tables':
    case 'create_table':
    case 'drop_table':
    case 'truncate_table':
    case 'table_structure':
        require_once __DIR__ . '/controllers/TableController.php';
        break;

    // --- Column operations ---
    case 'add_column':
    case 'edit_column':
    case 'drop_column':
        require_once __DIR__ . '/controllers/ColumnController.php';
        break;

    // --- Record operations ---
    case 'browse':
    case 'insert':
    case 'edit_record':
    case 'delete_record':
        require_once __DIR__ . '/controllers/RecordController.php';
        break;

    // --- SQL Editor ---
    case 'sql':
        require_once __DIR__ . '/controllers/SqlController.php';
        break;

    // --- Import / Export ---
    case 'import':
        require_once __DIR__ . '/controllers/ImportController.php';
        break;

    case 'export':
        require_once __DIR__ . '/controllers/ExportController.php';
        break;

    // --- Default: redirect to dashboard ---
    default:
        redirect('index.php?page=dashboard');
        break;
}
