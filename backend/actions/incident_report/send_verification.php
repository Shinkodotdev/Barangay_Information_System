<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Manila');

require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../../vendor/autoload.php';
include '../../config/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Catch all errors
set_exception_handler(fn($e) => exit(json_encode(["status" => "error", "message" => "Server Exception: " . $e->getMessage()])));
set_error_handler(fn($errno, $errstr) => exit(json_encode(["status" => "error", "message" => "PHP Error: $errstr"])));

// ---------------------------
// Sanitize inputs
// ---------------------------
$f_name   = htmlspecialchars(trim($_POST['f_name'] ?? ''), ENT_QUOTES, 'UTF-8');
$m_name   = htmlspecialchars(trim($_POST['m_name'] ?? ''), ENT_QUOTES, 'UTF-8');
$l_name   = htmlspecialchars(trim($_POST['l_name'] ?? ''), ENT_QUOTES, 'UTF-8');
$ext_name = htmlspecialchars(trim($_POST['ext_name'] ?? ''), ENT_QUOTES, 'UTF-8');
$email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$contact  = htmlspecialchars(trim($_POST['contact_no'] ?? ''), ENT_QUOTES, 'UTF-8');
$address  = htmlspecialchars(trim($_POST['address'] ?? ''), ENT_QUOTES, 'UTF-8');

if (!$f_name || !$l_name || !$email) {
    exit(json_encode(["status" => "error", "message" => "Invalid input."]));
}

// ---------------------------
// Check if already registered
// ---------------------------
try {
    $stmt = $pdo->prepare("
        SELECT u.user_id 
        FROM users u
        INNER JOIN user_details ud ON u.user_id = ud.user_id
        WHERE ud.f_name = ? AND ud.m_name = ? AND ud.l_name = ? AND ud.ext_name = ?
    ");
    $stmt->execute([$f_name, $m_name, $l_name, $ext_name]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        exit(json_encode([
            "status" => "error",
            "message" => "⚠️ This name belongs to a registered resident. Please log in to continue."
        ]));
    }
} catch (Exception $e) {
    exit(json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]));
}

// ---------------------------
// Insert or get non-resident
// ---------------------------
try {
    // Check if non-resident already exists by email
    $stmt = $pdo->prepare("SELECT non_resident_id FROM non_residents WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $nonResident = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($nonResident) {
        $nonResidentId = $nonResident['non_resident_id'];
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO non_residents (f_name, m_name, l_name, ext_name, email, contact_no, address, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$f_name, $m_name, $l_name, $ext_name, $email, $contact, $address, date('Y-m-d H:i:s')]);
        $nonResidentId = $pdo->lastInsertId();
    }
} catch (Exception $e) {
    exit(json_encode(["status" => "error", "message" => "Database error (non-resident): " . $e->getMessage()]));
}

// ---------------------------
// OTP Rate Limit (3 mins)
// ---------------------------
try {
    $stmt = $pdo->prepare("
        SELECT created_at 
        FROM otp_verifications 
        WHERE non_resident_id = ? 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$nonResidentId]);
    $lastOtp = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lastOtp && (time() - strtotime($lastOtp['created_at'])) < 300) {
        exit(json_encode([
            "status" => "error",
            "message" => "⚠️ You can only request a new OTP once every 5 minutes. Please wait."
        ]));
    }
} catch (Exception $e) {
    exit(json_encode(["status" => "error", "message" => "Database error (OTP check): " . $e->getMessage()]));
}

// ---------------------------
// Generate and insert OTP
// ---------------------------
$otp = rand(100000, 999999);
$expiry = date('Y-m-d H:i:s', time() + 300); // 5 min expiry

try {
    $stmt = $pdo->prepare("
        INSERT INTO otp_verifications (non_resident_id, otp_code, expires_at, verified, created_at)
        VALUES (?, ?, ?, 0, ?)
    ");
    $stmt->execute([$nonResidentId, $otp, $expiry, date('Y-m-d H:i:s')]);

    // ✅ Store session for verification
    $_SESSION['non_resident'] = [
        'email' => $email,
        'non_resident_id' => $nonResidentId
    ];
} catch (Exception $e) {
    exit(json_encode(["status" => "error", "message" => "Database error (OTP insert): " . $e->getMessage()]));
}


// ---------------------------
// Send OTP via email
// ---------------------------
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = "smtp.gmail.com";
    $mail->SMTPAuth   = true;
    $mail->Username   = "poblacionsur648@gmail.com"; 
    $mail->Password   = "rutp czsu frkt vrhz"; 
    $mail->SMTPSecure = "tls";
    $mail->Port       = 587;

    $mail->setFrom("poblacionsur648@gmail.com", "Barangay Incident Reporting");
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Email Verification Code";
    $mail->Body    = "Your OTP verification code is: <b>$otp</b><br><br>This code will expire in 5 minutes.";

    $mail->send();

    echo json_encode(["status" => "success", "message" => "OTP sent to $email"]);
} catch (Exception $e) {
    exit(json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]));
}
