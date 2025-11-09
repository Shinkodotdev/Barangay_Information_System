<?php
header("Content-Type: application/json");
require_once "../config/db.php"; // adjust path if needed

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
        exit;
    }

    // Required fields
    $id          = $_POST["announcement_id"] ?? null;
    $title       = trim($_POST["announcement_title"] ?? "");
    $content     = trim($_POST["announcement_content"] ?? "");
    $category    = $_POST["announcement_category"] ?? "";
    $location    = $_POST["announcement_location"] ?? "";
    $status      = $_POST["status"] ?? "Active";
    $audience    = $_POST["audience"] ?? "All";
    $priority    = $_POST["priority"] ?? "Normal";
    $valid_until = $_POST["valid_until"] ?? null;

    if (!$id || !$title) {
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
        exit;
    }

    // === Handle file uploads ===
    $uploadDir = realpath(__DIR__ . "/../../uploads/announcement/") . "/";

    // Get existing file names
    $stmtOld = $pdo->prepare("SELECT announcement_image, attachment FROM announcements WHERE announcement_id = ?");
    $stmtOld->execute([$id]);
    $old = $stmtOld->fetch(PDO::FETCH_ASSOC);

    $imagePath = $old["announcement_image"] ?? null;
    $attachmentPath = $old["attachment"] ?? null;

    // Helper for secure file upload
    function handleFileUpload($fileKey, $prefix, $uploadDir) {
        if (!empty($_FILES[$fileKey]["name"]) && is_uploaded_file($_FILES[$fileKey]["tmp_name"])) {
            $ext = pathinfo($_FILES[$fileKey]["name"], PATHINFO_EXTENSION);
            $newName = $prefix . "_" . time() . "_" . uniqid("", true) . "." . $ext;
            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($_FILES[$fileKey]["tmp_name"], $targetPath)) {
                return "../../../uploads/announcement/" . $newName; // relative path for DB
            }
        }
        return null;
    }

    // Process image
    $newImage = handleFileUpload("announcement_image", "img", $uploadDir);
    if ($newImage) {
        $imagePath = $newImage;
    }

    // Process attachment
    $newAttachment = handleFileUpload("attachment", "att", $uploadDir);
    if ($newAttachment) {
        $attachmentPath = $newAttachment;
    }

    // === Archive logic ===
$isArchived = 0;
$archivedAt = null;

if ($status === "Archived") {
    $isArchived = 1;
    $archivedAt = date("Y-m-d H:i:s");
}

// === Update query ===
$stmt = $pdo->prepare("
    UPDATE announcements
    SET 
        announcement_title = ?,
        announcement_content = ?,
        announcement_category = ?,
        announcement_location = ?,
        status = ?,
        audience = ?,
        priority = ?,
        valid_until = ?,
        announcement_image = ?,
        attachment = ?,
        is_archived = ?,
        archived_at = ?,
        updated_at = NOW()
    WHERE announcement_id = ?
");

$stmt->execute([
    $title,
    $content,
    $category,
    $location,
    $status,
    $audience,
    $priority,
    $valid_until ?: null,
    $imagePath,
    $attachmentPath,
    $isArchived,
    $archivedAt,
    $id
]);

    echo json_encode(["success" => true, "message" => "Announcement updated successfully!"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
