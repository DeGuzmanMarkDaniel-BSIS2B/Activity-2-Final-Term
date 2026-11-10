<?php
require_once "auth.php";
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>PCWORKZ</h1>
    <h2>Dashboard</h2>
    <?php include "nav.php"; ?>
    <p>Welcome, <?= h($_SESSION["username"]) ?> (<?= h($_SESSION["role"]) ?>)</p>

    <ul>
        <li><a href="products.php">Manage Products</a></li>
        <?php if (isAdmin()): ?>
            <li><a href="users.php">Manage Users</a></li>
        <?php endif; ?>
    </ul>
</body>
</html>
