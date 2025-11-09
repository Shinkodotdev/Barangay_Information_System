<section class="bg-gray-50 p-6 rounded-xl shadow-sm">
    <h3 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center gap-2">
        <i class="fa-solid fa-house"></i> RESIDENCY INFORMATION
    </h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <input name="house_no" placeholder="HOUSE NO." class="border rounded-lg p-3 w-full uppercase">

        <select name="purok" class="border rounded-lg p-3 w-full uppercase">
            <option value="">SELECT PUROK</option>
            <option>PUROK 1</option>
            <option>PUROK 2</option>
            <option>PUROK 3</option>
            <option>PUROK 4</option>
            <option>PUROK 5</option>
            <option>PUROK 6</option>
            <option>PUROK 7A</option>
            <option>PUROK 7B</option>
        </select>

        <input name="barangay" value="POBLACION SUR" class="border rounded-lg p-3 w-full uppercase" readonly>
        <input name="municipality" value="TALAVERA" class="border rounded-lg p-3 w-full uppercase" readonly>
        <input name="province" value="NUEVA ECIJA" class="border rounded-lg p-3 w-full uppercase" readonly>

        <input name="years_residency" type="number" placeholder="YEARS OF RESIDENCY" class="border rounded-lg p-3 w-full uppercase">

        <select name="household_head" class="border rounded p-3 w-full uppercase" required>
            <option value="">ARE YOU HOUSEHOLD HEAD? *</option>
            <option value="YES">YES</option>
            <option value="NO">NO</option>
        </select>

        <select name="house_type" class="border rounded p-3 w-full uppercase" required>
            <option value="">HOUSE TYPE *</option>
            <option value="HOUSE">HOUSE</option>
            <option value="APARTMENT">APARTMENT</option>
            <option value="DORMITORY">DORMITORY</option>
            <option value="OTHER">OTHER</option>
        </select>

        <select name="ownership_status" class="border rounded p-3 w-full uppercase" required>
            <option value="">OWNERSHIP STATUS *</option>
            <option value="OWNED">OWNED</option>
            <option value="RENTED">RENTED</option>
            <option value="LIVING WITH RELATIVES">LIVING WITH RELATIVES</option>
        </select>

        <input name="previous_address" placeholder="PREVIOUS ADDRESS" class="border rounded-lg p-3 w-full uppercase">
    </div>
</section>
