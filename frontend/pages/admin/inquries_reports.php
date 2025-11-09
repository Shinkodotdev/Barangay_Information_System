<?php
require '../../../backend/config/db.php';

// Fetch inquiries in descending order
$stmt = $pdo->query("SELECT * FROM inquiries ORDER BY created_at DESC");
$inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include('admin-head.php'); ?>

<body class="bg-gray-100 font-sans">
    <?php include('../../components/DashNav.php'); ?>

    <main class="container mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold text-indigo-700 mb-6 text-center">Inquiries Report</h1>

        <?php if (count($inquiries) > 0): ?>
        <div class="overflow-x-auto shadow-lg rounded-lg bg-white">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-indigo-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Message</th>
                        <th class="px-4 py-3 text-left">Date Sent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($inquiries as $inq): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-gray-700"><?= $inq['inquiries_id'] ?></td>
                        <td class="px-4 py-3 text-gray-800"><?= htmlspecialchars($inq['inquiries_name']) ?></td>
                        <td class="px-4 py-3 text-blue-600 underline"><?= htmlspecialchars($inq['inquiries_email']) ?></td>
                        <td class="px-4 py-3 text-gray-700"><?= nl2br(htmlspecialchars($inq['inquiries_message'])) ?></td>
                        <td class="px-4 py-3 text-gray-600 text-sm"><?= date('F j, Y g:i A', strtotime($inq['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="text-center py-10 text-gray-500 bg-white rounded-lg shadow-md">
                <p>No inquiries found.</p>
            </div>
        <?php endif; ?>
    </main>
</body>
