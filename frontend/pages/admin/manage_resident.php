<?php
session_start();

// ✅ Role and Status Validation
$allowedRoles = ['Admin'];
$allowedStatus = ['Approved'];

require_once "../../../backend/auth/auth_check.php";
require_once "../../../backend/config/db.php";
require_once "../../../backend/models/Repository.php";

// ✅ Filtering Logic
$filter = $_GET['filter'] ?? 'all';

if ($filter === 'archived_rejected') {
    $stmt = $pdo->prepare("SELECT 
        u.user_id, 
        u.email, 
        u.role, 
        u.status,
        u.is_archived,
        u.created_at,
        CONCAT(
            ud.f_name, ' ',
            COALESCE(CONCAT(ud.m_name, ' '), ''),
            ud.l_name,
            IF(ud.ext_name IS NOT NULL AND ud.ext_name != '', CONCAT(' ', ud.ext_name), '')
        ) AS full_name
    FROM users u
    LEFT JOIN user_details ud ON u.user_id = ud.user_id
    WHERE u.role = 'Resident' AND u.is_archived = 1 AND u.status = 'Rejected'
    ORDER BY u.created_at DESC
    LIMIT 100");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->prepare("SELECT 
        u.user_id, 
        u.email, 
        u.role, 
        u.status,
        u.is_archived,
        u.created_at,
        CONCAT(
            ud.f_name, ' ',
            COALESCE(CONCAT(ud.m_name, ' '), ''),
            ud.l_name,
            IF(ud.ext_name IS NOT NULL AND ud.ext_name != '', CONCAT(' ', ud.ext_name), '')
        ) AS full_name
    FROM users u
    LEFT JOIN user_details ud ON u.user_id = ud.user_id
    WHERE u.role = 'Resident' AND u.is_archived = 0 AND u.status IN ('Verified', 'Pending', 'Approved')
    ORDER BY u.created_at DESC
    LIMIT 100");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// ✅ Page Meta Information
$pageTitle = "Admin | Manage Residents";
$pageDescription = "Manage Residents for Barangay Poblacion Sur System";

// ✅ Include Admin Header
include 'admin-head.php';
?>
<body class="bg-gray-100">
    <?php include('../../components/DashNav.php'); ?>
    <main class="flex-1 p-3 sm:p-6 md:p-8 mt-16 md:mt-0">
        <div class="max-w-7xl mx-auto space-y-10">
            <section>
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800">👥 Manage Residents</h1>
                    
                    <button onclick="openCreateResidentModal()"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow">
                        ➕ Create
                    </button>
                </div>
                <?php include('../../components/manage/user.php'); ?>
            </section>
        </div>
    </main>
    <div id="modalContainer"></div>
    <?php 
    include('../../assets/modals/user_create_modal.php');
    include('../../assets/modals/user_view_modal.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../assets/js/Approval_Search.js"></script>
    <script src="../../assets/js/Open_profile_modal.js"></script>
    <script src="../../assets/js/User_Reminder.js"></script>
</body>
</html>