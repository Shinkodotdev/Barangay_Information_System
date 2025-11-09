    <?php
        session_start();
        $navbarLinks = [
            ['path' => 'index.php', 'label' => 'Home'],
            ['path' => 'frontend/pages/landing-page/About.php', 'label' => 'About us'],
            ['path' => 'frontend/pages/landing-page/Announcements.php', 'label' => 'Announcements'],
            ['path' => 'frontend/pages/landing-page/Events.php', 'label' => 'Events'],
            ['path' => 'frontend/pages/landing-page/Contact.php', 'label' => 'Contact us'],
        ];
        $current_page = basename($_SERVER['PHP_SELF']);
        include('./backend/config/db.php');
    ?>
<!DOCTYPE html>
<html lang="en">
<?php include './frontend/components/Head.php'; ?>
<body class="bg-gray-100 text-gray-800">
    <!-- Navbar -->
    <?php include('./frontend/components/landing-page/header.php'); ?>
    <!-- Main -->
    <main>
        <?php include('./frontend/components/landing-page/hero.php'); ?>
        <!-- Quick Info Section -->
        <section id="info-section" class="py-16">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-center mb-12 text-indigo-700">Quick Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                    <?php
                    include('./frontend/components/landing-page/services_documents.php');
                    include('./frontend/components/landing-page/emergency_contact.php');
                    include('./frontend/components/landing-page/incident_report.php'); ?>
                </div>
            </div>
        </section>
        <?php include './frontend/components/Footer.php'; ?>
    </main>
    <!-- Emergency Contacts Modal -->
    <?php include('./frontend/assets/modals/emergency_contact_modal.php'); ?>
    <!-- Incident Report Modal -->
    <?php include('./frontend/assets/modals/incident_report_modal.php'); ?>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="./frontend/assets/js/index.js"></script>
</body>
</html>