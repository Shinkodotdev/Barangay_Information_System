<?php
include('admin-head.php');
include('../../components/DashNav.php');
include('../../../backend/config/db.php');
include('../../../backend/models/Repository.php'); 
$Announcements = getActiveAnnouncements($pdo);
$Events = getActiveEvents($pdo);
?>
<body class="bg-gray-100">
    <main class="pt-24 px-4 sm:px-6 lg:px-10 space-y-12 w-full min-h-screen">
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold text-center text-indigo-700 mb-10">📢 Announcement & 🎉 Events</h1>
            <!-- Add Buttons -->
            <div class="flex justify-end mb-4 gap-2">
                <button onclick="openAnnouncementForm()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Add Announcement</button>
                <button onclick="openEventForm()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">+ Add Event</button>
            </div>
            <!-- Announcements Section -->
            <section id="Announcements" class="mb-16">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">📢 Barangay Announcements</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php if (!empty($Announcements)): ?>
                        <?php foreach ($Announcements as $a): ?>
                            <?php
                            $image = !empty($a['announcement_image'])
                                ? '../../uploads/announcement/' . htmlspecialchars($a['announcement_image'])
                                : '../../assets/images/home.jpg';

                            $priorityColors = [
                                'Urgent' => 'bg-red-600 text-white',
                                'High'   => 'bg-orange-500 text-white',
                                'Normal' => 'bg-blue-500 text-white',
                                'Low'    => 'bg-gray-400 text-white'
                            ];
                            $priorityIcons = [
                                'Urgent' => 'fa-triangle-exclamation',
                                'High'   => 'fa-arrow-up',
                                'Normal' => 'fa-minus',
                                'Low'    => 'fa-arrow-down',
                            ];

                            // Author display
                            if ($a['role'] === 'Admin') {
                                $authorName = "Admin";
                            } else {
                                $authorName = trim($a['f_name'] . ' ' . ($a['m_name'] ? $a['m_name'][0] . '.' : '') . ' ' . $a['l_name'] . ' ' . $a['ext_name']);
                            }
                            ?>
                            <div class="bg-white shadow-md rounded-lg p-4 flex flex-col hover:shadow-lg transition relative">
                                <img src="<?= $image ?>"
                                    alt="<?= htmlspecialchars($a['announcement_title']) ?>"
                                    class="w-full h-40 object-cover rounded-lg mb-3"
                                    onerror="this.onerror=null;this.src='../../uploads/images/default.jpg';">

                                <!-- Priority -->
                                <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-bold rounded-full <?= $priorityColors[$a['priority']] ?? 'bg-gray-400 text-white' ?> mb-2">
                                    <i class="fa-solid <?= $priorityIcons[$a['priority']] ?? 'fa-circle' ?>"></i>
                                    <?= htmlspecialchars($a['priority']) ?>
                                </span>

                                <!-- Title -->
                                <h2 class="text-lg font-semibold mb-1"><?= htmlspecialchars($a['announcement_title']) ?></h2>

                                <!-- Location -->
                                <?php if (!empty($a['announcement_location'])): ?>
                                    <p class="text-sm text-gray-500 mb-1">📍 <?= htmlspecialchars($a['announcement_location']) ?></p>
                                <?php endif; ?>

                                <!-- Content -->
                                <p class="text-gray-600 text-sm flex-grow"><?= nl2br(htmlspecialchars(substr($a['announcement_content'], 0, 120))) ?>...</p>

                                <!-- Author + Status -->
                                <div class="text-xs text-gray-500 mt-2">
                                    👤 <?= htmlspecialchars($authorName) ?> |
                                    <span class="<?= $a['status'] === 'Published' ? 'text-green-600' : 'text-gray-600' ?>">
                                        <?= htmlspecialchars($a['status']) ?>
                                    </span>
                                </div>

                                <!-- VIEW Edit/Archive Buttons -->
                                <div class="flex gap-2 mt-2 justify-end">
                                    <button onclick='openViewAnnouncement(<?= json_encode($a) ?>)'
                                        class="text-white bg-indigo-500 px-2 py-1 rounded hover:bg-indigo-600 text-xs">View</button>
                                    <button onclick='openAnnouncementForm(<?= json_encode($a) ?>)'
                                        class="text-white bg-yellow-500 px-2 py-1 rounded hover:bg-yellow-600 text-xs">Edit</button>
                                    <button onclick="confirmDelete('announcement', <?= $a['announcement_id'] ?>)"
                                        class="text-white bg-red-500 px-2 py-1 rounded hover:bg-red-600 text-xs">Archive</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center shadow-sm p-6 bg-white rounded-lg">
                            <h5 class="text-xl font-semibold text-gray-700">No Announcements Found</h5>
                            <p class="text-gray-500">Check back later for updates</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
            <!-- Events Section -->
            <?php include('./admin-section/event_section.php'); ?>
        </div>
    </main>
    <?php include('../../assets/modals/announcement_modal.php'); ?>
    <?php include('../../assets/modals/event_modal.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(type, id) {
            let url = "";
            if (type === "announcement") {
                url = "../../../backend/actions/announcement_crud.php?delete=" + id;
            } else if (type === "event") {
                url = "../../../backend/actions/event_crud.php?delete=" + id;
            }

            Swal.fire({
                title: "Are you sure?",
                text: "This will archive the " + type + " and move it out of the active list.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e3342f",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, archive it"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
        const announcements = <?= json_encode($Announcements) ?>;
        const events = <?= json_encode($Events) ?>;
    </script>
    <script src="../../assets/js/Manage_announcement.js"></script>
</body>

</html>