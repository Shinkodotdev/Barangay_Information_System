<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../config/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ContactController
{
    public function sendMessage($data)
    {
        global $pdo;

        $name = htmlspecialchars(trim($data['name']));
        $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
        $message = htmlspecialchars(trim($data['message']));

        if (empty($name) || empty($email) || empty($message)) {
            $this->alert('error', 'All Fields Required', 'Please fill out all fields before sending.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->alert('error', 'Invalid Email', 'Please enter a valid email address.');
            return;
        }

        try {
            // ✅ Save inquiry to database
            $stmt = $pdo->prepare("
                INSERT INTO inquiries (inquiries_name, inquiries_email, inquiries_message)
                VALUES (:name, :email, :message)
            ");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':message' => $message
            ]);

            // ✅ Send email
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'poblacionsur648@gmail.com';
            $mail->Password   = 'rutp czsu frkt vrhz';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('poblacionsur648@gmail.com', 'Barangay Poblacion Sur');
            $mail->addAddress('galvezcrizelvalenzuela13@gmail.com', 'Barangay Information System Admin');
            $mail->addAddress('poblacionsur648@gmail.com', 'Barangay Information System');
            $mail->addReplyTo($email, $name);
            $mail->addCC($email, $name);

            $mail->AddEmbeddedImage(__DIR__ . "/../../frontend/assets/images/Logo.jpg", "logo", "Logo.jpg");

            $mail->isHTML(true);
            $mail->Subject = "New Contact Message from $name";
            $mail->Body = "
            <div style='font-family: Arial, sans-serif; background:#f5f7fa; padding:20px;'>
                <div style='max-width:600px; margin:auto; background:#fff; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); overflow:hidden;'>
                    <div style='background:#2563eb; color:#fff; text-align:center; padding:20px;'>
                        <img src='cid:logo' alt='Barangay Logo' style='width:80px; height:80px; border-radius:50%; margin-bottom:10px;'>
                        <h2 style='margin:0;'>Barangay Information System</h2>
                    </div>
                    <div style='padding:30px; color:#333; line-height:1.6;'>
                        <h3 style='color:#2563eb;'>New Message Received</h3>
                        <p><b>Name:</b> $name</p>
                        <p><b>Email:</b> $email</p>
                        <p><b>Message:</b><br>$message</p>
                        <hr style='margin:20px 0;'>
                        <p style='font-size:14px; color:#555;'>This message was sent from the Barangay Contact Form.</p>
                    </div>
                    <div style='background:#f1f5f9; padding:15px; text-align:center; font-size:12px; color:#777;'>
                        &copy; " . date("Y") . " Barangay Poblacion Sur. All rights reserved.
                    </div>
                </div>
            </div>";

            $mail->AltBody = "New Message from $name ($email): $message";
            $mail->send();

            $this->alert('success', 'Message Sent!', 'Thank you for contacting us. A copy has been sent to your email.', '../../frontend/pages/landing-page/Contact.php');
        } catch (Exception $e) {
            $this->alert('error', 'Failed to Send', 'Message could not be sent. Please try again.');
        } catch (PDOException $e) {
            $this->alert('error', 'Database Error', 'Unable to save your message. Please try again later.');
        }
    }

    private function alert($icon, $title, $text, $redirect = null)
    {
        $redirectJs = $redirect ? "window.location.href='$redirect';" : "window.history.back();";
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '$icon',
                    title: '$title',
                    text: '$text',
                    confirmButtonColor: '#2563eb'
                }).then(() => { $redirectJs });
            });
            </script>";
        exit;
    }
}
