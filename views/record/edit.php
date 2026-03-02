<?php
/**
 * Edit Record View
 * 
 * Form to update an existing record in the table.
 * Primary key values are passed as hidden fields to identify the row.
 */
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h2><i data-lucide="edit" class="icon"></i> Edit Record in <span class="highlight"><?= h($tableName) ?></span></h2>
    <a href="<?= h(build_url(['page' => 'browse', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-secondary">
        <i data-lucide="arrow-left" class="icon"></i> Back to Browse
    </a>
</div>

<div class="card">
    <form method="POST" action="<?= h(build_url(['page' => 'edit_record', 'db' => $dbName, 'table' => $tableName])) ?>" class="form-card">
        <input type="hidden" name="db" value="<?= h($dbName) ?>">
        <input type="hidden" name="table" value="<?= h($tableName) ?>">

        <!-- Hidden primary key values for WHERE clause -->
        <?php foreach ($primaryKeys as $pk): ?>
        <input type="hidden" name="pk[<?= h($pk) ?>]" value="<?= h($record[$pk] ?? '') ?>">
        <?php endforeach; ?>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Column</th>
                        <th>Type</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($columns as $col): ?>
                    <?php
                    $isPK   = ($col['Key'] === 'PRI');
                    $isAI   = (strpos($col['Extra'], 'auto_increment') !== false);
                    $type   = strtolower($col['Type']);
                    $isText = (strpos($type, 'text') !== false || strpos($type, 'blob') !== false);
                    $value  = $record[$col['Field']] ?? '';
                    ?>
                    <tr>
                        <td>
                            <strong><?= h($col['Field']) ?></strong>
                            <?php if ($isPK): ?>
                                <span class="badge badge-primary">PK</span>
                            <?php endif; ?>
                            <?php if ($isAI): ?>
                                <span class="badge badge-info">AI</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge badge-type"><?= h($col['Type']) ?></span></td>
                        <td>
                            <?php if ($isText): ?>
                                <textarea name="fields[<?= h($col['Field']) ?>]" rows="4" class="form-input"
                                    <?= ($isPK && $isAI) ? 'readonly' : '' ?>><?= h($value) ?></textarea>
                            <?php else: ?>
                                <input type="text" name="fields[<?= h($col['Field']) ?>]" class="form-input"
                                       value="<?= h($value) ?>"
                                       <?= ($isPK && $isAI) ? 'readonly' : '' ?>>
                            <?php endif; ?>
                            <?php if ($isPK && $isAI): ?>
                                <small class="form-help">Primary key (read-only)</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <a href="<?= h(build_url(['page' => 'browse', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="check" class="icon"></i> Update Record
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
