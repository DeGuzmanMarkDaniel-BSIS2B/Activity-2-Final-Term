<p>
    <a href="dashboard.php">Dashboard</a> |
    <a href="products.php">Products</a>
    <?php if (isAdmin()): ?>
        | <a href="users.php">User Management</a>
    <?php endif; ?>
    | <a href="logout.php">Logout</a>
</p>
<hr>
