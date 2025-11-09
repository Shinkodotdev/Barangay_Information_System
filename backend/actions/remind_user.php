<?php
require_once __DIR__ . "/../config/db.php";

require __DIR__ . '/../../vendor/autoload.php'; // Adjust path if needed
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    try {
        // 1. Get user details
        $stmt = $pdo->prepare("
            SELECT u.user_id, ud.f_name, ud.m_name, ud.l_name, ud.ext_name
            FROM users u
            JOIN user_details ud ON u.user_id = ud.user_id
            WHERE u.email = ?
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("User not found.");
        }

        $fullName = trim($user['f_name'] . ' ' . $user['m_name'] . ' ' . $user['l_name'] . ' ' . $user['ext_name']);

        // 2. Check last reminder (within 3 days)
        $stmt = $pdo->prepare("
            SELECT sent_at 
            FROM reminders 
            WHERE user_id = ? AND reminder_type = 'Complete Profile' 
            ORDER BY sent_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$user['user_id']]);
        $lastReminder = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($lastReminder && strtotime($lastReminder['sent_at']) > strtotime('-3 days')) {
            $nextAllowed = date("F j, Y g:ia", strtotime($lastReminder['sent_at'] . " +3 days"));
            echo json_encode([
                "success" => false,
                "message" => "Reminder already sent. Next allowed on: $nextAllowed"
            ]);
            exit;
        }

        // 3. Log reminder
        $stmt = $pdo->prepare("INSERT INTO reminders (user_id, reminder_type, sent_at) VALUES (?, 'Complete Profile', NOW())");
        $stmt->execute([$user['user_id']]);
        $reminder_id = $pdo->lastInsertId();

        // 4. Setup PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'poblacionsur648@gmail.com';
        $mail->Password   = 'rutp czsu frkt vrhz'; // ⚠️ Use Gmail app password only
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('poblacionsur648@gmail.com', 'Barangay Poblacion Sur System');
        $mail->addAddress($email, $fullName);

        // 5. Email Content
        $mail->isHTML(true);
        $mail->Subject = "Reminder: Please Complete Your Personal Information";
        $mail->AddEmbeddedImage(__DIR__ . "/../../frontend/assets/images/Logo.jpg", "logo", "Logo.webp");

        $link = "http://localhost/BARANGAY_INFORMATION_SYSTEM/frontend/pages/login.php";

        $mail->Body = "
            <div style='font-family: Arial, sans-serif; line-height:1.6; color:#333;'>
                <div style='text-align:center;'>
                    <img src='cid:logo' alt='Barangay Logo' style='width:100px; height:100px; border-radius:50%;'>
                </div>
                <h2 style='color:#2563eb; text-align:center;'>Barangay Information System</h2>
                <p>Hello <b>{$fullName}</b>,</p>
                <p>We noticed that your account is verified but your personal information is not yet complete.</p>
                <p>Please log in to your account to update your profile by clicking the button below:</p>

                <div style='text-align:center; margin:20px 0;'>
                    <a href='$link' 
                        style='background:#2563eb; color:#fff; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block;'>
                        Log In Now
                    </a>
                </div>

                <p style='color:#666; font-size:14px;'>⚠ Keeping your details updated ensures smooth communication with the barangay.</p>
            </div>
        ";

        $mail->AltBody = "Hello $fullName, please complete your information here: $link";

        $mail->send();

        echo json_encode([
            "success" => true,
            "reminder_id" => $reminder_id,
            "message" => "Reminder email sent to $email."
        ]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Reminder failed: " . $e->getMessage()]);
    }
}
?>