<?php
require_once "../../config/db.php";
header('Content-Type: application/json; charset=utf-8');

try {
    $user_id = $_GET['user_id'] ?? null;

    if ($user_id) {
        $stmt = $pdo->prepare("
            SELECT 
                u.user_id, 
                u.email, 
                ud.f_name, ud.m_name, ud.l_name, ud.ext_name, 
                ud.blood_type, ud.pwd_status, ud.senior_citizen_status,
                uhi.health_condition, uhi.common_health_issue, uhi.vaccination_status,
                uhi.height_cm, uhi.weight_kg, uhi.last_medical_checkup, uhi.health_remarks
            FROM users u
            LEFT JOIN user_details ud ON u.user_id = ud.user_id
            LEFT JOIN user_health_info uhi ON u.user_id = uhi.user_id
            WHERE u.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $user_id]);
    } else {
        $stmt = $pdo->query("
            SELECT 
                u.user_id, 
                u.email, 
                ud.f_name, ud.m_name, ud.l_name, ud.ext_name, 
                ud.blood_type, ud.pwd_status, ud.senior_citizen_status,
                uhi.health_condition, uhi.common_health_issue, uhi.vaccination_status,
                uhi.height_cm, uhi.weight_kg, uhi.last_medical_checkup, uhi.health_remarks
            FROM users u
            LEFT JOIN user_details ud ON u.user_id = ud.user_id
            LEFT JOIN user_health_info uhi ON u.user_id = uhi.user_id
            ORDER BY ud.l_name ASC
        ");
    }

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($data) {
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        echo json_encode(['status' => 'empty', 'message' => 'No records found', 'data' => []]);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
