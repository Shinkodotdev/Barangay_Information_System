<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../config/db.php');

/* ✅ Define upload directories (absolute paths) */
$eventUploadDir = __DIR__ . '/../../uploads/events/';
$attachmentUploadDir = __DIR__ . '/../../uploads/events/';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = $_POST['event_id'] ?? null;
    $title    = $_POST['event_title'];
    $desc     = $_POST['event_description'];
    $start    = $_POST['event_start'];
    $end      = $_POST['event_end'];
    $location = $_POST['event_location'];
    $type     = $_POST['event_type'];
    $audience = $_POST['audience'];
    $status   = $_POST['status']; // ✅ Get status from form

    // ✅ Get old values from hidden inputs
    $eventImage   = $_POST['old_event_image'] ?? '';
    $attachment   = $_POST['old_attachment'] ?? '';

    // ✅ Handle attachment upload
    if (!empty($_FILES['attachment']['name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $filename = time() . '_' . basename($_FILES['attachment']['name']);
        $uploadPath = $attachmentUploadDir . $filename;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadPath)) {
            $attachment = '../../../uploads/events/' . $filename;
        }
    }

    // ✅ Handle event image upload
    if (!empty($_FILES['event_image']['name']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $filename = time() . '_' . basename($_FILES['event_image']['name']);
        $uploadPath = $eventUploadDir . $filename;

        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $uploadPath)) {
            $eventImage = '../../../uploads/events/' . $filename;
        }
    }

    // ✅ Update existing event
if ($id) {
    // Build query dynamically
    $sql = "UPDATE events 
            SET event_title=?, event_description=?, event_start=?, event_end=?, 
                event_location=?, event_type=?, audience=?, status=?";

    $params = [$title, $desc, $start, $end, $location, $type, $audience, $status];

    // If new event image uploaded
    if (!empty($_FILES['event_image']['name']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $sql .= ", event_image=?";
        $params[] = $eventImage;
    }

    // If new attachment uploaded
    if (!empty($_FILES['attachment']['name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $sql .= ", attachment=?";
        $params[] = $attachment;
    }

    $sql .= " WHERE event_id=?";
    $params[] = $id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $_SESSION['swal'] = ['type' => 'success', 'msg' => 'Event updated successfully!'];
}
 else {
        // ✅ Insert new event
        $stmt = $pdo->prepare("INSERT INTO events 
            (event_title, event_description, event_start, event_end, event_location, 
             event_type, audience, status, event_image, attachment, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$title, $desc, $start, $end, $location, $type, $audience, $status, $eventImage, $attachment]);

        $_SESSION['swal'] = ['type' => 'success', 'msg' => 'Event created successfully!'];
    }

   // ✅ Redirect back to the correct page
$redirect = $_SERVER['HTTP_REFERER'] ?? '';

if (strpos($redirect, 'manage_announcements.php') !== false) {
    header("Location: ../../frontend/pages/admin/manage_announcements.php");
} elseif (strpos($redirect, 'Events.php') !== false) {
    header("Location: ../../frontend/pages/admin/Events.php");
} elseif ($_SESSION['role'] === 'Official') {
    header("Location: ../../frontend/pages/official/official_announcement_events.php");
} else {
    // Default fallback
    header("Location: ../../frontend/pages/admin/manage_announcements.php");
}
exit;

}

/* ✅ Archive event (instead of delete) */
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("UPDATE events 
                           SET is_archived = 1, status = 'Cancelled' 
                           WHERE event_id = ?");
    $stmt->execute([$_GET['delete']]);

    $_SESSION['swal'] = ['type' => 'success', 'msg' => 'Event archived and cancelled successfully!'];

    // ✅ Redirect back to the correct page
    $redirect = $_SERVER['HTTP_REFERER'] ?? '';

    if (strpos($redirect, 'manage_announcements.php') !== false) {
        header("Location: ../../frontend/pages/admin/manage_announcements.php");
    } elseif (strpos($redirect, 'Events.php') !== false) {
        header("Location: ../../frontend/pages/admin/Events.php");
    } elseif ($_SESSION['role'] === 'Official') {
        header("Location: ../../frontend/pages/official/official_announcement_events.php");
    } else {
        // Default fallback
        header("Location: ../../frontend/pages/admin/manage_announcements.php");
    }
    exit;
}



?>
