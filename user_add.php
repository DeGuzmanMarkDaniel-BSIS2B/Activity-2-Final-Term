<?php
require_once "auth.php";
requireRole("admin");

$errors = [];
$username = "";
$role = "user";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $role     = $_POST["role"];

    if ($username === "" || $password === "") {
        $errors[] = "Username and password are required.";
    }
    if (!in_array($role, ["admin", "user"], true)) {
        $errors[] = "Invalid role.";
    }

    if (!$errors) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Username is already taken.";
        }
        $stmt->close();
    }

    if (!$errors) {
        $hash   = password_hash($password, PASSWORD_DEFAULT);
        $status = "approved"; // created by admin, so no approval needed
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hash, $role, $status);
        $stmt->execute();
        $stmt->close();
        header("Location: users.php?msg=" . urlencode("User added."));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
</head>
<body>
    <h1>PCWORKZ</h1>
    <h2>Add User</h2>
    <?php include "nav.php"; ?>

    <?php foreach ($errors as $e): ?>
        <p style="color:red;"><?= h($e) ?></p>
    <?php endforeach; ?>

    <form action="" method="post">
        <label for="username">Username: </label>
        <input type="text" name="username" id="username" value="<?= h($username) ?>" required>
        <br><br>
        <label for="password">Password: </label>
        <input type="password" name="password" id="password" required>
        <br><br>
        <label for="role">Role: </label>
        <select name="role" id="role">
            <option value="user"  <?= $role === "user"  ? "selected" : "" ?>>user</option>
            <option value="admin" <?= $role === "admin" ? "selected" : "" ?>>admin</option>
        </select>
        <br><br>
        <input type="submit" value="Add User">
        <a href="users.php">Cancel</a>
    </form>
</body>
</html>
