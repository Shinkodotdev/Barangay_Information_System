 <div id="createResidentModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white w-full max-w-5xl max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl p-8 relative">
            <button type="button" onclick="closeCreateResidentModal()"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-xl font-bold">&times;</button>

            <h2 class="text-3xl font-bold text-center mb-6 text-indigo-700 flex items-center justify-center gap-2">
                <i class="fa-solid fa-user-plus"></i> Create New Resident
            </h2>

<form id="createResidentForm" method="POST" enctype="multipart/form-data" class="space-y-8">
                <?php
                //✅ PERSONAL INFO
                include('../profile_section/create/personal_info_create.php');
                //✅ BIRTH INFO
                include('../profile_section/create/birth_info_create.php');
                //✅ RESIDENCY INFO
                include('../profile_section/create/residency_info_create.php');
                // ✅ FAMILY INFO
                include('../profile_section/create/family_info_create.php');
                //✅ HEALTH INFO
                include('../profile_section/create/health_info_create.php');
                //✅ INCOME INFO
                include('../profile_section/create/income_info_create.php');
                //✅ IDENTITY INFO
                include('../profile_section/create/identity_info_create.php');
                ?>

                <!-- ✅ ACCOUNT INFO -->
                <section class="bg-gray-50 p-6 rounded-xl shadow-sm">
                    <h3 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-user-shield"></i> Account Information
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Role</label>
                            <select name="role" id="roleSelect" class="border rounded p-2 w-full uppercase" required>
                                <option value="">Select Role</option>
                                <option value="Resident">Resident</option>
                                <option value="Official">Official</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Status</label>
                            <select name="status" class="border rounded p-2 w-full uppercase" required>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>

                        <div id="positionField" class="sm:col-span-2 hidden">
                            <label class="block text-gray-700 font-medium mb-1">Position (if Official)</label>
                            <select name="position" class="border rounded p-2 w-full uppercase">
                                <option value="">Select Position</option>
                                <option>Barangay Captain</option>
                                <option>Barangay Councilor</option>
                                <option>SK Chairman</option>
                                <option>SK Councilor</option>
                            </select>
                        </div>
                    </div>
                </section>

                <script>
                    // ✅ Show/Hide Position Field Based on Role
                    document.addEventListener('DOMContentLoaded', () => {
                        const roleSelect = document.getElementById('roleSelect');
                        const positionField = document.getElementById('positionField');

                        roleSelect.addEventListener('change', () => {
                            if (roleSelect.value === 'Official') {
                                positionField.classList.remove('hidden');
                            } else {
                                positionField.classList.add('hidden');
                                positionField.querySelector('select').value = ''; // Clear position if hidden
                            }
                        });
                    });
                </script>


                <!-- ✅ ACTION BUTTONS -->
                <div class="flex justify-center gap-4 pt-6">
                    <button type="button" onclick="closeCreateResidentModal()"
                        class="px-6 py-3 bg-gray-400 hover:bg-gray-500 text-white rounded-lg">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">
                        <i class="fa-solid fa-save"></i> Save Resident
                    </button>
                </div>
            </form>
        </div>
    </div>