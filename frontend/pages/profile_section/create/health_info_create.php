<!-- ✅ HEALTH INFORMATION -->
<section class="bg-gray-50 p-6 rounded-xl shadow-sm">
    <h3 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center gap-2">
        <i class="fa-solid fa-heart-pulse"></i> HEALTH INFORMATION
    </h3>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <select name="health_condition" class="border rounded-lg p-3 w-full uppercase" required>
            <option value="">HEALTH CONDITION *</option>
            <option value="HEALTHY">HEALTHY</option>
            <option value="MINOR ILLNESS">MINOR ILLNESS</option>
            <option value="CHRONIC ILLNESS">CHRONIC ILLNESS</option>
            <option value="DISABLED">DISABLED</option>
        </select>

        <select name="common_health_issue" class="border rounded-lg p-3 w-full uppercase" required>
            <option value="">COMMON HEALTH ISSUE *</option>
            <option value="NONE">NONE</option>
            <option value="DIABETES">DIABETES</option>
            <option value="HYPERTENSION">HYPERTENSION</option>
            <option value="ASTHMA">ASTHMA</option>
            <option value="HEART DISEASE">HEART DISEASE</option>
        </select>

        <select name="vaccination_status" class="border rounded-lg p-3 w-full uppercase" required>
            <option value="">VACCINATION STATUS *</option>
            <option value="NOT VACCINATED">NOT VACCINATED</option>
            <option value="PARTIALLY VACCINATED">PARTIALLY VACCINATED</option>
            <option value="FULLY VACCINATED">FULLY VACCINATED</option>
            <option value="BOOSTERED">BOOSTERED</option>
        </select>

        <input name="height_cm" type="number" placeholder="HEIGHT (CM)" class="border rounded-lg p-3 w-full uppercase">
        <input name="weight_kg" type="number" placeholder="WEIGHT (KG)" class="border rounded-lg p-3 w-full uppercase">
        <input type="date" name="last_medical_checkup" class="border rounded-lg p-3 w-full uppercase">
        <textarea name="health_remarks" placeholder="HEALTH REMARKS" class="border rounded-lg p-3 w-full uppercase sm:col-span-2"></textarea>
    </div>
</section>
