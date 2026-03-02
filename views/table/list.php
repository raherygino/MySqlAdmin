<?php
/**
 * Table List View
 * 
 * Lists all tables in the selected database with actions.
 */
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h2><i data-lucide="table" class="icon"></i> Tables in <span class="highlight"><?= h($dbName) ?></span></h2>
    <div class="header-actions">
        <a href="<?= h(build_url(['page' => 'create_table', 'db' => $dbName])) ?>" class="btn btn-primary">
            <i data-lucide="plus" class="icon"></i> Create Table
        </a>
        <a href="<?= h(build_url(['page' => 'export', 'db' => $dbName])) ?>" class="btn btn-success">
            <i data-lucide="download" class="icon"></i> Export DB
        </a>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Table Name</th>
                    <th>Engine</th>
                    <th>Rows</th>
                    <th>Size</th>
                    <th>Collation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($tables)): ?>
                <?php foreach ($tables as $tbl): ?>
                <tr>
                    <td>
                        <a href="<?= h(build_url(['page' => 'browse', 'db' => $dbName, 'table' => $tbl['name']])) ?>" class="table-link">
                            <i data-lucide="table" class="icon-sm"></i> <?= h($tbl['name']) ?>
                        </a>
                    </td>
                    <td><span class="badge"><?= h($tbl['engine'] ?? 'N/A') ?></span></td>
                    <td><?= number_format((int)($tbl['row_count'] ?? 0)) ?></td>
                    <td><?= format_bytes((int)($tbl['size'] ?? 0)) ?></td>
                    <td><?= h($tbl['collation'] ?? 'N/A') ?></td>
                    <td class="actions">
                        <a href="<?= h(build_url(['page' => 'browse', 'db' => $dbName, 'table' => $tbl['name']])) ?>" 
                           class="btn btn-info btn-xs" title="Browse">
                            <i data-lucide="eye" class="icon-xs"></i>
                        </a>
                        <a href="<?= h(build_url(['page' => 'table_structure', 'db' => $dbName, 'table' => $tbl['name']])) ?>" 
                           class="btn btn-secondary btn-xs" title="Structure">
                            <i data-lucide="columns" class="icon-xs"></i>
                        </a>
                        <a href="<?= h(build_url(['page' => 'insert', 'db' => $dbName, 'table' => $tbl['name']])) ?>" 
                           class="btn btn-success btn-xs" title="Insert">
                            <i data-lucide="plus" class="icon-xs"></i>
                        </a>
                        <a href="<?= h(build_url(['page' => 'export', 'db' => $dbName, 'table' => $tbl['name']])) ?>" 
                           class="btn btn-warning btn-xs" title="Export">
                            <i data-lucide="download" class="icon-xs"></i>
                        </a>
                        <a href="<?= h(build_url(['page' => 'truncate_table', 'db' => $dbName, 'table' => $tbl['name']])) ?>" 
                           class="btn btn-danger btn-xs" title="Truncate"
                           onclick="return confirm('Truncate table \'<?= h($tbl['name']) ?>\'? All data will be deleted!')">
                            <i data-lucide="eraser" class="icon-xs"></i>
                        </a>
                        <a href="<?= h(build_url(['page' => 'drop_table', 'db' => $dbName, 'table' => $tbl['name']])) ?>" 
                           class="btn btn-danger btn-xs" title="Drop"
                           onclick="return confirm('Drop table \'<?= h($tbl['name']) ?>\'? This cannot be undone!')">
                            <i data-lucide="trash-2" class="icon-xs"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="empty-state">No tables in this database.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
