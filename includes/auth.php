<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION["user_id"]);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../public/index.php");
        exit();
    }
}

function requireRole($roles) {
    requireLogin();
    if (!in_array($_SESSION["role"], $roles)) {
        header("Location: ../public/dashboard.php");
        exit();
    }
}

function logout() {
    session_destroy();
    header("Location: ../public/index.php");
    exit();
}
?>
