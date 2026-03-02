<?php
/**
 * Record Controller
 * 
 * Handles row-level operations:
 * - Browse (paginated listing of records)
 * - Insert a new record
 * - Edit an existing record
 * - Delete a record
 */

require_once __DIR__ . '/../helpers/functions.php';

$action    = $page;
$dbName    = $_GET['db']    ?? $_POST['db']    ?? '';
$tableName = $_GET['table'] ?? $_POST['table'] ?? '';

if (empty($dbName) || empty($tableName)) {
    set_flash('error', 'Database and table are required.');
    redirect('index.php?page=databases');
}

$browseUrl = build_url(['page' => 'browse', 'db' => $dbName, 'table' => $tableName]);

try {
    $pdo = get_connection($dbName);

    switch ($action) {

        // --- Browse Records (with pagination & search) ---
        case 'browse':
            $perPage     = (int)($_GET['per_page'] ?? 25);
            $currentPage = max(1, (int)($_GET['p'] ?? 1));
            $search      = trim($_GET['search'] ?? '');
            $sortCol     = $_GET['sort'] ?? '';
            $sortDir     = (strtoupper($_GET['dir'] ?? 'ASC') === 'DESC') ? 'DESC' : 'ASC';
            $offset      = ($currentPage - 1) * $perPage;

            // Get columns for the table
            $columns = $pdo->query("SHOW COLUMNS FROM `{$tableName}`")->fetchAll();

            // Build WHERE clause for search
            $whereSql = '';
            $searchParams = [];
            if (!empty($search)) {
                $conditions = [];
                foreach ($columns as $col) {
                    $conditions[] = "`{$col['Field']}` LIKE ?";
                    $searchParams[] = "%{$search}%";
                }
                $whereSql = 'WHERE ' . implode(' OR ', $conditions);
            }

            // Build ORDER BY clause
            $orderSql = '';
            if (!empty($sortCol)) {
                $orderSql = "ORDER BY `{$sortCol}` {$sortDir}";
            }

            // Count total records
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM `{$tableName}` {$whereSql}");
            $countStmt->execute($searchParams);
            $totalRecords = (int)$countStmt->fetchColumn();
            $totalPages   = max(1, ceil($totalRecords / $perPage));

            // Fetch records
            $dataStmt = $pdo->prepare("SELECT * FROM `{$tableName}` {$whereSql} {$orderSql} LIMIT {$perPage} OFFSET {$offset}");
            $dataStmt->execute($searchParams);
            $records = $dataStmt->fetchAll();

            // Detect primary key column(s)
            $primaryKeys = [];
            foreach ($columns as $col) {
                if ($col['Key'] === 'PRI') {
                    $primaryKeys[] = $col['Field'];
                }
            }

            require_once __DIR__ . '/../views/record/browse.php';
            break;

        // --- Insert Record ---
        case 'insert':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fields = $_POST['fields'] ?? [];

                if (empty($fields)) {
                    set_flash('error', 'No data provided.');
                    redirect($browseUrl);
                }

                // Filter out empty auto-increment fields
                $insertFields = [];
                $insertValues = [];
                $placeholders = [];

                foreach ($fields as $fieldName => $value) {
                    if ($value === '' || $value === null) {
                        // Check if column allows NULL or has AUTO_INCREMENT
                        continue; // skip empty values; let MySQL use defaults
                    }
                    $insertFields[] = "`{$fieldName}`";
                    $insertValues[] = $value;
                    $placeholders[] = '?';
                }

                if (!empty($insertFields)) {
                    $sql = "INSERT INTO `{$tableName}` (" . implode(', ', $insertFields) . ") VALUES (" . implode(', ', $placeholders) . ")";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($insertValues);
                    set_flash('success', 'Record inserted successfully. Affected rows: ' . $stmt->rowCount());
                } else {
                    // Insert with all defaults
                    $pdo->exec("INSERT INTO `{$tableName}` () VALUES ()");
                    set_flash('success', 'Record inserted with default values.');
                }
                redirect($browseUrl);
            }

            // GET: show insert form
            $columns = $pdo->query("SHOW FULL COLUMNS FROM `{$tableName}`")->fetchAll();
            require_once __DIR__ . '/../views/record/insert.php';
            break;

        // --- Edit Record ---
        case 'edit_record':
            // Build WHERE clause from primary key values passed in query string
            $columns    = $pdo->query("SHOW FULL COLUMNS FROM `{$tableName}`")->fetchAll();
            $primaryKeys = [];
            foreach ($columns as $col) {
                if ($col['Key'] === 'PRI') {
                    $primaryKeys[] = $col['Field'];
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fields    = $_POST['fields'] ?? [];
                $pkValues  = $_POST['pk'] ?? [];

                if (empty($primaryKeys)) {
                    set_flash('error', 'Cannot edit: no primary key defined on this table.');
                    redirect($browseUrl);
                }

                // Build SET clause
                $setClauses = [];
                $params     = [];
                foreach ($fields as $fieldName => $value) {
                    $setClauses[] = "`{$fieldName}` = ?";
                    $params[] = ($value === '') ? null : $value;
                }

                // Build WHERE clause
                $whereClauses = [];
                foreach ($primaryKeys as $pk) {
                    $whereClauses[] = "`{$pk}` = ?";
                    $params[] = $pkValues[$pk] ?? '';
                }

                $sql  = "UPDATE `{$tableName}` SET " . implode(', ', $setClauses) . " WHERE " . implode(' AND ', $whereClauses);
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                set_flash('success', 'Record updated successfully. Affected rows: ' . $stmt->rowCount());
                redirect($browseUrl);
            }

            // GET: fetch the record to edit
            if (empty($primaryKeys)) {
                set_flash('error', 'Cannot edit: no primary key defined on this table.');
                redirect($browseUrl);
            }

            $whereClauses = [];
            $params       = [];
            foreach ($primaryKeys as $pk) {
                $whereClauses[] = "`{$pk}` = ?";
                $params[]       = $_GET[$pk] ?? '';
            }

            $stmt = $pdo->prepare("SELECT * FROM `{$tableName}` WHERE " . implode(' AND ', $whereClauses) . " LIMIT 1");
            $stmt->execute($params);
            $record = $stmt->fetch();

            if (!$record) {
                set_flash('error', 'Record not found.');
                redirect($browseUrl);
            }

            require_once __DIR__ . '/../views/record/edit.php';
            break;

        // --- Delete Record ---
        case 'delete_record':
            $columns = $pdo->query("SHOW FULL COLUMNS FROM `{$tableName}`")->fetchAll();
            $primaryKeys = [];
            foreach ($columns as $col) {
                if ($col['Key'] === 'PRI') {
                    $primaryKeys[] = $col['Field'];
                }
            }

            if (empty($primaryKeys)) {
                set_flash('error', 'Cannot delete: no primary key defined on this table.');
                redirect($browseUrl);
            }

            $whereClauses = [];
            $params       = [];
            foreach ($primaryKeys as $pk) {
                $whereClauses[] = "`{$pk}` = ?";
                $params[]       = $_GET[$pk] ?? '';
            }

            $stmt = $pdo->prepare("DELETE FROM `{$tableName}` WHERE " . implode(' AND ', $whereClauses) . " LIMIT 1");
            $stmt->execute($params);

            set_flash('success', 'Record deleted. Affected rows: ' . $stmt->rowCount());
            redirect($browseUrl);
            break;
    }

} catch (PDOException $e) {
    set_flash('error', 'Record error: ' . $e->getMessage());
    redirect($browseUrl);
}
