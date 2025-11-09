<?php
session_start();
require_once "../config/db.php";
require_once "../controllers/ProfileController.php";

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../frontend/pages/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Pass only $pdo to the controller (make sure ProfileController accepts this)
$controller = new ProfileController($pdo);

try {
    // ✅ Save profile with posted form data
    $controller->saveProfile($user_id, $_POST, $_FILES);

    $_SESSION['success'] = "Profile updated successfully!";
    header("Location: ../../frontend/pages/pending/pending.php");
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = "Error updating profile: " . $e->getMessage();
    header("Location: ../../frontend/pages/user/dashboard.php");
    exit;
}
