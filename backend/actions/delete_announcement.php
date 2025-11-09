<?php
require '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing announcement ID']);
    exit;
}

try {
    if ($action === "archive") {
        $stmt = $pdo->prepare("UPDATE announcements 
                               SET status = 'Archived', is_archived = 1, archived_at = NOW() 
                               WHERE announcement_id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Announcement archived successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Announcement not found or already archived']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
