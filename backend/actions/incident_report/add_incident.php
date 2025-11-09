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

try {
    // Collect inputs
    $incident_category    = trim($_POST['incident_category'] ?? '');
    $incident_type        = trim($_POST['incident_type'] ?? '');
    $incident_description = trim($_POST['incident_description'] ?? '');
    $incident_location    = trim($_POST['incident_location'] ?? '');
    $date_time            = trim($_POST['date_time'] ?? '');
    
    // Validate required fields
    if (empty($incident_category) || empty($incident_type) || empty($incident_description) || empty($incident_location) || empty($date_time)) {
        echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled.']);
        exit;
    }

    // ✅ Handle photo upload (optional)
    $photoPath = null;
    if (!empty($_FILES['incident_photo']['name'])) {
        $uploadDir = "../../uploads/incidents/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES['incident_photo']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['incident_photo']['tmp_name'], $targetFile)) {
            $photoPath = "uploads/incidents/" . $fileName;
        }
    }

    // ✅ Insert into incident_reports
    $stmt = $pdo->prepare("
        INSERT INTO incident_reports 
        (user_id, category, type, description, location, photo, date_time, status, created_at) 
        VALUES 
        (:user_id, :category, :type, :description, :location, :photo, :date_time, 'Pending', NOW())
    ");
    $stmt->execute([
        ':user_id'     => $user_id,
        ':category'    => $incident_category,
        ':type'        => $incident_type,
        ':description' => $incident_description,
        ':location'    => $incident_location,
        ':photo'       => $photoPath,
        ':date_time'   => $date_time
    ]);

    $incident_id = $pdo->lastInsertId();

    // ✅ Insert persons involved
    if (!empty($_POST['roles'])) {
        foreach ($_POST['roles'] as $index => $role) {
            $person_type = $_POST['person_type'][$index] ?? '';
            $resident_id = $_POST['resident_id'][$index] ?? null;
            $non_resident_name = $_POST['non_resident_name'][$index] ?? null;

            $stmtPerson = $pdo->prepare("
                INSERT INTO incident_persons 
                (incident_id, person_type, resident_id, non_resident_name, role) 
                VALUES (:incident_id, :person_type, :resident_id, :non_resident_name, :role)
            ");
            $stmtPerson->execute([
                ':incident_id'      => $incident_id,
                ':person_type'      => $person_type,
                ':resident_id'      => !empty($resident_id) ? $resident_id : null,
                ':non_resident_name'=> !empty($non_resident_name) ? strtoupper($non_resident_name) : null,
                ':role'             => $role
            ]);
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Incident report submitted successfully!'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
