<?php
require_once __DIR__ . "/../controllers/ContactController.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = new ContactController();
    $contact->sendMessage($_POST);
} else {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Access',
                text: 'Please use the contact form to send a message.',
                confirmButtonColor: '#2563eb'
            }).then(() => { window.location.href='../../frontend/pages/landing-page/Contact.php'; });
        });
    </script>";
    exit;
}
