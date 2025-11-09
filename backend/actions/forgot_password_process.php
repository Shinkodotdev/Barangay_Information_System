<?php
require_once "../../backend/controllers/AuthController.php";
session_start(); // Start session for flash messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;

    try {
        if (!$email) {
            throw new Exception("Email is required.");
        }

        $auth = new AuthController();
        $auth->forgotPassword($email);

        $_SESSION['success'] = "Reset link sent to your email. Please check your inbox.";
        header("Location: ../../frontend/pages/login.php");
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../../frontend/pages/forgot_password.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../../frontend/pages/login.php");
    exit;
}
