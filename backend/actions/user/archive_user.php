<?php
require_once "../../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);

    try {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET status = 'Rejected', 
                is_archived = 1, 
                archived_at = NOW() 
            WHERE user_id = ?
        ");
        
        if ($stmt->execute([$userId])) {
            echo json_encode([
                'success' => true, 
                'message' => 'User archived successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to archive user.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
?>
