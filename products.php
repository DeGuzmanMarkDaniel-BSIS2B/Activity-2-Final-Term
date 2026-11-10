<?php
require_once "auth.php";
requireLogin();   // admin and user can both view (Read)

$result = $conn->query("SELECT * FROM products ORDER BY id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products</title>
</head>
<body>
    <h1>PCWORKZ</h1>
    <h2>Products</h2>
    <?php include "nav.php"; ?>

    <?php if (isset($_GET["msg"])): ?>
        <p style="color:green;"><?= h($_GET["msg"]) ?></p>
    <?php endif; ?>

    <p><a href="product_add.php">+ Add Product</a></p>

    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th>
        </tr>
        <?php while ($p = $result->fetch_assoc()): ?>
        <tr>
            <td><?= (int)$p["id"] ?></td>
            <td><?= h($p["name"]) ?></td>
            <td><?= h($p["category"]) ?></td>
            <td><?= number_format($p["price"], 2) ?></td>
            <td><?= (int)$p["stock"] ?></td>
            <td>
                <a href="product_edit.php?id=<?= (int)$p["id"] ?>">Edit</a>
                <?php if (isAdmin()): ?>
                    <form action="product_delete.php" method="post" style="display:inline;"
                          onsubmit="return confirm('Delete this product?');">
                        <input type="hidden" name="id" value="<?= (int)$p["id"] ?>">
                        <input type="submit" value="Delete">
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
