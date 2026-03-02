<?php
/**
 * Layout Footer
 * 
 * Shared footer for all authenticated pages.
 * Initializes Lucide icons and loads the application JavaScript.
 */
?>
</main>

<footer class="footer">
    <p>SGBD – Web Database Manager &copy; <?= date('Y') ?></p>
</footer>

<script src="assets/js/app.js"></script>
<script>
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
</body>
</html>
