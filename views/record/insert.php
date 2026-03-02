<?php
/**
 * Insert Record View
 * 
 * Form to insert a new record into the table.
 * Auto-detects column types and displays appropriate input fields.
 */
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h2><i data-lucide="plus" class="icon"></i> Insert into <span class="highlight"><?= h($tableName) ?></span></h2>
    <a href="<?= h(build_url(['page' => 'browse', 'db' => $dbName, 'table' => $tableName])) ?>" class="btn btn-secondary">
        <i data-lucide="arrow-left" class="icon"></i> Back to Browse
    </a>
</div>

<div class="card">
    <form method="POST" action="<?= h(build_url(['page' => 'insert', 'db' => $dbName, 'table' => $tableName])) ?>" class="form-card">
        <input type="hidden" name="db" value="<?= h($dbName) ?>">
        <input type="hidden" name="table" value="<?= h($tableName) ?>">

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Column</th>
                        <th>Type</th>
                        <th>Null</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($columns as $col): ?>
                    <?php
                    $isAI   = (strpos($col['Extra'], 'auto_increment') !== false);
                    $isNull = ($col['Null'] === 'YES');
                    $type   = strtolower($col['Type']);
                    $isText = (strpos($type, 'text') !== false || strpos($type, 'blob') !== false);
                    ?>
                    <tr>
                        <td>
                            <strong><?= h($col['Field']) ?></strong>
                            <?php if ($col['Key'] === 'PRI'): ?>
                                <span class="badge badge-primary">PK</span>
                            <?php endif; ?>
                            <?php if ($isAI): ?>
                                <span class="badge badge-info">AI</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge badge-type"><?= h($col['Type']) ?></span></td>
                        <td>
                            <?php if ($isNull): ?>
                                <span class="badge badge-null">NULL</span>
                            <?php else: ?>
                                <span class="badge badge-notnull">NOT NULL</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($isText): ?>
                                <textarea name="fields[<?= h($col['Field']) ?>]" rows="3" class="form-input"
                                          placeholder="<?= $isAI ? 'Auto Increment' : '' ?>"><?= h($col['Default'] ?? '') ?></textarea>
                            <?php else: ?>
                                <input type="text" name="fields[<?= h($col['Field']) ?>]" class="form-input"
                                       value="<?= h($col['Default'] ?? '') ?>"
                                       placeholder="<?= $isAI ? 'Auto Increment' : '' ?>">
                            <?php endif; ?>
                            <?php if ($isAI): ?>
                                <small class="form-help">Leave empty for auto-increment</small>
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
                <i data-lucide="plus" class="icon"></i> Insert Record
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
