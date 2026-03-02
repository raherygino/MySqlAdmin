<?php
/**
 * Create Table View
 * 
 * Dynamic form to create a new table with multiple columns.
 * Users can add/remove column definitions via JavaScript.
 */
require_once __DIR__ . '/../layout/header.php';
$mysqlTypes = get_mysql_types();
?>

<div class="page-header">
    <h2><i data-lucide="plus-circle" class="icon"></i> Create Table in <span class="highlight"><?= h($dbName) ?></span></h2>
    <a href="<?= h(build_url(['page' => 'tables', 'db' => $dbName])) ?>" class="btn btn-secondary">
        <i data-lucide="arrow-left" class="icon"></i> Back
    </a>
</div>

<div class="card">
    <form method="POST" action="<?= h(build_url(['page' => 'create_table', 'db' => $dbName])) ?>" class="form-card" id="createTableForm">
        
        <div class="form-row">
            <div class="form-group">
                <label for="table_name">Table Name <span class="required">*</span></label>
                <input type="text" id="table_name" name="table_name" required
                       pattern="[a-zA-Z0-9_]+" title="Letters, numbers, and underscores only"
                       placeholder="my_table">
            </div>
            <div class="form-group">
                <label for="engine">Engine</label>
                <select id="engine" name="engine">
                    <?php foreach ($engines as $eng): ?>
                        <?php if ($eng['Support'] === 'YES' || $eng['Support'] === 'DEFAULT'): ?>
                        <option value="<?= h($eng['Engine']) ?>" <?= $eng['Support'] === 'DEFAULT' ? 'selected' : '' ?>>
                            <?= h($eng['Engine']) ?>
                        </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <h3 class="section-title">Columns</h3>
        <div class="table-responsive">
            <table class="data-table" id="columnsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name <span class="required">*</span></th>
                        <th>Type</th>
                        <th>Length/Values</th>
                        <th>Null</th>
                        <th>Default</th>
                        <th>A_I</th>
                        <th>PK</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="columnsBody">
                    <tr class="column-row">
                        <td>1</td>
                        <td><input type="text" name="columns[0][name]" placeholder="id" required></td>
                        <td>
                            <select name="columns[0][type]">
                                <?php foreach ($mysqlTypes as $group => $types): ?>
                                <optgroup label="<?= h($group) ?>">
                                    <?php foreach ($types as $t): ?>
                                    <option value="<?= h($t) ?>" <?= $t === 'INT' ? 'selected' : '' ?>><?= h($t) ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="text" name="columns[0][length]" placeholder="11" class="input-sm"></td>
                        <td>
                            <select name="columns[0][null]">
                                <option value="no" selected>NOT NULL</option>
                                <option value="yes">NULL</option>
                            </select>
                        </td>
                        <td><input type="text" name="columns[0][default]" placeholder="" class="input-sm"></td>
                        <td><input type="checkbox" name="columns[0][auto_increment]" checked></td>
                        <td><input type="radio" name="pk_col" value="0" checked onchange="setPrimaryKey(this)"></td>
                        <td><button type="button" class="btn btn-danger btn-xs" onclick="removeColumn(this)">&times;</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="form-actions-left">
            <button type="button" class="btn btn-secondary" onclick="addColumn()">
                <i data-lucide="plus" class="icon"></i> Add Column
            </button>
        </div>

        <div class="form-actions">
            <a href="<?= h(build_url(['page' => 'tables', 'db' => $dbName])) ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="check" class="icon"></i> Create Table
            </button>
        </div>
    </form>
</div>

<script>
var colIndex = 1;
var typeOptions = <?= json_encode($mysqlTypes) ?>;

function addColumn() {
    var tbody = document.getElementById('columnsBody');
    var rowCount = tbody.rows.length + 1;
    var idx = colIndex++;
    
    var optionsHtml = '';
    for (var group in typeOptions) {
        optionsHtml += '<optgroup label="' + group + '">';
        typeOptions[group].forEach(function(t) {
            var sel = (t === 'VARCHAR') ? ' selected' : '';
            optionsHtml += '<option value="' + t + '"' + sel + '>' + t + '</option>';
        });
        optionsHtml += '</optgroup>';
    }

    var tr = document.createElement('tr');
    tr.className = 'column-row';
    tr.innerHTML = '<td>' + rowCount + '</td>' +
        '<td><input type="text" name="columns[' + idx + '][name]" placeholder="column_name" required></td>' +
        '<td><select name="columns[' + idx + '][type]">' + optionsHtml + '</select></td>' +
        '<td><input type="text" name="columns[' + idx + '][length]" placeholder="255" class="input-sm"></td>' +
        '<td><select name="columns[' + idx + '][null]"><option value="no" selected>NOT NULL</option><option value="yes">NULL</option></select></td>' +
        '<td><input type="text" name="columns[' + idx + '][default]" placeholder="" class="input-sm"></td>' +
        '<td><input type="checkbox" name="columns[' + idx + '][auto_increment]"></td>' +
        '<td><input type="radio" name="pk_col" value="' + idx + '" onchange="setPrimaryKey(this)"></td>' +
        '<td><button type="button" class="btn btn-danger btn-xs" onclick="removeColumn(this)">&times;</button></td>';
    tbody.appendChild(tr);
    renumberRows();
}

function removeColumn(btn) {
    var tbody = document.getElementById('columnsBody');
    if (tbody.rows.length <= 1) {
        alert('At least one column is required.');
        return;
    }
    btn.closest('tr').remove();
    renumberRows();
}

function renumberRows() {
    var rows = document.getElementById('columnsBody').rows;
    for (var i = 0; i < rows.length; i++) {
        rows[i].cells[0].textContent = i + 1;
    }
}

function setPrimaryKey(radio) {
    // Add hidden primary_key input to the selected column's row
    document.querySelectorAll('input[name$="[primary_key]"]').forEach(function(el) { el.remove(); });
    var tr = radio.closest('tr');
    var nameInput = tr.querySelector('input[type="text"]');
    if (nameInput) {
        var colName = nameInput.name.replace('[name]', '[primary_key]');
        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = colName;
        hidden.value = '1';
        tr.appendChild(hidden);
    }
}

// Initialize primary key for the first column
document.addEventListener('DOMContentLoaded', function() {
    var firstPk = document.querySelector('input[name="pk_col"][value="0"]');
    if (firstPk) setPrimaryKey(firstPk);
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
