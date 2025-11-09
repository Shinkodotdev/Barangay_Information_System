<?php

class IncidentRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // 🔹 Count total incidents (archived or not)
    public function countIncidents($archived)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total FROM incidents WHERE is_archived = :archived");
        $stmt->execute(['archived' => $archived]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // 🔹 Fetch grouped categories
    public function getCategories($archived)
    {
        $stmt = $this->pdo->prepare("
            SELECT category, COUNT(*) AS total 
            FROM incidents 
            WHERE is_archived = :archived 
            GROUP BY category
        ");
        $stmt->execute(['archived' => $archived]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Fetch grouped types
    public function getTypes($archived)
    {
        $stmt = $this->pdo->prepare("
            SELECT type, COUNT(*) AS total 
            FROM incidents 
            WHERE is_archived = :archived 
            GROUP BY type
        ");
        $stmt->execute(['archived' => $archived]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Fetch paginated incidents with reporter details
    public function getIncidents($archived, $limit, $offset)
    {
        $sql = "
            SELECT i.incident_id, i.category, i.type, i.description, i.location, i.date_time,
                   i.photo, i.is_archived, i.reporter_user_id, i.reporter_non_resident_id,
                   ud.f_name AS resident_fname, ud.l_name AS resident_lname,
                   nr.f_name AS nonres_fname, nr.l_name AS nonres_lname
            FROM incidents i
            LEFT JOIN users u ON i.reporter_user_id = u.user_id
            LEFT JOIN user_details ud ON u.user_id = ud.user_id
            LEFT JOIN non_residents nr ON i.reporter_non_resident_id = nr.non_resident_id
            WHERE i.is_archived = :archived
            ORDER BY i.date_time DESC
            LIMIT :limit OFFSET :offset
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':archived', $archived, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔹 Fetch persons involved for multiple incidents
    public function getPersonsInvolved($incidentIds)
    {
        if (empty($incidentIds))
            return [];

        $inQuery = implode(',', array_map('intval', $incidentIds));
        $sql = "
            SELECT ip.*, 
                   ud.f_name AS res_fname, ud.m_name AS res_mname, ud.l_name AS res_lname,
                   nr.f_name AS nonres_fname, nr.m_name AS nonres_mname, nr.l_name AS nonres_lname
            FROM incident_persons ip
            LEFT JOIN users u ON ip.user_id = u.user_id
            LEFT JOIN user_details ud ON u.user_id = ud.user_id
            LEFT JOIN non_residents nr ON ip.non_resident_id = nr.non_resident_id
            WHERE ip.incident_id IN ($inQuery)
        ";
        $stmt = $this->pdo->query($sql);
        $persons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $incidentPersons = [];
        foreach ($persons as $p) {
            $incidentPersons[$p['incident_id']][] = $p;
        }

        return $incidentPersons;
    }
    // 🔹 Fetch paginated incidents by specific reporter
    public function getIncidentsByReporter($reporterUserId, $archived, $limit, $offset)
    {
        $sql = "
        SELECT 
            i.incident_id, i.category, i.type, i.description, i.location, i.date_time,
            i.photo, i.is_archived, i.reporter_user_id, i.reporter_non_resident_id,
            ud.f_name AS resident_fname, ud.l_name AS resident_lname,
            nr.f_name AS nonres_fname, nr.l_name AS nonres_lname
        FROM incidents i
        LEFT JOIN users u ON i.reporter_user_id = u.user_id
        LEFT JOIN user_details ud ON u.user_id = ud.user_id
        LEFT JOIN non_residents nr ON i.reporter_non_resident_id = nr.non_resident_id
        WHERE 
            i.is_archived = :archived
            AND i.reporter_user_id = :reporter_user_id
        ORDER BY i.date_time DESC
        LIMIT :limit OFFSET :offset
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':archived', $archived, PDO::PARAM_INT);
        $stmt->bindValue(':reporter_user_id', $reporterUserId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function countIncidentsByReporter($reporterUserId, $archived)
    {
        $stmt = $this->pdo->prepare("
        SELECT COUNT(*) AS total 
        FROM incidents 
        WHERE is_archived = :archived 
          AND reporter_user_id = :reporter_user_id
    ");
        $stmt->execute([
            'archived' => $archived,
            'reporter_user_id' => $reporterUserId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

}

