<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-2xl max-w-xl w-full p-6 relative overflow-y-auto max-h-[90vh] border border-gray-300">
        <!-- Close Button -->
        <button onclick="closeViewModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-2xl font-bold">&times;</button>

        <!-- Header with Logos -->
        <div class="flex items-center justify-between mb-6">
            <img src="../../../frontend/assets/images/talavera.png" alt="Left Logo" class="h-16">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-indigo-700 uppercase">Incident Report</h1>
                <p class="text-gray-500 text-sm">Barangay Incident Management System</p>
            </div>
            <img src="../../../frontend/assets/images/Logo.jpg" alt="Right Logo" class="h-16">
        </div>
        <hr class="border-gray-300 mb-6">

        <!-- Incident Details -->
        <div id="viewContent" class="space-y-4 text-gray-700 text-sm">
            <!-- Content injected dynamically -->
        </div>

        <!-- Signature Section -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 text-center">
            <div class="flex flex-col items-center border-t pt-2">
                <p class="font-semibold">Barangay Officer</p>
                <p class="text-xs text-gray-500">Signature & Date</p>
            </div>
            <div class="flex flex-col items-center border-t pt-2">
                <p class="font-semibold">Barangay Captain</p>
                <p class="text-xs text-gray-500">Signature & Date</p>
            </div>
        </div>

        <!-- Footer Close Button -->
        <div class="mt-6 text-right">
            <button onclick="closeViewModal()" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">Close</button>
        </div>
    </div>
</div>

<script>
const incidents = <?php echo json_encode($incidents, JSON_UNESCAPED_UNICODE); ?>;
const incidentPersons = <?php echo json_encode($incidentPersons, JSON_UNESCAPED_UNICODE); ?>;

function openViewModal(id){
    const viewModal = document.getElementById('viewModal');
    const content = document.getElementById('viewContent');
    const incident = incidents.find(i => i.incident_id == id);
    if(!incident) return;

    // Persons involved
    let personsHTML = '';
    const persons = incidentPersons[id] || [];
    if(persons.length){
        personsHTML = `
        <h3 class="font-semibold text-indigo-700 mt-6 border-b border-gray-300 pb-1">Persons Involved</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
        `;
        persons.forEach(p=>{
            const name = p.person_type === 'resident' 
                ? [p.res_fname, p.res_mname, p.res_lname].filter(Boolean).join(' ')
                : [p.nonres_fname, p.nonres_mname, p.nonres_lname].filter(Boolean).join(' ');
            personsHTML += `
            <div class="border p-3 rounded bg-gray-50 shadow-sm">
                <p><strong>Role:</strong> ${p.role}</p>
                <p><strong>Type:</strong> ${p.person_type}</p>
                <p><strong>Name:</strong> ${name}</p>
            </div>`;
        });
        personsHTML += '</div>';
    }

    // Reporter
    const reporterName = incident.resident_fname 
        ? `${incident.resident_fname} ${incident.resident_lname} (Resident)` 
        : incident.nonres_fname 
        ? `${incident.nonres_fname} ${incident.nonres_lname} (Non-Resident)` 
        : 'Unknown';

    // Incident details layout
    content.innerHTML = `
        <h3 class="font-semibold text-indigo-700 border-b border-gray-300 pb-1">Incident Details</h3>
        <div class="grid grid-cols-3 gap-4 mt-2 ">
            <div><strong>Incident #</strong> ${incident.incident_id}</div>
            <div><strong>Category:</strong> ${incident.category}</div>
            <div><strong>Type:</strong> ${incident.type}</div>
            <div><strong>Date & Time:</strong> ${new Date(incident.date_time).toLocaleString()}</div>
            <div class="md:col-span-2"><strong>Description:</strong> ${incident.description}</div>
            <div><strong>Location:</strong> ${incident.location}</div>
            <div><strong>Reporter:</strong> ${reporterName}</div>
            
        </div>
        <div class="md:col-span-2">
                <strong>Photo:</strong><br>
                ${incident.photo ? `<img src='../../../uploads/incidents/${incident.photo}' class='w-full max-w-xs h-64 object-cover rounded mt-2 border' />` : 'N/A'}
            </div>
        ${personsHTML}
    `;

    viewModal.classList.remove('hidden');
}

function closeViewModal(){ 
    document.getElementById('viewModal').classList.add('hidden'); 
}
document.getElementById('viewModal').addEventListener('click', function(e){ 
    if(e.target === this) closeViewModal(); 
});
</script>
