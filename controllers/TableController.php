<?php
/**
 * Table Controller
 * 
 * Handles table-level operations:
 * - List tables in a database
 * - Create a new table
 * - Drop a table
 * - Truncate a table
 * - View table structure (columns, indexes, etc.)
 */

require_once __DIR__ . '/../helpers/functions.php';

$action = $page;
$dbName = $_GET['db'] ?? $_POST['db'] ?? '';

if (empty($dbName)) {
    set_flash('error', 'No database selected.');
    redirect('index.php?page=databases');
}

try {
    $pdo = get_connection($dbName);

    switch ($action) {

        // --- Create Table ---
        case 'create_table':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $tableName = trim($_POST['table_name'] ?? '');
                $columns   = $_POST['columns'] ?? [];
                $engine    = trim($_POST['engine'] ?? 'InnoDB');

                if (empty($tableName)) {
                    set_flash('error', 'Table name is required.');
                    redirect(build_url(['page' => 'create_table', 'db' => $dbName]));
                }

                if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
                    set_flash('error', 'Invalid table name. Use only letters, numbers, and underscores.');
                    redirect(build_url(['page' => 'create_table', 'db' => $dbName]));
                }

                if (empty($columns)) {
                    set_flash('error', 'At least one column is required.');
                    redirect(build_url(['page' => 'create_table', 'db' => $dbName]));
                }

                // Build CREATE TABLE SQL
                $colDefs = [];
                $primaryKey = '';

                foreach ($columns as $col) {
                    $name    = trim($col['name'] ?? '');
                    $type    = trim($col['type'] ?? 'VARCHAR');
                    $length  = trim($col['length'] ?? '');
                    $null    = ($col['null'] ?? '') === 'yes' ? 'NULL' : 'NOT NULL';
                    $default = trim($col['default'] ?? '');
                    $ai      = isset($col['auto_increment']);
                    $pk      = isset($col['primary_key']);

                    if (empty($name)) continue;

                    $def = "`{$name}` {$type}";
                    if (!empty($length)) {
                        $def .= "({$length})";
                    }
                    $def .= " {$null}";
                    if ($default !== '') {
                        $def .= " DEFAULT '{$default}'";
                    }
                    if ($ai) {
                        $def .= " AUTO_INCREMENT";
                    }

                    $colDefs[] = $def;

                    if ($pk) {
                        $primaryKey = $name;
                    }
                }

                if (!empty($primaryKey)) {
                    $colDefs[] = "PRIMARY KEY (`{$primaryKey}`)";
                }

                $sql = "CREATE TABLE `{$tableName}` (\n" . implode(",\n", $colDefs) . "\n) ENGINE={$engine}";
                $pdo->exec($sql);

                set_flash('success', "Table <strong>{$tableName}</strong> created successfully.");
                redirect(build_url(['page' => 'tables', 'db' => $dbName]));
            }

            // GET: show create table form
            $engines = $pdo->query("SHOW ENGINES")->fetchAll();
            require_once __DIR__ . '/../views/table/create.php';
            break;

        // --- Drop Table ---
        case 'drop_table':
            $tableName = $_GET['table'] ?? '';
            if (!empty($tableName)) {
                $pdo->exec("DROP TABLE `{$tableName}`");
                set_flash('success', "Table <strong>{$tableName}</strong> dropped successfully.");
            }
            redirect(build_url(['page' => 'tables', 'db' => $dbName]));
            break;

        // --- Truncate Table ---
        case 'truncate_table':
            $tableName = $_GET['table'] ?? '';
            if (!empty($tableName)) {
                $pdo->exec("TRUNCATE TABLE `{$tableName}`");
                set_flash('success', "Table <strong>{$tableName}</strong> truncated successfully.");
            }
            redirect(build_url(['page' => 'tables', 'db' => $dbName]));
            break;

        // --- Table Structure ---
        case 'table_structure':
            $tableName = $_GET['table'] ?? '';
            if (empty($tableName)) {
                set_flash('error', 'No table specified.');
                redirect(build_url(['page' => 'tables', 'db' => $dbName]));
            }

            // Get columns
            $columns = $pdo->query("SHOW FULL COLUMNS FROM `{$tableName}`")->fetchAll();

            // Get indexes
            $indexes = $pdo->query("SHOW INDEX FROM `{$tableName}`")->fetchAll();

            // Get table status (engine, rows, size, etc.)
            $statusStmt = $pdo->prepare("SHOW TABLE STATUS FROM `{$dbName}` WHERE Name = ?");
            $statusStmt->execute([$tableName]);
            $tableStatus = $statusStmt->fetch();

            require_once __DIR__ . '/../views/table/structure.php';
            break;

        // --- List Tables ---
        case 'tables':
        default:
            $tables = $pdo->query("
                SELECT 
                    TABLE_NAME AS name,
                    ENGINE AS engine,
                    TABLE_ROWS AS row_count,
                    DATA_LENGTH + INDEX_LENGTH AS size,
                    TABLE_COLLATION AS collation,
                    CREATE_TIME AS created,
                    UPDATE_TIME AS updated
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = " . $pdo->quote($dbName) . "
                ORDER BY TABLE_NAME
            ")->fetchAll();

            require_once __DIR__ . '/../views/table/list.php';
            break;
    }

} catch (PDOException $e) {
    set_flash('error', 'Table error: ' . $e->getMessage());
    // Avoid redirect loop if selected database is invalid/inaccessible.
    redirect('index.php?page=databases');
}
