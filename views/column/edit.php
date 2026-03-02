<?php
/**
 * Edit Column View
 * 
 * Form to modify an existing column's name, type, length, null, and default.
 */
require_once __DIR__ . '/../layout/header.php';
$mysqlTypes = get_mysql_types();

// Parse existing type info (e.g., "varchar(255)" → type=VARCHAR, length=255)
$currentType   = strtoupper(preg_replace('/\(.*\)/', '', $column['Type']));
$currentLength = '';
if (preg_match('/\(([^)]+)\)/', $column['Type'], $m)) {
    $currentLength = $m[1];
}
$currentNull = ($column['Null'] === 'YES') ? 'yes' : 'no';
?>

<div class="page-header">
    <h2><i data-lucide="edit" class="icon"></i> Edit Column: <span class="highlight"><?= h($column['Field']) ?></span></h2>
    <a href="<?= h(build_url(['page' => 'table_structure', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-secondary">
        <i data-lucide="arrow-left" class="icon"></i> Back to Structure
    </a>
</div>

<div class="card">
    <form method="POST" action="<?= h(build_url(['page' => 'edit_column', 'db' => $dbName, 'table' => $tableName])) ?>" class="form-card">
        <input type="hidden" name="db" value="<?= h($dbName) ?>">
        <input type="hidden" name="table" value="<?= h($tableName) ?>">
        <input type="hidden" name="old_name" value="<?= h($column['Field']) ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="col_name">Column Name <span class="required">*</span></label>
                <input type="text" id="col_name" name="col_name" required value="<?= h($column['Field']) ?>">
            </div>
            <div class="form-group">
                <label for="col_type">Type</label>
                <select id="col_type" name="col_type">
                    <?php foreach ($mysqlTypes as $group => $types): ?>
                    <optgroup label="<?= h($group) ?>">
                        <?php foreach ($types as $t): ?>
                        <option value="<?= h($t) ?>" <?= $t === $currentType ? 'selected' : '' ?>><?= h($t) ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="col_length">Length / Values</label>
                <input type="text" id="col_length" name="col_length" value="<?= h($currentLength) ?>">
            </div>
            <div class="form-group">
                <label for="col_null">Null</label>
                <select id="col_null" name="col_null">
                    <option value="no" <?= $currentNull === 'no' ? 'selected' : '' ?>>NOT NULL</option>
                    <option value="yes" <?= $currentNull === 'yes' ? 'selected' : '' ?>>NULL</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="col_default">Default Value</label>
            <input type="text" id="col_default" name="col_default" value="<?= h($column['Default'] ?? '') ?>">
        </div>

        <div class="form-actions">
            <a href="<?= h(build_url(['page' => 'table_structure', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="check" class="icon"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
