<!-- Identity Info -->
<div class="bg-white shadow rounded-xl p-6">
    <legend class="font-semibold text-lg mb-4 text-indigo-600 border-b pb-2">Identity Documents</legend>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- ID Type -->
        <div class="mb-4">
            <label class="block mb-1 mt-2 font-medium text-sm">TYPE OF VALID ID *</label>
            <select name="id_type"
                class="w-full border rounded px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                required>
                <option value="">Select ID Type</option>
                <option value="PhilHealth ID" <?= ($data['id_type'] ?? '') === 'PhilHealth ID' ? 'selected' : ''; ?>>PhilHealth ID</option>
                <option value="SSS ID" <?= ($data['id_type'] ?? '') === 'SSS ID' ? 'selected' : ''; ?>>SSS ID</option>
                <option value="TIN ID" <?= ($data['id_type'] ?? '') === 'TIN ID' ? 'selected' : ''; ?>>TIN ID</option>
                <option value="Driver's License" <?= ($data['id_type'] ?? '') === "Driver's License" ? 'selected' : ''; ?>>Driver's License</option>
                <option value="UMID" <?= ($data['id_type'] ?? '') === 'UMID' ? 'selected' : ''; ?>>UMID</option>
                <option value="Voter's ID" <?= ($data['id_type'] ?? '') === "Voter's ID" ? 'selected' : ''; ?>>Voter's ID</option>
                <option value="Postal ID" <?= ($data['id_type'] ?? '') === 'Postal ID' ? 'selected' : ''; ?>>Postal ID</option>
                <option value="National ID" <?= ($data['id_type'] ?? '') === 'National ID' ? 'selected' : ''; ?>>National ID</option>
                <option value="Student ID" <?= ($data['id_type'] ?? '') === 'Student ID' ? 'selected' : ''; ?>>Student ID</option>
            </select>
        </div>

        <?php
        // Define your separate folder directories for each image type
        $frontDir = '../../../uploads/ids/front/';
        $backDir = '../../../uploads/ids/back/';
        $selfieDir = '../../../uploads/ids/selfie/';
        ?>

        <!-- Front ID -->
        <div>
            <label for="front_valid_id_path" class="block text-sm font-medium text-gray-700 mb-1">Upload Front of ID</label>

            <?php if (!empty($data['front_valid_id_path'])): 
                $frontPath = $frontDir . basename($data['front_valid_id_path']);
            ?>
                <div class="mb-2">
                    <a href="<?= htmlspecialchars($frontPath) ?>" target="_blank">
                        <img src="<?= htmlspecialchars($frontPath) ?>" alt="Front ID Preview"
                             class="w-40 h-28 object-cover border rounded-lg shadow hover:scale-105 transition-transform">
                    </a>
                </div>
            <?php endif; ?>

            <input type="file" id="front_valid_id_path" name="front_valid_id_path"
                class="w-full border rounded px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <!-- Back ID -->
        <div>
            <label for="back_valid_id_path" class="block text-sm font-medium text-gray-700 mb-1">Upload Back of ID</label>

            <?php if (!empty($data['back_valid_id_path'])): 
                $backPath = $backDir . basename($data['back_valid_id_path']);
            ?>
                <div class="mb-2">
                    <a href="<?= htmlspecialchars($backPath) ?>" target="_blank">
                        <img src="<?= htmlspecialchars($backPath) ?>" alt="Back ID Preview"
                             class="w-40 h-28 object-cover border rounded-lg shadow hover:scale-105 transition-transform">
                    </a>
                </div>
            <?php endif; ?>

            <input type="file" id="back_valid_id_path" name="back_valid_id_path"
                class="w-full border rounded px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <!-- Selfie with ID -->
        <div>
            <label for="selfie_with_id" class="block text-sm font-medium text-gray-700 mb-1">Upload Selfie with ID</label>

            <?php if (!empty($data['selfie_with_id'])): 
                $selfiePath = $selfieDir . basename($data['selfie_with_id']);
            ?>
                <div class="mb-2">
                    <a href="<?= htmlspecialchars($selfiePath) ?>" target="_blank">
                        <img src="<?= htmlspecialchars($selfiePath) ?>" alt="Selfie with ID Preview"
                             class="w-40 h-28 object-cover border rounded-lg shadow hover:scale-105 transition-transform">
                    </a>
                </div>
            <?php endif; ?>

            <input type="file" id="selfie_with_id" name="selfie_with_id"
                class="w-full border rounded px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>
</div>
