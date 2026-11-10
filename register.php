<?php
require_once "config.php";

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, "UTF-8");
}

$msg    = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username  = trim($_POST["uname"]);
    $password  = $_POST["password"];
    $password2 = $_POST["password2"];

    if ($username === "" || $password === "") {
        $errors[] = "Username and password are required.";
    } elseif ($password === $password2) {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();
        $taken = $check->num_rows > 0;
        $check->close();

        if ($taken) {
            $errors[] = "Username is already taken.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hash);
            if ($stmt->execute()) {
                $msg[] = "Registered successfully. Please wait for the admin approval of your account.";
            } else {
                $errors[] = "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    } else {
        $errors[] = "Passwords should match";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Inventory System</h1>
    <h2>Create your Account</h2>
    <br>
    <?php foreach ($errors as $e): ?>
        <p style="color:red;"><?= h($e) ?></p>
    <?php endforeach; ?>
    <?php foreach ($msg as $m): ?>
        <p style="color:green;"><?= h($m) ?></p>
    <?php endforeach; ?>
    <form action="" method="post">
        <label for="uname">Username: </label>
        <input type="text" name="uname" id="uname" required>
        <br><br>
        <label for="password">Password: </label>
        <input type="password" name="password" id="password" required>
        <br><br>
        <label for="password2">Confirm Password: </label>
        <input type="password" name="password2" id="password2" required>
        <br><br>
        <input type="submit" value="Register">
    </form>
    <br>
    <p>Already have an account? <a href="index.php">Sign In</a></p>
</body>
</html>