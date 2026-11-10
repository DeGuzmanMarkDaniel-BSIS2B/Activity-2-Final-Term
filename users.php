<?php
require_once "auth.php";
requireRole("admin");

$result = $conn->query("SELECT id, username, role, status FROM users ORDER BY id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
</head>
<body>
    <h1>PCWORKZ</h1>
    <h2>User Management</h2>
    <?php include "nav.php"; ?>

    <?php if (isset($_GET["msg"])): ?>
        <p style="color:green;"><?= h($_GET["msg"]) ?></p>
    <?php endif; ?>
    <?php if (isset($_GET["err"])): ?>
        <p style="color:red;"><?= h($_GET["err"]) ?></p>
    <?php endif; ?>

    <p><a href="user_add.php">+ Add User</a></p>

    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th>ID</th><th>Username</th><th>Role</th><th>Status</th><th>Actions</th>
        </tr>
        <?php while ($u = $result->fetch_assoc()): ?>
        <tr>
            <td><?= (int)$u["id"] ?></td>
            <td><?= h($u["username"]) ?></td>
            <td><?= h($u["role"]) ?></td>
            <td><?= h($u["status"]) ?></td>
            <td>
                <?php if ($u["status"] !== "approved"): ?>
                    <form action="user_approve.php" method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= (int)$u["id"] ?>">
                        <input type="submit" value="Approve">
                    </form>
                <?php endif; ?>
                <a href="user_edit.php?id=<?= (int)$u["id"] ?>">Edit</a>
                <?php if ((int)$u["id"] !== (int)$_SESSION["user_id"]): ?>
                    <form action="user_delete.php" method="post" style="display:inline;"
                          onsubmit="return confirm('Delete this user?');">
                        <input type="hidden" name="id" value="<?= (int)$u["id"] ?>">
                        <input type="submit" value="Delete">
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
