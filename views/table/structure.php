<?php
/**
 * Table Structure View
 * 
 * Displays column definitions, indexes, and table info.
 * Provides actions to add, edit, and drop columns.
 */
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h2><i data-lucide="columns" class="icon"></i> Structure: <span class="highlight"><?= h($tableName) ?></span></h2>
    <div class="header-actions">
        <a href="<?= h(build_url(['page' => 'browse', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-info btn-sm">
            <i data-lucide="eye" class="icon"></i> Browse
        </a>
        <a href="<?= h(build_url(['page' => 'insert', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-success btn-sm">
            <i data-lucide="plus" class="icon"></i> Insert
        </a>
        <a href="<?= h(build_url(['page' => 'sql', 'db' => $dbName])) ?>" class="btn btn-secondary btn-sm">
            <i data-lucide="terminal" class="icon"></i> SQL
        </a>
        <a href="<?= h(build_url(['page' => 'add_column', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-primary btn-sm">
            <i data-lucide="plus-circle" class="icon"></i> Add Column
        </a>
    </div>
</div>

<!-- Table Info Card -->
<?php if ($tableStatus): ?>
<div class="info-cards">
    <div class="info-card">
        <div class="info-card-content">
            <span class="info-label">Engine</span>
            <span class="info-value"><?= h($tableStatus['Engine'] ?? 'N/A') ?></span>
        </div>
    </div>
    <div class="info-card">
        <div class="info-card-content">
            <span class="info-label">Rows</span>
            <span class="info-value"><?= number_format((int)($tableStatus['Rows'] ?? 0)) ?></span>
        </div>
    </div>
    <div class="info-card">
        <div class="info-card-content">
            <span class="info-label">Size</span>
            <span class="info-value"><?= format_bytes((int)(($tableStatus['Data_length'] ?? 0) + ($tableStatus['Index_length'] ?? 0))) ?></span>
        </div>
    </div>
    <div class="info-card">
        <div class="info-card-content">
            <span class="info-label">Collation</span>
            <span class="info-value"><?= h($tableStatus['Collation'] ?? 'N/A') ?></span>
        </div>
    </div>
    <div class="info-card">
        <div class="info-card-content">
            <span class="info-label">Auto Increment</span>
            <span class="info-value"><?= h($tableStatus['Auto_increment'] ?? 'N/A') ?></span>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Columns -->
<div class="card">
    <div class="card-header">
        <h3>Columns</h3>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Null</th>
                    <th>Key</th>
                    <th>Default</th>
                    <th>Extra</th>
                    <th>Collation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($columns as $i => $col): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><strong><?= h($col['Field']) ?></strong></td>
                    <td><span class="badge badge-type"><?= h($col['Type']) ?></span></td>
                    <td>
                        <?php if ($col['Null'] === 'YES'): ?>
                            <span class="badge badge-null">NULL</span>
                        <?php else: ?>
                            <span class="badge badge-notnull">NOT NULL</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($col['Key'] === 'PRI'): ?>
                            <span class="badge badge-primary">PRI</span>
                        <?php elseif ($col['Key'] === 'UNI'): ?>
                            <span class="badge badge-unique">UNI</span>
                        <?php elseif ($col['Key'] === 'MUL'): ?>
                            <span class="badge badge-index">MUL</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $col['Default'] !== null ? h($col['Default']) : '<em class="muted">NULL</em>' ?></td>
                    <td><?= h($col['Extra']) ?></td>
                    <td><?= h($col['Collation'] ?? '') ?></td>
                    <td class="actions">
                        <a href="<?= h(build_url(['page' => 'edit_column', 'db' => $dbName, 'table' => $tableName, 'column' => $col['Field']])) ?>" 
                           class="btn btn-warning btn-xs" title="Edit">
                            <i data-lucide="edit" class="icon-xs"></i>
                        </a>
                        <a href="<?= h(build_url(['page' => 'drop_column', 'db' => $dbName, 'table' => $tableName, 'column' => $col['Field']])) ?>" 
                           class="btn btn-danger btn-xs" title="Drop"
                           onclick="return confirm('Drop column \'<?= h($col['Field']) ?>\'? This cannot be undone!')">
                            <i data-lucide="trash-2" class="icon-xs"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Indexes -->
<?php if (!empty($indexes)): ?>
<div class="card">
    <div class="card-header">
        <h3>Indexes</h3>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Key Name</th>
                    <th>Column</th>
                    <th>Unique</th>
                    <th>Type</th>
                    <th>Cardinality</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($indexes as $idx): ?>
                <tr>
                    <td><strong><?= h($idx['Key_name']) ?></strong></td>
                    <td><?= h($idx['Column_name']) ?></td>
                    <td><?= $idx['Non_unique'] ? 'No' : 'Yes' ?></td>
                    <td><?= h($idx['Index_type']) ?></td>
                    <td><?= h($idx['Cardinality'] ?? 'N/A') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
