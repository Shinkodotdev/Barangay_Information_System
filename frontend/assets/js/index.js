       /* ---------------------- CONTACTS MODAL ---------------------- */
        function openContactsModal() {
            const modal = document.getElementById('contacts-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeContactsModal() {
            const modal = document.getElementById('contacts-modal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        /* ---------------------- INCIDENT MODAL CONTROLS ---------------------- */
        function resetForms() {
            // Hide all form steps
            document.querySelectorAll("#residentForm, #nonResidentForm, #otpForm, #incidentForm").forEach(f => {
                if (f) f.classList.add("hidden");
            });

            // Show initial step
            const reporterStep = document.getElementById("reporterStep");
            if (reporterStep) reporterStep.classList.remove("hidden");

            // Reset all inputs
            document.querySelectorAll("form").forEach(f => f.reset());
        }

        function openIncidentModal() {
            const modal = document.getElementById("incidentModal");
            if (modal) {
                modal.classList.remove("hidden");
                document.body.classList.add("overflow-hidden");
            }

            // ✅ Show Non-Resident form by default
            document.getElementById("nonResidentForm").classList.remove("hidden");
            document.getElementById("otpForm").classList.add("hidden");
            document.getElementById("incidentForm").classList.add("hidden");
        }

        function closeIncidentModal() {
            const modal = document.getElementById("incidentModal");
            if (modal) {
                modal.classList.add("hidden");
                document.body.classList.remove("overflow-hidden");
            }
        }

        function showResidentForm() {
            const reporterStep = document.getElementById("reporterStep");
            const residentForm = document.getElementById("residentForm");
            if (reporterStep && residentForm) {
                reporterStep.classList.add("hidden");
                residentForm.classList.remove("hidden");
            }
        }

        function continueIncidentForm() {
            resetForms();
            const form = document.getElementById("incidentForm");
            if (form) form.classList.remove("hidden");
        }

        /* ---------------------- EMAIL VERIFICATION ---------------------- */
        async function verifyEmail() {
            const form = document.getElementById("nonResidentForm");
            const btn = document.getElementById("verifyBtn");
            if (!form || !btn) return;

            const formData = new FormData(form);
            btn.disabled = true;
            btn.textContent = "Sending...";

            try {
                const res = await fetch("./backend/actions/incident_report/send_verification.php", {
                    method: "POST",
                    body: formData
                });
                const data = await res.json();

                if (data.status === "success") {
                    Swal.fire("Verification Sent", data.message, "success");
                    form.classList.add("hidden");
                    document.getElementById("otpForm").classList.remove("hidden");
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            } catch (err) {
                console.error(err);
                Swal.fire("Error", "An unexpected error occurred.", "error");
            } finally {
                btn.disabled = false;
                btn.textContent = "Verify Email";
            }
        }

        /* ---------------------- OTP VERIFICATION ---------------------- */
        let otpTimer;
        let remainingTime = 300; // 5 minutes = 300 seconds

        function startOtpTimer() {
            const resendBtn = document.getElementById("resendBtn");
            resendBtn.disabled = true;

            // Reset timer
            clearInterval(otpTimer);
            remainingTime = 300;

            otpTimer = setInterval(() => {
                const minutes = Math.floor(remainingTime / 60);
                const seconds = remainingTime % 60;

                resendBtn.textContent = `Resend OTP (Wait ${minutes}:${seconds
                    .toString()
                    .padStart(2, "0")})`;

                if (remainingTime <= 0) {
                    clearInterval(otpTimer);
                    resendBtn.disabled = false;
                    resendBtn.textContent = "Resend OTP";
                }

                remainingTime--;
            }, 1000);
        }

        // ✅ Call this when OTP form is first shown
        document.addEventListener("DOMContentLoaded", () => {
            startOtpTimer();
        });
        async function submitOtp() {
            const form = document.getElementById("otpForm");
            const btn = document.getElementById("otpBtn");
            if (!form || !btn) return;

            const formData = new FormData(form);
            btn.disabled = true;
            btn.textContent = "Verifying...";

            try {
                const res = await fetch("./backend/actions/incident_report/verify_otp.php", {
                    method: "POST",
                    body: formData,
                    credentials: "include"
                });

                let data;
                try {
                    data = await res.json();
                } catch (jsonErr) {
                    console.error("Invalid JSON response:", await res.text());
                    Swal.fire("Error", "Invalid server response. Check console.", "error");
                    btn.disabled = false;
                    btn.textContent = "Continue";
                    return;
                }

                if (data.status === "success") {
                    Swal.fire("Verified", data.message, "success");
                    form.classList.add("hidden");
                    document.getElementById("incidentForm").classList.remove("hidden");
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            } catch (err) {
                console.error("Fetch error:", err);
                Swal.fire("Error", "Failed to verify OTP.", "error");
            } finally {
                btn.disabled = false;
                btn.textContent = "Continue";
            }
        }

        async function resendOtp() {
            const resendBtn = document.getElementById("resendBtn");
            resendBtn.disabled = true;
            resendBtn.textContent = "Resending...";

            try {
                const res = await fetch("./backend/actions/incident_report/resend_otp.php", {
                    method: "POST",
                });
                const data = await res.json();

                if (data.status === "success") {
                    Swal.fire("OTP Sent", data.message, "success");
                    startOtpTimer(); // restart 5-minute timer
                } else {
                    Swal.fire("Error", data.message, "error");
                    resendBtn.disabled = false;
                    resendBtn.textContent = "Resend OTP";
                }
            } catch (err) {
                console.error(err);
                Swal.fire("Error", "Failed to resend OTP.", "error");
                resendBtn.disabled = false;
                resendBtn.textContent = "Resend OTP";
            }
        }

        
        /* ---------------------- INCIDENT CATEGORY TYPES ---------------------- */
        const incidentTypes = {
            Incident: ["Theft", "Vandalism", "Physical Assault", "Burglary", "Robbery", "Property Damage", "Harassment", "Disturbance", "Trespassing", "Other"],
            Emergency: ["Fire", "Medical Emergency", "Natural Disaster", "Flood", "Earthquake", "Severe Weather", "Gas Leak", "Power Outage", "Missing Person", "Other"],
            Accident: ["Traffic Accident", "Slip/Trip/Fall", "Workplace Accident", "Construction Accident", "Drowning", "Explosion", "Electrical Accident", "Animal Bite/Attack", "Poisoning", "Other"]
        };

        const incidentCategory = document.getElementById("incidentCategory");
        if (incidentCategory) {
            incidentCategory.addEventListener("change", function () {
                const typeSelect = document.getElementById("incidentType");
                if (!typeSelect) return;
                typeSelect.innerHTML = '<option value="">-- Select Type --</option>';

                const category = this.value;
                if (incidentTypes[category]) {
                    incidentTypes[category].forEach(type => {
                        const option = document.createElement("option");
                        option.value = type;
                        option.textContent = type;
                        typeSelect.appendChild(option);
                    });
                }
            });
        }

        /* ---------------------- ADDITIONAL PERSON HANDLER ---------------------- */
        function togglePersonInput(select) {
            const container = select.closest(".border.p-4.rounded-lg");
            const residentField = container.querySelector(".resident-field");
            const nonResidentField = container.querySelector(".non-resident-field");

            if (!residentField || !nonResidentField) return;

            if (select.value === "resident") {
                residentField.classList.remove("hidden");
                nonResidentField.classList.add("hidden");
            } else if (select.value === "non_resident") {
                residentField.classList.add("hidden");
                nonResidentField.classList.remove("hidden");
            } else {
                residentField.classList.add("hidden");
                nonResidentField.classList.add("hidden");
            }
        }

        /* ---------------------- ADD NEW PERSON BLOCK ---------------------- */
        function addPerson() {
            const container = document.getElementById("personsContainer");
            if (!container) return;

            const firstPerson = container.querySelector(".border.p-4.rounded-lg");
            if (!firstPerson) return;

            const clone = firstPerson.cloneNode(true);

            // Reset all input fields
            clone.querySelectorAll("input, select, textarea").forEach(el => {
                el.value = "";
            });

            // Hide both resident/non-resident fields by default
            clone.querySelectorAll(".resident-field, .non-resident-field").forEach(div => {
                div.classList.add("hidden");
            });

            // Clear any previous suggestions
            const suggestionBoxes = clone.querySelectorAll(".suggestions");
            suggestionBoxes.forEach(s => {
                s.innerHTML = "";
                s.classList.add("hidden");
            });

            container.appendChild(clone);
        }

        /* ---------------------- RESIDENT SEARCH AUTOCOMPLETE ---------------------- */
        function searchResident(input) {
            const query = input.value.trim();
            const container = input.closest(".relative");
            const suggestions = container.querySelector(".suggestions");
            const hiddenInput = container.querySelector(".resident-id");

            if (!suggestions || !hiddenInput) return;

            if (query.length < 2) {
                suggestions.innerHTML = "";
                suggestions.classList.add("hidden");
                return;
            }

            fetch(`./backend/actions/incident_report/search_resident.php?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestions.innerHTML = "";

                    if (data.length === 0) {
                        suggestions.innerHTML = `<div class="p-2 text-gray-500">No residents found</div>`;
                    } else {
                        data.forEach(resident => {
                            const option = document.createElement("div");
                            option.textContent = resident.text;
                            option.className = "p-2 hover:bg-indigo-100 cursor-pointer";
                            option.onclick = () => {
                                input.value = resident.text;
                                hiddenInput.value = resident.id;
                                suggestions.classList.add("hidden");
                            };
                            suggestions.appendChild(option);
                        });
                    }

                    suggestions.classList.remove("hidden");
                })
                .catch(() => {
                    suggestions.innerHTML = `<div class="p-2 text-gray-500">Error loading residents</div>`;
                    suggestions.classList.remove("hidden");
                });
        }


        // Hide suggestions when clicking outside
        document.addEventListener("click", (e) => {
            if (!e.target.classList.contains("resident-search")) {
                document.querySelectorAll(".suggestions").forEach(s => s.classList.add("hidden"));
            }
        });

 /* ---------------------- INCIDENT REPORT SUBMISSION ---------------------- */
document.addEventListener("DOMContentLoaded", () => {
    const incidentForm = document.getElementById("incidentForm");
    if (!incidentForm) return;

    incidentForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        if (!incidentForm.checkValidity()) {
            incidentForm.reportValidity();
            return;
        }

        const submitBtn = incidentForm.querySelector("button[type='submit']");
        const formData = new FormData(incidentForm);

        // ✅ Ensure at least one person_type field exists
        const personTypeInputs = incidentForm.querySelectorAll("[name='person_type[]']");
        if (personTypeInputs.length === 0) {
            console.warn("⚠️ No person_type[] fields found — adding default fallback.");
            formData.append("person_type[]", "non_resident");
        }

        submitBtn.disabled = true;
        submitBtn.textContent = "Submitting...";

        try {
            const res = await fetch("./backend/actions/incident_report/report_incident.php", {
                method: "POST",
                body: formData,
            });

            const rawText = await res.text(); // read once only
            let data;

            // ✅ Debug any stray HTML or PHP warning output
            if (!res.ok) {
                console.error("❌ HTTP Error:", res.status, rawText);
                Swal.fire("Error", `Server error (${res.status}). Please try again.`, "error");
                return;
            }

            try {
                // Attempt to parse JSON strictly
                data = JSON.parse(rawText.trim());
            } catch (parseErr) {
                console.error("❌ Invalid JSON from server.\nFull Response:\n", rawText);
                Swal.fire({
                    title: "Unexpected Server Response",
                    html: `<pre style="text-align:left;white-space:pre-wrap;">${rawText
                        .replace(/[<>]/g, (m) => (m === "<" ? "&lt;" : "&gt;"))}</pre>`,
                    icon: "error",
                    width: 600,
                });
                return;
            }

            if (data.status === "success") {
                Swal.fire({
                    title: "Incident Report Submitted",
                    text: "Your incident report has been successfully submitted.",
                    icon: "success",
                    confirmButtonColor: "#16a34a",
                }).then(() => {
                    incidentForm.reset();
                    if (typeof closeIncidentModal === "function") closeIncidentModal();
                });
            } else {
                Swal.fire("Error", data.message || "Failed to submit report.", "error");
            }
        } catch (err) {
            console.error("⚠️ Network or JS error:", err);
            Swal.fire("Error", "Network or server error. Please try again.", "error");
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = "Submit Incident";
        }
    });
});
