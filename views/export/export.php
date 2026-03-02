<?php
/**
 * Export View
 * 
 * Form to select tables for export from a database.
 * Exports in SQL format with CREATE TABLE and INSERT statements.
 */
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h2><i data-lucide="download" class="icon"></i> Export: <span class="highlight"><?= h($dbName) ?></span></h2>
    <a href="<?= h(build_url(['page' => 'tables', 'db' => $dbName])) ?>" class="btn btn-secondary">
        <i data-lucide="arrow-left" class="icon"></i> Back
    </a>
</div>

<div class="card">
    <form method="POST" action="<?= h(build_url(['page' => 'export', 'db' => $dbName])) ?>" class="form-card">
        <input type="hidden" name="db" value="<?= h($dbName) ?>">

        <?php if (!empty($tableName)): ?>
        <!-- Single table export -->
        <input type="hidden" name="tables[]" value="<?= h($tableName) ?>">
        <div class="alert alert-info">
            Exporting table <strong><?= h($tableName) ?></strong> from database <strong><?= h($dbName) ?></strong>.
        </div>
        <?php else: ?>
        <!-- Multi-table selection -->
        <div class="form-group">
            <label>Select Tables to Export</label>
            <div class="checkbox-group">
                <div class="checkbox-header">
                    <label class="checkbox-label">
                        <input type="checkbox" id="selectAll" onchange="toggleAll(this.checked)">
                        <strong>Select All</strong>
                    </label>
                </div>
                <?php if (!empty($tables)): ?>
                    <?php foreach ($tables as $tbl): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="tables[]" value="<?= h($tbl) ?>" checked>
                        <i data-lucide="table" class="icon-sm"></i> <?= h($tbl) ?>
                    </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="muted">No tables in this database.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label>Export Format</label>
            <div class="export-info">
                <span class="badge badge-info">SQL</span>
                <span class="muted">Generates DROP TABLE, CREATE TABLE, and INSERT statements</span>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= h(build_url(['page' => 'tables', 'db' => $dbName])) ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="download" class="icon"></i> Export & Download
            </button>
        </div>
    </form>
</div>

<script>
function toggleAll(checked) {
    document.querySelectorAll('input[name="tables[]"]').forEach(function(cb) {
        cb.checked = checked;
    });
}
// Initialize "Select All" state
document.addEventListener('DOMContentLoaded', function() {
    var selectAll = document.getElementById('selectAll');
    if (selectAll) selectAll.checked = true;
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
