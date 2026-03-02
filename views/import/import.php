<?php
/**
 * Import View
 * 
 * Form to upload and import a .sql file into a selected database.
 */
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h2><i data-lucide="upload" class="icon"></i> Import SQL File</h2>
</div>

<div class="card">
    <form method="POST" action="<?= h(build_url(['page' => 'import', 'db' => $dbName])) ?>" 
          enctype="multipart/form-data" class="form-card">

        <div class="form-group">
            <label for="target_db">Target Database <span class="required">*</span></label>
            <select id="target_db" name="target_db" required>
                <option value="">-- Select database --</option>
                <?php foreach ($databases as $d): ?>
                <option value="<?= h($d) ?>" <?= $d === $dbName ? 'selected' : '' ?>><?= h($d) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="sql_file">SQL File <span class="required">*</span></label>
            <div class="file-upload">
                <input type="file" id="sql_file" name="sql_file" accept=".sql" required>
                <div class="file-upload-info">
                    <i data-lucide="file-text" class="icon-lg"></i>
                    <p>Choose a <strong>.sql</strong> file to import</p>
                    <p class="muted">Maximum file size: 50MB</p>
                </div>
            </div>
        </div>

        <div class="alert alert-warning">
            <strong>Warning:</strong> Importing a SQL file may overwrite existing data. 
            The file will be executed directly on the selected database.
            Make sure you have a backup before proceeding.
        </div>

        <div class="form-actions">
            <a href="<?= h(build_url(['page' => 'tables', 'db' => $dbName])) ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="upload" class="icon"></i> Import
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
