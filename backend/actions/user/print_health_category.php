<?php
require_once "../../config/db.php";
header('Content-Type: application/json; charset=utf-8');

try {
    $category = $_GET['category'] ?? '';
    $category = strtoupper(trim($category));

    if (empty($category)) {
        echo json_encode(['success' => false, 'message' => 'Missing category parameter.']);
        exit;
    }

    // 🟢 Build WHERE clause based on category
    switch ($category) {
        case 'MALE':
        case 'FEMALE':
        case 'LGBTQ':
        case 'OTHER':
            $whereClause = "ud.gender = '$category'";
            break;

        case 'PWD':
            $whereClause = "ud.pwd_status = 'Yes'";
            break;

        case 'SENIOR':
        case 'SENIOR CITIZEN':
            $whereClause = "ud.senior_citizen_status = 'Yes'";
            break;

        case 'VACCINATED':
            $whereClause = "uhi.vaccination_status LIKE '%Vaccinated%'";
            break;

        case 'UNVACCINATED':
            $whereClause = "uhi.vaccination_status IS NULL OR uhi.vaccination_status = 'Unvaccinated'";
            break;

        case 'HEALTHY':
            $whereClause = "uhi.health_condition = 'Healthy'";
            break;

        case 'DIABETES':
            $whereClause = "uhi.common_health_issue LIKE '%diabetes%'";
            break;

        case 'HYPERTENSION':
            $whereClause = "uhi.common_health_issue LIKE '%hypertension%'";
            break;

        case 'ASTHMA':
            $whereClause = "uhi.common_health_issue LIKE '%asthma%'";
            break;

        case 'OVERWEIGHT':
            $whereClause = "(uhi.weight_kg / POWER(uhi.height_cm / 100, 2)) BETWEEN 25 AND 29.9";
            break;

        case 'OBESE':
            $whereClause = "(uhi.weight_kg / POWER(uhi.height_cm / 100, 2)) >= 30";
            break;

        case 'UNDERWEIGHT':
            $whereClause = "(uhi.weight_kg / POWER(uhi.height_cm / 100, 2)) < 18.5";
            break;

        case 'ALL':
            $whereClause = "1";
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid category.']);
            exit;
    }

    // 🟢 Main Query — joined with all relevant tables
    $query = "
        SELECT 
            u.user_id,
            u.email,
            CONCAT(
                ud.f_name, ' ',
                COALESCE(CONCAT(ud.m_name, ' '), ''),
                ud.l_name,
                IF(ud.ext_name IS NOT NULL AND ud.ext_name != '', CONCAT(' ', ud.ext_name), '')
            ) AS full_name,
            ud.gender,
            ud.contact_no,
            ur.purok,
            ur.barangay,
            ur.municipality,
            ur.province,
            TIMESTAMPDIFF(YEAR, ub.birth_date, CURDATE()) AS age,
            ud.pwd_status,
            ud.senior_citizen_status,
            ud.blood_type,
            uhi.health_condition,
            uhi.common_health_issue,
            uhi.vaccination_status,
            uhi.height_cm,
            uhi.weight_kg,
            ROUND(uhi.weight_kg / POWER(uhi.height_cm / 100, 2), 1) AS bmi,
            CASE
                WHEN ROUND(uhi.weight_kg / POWER(uhi.height_cm / 100, 2), 1) < 18.5 THEN 'Underweight'
                WHEN ROUND(uhi.weight_kg / POWER(uhi.height_cm / 100, 2), 1) BETWEEN 18.5 AND 24.9 THEN 'Normal'
                WHEN ROUND(uhi.weight_kg / POWER(uhi.height_cm / 100, 2), 1) BETWEEN 25 AND 29.9 THEN 'Overweight'
                WHEN ROUND(uhi.weight_kg / POWER(uhi.height_cm / 100, 2), 1) >= 30 THEN 'Obese'
                ELSE 'Unknown'
            END AS bmi_category,
            uhi.last_medical_checkup,
            uhi.health_remarks
        FROM users u
        LEFT JOIN user_details ud ON u.user_id = ud.user_id
        LEFT JOIN user_birthdates ub ON u.user_id = ub.user_id
        LEFT JOIN user_health_info uhi ON u.user_id = uhi.user_id
        LEFT JOIN user_residency ur ON u.user_id = ur.user_id
        WHERE $whereClause
        AND (u.is_deleted = 0 OR u.is_deleted IS NULL)
        AND (u.is_archived = 0 OR u.is_archived IS NULL)
        AND u.status NOT IN ('Rejected')
        ORDER BY ud.l_name ASC
    ";

    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'category' => $category,
        'users' => $users
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
