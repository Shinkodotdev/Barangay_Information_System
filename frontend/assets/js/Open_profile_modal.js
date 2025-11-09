const modal = document.getElementById('profileModal');
const openBtn = document.getElementById('openModalBtn'); // Optional if trigger exists
const closeBtn = document.getElementById('closeModalBtn');
const modalBody = document.getElementById('modalBody');
//ALREADY WORKING DON'T TOUCH
function viewUser(userId) {
    fetch(`../../assets/modals/user_view_modal.php?user_id=${userId}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;

            const modal = document.getElementById('profileModal');
            const closeBtn = document.getElementById('closeModalBtn');

            // Show the modal
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');

            // Close button functionality
            closeBtn.addEventListener('click', () => {
                modal.classList.add('opacity-0', 'pointer-events-none');
                modal.classList.remove('opacity-100');
            });

            // Close when clicking outside the modal content
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('opacity-0', 'pointer-events-none');
                    modal.classList.remove('opacity-100');
                }
            });
        });
}
//ALREADY WORKING DON'T TOUCH
function deleteUser(userId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This user will be archived, not permanently deleted.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, archive it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX to backend
            fetch('../../../backend/actions/user/archive_user.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'user_id=' + userId
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Archived!', data.message, 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(() => Swal.fire('Error!', 'Request failed.', 'error'));
        }
    });
}
//ALREADY WORKING DON'T TOUCH
function restoreUser(userId) {
    Swal.fire({
        title: "Restore User?",
        text: "This will restore the archived user and set their status back to Pending.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#16a34a",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, restore"
    }).then((result) => {
        if (result.isConfirmed) {
            // AJAX to backend
            fetch("../../../backend/actions/user/restore_user.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "user_id=" + encodeURIComponent(userId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire("Restored!", data.message, "success").then(() => {
                        // Reload page or update table dynamically
                        location.reload();
                    });
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            })
            .catch(() => {
                Swal.fire("Error", "Something went wrong while restoring the user.", "error");
            });
        }
    });
}
//ALREADY WORKING DON'T TOUCH
document.getElementById("createResidentForm").addEventListener("submit", async function (e) {
            e.preventDefault();

            const form = e.target;
            const submitBtn = form.querySelector("button[type='submit']");
            const formData = new FormData(form);

            // Disable button & show loading spinner
            submitBtn.disabled = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;

            try {
                const response = await fetch("../../../backend/actions/user/create_user.php", {
                    method: "POST",
                    body: formData
                });

                const text = await response.text();
                let data;

                try {
                    data = JSON.parse(text);
                } catch {
                    data = { status: "error", message: text };
                }

                // ✅ Success case
                if (data.status === "success" || data.success) {
                    await Swal.fire({
                        title: "Resident Saved",
                        text: data.message || "Profile has been successfully created.",
                        icon: "success",
                        confirmButtonColor: "#4F46E5"
                    });

                    closeCreateResidentModal();
                    window.location.reload();
                } else {
                    // ❌ Error from backend
                    Swal.fire({
                        title: "Error",
                        text: data.message || "An error occurred while saving the profile.",
                        icon: "error",
                        confirmButtonColor: "#EF4444"
                    });
                }

            } catch (error) {
                console.error("Form Submit Error:", error);
                Swal.fire({
                    title: "Network Error",
                    text: "Unable to connect to the server. Please check your internet connection.",
                    icon: "error",
                    confirmButtonColor: "#EF4444"
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
        function openCreateResidentModal() {
            document.getElementById("createResidentModal").classList.remove("hidden");
            document.getElementById("createResidentModal").classList.add("flex");
        }
        function closeCreateResidentModal() {
            document.getElementById("createResidentModal").classList.add("hidden");
        }
//NOT YET WORKING        
function editUser(userId) {
    Swal.fire({
        title: 'Edit User',
        text: "Do you want to edit this user’s details?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, edit'
    }).then((result) => {
        if (result.isConfirmed) {
            openEditModal(userId);
        }
    });
}
function openEditModal(userId) {
    fetch(`../../assets/modals/edit_user_modal.php?user_id=${userId}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('modalContainer').innerHTML = html;
            const modal = document.getElementById('editUserModal');
            const closeBtn = document.getElementById('closeEditModal');

            modal.classList.remove('hidden');

            closeBtn.addEventListener('click', () => {
                modal.remove();
            });

            document.getElementById('editUserForm').addEventListener('submit', (e) => {
                e.preventDefault();

                const formData = new FormData(e.target);

                fetch('../../../backend/actions/user/update_user.php', {
                    method: 'POST',
                    body: new URLSearchParams(formData)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Updated!', data.message, 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error!', 'Update request failed.', 'error');
                });
            });
        });
}
