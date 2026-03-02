<?php
/**
 * SQL Editor View
 * 
 * Provides a text area for executing custom SQL queries.
 * Displays results in a table (for SELECT) or affected rows count (for others).
 */
require_once __DIR__ . '/../layout/header.php';
?>

<div class="page-header">
    <h2><i data-lucide="terminal" class="icon"></i> SQL Editor</h2>
</div>

<div class="card">
    <form method="POST" action="<?= h(build_url(['page' => 'sql', 'db' => $dbName])) ?>" class="form-card" id="sqlForm">
        
        <!-- Database Selector -->
        <div class="form-row">
            <div class="form-group">
                <label for="db">Database</label>
                <select id="db" name="db" onchange="this.form.action='<?= h(build_url(['page' => 'sql'])) ?>&db=' + this.value;">
                    <option value="">-- Server level --</option>
                    <?php foreach ($databases as $d): ?>
                    <option value="<?= h($d) ?>" <?= $d === $dbName ? 'selected' : '' ?>><?= h($d) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- SQL Query Editor -->
        <div class="form-group">
            <label for="sqlQuery">SQL Query</label>
            <textarea id="sqlQuery" name="sql" rows="8" class="sql-editor" 
                      placeholder="SELECT * FROM table_name LIMIT 100;"
                      spellcheck="false"><?= h($sql) ?></textarea>
            <div class="sql-toolbar">
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="play" class="icon"></i> Execute
                </button>
                <button type="button" class="btn btn-secondary" onclick="clearEditor()">
                    <i data-lucide="x" class="icon"></i> Clear
                </button>
                <div class="sql-shortcuts">
                    <?php if (!empty($dbName)): ?>
                    <button type="button" class="btn btn-outline btn-xs" onclick="setQuery('SHOW TABLES')">SHOW TABLES</button>
                    <button type="button" class="btn btn-outline btn-xs" onclick="setQuery('SHOW TABLE STATUS')">TABLE STATUS</button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline btn-xs" onclick="setQuery('SHOW DATABASES')">SHOW DATABASES</button>
                    <button type="button" class="btn btn-outline btn-xs" onclick="setQuery('SHOW VARIABLES')">VARIABLES</button>
                    <button type="button" class="btn btn-outline btn-xs" onclick="setQuery('SHOW PROCESSLIST')">PROCESSLIST</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Error Message -->
<?php if ($errorMsg): ?>
<div class="alert alert-error">
    <strong>Error:</strong> <?= h($errorMsg) ?>
</div>
<?php endif; ?>

<!-- Results -->
<?php if (!empty($statementResults)): ?>
    <?php foreach ($statementResults as $i => $r): ?>
    <div class="card">
        <div class="card-header">
            <h3>Statement <?= $i + 1 ?></h3>
            <span class="result-meta"><?= $executionTime ?>ms total</span>
        </div>
        <div class="form-card" style="padding-top: 0.75rem; padding-bottom: 0.75rem;">
            <div class="sql-statement" style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--text-light); white-space: pre-wrap;">
                <?= h($r['sql']) ?>
            </div>
        </div>

        <?php if (($r['type'] ?? '') === 'resultset'): ?>
            <div class="card-header" style="border-top: 1px solid var(--border);">
                <h3>Results</h3>
                <span class="result-meta"><?= (int)($r['row_count'] ?? 0) ?> row(s) returned</span>
            </div>
            <?php if (!empty($r['rows'])): ?>
            <div class="table-responsive">
                <table class="data-table data-table-striped">
                    <thead>
                        <tr>
                            <?php foreach (($r['columns'] ?? []) as $col): ?>
                            <th><?= h($col) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($r['rows'] as $row): ?>
                        <tr>
                            <?php foreach (($r['columns'] ?? []) as $col): ?>
                            <td>
                                <?php
                                $val = $row[$col] ?? null;
                                if ($val === null) {
                                    echo '<em class="muted">NULL</em>';
                                } elseif (is_string($val) && strlen($val) > 200) {
                                    echo h(substr($val, 0, 200)) . '...';
                                } else {
                                    echo h($val);
                                }
                                ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state-box">
                <p>Query executed successfully. 0 rows returned.</p>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="card-header" style="border-top: 1px solid var(--border);">
                <h3>Query Executed</h3>
                <span class="result-meta">OK</span>
            </div>
            <div class="result-summary">
                <p><strong><?= (int)($r['affected_rows'] ?? 0) ?></strong> row(s) affected.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
<?php else: ?>
    <?php if ($results !== null): ?>
    <div class="card">
        <div class="card-header">
            <h3>Results</h3>
            <span class="result-meta">
                <?= count($results) ?> row(s) returned in <?= $executionTime ?>ms
            </span>
        </div>
        <?php if (!empty($results)): ?>
        <div class="table-responsive">
            <table class="data-table data-table-striped">
                <thead>
                    <tr>
                        <?php foreach ($columns as $col): ?>
                        <th><?= h($col) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                    <tr>
                        <?php foreach ($columns as $col): ?>
                        <td>
                            <?php
                            $val = $row[$col] ?? null;
                            if ($val === null) {
                                echo '<em class="muted">NULL</em>';
                            } elseif (strlen($val) > 200) {
                                echo h(substr($val, 0, 200)) . '...';
                            } else {
                                echo h($val);
                            }
                            ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state-box">
            <p>Query executed successfully. 0 rows returned.</p>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Affected Rows (for non-SELECT queries) -->
    <?php if ($affectedRows !== null): ?>
    <div class="card">
        <div class="card-header">
            <h3>Query Executed</h3>
            <span class="result-meta">Completed in <?= $executionTime ?>ms</span>
        </div>
        <div class="result-summary">
            <p><strong><?= (int)$affectedRows ?></strong> row(s) affected.</p>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<script>
function clearEditor() {
    document.getElementById('sqlQuery').value = '';
    document.getElementById('sqlQuery').focus();
}

function setQuery(q) {
    document.getElementById('sqlQuery').value = q;
    document.getElementById('sqlQuery').focus();
}

// Ctrl+Enter to execute
document.getElementById('sqlQuery').addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        document.getElementById('sqlForm').submit();
    }
    // Tab key inserts tab instead of changing focus
    if (e.key === 'Tab') {
        e.preventDefault();
        var start = this.selectionStart;
        var end = this.selectionEnd;
        this.value = this.value.substring(0, start) + '    ' + this.value.substring(end);
        this.selectionStart = this.selectionEnd = start + 4;
    }
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
