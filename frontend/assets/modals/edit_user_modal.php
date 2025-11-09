<?php
require_once '../../../backend/config/db.php';
require_once '../../../backend/models/Repository.php';
include('../../assets/helpers/helpers.php');
$userId = $_GET['user_id'] ?? null;
if (!$userId || !is_numeric($userId)) {
    exit("Invalid request.");
}
// ✅ Fetch user full data
$data = getUserProfileById($pdo, $userId);
if (!$data)
    exit("User not found.");
// ✅ Fetch position if official
$stmt = $pdo->prepare("SELECT position FROM officials WHERE user_id = ?");
$stmt->execute([$userId]);
$official = $stmt->fetch(PDO::FETCH_ASSOC);
$position = $official['position'] ?? '';
?>
<!-- ✅ Modal Container -->
<div id="editUserModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-2 sm:p-4 overflow-y-auto">
    <!-- ✅ Modal Box -->
    <div
        class="bg-white rounded-lg shadow-lg w-full max-w-5xl mx-auto p-4 sm:p-6 relative overflow-y-auto max-h-[95vh]">
        <h2 class="text-xl font-semibold mb-4 text-center text-gray-800">Edit User Details</h2>

        <form  action="../../../backend/controllers/admin_update_user.php" method="POST"
            enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($data['user_id']) ?>">
            <input type="hidden" name="role" value="<?= htmlspecialchars($data['role']) ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($data['email']) ?>">
            <!-- Personal Info -->
            <?php include('../../pages/profile_section/personal_info.php'); ?>
            <!-- Birth Info -->
            <?php include('../../pages/profile_section/birth_info.php'); ?>
            <!-- Residency -->
            <?php include('../../pages/profile_section/residency_info.php'); ?>
            <!-- Family Info -->
            <?php include('../../pages/profile_section/family_info.php'); ?>
            <!-- Health Info -->
            <?php include('../../pages/profile_section/health_info.php'); ?>
            <!-- Income Info -->
            <?php include('../../pages/profile_section/income_info.php'); ?>
            <!-- Identity Docs -->
            <?php include('../../pages/profile_section/identity_info.php'); ?>
            <!-- ✅ ACCOUNT INFO -->
            <fieldset>
                <legend class="font-semibold text-indigo-600 border-b pb-2 mb-3">Account Information</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label>Status</label>
                        <select name="status" class="w-full border p-2 rounded">
                            <option value="Approved" <?= ($user['user_status'] ?? '') === 'Approved' ? 'selected' : '' ?>>
                                Approved</option>
                            <option value="Pending" <?= ($user['user_status'] ?? '') === 'Pending' ? 'selected' : '' ?>>
                                Pending</option>
                            <option value="Rejected" <?= ($user['user_status'] ?? '') === 'Rejected' ? 'selected' : '' ?>>
                                Rejected</option>
                        </select>
                    </div>

                    <?php if (($user['role'] ?? '') === 'official'): ?>
                        <div class="sm:col-span-2">
                            <label>Position (if Official)</label>
                            <select name="position" class="w-full border p-2 rounded">
                                <option value="">Select Position</option>
                                <option value="Barangay Captain" <?= $position === 'Barangay Captain' ? 'selected' : '' ?>>
                                    Barangay Captain</option>
                                <option value="Barangay Councilor" <?= $position === 'Barangay Councilor' ? 'selected' : '' ?>>
                                    Barangay Councilor</option>
                                <option value="SK Chairman" <?= $position === 'SK Chairman' ? 'selected' : '' ?>>SK Chairman
                                </option>
                                <option value="SK Councilor" <?= $position === 'SK Councilor' ? 'selected' : '' ?>>SK Councilor
                                </option>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </fieldset>
            <!-- ✅ BUTTONS -->
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" id="closeEditModal"
                    class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded transition">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">Save
                    Changes</button>
            </div>
        </form>
    </div>
</div>
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function () {
            const output = document.getElementById('preview');
            output.src = reader.result;
            output.classList.remove('hidden');
        };
        reader.readAsDataURL(event.target.files[0]);
    }
    document.addEventListener("DOMContentLoaded", function () {
        const civilStatus = document.getElementById("civil_status");
        const spouseField = document.getElementById("spouse_field");

        function toggleSpouseField() {
            if (civilStatus.value === "Single") {
                spouseField.classList.add("hidden");
            } else {
                spouseField.classList.remove("hidden");
            }
        }
        // Run on page load
        toggleSpouseField();
        // Run on change
        civilStatus.addEventListener("change", toggleSpouseField);
    });
</script>