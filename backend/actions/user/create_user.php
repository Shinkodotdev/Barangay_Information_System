<?php
session_start();
require_once "../../config/db.php";
require_once "../../../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function clean($v)
{
    return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
}

function generateRandomPassword($len = 8)
{
    return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$"), 0, $len);
}

try {
    // ✅ Begin transaction
    $pdo->beginTransaction();

    // ✅ Generate random password
    $plainPassword = generateRandomPassword();
    $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

    // ✅ Insert user account
    $email = clean($_POST['email']);
    $role = clean($_POST['role'] ?? 'Resident');
    $status = 'Pending';

    // ✅ Check for duplicate email first
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO users (email, password, role, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$email, $hashedPassword, $role, $status]);
    $user_id = $pdo->lastInsertId();

    // ✅ Generate token for verification
    $token = bin2hex(random_bytes(16));
    $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
    $stmt = $pdo->prepare("INSERT INTO verifications (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $token, $expires_at]);

    // ✅ Upload profile photo
    $photo_path = '';
    if (!empty($_FILES['photo']['name'])) {
        $upload_dir = "../../../uploads/profile/";
        if (!file_exists($upload_dir))
            mkdir($upload_dir, 0777, true);
        $filename = time() . "_" . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $filename);
        $photo_path = "../uploads/profile/" . $filename;
    }

    // ✅ user_details
    $stmt = $pdo->prepare("
        INSERT INTO user_details (
            user_id, f_name, m_name, l_name, ext_name,
            gender, contact_no, civil_status, occupation,
            nationality, religion, blood_type, voter_status,
            pwd_status, senior_citizen_status, educational_attainment, photo
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $user_id,
        clean($_POST['f_name']),
        clean($_POST['m_name']),
        clean($_POST['l_name']),
        clean($_POST['ext_name']),
        clean($_POST['gender']),
        '+63' . clean($_POST['contact_no']),
        clean($_POST['civil_status']),
        clean($_POST['occupation']),
        clean($_POST['nationality']),
        clean($_POST['religion']),
        clean($_POST['blood_type']),
        clean($_POST['voter_status']),
        clean($_POST['pwd_status']),
        clean($_POST['senior_citizen_status']),
        clean($_POST['educational_attainment']),
        $photo_path
    ]);

    // ✅ user_birthdates
    $stmt = $pdo->prepare("INSERT INTO user_birthdates (user_id, birth_date, birth_place) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, clean($_POST['birth_date']), clean($_POST['birth_place'])]);

    // ✅ user_residency
    $stmt = $pdo->prepare("
        INSERT INTO user_residency (user_id, house_no, purok, barangay, municipality, province, years_residency, household_head, house_type, ownership_status, previous_address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $user_id,
        clean($_POST['house_no']),
        clean($_POST['purok']),
        clean($_POST['barangay']),
        clean($_POST['municipality']),
        clean($_POST['province']),
        clean($_POST['years_residency']),
        clean($_POST['household_head']),
        clean($_POST['house_type']),
        clean($_POST['ownership_status']),
        clean($_POST['previous_address'])
    ]);

    // ✅ user_family_info
    $stmt = $pdo->prepare("
        INSERT INTO user_family_info (user_id, fathers_name, fathers_birthplace, mothers_name, mothers_birthplace, spouse_name, num_dependents, contact_person, emergency_contact_no)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $user_id,
        clean($_POST['fathers_name']),
        clean($_POST['fathers_birthplace']),
        clean($_POST['mothers_name']),
        clean($_POST['mothers_birthplace']),
        clean($_POST['spouse_name']),
        clean($_POST['num_dependents']),
        clean($_POST['contact_person']),
        '+63' . clean($_POST['emergency_contact_no'])
    ]);

    // ✅ user_health_info
    $stmt = $pdo->prepare("
        INSERT INTO user_health_info (user_id, health_condition, common_health_issue, vaccination_status, height_cm, weight_kg, last_medical_checkup, health_remarks)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $user_id,
        clean($_POST['health_condition']),
        clean($_POST['common_health_issue']),
        clean($_POST['vaccination_status']),
        clean($_POST['height_cm']),
        clean($_POST['weight_kg']),
        clean($_POST['last_medical_checkup']),
        clean($_POST['health_remarks'])
    ]);

    // ✅ user_income_info
    $income_proof = '';
    if (!empty($_FILES['income_proof']['name'])) {
        $dir = "../../../uploads/income/";
        if (!file_exists($dir))
            mkdir($dir, 0777, true);
        $fname = time() . "_" . basename($_FILES['income_proof']['name']);
        move_uploaded_file($_FILES['income_proof']['tmp_name'], $dir . $fname);
        $income_proof = "../uploads/income/" . $fname;
    }

    $stmt = $pdo->prepare("
        INSERT INTO user_income_info (user_id, monthly_income, income_source, household_head_occupation, household_members, additional_income_sources, income_proof)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $user_id,
        clean($_POST['monthly_income']),
        clean($_POST['income_source']),
        clean($_POST['household_head_occupation']),
        clean($_POST['household_members']),
        clean($_POST['additional_income_sources']),
        $income_proof
    ]);

    // ✅ user_identity_docs
    $uploadDir = "../../../uploads/ids/";
    if (!file_exists($uploadDir))
        mkdir($uploadDir, 0777, true);
    $front = $back = $selfie = "";

    if (!empty($_FILES['front_valid_id_path']['name'])) {
        $frontName = time() . "_front_" . basename($_FILES['front_valid_id_path']['name']);
        move_uploaded_file($_FILES['front_valid_id_path']['tmp_name'], $uploadDir . $frontName);
        $front = "../uploads/ids/" . $frontName;
    }

    if (!empty($_FILES['back_valid_id_path']['name'])) {
        $backName = time() . "_back_" . basename($_FILES['back_valid_id_path']['name']);
        move_uploaded_file($_FILES['back_valid_id_path']['tmp_name'], $uploadDir . $backName);
        $back = "../uploads/ids/" . $backName;
    }

    if (!empty($_FILES['selfie_with_id']['name'])) {
        $selfieName = time() . "_selfie_" . basename($_FILES['selfie_with_id']['name']);
        move_uploaded_file($_FILES['selfie_with_id']['tmp_name'], $uploadDir . $selfieName);
        $selfie = "../uploads/ids/" . $selfieName;
    }

    $stmt = $pdo->prepare("
        INSERT INTO user_identity_docs (user_id, id_type, front_valid_id_path, back_valid_id_path, selfie_with_id)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, clean($_POST['id_type']), $front, $back, $selfie]);

    // ✅ Commit transaction
    $pdo->commit();

    // =============================
    // 📧 Send Email Verification
    // =============================
    $verifyLink = "http://localhost/BARANGAY_INFORMATION_SYSTEM/backend/actions/user/verify_create.php?token=" . $token;

    $mail = new PHPMailer(true);
    $f_name = clean($_POST['f_name']);
    $m_name = clean($_POST['m_name']);
    $l_name = clean($_POST['l_name']);
    $ext_name = clean($_POST['ext_name']);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'poblacionsur648@gmail.com';
    $mail->Password = 'rutp czsu frkt vrhz'; // ⚠️ App password, not your real Gmail password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('poblacionsur648@gmail.com', 'Barangay Poblacion Sur System');
    $mail->addAddress($email, "$f_name $l_name");
    $mail->isHTML(true);
    $mail->Subject = "Verify Your Account";

    $mail->AddEmbeddedImage(__DIR__ . "/../../../frontend/assets/images/Logo.jpg", "logo", "Logo.webp");
    $mail->Body = "
        <div style='font-family: Arial, sans-serif; line-height:1.6; color:#333;'>
            <div style='text-align:center;'>
                <img src='cid:logo' alt='Barangay Logo' style='width:100px; height:100px; border-radius:50%;'>
            </div>
            <h2 style='color:#2563eb; text-align:center;'>Barangay Information System</h2>
            <p>Hello <b>{$f_name} {$m_name} {$l_name} {$ext_name}</b>,</p>
            <p>Your account has been created successfully.</p>
            <p><b>Temporary Password:</b> {$plainPassword}</p>
            <p>Please verify your email address by clicking below:</p>
            <div style='text-align:center; margin:20px 0;'>
                <a href='$verifyLink' style='background:#2563eb; color:#fff; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:bold;'>Verify My Account</a>
            </div>
            <p>If the button doesn’t work, copy and paste this link:</p>
            <p style='word-break:break-all;'><a href='$verifyLink'>$verifyLink</a></p>
            <p style='color:#666; font-size:14px;'>⚠ This link will expire in <b>24 hours</b>.</p>
        </div>
    ";
    $mail->AltBody = "Verify your account: $verifyLink";
    $mail->send();

    echo json_encode(['status' => 'success', 'message' => 'Resident created and email sent for verification.']);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();

    if ($e->getCode() == 23000) { // duplicate entry
        echo json_encode(['status' => 'error', 'message' => 'This email is already registered.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

?>