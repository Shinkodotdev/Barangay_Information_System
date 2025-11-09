<?php
session_start();
require_once __DIR__ . "/../controllers/AuthController.php";
require_once __DIR__ . "/../config/db.php"; // ✅ DB connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        // ✅ Simple email validation before passing to controller
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Please enter a valid email address.";
            header("Location: ../../frontend/pages/login.php");
            exit;
        }

        // ✅ Use AuthController to login
        $auth = new AuthController();
        $auth->login($email, $password);  
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../../frontend/pages/login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../../frontend/pages/login.php");
    exit;
}
