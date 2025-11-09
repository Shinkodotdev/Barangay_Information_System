<!-- ✅ IDENTITY INFORMATION -->
<section class="bg-gray-50 p-6 rounded-xl shadow-sm">
    <h3 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center gap-2">
        <i class="fa-solid fa-id-card"></i> IDENTITY INFORMATION
    </h3>
<select name="id_type" class="border rounded-lg p-3 w-full uppercase" required>
            <option value="">SELECT ID TYPE *</option>
            <option value="PHILHEALTH ID">PHILHEALTH ID</option>
            <option value="SSS ID">SSS ID</option>
            <option value="TIN ID">TIN ID</option>
            <option value="DRIVER'S LICENSE">DRIVER'S LICENSE</option>
            <option value="UMID">UMID</option>
            <option value="VOTER'S ID">VOTER'S ID</option>
            <option value="POSTAL ID">POSTAL ID</option>
            <option value="NATIONAL ID">NATIONAL ID</option>
            <option value="STUDENT ID">STUDENT ID</option>
        </select>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        

        <div>
            <label class="block text-gray-700 font-medium mb-1 uppercase">FRONT ID</label>
            <input type="file" name="front_valid_id_path" accept="image/*" class="border rounded-lg p-2 w-full bg-white uppercase">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-1 uppercase">BACK ID</label>
            <input type="file" name="back_valid_id_path" accept="image/*" class="border rounded-lg p-2 w-full bg-white uppercase">
        </div>

        <div>
            <label class="block text-gray-700 font-medium mb-1 uppercase">SELFIE WITH ID</label>
            <input type="file" name="selfie_with_id" accept="image/*" class="border rounded-lg p-2 w-full bg-white uppercase">
        </div>
    </div>
</section>
