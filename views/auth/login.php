<?php
/**
 * Login View
 * 
 * Displays the login form for MySQL server authentication.
 * Users provide host, username, and password to connect.
 */
require_once __DIR__ . '/../../helpers/functions.php';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGBD – Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="icon" href="./assets/img/favicon.png" type="image/png">
</head>
<body class="login-body">

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <span class="logo-icon-lg">&#9881;</span>
            <h1>SGBD</h1>
            <p class="login-subtitle">Web Database Manager</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <span><?= h($error) ?></span>
        </div>
        <?php endif; ?>

        <?php if ($flash): ?>
        <div class="alert alert-<?= h($flash['type']) ?>">
            <span><?= $flash['message'] ?></span>
        </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=login" class="login-form">
            <div class="form-group">
                <label for="host">
                    <i data-lucide="server" class="icon"></i> Server Host
                </label>
                <input type="text" id="host" name="host" value="<?= h($_POST['host'] ?? 'localhost') ?>" 
                       placeholder="localhost" required>
            </div>

            <div class="form-group">
                <label for="username">
                    <i data-lucide="user" class="icon"></i> Username
                </label>
                <input type="text" id="username" name="username" value="<?= h($_POST['username'] ?? 'root') ?>" 
                       placeholder="root" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">
                    <i data-lucide="lock" class="icon"></i> Password
                </label>
                <input type="password" id="password" name="password" placeholder="Password">
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i data-lucide="log-in" class="icon"></i> Connect
            </button>
        </form>
    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') { lucide.createIcons(); }
</script>
</body>
</html>
