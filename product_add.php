<?php
require_once "auth.php";
requireLogin(); 

$errors   = [];
$name     = "";
$category = "";
$price    = "";
$stock    = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name     = trim($_POST["name"]);
    $category = trim($_POST["category"]);
    $price    = $_POST["price"];
    $stock    = $_POST["stock"];

    if ($name === "") {
        $errors[] = "Product name is required.";
    }
    if (!is_numeric($price) || $price < 0) {
        $errors[] = "Price must be a number (0 or higher).";
    }
    if (!ctype_digit((string)$stock)) {
        $errors[] = "Stock must be a whole number (0 or higher).";
    }

    if (!$errors) {
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, stock) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $name, $category, $price, $stock);
        $stmt->execute();
        $stmt->close();
        header("Location: products.php?msg=" . urlencode("Product added."));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
</head>
<body>
    <h1>PCWORKZ</h1>
    <h2>Add Product</h2>
    <?php include "nav.php"; ?>

    <?php foreach ($errors as $e): ?>
        <p style="color:red;"><?= h($e) ?></p>
    <?php endforeach; ?>

    <form action="" method="post">
        <label for="name">Name: </label>
        <input type="text" name="name" id="name" value="<?= h($name) ?>" required>
        <br><br>
        <label for="category">Category: </label>
        <input type="text" name="category" id="category" value="<?= h($category) ?>">
        <br><br>
        <label for="price">Price: </label>
        <input type="number" step="0.01" min="0" name="price" id="price" value="<?= h($price) ?>" required>
        <br><br>
        <label for="stock">Stock: </label>
        <input type="number" min="0" name="stock" id="stock" value="<?= h($stock) ?>" required>
        <br><br>
        <input type="submit" value="Add Product">
        <a href="products.php">Cancel</a>
    </form>
</body>
</html>
