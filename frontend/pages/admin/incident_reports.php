<?php
session_start();
include('../../../backend/config/db.php');
require_once('../../../backend/models/IncidentRepository.php');
// Initialize repository
$repo = new IncidentRepository($pdo);

$archived = isset($_GET['archived']) && $_GET['archived'] == 1 ? 1 : 0;

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get totals and charts
$totalIncidents = $repo->countIncidents($archived);
$totalPages = ceil($totalIncidents / $limit);
$categories = $repo->getCategories($archived);
$types = $repo->getTypes($archived);

// Get incidents and persons
$incidents = $repo->getIncidents($archived, $limit, $offset);
$incidentIds = array_column($incidents, 'incident_id');
$incidentPersons = $repo->getPersonsInvolved($incidentIds);

// Load components
include('admin-head.php');
?>

<body class="bg-gray-100 font-sans">
    <?php include('../../components/DashNav.php'); ?>
    <main class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-indigo-700 mb-6 text-center">Incident Reports</h1>
        <div class="flex justify-end mb-6">
            <button id="createIncidentBtn"
                class="bg-indigo-600 text-white px-5 py-2 rounded-lg shadow-md hover:bg-indigo-700 transition flex items-center gap-2"
                onclick="openCreateIncidentModal()">
                <i class="fa-solid fa-plus"></i> Create Incident Report
            </button>
        </div>
        <?php
        // Incident Charts
        include('../../components/incident_report/incident_charts.php');
        // Incident Reports Table
        include('../../components/incident_report/incident_table.php');
        ?>
    </main>
    <?php
    // View Modal
    include('../../../backend/actions/incident_report/view.php');
    // Update Modal
    include('../../../backend/actions/incident_report/update_modal.php');
    // Create Modal
    include('../../../backend/actions/incident_report/create_modal.php');
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="../../../backend/actions/incident_report/download.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../assets/js/incident_report.js"></script>
    <script>
        const categoryLabels = <?php echo json_encode(array_column($categories, 'category')); ?>;
        const categoryData = <?php echo json_encode(array_column($categories, 'total')); ?>;
        const typeLabels = <?php echo json_encode(array_column($types, 'type')); ?>;
        const typeData = <?php echo json_encode(array_column($types, 'total')); ?>;

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 20, padding: 15 } } },
            layout: { padding: 10 }
        };

        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: { labels: categoryLabels, datasets: [{ data: categoryData, backgroundColor: ['#6366F1', '#10B981', '#F59E0B', '#EF4444', '#3B82F6', '#8B5CF6', '#EC4899'], borderWidth: 1 }] },
            options: commonOptions
        });

        new Chart(document.getElementById('typeChart'), {
            type: 'bar',
            data: { labels: typeLabels, datasets: [{ data: typeData, backgroundColor: '#3B82F6', borderWidth: 1 }] },
            options: { ...commonOptions, scales: { y: { beginAtZero: true } } }
        });
    </script>
    <?php if (isset($_GET['success'])): ?>
        <script>
            Swal.fire({ icon: 'success', title: 'Success!', text: '<?php echo htmlspecialchars($_GET['success']); ?>', confirmButtonColor: '#6366F1' });
        </script>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <script>
            Swal.fire({ icon: 'error', title: 'Oops!', text: '<?php echo htmlspecialchars($_GET['error']); ?>', confirmButtonColor: '#6366F1' });
        </script>
    <?php endif; ?>

</body>

</html>