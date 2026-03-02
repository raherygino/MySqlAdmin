<?php
/**
 * Export Controller
 * 
 * Handles exporting databases and individual tables in SQL format.
 * Generates a downloadable .sql file containing CREATE and INSERT statements.
 */

require_once __DIR__ . '/../helpers/functions.php';

$dbName    = $_GET['db']    ?? $_POST['db']    ?? '';
$tableName = $_GET['table'] ?? $_POST['table'] ?? '';
$action    = $_GET['action'] ?? 'form'; // 'form' or 'download'

if (empty($dbName)) {
    set_flash('error', 'No database selected for export.');
    redirect('index.php?page=databases');
}

try {
    $pdo = get_connection($dbName);

    // --- Download export ---
    if ($action === 'download' || $_SERVER['REQUEST_METHOD'] === 'POST') {
        $exportTables = [];

        if (!empty($tableName)) {
            // Export a single table
            $exportTables = [$tableName];
        } elseif (isset($_POST['tables']) && is_array($_POST['tables'])) {
            // Export selected tables
            $exportTables = $_POST['tables'];
        } else {
            // Export all tables in the database
            $exportTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        }

        // Build the SQL dump
        $dump = "";
        $dump .= "-- ============================================\n";
        $dump .= "-- SGBD Export\n";
        $dump .= "-- Database: {$dbName}\n";
        $dump .= "-- Date: " . date('Y-m-d H:i:s') . "\n";
        $dump .= "-- ============================================\n\n";
        $dump .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $dump .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $dump .= "SET AUTOCOMMIT = 0;\n";
        $dump .= "START TRANSACTION;\n\n";

        foreach ($exportTables as $tbl) {
            $dump .= "-- -------------------------------------------\n";
            $dump .= "-- Table structure for `{$tbl}`\n";
            $dump .= "-- -------------------------------------------\n\n";

            // DROP TABLE
            $dump .= "DROP TABLE IF EXISTS `{$tbl}`;\n";

            // CREATE TABLE
            $createStmt = $pdo->query("SHOW CREATE TABLE `{$tbl}`")->fetch();
            $dump .= $createStmt['Create Table'] . ";\n\n";

            // INSERT statements
            $rows = $pdo->query("SELECT * FROM `{$tbl}`")->fetchAll();

            if (!empty($rows)) {
                $dump .= "-- -------------------------------------------\n";
                $dump .= "-- Data for table `{$tbl}`\n";
                $dump .= "-- -------------------------------------------\n\n";

                foreach ($rows as $row) {
                    $values = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = $pdo->quote($val);
                        }
                    }
                    $cols = array_map(function ($c) { return "`{$c}`"; }, array_keys($row));
                    $dump .= "INSERT INTO `{$tbl}` (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $values) . ");\n";
                }
                $dump .= "\n";
            }
        }

        $dump .= "COMMIT;\n";
        $dump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        // Send as download
        $filename = $dbName . (!empty($tableName) ? "_{$tableName}" : '') . '_' . date('Ymd_His') . '.sql';
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($dump));
        echo $dump;
        exit;
    }

    // --- Show export form ---
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    require_once __DIR__ . '/../views/export/export.php';

} catch (PDOException $e) {
    set_flash('error', 'Export error: ' . $e->getMessage());
    redirect(build_url(['page' => 'tables', 'db' => $dbName]));
}
