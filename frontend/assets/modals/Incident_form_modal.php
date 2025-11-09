<form id="incidentForm" class="hidden mt-4 space-y-4" enctype="multipart/form-data">
    <!-- Category -->
    <div>
        <label for="incidentCategory" class="block font-semibold mb-1">Category</label>
        <select id="incidentCategory" name="category" required class="w-full border rounded p-2">
            <option value="">-- Select Category --</option>
            <option value="Incident">Incident</option>
            <option value="Emergency">Emergency</option>
            <option value="Accident">Accident</option>
        </select>
    </div>

    <!-- Type -->
    <div>
        <label for="incidentType" class="block font-semibold mb-1">Type</label>
        <select id="incidentType" name="type" required class="w-full border rounded p-2">
            <option value="">-- Select Type --</option>
        </select>
    </div>

    <!-- Description -->
    <div>
        <label for="incidentDescription" class="block font-semibold mb-1">Description</label>
        <textarea id="incidentDescription" name="description" rows="4" placeholder="Describe the incident in detail"
            class="w-full border rounded p-2" required></textarea>
    </div>

    <!-- Location -->
    <div>
        <label for="incidentLocation" class="block font-semibold mb-1">Location</label>
        <input type="text" id="incidentLocation" name="location" class="w-full border rounded p-2"
            placeholder="Enter the exact location" required />
    </div>

    <!-- Date and Time -->
    <div>
        <label for="incidentDateTime" class="block font-semibold mb-1">Date and Time</label>
        <input type="datetime-local" id="incidentDateTime" name="date_time" required
            class="w-full border rounded p-2" />
    </div>

    <!-- Photo Upload -->
    <div>
        <label for="incidentPhoto" class="block font-semibold mb-1">Attach Photo (optional)</label>
        <input type="file" id="incidentPhoto" name="photo" accept="image/*" class="w-full border rounded p-2" />
    </div>

    <!-- Persons Involved -->
    <div>
        <label class="block font-semibold mb-2">Persons Involved</label>
        <div id="personsContainer" class="space-y-4">
            <div class="border p-4 rounded-lg bg-gray-50">
                <!-- Person Type -->
                <div class="mb-3">
                    <label class="block text-sm font-medium">Person Type</label>
                    <select name="person_type[]" onchange="togglePersonInput(this)" class="border rounded p-2 w-full">
                        <option value="">-- Select Person Type --</option>
                        <option value="resident">Resident</option>
                        <option value="non_resident">Non-Resident</option>
                    </select>
                </div>

                <!-- Resident Search -->
                <div class="resident-field hidden mb-3">
                    <label class="block text-sm font-medium">Search Resident</label>
                    <div class="relative">
                        <input type="text" class="resident-search w-full border rounded p-2"
                            placeholder="Type to search..." onkeyup="searchResident(this)" />
                        <input type="hidden" name="resident_id[]" class="resident-id" />
                        <div
                            class="suggestions absolute bg-white border rounded w-full mt-1 hidden max-h-32 overflow-y-auto z-10">
                        </div>
                    </div>
                </div>

                <!-- Non-Resident Fields -->
                <div class="non-resident-field hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" name="f_name[]" placeholder="First Name" class="border rounded p-2 uppercase" />
                        <input type="text" name="m_name[]" placeholder="Middle Name" class="border rounded p-2 uppercase" />
                        <input type="text" name="l_name[]" placeholder="Last Name" class="border rounded p-2 uppercase" />
                        <input type="text" name="ext_name[]" placeholder="Extension (e.g., Jr., Sr.)" class="border rounded p-2 uppercase" />
                        <input type="text" name="address[]" placeholder="Complete Address" class="border rounded p-2 uppercase sm:col-span-2" />
                        <input type="email" name="email[]" placeholder="Email Address" class="border rounded p-2 sm:col-span-2" />
                        <div class="flex sm:col-span-2">
                            <span class="inline-flex items-center px-3 border border-r-0 rounded-l-lg bg-gray-100 text-gray-600 text-sm">+63</span>
                            <input type="text" name="contact_no[]" placeholder="9XXXXXXXXX" maxlength="10" class="w-full border border-gray-300 rounded-r-lg p-2" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                        </div>
                    </div>
                </div>

                <!-- Role -->
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

        <button type="button" onclick="addPerson()" class="bg-indigo-600 text-white px-3 py-2 rounded mt-3 hover:bg-indigo-700">+ Add Another Person</button>
    </div>

    <!-- Submit -->
    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 w-full sm:w-auto">Submit Incident</button>
</form>
