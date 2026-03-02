<?php
/**
 * Dashboard Controller
 * 
 * Shows server information and lists all databases with their sizes.
 */

require_once __DIR__ . '/../helpers/functions.php';

try {
    $pdo = get_connection();

    // Fetch server variables
    $serverVersion = $pdo->query("SELECT VERSION()")->fetchColumn();
    $uptime        = $pdo->query("SHOW STATUS LIKE 'Uptime'")->fetch();
    $charset       = $pdo->query("SHOW VARIABLES LIKE 'character_set_server'")->fetch();

    // Fetch all databases with sizes
    $databases = $pdo->query("
        SELECT 
            SCHEMA_NAME AS name,
            DEFAULT_CHARACTER_SET_NAME AS charset,
            DEFAULT_COLLATION_NAME AS collation,
            IFNULL(ROUND(SUM(t.DATA_LENGTH + t.INDEX_LENGTH), 0), 0) AS size,
            COUNT(t.TABLE_NAME) AS table_count
        FROM information_schema.SCHEMATA s
        LEFT JOIN information_schema.TABLES t ON t.TABLE_SCHEMA = s.SCHEMA_NAME
        GROUP BY s.SCHEMA_NAME, s.DEFAULT_CHARACTER_SET_NAME, s.DEFAULT_COLLATION_NAME
        ORDER BY s.SCHEMA_NAME
    ")->fetchAll();

} catch (PDOException $e) {
    set_flash('error', 'Error: ' . $e->getMessage());
    $databases = [];
    $serverVersion = 'N/A';
}

// Render view
require_once __DIR__ . '/../views/dashboard.php';
