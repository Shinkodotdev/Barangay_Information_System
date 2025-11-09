<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

function getPost($key, $default = null) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
            throw new Exception("Invalid user ID.");
        }
        $user_id = (int)$_POST['user_id'];

        $pdo->beginTransaction();

        // --- File uploads (only set paths if upload succeeded) ---
        $photoPath = null;
        if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "../../uploads/profile/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $fileName = time() . "_" . basename($_FILES['photo']['name']);
            $targetFile = $targetDir . $fileName;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                $photoPath = "../uploads/profile/" . $fileName;
            } else {
                throw new Exception("Failed to upload profile photo.");
            }
        }

        $baseDir = "../..//uploads/ids/";
        $frontIDPath = null;
        $backIDPath = null;
        $selfieIDPath = null;

        // front
        $frontDir = $baseDir . "front/";
        if (!is_dir($frontDir)) mkdir($frontDir, 0777, true);
        if (!empty($_FILES['front_valid_id_path']['name']) && $_FILES['front_valid_id_path']['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . "_front_" . basename($_FILES['front_valid_id_path']['name']);
            if (move_uploaded_file($_FILES['front_valid_id_path']['tmp_name'], $frontDir . $fileName)) {
                $frontIDPath = "../uploads/ids/front/" . $fileName;
            }
        }

        // back
        $backDir = $baseDir . "back/";
        if (!is_dir($backDir)) mkdir($backDir, 0777, true);
        if (!empty($_FILES['back_valid_id_path']['name']) && $_FILES['back_valid_id_path']['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . "_back_" . basename($_FILES['back_valid_id_path']['name']);
            if (move_uploaded_file($_FILES['back_valid_id_path']['tmp_name'], $backDir . $fileName)) {
                $backIDPath = "../uploads/ids/back/" . $fileName;
            }
        }

        // selfie
        $selfieDir = $baseDir . "selfie/";
        if (!is_dir($selfieDir)) mkdir($selfieDir, 0777, true);
        if (!empty($_FILES['selfie_with_id']['name']) && $_FILES['selfie_with_id']['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . "_selfie_" . basename($_FILES['selfie_with_id']['name']);
            if (move_uploaded_file($_FILES['selfie_with_id']['tmp_name'], $selfieDir . $fileName)) {
                $selfieIDPath = "../uploads/ids/selfie/" . $fileName;
            }
        }

        // income proof
        $incomeProofPath = null;
        $incomeProofDir = "../../uploads/income/";
        if (!is_dir($incomeProofDir)) mkdir($incomeProofDir, 0777, true);
        if (!empty($_FILES['income_proof']['name']) && $_FILES['income_proof']['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . "_income_" . basename($_FILES['income_proof']['name']);
            if (move_uploaded_file($_FILES['income_proof']['tmp_name'], $incomeProofDir . $fileName)) {
                $incomeProofPath = "../uploads/income/" . $fileName;
            }
        }

        // --- 1) Update users table (NOTE: column is `status` not `user_status`) ---
        $sqlUsers = "UPDATE users 
                     SET email = :email, 
                         role = :role, 
                         status = :status,
                         updated_at = NOW() 
                     WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sqlUsers);
        $stmt->execute([
            ':email' => getPost('email', ''),
            ':role' => getPost('role', ''),
            ':status' => getPost('status', ''),
            ':user_id' => $user_id
        ]);
        // Optionally inspect: $stmt->rowCount()

        // --- 1.1 officials table ---
        $roleLower = strtolower(getPost('role', ''));
        if ($roleLower === 'official' || $roleLower === 'officials' || $roleLower === 'official ') {
            $position = getPost('position', null);
            if ($position) {
                $stmt = $pdo->prepare("
                    INSERT INTO officials (user_id, position, updated_at)
                    VALUES (:user_id, :position, NOW())
                    ON DUPLICATE KEY UPDATE 
                        position = VALUES(position), 
                        updated_at = NOW()
                ");
                $stmt->execute([':user_id' => $user_id, ':position' => $position]);
            }
        } else {
            $pdo->prepare("DELETE FROM officials WHERE user_id = ?")->execute([$user_id]);
        }

        // --- 2) user_details ---
        $sqlDetails = "UPDATE user_details SET
            f_name = :f_name,
            m_name = :m_name,
            l_name = :l_name,
            ext_name = :ext_name,
            gender = :gender,
            contact_no = :contact_no,
            civil_status = :civil_status,
            occupation = :occupation,
            nationality = :nationality,
            voter_status = :voter_status,
            pwd_status = :pwd_status,
            senior_citizen_status = :senior_citizen_status,
            religion = :religion,
            blood_type = :blood_type,
            educational_attainment = :educational_attainment,
            photo = COALESCE(:photo, photo),
            updated_at = NOW()
            WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sqlDetails);
        $stmt->execute([
            ':f_name' => getPost('f_name', ''),
            ':m_name' => getPost('m_name', ''),
            ':l_name' => getPost('l_name', ''),
            ':ext_name' => getPost('ext_name', ''),
            ':gender' => getPost('gender', ''),
            ':contact_no' => getPost('contact_no', ''),
            ':civil_status' => getPost('civil_status', ''),
            ':occupation' => getPost('occupation', ''),
            ':nationality' => getPost('nationality', ''),
            ':voter_status' => getPost('voter_status', ''),
            ':pwd_status' => getPost('pwd_status', ''),
            ':senior_citizen_status' => getPost('senior_citizen_status', ''),
            ':religion' => getPost('religion', ''),
            ':blood_type' => getPost('blood_type', ''),
            ':educational_attainment' => getPost('educational_attainment', ''),
            ':photo' => $photoPath ?: null,
            ':user_id' => $user_id
        ]);

        // --- 3) user_birthdates ---
        $birthDate = getPost('birth_date', null);
        if ($birthDate === '') $birthDate = null;
        $stmt = $pdo->prepare("UPDATE user_birthdates 
                     SET birth_date = :birth_date, birth_place = :birth_place, updated_at = NOW()
                     WHERE user_id = :user_id");
        $stmt->execute([
            ':birth_date' => $birthDate,
            ':birth_place' => getPost('birth_place', null),
            ':user_id' => $user_id
        ]);

        // --- 4) user_residency ---
        $stmt = $pdo->prepare("UPDATE user_residency SET
            house_no = :house_no,
            purok = :purok,
            barangay = :barangay,
            municipality = :municipality,
            province = :province,
            updated_at = NOW()
            WHERE user_id = :user_id");
        $stmt->execute([
            ':house_no' => getPost('house_no', ''),
            ':purok' => getPost('purok', ''),
            ':barangay' => getPost('barangay', ''),
            ':municipality' => getPost('municipality', ''),
            ':province' => getPost('province', ''),
            ':user_id' => $user_id
        ]);

        // --- 5) user_family_info ---
        $stmt = $pdo->prepare("UPDATE user_family_info SET
            fathers_name = :fathers_name,
            fathers_birthplace = :fathers_birthplace,
            mothers_name = :mothers_name,
            mothers_birthplace = :mothers_birthplace,
            spouse_name = :spouse_name,
            num_dependents = :num_dependents,
            contact_person = :contact_person,
            emergency_contact_no = :emergency_contact_no,
            updated_at = NOW()
            WHERE user_id = :user_id");
        $stmt->execute([
            ':fathers_name' => getPost('fathers_name', ''),
            ':fathers_birthplace' => getPost('fathers_birthplace', ''),
            ':mothers_name' => getPost('mothers_name', ''),
            ':mothers_birthplace' => getPost('mothers_birthplace', ''),
            ':spouse_name' => getPost('spouse_name', ''),
            ':num_dependents' => getPost('num_dependents', 0),
            ':contact_person' => getPost('contact_person', ''),
            ':emergency_contact_no' => getPost('emergency_contact_no', ''),
            ':user_id' => $user_id
        ]);

        // --- 6) user_health_info ---
        $last_check = getPost('last_medical_checkup', null);
        if ($last_check === '') $last_check = null;
        $stmt = $pdo->prepare("UPDATE user_health_info SET
            health_condition = :health_condition,
            common_health_issue = :common_health_issue,
            vaccination_status = :vaccination_status,
            height_cm = :height_cm,
            weight_kg = :weight_kg,
            last_medical_checkup = :last_medical_checkup,
            health_remarks = :health_remarks,
            updated_at = NOW()
            WHERE user_id = :user_id");
        $stmt->execute([
            ':health_condition' => getPost('health_condition', ''),
            ':common_health_issue' => getPost('common_health_issue', ''),
            ':vaccination_status' => getPost('vaccination_status', ''),
            ':height_cm' => getPost('height_cm', null),
            ':weight_kg' => getPost('weight_kg', null),
            ':last_medical_checkup' => $last_check,
            ':health_remarks' => getPost('health_remarks', ''),
            ':user_id' => $user_id
        ]);

        // --- 7) user_identity_docs ---
        $stmt = $pdo->prepare("UPDATE user_identity_docs SET
            id_type = :id_type,
            front_valid_id_path = COALESCE(:front_valid_id_path, front_valid_id_path),
            back_valid_id_path = COALESCE(:back_valid_id_path, back_valid_id_path),
            selfie_with_id = COALESCE(:selfie_with_id, selfie_with_id),
            updated_at = NOW()
            WHERE user_id = :user_id");
        $stmt->execute([
            ':id_type' => getPost('id_type', ''),
            ':front_valid_id_path' => $frontIDPath ?: null,
            ':back_valid_id_path' => $backIDPath ?: null,
            ':selfie_with_id' => $selfieIDPath ?: null,
            ':user_id' => $user_id
        ]);

        // --- 8) user_income_info ---
        $stmt = $pdo->prepare("UPDATE user_income_info SET
            monthly_income = :monthly_income,
            income_source = :income_source,
            household_head_occupation = :household_head_occupation,
            household_members = :household_members,
            additional_income_sources = :additional_income_sources,
            income_proof = COALESCE(:income_proof, income_proof),
            updated_at = NOW()
            WHERE user_id = :user_id");
        $stmt->execute([
            ':monthly_income' => getPost('monthly_income', ''),
            ':income_source' => getPost('income_source', ''),
            ':household_head_occupation' => getPost('household_head_occupation', ''),
            ':household_members' => getPost('household_members', null),
            ':additional_income_sources' => getPost('additional_income_sources', ''),
            ':income_proof' => $incomeProofPath ?: null,
            ':user_id' => $user_id
        ]);

        $pdo->commit();

        echo "<!DOCTYPE html>
<html>
<head>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head>
<body>
<script>
Swal.fire({
    icon: 'success',
    title: 'Profile Updated!',
    text: 'User information successfully saved.',
    confirmButtonColor: '#4F46E5'
}).then(() => {
    window.location.href = '../../frontend/pages/admin/manage_resident.php';
});
</script>
</body>
</html>";
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Update Failed',
            text: '" . addslashes($e->getMessage()) . "',
            confirmButtonColor: '#EF4444'
        }).then(() => {
            window.history.back();
        });
    </script>";
        exit();
    }
}
?>
