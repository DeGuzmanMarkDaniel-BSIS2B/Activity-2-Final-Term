<?php
require_once "auth.php";
requireRole("admin");

$id = (int)($_GET["id"] ?? $_POST["id"] ?? 0);

$stmt = $conn->prepare("SELECT id, username, role, status FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: users.php?err=" . urlencode("User not found."));
    exit;
}

$isSelf = ($id === (int)$_SESSION["user_id"]);
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $role     = $_POST["role"] ?? $user["role"];
    $status   = $_POST["status"] ?? $user["status"];
    $password = $_POST["password"];

    // An admin cannot demote / disable their own account (prevents lock-out)
    if ($isSelf) {
        $role   = $user["role"];
        $status = $user["status"];
    }

    if ($username === "") {
        $errors[] = "Username is required.";
    }
    if (!in_array($role, ["admin", "user"], true)) {
        $errors[] = "Invalid role.";
    }
    if (!in_array($status, ["pending", "approved"], true)) {
        $errors[] = "Invalid status.";
    }

    if (!$errors) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id <> ?");
        $stmt->bind_param("si", $username, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Username is already taken.";
        }
        $stmt->close();
    }

    if (!$errors) {
        if ($password !== "") {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username=?, role=?, status=?, password=? WHERE id=?");
            $stmt->bind_param("ssssi", $username, $role, $status, $hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username=?, role=?, status=? WHERE id=?");
            $stmt->bind_param("sssi", $username, $role, $status, $id);
        }
        $stmt->execute();
        $stmt->close();
        header("Location: users.php?msg=" . urlencode("User updated."));
        exit;
    }

    $user["username"] = $username;
    $user["role"]     = $role;
    $user["status"]   = $status;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
</head>
<body>
    <h1>PCWORKZ</h1>
    <h2>Edit User</h2>
    <?php include "nav.php"; ?>

    <?php foreach ($errors as $e): ?>
        <p style="color:red;"><?= h($e) ?></p>
    <?php endforeach; ?>

    <form action="" method="post">
        <input type="hidden" name="id" value="<?= (int)$user["id"] ?>">

        <label for="username">Username: </label>
        <input type="text" name="username" id="username" value="<?= h($user["username"]) ?>" required>
        <br><br>
        <label for="role">Role: </label>
        <select name="role" id="role" <?= $isSelf ? "disabled" : "" ?>>
            <option value="user"  <?= $user["role"] === "user"  ? "selected" : "" ?>>user</option>
            <option value="admin" <?= $user["role"] === "admin" ? "selected" : "" ?>>admin</option>
        </select>
        <br><br>
        <label for="status">Status: </label>
        <select name="status" id="status" <?= $isSelf ? "disabled" : "" ?>>
            <option value="pending"  <?= $user["status"] === "pending"  ? "selected" : "" ?>>pending</option>
            <option value="approved" <?= $user["status"] === "approved" ? "selected" : "" ?>>approved</option>
        </select>
        <?php if ($isSelf): ?>
            <small>(you cannot change your own role/status)</small>
        <?php endif; ?>
        <br><br>
        <label for="password">New Password: </label>
        <input type="password" name="password" id="password" placeholder="Leave blank to keep current">
        <br><br>
        <input type="submit" value="Save Changes">
        <a href="users.php">Cancel</a>
    </form>
</body>
</html>
