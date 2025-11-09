<?php
session_start();
include('../../config/db.php');

// Get incident ID from query
$incidentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$incidentId) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid incident ID']);
    exit;
}

// Fetch incident details
$incidentQuery = $pdo->prepare("
    SELECT i.incident_id, i.category, i.type, i.description, i.location, i.date_time, i.photo,
           ud.f_name AS resident_fname, ud.l_name AS resident_lname,
           nr.f_name AS nonres_fname, nr.l_name AS nonres_lname
    FROM incidents i
    LEFT JOIN users u ON i.reporter_user_id = u.user_id
    LEFT JOIN user_details ud ON u.user_id = ud.user_id
    LEFT JOIN non_residents nr ON i.reporter_non_resident_id = nr.non_resident_id
    WHERE i.incident_id = :id
    LIMIT 1
");
$incidentQuery->bindValue(':id', $incidentId, PDO::PARAM_INT);
$incidentQuery->execute();
$incident = $incidentQuery->fetch(PDO::FETCH_ASSOC);

if (!$incident) {
    echo json_encode(['status' => 'error', 'message' => 'Incident not found']);
    exit;
}

// Determine reporter name
$reporter = $incident['resident_fname'] 
            ? $incident['resident_fname'] . ' ' . $incident['resident_lname'] . " (Resident)"
            : ($incident['nonres_fname'] 
               ? $incident['nonres_fname'] . ' ' . $incident['nonres_lname'] . " (Non-Resident)"
               : "Unknown");
$incident['reporter'] = $reporter;

// Fetch persons involved
$personsQuery = $pdo->prepare("
    SELECT ip.*, 
           ud.f_name AS res_fname, ud.m_name AS res_mname, ud.l_name AS res_lname,
           nr.f_name AS nonres_fname, nr.m_name AS nonres_mname, nr.l_name AS nonres_lname,
           ip.role
    FROM incident_persons ip
    LEFT JOIN users u ON ip.user_id = u.user_id
    LEFT JOIN user_details ud ON u.user_id = ud.user_id
    LEFT JOIN non_residents nr ON ip.non_resident_id = nr.non_resident_id
    WHERE ip.incident_id = :id
");
$personsQuery->bindValue(':id', $incidentId, PDO::PARAM_INT);
$personsQuery->execute();
$persons = $personsQuery->fetchAll(PDO::FETCH_ASSOC);

// Return JSON
echo json_encode([
    'status' => 'success',
    'incident' => $incident,
    'persons' => $persons
]);
