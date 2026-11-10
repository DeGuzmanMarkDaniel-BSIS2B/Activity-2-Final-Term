<?php
require_once "auth.php";
requireLogin(); 

$id = (int)($_GET["id"] ?? $_POST["id"] ?? 0);

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: products.php?msg=" . urlencode("Product not found."));
    exit;
}

$errors = [];

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
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, stock=? WHERE id=?");
        $stmt->bind_param("ssdii", $name, $category, $price, $stock, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: products.php?msg=" . urlencode("Product updated."));
        exit;
    }

    $product["name"]     = $name;
    $product["category"] = $category;
    $product["price"]    = $price;
    $product["stock"]    = $stock;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
</head>
<body>
    <h1>PCWORKZ</h1>
    <h2>Edit Product</h2>
    <?php include "nav.php"; ?>

    <?php foreach ($errors as $e): ?>
        <p style="color:red;"><?= h($e) ?></p>
    <?php endforeach; ?>

    <form action="" method="post">
        <input type="hidden" name="id" value="<?= (int)$product["id"] ?>">
        <label for="name">Name: </label>
        <input type="text" name="name" id="name" value="<?= h($product["name"]) ?>" required>
        <br><br>
        <label for="category">Category: </label>
        <input type="text" name="category" id="category" value="<?= h($product["category"]) ?>">
        <br><br>
        <label for="price">Price: </label>
        <input type="number" step="0.01" min="0" name="price" id="price" value="<?= h($product["price"]) ?>" required>
        <br><br>
        <label for="stock">Stock: </label>
        <input type="number" min="0" name="stock" id="stock" value="<?= h($product["stock"]) ?>" required>
        <br><br>
        <input type="submit" value="Save Changes">
        <a href="products.php">Cancel</a>
    </form>
</body>
</html>
