<?php
require_once "auth.php";
requireRole("admin");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)$_POST["id"];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: products.php?msg=" . urlencode("Product deleted."));
    exit;
}
header("Location: products.php");
exit;
