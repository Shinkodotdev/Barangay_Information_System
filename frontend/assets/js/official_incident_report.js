function confirmArchive(id, isArchived) {
    const actionText = isArchived ? "unarchive" : "archive";
    Swal.fire({
        title: `Are you sure you want to ${actionText} this incident?`,
        text: `This will ${isArchived ? "restore" : "move"} the record ${isArchived ? "back to active incidents" : "to the archive"
            }.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#6366F1",
        cancelButtonColor: "#d33",
        confirmButtonText: `Yes, ${actionText} it!`,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `../../../backend/actions/incident_report/archive.php?id=${id}`;
        }
    });
}
// Dropdown functions
document.querySelectorAll(".action-btn").forEach((btn) => {
    btn.addEventListener("click", function (e) {
        e.stopPropagation();
        const id = this.dataset.dropdownId;
        document.querySelectorAll('[id^="dropdown-"]').forEach((d) => {
            if (d.id !== id) d.classList.add("hidden");
        });
        document.getElementById(id).classList.toggle("hidden");
    });
});
document.addEventListener("click", function () {
    document
        .querySelectorAll('[id^="dropdown-"]')
        .forEach((d) => d.classList.add("hidden"));
});
function openCreateIncidentModal() {
    const modal = document.getElementById("incidentModal");
    if (!modal) return;

    modal.classList.remove("hidden");
    modal.classList.add("flex");
    document.body.classList.add("overflow-hidden");

    modal.style.opacity = "0";
    requestAnimationFrame(() => {
        modal.style.transition = "opacity 0.3s ease";
        modal.style.opacity = "1";
    });
}

function closeIncidentModal() {
    const modal = document.getElementById("incidentModal");
    if (!modal) return;

    modal.style.opacity = "0";
    setTimeout(() => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
        document.body.classList.remove("overflow-hidden");
    }, 300);
}

/* ====================== RESIDENT AUTOCOMPLETE ====================== */
function searchResident(input) {
    const query = input.value.trim();
    const wrapper = input.closest(".relative");
    const suggestions = wrapper.querySelector(".suggestions");
    const hiddenInput = wrapper.querySelector(".resident-id");

    if (!suggestions || !hiddenInput) return;

    if (query.length < 2) {
        suggestions.innerHTML = "";
        suggestions.classList.add("hidden");
        return;
    }

    fetch(
        `../../../backend/actions/incident_report/search_resident.php?q=${encodeURIComponent(
            query
        )}`
    )
        .then((res) => res.json())
        .then((data) => {
            suggestions.innerHTML = "";
            if (!data.length) {
                suggestions.innerHTML = `<div class="p-2 text-gray-500">No residents found</div>`;
            } else {
                data.forEach((resident) => {
                    const div = document.createElement("div");
                    div.className = "p-2 hover:bg-indigo-100 cursor-pointer";
                    div.textContent = resident.text;
                    div.onclick = () => {
                        input.value = resident.text;
                        hiddenInput.value = resident.id;
                        suggestions.classList.add("hidden");
                    };
                    suggestions.appendChild(div);
                });
            }
            suggestions.classList.remove("hidden");
        })
        .catch(() => {
            suggestions.innerHTML = `<div class="p-2 text-gray-500">Error loading residents</div>`;
            suggestions.classList.remove("hidden");
        });
}

// Hide suggestions when clicking elsewhere
document.addEventListener("click", (e) => {
    if (!e.target.classList.contains("resident-search")) {
        document
            .querySelectorAll(".suggestions")
            .forEach((s) => s.classList.add("hidden"));
    }
});
/* ====================== INCIDENT CATEGORY HANDLER ====================== */
/* ====================== INCIDENT CATEGORY HANDLER ====================== */
const incidentTypes = {
    Incident: [
        "Theft",
        "Vandalism",
        "Physical Assault",
        "Burglary",
        "Robbery",
        "Property Damage",
        "Harassment",
        "Disturbance",
        "Trespassing",
        "Other",
    ],
    Emergency: [
        "Fire",
        "Medical Emergency",
        "Natural Disaster",
        "Flood",
        "Earthquake",
        "Severe Weather",
        "Gas Leak",
        "Power Outage",
        "Missing Person",
        "Other",
    ],
    Accident: [
        "Traffic Accident",
        "Slip/Trip/Fall",
        "Workplace Accident",
        "Construction Accident",
        "Drowning",
        "Explosion",
        "Electrical Accident",
        "Animal Bite/Attack",
        "Poisoning",
        "Other",
    ],
};

document.addEventListener("DOMContentLoaded", () => {
    const categorySelect = document.getElementById("incidentCategory");
    const typeSelect = document.getElementById("incidentType");

    if (!categorySelect || !typeSelect) {
        console.error("Incident category or type select not found.");
        return;
    }

    categorySelect.addEventListener("change", () => {
        const category = categorySelect.value;
        typeSelect.innerHTML = '<option value="">-- Select Type --</option>';

        if (incidentTypes[category]) {
            incidentTypes[category].forEach((type) => {
                const option = document.createElement("option");
                option.value = type;
                option.textContent = type;
                typeSelect.appendChild(option);
            });
        }
    });
});

/* ====================== PERSON FIELD HANDLING (Robust) ====================== */

function addAnotherPersonV2() {
    const container = document.getElementById("personsContainerV2");
    if (!container) {
        console.error("❌ personsContainer not found!");
        return;
    }

    // Find the first person-card as a template
    const template = container.querySelector(".person-card");
    if (!template) {
        console.error("❌ No person template found to clone!");
        return;
    }

    // Deep clone the template
    const clone = template.cloneNode(true);

    // Clear values for inputs, selects, textareas inside clone
    clone.querySelectorAll("input, select, textarea").forEach((el) => {
        // For checkboxes/radios you may want to handle differently; not present here.
        if (el.type === "file") {
            el.value = "";
        } else {
            el.value = "";
        }
    });

    // Ensure resident hidden id and suggestions cleared
    clone.querySelectorAll(".resident-id").forEach((h) => (h.value = ""));
    clone.querySelectorAll(".suggestions").forEach((s) => {
        s.innerHTML = "";
        s.classList.add("hidden");
    });

    // Hide conditional sections on cloned card
    clone
        .querySelectorAll(".resident-field, .non-resident-field")
        .forEach((el) => {
            el.classList.add("hidden");
        });

    // Append the clone
    container.appendChild(clone);

    // Optionally focus the first input (person type select) on the newly added card
    const newSelect = clone.querySelector('select[name="person_type[]"]');
    if (newSelect) newSelect.focus();
}

// Event delegation for remove buttons (works for cloned nodes too)
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("personsContainerV2");
    if (!container) return;

    container.addEventListener("click", function (e) {
        const btn = e.target.closest(".remove-person");
        if (!btn) return;

        removePersonV2(btn);
    });
});

function removePersonV2(btn) {
    const container = document.getElementById("personsContainerV2");
    if (!container) return;

    const card = btn.closest(".person-card");
    if (!card) return;

    // If only 1 person-card left, prevent removal
    const cards = container.querySelectorAll(".person-card");
    if (cards.length <= 1) {
        alert("At least one person must be listed.");
        return;
    }

    card.remove();
}

// Toggle fields based on person type (no change)
function togglePersonInputV2(select) {
    const parent = select.closest(".person-card");
    if (!parent) return;
    const residentField = parent.querySelector(".resident-field");
    const nonResidentField = parent.querySelector(".non-resident-field");

    if (select.value === "resident") {
        if (residentField) residentField.classList.remove("hidden");
        if (nonResidentField) nonResidentField.classList.add("hidden");
    } else if (select.value === "non_resident") {
        if (nonResidentField) nonResidentField.classList.remove("hidden");
        if (residentField) residentField.classList.add("hidden");
    } else {
        if (residentField) residentField.classList.add("hidden");
        if (nonResidentField) nonResidentField.classList.add("hidden");
    }
}
/* ====================== INCIDENT FORM SUBMIT ====================== */
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("incidentForm");
    if (!form) return;

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);

        submitBtn.disabled = true;
        submitBtn.textContent = "Submitting...";

        try {
            const res = await fetch(
                "../../../backend/actions/incident_report/report_incident.php",
                {
                    method: "POST",
                    body: formData,
                }
            );
            const rawText = await res.text();
            let data;

            try {
                data = JSON.parse(rawText.trim());
            } catch {
                data = null;
            }

            if (!res.ok || !data) {
                Swal.fire("Error", "Server error or invalid response.", "error");
            } else if (data.status === "success") {
                Swal.fire({
                    title: "Incident Submitted",
                    text: "Your incident report has been submitted successfully.",
                    icon: "success",
                    confirmButtonColor: "#16a34a",
                }).then(() => {
                    form.reset();
                    closeIncidentModal();
                });
            } else {
                Swal.fire("Error", data.message || "Failed to submit report.", "error");
            }
        } catch {
            Swal.fire("Error", "Network or server error. Please try again.", "error");
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = "Submit Incident";
        }
    });
});
let stream = null;
let currentFacingMode = "environment"; // start with back camera on mobile

const useWebcamBtn = document.getElementById("useWebcamBtn");
const cameraModal = document.getElementById("cameraModal");
const closeCamera = document.getElementById("closeCamera");
const video = document.getElementById("cameraStream");
const flipCameraBtn = document.getElementById("flipCameraBtn");
const captureBtn = document.getElementById("captureBtn");
const canvas = document.getElementById("snapshotCanvas");
const photoPreviewContainer = document.getElementById("photoPreviewContainer");
const photoPreview = document.getElementById("photoPreview");
const photoInput = document.getElementById("photoInput");

// ✅ Start Camera
async function startCamera() {
    try {
        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
        }

        const constraints = {
            video: { facingMode: currentFacingMode },
            audio: false,
        };

        stream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = stream;
        cameraModal.classList.remove("hidden");
    } catch (err) {
        console.error("Camera access error:", err);
        alert(
            "Unable to access camera. Please check permissions or try a different browser."
        );
    }
}

// ✅ Stop Camera
function stopCamera() {
    if (stream) {
        stream.getTracks().forEach((track) => track.stop());
        stream = null;
    }
    cameraModal.classList.add("hidden");
}

// ✅ Flip Camera
flipCameraBtn.addEventListener("click", async () => {
    currentFacingMode =
        currentFacingMode === "environment" ? "user" : "environment";
    await startCamera();
});

// ✅ Capture Image
captureBtn.addEventListener("click", () => {
    const context = canvas.getContext("2d");
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageData = canvas.toDataURL("image/png");

    // Show preview
    photoPreviewContainer.classList.remove("hidden");
    photoPreview.src = imageData;

    // Convert base64 image to a File for the form
    fetch(imageData)
        .then((res) => res.blob())
        .then((blob) => {
            const file = new File([blob], "captured_photo.png", {
                type: "image/png",
            });

            // Create a DataTransfer to set the file input value
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            photoInput.files = dataTransfer.files;
        });

    stopCamera();
});

// Event Listeners
useWebcamBtn.addEventListener("click", startCamera);
closeCamera.addEventListener("click", stopCamera);

// Close camera when modal background is clicked
cameraModal.addEventListener("click", (e) => {
    if (e.target === cameraModal) stopCamera();
});
