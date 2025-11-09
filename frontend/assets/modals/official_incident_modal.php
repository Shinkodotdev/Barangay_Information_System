    <!-- ✅ INCIDENT REPORT MODAL -->
<div id="incidentModal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 transition-opacity duration-300 ease-out">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 relative overflow-y-auto max-h-[90vh]"
        onclick="event.stopPropagation()">
        <!-- Close Button -->
        <button type="button" onclick="closeIncidentModal()"
            class="absolute top-3 right-4 text-gray-600 hover:text-black text-2xl font-bold">&times;</button>

        <h2 class="text-2xl font-bold mb-4 text-center text-indigo-700">📋 Report an Incident</h2>

        <?php if (isset($_SESSION['role'])): ?>
            <?php if (in_array($_SESSION['role'], ['Resident', 'Official', 'Admin'])): ?>
                <div class="mb-4 text-gray-600 text-sm text-center">
                    <p>You are logged in as
                        <span class="font-semibold text-indigo-600"><?= htmlspecialchars($_SESSION['role']); ?></span>.
                    </p>
                    <p class="text-xs text-gray-500">Please provide accurate incident details below.</p>
                </div>

                <!-- ✅ INCIDENT FORM -->
                <form id="incidentForm" class="space-y-5" enctype="multipart/form-data">
                    <!-- Category -->
                    <div>
                        <label for="incidentCategory" class="block font-semibold mb-1">Category</label>
                        <select id="incidentCategory" name="category" required class="w-full border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select Category --</option>
                            <option value="Incident">Incident</option>
                            <option value="Emergency">Emergency</option>
                            <option value="Accident">Accident</option>
                        </select>
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="incidentType" class="block font-semibold mb-1">Type</label>
                        <select id="incidentType" name="type" required class="w-full border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Select Type --</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block font-semibold mb-1">Description</label>
                        <textarea name="description" rows="4" placeholder="Describe what happened..."
                            class="w-full border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500" required></textarea>
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block font-semibold mb-1">Location</label>
                        <input type="text" name="location" placeholder="Enter the incident location"
                            class="w-full border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>

                    <!-- Date & Time -->
                    <div>
                        <label class="block font-semibold mb-1">Date & Time</label>
                        <input type="datetime-local" name="date_time"
                            class="w-full border rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>

                    <!-- Attach Photo -->
                    <div>
                        <label class="block font-semibold mb-1">Attach Photo (optional)</label>
                        <input type="file" id="photoInput" name="photo" accept="image/*"
                            class="w-full border rounded-lg p-2">

                        <div class="flex flex-wrap gap-2 mt-3">
                            <button type="button" id="useWebcamBtn"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                                <i class="fa-solid fa-camera"></i> Use Camera
                            </button>
                        </div>

                        <!-- Preview -->
                        <div id="photoPreviewContainer" class="mt-3 hidden">
                            <p class="text-sm text-gray-500 mb-1">Preview:</p>
                            <img id="photoPreview" src="" alt="Captured photo" class="rounded-lg shadow-md max-h-48">
                        </div>
                    </div>

                    <!-- ✅ CAMERA MODAL -->
                    <div id="cameraModal"
                        class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-[60]">
                        <div class="bg-white rounded-xl shadow-xl p-4 relative w-[90%] sm:w-[400px] flex flex-col items-center">
                            <button type="button" id="closeCamera"
                                class="absolute top-2 right-3 text-gray-600 text-xl hover:text-black">&times;</button>

                            <video id="cameraStream" autoplay playsinline class="w-full rounded-lg bg-black"></video>

                            <div class="flex justify-between w-full mt-3">
                                <button type="button" id="flipCameraBtn"
                                    class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700">
                                    Flip Camera
                                </button>
                                <button type="button" id="captureBtn"
                                    class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                    Capture
                                </button>
                            </div>
                        </div>
                        <canvas id="snapshotCanvas" class="hidden"></canvas>
                    </div>

                    <!-- Persons Involved -->
                    <div>
                        <label class="block font-semibold mb-2">Persons Involved</label>
                        <div id="personsContainerV2" class="space-y-4">
                            <div class="person-card border p-4 rounded-lg bg-gray-50 relative">
                                <button type="button"
                                    class="remove-person absolute top-2 right-2 text-red-600 hover:text-red-800 text-lg">&times;</button>

                                <div class="mb-3">
                                    <label class="block text-sm font-medium">Person Type</label>
                                    <select name="person_type[]" onchange="togglePersonInputV2(this)"
                                        class="border rounded p-2 w-full">
                                        <option value="">-- Select Person Type --</option>
                                        <option value="resident">Resident</option>
                                        <option value="non_resident">Non-Resident</option>
                                    </select>
                                </div>

                                <div class="resident-field hidden mb-3">
                                    <label class="block text-sm font-medium">Search Resident</label>
                                    <div class="relative">
                                        <input type="text" class="resident-search w-full border rounded p-2"
                                            placeholder="Type to search..." onkeyup="searchResident(this)">
                                        <input type="hidden" name="resident_id[]" class="resident-id">
                                        <div
                                            class="suggestions absolute bg-white border rounded w-full mt-1 hidden max-h-32 overflow-y-auto z-10">
                                        </div>
                                    </div>
                                </div>

                                <div class="non-resident-field hidden">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <input type="text" name="f_name[]" placeholder="First Name"
                                            class="border rounded p-2 uppercase">
                                        <input type="text" name="m_name[]" placeholder="Middle Name"
                                            class="border rounded p-2 uppercase">
                                        <input type="text" name="l_name[]" placeholder="Last Name"
                                            class="border rounded p-2 uppercase">
                                        <input type="text" name="ext_name[]" placeholder="Extension (e.g., Jr.)"
                                            class="border rounded p-2 uppercase">
                                        <input type="text" name="address[]" placeholder="Complete Address"
                                            class="border rounded p-2 uppercase sm:col-span-2">
                                        <input type="email" name="email[]" placeholder="Email Address"
                                            class="border rounded p-2 sm:col-span-2">
                                        <div class="flex sm:col-span-2">
                                            <span
                                                class="inline-flex items-center px-3 border border-r-0 rounded-l-lg bg-gray-100 text-gray-600 text-sm">+63</span>
                                            <input type="text" name="contact_no[]" placeholder="9XXXXXXXXX" maxlength="10"
                                                class="w-full border border-gray-300 rounded-r-lg p-2"
                                                oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium">Role</label>
                                    <select name="role[]" class="border rounded p-2 w-full" required>
                                        <option value="">-- Select Role --</option>
                                        <option value="Victim">Victim</option>
                                        <option value="Witness">Witness</option>
                                        <option value="Suspect">Suspect</option>
                                        <option value="Reporter">Reporter</option>
                                        <option value="Respondent">Respondent</option>
                                        <option value="Complainant">Complainant</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="addAnotherPersonV2()"
                            class="bg-indigo-600 text-white px-3 py-2 rounded mt-3 hover:bg-indigo-700">
                            + Add Another Person
                        </button>
                    </div>

                    <button type="submit"
                        class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 w-full sm:w-auto">
                        Submit Incident
                    </button>
                </form>
            <?php else: ?>
                <div class="text-center py-6 text-red-600">
                    ⚠️ Your user type is not authorized to create an incident report.
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-6 text-red-600">
                ⚠️ Please log in to report an incident.
            </div>
        <?php endif; ?>
    </div>
</div>