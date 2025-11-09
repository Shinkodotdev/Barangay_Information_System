<?php
require_once "../config/db.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $pdo->prepare("
        SELECT 
            u.user_id, u.email, u.role, u.status, u.created_at, u.updated_at, u.is_alive,
            ud.f_name, ud.m_name, ud.l_name, ud.ext_name, ud.gender, ud.photo, ud.contact_no, 
            ud.civil_status, ud.occupation, ud.nationality, ud.voter_status, ud.valid_id_path, 
            ud.pwd_status, ud.senior_citizen_status, ud.religion, ud.blood_type, ud.educational_attainment,
            
            ub.birth_date, ub.birth_place,
            
            uf.fathers_name, uf.fathers_birthplace, uf.mothers_name, uf.mothers_birthplace,
            uf.spouse_name, uf.num_dependents, uf.contact_person, uf.emergency_contact_no,
            
            uh.health_condition, uh.common_health_issue, uh.vaccination_status, uh.height_cm, uh.weight_kg,
            uh.last_medical_checkup, uh.health_remarks,
            
            ui.id_type, ui.front_valid_id_path, ui.back_valid_id_path, ui.selfie_with_id,
            
            inc.monthly_income, inc.income_source, inc.household_members, inc.additional_income_sources,
            inc.household_head_occupation, inc.income_proof,
            
            ur.house_no, ur.purok, ur.barangay, ur.municipality, ur.province, ur.years_residency,
            ur.household_head, ur.house_type, ur.ownership_status, ur.previous_address
        FROM users u
        LEFT JOIN user_details ud ON u.user_id = ud.user_id
        LEFT JOIN user_birthdates ub ON u.user_id = ub.user_id
        LEFT JOIN user_family_info uf ON u.user_id = uf.user_id
        LEFT JOIN user_health_info uh ON u.user_id = uh.user_id
        LEFT JOIN user_identity_docs ui ON u.user_id = ui.user_id
        LEFT JOIN user_income_info inc ON u.user_id = inc.user_id
        LEFT JOIN user_residency ur ON u.user_id = ur.user_id
        WHERE u.user_id = ?
    ");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode($user);
    } else {
        echo json_encode(["error" => "User not found"]);
    }
}
?>
