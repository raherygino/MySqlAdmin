<?php
/**
 * Column Controller
 * 
 * Handles column-level operations on a table:
 * - Add a new column
 * - Edit (modify) an existing column
 * - Drop (delete) a column
 */

require_once __DIR__ . '/../helpers/functions.php';

$action    = $page;
$dbName    = $_GET['db']    ?? $_POST['db']    ?? '';
$tableName = $_GET['table'] ?? $_POST['table'] ?? '';

if (empty($dbName) || empty($tableName)) {
    set_flash('error', 'Database and table are required.');
    redirect('index.php?page=databases');
}

$structureUrl = build_url(['page' => 'table_structure', 'db' => $dbName, 'table' => $tableName]);

try {
    $pdo = get_connection($dbName);

    switch ($action) {

        // --- Add Column ---
        case 'add_column':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name    = trim($_POST['col_name'] ?? '');
                $type    = trim($_POST['col_type'] ?? 'VARCHAR');
                $length  = trim($_POST['col_length'] ?? '');
                $null    = ($_POST['col_null'] ?? '') === 'yes' ? 'NULL' : 'NOT NULL';
                $default = trim($_POST['col_default'] ?? '');
                $after   = trim($_POST['col_after'] ?? '');

                if (empty($name)) {
                    set_flash('error', 'Column name is required.');
                    redirect($structureUrl);
                }

                $sql = "ALTER TABLE `{$tableName}` ADD COLUMN `{$name}` {$type}";
                if (!empty($length)) {
                    $sql .= "({$length})";
                }
                $sql .= " {$null}";
                if ($default !== '') {
                    $sql .= " DEFAULT " . $pdo->quote($default);
                }
                if (!empty($after)) {
                    $sql .= " AFTER `{$after}`";
                }

                $pdo->exec($sql);
                set_flash('success', "Column <strong>{$name}</strong> added successfully.");
                redirect($structureUrl);
            }

            // GET: show add column form
            $existingColumns = $pdo->query("SHOW COLUMNS FROM `{$tableName}`")->fetchAll();
            require_once __DIR__ . '/../views/column/add.php';
            break;

        // --- Edit Column ---
        case 'edit_column':
            $colName = $_GET['column'] ?? $_POST['old_name'] ?? '';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $oldName = trim($_POST['old_name'] ?? '');
                $newName = trim($_POST['col_name'] ?? '');
                $type    = trim($_POST['col_type'] ?? 'VARCHAR');
                $length  = trim($_POST['col_length'] ?? '');
                $null    = ($_POST['col_null'] ?? '') === 'yes' ? 'NULL' : 'NOT NULL';
                $default = trim($_POST['col_default'] ?? '');

                if (empty($newName)) {
                    set_flash('error', 'Column name is required.');
                    redirect($structureUrl);
                }

                $sql = "ALTER TABLE `{$tableName}` CHANGE `{$oldName}` `{$newName}` {$type}";
                if (!empty($length)) {
                    $sql .= "({$length})";
                }
                $sql .= " {$null}";
                if ($default !== '') {
                    $sql .= " DEFAULT " . $pdo->quote($default);
                }

                $pdo->exec($sql);
                set_flash('success', "Column <strong>{$oldName}</strong> modified successfully.");
                redirect($structureUrl);
            }

            // GET: fetch current column info and show form
            if (empty($colName)) {
                set_flash('error', 'No column specified.');
                redirect($structureUrl);
            }

            $columns = $pdo->query("SHOW FULL COLUMNS FROM `{$tableName}`")->fetchAll();
            $column  = null;
            foreach ($columns as $c) {
                if ($c['Field'] === $colName) {
                    $column = $c;
                    break;
                }
            }

            if (!$column) {
                set_flash('error', 'Column not found.');
                redirect($structureUrl);
            }

            require_once __DIR__ . '/../views/column/edit.php';
            break;

        // --- Drop Column ---
        case 'drop_column':
            $colName = $_GET['column'] ?? '';
            if (!empty($colName)) {
                $pdo->exec("ALTER TABLE `{$tableName}` DROP COLUMN `{$colName}`");
                set_flash('success', "Column <strong>{$colName}</strong> dropped successfully.");
            }
            redirect($structureUrl);
            break;
    }

} catch (PDOException $e) {
    set_flash('error', 'Column error: ' . $e->getMessage());
    redirect($structureUrl);
}
