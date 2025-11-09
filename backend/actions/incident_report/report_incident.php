<?php
session_start();
require '../../config/db.php';
error_reporting(E_ERROR | E_PARSE); // Only fatal errors
header('Content-Type: application/json; charset=utf-8');

// ---------------------- Helper ----------------------
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// ---------------------- Form Fields ----------------------
$category    = clean($_POST['category'] ?? '');
$type        = clean($_POST['type'] ?? '');
$description = clean($_POST['description'] ?? '');
$location    = clean($_POST['location'] ?? '');
$date_time   = clean($_POST['date_time'] ?? '');

// Persons involved arrays
$person_types = $_POST['person_type'] ?? [];
$resident_ids = $_POST['resident_id'] ?? [];
$f_names      = $_POST['f_name'] ?? [];
$m_names      = $_POST['m_name'] ?? [];
$l_names      = $_POST['l_name'] ?? [];
$ext_names    = $_POST['ext_name'] ?? [];
$addresses    = $_POST['address'] ?? [];
$emails       = $_POST['email'] ?? [];
$contact_nos  = $_POST['contact_no'] ?? [];
$roles        = $_POST['role'] ?? [];

// Ensure all arrays have same length
$max = max(count($person_types), count($resident_ids), count($f_names), count($l_names), count($roles));
for ($i = 0; $i < $max; $i++) {
    $person_types[$i] = $person_types[$i] ?? 'non_resident';
    $resident_ids[$i] = $resident_ids[$i] ?? null;
    $f_names[$i]      = $f_names[$i] ?? '';
    $m_names[$i]      = $m_names[$i] ?? '';
    $l_names[$i]      = $l_names[$i] ?? '';
    $ext_names[$i]    = $ext_names[$i] ?? '';
    $addresses[$i]    = $addresses[$i] ?? '';
    $emails[$i]       = $emails[$i] ?? '';
    $contact_nos[$i]  = $contact_nos[$i] ?? '';
    $roles[$i]        = $roles[$i] ?? '';
}

// ---------------------- Validate required fields ----------------------
if (!$category || !$type || !$description || !$location || !$date_time) {
    echo json_encode(["status" => "error", "message" => "All required fields must be filled in."]);
    exit;
}

// ---------------------- Photo Upload ----------------------
$photoPath = null;
if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === 0) {
    $targetDir = "../../../uploads/incidents/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $fileName = time() . "_" . basename($_FILES['photo']['name']);
    $targetFile = $targetDir . $fileName;
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

    if (!in_array($_FILES['photo']['type'], $allowedTypes)) {
        echo json_encode(["status" => "error", "message" => "Invalid file type. Only JPG and PNG allowed."]);
        exit;
    }

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        $photoPath = $fileName;
    }
}

// ---------------------- Reporter Identification ----------------------
$reporter_user_id = $_SESSION['user_id'] ?? null;
$reporter_non_resident_id = null;

if (!$reporter_user_id && isset($_SESSION['non_resident'])) {
    if (empty($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
        echo json_encode(["status" => "error", "message" => "Please verify your email before submitting."]);
        exit;
    }

    $f_name = clean($_SESSION['non_resident']['f_name']);
    $m_name = clean($_SESSION['non_resident']['m_name'] ?? '');
    $l_name = clean($_SESSION['non_resident']['l_name']);
    $ext_name = clean($_SESSION['non_resident']['ext_name'] ?? '');
    $email = clean($_SESSION['non_resident']['email']);
    $contact = clean($_SESSION['non_resident']['contact_no'] ?? '');

    $stmt = $pdo->prepare("SELECT non_resident_id FROM non_residents WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();

    if ($existing) {
        $reporter_non_resident_id = $existing['non_resident_id'];
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO non_residents (f_name, m_name, l_name, ext_name, email, contact_no, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$f_name, $m_name, $l_name, $ext_name, $email, $contact]);
        $reporter_non_resident_id = $pdo->lastInsertId();
    }
} elseif (!$reporter_user_id) {
    echo json_encode(["status" => "error", "message" => "Unauthorized submission."]);
    exit;
}

// ---------------------- Insert or Update Incident ----------------------
try {
    $pdo->beginTransaction();
    $incident_id = clean($_POST['incident_id'] ?? '');

    if ($incident_id) {
        // Update
        $stmt = $pdo->prepare("
            UPDATE incidents
            SET category = ?, type = ?, description = ?, location = ?, photo = COALESCE(?, photo),
                date_time = ?, updated_at = NOW()
            WHERE incident_id = ?
        ");
        $stmt->execute([$category, $type, $description, $location, $photoPath, $date_time, $incident_id]);
        $pdo->prepare("DELETE FROM incident_persons WHERE incident_id = ?")->execute([$incident_id]);
    } else {
        // Insert
        $stmt = $pdo->prepare("
            INSERT INTO incidents (reporter_user_id, reporter_non_resident_id, category, type, description, location, photo, date_time, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$reporter_user_id, $reporter_non_resident_id, $category, $type, $description, $location, $photoPath, $date_time]);
        $incident_id = $pdo->lastInsertId();
    }

    // ---------------------- Insert Persons Involved ----------------------
    foreach ($person_types as $i => $ptype) {
        $role = clean($roles[$i] ?? '');

        if ($ptype === 'resident' && !empty($resident_ids[$i])) {
            $stmtPerson = $pdo->prepare("
                INSERT INTO incident_persons (incident_id, user_id, non_resident_id, person_type, role, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmtPerson->execute([$incident_id, $resident_ids[$i], null, $ptype, $role]);

        } elseif ($ptype === 'non_resident' && !empty($f_names[$i]) && !empty($l_names[$i])) {
            $stmtNR = $pdo->prepare("
                INSERT INTO non_residents (f_name, m_name, l_name, ext_name, email, contact_no, address, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmtNR->execute([
                clean($f_names[$i]),
                clean($m_names[$i]),
                clean($l_names[$i]),
                clean($ext_names[$i]),
                clean($emails[$i]),
                clean($contact_nos[$i]),
                clean($addresses[$i])
            ]);
            $non_resident_id = $pdo->lastInsertId();

            $stmtPerson = $pdo->prepare("
                INSERT INTO incident_persons (incident_id, user_id, non_resident_id, person_type, role, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmtPerson->execute([$incident_id, null, $non_resident_id, $ptype, $role]);
        }
    }

    $pdo->commit();
    echo json_encode(["status" => "success", "message" => "Incident report submitted successfully."]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>
