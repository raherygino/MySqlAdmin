<?php
/**
 * Dashboard View
 * 
 * Displays server information and a summary of all databases.
 */
require_once __DIR__ . '/layout/header.php';
?>

<div class="page-header">
    <h2><i data-lucide="layout-dashboard" class="icon"></i> Server Dashboard</h2>
</div>

<!-- Server Info Cards -->
<div class="info-cards">
    <div class="info-card">
        <div class="info-card-icon"><i data-lucide="server" class="icon-lg"></i></div>
        <div class="info-card-content">
            <span class="info-label">MySQL Version</span>
            <span class="info-value"><?= h($serverVersion ?? 'N/A') ?></span>
        </div>
    </div>
    <div class="info-card">
        <div class="info-card-icon"><i data-lucide="database" class="icon-lg"></i></div>
        <div class="info-card-content">
            <span class="info-label">Databases</span>
            <span class="info-value"><?= count($databases ?? []) ?></span>
        </div>
    </div>
    <div class="info-card">
        <div class="info-card-icon"><i data-lucide="clock" class="icon-lg"></i></div>
        <div class="info-card-content">
            <span class="info-label">Uptime</span>
            <span class="info-value"><?= isset($uptime['Value']) ? gmdate('H\h i\m s\s', (int)$uptime['Value']) : 'N/A' ?></span>
        </div>
    </div>
    <div class="info-card">
        <div class="info-card-icon"><i data-lucide="type" class="icon-lg"></i></div>
        <div class="info-card-content">
            <span class="info-label">Charset</span>
            <span class="info-value"><?= h($charset['Value'] ?? 'N/A') ?></span>
        </div>
    </div>
</div>

<!-- Databases Table -->
<div class="card">
    <div class="card-header">
        <h3>All Databases</h3>
        <a href="index.php?page=create_database" class="btn btn-primary btn-sm">
            <i data-lucide="plus" class="icon"></i> Create Database
        </a>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Database</th>
                    <th>Charset</th>
                    <th>Collation</th>
                    <th>Tables</th>
                    <th>Size</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($databases)): ?>
                <?php foreach ($databases as $db): ?>
                <tr>
                    <td>
                        <a href="<?= h(build_url(['page' => 'tables', 'db' => $db['name']])) ?>" class="db-link">
                            <i data-lucide="database" class="icon-sm"></i> <?= h($db['name']) ?>
                        </a>
                    </td>
                    <td><?= h($db['charset']) ?></td>
                    <td><?= h($db['collation']) ?></td>
                    <td><?= (int)$db['table_count'] ?></td>
                    <td><?= format_bytes((int)$db['size']) ?></td>
                    <td class="actions">
                        <a href="<?= h(build_url(['page' => 'tables', 'db' => $db['name']])) ?>" class="btn btn-info btn-xs" title="Browse">
                            <i data-lucide="eye" class="icon-xs"></i>
                        </a>
                        <a href="<?= h(build_url(['page' => 'sql', 'db' => $db['name']])) ?>" class="btn btn-secondary btn-xs" title="SQL">
                            <i data-lucide="terminal" class="icon-xs"></i>
                        </a>
                        <a href="<?= h(build_url(['page' => 'export', 'db' => $db['name']])) ?>" class="btn btn-success btn-xs" title="Export">
                            <i data-lucide="download" class="icon-xs"></i>
                        </a>
                        <a href="<?= h(build_url(['page' => 'drop_database', 'name' => $db['name']])) ?>" 
                           class="btn btn-danger btn-xs" title="Drop"
                           onclick="return confirm('Are you sure you want to drop database \'<?= h($db['name']) ?>\'? This action cannot be undone!')">
                            <i data-lucide="trash-2" class="icon-xs"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="empty-state">No databases found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
