<?php
session_start();
header('Content-Type: application/json');

require_once "../config/db.php"; 

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in first.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$document_name = trim($_POST['document_name'] ?? '');
$purpose = trim($_POST['purpose'] ?? '');
$business_name = trim($_POST['business_name'] ?? null);
$indigency_for = trim($_POST['indigency_for'] ?? null);

// Validate input
if (empty($document_name) || empty($purpose)) {
    echo json_encode(['status' => 'error', 'message' => 'Document name and purpose are required.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO document_requests 
            (user_id, document_name, purpose, business_name, indigency_for, status, requested_at, is_deleted) 
        VALUES 
            (:user_id, :document_name, :purpose, :business_name, :indigency_for, 'Pending', NOW(), 0)
    ");

    $stmt->execute([
        ':user_id'       => $user_id,
        ':document_name' => $document_name,
        ':purpose'       => $purpose,
        ':business_name' => !empty($business_name) ? $business_name : null,
        ':indigency_for' => !empty($indigency_for) ? $indigency_for : null,
    ]);

    echo json_encode([
        'status' => 'success', 
        'message' => 'Your document request has been submitted successfully!'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
