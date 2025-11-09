<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../config/db.php');

/* ✅ Define upload directory (absolute path) */
$uploadDir = __DIR__ . '/../../uploads/announcement/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['announcement_id'] ?? null;
    $title = $_POST['announcement_title'];
    $content = $_POST['announcement_content'];
    $priority = $_POST['priority'];
    $audience = $_POST['audience'];
    $status = $_POST['status'];
    $location = $_POST['announcement_location'] ?? null;
    $valid_until = !empty($_POST['valid_until']) ? $_POST['valid_until'] : null; // ✅ new field
    $author_id = $_SESSION['user_id'] ?? null; // 👈 logged-in user is the author

    $attachment = '';
    $image = '';

    // ✅ Handle attachment upload
    if (!empty($_FILES['attachment']['name'])) {
        $filename = time() . '_' . basename($_FILES['attachment']['name']);
        $uploadPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadPath)) {
            // Save relative path for DB
            $attachment = '../../../uploads/announcement/' . $filename;
        }
    }

    // ✅ Handle announcement image upload
    if (!empty($_FILES['announcement_image']['name'])) {
        $filename = time() . '_' . basename($_FILES['announcement_image']['name']);
        $uploadPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['announcement_image']['tmp_name'], $uploadPath)) {
            $image = '../../../uploads/announcement/' . $filename;
        }
    }

    if ($id) {
        // ✅ Update existing announcement
        $stmt = $pdo->prepare("UPDATE announcements 
            SET announcement_title=?, announcement_content=?, priority=?, audience=?, status=?, 
                announcement_location=?, announcement_image=?, attachment=?, valid_until=? 
            WHERE announcement_id=?");
        $stmt->execute([$title, $content, $priority, $audience, $status, $location, $image, $attachment, $valid_until, $id]);

        $_SESSION['swal'] = ['type' => 'success', 'msg' => 'Announcement updated successfully!'];
    } else {
        // ✅ Insert new announcement
        $stmt = $pdo->prepare("INSERT INTO announcements 
            (announcement_title, announcement_content, priority, audience, status, 
             announcement_location, announcement_image, attachment, valid_until, author_id, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$title, $content, $priority, $audience, $status, $location, $image, $attachment, $valid_until, $author_id]);

        $_SESSION['swal'] = ['type' => 'success', 'msg' => 'Announcement created successfully!'];
    }

    // ✅ Redirect based on role
    if ($_SESSION['role'] === 'Admin') {
        header("Location: ../../frontend/pages/admin/manage_announcements.php");
    } elseif ($_SESSION['role'] === 'Official') {
        header("Location: ../../frontend/pages/official/official_announcement_events.php");
    }
    exit;
}

// ✅ Archive instead of delete (also update status)
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("UPDATE announcements 
                               SET status = 'Archived', is_archived = 1, archived_at = NOW() 
                               WHERE announcement_id = ?");
    $stmt->execute([$_GET['delete']]);

    $_SESSION['swal'] = ['type' => 'success', 'msg' => 'Announcement archived successfully!'];

    if ($_SESSION['role'] === 'Admin') {
        header("Location: ../../frontend/pages/admin/manage_announcements.php");
    } elseif ($_SESSION['role'] === 'Official') {
        header("Location: ../../frontend/pages/official/official_announcement_events.php");
    }
    exit;
}
