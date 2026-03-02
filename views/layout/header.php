<?php
/**
 * Layout Header
 * 
 * Shared header for all authenticated pages.
 * Includes navigation, breadcrumbs, and flash messages.
 */
require_once __DIR__ . '/../../helpers/functions.php';

$flash     = get_flash();
$currentDb = $_GET['db'] ?? '';
$currentTable = $_GET['table'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGBD – Database Manager</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Lucide Icons via CDN -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Favicon -->
    <link rel="icon" href="./assets/img/favicon.png" type="image/png">

</head>
<body>

<!-- Top Navigation Bar -->
<nav class="navbar">
    <div class="navbar-brand">
        <a href="index.php?page=dashboard">
            <span class="logo-icon">&#9881;</span> SGBD
        </a>
    </div>
    <div class="navbar-menu">
        <a href="index.php?page=dashboard" class="nav-link <?= current_page() === 'dashboard' ? 'active' : '' ?>">
            <i data-lucide="layout-dashboard" class="icon"></i> Dashboard
        </a>
        <a href="index.php?page=databases" class="nav-link <?= current_page() === 'databases' ? 'active' : '' ?>">
            <i data-lucide="database" class="icon"></i> Databases
        </a>
        <?php if (!empty($currentDb)): ?>
        <a href="<?= h(build_url(['page' => 'tables', 'db' => $currentDb])) ?>" class="nav-link <?= in_array(current_page(), ['tables','table_structure','browse','insert','edit_record']) ? 'active' : '' ?>">
            <i data-lucide="table" class="icon"></i> Tables
        </a>
        <a href="<?= h(build_url(['page' => 'sql', 'db' => $currentDb])) ?>" class="nav-link <?= current_page() === 'sql' ? 'active' : '' ?>">
            <i data-lucide="terminal" class="icon"></i> SQL
        </a>
        <a href="<?= h(build_url(['page' => 'import', 'db' => $currentDb])) ?>" class="nav-link <?= current_page() === 'import' ? 'active' : '' ?>">
            <i data-lucide="upload" class="icon"></i> Import
        </a>
        <a href="<?= h(build_url(['page' => 'export', 'db' => $currentDb])) ?>" class="nav-link <?= current_page() === 'export' ? 'active' : '' ?>">
            <i data-lucide="download" class="icon"></i> Export
        </a>
        <?php else: ?>
        <a href="index.php?page=sql" class="nav-link <?= current_page() === 'sql' ? 'active' : '' ?>">
            <i data-lucide="terminal" class="icon"></i> SQL
        </a>
        <a href="index.php?page=import" class="nav-link <?= current_page() === 'import' ? 'active' : '' ?>">
            <i data-lucide="upload" class="icon"></i> Import
        </a>
        <?php endif; ?>
    </div>
    <div class="navbar-right">
        <span class="user-info">
            <i data-lucide="user" class="icon"></i>
            <?= h($_SESSION['db_user'] ?? '') ?>@<?= h($_SESSION['db_host'] ?? '') ?>
        </span>
        <a href="index.php?page=logout" class="nav-link logout-link">
            <i data-lucide="log-out" class="icon"></i> Logout
        </a>
    </div>
</nav>

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <a href="index.php?page=dashboard">Home</a>
    <?php if (!empty($currentDb)): ?>
        <span class="sep">/</span>
        <a href="<?= h(build_url(['page' => 'tables', 'db' => $currentDb])) ?>"><?= h($currentDb) ?></a>
    <?php endif; ?>
    <?php if (!empty($currentTable)): ?>
        <span class="sep">/</span>
        <a href="<?= h(build_url(['page' => 'table_structure', 'db' => $currentDb, 'table' => $currentTable])) ?>"><?= h($currentTable) ?></a>
    <?php endif; ?>
</div>

<!-- Flash Messages -->
<?php if ($flash): ?>
<div class="alert alert-<?= h($flash['type']) ?>">
    <span><?= $flash['message'] ?></span>
    <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
</div>
<?php endif; ?>

<!-- Main Content Area -->
<main class="container">
