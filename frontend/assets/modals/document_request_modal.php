<div id="requestModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 id="modalTitle" class="text-xl font-bold mb-4">Request Document</h2>

        <form id="requestForm" class="space-y-4">
            <!-- Select Document -->
            <div>
                <label for="documentName" class="block text-sm font-medium">Select Document</label>
                <div class="max-h-40 overflow-y-auto border rounded">
                    <select name="document_name" id="documentName" class="w-full p-2" size="6" required
                        onchange="handleDocumentChange()">
                        <option value="First Time Job Seeker">First Time Job Seeker</option>
                        <option value="Certificate of Indigency">Certificate of Indigency</option>
                        <option value="Travel Permit">Travel Permit</option>
                        <option value="Certificate of Living Together">Certificate of Living Together</option>
                        <option value="Proof of Income">Proof of Income</option>
                        <option value="Same Person Certificate">Same Person Certificate</option>
                        <option value="Oath of Undertaking">Oath of Undertaking</option>
                        <option value="Certificate of Guardianship">Certificate of Guardianship</option>
                        <option value="Certificate of Residency">Certificate of Residency</option>
                        <option value="Endorsement Letter for Mayor">Endorsement Letter for Mayor</option>
                        <option value="Certificate for Electricity">Certificate for Electricity</option>
                        <option value="Certificate of Low Income">Certificate of Low Income</option>
                        <option value="Business Permit">Business Permit</option>
                        <option value="Barangay Clearance">Barangay Clearance</option>
                    </select>
                </div>
            </div>

            <!-- Purpose -->
            <div>
                <label for="purpose" class="block text-sm font-medium">Purpose</label>
                <textarea name="purpose" id="purpose" rows="3" class="w-full border rounded p-2" required></textarea>
            </div>

            <!-- Business Name (hidden by default) -->
            <div id="businessNameField" class="hidden">
                <label for="business_name" class="block text-sm font-medium">Business Name</label>
                <input type="text" name="business_name" id="business_name"
                    class="w-full border rounded p-2" placeholder="Enter your business name" uppercase required>
            </div>

            <!-- Indigency For (hidden by default) -->
            <div id="indigencyForField" class="hidden">
                <label for="indigency_for" class="block text-sm font-medium">Indigency For</label>
                <input type="text" name="indigency_for" id="indigency_for"
                    class="w-full border rounded p-2" placeholder="What is this certificate for?" uppercase required>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRequestModal()" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
    function handleDocumentChange() {
        const documentName = document.getElementById('documentName').value;

        // Business Name field
        if (documentName === "Business Permit") {
            document.getElementById('businessNameField').classList.remove('hidden');
            document.getElementById('business_name').setAttribute("required", "true");
        } else {
            document.getElementById('businessNameField').classList.add('hidden');
            document.getElementById('business_name').removeAttribute("required");
        }

        // Indigency For field
        if (documentName === "Certificate of Indigency") {
            document.getElementById('indigencyForField').classList.remove('hidden');
            document.getElementById('indigency_for').setAttribute("required", "true");
        } else {
            document.getElementById('indigencyForField').classList.add('hidden');
            document.getElementById('indigency_for').removeAttribute("required");
        }
    }
</script>
