<?php
require_once __DIR__ . "/../controllers/AuthController.php";
require_once __DIR__ . "/../config/db.php"; // ✅ DB connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $email   = trim($_POST['email']);
    $f_name  = trim($_POST['f_name']);
    $m_name  = trim($_POST['m_name']);
    $l_name  = trim($_POST['l_name']);
    $ext_name = trim($_POST['ext_name']);

    // ✅ Password confirmation check
    if ($password !== $confirmPassword) {
        header("Location: ../../frontend/pages/signup.php?error=" . urlencode("Passwords do not match!"));
        exit;
    }

    // ✅ Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../../frontend/pages/signup.php?error=" . urlencode("Please enter a valid email address."));
        exit;
    }

    // ✅ Password strength validation
    $passwordPattern = "/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";
    if (!preg_match($passwordPattern, $password)) {
        header("Location: ../../frontend/pages/signup.php?error=" . urlencode("Password must be at least 8 characters long, contain 1 uppercase letter, 1 digit, and 1 special character."));
        exit;
    }

    try {
        // ✅ Check duplicate email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            header("Location: ../../frontend/pages/signup.php?error=" . urlencode("Email is already registered. Please use another email."));
            exit;
        }

        // 🚀 Proceed with signup
        $auth = new AuthController();
        $auth->signup($_POST);

        // ✅ Redirect with success
        header("Location: ../../frontend/pages/login.php?success=" . urlencode("Your account has been created. Please check your email to verify your account."));
        exit;

    } catch (Exception $e) {
        header("Location: ../../frontend/pages/signup.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}
?>
