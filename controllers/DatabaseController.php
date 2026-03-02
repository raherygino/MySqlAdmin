<?php
/**
 * Database Controller
 * 
 * Handles database-level operations:
 * - List all databases
 * - Create a new database
 * - Drop (delete) a database
 * - Rename a database (by creating new, moving tables, dropping old)
 */

require_once __DIR__ . '/../helpers/functions.php';

$action = $page;

try {
    $pdo = get_connection();

    switch ($action) {

        // --- Create Database ---
        case 'create_database':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $dbName    = trim($_POST['db_name'] ?? '');
                $charset   = trim($_POST['charset'] ?? 'utf8mb4');
                $collation = trim($_POST['collation'] ?? 'utf8mb4_general_ci');

                if (empty($dbName)) {
                    set_flash('error', 'Database name is required.');
                } else {
                    // Validate database name (alphanumeric + underscore only)
                    if (!preg_match('/^[a-zA-Z0-9_]+$/', $dbName)) {
                        set_flash('error', 'Invalid database name. Use only letters, numbers, and underscores.');
                    } else {
                        $safeName = preg_replace('/[^a-zA-Z0-9_]/', '', $dbName);
                        $pdo->exec("CREATE DATABASE `{$safeName}` CHARACTER SET {$charset} COLLATE {$collation}");
                        set_flash('success', "Database <strong>{$safeName}</strong> created successfully.");
                    }
                }
                redirect('index.php?page=databases');
            }
            // GET: show form
            $charsets = $pdo->query("SHOW CHARACTER SET")->fetchAll();
            $collations = $pdo->query("SHOW COLLATION")->fetchAll();
            require_once __DIR__ . '/../views/database/create.php';
            break;

        // --- Drop Database ---
        case 'drop_database':
            $dbName = $_GET['name'] ?? '';
            if (!empty($dbName) && preg_match('/^[a-zA-Z0-9_]+$/', $dbName)) {
                $pdo->exec("DROP DATABASE `{$dbName}`");
                set_flash('success', "Database <strong>{$dbName}</strong> dropped successfully.");
            } else {
                set_flash('error', 'Invalid database name.');
            }
            redirect('index.php?page=databases');
            break;

        // --- Rename Database ---
        case 'rename_database':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $oldName = trim($_POST['old_name'] ?? '');
                $newName = trim($_POST['new_name'] ?? '');

                if (empty($oldName) || empty($newName)) {
                    set_flash('error', 'Both old and new database names are required.');
                } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $newName)) {
                    set_flash('error', 'Invalid new database name.');
                } else {
                    // Get charset and collation from old database
                    $stmt = $pdo->prepare("SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?");
                    $stmt->execute([$oldName]);
                    $info = $stmt->fetch();

                    if ($info) {
                        $charset   = $info['DEFAULT_CHARACTER_SET_NAME'];
                        $collation = $info['DEFAULT_COLLATION_NAME'];

                        // Create new database
                        $pdo->exec("CREATE DATABASE `{$newName}` CHARACTER SET {$charset} COLLATE {$collation}");

                        // Move all tables
                        $tables = $pdo->query("SHOW TABLES FROM `{$oldName}`")->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($tables as $table) {
                            $pdo->exec("ALTER TABLE `{$oldName}`.`{$table}` RENAME `{$newName}`.`{$table}`");
                        }

                        // Drop old database
                        $pdo->exec("DROP DATABASE `{$oldName}`");
                        set_flash('success', "Database renamed from <strong>{$oldName}</strong> to <strong>{$newName}</strong>.");
                    } else {
                        set_flash('error', 'Source database not found.');
                    }
                }
                redirect('index.php?page=databases');
            }
            break;

        // --- List Databases ---
        case 'databases':
        default:
            $databases = $pdo->query("
                SELECT 
                    SCHEMA_NAME AS name,
                    DEFAULT_CHARACTER_SET_NAME AS charset,
                    DEFAULT_COLLATION_NAME AS collation
                FROM information_schema.SCHEMATA
                ORDER BY SCHEMA_NAME
            ")->fetchAll();
            require_once __DIR__ . '/../views/database/list.php';
            break;
    }

} catch (PDOException $e) {
    set_flash('error', 'Database error: ' . $e->getMessage());
    redirect('index.php?page=databases');
}
