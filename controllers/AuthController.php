<?php
/**
 * Authentication Controller
 * 
 * Handles login and logout actions.
 * Credentials are validated by attempting a real PDO connection.
 * On success, credentials are stored in the session for subsequent requests.
 */

require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../config/database.php';

$action = $page; // 'login' or 'logout'

// --- Logout ---
if ($action === 'logout') {
    session_destroy();
    // Start a fresh session for the flash message
    init_session();
    set_flash('success', 'You have been logged out.');
    redirect('index.php?page=login');
}

// --- Login ---
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = trim($_POST['host'] ?? 'localhost');
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    // Validate required fields
    if (empty($user)) {
        $error = 'Username is required.';
    } else {
        try {
            // Attempt connection to validate credentials
            $db = new Database($host, $user, $pass);
            $db->connect();
            $db->disconnect();

            // Store credentials in session
            $_SESSION['db_host'] = $host;
            $_SESSION['db_user'] = $user;
            $_SESSION['db_pass'] = $pass;

            set_flash('success', "Connected as <strong>{$user}</strong>@{$host}");
            redirect('index.php?page=dashboard');
        } catch (PDOException $e) {
            $error = 'Connection failed: ' . $e->getMessage();
        }
    }
}

// Render the login view
require_once __DIR__ . '/../views/auth/login.php';
