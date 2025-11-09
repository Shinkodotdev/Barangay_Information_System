<?php
require_once '../../../backend/config/db.php';
header('Content-Type: application/json');

if (!isset($_GET['incident_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Incident ID is required.']);
    exit;
}

$incident_id = intval($_GET['incident_id']);

try {
    // ✅ Fetch all persons involved in the incident
    $stmt = $pdo->prepare("
        SELECT 
            ip.person_id,
            ip.person_type,
            ip.role,
            ip.user_id,
            ip.non_resident_id,
            ud.f_name AS resident_fname,
            ud.m_name AS resident_mname,
            ud.l_name AS resident_lname,
            ud.ext_name AS resident_ext,
            u.email AS resident_email,
            ud.contact_no AS resident_contact,
            nr.f_name AS non_fname,
            nr.m_name AS non_mname,
            nr.l_name AS non_lname,
            nr.ext_name AS non_ext,
            nr.email AS non_email,
            nr.contact_no AS non_contact,
            nr.address AS non_address
        FROM incident_persons ip
        LEFT JOIN users u ON ip.user_id = u.user_id
        LEFT JOIN user_details ud ON ip.user_id = ud.user_id
        LEFT JOIN non_residents nr ON ip.non_resident_id = nr.non_resident_id
        WHERE ip.incident_id = ?
    ");
    $stmt->execute([$incident_id]);

    $persons = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $details = [];
        if ($row['person_type'] === 'resident') {
            $fullName = trim($row['resident_fname'] . ' ' . $row['resident_mname'] . ' ' . $row['resident_lname'] . ' ' . $row['resident_ext']);
            $details = [
                'user_id' => $row['user_id'],
                'full_name' => $fullName,
                'f_name' => $row['resident_fname'],
                'm_name' => $row['resident_mname'],
                'l_name' => $row['resident_lname'],
                'ext_name' => $row['resident_ext'],
                'email' => $row['resident_email'],
                'contact_no' => $row['resident_contact'],
            ];
        } elseif ($row['person_type'] === 'non_resident') {
            $fullName = trim($row['non_fname'] . ' ' . $row['non_mname'] . ' ' . $row['non_lname'] . ' ' . $row['non_ext']);
            $details = [
                'non_resident_id' => $row['non_resident_id'],
                'full_name' => $fullName,
                'f_name' => $row['non_fname'],
                'm_name' => $row['non_mname'],
                'l_name' => $row['non_lname'],
                'ext_name' => $row['non_ext'],
                'email' => $row['non_email'],
                'contact_no' => $row['non_contact'],
                'address' => $row['non_address']
            ];
        }

        $persons[] = [
            'person_type' => $row['person_type'],
            'role' => $row['role'],
            'details' => $details
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $persons]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
