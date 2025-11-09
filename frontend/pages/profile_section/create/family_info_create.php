<!-- ✅ FAMILY INFO -->
<section class="bg-gray-50 p-6 rounded-xl shadow-sm">
    <h3 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center gap-2">
        <i class="fa-solid fa-people-roof"></i> FAMILY INFORMATION
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <input name="fathers_name" placeholder="FATHER'S NAME" class="border rounded-lg p-3 w-full uppercase">
        <input name="fathers_birthplace" placeholder="FATHER'S BIRTHPLACE" class="border rounded-lg p-3 w-full uppercase">
        <input name="mothers_name" placeholder="MOTHER'S NAME" class="border rounded-lg p-3 w-full uppercase">
        <input name="mothers_birthplace" placeholder="MOTHER'S BIRTHPLACE" class="border rounded-lg p-3 w-full uppercase">

        <!-- 🟣 SPOUSE (Hidden by default) -->
        <div id="spouseField" class="hidden">
            <input name="spouse_name" placeholder="SPOUSE NAME" class="border rounded-lg p-3 w-full uppercase">
        </div>

        <input type="number" name="num_dependents" placeholder="NUMBER OF DEPENDENTS" class="border rounded-lg p-3 w-full uppercase">
    </div>

    <!-- 🆘 Emergency Contacts -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-3 text-indigo-600 flex items-center gap-2">
            <i class="fa-solid fa-phone"></i> EMERGENCY CONTACT
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <input type="text" name="contact_person" placeholder="EMERGENCY CONTACT PERSON *" class="border rounded-lg p-3 w-full uppercase" required>

            <div class="flex">
                <span class="inline-flex items-center px-3 border border-r-0 rounded-l-lg bg-gray-100 text-gray-600">+63</span>
                <input type="text" name="emergency_contact_no" placeholder="9123456789" class="border rounded-r-lg p-3 w-full" pattern="[0-9]{10}" maxlength="10" required>
            </div>
        </div>
    </div>
</section>

<script>
// 🟢 Show spouse name only if civil status is not SINGLE
document.addEventListener("DOMContentLoaded", () => {
    const civilStatusSelect = document.querySelector("select[name='civil_status']");
    const spouseField = document.getElementById("spouseField");

    if (civilStatusSelect) {
        const toggleSpouseField = () => {
            const value = civilStatusSelect.value.trim().toUpperCase();
            if (value === "SINGLE" || value === "") {
                spouseField.classList.add("hidden");
                spouseField.querySelector("input").value = "";
            } else {
                spouseField.classList.remove("hidden");
            }
        };

        civilStatusSelect.addEventListener("change", toggleSpouseField);
        toggleSpouseField(); // Initialize on load
    }
});
</script>
