<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Manila');

require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure session email exists
if (!isset($_SESSION['non_resident']['email'])) {
    echo json_encode(["status" => "error", "message" => "No session found. Please verify again."]);
    exit;
}

$email = $_SESSION['non_resident']['email'];

// Get non_resident_id
try {
    $stmt = $pdo->prepare("SELECT non_resident_id FROM non_residents WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        echo json_encode(["status" => "error", "message" => "Non-resident not found."]);
        exit;
    }
    $nonResidentId = $user['non_resident_id'];
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    exit;
}

// Check rate limit (5 mins)
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
        echo json_encode([
            "status" => "error",
            "message" => "⚠️ You can only request a new OTP once every 5 minutes. Please wait."
        ]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Database error (rate limit): " . $e->getMessage()]);
    exit;
}

// Generate new OTP
$otp = rand(100000, 999999);
$expiry = date('Y-m-d H:i:s', time() + 300);

try {
    $stmt = $pdo->prepare("
        INSERT INTO otp_verifications (non_resident_id, otp_code, expires_at, verified, created_at)
        VALUES (?, ?, ?, 0, ?)
    ");
    $stmt->execute([$nonResidentId, $otp, $expiry, date('Y-m-d H:i:s')]);

    // Send email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "poblacionsur648@gmail.com";
    $mail->Password = "rutp czsu frkt vrhz";
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    $mail->setFrom("poblacionsur648@gmail.com", "Barangay Incident Reporting");
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = "New OTP Verification Code";
    $mail->Body = "Your new OTP code is: <b>$otp</b><br><br>This code will expire in 5 minutes.";

    $mail->send();

    echo json_encode(["status" => "success", "message" => "A new OTP has been sent to $email"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Mailer Error: " . $e->getMessage()]);
}
