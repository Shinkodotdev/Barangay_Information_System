<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once "../config/db.php";

// Only Admins can approve users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$userId = $data['user_id'] ?? null;
$action = $data['action'] ?? null;

if (!$userId || !$action) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    // Update user status
    $stmt = $pdo->prepare("
        UPDATE users 
        SET status = ?, updated_at = NOW() 
        WHERE user_id = ?
    ");
    $stmt->execute([$action, $userId]);

    echo json_encode([
        'success' => true,
        'message' => "User #$userId has been $action"
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
