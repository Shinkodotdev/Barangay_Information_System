<?php
function getUsersByStatus($pdo, $status, $limit = 50)
{
    $sql = "SELECT u.user_id, u.email, u.role, u.status, u.created_at,
                    CONCAT(ud.f_name, ' ', COALESCE(CONCAT(ud.m_name, ' '), ''), ud.l_name) AS full_name
                FROM users u
                JOIN user_details ud ON u.user_id = ud.user_id
                WHERE u.status = :status
                ORDER BY u.created_at DESC
                LIMIT :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getDocumentByStatus($pdo, $status, $limit = 50)
{
    $sql = "SELECT dr.request_id, 
                        dr.document_name, 
                        dr.purpose, 
                        dr.status, 
                        dr.requested_at, 
                        dr.processed_at,
                        dr.remarks,
                        CONCAT(ud.f_name, ' ', COALESCE(CONCAT(ud.m_name, ' '), ''), ud.l_name,
                                IF(ud.ext_name IS NOT NULL AND ud.ext_name != '', CONCAT(' ', ud.ext_name), '')
                        ) AS user_name,
                        CONCAT(ad.f_name, ' ', COALESCE(CONCAT(ad.m_name, ' '), ''), ad.l_name,
                                IF(ad.ext_name IS NOT NULL AND ad.ext_name != '', CONCAT(' ', ad.ext_name), '')
                        ) AS approved_by
                    FROM document_requests dr
                    JOIN user_details ud ON dr.user_id = ud.user_id
                    LEFT JOIN user_details ad ON dr.processed_by = ad.user_id
                    WHERE dr.status = :status
                    ORDER BY dr.processed_at DESC
                    LIMIT :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAllUsers($pdo, $status, $limit = 50)
{
    $sql = "SELECT u.user_id, u.email, u.role, u.status, u.is_Alive, u.created_at, u.updated_at, u.archived_at, u.dead_at,
                    CONCAT(ud.f_name, ' ', COALESCE(CONCAT(ud.m_name, ' '), ''), ud.l_name) AS full_name
                FROM users u
                JOIN user_details ud ON u.user_id = ud.user_id
                WHERE u.role = :status
                ORDER BY u.created_at DESC
                LIMIT :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAllEvents($pdo, $status = null, $limit = 50)
{
    $sql = "SELECT 
        e.event_id, 
        e.event_title, 
        e.event_description, 
        e.event_start, 
        e.event_end, 
        e.event_location, 
        e.event_type, 
        e.event_image, 
        e.attachment, 
        e.audience, 
        e.status,
        e.is_archived, 
        e.is_deleted, 
        e.created_at
    FROM events e";

    if ($status !== null) {
        $sql .= " WHERE e.status = :status";
    }

    $sql .= " ORDER BY e.created_at DESC LIMIT :limit";

    $stmt = $pdo->prepare($sql);

    if ($status !== null) {
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAllAnnouncements($pdo, $status = null, $limit = 50)
{
    $sql = "SELECT 
                a.announcement_id,
                a.announcement_title,
                a.announcement_content,
                a.announcement_category,
                a.announcement_location,
                a.announcement_image,
                a.attachment,
                a.status,
                a.priority,
                a.valid_until,
                a.audience,
                a.is_archived,
                a.created_at,
                a.updated_at,
                a.archived_at,

                -- 👇 Join users + user_details to fetch the full name
                CONCAT(ud.f_name, ' ', COALESCE(CONCAT(ud.m_name, ' '), ''), ud.l_name, 
                       IF(ud.ext_name IS NOT NULL AND ud.ext_name != '', CONCAT(' ', ud.ext_name), '')
                ) AS full_name

            FROM announcements a
            JOIN users u ON a.author_id = u.user_id
            JOIN user_details ud ON u.user_id = ud.user_id";

    if ($status !== null) {
        $sql .= " WHERE a.status = :status";
    }

    $sql .= " ORDER BY a.created_at DESC LIMIT :limit";

    $stmt = $pdo->prepare($sql);

    if ($status !== null) {
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getUserProfileById($pdo, $user_id)
{
    $sql = "
            SELECT 
                u.user_id, u.email, u.password, u.role, u.status AS user_status, u.is_alive, u.created_at, u.updated_at,

                ud.f_name, ud.m_name, ud.l_name, ud.ext_name, ud.gender, ud.photo, ud.contact_no, ud.civil_status,
                ud.occupation, ud.nationality, ud.voter_status, ud.pwd_status, 
                ud.senior_citizen_status, ud.religion, ud.blood_type, ud.educational_attainment,

                ub.birth_date, ub.birth_place,

                ur.house_no, ur.purok, ur.barangay, ur.municipality, ur.province, ur.years_residency, 
                ur.household_head, ur.house_type, ur.ownership_status, ur.previous_address,

                uf.fathers_name, uf.fathers_birthplace, uf.mothers_name, uf.mothers_birthplace, 
                uf.spouse_name, uf.num_dependents, uf.contact_person, uf.emergency_contact_no,

                uh.health_condition, uh.common_health_issue, uh.vaccination_status, uh.height_cm, uh.weight_kg, 
                uh.last_medical_checkup, uh.health_remarks,

                ui.monthly_income, ui.income_source, ui.household_members, ui.additional_income_sources,
                ui.household_head_occupation, ui.income_proof,

                uid.id_type, uid.front_valid_id_path, uid.back_valid_id_path, uid.selfie_with_id

            FROM users u
            LEFT JOIN user_details ud ON u.user_id = ud.user_id
            LEFT JOIN user_birthdates ub ON u.user_id = ub.user_id
            LEFT JOIN user_residency ur ON u.user_id = ur.user_id
            LEFT JOIN user_family_info uf ON u.user_id = uf.user_id
            LEFT JOIN user_health_info uh ON u.user_id = uh.user_id
            LEFT JOIN user_income_info ui ON u.user_id = ui.user_id
            LEFT JOIN user_identity_docs uid ON u.user_id = uid.user_id
            WHERE u.user_id = ?
        ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getManageUsers($pdo, $role = null, $limit = 50)
{
    $sql = "SELECT 
                u.user_id, 
                u.email, 
                u.role, 
                u.status,
                u.is_archived,
                u.created_at,
                CONCAT(
                    ud.f_name, ' ',
                    COALESCE(CONCAT(ud.m_name, ' '), ''),
                    ud.l_name,
                    IF(ud.ext_name IS NOT NULL AND ud.ext_name != '', CONCAT(' ', ud.ext_name), '')
                ) AS full_name
            FROM users u
            LEFT JOIN user_details ud ON u.user_id = ud.user_id
            WHERE u.role = :role
            ORDER BY u.created_at DESC
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':role', $role, PDO::PARAM_STR);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getResidentAnnouncements($pdo, $limit = 5)
{
    $sql = "SELECT 
                    announcement_id, 
                    announcement_title, 
                    announcement_content, 
                    announcement_category, 
                    announcement_image, 
                    created_at
                FROM announcements
                WHERE (audience = 'Residents' OR audience = 'Public')
                AND is_archived = 0
                AND status = 'Published'
                ORDER BY priority DESC, created_at DESC
                LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getResidentEvents($pdo, $limit = 5)
{
    $sql = "SELECT 
                event_id, 
                event_title, 
                event_description, 
                event_start, 
                event_end, 
                event_location, 
                event_type, 
                event_image
            FROM events
            WHERE (audience = 'Residents' OR audience = 'Public')
              AND is_archived = 0
              AND status IN ('Upcoming', 'Ongoing')
            ORDER BY event_start ASC
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getResidentRecentDocumentRequests($pdo, $user_id, $limit = 5)
{
    $sql = "SELECT 
                request_id, 
                document_name, 
                status, 
                requested_at
            FROM document_requests
            WHERE user_id = :user_id
            AND is_deleted = 0
            ORDER BY requested_at DESC
            LIMIT :limit";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getActiveAnnouncements($pdo, $limit = 100)
{
    $sql = "
        SELECT a.*,
            ud.f_name, ud.m_name, ud.l_name, ud.ext_name,
            u.role
        FROM announcements a
        LEFT JOIN users u ON a.author_id = u.user_id
        LEFT JOIN user_details ud ON u.user_id = ud.user_id
        WHERE a.is_archived = 0
        AND a.audience IN ('Public','Residents','Officials')
        AND (a.valid_until IS NULL OR a.valid_until >= NOW())
        ORDER BY FIELD(a.priority, 'Urgent','High','Normal','Low'), a.created_at DESC
        LIMIT :limit
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getActiveEvents($pdo, $limit = 100)
{
    $sql = "
        SELECT e.*, DATE_ADD(e.event_end, INTERVAL 3 DAY) AS keep_until
        FROM events e
        WHERE e.is_archived = 0
        AND e.audience IN ('Public','Residents','Officials')
        ORDER BY e.event_start ASC
        LIMIT :limit
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getUserHealthInfo(PDO $pdo, int $user_id): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM user_health_info WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}
function computeBMI(?float $height_cm, ?float $weight_kg): array
{
    if (empty($height_cm) || empty($weight_kg)) {
        return ['bmi' => null, 'category' => 'N/A'];
    }

    $height_m = $height_cm / 100;
    $bmi = $weight_kg / ($height_m * $height_m);

    if ($bmi < 18.5) {
        $category = "Underweight";
    } elseif ($bmi < 24.9) {
        $category = "Normal";
    } elseif ($bmi < 29.9) {
        $category = "Overweight";
    } else {
        $category = "Obese";
    }

    return ['bmi' => round($bmi, 1), 'category' => $category];
}
function getCommunityHealthStats(PDO $pdo): array
{
    $stats = [];

    // Gender
    $stmt = $pdo->query("SELECT gender, COUNT(*) AS total FROM user_details GROUP BY gender");
    $stats['gender'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Blood Type
    $stmt = $pdo->query("SELECT blood_type, COUNT(*) AS total FROM user_details GROUP BY blood_type");
    $stats['bloodtype'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // PWD
    $stmt = $pdo->query("SELECT pwd_status, COUNT(*) AS total FROM user_details GROUP BY pwd_status");
    $pwdRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stats['pwd'] = ['PWD' => 0, 'Non-PWD' => 0];
    foreach ($pwdRows as $row) {
        if (strtolower($row['pwd_status']) === 'yes') {
            $stats['pwd']['PWD'] = $row['total'];
        } else {
            $stats['pwd']['Non-PWD'] = $row['total'];
        }
    }

    // Health Condition
    $stmt = $pdo->query("
        SELECT 
            SUM(CASE WHEN health_condition = 'Healthy' THEN 1 ELSE 0 END) AS healthy,
            SUM(CASE WHEN health_condition = 'Minor Illness' THEN 1 ELSE 0 END) AS minor,
            SUM(CASE WHEN health_condition = 'Chronic Illness' THEN 1 ELSE 0 END) AS chronic,
            SUM(CASE WHEN health_condition = 'Disabled' THEN 1 ELSE 0 END) AS disabled
        FROM user_health_info
    ");
    $stats['condition'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Common Health Issues
    $stmt = $pdo->query("
        SELECT 
            SUM(CASE WHEN common_health_issue LIKE '%diabetes%' THEN 1 ELSE 0 END) AS diabetes,
            SUM(CASE WHEN common_health_issue LIKE '%hypertension%' THEN 1 ELSE 0 END) AS hypertension,
            SUM(CASE WHEN common_health_issue LIKE '%asthma%' THEN 1 ELSE 0 END) AS asthma,
            SUM(CASE WHEN common_health_issue LIKE '%heart%' THEN 1 ELSE 0 END) AS heart
        FROM user_health_info
    ");
    $stats['issues'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // Average Height & Weight
    $stmt = $pdo->query("SELECT AVG(height_cm) AS avg_height FROM user_health_info WHERE height_cm > 0");
    $stats['avg_height'] = (float) ($stmt->fetchColumn() ?? 0);

    $stmt = $pdo->query("SELECT AVG(weight_kg) AS avg_weight FROM user_health_info WHERE weight_kg > 0");
    $stats['avg_weight'] = (float) ($stmt->fetchColumn() ?? 0);

    // Height Ranges
    $stmt = $pdo->query("
        SELECT 
            SUM(CASE WHEN height_cm < 150 THEN 1 ELSE 0 END) AS short,
            SUM(CASE WHEN height_cm BETWEEN 150 AND 170 THEN 1 ELSE 0 END) AS average,
            SUM(CASE WHEN height_cm > 170 THEN 1 ELSE 0 END) AS tall
        FROM user_health_info
    ");
    $stats['height_ranges'] = $stmt->fetch(PDO::FETCH_ASSOC);

    // BMI Distribution
    $stmt = $pdo->query("
        SELECT 
            SUM(CASE WHEN (weight_kg/(POWER(height_cm/100,2))) < 18.5 THEN 1 ELSE 0 END) AS underweight,
            SUM(CASE WHEN (weight_kg/(POWER(height_cm/100,2))) BETWEEN 18.5 AND 24.9 THEN 1 ELSE 0 END) AS normal,
            SUM(CASE WHEN (weight_kg/(POWER(height_cm/100,2))) BETWEEN 25 AND 29.9 THEN 1 ELSE 0 END) AS overweight,
            SUM(CASE WHEN (weight_kg/(POWER(height_cm/100,2))) >= 30 THEN 1 ELSE 0 END) AS obese
        FROM user_health_info
        WHERE height_cm > 0 AND weight_kg > 0
    ");
    $stats['bmi_ranges'] = $stmt->fetch(PDO::FETCH_ASSOC);

    return $stats;
}
function getAllOfficials($pdo, $limit = 50)
{
    $sql = "
        SELECT 
            u.user_id, 
            u.email, 
            u.role, 
            u.status, 
            u.created_at,
            CONCAT(ud.f_name, ' ', COALESCE(CONCAT(ud.m_name, ' '), ''), ud.l_name) AS full_name,
            o.position
        FROM users u
        JOIN user_details ud ON u.user_id = ud.user_id
        LEFT JOIN officials o ON u.user_id = o.user_id
        WHERE u.role = 'Official'
        ORDER BY o.position ASC, u.created_at DESC
        LIMIT :limit
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function updateExpiredOfficials($pdo)
{
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("
        UPDATE officials o
        INNER JOIN users u ON o.user_id = u.user_id
        SET u.role = CONCAT('Former ', o.position)
        WHERE o.end_of_term < :today
    ");
    $stmt->execute([':today' => $today]);
}
function assignOfficial($pdo, $userId, $position, $startOfTerm, $endOfTerm)
{
    try {
        $pdo->beginTransaction();

        // Check if already assigned
        $check = $pdo->prepare("SELECT COUNT(*) FROM officials WHERE user_id = :user_id");
        $check->execute([':user_id' => $userId]);
        if ($check->fetchColumn() > 0) {
            throw new Exception("This user is already assigned as an Official.");
        }

        // Insert new record
        $stmt = $pdo->prepare("
            INSERT INTO officials (user_id, position, start_of_term, end_of_term)
            VALUES (:user_id, :position, :start_of_term, :end_of_term)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':position' => $position,
            ':start_of_term' => $startOfTerm,
            ':end_of_term' => $endOfTerm
        ]);

        // Update user's role
        $stmt = $pdo->prepare("UPDATE users SET role = 'Official' WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);

        $pdo->commit();
        return "✅ User assigned as Official successfully!";
    } catch (Exception $e) {
        $pdo->rollBack();
        throw new Exception($e->getMessage());
    }
}
function getApprovedUsersForAssignment($pdo, $limit = 50)
{
    $sql = "
        SELECT u.user_id, u.email, u.role, u.status,
            CONCAT(
                d.f_name, ' ',
                COALESCE(CONCAT(d.m_name, ' '), ''),
                d.l_name,
                IF(d.ext_name IS NOT NULL AND d.ext_name != '', CONCAT(' ', d.ext_name), '')
            ) AS full_name
        FROM users u
        LEFT JOIN user_details d ON u.user_id = d.user_id
        LEFT JOIN officials o ON u.user_id = o.user_id
        WHERE u.role != 'Admin'
            AND u.status = 'Approved'
            AND o.user_id IS NULL
        ORDER BY u.created_at DESC
        LIMIT :limit
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAssignedPositions($pdo)
{
    $stmt = $pdo->query("SELECT position FROM officials");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
function updateUserDetails($pdo, $userId, $email, $status) {
    $sql = "UPDATE users 
            SET email = :email, status = :status, updated_at = NOW()
            WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':email' => $email,
        ':status' => $status,
        ':user_id' => $userId
    ]);
    return $stmt->rowCount() > 0;
}
function updateOfficialPosition($pdo, $userId, $position) {
    $sql = "UPDATE officials 
            SET position = :position, updated_at = NOW()
            WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':position' => $position,
        ':user_id' => $userId
    ]);
    return $stmt->rowCount() > 0;
}
function getAllActiveOfficials($pdo, $limit = 50)
{
    $sql = "
        SELECT 
            u.user_id, 
            u.email, 
            u.role, 
            u.status, 
            u.is_archived,
            u.is_deleted,
            u.created_at,
            CONCAT(ud.f_name, ' ', COALESCE(CONCAT(ud.m_name, ' '), ''), ud.l_name) AS full_name,
            o.position
        FROM users u
        JOIN user_details ud ON u.user_id = ud.user_id
        LEFT JOIN officials o ON u.user_id = o.user_id
        WHERE 
            u.role = 'Official'
            AND u.status != 'Rejected'
            AND (u.is_archived = 0 OR u.is_archived IS NULL)
            AND (u.is_deleted = 0 OR u.is_deleted IS NULL)
        ORDER BY o.position ASC, u.created_at DESC
        LIMIT :limit
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAllActiveUsers($pdo, $status, $limit = 50)
{
    $sql = "
        SELECT 
            u.user_id, 
            u.email, 
            u.role, 
            u.status, 
            u.is_Alive, 
            u.is_archived,
            u.is_deleted,
            u.created_at, 
            u.updated_at, 
            u.archived_at, 
            u.dead_at,
            CONCAT(ud.f_name, ' ', COALESCE(CONCAT(ud.m_name, ' '), ''), ud.l_name) AS full_name
        FROM users u
        JOIN user_details ud ON u.user_id = ud.user_id
        WHERE 
            u.role = :status
            AND u.status != 'Rejected'
            AND (u.is_archived = 0 OR u.is_archived IS NULL)
            AND (u.is_deleted = 0 OR u.is_deleted IS NULL)
        ORDER BY u.created_at DESC
        LIMIT :limit
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getOfficialAnnouncements($pdo, $limit = 5)
{
    $sql = "
        SELECT 
            announcement_id, 
            announcement_title, 
            announcement_content, 
            announcement_category, 
            announcement_image, 
            created_at
        FROM announcements
        WHERE 
            (audience IN ('Officials', 'Public', 'Residents'))
            AND is_archived = 0
            AND status = 'Published'
        ORDER BY priority DESC, created_at DESC
        LIMIT :limit
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// =============================
// ✅ Fetch Events for Officials
// =============================
function getOfficialEvents($pdo, $limit = 5)
{
    $sql = "
        SELECT 
            event_id, 
            event_title, 
            event_description, 
            event_start, 
            event_end, 
            event_location, 
            event_type, 
            event_image
        FROM events
        WHERE 
            (audience IN ('Officials', 'Public', 'Residents'))
            AND is_deleted = 0
            AND is_archived = 0
            AND status IN ('Upcoming', 'Ongoing')
        ORDER BY event_start ASC
        LIMIT :limit
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>