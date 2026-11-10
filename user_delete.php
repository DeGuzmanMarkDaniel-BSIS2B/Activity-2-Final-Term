<?php
require_once "auth.php";
requireRole("admin");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)$_POST["id"];

    if ($id === (int)$_SESSION["user_id"]) {
        header("Location: users.php?err=" . urlencode("You cannot delete your own account."));
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php?msg=" . urlencode("User deleted."));
    exit;
}
header("Location: users.php");
exit;
