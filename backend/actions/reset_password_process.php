<?php
session_start();
require_once "../../backend/controllers/AuthController.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? null;
    $password = $_POST['password'] ?? null;
    $confirm  = $_POST['confirm'] ?? null;

    $auth = new AuthController();
    try {
        $auth->resetPassword($token, $password, $confirm);
        $_SESSION['success'] = "Password reset successfully. You can now log in.";
        header("Location: ../../frontend/pages/login.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../../frontend/pages/reset_password.php?token=" . urlencode($token));
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../../frontend/pages/login.php");
    exit;
}
?>
