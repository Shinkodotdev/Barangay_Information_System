    <?php
    session_start();
    include('../../../backend/config/db.php');
    require_once('../../../backend/models/IncidentRepository.php');

    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Official') {
        header('Location: ../../../index.php?error=Please log in as official');
        exit();
    }

    $user_id = $_SESSION['user_id']; 
    $repo = new IncidentRepository($pdo);
    $archived = isset($_GET['archived']) && $_GET['archived'] == 1 ? 1 : 0;
    $limit = 10;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;
    $totalIncidents = $repo->countIncidentsByReporter($user_id, $archived);
    $totalPages = ceil($totalIncidents / $limit);
    $categories = $repo->getCategories($archived);
    $types = $repo->getTypes($archived);
    $incidents = $repo->getIncidentsByReporter($user_id, $archived, $limit, $offset);
    $incidentIds = array_column($incidents, 'incident_id');
    $incidentPersons = $repo->getPersonsInvolved($incidentIds);

    include('official-head.php');
    ?>

<body class="bg-gray-100 font-sans">
    <?php include('../../components/DashNav.php'); ?>
    <main class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-indigo-700 mb-6 text-center">My Incident Reports</h1>

        <div class="flex justify-end mb-6">
            <button id="createIncidentBtn"
                class="bg-indigo-600 text-white px-5 py-2 rounded-lg shadow-md hover:bg-indigo-700 transition flex items-center gap-2"
                onclick="openCreateIncidentModal()">
                <i class="fa-solid fa-plus"></i> Create Incident Report
            </button>
        </div>
        <?php include('../../components/incident_report/incident_table.php'); ?>
    </main>

    <?php
    include('../../../backend/actions/incident_report/view.php');
    include('../../../backend/actions/incident_report/update_modal.php');
    include('../../../backend/actions/incident_report/create_modal.php');
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="../../../backend/actions/incident_report/download.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../assets/js/incident_report.js"></script>
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
