<section class="bg-gray-50 p-6 rounded-xl shadow-sm">
    <h3 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center gap-2">
        <i class="fa-solid fa-coins"></i> Income Information
    </h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <!-- Monthly Income -->
        <input type="number" name="monthly_income" placeholder="Monthly Income"
            class="border rounded p-2 w-full uppercase" required>

        <!-- Source of Income -->
        <select name="income_source" class="border rounded p-2 w-full uppercase" required>
            <option value="">Select Income Source</option>
            <option value="Employment">Employment</option>
            <option value="Business">Business</option>
            <option value="Farming">Farming</option>
            <option value="Remittance">Remittance</option>
            <option value="Government Aid">Government Aid</option>
            <option value="None">None</option>
        </select>

        <!-- Household Head Occupation -->
        <input type="text" name="household_head_occupation" placeholder="Household Head Occupation"
            class="border rounded p-2 w-full uppercase">

        <!-- Household Members -->
        <input type="number" name="household_members" placeholder="Number of Household Members"
            class="border rounded p-2 w-full uppercase">

        <!-- Additional Income Sources -->
        <input type="text" name="additional_income_sources" placeholder="Additional Income Sources"
            class="border rounded p-2 w-full uppercase">

        <!-- Remarks -->
        <textarea name="income_remarks" placeholder="Remarks or Notes"
            class="border rounded p-2 w-full uppercase sm:col-span-2"></textarea>

        <!-- Proof of Income Upload -->
        <div class="sm:col-span-2">
            <label class="block text-gray-700 font-medium mb-1">Proof of Income</label>
            <input type="file" name="income_proof" accept="image/*" class="file-input">
        </div>
    </div>
</section>