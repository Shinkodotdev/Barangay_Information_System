<?php
session_start();
require_once "../../../backend/config/db.php";
require_once "../../../backend/auth/login.php";
require_once "./user_pending_check.php";

// ✅ Always reset user status to Pending when opening this form
if (isset($_SESSION['user_id'])) {
    $updateStatus = $pdo->prepare("UPDATE users SET status = 'Pending' WHERE user_id = ?");
    $updateStatus->execute([$_SESSION['user_id']]);
    $_SESSION['status'] = "Pending"; // keep session in sync
}

$status = $_SESSION['status'] ?? 'Pending';
$user_id = $_SESSION['user_id'] ?? null;

$stmt = $pdo->prepare("
    SELECT 
        d.*, 
        r.house_no, r.purok, r.barangay, r.municipality, r.province, 
        r.years_residency, r.household_head, r.house_type, 
        r.ownership_status, r.previous_address,
        b.birth_date, b.birth_place,
        i.id_type, i.front_valid_id_path, i.back_valid_id_path, i.selfie_with_id,
        f.fathers_name, f.fathers_birthplace, f.mothers_name, f.mothers_birthplace,
        f.spouse_name, f.num_dependents, f.contact_person, f.emergency_contact_no,
        h.health_condition, h.common_health_issue,
        h.vaccination_status, h.height_cm, h.weight_kg, h.last_medical_checkup, h.health_remarks,
        inc.monthly_income, inc.income_source, inc.household_members,
        inc.additional_income_sources, inc.household_head_occupation, inc.income_proof
    FROM user_details d
    LEFT JOIN user_residency r ON d.user_id = r.user_id
    LEFT JOIN user_birthdates b ON d.user_id = b.user_id
    LEFT JOIN user_identity_docs i ON d.user_id = i.user_id
    LEFT JOIN user_family_info f ON d.user_id = f.user_id
    LEFT JOIN user_health_info h ON d.user_id = h.user_id
    LEFT JOIN user_income_info inc ON d.user_id = inc.user_id
    WHERE d.user_id = ?
");
$stmt->execute([$user_id]);
$userDetails = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | Barangay Information System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="../../assets/images/Logo.webp" type="image/x-icon">
</head>

<body class="bg-gray-100 p-6">

    <div class="bg-white shadow-lg rounded-2xl p-6 sm:p-10 w-full max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-center text-indigo-600">Edit My Profile</h1>

        <form action="./update_pending_profile.php" method="POST" enctype="multipart/form-data" class="space-y-8">

            <!-- ✅ Personal Info -->
            <section class="mb-8 bg-gray-50 p-6 rounded-xl shadow-sm">
                <h2 class="text-xl font-semibold text-indigo-500 border-b pb-2 mb-4">Personal Information</h2>
                <!-- ✅ Profile Photo -->
                <div class="mb-4 flex flex-col items-center text-center">
                    <label class="block mb-1 font-medium">Profile Photo</label>
                    <?php if (!empty($userDetails['photo'])): ?>
                        <img src="<?= '/Barangay_Information_System/uploads/profile/' . basename($userDetails['photo']); ?>"
                            class="w-32 h-32 object-cover rounded-full shadow mb-2">
                        <input type="hidden" name="old_photo" value="<?= htmlspecialchars($userDetails['photo']); ?>">
                    <?php else: ?>
                        <p class="text-gray-500 italic mb-2">No profile photo uploaded</p>
                    <?php endif; ?>
                    <input type="file" name="photo" class="border p-2 rounded w-full sm:w-64">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <!-- First Name -->
                    <div>
                        <label for="f_name" class="block mb-1 font-medium">First Name</label>
                        <input type="text" id="f_name" name="f_name"
                            value="<?= htmlspecialchars($userDetails['f_name'] ?? '') ?>" placeholder="First Name"
                            class="border p-2 rounded w-full" required>
                    </div> <!-- Middle Name -->
                    <div>
                        <label for="m_name" class="block mb-1 font-medium">Middle Name</label>
                        <input type="text" id="m_name" name="m_name"
                            value="<?= htmlspecialchars($userDetails['m_name'] ?? '') ?>" placeholder="Middle Name"
                            class="border p-2 rounded w-full">
                    </div> <!-- Last Name -->
                    <div>
                        <label for="l_name" class="block mb-1 font-medium">Last Name</label>
                        <input type="text" id="l_name" name="l_name"
                            value="<?= htmlspecialchars($userDetails['l_name'] ?? '') ?>" placeholder="Last Name"
                            class="border p-2 rounded w-full" required>
                    </div>
                    <div>
                        <label for="ext_name" class="block mb-1 font-medium">Extension Name</label>
                        <select name="ext_name" class="border p-2 rounded uppercase">
                            <option value="">Ext. Name</option>
                            <option value="Jr" <?php echo strtoupper($userDetails['ext_name'] ?? '') === 'JR' ? 'selected' : ''; ?>>Jr.</option>
                            <option value="Sr" <?php echo strtoupper($userDetails['ext_name'] ?? '') === 'SR' ? 'selected' : ''; ?>>Sr.</option>
                            <option value="II" <?php echo strtoupper($userDetails['ext_name'] ?? '') === 'II' ? 'selected' : ''; ?>>II</option>
                            <option value="III" <?php echo strtoupper($userDetails['ext_name'] ?? '') === 'III' ? 'selected' : ''; ?>>III</option>
                            <option value="IV" <?php echo strtoupper($userDetails['ext_name'] ?? '') === 'IV' ? 'selected' : ''; ?>>IV</option>
                        </select>
                    </div>
                    <div>
                        <label for="gender" class="block mb-1 font-medium"> Gender</label>
                        <select name="gender" class="border rounded-lg p-2 w-full" required>
                            <option value="">SELECT GENDER *</option>
                            <option value="MALE" <?= ($userDetails['gender'] ?? '') === 'MALE' ? 'selected' : '' ?>>MALE
                            </option>
                            <option value="FEMALE" <?= ($userDetails['gender'] ?? '') === 'FEMALE' ? 'selected' : '' ?>>
                                FEMALE</option>
                            <option value="LGBTQ" <?= ($userDetails['gender'] ?? '') === 'LGBTQ' ? 'selected' : '' ?>>LGBTQ
                            </option>

                        </select>
                    </div>
                    <div>
                        <label for="contact_no" class="block mb-1 font-medium">Contact No</label>
                        <div class="flex">
                            <span
                                class="inline-flex items-center px-2 border border-r-0 rounded-l bg-gray-100">+63</span>
                            <input type="text" name="contact_no" value="<?php echo $userDetails['contact_no'] ?? ''; ?>"
                                placeholder="9123456789" class="border rounded-r-lg p-3 w-full" pattern="[0-9]{10}"
                                maxlength="10" required>
                        </div>
                    </div>
                    <div>
                        <label for="civil_status" class="block mb-1 font-medium">Civil Status</label>

                        <select id="civil_status" name="civil_status" class="border rounded-lg p-2 w-full uppercase"
                            required>
                            <option value="">CIVIL STATUS *</option>
                            <option value="Single" <?php echo ($userDetails['civil_status'] ?? '') === 'Single' ? 'selected' : ''; ?>>Single</option>
                            <option value="Married" <?php echo ($userDetails['civil_status'] ?? '') === 'Married' ? 'selected' : ''; ?>>Married</option>
                            <option value="Widowed" <?php echo ($userDetails['civil_status'] ?? '') === 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                            <option value="Separated" <?php echo ($userDetails['civil_status'] ?? '') === 'Separated' ? 'selected' : ''; ?>>Separated</option>

                        </select>
                    </div>
                    <div>
                        <label for="occupation" class="block mb-1 font-medium">Occupation</label>
                        <select name="occupation" class="border rounded-lg p-3 w-full uppercase" required>
                            <option value="">OCUPATION *</option>
                            <option value="Student" <?php echo ($userDetails['occupation'] ?? '') === 'Student' ? 'selected' : ''; ?>>Student</option>
                            <option value="Farmer" <?php echo ($userDetails['occupation'] ?? '') === 'Farmer' ? 'selected' : ''; ?>>Farmer</option>
                            <option value="Teacher" <?php echo ($userDetails['occupation'] ?? '') === 'Teacher' ? 'selected' : ''; ?>>Teacher</option>
                            <option value="Government Employee" <?php echo ($userDetails['occupation'] ?? '') === 'Government Employee' ? 'selected' : ''; ?>>Government Employee</option>
                            <option value="Private Employee" <?php echo ($userDetails['occupation'] ?? '') === 'Private Employee' ? 'selected' : ''; ?>>Private Employee</option>
                            <option value="Business Owner" <?php echo ($userDetails['occupation'] ?? '') === 'Business Owner' ? 'selected' : ''; ?>>Business Owner</option>
                            <option value="Unemployed" <?php echo ($userDetails['occupation'] ?? '') === 'Unemployed' ? 'selected' : ''; ?>>Unemployed</option>
                        </select>
                    </div>
                    <div>
                        <label for="nationality" class="block mb-1 font-medium">Nationality</label>
                        <select name="nationality" class="border rounded-lg p-3 w-full uppercase" required>
                            <option value="">NATIONALITY *</option>
                            <option value="Filipino" <?php echo ($userDetails['nationality'] ?? '') === 'Filipino' ? 'selected' : ''; ?>>Filipino</option>
                            <option value="American" <?php echo ($userDetails['nationality'] ?? '') === 'American' ? 'selected' : ''; ?>>American</option>
                            <option value="Chinese" <?php echo ($userDetails['nationality'] ?? '') === 'Chinese' ? 'selected' : ''; ?>>Chinese</option>
                            <option value="Japanese" <?php echo ($userDetails['nationality'] ?? '') === 'Japanese' ? 'selected' : ''; ?>>Japanese</option>
                            <option value="Korean" <?php echo ($userDetails['nationality'] ?? '') === 'Korean' ? 'selected' : ''; ?>>Korean</option>
                            <option value="Indian" <?php echo ($userDetails['nationality'] ?? '') === 'Indian' ? 'selected' : ''; ?>>Indian</option>
                        </select>
                    </div>
                    <div>
                        <label for="religion" class="block mb-1 font-medium">Religion</label>
                        <select name="religion" class="border rounded-lg p-3 w-full uppercase" required>
                            <option value="">RELIGION *</option>
                            <option value="Roman Catholic" <?php echo ($userDetails['religion'] ?? '') === 'Roman Catholic' ? 'selected' : ''; ?>>Roman Catholic</option>
                            <option value="Christian" <?php echo ($userDetails['religion'] ?? '') === 'Christian' ? 'selected' : ''; ?>>Christian</option>
                            <option value="Iglesia ni Cristo" <?php echo ($userDetails['religion'] ?? '') === 'Iglesia ni Cristo' ? 'selected' : ''; ?>>Iglesia ni Cristo</option>
                            <option value="Islam" <?php echo ($userDetails['religion'] ?? '') === 'Islam' ? 'selected' : ''; ?>>Islam</option>
                            <option value="Buddhism" <?php echo ($userDetails['religion'] ?? '') === 'Buddhism' ? 'selected' : ''; ?>>Buddhism</option>
                            <option value="Hinduism" <?php echo ($userDetails['religion'] ?? '') === 'Hinduism' ? 'selected' : ''; ?>>Hinduism</option>
                            <option value="Judaism" <?php echo ($userDetails['religion'] ?? '') === 'Judaism' ? 'selected' : ''; ?>>Judaism</option>
                            <option value="Muslim" <?php echo ($userDetails['religion'] ?? '') === 'Muslim' ? 'selected' : ''; ?>>Muslim</option>
                        </select>
                    </div>
                    <div>
                        <label for="blood_type" class="block mb-1 font-medium">Blood Type</label>
                        <select name="blood_type" class="border rounded-lg p-3 w-full">
                            <option value="">BLOOD TYPE</option>
                            <option value="A+" <?php echo ($userDetails['blood_type'] ?? '') === 'A+' ? 'selected' : ''; ?>>A+</option>
                            <option value="A-" <?php echo ($userDetails['blood_type'] ?? '') === 'A-' ? 'selected' : ''; ?>>A-</option>
                            <option value="B+" <?php echo ($userDetails['blood_type'] ?? '') === 'B+' ? 'selected' : ''; ?>>B+</option>
                            <option value="B-" <?php echo ($userDetails['blood_type'] ?? '') === 'B-' ? 'selected' : ''; ?>>B-</option>
                            <option value="O+" <?php echo ($userDetails['blood_type'] ?? '') === 'O+' ? 'selected' : ''; ?>>O+</option>
                            <option value="O-" <?php echo ($userDetails['blood_type'] ?? '') === 'O-' ? 'selected' : ''; ?>>O-</option>
                            <option value="AB+" <?php echo ($userDetails['blood_type'] ?? '') === 'AB+' ? 'selected' : ''; ?>>AB+</option>
                            <option value="AB-" <?php echo ($userDetails['blood_type'] ?? '') === 'AB-' ? 'selected' : ''; ?>>AB-</option>
                        </select>
                    </div>
                    <div>
                        <label for="educational_attainment" class="block mb-1 font-medium">Educational
                            Attainment</label>
                        <select name="educational_attainment" class="border rounded-lg p-3 w-full">
                            <option value="">EDUCATION ATTAINMENT</option>
                            <option value="ELEMENTARY" <?php echo ($userDetails['educational_attainment'] ?? '') === 'ELEMENTARY' ? 'selected' : ''; ?>>ELEMENTARY</option>
                            <option value="HIGH SCHOOL" <?php echo ($userDetails['educational_attainment'] ?? '') === 'HIGH SCHOOL' ? 'selected' : ''; ?>>HIGH SCHOOL</option>
                            <option value="COLLEGE" <?php echo ($userDetails['educational_attainment'] ?? '') === 'COLLEGE' ? 'selected' : ''; ?>>COLLEGE</option>
                            <option value="VOCATIONAL" <?php echo ($userDetails['educational_attainment'] ?? '') === 'VOCATIONAL' ? 'selected' : ''; ?>>VOCATIONAL</option>
                            <option value="POSTGRADUATE" <?php echo ($userDetails['educational_attainment'] ?? '') === 'POSTGRADUATE' ? 'selected' : ''; ?>>POSTGRADUATE</option>
                        </select>
                    </div>
                    <div>
                        <label for="voter_status" class="block mb-1 font-medium">Voter Status</label>
                        <select name="voter_status" class="border rounded-lg p-3 w-full" required>
                            <option value="">VOTER STATUS *</option>
                            <option value="YES" <?php echo ($userDetails['voter_status'] ?? '') === 'Yes' ? 'selected' : ''; ?>>YES</option>
                            <option value="NO" <?php echo ($userDetails['voter_status'] ?? '') === 'No' ? 'selected' : ''; ?>>NO</option>
                        </select>
                    </div>
                </div>
            </section>

            <!-- ✅ Birth Info -->
            <section class="mb-8 bg-gray-50 p-6 rounded-xl shadow-sm">
                <h2 class="text-xl font-semibold text-indigo-500 border-b pb-2 mb-4">Birth Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="birth_date" class="block mb-1 font-medium">Birth Date</label>
                        <input type="date" name="birth_date"
                            value="<?= htmlspecialchars($userDetails['birth_date'] ?? '') ?>"
                            class="border p-2 rounded">
                    </div>
                    <div>
                        <label for="birth_place" class="block mb-1 font-medium">Birth Place</label>
                        <input type="text" name="birth_place"
                            value="<?= htmlspecialchars($userDetails['birth_place'] ?? '') ?>" placeholder="Birth Place"
                            class="border p-2 rounded">
                    </div>
                </div>
            </section>

            <!-- ✅ Residency -->
<section class="mb-8 bg-gray-50 p-6 rounded-xl shadow-sm">
    <h2 class="text-xl font-semibold text-indigo-500 border-b pb-2 mb-4">Residency</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="house_no" class="block mb-1 font-medium">House No.</label>
            <input type="text" id="house_no" name="house_no"
                value="<?= htmlspecialchars($userDetails['house_no'] ?? '') ?>" placeholder="House No"
                class="border p-2 rounded w-full">
        </div>

        <div>
            <label for="purok" class="block mb-1 font-medium">Purok</label>
            <input type="text" id="purok" name="purok"
                value="<?= htmlspecialchars($userDetails['purok'] ?? '') ?>" placeholder="Purok"
                class="border p-2 rounded w-full">
        </div>

        <div>
            <label for="municipality" class="block mb-1 font-medium">Municipality</label>
            <input type="text" id="municipality" name="municipality" value="TALAVERA"
                class="border rounded p-2 w-full uppercase" readonly>
        </div>

        <div>
            <label for="province" class="block mb-1 font-medium">Province</label>
            <input type="text" id="province" name="province" value="NUEVA ECIJA"
                class="border rounded p-2 w-full uppercase" readonly>
        </div>

        <div>
            <label for="years_residency" class="block mb-1 font-medium">Years of Residency</label>
            <input type="number" id="years_residency" name="years_residency"
                value="<?= htmlspecialchars($userDetails['years_residency'] ?? '') ?>"
                placeholder="Years of Residency" class="border rounded p-2 w-full">
        </div>

        <div>
            <label for="household_head" class="block mb-1 font-medium">Household Head</label>
            <select id="household_head" name="household_head" class="border rounded p-2 w-full" required>
                <option value="">ARE YOU HOUSEHOLD HEAD? *</option>
                <option value="YES" <?= ($userDetails['household_head'] ?? '') === 'Yes' ? 'selected' : ''; ?>>YES</option>
                <option value="NO" <?= ($userDetails['household_head'] ?? '') === 'No' ? 'selected' : ''; ?>>NO</option>
            </select>
        </div>

        <div>
            <label for="house_type" class="block mb-1 font-medium">House Type</label>
            <select id="house_type" name="house_type" class="border rounded p-2 w-full" required>
                <option value="">HOUSE TYPE *</option>
                <option value="HOUSE" <?= ($userDetails['house_type'] ?? '') === 'HOUSE' ? 'selected' : ''; ?>>HOUSE</option>
                <option value="APARTMENT" <?= ($userDetails['house_type'] ?? '') === 'APARTMENT' ? 'selected' : ''; ?>>APARTMENT</option>
                <option value="DORMITORY" <?= ($userDetails['house_type'] ?? '') === 'DORMITORY' ? 'selected' : ''; ?>>DORMITORY</option>
                <option value="OTHER" <?= ($userDetails['house_type'] ?? '') === 'OTHER' ? 'selected' : ''; ?>>OTHER</option>
            </select>
        </div>

        <div>
            <label for="ownership_status" class="block mb-1 font-medium">Ownership Status</label>
            <select id="ownership_status" name="ownership_status" class="border rounded p-2 w-full" required>
                <option value="">OWNERSHIP STATUS *</option>
                <option value="OWNED" <?= ($userDetails['ownership_status'] ?? '') === 'OWNED' ? 'selected' : ''; ?>>OWNED</option>
                <option value="RENTED" <?= ($userDetails['ownership_status'] ?? '') === 'RENTED' ? 'selected' : ''; ?>>RENTED</option>
                <option value="LIVING WITH RELATIVES" <?= ($userDetails['ownership_status'] ?? '') === 'LIVING WITH RELATIVES' ? 'selected' : ''; ?>>LIVING WITH RELATIVES</option>
            </select>
        </div>

        <div class="sm:col-span-2">
            <label for="previous_address" class="block mb-1 font-medium">Previous Address (if any)</label>
            <input type="text" id="previous_address" name="previous_address"
                value="<?= htmlspecialchars($userDetails['previous_address'] ?? '') ?>"
                placeholder="Previous Address (if any)" class="border rounded p-2 w-full uppercase">
        </div>
    </div>
</section>


            <!-- ✅ Family Info -->
            <section class="mb-8 bg-gray-50 p-6 rounded-xl shadow-sm">
                <h2 class="text-xl font-semibold text-indigo-500 border-b pb-2 mb-4">Family Information</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Father's Name -->
                    <div>
                        <label for="fathers_name" class="block text-sm font-medium text-gray-700 mb-1">Father's
                            Name</label>
                        <input type="text" id="fathers_name" name="fathers_name"
                            value="<?= htmlspecialchars($userDetails['fathers_name'] ?? '') ?>"
                            placeholder="Father's Name" class="border p-2 rounded w-full">
                    </div>
                    <div>
                        <label for="fathers_birthplace" class="block text-sm font-medium text-gray-700 mb-1">Father's
                            Birthplace</label>
                    <input type="text" name="fathers_birthplace"
                            value="<?php echo strtoupper($userDetails['fathers_birthplace'] ?? ''); ?>"
                            placeholder="FATHER'S BIRTHPLACE *"
                            class="border rounded p-2 w-full uppercase" required>
                    </div>
                    <!-- Mother's Name -->
                    <div>
                        <label for="mothers_name" class="block text-sm font-medium text-gray-700 mb-1">Mother's
                            Name</label>
                        <input type="text" id="mothers_name" name="mothers_name"
                            value="<?= htmlspecialchars($userDetails['mothers_name'] ?? '') ?>"
                            placeholder="Mother's Name" class="border p-2 rounded w-full">
                    </div>
                    <div>
                        <label for="mothers_birthplace" class="block text-sm font-medium text-gray-700 mb-1">Mother's
                            Birthplace</label>
                     <input type="text" name="mothers_birthplace"
                            value="<?php echo strtoupper($userDetails['mothers_birthplace'] ?? ''); ?>"
                            placeholder="MOTHER'S BIRTHPLACE *"
                            class="border rounded p-2 w-full uppercase" required>
                    </div>
                    <!-- Show spouse and dependents only if not SINGLE -->
                    <?php if (($userDetails['civil_status'] ?? '') !== 'Single'): ?>
                        <div>
                            <label for="spouse_name" class="block text-sm font-medium text-gray-700 mb-1">Spouse
                                Name</label>
                            <input type="text" id="spouse_name" name="spouse_name"
                                value="<?= strtoupper($userDetails['spouse_name'] ?? '') ?>" placeholder="Spouse Name"
                                class="border rounded p-2 w-full uppercase">
                        </div>

                        <div>
                            <label for="num_dependents" class="block text-sm font-medium text-gray-700 mb-1">Number of
                                Dependents</label>
                            <input type="number" id="num_dependents" name="num_dependents"
                                value="<?= $userDetails['num_dependents'] ?? '' ?>" placeholder="Dependents"
                                class="border rounded p-2 w-full">
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Emergency Contacts -->
                <div class="mt-4">
                    <h3 class="text-lg font-semibold mb-3">Emergency Contact</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Contact Person -->
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-1">Emergency
                                Contact Person *</label>
                            <input type="text" id="contact_person" name="contact_person"
                                value="<?= strtoupper($userDetails['contact_person'] ?? '') ?>"
                                placeholder="Emergency Contact Person" class="border rounded p-2 w-full uppercase"
                                required>
                        </div>

                        <!-- Contact Number -->
                        <div>
                            <label for="emergency_contact_no"
                                class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Number *</label>
                            <div class="flex">
                                <span
                                    class="inline-flex items-center px-3 border border-r-0 rounded-l bg-gray-100">+63</span>
                                <input type="text" id="emergency_contact_no" name="emergency_contact_no"
                                    value="<?= $userDetails['emergency_contact_no'] ?? '' ?>" placeholder="9123456789"
                                    class="border rounded-r-lg p-2 w-full" pattern="[0-9]{10}" maxlength="10" required>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <!-- ✅ Health Info -->
            <section class="mb-8 bg-gray-50 p-6 rounded-xl shadow-sm">
                <h2 class="text-xl font-semibold text-indigo-500 border-b pb-2 mb-4">Health Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- Health Condition -->
                    <div>
                        <label for="health_condition" class="block text-sm font-medium text-gray-700 mb-1">Health
                            Condition *</label>
                        <select id="health_condition" name="health_condition" class="border rounded p-2 w-full"
                            required>
                            <option value="">Select Health Condition</option>
                            <option value="Healthy" <?= ($userDetails['health_condition'] ?? '') === 'Healthy' ? 'selected' : ''; ?>>Healthy</option>
                            <option value="Minor Illness" <?= ($userDetails['health_condition'] ?? '') === 'Minor Illness' ? 'selected' : ''; ?>>Minor Illness</option>
                            <option value="Chronic Illness" <?= ($userDetails['health_condition'] ?? '') === 'Chronic Illness' ? 'selected' : ''; ?>>Chronic Illness</option>
                            <option value="Disabled" <?= ($userDetails['health_condition'] ?? '') === 'Disabled' ? 'selected' : ''; ?>>Disabled</option>
                        </select>
                    </div>

                    <!-- Common Issues -->
                    <div>
                        <label for="common_health_issue" class="block text-sm font-medium text-gray-700 mb-1">Common
                            Health Issue *</label>
                        <select id="common_health_issue" name="common_health_issue" class="border rounded p-2 w-full"
                            required>
                            <option value="">Select Common Issue</option>
                            <option value="None" <?= ($userDetails['common_health_issue'] ?? '') === 'None' ? 'selected' : ''; ?>>None</option>
                            <option value="Diabetes" <?= ($userDetails['common_health_issue'] ?? '') === 'Diabetes' ? 'selected' : ''; ?>>Diabetes</option>
                            <option value="Hypertension" <?= ($userDetails['common_health_issue'] ?? '') === 'Hypertension' ? 'selected' : ''; ?>>Hypertension</option>
                            <option value="Asthma" <?= ($userDetails['common_health_issue'] ?? '') === 'Asthma' ? 'selected' : ''; ?>>Asthma</option>
                            <option value="Heart Disease" <?= ($userDetails['common_health_issue'] ?? '') === 'Heart Disease' ? 'selected' : ''; ?>>Heart Disease</option>
                        </select>
                    </div>

                    <!-- PWD Status -->
                    <div>
                        <label for="pwd_status" class="block text-sm font-medium text-gray-700 mb-1">PWD Status
                            *</label>
                        <select id="pwd_status" name="pwd_status" class="border rounded p-2 w-full" required>
                            <option value="">Select PWD Status</option>
                            <option value="Yes" <?= ($userDetails['pwd_status'] ?? '') === 'Yes' ? 'selected' : ''; ?>>Yes
                            </option>
                            <option value="No" <?= ($userDetails['pwd_status'] ?? '') === 'No' ? 'selected' : ''; ?>>No
                            </option>
                        </select>
                    </div>

                    <!-- Senior Citizen -->
                    <div>
                        <label for="senior_citizen_status" class="block text-sm font-medium text-gray-700 mb-1">Senior
                            Citizen *</label>
                        <select id="senior_citizen_status" name="senior_citizen_status"
                            class="border rounded p-2 w-full" required>
                            <option value="">Select Senior Citizen Status</option>
                            <option value="Yes" <?= ($userDetails['senior_citizen_status'] ?? '') === 'Yes' ? 'selected' : ''; ?>>Yes</option>
                            <option value="No" <?= ($userDetails['senior_citizen_status'] ?? '') === 'No' ? 'selected' : ''; ?>>No</option>
                        </select>
                    </div>

                    <!-- Vaccination -->
                    <div>
                        <label for="vaccination_status" class="block text-sm font-medium text-gray-700 mb-1">Vaccination
                            Status *</label>
                        <select id="vaccination_status" name="vaccination_status" class="border rounded p-2 w-full"
                            required>
                            <option value="">Select Vaccination Status</option>
                            <option value="Not Vaccinated" <?= ($userDetails['vaccination_status'] ?? '') === 'Not Vaccinated' ? 'selected' : ''; ?>>Not Vaccinated</option>
                            <option value="Partially Vaccinated" <?= ($userDetails['vaccination_status'] ?? '') === 'Partially Vaccinated' ? 'selected' : ''; ?>>Partially Vaccinated</option>
                            <option value="Fully Vaccinated" <?= ($userDetails['vaccination_status'] ?? '') === 'Fully Vaccinated' ? 'selected' : ''; ?>>Fully Vaccinated</option>
                            <option value="Boostered" <?= ($userDetails['vaccination_status'] ?? '') === 'Boostered' ? 'selected' : ''; ?>>Boostered</option>
                        </select>
                    </div>

                    <!-- Height -->
                    <div>
                        <label for="height_cm" class="block text-sm font-medium text-gray-700 mb-1">Height (cm)
                            *</label>
                        <input type="number" step="0.1" id="height_cm" name="height_cm"
                            value="<?= $userDetails['height_cm'] ?? ''; ?>" placeholder="Height in cm"
                            class="border rounded p-2 w-full uppercase" required>
                    </div>

                    <!-- Weight -->
                    <div>
                        <label for="weight_kg" class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)
                            *</label>
                        <input type="number" step="0.1" id="weight_kg" name="weight_kg"
                            value="<?= $userDetails['weight_kg'] ?? ''; ?>" placeholder="Weight in kg"
                            class="border rounded p-2 w-full uppercase" required>
                    </div>

                    <!-- Last Checkup -->
                    <div class="sm:col-span-2">
                        <label for="last_medical_checkup" class="block text-sm font-medium text-gray-700 mb-1">Last
                            Medical Checkup</label>
                        <input type="date" id="last_medical_checkup" name="last_medical_checkup"
                            value="<?= $userDetails['last_medical_checkup'] ?? ''; ?>"
                            class="border rounded p-2 w-full">
                    </div>

                    <!-- Remarks -->
                    <div class="sm:col-span-2">
                        <label for="health_remarks" class="block text-sm font-medium text-gray-700 mb-1">Additional
                            Health Remarks</label>
                        <textarea id="health_remarks" name="health_remarks" class="border rounded p-2 w-full uppercase"
                            rows="3"
                            placeholder="Additional health remarks..."><?= strtoupper($userDetails['health_remarks'] ?? ''); ?></textarea>
                    </div>
                </div>
            </section>


            <!-- ✅ Income Info -->
            <section class="mb-8 bg-gray-50 p-6 rounded-xl shadow-sm">
                <h2 class="text-xl font-semibold text-indigo-500 border-b pb-2 mb-4">Income Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <!-- Monthly Income -->
                    <div>
                        <label for="monthly_income" class="block text-sm font-medium text-gray-700 mb-1">Monthly
                            Income</label>
                        <input type="text" id="monthly_income" name="monthly_income"
                            value="<?= htmlspecialchars($userDetails['monthly_income'] ?? '') ?>"
                            placeholder="Monthly Income" class="border p-2 rounded w-full">
                    </div>

                    <!-- Primary Income Source -->
                    <div>
                        <label for="income_source" class="block text-sm font-medium text-gray-700 mb-1">Primary Income
                            Source *</label>
                        <select id="income_source" name="income_source" class="border rounded p-2 w-full uppercase"
                            required>
                            <option value="">Select Primary Income Source</option>
                            <option value="Employment" <?= ($userDetails['income_source'] ?? '') === 'Employment' ? 'selected' : ''; ?>>Employment</option>
                            <option value="Business" <?= ($userDetails['income_source'] ?? '') === 'Business' ? 'selected' : ''; ?>>Business</option>
                            <option value="Farming" <?= ($userDetails['income_source'] ?? '') === 'Farming' ? 'selected' : ''; ?>>Farming</option>
                            <option value="Remittance" <?= ($userDetails['income_source'] ?? '') === 'Remittance' ? 'selected' : ''; ?>>Remittance</option>
                            <option value="Government Aid" <?= ($userDetails['income_source'] ?? '') === 'Government Aid' ? 'selected' : ''; ?>>Government Aid</option>
                            <option value="None" <?= ($userDetails['income_source'] ?? '') === 'None' ? 'selected' : ''; ?>>None</option>
                        </select>
                    </div>

                    <!-- Household Members -->
                    <div>
                        <label for="household_members" class="block text-sm font-medium text-gray-700 mb-1">Total
                            Household Members *</label>
                        <input type="number" id="household_members" name="household_members"
                            value="<?= $userDetails['household_members'] ?? ''; ?>"
                            placeholder="Total Household Members" class="border rounded p-2 w-full uppercase" required>
                    </div>

                    <!-- Additional Sources (Optional) -->
                    <div>
                        <label for="additional_income_sources"
                            class="block text-sm font-medium text-gray-700 mb-1">Additional Income Sources
                            (Optional)</label>
                        <input type="text" id="additional_income_sources" name="additional_income_sources"
                            value="<?= $userDetails['additional_income_sources'] ?? ''; ?>"
                            placeholder="Additional Income Sources" class="border rounded p-2 w-full uppercase">
                    </div>

                    <!-- Household Head Occupation (Optional) -->
                    <div>
                        <label for="household_head_occupation"
                            class="block text-sm font-medium text-gray-700 mb-1">Household Head Occupation
                            (Optional)</label>
                        <input type="text" id="household_head_occupation" name="household_head_occupation"
                            value="<?= $userDetails['household_head_occupation'] ?? ''; ?>"
                            placeholder="Household Head Occupation" class="border rounded p-2 w-full uppercase">
                    </div>
                </div>

                <!-- Upload Income Proof -->
                <div class="mt-4">
                    <label for="income_proof" class="block font-semibold">Upload Income Proof</label>
                    <?php if (!empty($userDetails['income_proof'])): ?>
                        <img src="<?= '/Barangay_Information_System/uploads/income/' . basename($userDetails['income_proof']); ?>"
                            class="w-40 h-auto rounded mb-2">
                        <input type="hidden" name="old_income_proof"
                            value="<?= htmlspecialchars($userDetails['income_proof']); ?>">
                    <?php endif; ?>
                    <input type="file" id="income_proof" name="income_proof" class="border p-2 rounded w-full">
                </div>
            </section>


            <!-- ✅ Identity Docs -->
            <section class="mb-8 bg-gray-50 p-6 rounded-xl shadow-sm">
                <h2 class="text-2xl font-semibold text-indigo-500 border-b pb-2 mb-4">Identity Documents</h2>
                <div class="mb-4">
                    <label class="block mb-1 mt-2 font-medium text-sm">TYPE OF VALID ID *</label>
                    <select name="id_type" class="border rounded p-2 w-full" required>
                        <option value="">Select ID Type</option>
                        <option value="PhilHealth ID" <?= ($userDetails['id_type'] ?? '') === 'PhilHealth ID' ? 'selected' : ''; ?>>PhilHealth ID</option>
                        <option value="SSS ID" <?= ($userDetails['id_type'] ?? '') === 'SSS ID' ? 'selected' : ''; ?>>SSS
                            ID</option>
                        <option value="TIN ID" <?= ($userDetails['id_type'] ?? '') === 'TIN ID' ? 'selected' : ''; ?>>TIN
                            ID</option>
                        <option value="Driver's License" <?= ($userDetails['id_type'] ?? '') === "Driver's License" ? 'selected' : ''; ?>>Driver's License</option>
                        <option value="UMID" <?= ($userDetails['id_type'] ?? '') === 'UMID' ? 'selected' : ''; ?>>UMID
                        </option>
                        <option value="Voter's ID" <?= ($userDetails['id_type'] ?? '') === "Voter's ID" ? 'selected' : ''; ?>>Voter's ID</option>
                        <option value="Postal ID" <?= ($userDetails['id_type'] ?? '') === 'Postal ID' ? 'selected' : ''; ?>>Postal ID</option>
                        <option value="National ID" <?= ($userDetails['id_type'] ?? '') === 'National ID' ? 'selected' : ''; ?>>National ID</option>
                        <option value="Student ID" <?= ($userDetails['id_type'] ?? '') === 'Student ID' ? 'selected' : ''; ?>>Student ID</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- Front ID -->
                    <div>
                        <label class="block font-semibold">Front ID</label>
                        <?php if (!empty($userDetails['front_valid_id_path'])): ?>
                            <img src="<?= '/Barangay_Information_System/uploads/ids/front/' . basename($userDetails['front_valid_id_path']); ?>"
                                class="mx-auto w-full max-w-xs h-auto rounded shadow mb-2">
                            <input type="hidden" name="old_front_valid_id"
                                value="<?= htmlspecialchars($userDetails['front_valid_id_path']); ?>">
                        <?php else: ?>
                            <p class="text-gray-500 italic mb-2">Not uploaded</p>
                        <?php endif; ?>
                        <input type="file" name="front_valid_id" class="border p-2 rounded w-full">
                    </div>

                    <!-- Back ID -->
                    <div>
                        <label class="block font-semibold">Back ID</label>
                        <?php if (!empty($userDetails['back_valid_id_path'])): ?>
                            <img src="<?= '/Barangay_Information_System/uploads/ids/back/' . basename($userDetails['back_valid_id_path']); ?>"
                                class="mx-auto w-full max-w-xs h-auto rounded shadow mb-2">
                            <input type="hidden" name="old_back_valid_id"
                                value="<?= htmlspecialchars($userDetails['back_valid_id_path']); ?>">
                        <?php else: ?>
                            <p class="text-gray-500 italic mb-2">Not uploaded</p>
                        <?php endif; ?>
                        <input type="file" name="back_valid_id" class="border p-2 rounded w-full">
                    </div>

                    <!-- Selfie with ID -->
                    <div>
                        <label class="block font-semibold">Selfie with ID</label>
                        <?php if (!empty($userDetails['selfie_with_id'])): ?>
                            <img src="<?= '/Barangay_Information_System/uploads/ids/selfie/' . basename($userDetails['selfie_with_id']); ?>"
                                class="mx-auto w-full max-w-xs h-auto rounded shadow mb-2">
                            <input type="hidden" name="old_selfie_with_id"
                                value="<?= htmlspecialchars($userDetails['selfie_with_id']); ?>">
                        <?php else: ?>
                            <p class="text-gray-500 italic mb-2">Not uploaded</p>
                        <?php endif; ?>
                        <input type="file" name="selfie_with_id" class="border p-2 rounded w-full">
                    </div>
                </div>
            </section>

            <!-- ✅ Buttons -->
            <div class="flex justify-center space-x-4 mt-8">
                <a href="pending.php" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Cancel</a>
                <button type="submit"
                    class="px-6 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">Save</button>
            </div>
        </form>
    </div>

</body>

</html>