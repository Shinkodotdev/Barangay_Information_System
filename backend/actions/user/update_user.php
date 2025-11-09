<?php
require_once '../../config/db.php';
require_once '../../models/Repository.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? null;
    $email = trim($_POST['email'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $position = trim($_POST['position'] ?? '');

    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'Missing user ID.']);
        exit;
    }

    try {
        // Fetch current role
        $stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            exit;
        }

        // Begin transaction
        $pdo->beginTransaction();

        // Common updates
        $updated = updateUserDetails($pdo, $userId, $email, $status);

        // Update position if user is an official
        if ($user['role'] === 'Official' && !empty($position)) {
            updateOfficialPosition($pdo, $userId, $position);
        }

        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'User updated successfully.']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
