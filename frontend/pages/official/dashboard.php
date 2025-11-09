    <?php
    session_start();
    require_once "../../../backend/config/db.php";
    require_once "../../../backend/auth/login.php";
    require_once "../../../backend/auth/auth_check.php";
    require_once "../../../backend/models/Repository.php";

    redirectIfNotLoggedIn(['../login.php'], $pdo);
    $user_id = $_SESSION['user_id'];
    $name = $_SESSION['name'] ?? "Official";
    $role = $_SESSION['role'] ?? null;
    $status = $_SESSION['status'] ?? null;
    $allowedRoles = ['Official'];
    $allowedStatus = ['Approved'];
    if (!in_array($role, $allowedRoles) || !in_array($status, $allowedStatus)) {
        header("Location: ../../pages/unauthorized_page.php");
        exit;
    }

    $announcements = getOfficialAnnouncements($pdo);
    $events = getOfficialEvents($pdo);
    ?>
    <?php include('official-head.php'); ?>
<body class="bg-gray-100">
    <?php include('../../components/DashNav.php'); ?>
    <main class="pt-24 px-4 sm:px-6 lg:px-10 space-y-8">
        <?php
        //Announcement and Event Showcase
        include('../../components/announcement_event_showcase.php');
        //official Quick Action Cards    
        include('./official-includes/official_quick_action_cards.php');
        //Recent Document Table    
        include('./official-includes/recent_document_table.php');
        ?>
    </main>
    <?php
        //Document Request Modal 
        include('../../assets/modals/document_request_modal.php');
        include('../../assets/modals/official_incident_modal.php');
    ?>
    <script src="../../assets/js/officialDashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../assets/js/official_incident_report.js"></script>
    <?php if (isset($_GET['success'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?php echo htmlspecialchars($_GET['success']); ?>',
                confirmButtonColor: '#6366F1'
            });
        </script>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: '<?php echo htmlspecialchars($_GET['error']); ?>',
                confirmButtonColor: '#6366F1'
            });
        </script>
    <?php endif; ?>
</body>
</html>