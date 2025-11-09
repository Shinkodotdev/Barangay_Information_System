<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once "../config/db.php";

// Only Admins can process requests
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$requestId = $data['request_id'] ?? null;
$action     = $data['action'] ?? null;

if (!$requestId || !$action) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    $adminId = $_SESSION['user_id'];

    // Update the request
    $stmt = $pdo->prepare("
        UPDATE document_requests 
        SET status = ?, 
            processed_at = NOW(), 
            processed_by = ? 
        WHERE request_id = ?
    ");
    $stmt->execute([$action, $adminId, $requestId]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
