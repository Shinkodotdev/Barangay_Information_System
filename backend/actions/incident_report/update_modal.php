<!-- ✅ EDIT INCIDENT MODAL -->
<div id="editIncidentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-4 overflow-y-auto max-h-[90vh] p-6 relative">
    <div class="flex justify-between items-center border-b pb-3 mb-4">
      <h2 class="text-xl font-semibold text-gray-800">Edit Incident Report</h2>
      <button onclick="closeEditIncidentModal()" class="text-gray-500 hover:text-gray-700">✕</button>
    </div>

    <form id="editIncidentForm" enctype="multipart/form-data" class="space-y-4">
      <input type="hidden" id="editIncidentId" name="incident_id" />

      <!-- Category -->
      <div>
        <label for="editIncidentCategory" class="block font-semibold mb-1">Category</label>
        <select id="editIncidentCategory" name="category" required class="w-full border rounded p-2">
          <option value="">-- Select Category --</option>
          <option value="Incident">Incident</option>
          <option value="Emergency">Emergency</option>
          <option value="Accident">Accident</option>
        </select>
      </div>

      <!-- Type -->
      <div>
        <label for="editIncidentType" class="block font-semibold mb-1">Type</label>
        <select id="editIncidentType" name="type" required class="w-full border rounded p-2">
          <option value="">-- Select Type --</option>
        </select>
      </div>

      <!-- Description -->
      <div>
        <label for="editIncidentDescription" class="block font-semibold mb-1">Description</label>
        <textarea id="editIncidentDescription" name="description" rows="4" class="w-full border rounded p-2" required></textarea>
      </div>

      <!-- Location -->
      <div>
        <label for="editIncidentLocation" class="block font-semibold mb-1">Location</label>
        <input type="text" id="editIncidentLocation" name="location" class="w-full border rounded p-2" required />
      </div>

      <!-- Date & Time -->
      <div>
        <label for="editIncidentDateTime" class="block font-semibold mb-1">Date and Time</label>
        <input type="datetime-local" id="editIncidentDateTime" name="date_time" class="w-full border rounded p-2" required />
      </div>

      <!-- Current Photo -->
      <div id="currentPhotoContainer" class="hidden">
        <p class="font-semibold text-gray-700 mb-1">Current Photo:</p>
        <img id="currentPhotoPreview" src="" alt="Current Incident Photo" class="rounded-lg shadow-md max-h-48">
      </div>

      <!-- Replace Photo -->
      <div>
        <label for="editIncidentPhoto" class="block font-semibold mb-1">Replace Photo (optional)</label>
        <input type="file" id="editIncidentPhoto" name="photo" accept="image/*" class="w-full border rounded p-2" />
      </div>

      <!-- Persons Involved -->
      <div>
        <label class="block font-semibold mb-2">Persons Involved</label>
        <div id="personsContainer" class="space-y-4"></div>
        <button type="button" onclick="addPerson()" class="bg-indigo-600 text-white px-3 py-2 rounded mt-3 hover:bg-indigo-700">
          + Add Another Person
        </button>
      </div>

      <!-- Submit -->
      <div class="pt-4 border-t mt-6 flex justify-end">
        <button type="button" onclick="closeEditIncidentModal()" class="bg-gray-200 px-4 py-2 rounded mr-2 hover:bg-gray-300">Cancel</button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update Incident</button>
      </div>
    </form>
  </div>
</div>

<script>
/* ---------------------- CATEGORY TYPES ---------------------- */
const editIncidentTypes = {
  Incident: ["Theft", "Vandalism", "Physical Assault", "Burglary", "Robbery", "Harassment", "Trespassing", "Other"],
  Emergency: ["Fire", "Medical Emergency", "Flood", "Earthquake", "Power Outage", "Gas Leak", "Other"],
  Accident: ["Traffic Accident", "Workplace Accident", "Slip/Trip/Fall", "Construction Accident", "Other"]
};

document.getElementById("editIncidentCategory")?.addEventListener("change", function() {
  const typeSelect = document.getElementById("editIncidentType");
  typeSelect.innerHTML = '<option value="">-- Select Type --</option>';
  const types = editIncidentTypes[this.value];
  if (types) types.forEach(t => typeSelect.innerHTML += `<option value="${t}">${t}</option>`);
});

/* ---------------------- CREATE PERSON BLOCK ---------------------- */
function createPersonBlock(person = {}) {
  const div = document.createElement("div");
  div.className = "border border-gray-300 p-4 rounded-lg bg-gray-50 space-y-3 relative";

  div.innerHTML = `
    <div class="flex justify-between items-center">
      <label class="font-semibold text-gray-700">Person Type</label>
      <button type="button" class="text-red-500 hover:underline text-sm remove-person">Remove</button>
    </div>

    <select name="person_type[]" class="person-type border rounded p-2 w-full mt-1">
      <option value="">-- Select Person Type --</option>
      <option value="resident">Resident</option>
      <option value="non_resident">Non-Resident</option>
    </select>

    <div class="resident-field hidden mt-2">
      <label class="block text-sm font-medium">Resident Name</label>
      <input type="text" class="resident-search w-full border rounded p-2" placeholder="Type to search...">
      <input type="hidden" name="resident_id[]" class="resident-id" />
      <div class="suggestions absolute bg-white border rounded w-full mt-1 hidden max-h-32 overflow-y-auto z-10"></div>
    </div>

    <div class="non-resident-field hidden mt-2">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <input type="text" name="f_name[]" placeholder="First Name" class="border rounded p-2 uppercase" />
        <input type="text" name="m_name[]" placeholder="Middle Name" class="border rounded p-2 uppercase" />
        <input type="text" name="l_name[]" placeholder="Last Name" class="border rounded p-2 uppercase" />
        <input type="text" name="ext_name[]" placeholder="Extension" class="border rounded p-2 uppercase" />
        <input type="text" name="address[]" placeholder="Address" class="border rounded p-2 uppercase sm:col-span-2" />
        <input type="email" name="email[]" placeholder="Email" class="border rounded p-2 sm:col-span-2" />
        <div class="flex sm:col-span-2">
          <span class="inline-flex items-center px-3 border border-r-0 rounded-l-lg bg-gray-100 text-gray-600 text-sm">+63</span>
          <input type="text" name="contact_no[]" maxlength="10" placeholder="9XXXXXXXXX"
            class="w-full border border-gray-300 rounded-r-lg p-2"
            oninput="this.value=this.value.replace(/[^0-9]/g,'')" />
        </div>
      </div>
    </div>

    <label class="block text-sm font-medium mt-3">Role</label>
    <select name="role[]" class="border rounded p-2 w-full">
      <option value="">-- Select Role --</option>
      <option value="Victim">Victim</option>
      <option value="Witness">Witness</option>
      <option value="Suspect">Suspect</option>
      <option value="Reporter">Reporter</option>
      <option value="Respondent">Respondent</option>
      <option value="Complainant">Complainant</option>
      <option value="Other">Other</option>
    </select>
  `;

  // Remove handler
  div.querySelector(".remove-person").addEventListener("click", () => div.remove());

  // Type change handler
  const typeSelect = div.querySelector(".person-type");
  typeSelect.addEventListener("change", () => {
    div.querySelector(".resident-field").classList.add("hidden");
    div.querySelector(".non-resident-field").classList.add("hidden");
    if (typeSelect.value === "resident") div.querySelector(".resident-field").classList.remove("hidden");
    if (typeSelect.value === "non_resident") div.querySelector(".non-resident-field").classList.remove("hidden");
  });

  // Preload existing data
  if (person.person_type) {
    typeSelect.value = person.person_type;
    typeSelect.dispatchEvent(new Event("change"));
  }

  if (person.role) div.querySelector("[name='role[]']").value = person.role;

  // Resident
  if (person.person_type === "resident") {
    div.querySelector(".resident-search").value = person.resident_name || "";
    div.querySelector(".resident-id").value = person.resident_id || "";
  }
  // Non-resident
  else if (person.person_type === "non_resident") {
    div.querySelector("[name='f_name[]']").value = person.f_name || "";
    div.querySelector("[name='m_name[]']").value = person.m_name || "";
    div.querySelector("[name='l_name[]']").value = person.l_name || "";
    div.querySelector("[name='ext_name[]']").value = person.ext_name || "";
    div.querySelector("[name='address[]']").value = person.address || "";
    div.querySelector("[name='email[]']").value = person.email || "";
    div.querySelector("[name='contact_no[]']").value = person.contact_no || "";
  }

  return div;
}

/* ---------------------- ADD PERSON ---------------------- */
function addPerson() {
  document.getElementById("personsContainer").appendChild(createPersonBlock());
}

/* ---------------------- OPEN MODAL ---------------------- */
async function openEditIncidentModal(incidentData) {
  const modal = document.getElementById("editIncidentModal");
  modal.classList.remove("hidden");
  modal.classList.add("flex");

  document.getElementById("editIncidentId").value = incidentData.id;
  document.getElementById("editIncidentCategory").value = incidentData.category;
  document.getElementById("editIncidentCategory").dispatchEvent(new Event("change"));
  document.getElementById("editIncidentType").value = incidentData.type;
  document.getElementById("editIncidentDescription").value = incidentData.description;
  document.getElementById("editIncidentLocation").value = incidentData.location;
  document.getElementById("editIncidentDateTime").value = incidentData.date_time;

  const photoContainer = document.getElementById("currentPhotoContainer");
  const photoPreview = document.getElementById("currentPhotoPreview");
  if (incidentData.photo) {
    photoContainer.classList.remove("hidden");
    photoPreview.src = `../../../uploads/incidents/${incidentData.photo}`;
  } else photoContainer.classList.add("hidden");

  const personsContainer = document.getElementById("personsContainer");
  personsContainer.innerHTML = "";

const res = await fetch(`../../../backend/actions/incident_report/get_persons_involved.php?incident_id=${incidentData.incident_id}`);
  const json = await res.json();
  console.log(json); // Debug check

  if (json.status === "success" && Array.isArray(json.data) && json.data.length > 0) {
    json.data.forEach(p => personsContainer.appendChild(createPersonBlock(p)));
  } else {
    personsContainer.appendChild(createPersonBlock());
  }
}
/* ---------------------- RESIDENT LIVE SEARCH ---------------------- */
document.addEventListener("input", async function (e) {
  if (!e.target.classList.contains("resident-search")) return;

  const input = e.target;
  const suggestionsBox = input.parentElement.querySelector(".suggestions");
  const query = input.value.trim();

  if (query.length < 2) {
    suggestionsBox.classList.add("hidden");
    return;
  }

  try {
    const res = await fetch(`../../../backend/actions/incident_report/search_resident.php?q=${encodeURIComponent(query)}`);
    const data = await res.json();

    suggestionsBox.innerHTML = "";
    if (data.length > 0) {
      data.forEach(r => {
        const item = document.createElement("div");
        item.textContent = r.text;
        item.className = "px-3 py-2 hover:bg-indigo-100 cursor-pointer";
        item.addEventListener("click", () => {
          input.value = r.text;
          input.parentElement.querySelector(".resident-id").value = r.id;
          suggestionsBox.classList.add("hidden");
        });
        suggestionsBox.appendChild(item);
      });
      suggestionsBox.classList.remove("hidden");
    } else {
      suggestionsBox.innerHTML = `<div class="px-3 py-2 text-gray-500">No results found</div>`;
      suggestionsBox.classList.remove("hidden");
    }
  } catch (err) {
    console.error("Search error:", err);
    suggestionsBox.classList.add("hidden");
  }
});

/* Hide suggestions if clicked outside */
document.addEventListener("click", e => {
  document.querySelectorAll(".suggestions").forEach(box => {
    if (!box.contains(e.target) && !box.previousElementSibling?.contains(e.target)) {
      box.classList.add("hidden");
    }
  });
});
/* ---------------------- CLOSE MODAL ---------------------- */
function closeEditIncidentModal() {
  document.getElementById("editIncidentModal").classList.add("hidden");
  document.getElementById("editIncidentModal").classList.remove("flex");
}

/* ---------------------- SUBMIT ---------------------- */
document.getElementById("editIncidentForm")?.addEventListener("submit", async e => {
  e.preventDefault();
  const btn = e.target.querySelector("button[type='submit']");
  const data = new FormData(e.target);

  btn.disabled = true;
  btn.textContent = "Updating...";

  try {
    const res = await fetch("../../../backend/actions/incident_report/update.php", { method: "POST", body: data });
    const json = await res.json();
    if (json.status === "success") {
      Swal.fire("✅ Updated!", "Incident updated successfully.", "success").then(() => closeEditIncidentModal());
    } else Swal.fire("❌ Error", json.message || "Update failed.", "error");
  } catch {
    Swal.fire("⚠️ Error", "Network or server issue.", "error");
  } finally {
    btn.disabled = false;
    btn.textContent = "Update Incident";
  }
});
</script>
