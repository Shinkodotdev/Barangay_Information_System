<div class="max-w-5xl mx-auto px-6 py-10">
    <form action="../../../backend/residents/create_resident_action.php" method="POST" enctype="multipart/form-data" class="space-y-8">
        <!-- ✅ Profile Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6 bg-white p-6 rounded-xl shadow-md mb-10">
            <!-- Profile Photo Upload -->
            <div class="flex flex-col items-center sm:items-start">
                <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 shadow-inner text-lg">
                    <i class="fa-solid fa-user text-2xl"></i>
                </div>
                <input type="file" name="photo" accept="image/*" class="mt-3 text-sm text-gray-600">
            </div>

            <!-- Basic Info -->
            <div class="mt-4 sm:mt-0 text-center sm:text-left flex-1">
                <h1 class="text-2xl sm:text-3xl font-bold text-indigo-800">Create Resident Profile</h1>
                <p class="text-gray-600 text-base sm:text-lg">Role: Resident</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" required
                            class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required
                            class="border rounded px-3 py-2 w-full focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ Personal Information -->
        <section class="bg-white p-6 rounded-xl shadow-sm">
            <?php sectionHeader("fa-id-card", "Personal Information"); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="text" name="f_name" placeholder="First Name" class="input-field" required>
                <input type="text" name="m_name" placeholder="Middle Name" class="input-field">
                <input type="text" name="l_name" placeholder="Last Name" class="input-field" required>
                <input type="text" name="ext_name" placeholder="Extension (e.g. Jr.)" class="input-field">

                <select name="gender" class="input-field" required>
                    <option value="">Select Gender</option>
                    <option>Male</option>
                    <option>Female</option>
                </select>

                <input type="text" name="contact_no" placeholder="Contact No." class="input-field">
                <select name="civil_status" class="input-field">
                    <option value="">Civil Status</option>
                    <option>Single</option>
                    <option>Married</option>
                    <option>Widowed</option>
                    <option>Separated</option>
                </select>
                <input type="text" name="occupation" placeholder="Occupation" class="input-field">
                <input type="text" name="nationality" placeholder="Nationality" class="input-field">
                <input type="text" name="religion" placeholder="Religion" class="input-field">
                <input type="text" name="blood_type" placeholder="Blood Type" class="input-field">

                <select name="voter_status" class="input-field">
                    <option value="">Voter Status</option>
                    <option>Yes</option>
                    <option>No</option>
                </select>
                <select name="pwd_status" class="input-field">
                    <option value="">PWD</option>
                    <option>Yes</option>
                    <option>No</option>
                </select>
                <select name="senior_citizen_status" class="input-field">
                    <option value="">Senior Citizen</option>
                    <option>Yes</option>
                    <option>No</option>
                </select>
                <input type="text" name="educational_attainment" placeholder="Educational Attainment" class="input-field">
            </div>
        </section>

        <!-- ✅ Birth Information -->
        <section class="bg-white p-6 rounded-xl shadow-sm">
            <?php sectionHeader("fa-baby", "Birth Information"); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="date" name="birth_date" class="input-field" required>
                <input type="text" name="birth_place" placeholder="Birthplace" class="input-field" required>
            </div>
        </section>

        <!-- ✅ Residency Information -->
        <section class="bg-white p-6 rounded-xl shadow-sm">
            <?php sectionHeader("fa-home", "Residency Information"); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="text" name="house_no" placeholder="House No." class="input-field">
                <input type="text" name="purok" placeholder="Purok" class="input-field">
                <input type="text" name="barangay" placeholder="Barangay" class="input-field" value="Poblacion Sur">
                <input type="text" name="municipality" placeholder="Municipality" class="input-field" value="Cabanatuan City">
                <input type="text" name="province" placeholder="Province" class="input-field" value="Nueva Ecija">
                <input type="number" name="years_residency" placeholder="Years of Residency" class="input-field">
                <input type="text" name="house_type" placeholder="House Type" class="input-field">
                <input type="text" name="ownership_status" placeholder="Ownership Status" class="input-field">
                <input type="text" name="previous_address" placeholder="Previous Address" class="input-field">
            </div>
        </section>

        <!-- ✅ Family Information -->
        <section class="bg-white p-6 rounded-xl shadow-sm">
            <?php sectionHeader("fa-users", "Family Information"); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="text" name="fathers_name" placeholder="Father's Name" class="input-field">
                <input type="text" name="fathers_birthplace" placeholder="Father's Birthplace" class="input-field">
                <input type="text" name="mothers_name" placeholder="Mother's Maiden Name" class="input-field">
                <input type="text" name="mothers_birthplace" placeholder="Mother's Birthplace" class="input-field">
                <input type="text" name="spouse_name" placeholder="Spouse Name" class="input-field">
                <input type="number" name="num_dependents" placeholder="No. of Dependents" class="input-field">
                <input type="text" name="contact_person" placeholder="Emergency Contact Person" class="input-field">
                <input type="text" name="emergency_contact_no" placeholder="Emergency Contact No." class="input-field">
            </div>
        </section>

        <!-- ✅ Health Information -->
        <section class="bg-white p-6 rounded-xl shadow-sm">
            <?php sectionHeader("fa-heartbeat", "Health Information"); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="text" name="health_condition" placeholder="Health Condition" class="input-field">
                <input type="text" name="common_health_issue" placeholder="Common Health Issue" class="input-field">
                <input type="number" name="weight_kg" placeholder="Weight (kg)" class="input-field">
                <input type="number" name="height_cm" placeholder="Height (cm)" class="input-field">
                <input type="date" name="last_medical_checkup" class="input-field">
                <input type="text" name="health_remarks" placeholder="Health Remarks" class="input-field">
            </div>
        </section>

        <!-- ✅ Income Information -->
        <section class="bg-white p-6 rounded-xl shadow-sm">
            <?php sectionHeader("fa-coins", "Income Information"); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="text" name="monthly_income" placeholder="Monthly Income" class="input-field">
                <select name="income_source" class="input-field">
                    <option value="">Select Income Source</option>
                    <option>Employment</option>
                    <option>Business</option>
                    <option>Farming</option>
                    <option>Remittance</option>
                    <option>Government Aid</option>
                    <option>None</option>
                </select>
                <input type="number" name="household_members" placeholder="Household Members" class="input-field">
                <input type="text" name="additional_income_sources" placeholder="Additional Income Sources" class="input-field">
                <input type="text" name="household_head_occupation" placeholder="Household Head Occupation" class="input-field">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Income Proof</label>
                    <input type="file" name="income_proof" accept="image/*" class="input-field">
                </div>
            </div>
        </section>

        <!-- ✅ Identity Documents -->
        <section class="bg-white p-6 rounded-xl shadow-sm">
            <?php sectionHeader("fa-id-badge", "Identity Documents"); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="text" name="id_type" placeholder="ID Type" class="input-field">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Front ID</label>
                    <input type="file" name="front_valid_id_path" accept="image/*" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Back ID</label>
                    <input type="file" name="back_valid_id_path" accept="image/*" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Selfie with ID</label>
                    <input type="file" name="selfie_with_id" accept="image/*" class="input-field">
                </div>
            </div>
        </section>

        <!-- ✅ Submit -->
        <div class="flex justify-end mt-6">
            <button type="submit"
                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-lg transition">
                <i class="fa-solid fa-save mr-2"></i>Save Resident
            </button>
        </div>
    </form>
</div>


<style>
    .input-field {
        @apply border rounded px-3 py-2 w-full text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500;
    }
</style>
