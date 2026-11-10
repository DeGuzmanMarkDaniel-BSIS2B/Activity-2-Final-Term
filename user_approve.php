<?php
require_once "auth.php";
requireRole("admin");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)$_POST["id"];
    $stmt = $conn->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: users.php?msg=" . urlencode("User approved."));
    exit;
}
header("Location: users.php");
exit;
