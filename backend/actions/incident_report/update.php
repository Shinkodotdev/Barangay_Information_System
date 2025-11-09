<?php
require '../../config/db.php';
header('Content-Type: application/json');

function clean($d) { return htmlspecialchars(trim($d), ENT_QUOTES, 'UTF-8'); }

$incident_id = isset($_POST['incident_id']) ? (int)$_POST['incident_id'] : 0;
if (!$incident_id) {
    echo json_encode(['status'=>'error','message'=>'Invalid incident id']);
    exit;
}

$category = clean($_POST['category'] ?? '');
$type = clean($_POST['type'] ?? '');
$description = clean($_POST['description'] ?? '');
$location = clean($_POST['location'] ?? '');
$date_time = clean($_POST['date_time'] ?? '');

try {
    $pdo->beginTransaction();

    // ----------------- Handle photo upload -----------------
    $uploadsDir = "../../../uploads/incidents/";
    if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0777, true);

    // Get current photo filename
    $stmt = $pdo->prepare("SELECT photo FROM incidents WHERE incident_id = ?");
    $stmt->execute([$incident_id]);
    $curPhoto = $stmt->fetchColumn();

    $newPhotoName = $curPhoto;
    if (!empty($_FILES['photo']['name'])) {
        $tmp = $_FILES['photo']['tmp_name'];
        $name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.\-]/','_', $_FILES['photo']['name']);
        if (!move_uploaded_file($tmp, $uploadsDir . $name)) {
            throw new Exception('Failed to upload photo');
        }
        $newPhotoName = $name;
        // remove old photo if replaced
        if ($curPhoto && file_exists($uploadsDir . $curPhoto) && $curPhoto !== $newPhotoName) {
            @unlink($uploadsDir . $curPhoto);
        }
    }

    // ----------------- Update incidents table -----------------
    $stmt = $pdo->prepare("
        UPDATE incidents 
        SET category = ?, type = ?, description = ?, location = ?, photo = ?, date_time = ?, updated_at = NOW() 
        WHERE incident_id = ?
    ");
    $stmt->execute([$category, $type, $description, $location, $newPhotoName, $date_time, $incident_id]);

    // ----------------- Sync persons involved -----------------
    $existing = [];
    $stmt = $pdo->prepare("SELECT person_id FROM incident_persons WHERE incident_id = ?");
    $stmt->execute([$incident_id]);
    $existing = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

    // Input arrays
    $in_person_id = $_POST['person_id'] ?? [];
    $in_person_type = $_POST['person_type'] ?? [];
    $in_resident_id = $_POST['resident_id'] ?? [];
    $in_non_resident_id = $_POST['non_resident_id'] ?? [];
    $in_f = $_POST['f_name'] ?? [];
    $in_m = $_POST['m_name'] ?? [];
    $in_l = $_POST['l_name'] ?? [];
    $in_ext = $_POST['ext_name'] ?? [];
    $in_addr = $_POST['address'] ?? [];
    $in_email = $_POST['email'] ?? [];
    $in_contact = $_POST['contact_no'] ?? [];
    $in_role = $_POST['role'] ?? [];

    $processed = [];
    $count = max(count($in_person_type), count($in_role));

    for ($i = 0; $i < $count; $i++) {
        $ptype = $in_person_type[$i] ?? '';
        $role = clean($in_role[$i] ?? '');
        $pid = isset($in_person_id[$i]) && $in_person_id[$i] !== '' ? (int)$in_person_id[$i] : 0;

        // ---------- Resident ----------
        if ($ptype === 'resident') {
            $user_id = isset($in_resident_id[$i]) && $in_resident_id[$i] !== '' ? (int)$in_resident_id[$i] : null;

            if ($pid) {
                // update existing
                $pdo->prepare("
                    UPDATE incident_persons 
                    SET person_type = ?, user_id = ?, non_resident_id = NULL, role = ?, updated_at = NOW() 
                    WHERE person_id = ?
                ")->execute([$ptype, $user_id, $role, $pid]);
                $processed[] = $pid;
            } else {
                // insert new
                $pdo->prepare("
                    INSERT INTO incident_persons (incident_id, person_type, user_id, role, created_at) 
                    VALUES (?, ?, ?, ?, NOW())
                ")->execute([$incident_id, $ptype, $user_id, $role]);
                $processed[] = $pdo->lastInsertId();
            }
        }

        // ---------- Non-resident ----------
        elseif ($ptype === 'non_resident') {
            $nr_id_sub = isset($in_non_resident_id[$i]) && $in_non_resident_id[$i] !== '' ? (int)$in_non_resident_id[$i] : 0;
            $f = clean($in_f[$i] ?? '');
            $m = clean($in_m[$i] ?? '');
            $l = clean($in_l[$i] ?? '');
            $ext = clean($in_ext[$i] ?? '');
            $addr = clean($in_addr[$i] ?? '');
            $email = clean($in_email[$i] ?? '');
            $contact = clean($in_contact[$i] ?? '');

            if ($nr_id_sub) {
                // update existing non-resident
                $pdo->prepare("
                    UPDATE non_residents 
                    SET f_name = ?, m_name = ?, l_name = ?, ext_name = ?, email = ?, contact_no = ?, address = ? 
                    WHERE non_resident_id = ?
                ")->execute([$f, $m, $l, $ext, $email, $contact, $addr, $nr_id_sub]);
                $current_nr_id = $nr_id_sub;
            } else {
                // insert new non-resident
                $pdo->prepare("
                    INSERT INTO non_residents (f_name, m_name, l_name, ext_name, email, contact_no, address, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ")->execute([$f, $m, $l, $ext, $email, $contact, $addr]);
                $current_nr_id = $pdo->lastInsertId();
            }

            if ($pid) {
                // update link
                $pdo->prepare("
                    UPDATE incident_persons 
                    SET person_type = ?, user_id = NULL, non_resident_id = ?, role = ?, updated_at = NOW() 
                    WHERE person_id = ?
                ")->execute([$ptype, $current_nr_id, $role, $pid]);
                $processed[] = $pid;
            } else {
                // insert link
                $pdo->prepare("
                    INSERT INTO incident_persons (incident_id, person_type, non_resident_id, role, created_at) 
                    VALUES (?, ?, ?, ?, NOW())
                ")->execute([$incident_id, $ptype, $current_nr_id, $role]);
                $processed[] = $pdo->lastInsertId();
            }
        }
    }

    // ---------- Delete removed persons ----------
    foreach ($existing as $ex) {
        if (!in_array($ex, $processed)) {
            $pdo->prepare("DELETE FROM incident_persons WHERE person_id = ?")->execute([$ex]);
        }
    }

    $pdo->commit();
    echo json_encode(['status'=>'success','message'=>'Incident updated successfully.']);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Update incident error: " . $e->getMessage());
    echo json_encode(['status'=>'error','message'=>'Failed to update incident.']);
}
