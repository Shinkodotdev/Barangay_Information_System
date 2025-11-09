<?php
session_start();
include('../config/db.php');
require_once "../auth/auth_check.php";

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../frontend/pages/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 📂 Upload helper
function uploadFile($fileField, $uploadDir = "../uploads/")
{
    if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
        return null; // keep old file if no new upload
    }

    $fileName = time() . "_" . basename($_FILES[$fileField]['name']);
    $targetPath = $uploadDir . $fileName;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($_FILES[$fileField]['tmp_name'], $targetPath)) {
        return $targetPath;
    }

    return null;
}

try {
    $pdo->beginTransaction();

    // ✅ Update USER DETAILS
    $stmt = $pdo->prepare("UPDATE user_details 
        SET f_name = ?, m_name = ?, l_name = ?, ext_name = ?, gender = ?, contact_no = ?, civil_status = ?, occupation = ?
        WHERE user_id = ?");
    $stmt->execute([
        $_POST['f_name'] ?? null,
        $_POST['m_name'] ?? null,
        $_POST['l_name'] ?? null,
        $_POST['ext_name'] ?? null,
        $_POST['gender'] ?? null,
        $_POST['contact_no'] ?? null,
        $_POST['civil_status'] ?? null,
        $_POST['occupation'] ?? null,
        $user_id
    ]);

    // ✅ Update BIRTH INFO
    $stmt = $pdo->prepare("UPDATE user_birthdates 
        SET birth_date = ?, birth_place = ?
        WHERE user_id = ?");
    $stmt->execute([
        $_POST['birthdate'] ?? null,
        $_POST['birth_place'] ?? null,
        $user_id
    ]);

    // ✅ Update RESIDENCY
    $stmt = $pdo->prepare("UPDATE user_residency 
        SET house_no = ?, purok = ?, barangay = ?, municipality = ?, province = ?
        WHERE user_id = ?");
    $stmt->execute([
        $_POST['house_no'] ?? null,
        $_POST['purok'] ?? null,
        "POBLACION SUR",
        "TALAVERA",
        "NUEVA ECIJA",
        $user_id
    ]);

    // ✅ Update FAMILY INFO
    $stmt = $pdo->prepare("UPDATE user_family_info 
        SET fathers_name = ?, mothers_name = ?, spouse_name = ?
        WHERE user_id = ?");
    $stmt->execute([
        $_POST['fathers_name'] ?? null,
        $_POST['mothers_name'] ?? null,
        $_POST['spouse_name'] ?? null,
        $user_id
    ]);

    // ✅ Update HEALTH INFO
    $stmt = $pdo->prepare("UPDATE user_health_info 
        SET health_condition = ?, common_health_issue = ?
        WHERE user_id = ?");
    $stmt->execute([
        $_POST['health_condition'] ?? null,
        $_POST['common_health_issue'] ?? null,
        $user_id
    ]);

    // ✅ Fetch existing income proof
    $stmt = $pdo->prepare("SELECT income_proof FROM user_income_info WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $existingIncome = $stmt->fetch(PDO::FETCH_ASSOC);

    // ✅ Update INCOME INFO (keep old file if no new upload)
    $income_proof = uploadFile("income_proof") ?: ($existingIncome['income_proof'] ?? null);

    $stmt = $pdo->prepare("UPDATE user_income_info 
        SET monthly_income = ?, income_source = ?, income_proof = ?
        WHERE user_id = ?");
    $stmt->execute([
        $_POST['monthly_income'] ?? null,
        $_POST['income_source'] ?? null,
        $income_proof,
        $user_id
    ]);

    // ✅ Fetch existing identity docs
    $stmt = $pdo->prepare("SELECT front_valid_id_path, back_valid_id_path, selfie_with_id FROM user_identity_docs WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $existingIdentity = $stmt->fetch(PDO::FETCH_ASSOC);

    // ✅ Update IDENTITY DOCS (keep old files if no new upload)
    $front_id = uploadFile("front_valid_id_path") ?: ($existingIdentity['front_valid_id_path'] ?? null);
    $back_id  = uploadFile("back_valid_id_path")  ?: ($existingIdentity['back_valid_id_path'] ?? null);
    $selfie   = uploadFile("selfie_with_id")      ?: ($existingIdentity['selfie_with_id'] ?? null);

    $stmt = $pdo->prepare("UPDATE user_identity_docs 
        SET front_valid_id_path = ?, 
            back_valid_id_path = ?, 
            selfie_with_id = ?
        WHERE user_id = ?");
    $stmt->execute([
        $front_id,
        $back_id,
        $selfie,
        $user_id
    ]);

    $pdo->commit();

    // ✅ SweetAlert success + redirect
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Profile Updated',
            text: 'Your profile has been updated successfully.',
            confirmButtonColor: '#2563eb'
        }).then(() => {
            window.location.href='../../../frontend/pages/user/profile.php';
        });
    </script>";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Update Failed',
            text: '" . addslashes($e->getMessage()) . "',
            confirmButtonColor: '#d33'
        }).then(() => {
            window.history.back();
        });
    </script>";
}
