<?php
require '../../config/db.php';
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

$incident_id = isset($_GET['incident_id']) ? (int)$_GET['incident_id'] : 0;
if (!$incident_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid incident id']);
    exit;
}

try {
    $sql = "
        SELECT 
            ip.person_id, 
            ip.incident_id, 
            ip.person_type, 
            ip.user_id AS resident_user_id, 
            ip.non_resident_id,
            ip.role,

            -- Resident details
            u.user_id AS user_id,
            ud.user_details_id,
            ud.f_name AS resident_f, 
            ud.m_name AS resident_m, 
            ud.l_name AS resident_l, 
            ud.ext_name AS resident_ext,
            ud.contact_no AS resident_contact,

            -- Non-resident details
            nr.non_resident_id AS nr_id, 
            nr.f_name AS nr_f, 
            nr.m_name AS nr_m, 
            nr.l_name AS nr_l, 
            nr.ext_name AS nr_ext,
            nr.address AS nr_address, 
            nr.email AS nr_email, 
            nr.contact_no AS nr_contact

        FROM incident_persons ip
        LEFT JOIN users u ON ip.user_id = u.user_id
        LEFT JOIN user_details ud ON u.user_id = ud.user_id
        LEFT JOIN non_residents nr ON ip.non_resident_id = nr.non_resident_id
        WHERE ip.incident_id = ?
        ORDER BY ip.person_id ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$incident_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $persons = [];
    foreach ($rows as $row) {
        $isResident = ($row['person_type'] === 'resident');

        $persons[] = [
            'person_id' => (int)$row['person_id'],
            'person_type' => $row['person_type'],
            'role' => $row['role'],

            // Resident details
            'resident_id' => $isResident ? (int)$row['resident_user_id'] : '',
            'resident_name' => $isResident 
                ? trim($row['resident_l'] . ', ' . $row['resident_f'] . ' ' . 
                    ($row['resident_m'] ? substr($row['resident_m'], 0, 1) . '.' : '')) 
                : '',
            'resident_contact' => $isResident ? ($row['resident_contact'] ?? '') : '',

            // Non-resident details
            'non_resident_id' => !$isResident && $row['nr_id'] ? (int)$row['nr_id'] : '',
            'f_name' => !$isResident ? ($row['nr_f'] ?? '') : '',
            'm_name' => !$isResident ? ($row['nr_m'] ?? '') : '',
            'l_name' => !$isResident ? ($row['nr_l'] ?? '') : '',
            'ext_name' => !$isResident ? ($row['nr_ext'] ?? '') : '',
            'address' => !$isResident ? ($row['nr_address'] ?? '') : '',
            'email' => !$isResident ? ($row['nr_email'] ?? '') : '',
            'contact_no' => !$isResident ? ($row['nr_contact'] ?? '') : ''
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $persons]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
