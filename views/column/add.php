<?php
/**
 * Add Column View
 * 
 * Form to add a new column to an existing table.
 */
require_once __DIR__ . '/../layout/header.php';
$mysqlTypes = get_mysql_types();
?>

<div class="page-header">
    <h2><i data-lucide="plus-circle" class="icon"></i> Add Column to <span class="highlight"><?= h($tableName) ?></span></h2>
    <a href="<?= h(build_url(['page' => 'table_structure', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-secondary">
        <i data-lucide="arrow-left" class="icon"></i> Back to Structure
    </a>
</div>

<div class="card">
    <form method="POST" action="<?= h(build_url(['page' => 'add_column', 'db' => $dbName, 'table' => $tableName])) ?>" class="form-card">
        <input type="hidden" name="db" value="<?= h($dbName) ?>">
        <input type="hidden" name="table" value="<?= h($tableName) ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="col_name">Column Name <span class="required">*</span></label>
                <input type="text" id="col_name" name="col_name" required autofocus placeholder="column_name">
            </div>
            <div class="form-group">
                <label for="col_type">Type</label>
                <select id="col_type" name="col_type">
                    <?php foreach ($mysqlTypes as $group => $types): ?>
                    <optgroup label="<?= h($group) ?>">
                        <?php foreach ($types as $t): ?>
                        <option value="<?= h($t) ?>" <?= $t === 'VARCHAR' ? 'selected' : '' ?>><?= h($t) ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="col_length">Length / Values</label>
                <input type="text" id="col_length" name="col_length" placeholder="255">
            </div>
            <div class="form-group">
                <label for="col_null">Null</label>
                <select id="col_null" name="col_null">
                    <option value="no" selected>NOT NULL</option>
                    <option value="yes">NULL</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="col_default">Default Value</label>
                <input type="text" id="col_default" name="col_default" placeholder="None">
            </div>
            <div class="form-group">
                <label for="col_after">Position (After Column)</label>
                <select id="col_after" name="col_after">
                    <option value="">-- End of table --</option>
                    <?php foreach ($existingColumns as $ec): ?>
                    <option value="<?= h($ec['Field']) ?>"><?= h($ec['Field']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= h(build_url(['page' => 'table_structure', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="plus" class="icon"></i> Add Column
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
