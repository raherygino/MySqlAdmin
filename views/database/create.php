<?php
/**
 * Create Database View
 * 
 * Form to create a new database with charset and collation options.
 */
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h2><i data-lucide="plus-circle" class="icon"></i> Create Database</h2>
    <a href="index.php?page=databases" class="btn btn-secondary">
        <i data-lucide="arrow-left" class="icon"></i> Back
    </a>
</div>

<div class="card">
    <form method="POST" action="index.php?page=create_database" class="form-card">
        <div class="form-group">
            <label for="db_name">Database Name <span class="required">*</span></label>
            <input type="text" id="db_name" name="db_name" required autofocus
                   pattern="[a-zA-Z0-9_]+" title="Letters, numbers, and underscores only"
                   placeholder="my_database">
            <small class="form-help">Only letters, numbers, and underscores allowed.</small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="charset">Character Set</label>
                <select id="charset" name="charset">
                    <?php foreach ($charsets as $cs): ?>
                    <option value="<?= h($cs['Charset']) ?>" <?= $cs['Charset'] === 'utf8mb4' ? 'selected' : '' ?>>
                        <?= h($cs['Charset']) ?> – <?= h($cs['Description']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="collation">Collation</label>
                <select id="collation" name="collation">
                    <?php foreach ($collations as $col): ?>
                    <option value="<?= h($col['Collation']) ?>" <?= $col['Collation'] === 'utf8mb4_general_ci' ? 'selected' : '' ?>>
                        <?= h($col['Collation']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php?page=databases" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="plus" class="icon"></i> Create Database
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
