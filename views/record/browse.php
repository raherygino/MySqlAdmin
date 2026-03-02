<?php
/**
 * Browse Records View
 * 
 * Displays table data with pagination, search, sorting, and row actions.
 */
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h2><i data-lucide="eye" class="icon"></i> Browse: <span class="highlight"><?= h($tableName) ?></span></h2>
    <div class="header-actions">
        <a href="<?= h(build_url(['page' => 'table_structure', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-secondary btn-sm">
            <i data-lucide="columns" class="icon"></i> Structure
        </a>
        <a href="<?= h(build_url(['page' => 'insert', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-success btn-sm">
            <i data-lucide="plus" class="icon"></i> Insert
        </a>
        <a href="<?= h(build_url(['page' => 'export', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-info btn-sm">
            <i data-lucide="download" class="icon"></i> Export
        </a>
    </div>
</div>

<!-- Search Bar -->
<div class="card search-card">
    <form method="GET" class="search-form">
        <input type="hidden" name="page" value="browse">
        <input type="hidden" name="db" value="<?= h($dbName) ?>">
        <input type="hidden" name="table" value="<?= h($tableName) ?>">
        <div class="search-input-group">
            <input type="text" name="search" value="<?= h($search) ?>" placeholder="Search all columns..." class="search-input">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="search" class="icon"></i> Search
            </button>
            <?php if (!empty($search)): ?>
            <a href="<?= h(build_url(['page' => 'browse', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </div>
        <div class="search-meta">
            <span><?= number_format($totalRecords) ?> record(s) found</span>
            <span>Page <?= $currentPage ?> of <?= $totalPages ?></span>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="card">
    <div class="table-responsive">
        <table class="data-table data-table-striped">
            <thead>
                <tr>
                    <?php foreach ($columns as $col): ?>
                    <th>
                        <?php
                        $newDir = ($sortCol === $col['Field'] && $sortDir === 'ASC') ? 'DESC' : 'ASC';
                        $sortUrl = build_url(['page' => 'browse', 'db' => $dbName, 'table' => $tableName, 'sort' => $col['Field'], 'dir' => $newDir, 'search' => $search, 'p' => $currentPage]);
                        ?>
                        <a href="<?= h($sortUrl) ?>" class="sort-link">
                            <?= h($col['Field']) ?>
                            <?php if ($sortCol === $col['Field']): ?>
                                <span class="sort-arrow"><?= $sortDir === 'ASC' ? '&#9650;' : '&#9660;' ?></span>
                            <?php endif; ?>
                        </a>
                        <span class="col-type"><?= h($col['Type']) ?></span>
                    </th>
                    <?php endforeach; ?>
                    <?php if (!empty($primaryKeys)): ?>
                    <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($records)): ?>
                <?php foreach ($records as $row): ?>
                <tr>
                    <?php foreach ($columns as $col): ?>
                    <td>
                        <?php
                        $val = $row[$col['Field']] ?? null;
                        if ($val === null) {
                            echo '<em class="muted">NULL</em>';
                        } elseif (strlen($val) > 100) {
                            echo h(substr($val, 0, 100)) . '...';
                        } else {
                            echo h($val);
                        }
                        ?>
                    </td>
                    <?php endforeach; ?>
                    <?php if (!empty($primaryKeys)): ?>
                    <td class="actions nowrap">
                        <?php
                        // Build edit/delete URLs using primary key values
                        $pkParams = ['page' => 'edit_record', 'db' => $dbName, 'table' => $tableName];
                        $delParams = ['page' => 'delete_record', 'db' => $dbName, 'table' => $tableName];
                        foreach ($primaryKeys as $pk) {
                            $pkParams[$pk] = $row[$pk] ?? '';
                            $delParams[$pk] = $row[$pk] ?? '';
                        }
                        ?>
                        <a href="<?= h(build_url($pkParams)) ?>" class="btn btn-warning btn-xs" title="Edit">
                            <i data-lucide="edit" class="icon-xs"></i>
                        </a>
                        <a href="<?= h(build_url($delParams)) ?>" class="btn btn-danger btn-xs" title="Delete"
                           onclick="return confirm('Delete this record?')">
                            <i data-lucide="trash-2" class="icon-xs"></i>
                        </a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="<?= count($columns) + (!empty($primaryKeys) ? 1 : 0) ?>" class="empty-state">No records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php if ($currentPage > 1): ?>
    <a href="<?= h(build_url(['page' => 'browse', 'db' => $dbName, 'table' => $tableName, 'p' => $currentPage - 1, 'search' => $search, 'sort' => $sortCol, 'dir' => $sortDir])) ?>" class="btn btn-secondary btn-sm">&laquo; Prev</a>
    <?php endif; ?>

    <?php
    $start = max(1, $currentPage - 3);
    $end   = min($totalPages, $currentPage + 3);
    for ($i = $start; $i <= $end; $i++):
    ?>
    <a href="<?= h(build_url(['page' => 'browse', 'db' => $dbName, 'table' => $tableName, 'p' => $i, 'search' => $search, 'sort' => $sortCol, 'dir' => $sortDir])) ?>" 
       class="btn btn-sm <?= $i === $currentPage ? 'btn-primary' : 'btn-secondary' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($currentPage < $totalPages): ?>
    <a href="<?= h(build_url(['page' => 'browse', 'db' => $dbName, 'table' => $tableName, 'p' => $currentPage + 1, 'search' => $search, 'sort' => $sortCol, 'dir' => $sortDir])) ?>" class="btn btn-secondary btn-sm">Next &raquo;</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
