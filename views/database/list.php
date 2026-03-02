<?php
/**
 * Database List View
 * 
 * Lists all databases on the server with actions to browse, export, rename, and drop.
 */
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h2><i data-lucide="database" class="icon"></i> Databases</h2>
    <a href="index.php?page=create_database" class="btn btn-primary">
        <i data-lucide="plus" class="icon"></i> Create Database
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Database Name</th>
                    <th>Charset</th>
                    <th>Collation</th>
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
                    <td class="actions">
                        <a href="<?= h(build_url(['page' => 'tables', 'db' => $db['name']])) ?>" class="btn btn-info btn-xs" title="Browse Tables">
                            <i data-lucide="eye" class="icon-xs"></i> Browse
                        </a>
                        <a href="<?= h(build_url(['page' => 'export', 'db' => $db['name']])) ?>" class="btn btn-success btn-xs" title="Export">
                            <i data-lucide="download" class="icon-xs"></i> Export
                        </a>
                        <button class="btn btn-warning btn-xs" title="Rename"
                                onclick="renameDatabase('<?= h($db['name']) ?>')">
                            <i data-lucide="edit" class="icon-xs"></i> Rename
                        </button>
                        <a href="<?= h(build_url(['page' => 'drop_database', 'name' => $db['name']])) ?>" 
                           class="btn btn-danger btn-xs" title="Drop"
                           onclick="return confirm('Drop database \'<?= h($db['name']) ?>\'? This cannot be undone!')">
                            <i data-lucide="trash-2" class="icon-xs"></i> Drop
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" class="empty-state">No databases found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Rename Database Modal -->
<div id="renameModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Rename Database</h3>
            <button class="modal-close" onclick="closeModal('renameModal')">&times;</button>
        </div>
        <form method="POST" action="index.php?page=rename_database">
            <input type="hidden" name="old_name" id="renameOldName">
            <div class="form-group">
                <label>Current Name</label>
                <input type="text" id="renameCurrentDisplay" disabled>
            </div>
            <div class="form-group">
                <label for="renameNewName">New Name</label>
                <input type="text" name="new_name" id="renameNewName" required 
                       pattern="[a-zA-Z0-9_]+" title="Letters, numbers, and underscores only">
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal('renameModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Rename</button>
            </div>
        </form>
    </div>
</div>

<script>
function renameDatabase(name) {
    document.getElementById('renameOldName').value = name;
    document.getElementById('renameCurrentDisplay').value = name;
    document.getElementById('renameNewName').value = '';
    document.getElementById('renameModal').style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
