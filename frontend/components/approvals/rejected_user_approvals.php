<section>
    <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 mb-3">
        👥 Rejected User Accounts
    </h1>

    <div class="bg-white shadow-md sm:shadow-xl rounded-xl p-4 sm:p-6">
        <!-- Search -->
        <div class="mb-3 flex flex-col sm:flex-row justify-between gap-3">
            <input id="rejectedUserSearch" 
                type="text" 
                placeholder="Search users..."
                   class="w-full sm:w-1/3 border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500 focus:outline-none"
                   onkeyup="filterRejectedUsers()">
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto max-h-96 overflow-y-auto rounded-lg border">
            <table id="rejectedUserTable" class="min-w-full text-sm border-collapse">
                <thead class="bg-gray-100 text-gray-700 sticky top-0 z-20">
                    <tr>
                        <th class="px-3 py-2 text-left sticky left-0 bg-gray-100 z-30">Name</th>
                        <th class="px-3 py-2 text-left">Email</th>
                        <th class="px-3 py-2 text-left">Role</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rejectedUsers)): ?>
                        <?php foreach ($rejectedUsers as $row): ?>
                            <tr class="hover:bg-gray-50 transition border-b">
                                <td class="px-3 py-2 whitespace-nowrap sticky left-0 bg-white z-10">
                                    <?= htmlspecialchars($row['full_name'] ?? 'N/A') ?>
                                </td>
                                <td class="px-3 py-2"><?= htmlspecialchars($row['email']) ?></td>
                                <td class="px-3 py-2"><?= htmlspecialchars($row['role']) ?></td>
                                <td class="px-3 py-2 text-red-600 font-semibold">
                                    <?= htmlspecialchars($row['status']) ?>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button 
                                        onclick="openUserModal(<?= $row['user_id'] ?>)" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition">
                                        View
                                    </button>
                                    <button 
                                        class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded transition"
                                        onclick="restoreUser(<?= htmlspecialchars($row['user_id']) ?>)">
                                        Restore
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">No rejected users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="grid gap-3 md:hidden max-h-96 overflow-y-auto">
            <?php if (!empty($rejectedUsers)): ?>
                <?php foreach ($rejectedUsers as $row): ?>
                    <div class="border rounded-lg p-3 shadow-sm bg-gray-50">
                        <p><span class="font-semibold">Name:</span> <?= htmlspecialchars($row['full_name'] ?? 'N/A') ?></p>
                        <p><span class="font-semibold">Email:</span> <?= htmlspecialchars($row['email']) ?></p>
                        <p><span class="font-semibold">Role:</span> <?= htmlspecialchars($row['role']) ?></p>
                        <p><span class="font-semibold">Status:</span> 
                            <span class="text-red-600"><?= htmlspecialchars($row['status']) ?></span>
                        </p>

                        <div class="mt-3 flex justify-end gap-2">
                            <button 
                                onclick="openUserModal(<?= $row['user_id'] ?>)"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition">
                                View
                            </button>
                            <button 
                                class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded transition"
                                onclick="restoreUser(<?= htmlspecialchars($row['user_id']) ?>)">
                                Restore
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-500 py-4">No rejected users found</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ✅ Include the modal directly once -->
<?php include('../../assets/modals/user_view_modal.php'); ?>

<!-- Restore Script -->
<script>
function restoreUser(userId) {
    Swal.fire({
        title: "Restore this user?",
        text: "This will change the user's status back to 'Pending'.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#16A34A",
        cancelButtonColor: "#DC2626",
        confirmButtonText: "Yes, restore"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("../../../backend/actions/user/restore_user.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `user_id=${encodeURIComponent(userId)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire("Restored!", data.message, "success").then(() => location.reload());
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            })
            .catch(() => Swal.fire("Error", "Failed to restore user.", "error"));
        }
    });
}

// ✅ Simple modal control (no fetch)
function openUserModal(userId) {
    const modal = document.getElementById(`profileModal-${userId}`);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeUserModal(userId) {
    const modal = document.getElementById(`profileModal-${userId}`);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// ✅ Live search
function filterRejectedUsers() {
    const input = document.getElementById('rejectedUserSearch');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#rejectedUserTable tbody tr');
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}
</script>
