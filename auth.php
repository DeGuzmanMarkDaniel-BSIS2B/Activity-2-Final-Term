<?php
require_once "config.php";

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, "UTF-8");
}

function requireLogin() {
    global $conn;

    if (!isset($_SESSION["user_id"])) {
        header("Location: index.php");
        exit;
    }

    $stmt = $conn->prepare("SELECT username, role, status FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$u || $u["status"] !== "approved") {
        $_SESSION = [];
        session_destroy();
        header("Location: index.php");
        exit;
    }

    $_SESSION["username"] = $u["username"];
    $_SESSION["role"]     = $u["role"];
}

function requireRole($roles) {
    requireLogin();
    if (!in_array($_SESSION["role"], (array)$roles, true)) {
        http_response_code(403);
        die("403 Forbidden - you do not have permission to access this page. <a href='dashboard.php'>Back</a>");
    }
}

function isAdmin() {
    return isset($_SESSION["role"]) && $_SESSION["role"] === "admin";
}
