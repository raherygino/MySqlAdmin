<?php
/**
 * Import Controller
 * 
 * Handles importing SQL files into a database.
 * Supports .sql file uploads which are executed statement by statement.
 */

require_once __DIR__ . '/../helpers/functions.php';

$dbName = $_GET['db'] ?? $_POST['db'] ?? '';

try {
    $pdo = $dbName ? get_connection($dbName) : get_connection();

    // Get list of databases for the dropdown
    $databases = $pdo->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME")->fetchAll(PDO::FETCH_COLUMN);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $targetDb = trim($_POST['target_db'] ?? $dbName);

        if (empty($targetDb)) {
            set_flash('error', 'Please select a target database.');
            redirect(build_url(['page' => 'import', 'db' => $dbName]));
        }

        // Switch to target database
        $pdo = get_connection($targetDb);

        // Check if file was uploaded
        if (!isset($_FILES['sql_file']) || $_FILES['sql_file']['error'] !== UPLOAD_ERR_OK) {
            set_flash('error', 'Please select a valid SQL file to import.');
            redirect(build_url(['page' => 'import', 'db' => $targetDb]));
        }

        $file = $_FILES['sql_file'];

        // Validate file extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'sql') {
            set_flash('error', 'Only .sql files are allowed.');
            redirect(build_url(['page' => 'import', 'db' => $targetDb]));
        }

        // Validate file size (max 50MB)
        $maxSize = 50 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            set_flash('error', 'File is too large. Maximum size is 50MB.');
            redirect(build_url(['page' => 'import', 'db' => $targetDb]));
        }

        // Read and execute the SQL file
        $sqlContent = file_get_contents($file['tmp_name']);

        if (empty($sqlContent)) {
            set_flash('error', 'The SQL file is empty.');
            redirect(build_url(['page' => 'import', 'db' => $targetDb]));
        }

        // Execute the SQL content
        $startTime = microtime(true);
        $statementsExecuted = 0;

        // Use exec for the entire SQL content (handles multiple statements)
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        
        // Split by semicolons (basic splitting – handles most cases)
        $statements = array_filter(
            array_map('trim', preg_split('/;\s*$/m', $sqlContent)),
            function ($s) { return !empty($s) && $s !== ';'; }
        );

        foreach ($statements as $statement) {
            if (!empty(trim($statement))) {
                // Skip comments-only statements
                $clean = preg_replace('/^\s*--.*$/m', '', $statement);
                $clean = preg_replace('/\/\*.*?\*\//s', '', $clean);
                $clean = trim($clean);
                if (empty($clean)) continue;

                $pdo->exec($statement);
                $statementsExecuted++;
            }
        }

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);

        set_flash('success', "Import successful! Executed {$statementsExecuted} statement(s) in {$executionTime}ms on database <strong>{$targetDb}</strong>.");
        redirect(build_url(['page' => 'tables', 'db' => $targetDb]));
    }

} catch (PDOException $e) {
    set_flash('error', 'Import error: ' . $e->getMessage());
}

require_once __DIR__ . '/../views/import/import.php';
