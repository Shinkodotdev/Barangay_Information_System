<?php
require_once "../../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);
    $stmt = $pdo->prepare("UPDATE users 
                        SET status = 'Pending', is_archived = 0, archived_at = NULL 
                        WHERE user_id = ?");
    if ($stmt->execute([$userId])) {
        echo json_encode(['success' => true, 'message' => 'User has been restored successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to restore user.']);
    }
}
