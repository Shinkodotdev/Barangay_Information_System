<?php
session_start();
require('../../config/db.php');

// ✅ 1. Validate the incident ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../../../frontend/pages/admin/incident_reports.php?error=InvalidID');
    exit;
}

$incident_id = (int) $_GET['id'];

// ✅ 2. Fetch current archive status
$stmt = $pdo->prepare("SELECT is_archived FROM incidents WHERE incident_id = ?");
$stmt->execute([$incident_id]);
$incident = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$incident) {
    header('Location: ../../../frontend/pages/admin/incident_reports.php?error=NotFound');
    exit;
}

// ✅ 3. Toggle archive status
$new_status = $incident['is_archived'] ? 0 : 1;

// ✅ 4. Update the database
$update = $pdo->prepare("UPDATE incidents SET is_archived = ?, updated_at = NOW() WHERE incident_id = ?");
$update->execute([$new_status, $incident_id]);

// ✅ 5. Redirect back with success message
$statusText = $new_status ? 'archived' : 'unarchived';
header("Location: ../../../frontend/pages/admin/incident_reports.php?success=Incident has been $statusText successfully.&archived=$new_status");
exit;
?>
