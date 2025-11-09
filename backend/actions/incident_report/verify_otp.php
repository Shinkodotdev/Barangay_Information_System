<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../../config/db.php';
date_default_timezone_set('Asia/Manila');

// ✅ Ensure session exists
if (!isset($_SESSION['non_resident']['email'], $_SESSION['non_resident']['non_resident_id'])) {
    echo json_encode(["status" => "error", "message" => "No verification session found. Please request OTP again."]);
    exit;
}

$email = $_SESSION['non_resident']['email'];
$nonResidentId = $_SESSION['non_resident']['non_resident_id'];
$inputOtp = trim($_POST['otp'] ?? '');

if (empty($inputOtp)) {
    echo json_encode(["status" => "error", "message" => "Please enter your OTP."]);
    exit;
}

try {
    // ✅ Fetch the latest unverified OTP for this non-resident
    $stmt = $pdo->prepare("
        SELECT otp_id, otp_code, expires_at, verified
        FROM otp_verifications
        WHERE non_resident_id = ? AND verified = 0
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$nonResidentId]);
    $otpData = $stmt->fetch(PDO::FETCH_ASSOC);

    // ✅ No active OTP found
    if (!$otpData) {
        echo json_encode(["status" => "error", "message" => "No active OTP found. Please request a new one."]);
        exit;
    }

    // ✅ Check if expired
    $currentTime = time();
    $expiryTime = strtotime($otpData['expires_at']);

    if ($currentTime > $expiryTime) {
        // Expired → mark as invalid
        $stmt = $pdo->prepare("UPDATE otp_verifications SET verified = 2 WHERE otp_id = ?");
        $stmt->execute([$otpData['otp_id']]);

        echo json_encode(["status" => "error", "message" => "OTP expired. Please request a new one."]);
        exit;
    }

    // ✅ Check OTP match
    if ($inputOtp != $otpData['otp_code']) {
        echo json_encode(["status" => "error", "message" => "Invalid OTP."]);
        exit;
    }

    // ✅ OTP is valid and within time limit → mark verified
    $stmt = $pdo->prepare("UPDATE otp_verifications SET verified = 1 WHERE otp_id = ?");
    $stmt->execute([$otpData['otp_id']]);

    $_SESSION['otp_verified'] = true;

    echo json_encode(["status" => "success", "message" => "OTP verified successfully."]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
