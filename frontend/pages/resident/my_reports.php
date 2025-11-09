<?php
session_start();
require_once "../../../backend/config/db.php";
require_once "../../../backend/auth/login.php";
require_once "../../../backend/auth/auth_check.php";

// Redirect if not logged in
redirectIfNotLoggedIn(['../login.php'], $pdo);

// Get user info from session
$user_id = $_SESSION['user_id'];
$name    = $_SESSION['name'] ?? "Resident";
$role    = $_SESSION['role'] ?? null;
$status  = $_SESSION['status'] ?? null;

// Allowed roles and statuses
$allowedRoles = ['Resident'];
$allowedStatus = ['Approved'];

// Redirect if role not allowed
if (!in_array($role, $allowedRoles)) {
    header("Location: ../../pages/unauthorized_page.php");
    exit;
}

// Redirect if status not allowed
if (!in_array($status, $allowedStatus)) {
    header("Location: ../../pages/unauthorized_page.php");
    exit;
}

// Fetch ALL document reports for the logged-in user
// Fetch ALL incidents for the logged-in user (as reporter)
$stmt = $pdo->prepare("
    SELECT incident_id, reporter_user_id, reporter_non_resident_id, category, type, description, location, photo, date_time, created_at 
    FROM incidents 
    WHERE reporter_user_id = :user_id
    ORDER BY created_at DESC
");
$stmt->execute(['user_id' => $user_id]);
$incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include('resident-head.php'); ?>
<body class="bg-gray-100">
    <?php include('../../components/DashNav.php'); ?>
    <main class="pt-24 px-4 sm:px-6 lg:px-10 space-y-8 w-full min-h-screen">
        <div class="container">
            <!-- Document reports Table -->
            <div class="bg-white shadow-md rounded-xl p-6 mt-8">
    <h2 class="text-xl font-bold mb-4">My Incident Reports</h2>
    <?php if (count($incidents) > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-left p-2">Category</th>
                        <th class="text-left p-2">Type</th>
                        <th class="text-left p-2">Description</th>
                        <th class="text-left p-2">Location</th>
                        <th class="text-left p-2">Date & Time</th>
                        <th class="text-left p-2">Created At</th>
                        <th class="text-center p-2">Photo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incidents as $incident): ?>
                        <tr class="border-t">
                            <td class="p-2"><?= htmlspecialchars($incident['category']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($incident['type']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($incident['description']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($incident['location']) ?></td>
                            <td class="p-2"><?= date('M d, Y H:i', strtotime($incident['date_time'])) ?></td>
                            <td class="p-2"><?= date('M d, Y', strtotime($incident['created_at'])) ?></td>
                            <td class="p-2 text-center">
                                <?php if (!empty($incident['photo'])): ?>
                                    <a href="../../../uploads/incidents/<?= htmlspecialchars($incident['photo']) ?>" 
                                       target="_blank" 
                                       class="text-blue-600 underline">
                                       View
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm">No Photo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-500">You have not submitted any incident reports yet.</p>
    <?php endif; ?>
</div>

        </div>
    </main>
</body>
</html>
