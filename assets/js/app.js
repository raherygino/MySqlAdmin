/**
 * SGBD – Web Database Manager
 * Main JavaScript
 * 
 * Handles client-side interactions:
 * - Auto-dismiss flash messages
 * - Confirm dialogs
 * - Form enhancements
 */

document.addEventListener('DOMContentLoaded', function () {

    // --- Auto-dismiss flash alerts after 5 seconds ---
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.4s ease';
            alert.style.opacity = '0';
            setTimeout(function () {
                alert.remove();
            }, 400);
        }, 5000);
    });

    // --- Close modal on Escape key ---
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function (modal) {
                modal.style.display = 'none';
            });
        }
    });

    // --- Close modal on backdrop click ---
    document.querySelectorAll('.modal').forEach(function (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    // --- Auto-resize textareas ---
    document.querySelectorAll('textarea:not(.sql-editor)').forEach(function (ta) {
        ta.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });

    // --- Highlight current row on hover for data tables ---
    document.querySelectorAll('.data-table tbody tr').forEach(function (row) {
        row.addEventListener('mouseenter', function () {
            this.style.cursor = 'default';
        });
    });
});
