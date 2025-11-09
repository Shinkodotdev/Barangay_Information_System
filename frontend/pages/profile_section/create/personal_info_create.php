<!-- ✅ PERSONAL INFORMATION -->
<section class="bg-gray-50 p-6 rounded-xl shadow-sm">
    <h3 class="text-2xl font-semibold text-indigo-600 mb-4 flex items-center gap-2">
        <i class="fa-solid fa-id-card"></i> PERSONAL INFORMATION
    </h3>

    <!-- ✅ Profile Photo -->
    <div class="sm:col-span-2">
        <label class="block text-gray-700 font-medium mb-1">Profile Photo</label>
        <input 
            type="file" 
            name="photo" 
            id="photoUpload" 
            accept="image/*" 
            capture="environment" 
            class="border rounded-lg p-3 w-full bg-white mb-3"
        >
        <p class="text-xs text-gray-500 mb-3">
            You can take a photo using your device camera or upload from your gallery.
        </p>
        <button 
            type="button" 
            id="openCameraBtn"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow">
            Use Webcam
        </button>
        <div id="cameraContainer" class="hidden mt-4">
            <video id="cameraPreview" autoplay playsinline class="border rounded-lg w-full"></video>
            
            <div class="flex justify-between mt-3">
                <button 
                    type="button" 
                    id="capturePhotoBtn" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow">
                    Capture Photo
                </button>
                <button 
                    type="button" 
                    id="cancelCameraBtn" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow">
                    Cancel
                </button>
            </div>
            
            <canvas id="photoCanvas" class="hidden"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">

        <input 
            type="email" 
            name="email" 
            placeholder="EMAIL ADDRESS" 
            class="border rounded-lg p-3 w-full "
            required>

        <input name="f_name" placeholder="FIRST NAME" class="border rounded-lg p-3 w-full uppercase" required>
        <input name="m_name" placeholder="MIDDLE NAME" class="border rounded-lg p-3 w-full uppercase">
        <input name="l_name" placeholder="LAST NAME" class="border rounded-lg p-3 w-full uppercase" required>

        <select name="ext_name" class="border rounded-lg p-3 w-full uppercase">
            <option value="">EXT. NAME</option>
            <option value="JR">JR.</option>
            <option value="SR">SR.</option>
            <option value="II">II</option>
            <option value="III">III</option>
            <option value="IV">IV</option>
        </select>

        <select name="gender" class="border rounded-lg p-3 w-full uppercase" required>
            <option value="">SELECT GENDER</option>
            <option>MALE</option>
            <option>FEMALE</option>
            <option>LGBTQ</option>
            <option>OTHER</option>
        </select>

        <div class="flex">
            <span class="inline-flex items-center px-3 border border-r-0 rounded-l-lg bg-gray-100 text-gray-700">+63</span>
            <input 
                type="text"
                name="contact_no"
                id="contact_no"
                placeholder="9123456789"
                class="border rounded-r-lg p-3 w-full uppercase"
                maxlength="10"
                pattern="[0-9]{10}"
                required>
        </div>

        <select name="civil_status" class="border rounded-lg p-3 w-full uppercase" required>
            <option value="">SELECT CIVIL STATUS</option>
            <option>SINGLE</option>
            <option>MARRIED</option>
            <option>WIDOWED</option>
            <option>SEPARATED</option>
        </select>

        <select name="occupation" class="border rounded-lg p-3 w-full uppercase" required>
            <option value="">OCCUPATION *</option>
            <option>STUDENT</option>
            <option>FARMER</option>
            <option>TEACHER</option>
            <option>GOVERNMENT EMPLOYEE</option>
            <option>PRIVATE EMPLOYEE</option>
            <option>BUSINESS OWNER</option>
            <option>UNEMPLOYED</option>
        </select>

        <select name="nationality" class="border rounded-lg p-3 w-full uppercase" required>
            <option value="">NATIONALITY *</option>
            <option>FILIPINO</option>
            <option>AMERICAN</option>
            <option>CHINESE</option>
            <option>JAPANESE</option>
            <option>KOREAN</option>
            <option>INDIAN</option>
        </select>

        <select name="religion" class="border rounded-lg p-3 w-full uppercase" required>
            <option value="">RELIGION *</option>
            <option>ROMAN CATHOLIC</option>
            <option>CHRISTIAN</option>
            <option>IGLESIA NI CRISTO</option>
            <option>ISLAM</option>
            <option>BUDDHISM</option>
            <option>HINDUISM</option>
            <option>JUDAISM</option>
            <option>MUSLIM</option>
        </select>

        <select name="blood_type" class="border rounded-lg p-3 w-full uppercase">
            <option value="">BLOOD TYPE</option>
            <option>A+</option>
            <option>A-</option>
            <option>B+</option>
            <option>B-</option>
            <option>O+</option>
            <option>O-</option>
            <option>AB+</option>
            <option>AB-</option>
        </select>

        <select name="voter_status" class="border rounded-lg p-3 w-full uppercase" required>
            <option value="">VOTER STATUS *</option>
            <option>YES</option>
            <option>NO</option>
        </select>

        <select name="pwd_status" class="border rounded-lg p-3 w-full uppercase">
            <option value="">PWD?</option>
            <option>YES</option>
            <option>NO</option>
        </select>

        <select name="senior_citizen_status" class="border rounded-lg p-3 w-full uppercase">
            <option value="">SENIOR CITIZEN?</option>
            <option>YES</option>
            <option>NO</option>
        </select>

        <select name="educational_attainment" class="border rounded-lg p-3 w-full uppercase">
            <option value="">EDUCATIONAL ATTAINMENT</option>
            <option>ELEMENTARY</option>
            <option>HIGH SCHOOL</option>
            <option>COLLEGE</option>
            <option>VOCATIONAL</option>
            <option>POSTGRADUATE</option>
        </select>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const contactInput = document.getElementById("contact_no");

    contactInput.addEventListener("input", () => {
        // Remove any non-digit characters
        contactInput.value = contactInput.value.replace(/\D/g, "");
    });

    contactInput.addEventListener("keypress", (e) => {
        // Prevent typing non-digits
        if (!/[0-9]/.test(e.key)) e.preventDefault();
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const openBtn = document.getElementById("openCameraBtn");
    const cancelBtn = document.getElementById("cancelCameraBtn");
    const cameraContainer = document.getElementById("cameraContainer");
    const video = document.getElementById("cameraPreview");
    const canvas = document.getElementById("photoCanvas");
    const fileInput = document.getElementById("photoUpload");
    const captureBtn = document.getElementById("capturePhotoBtn");
    let stream;

    // 🎥 Open Camera
    openBtn.addEventListener("click", async () => {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert("Camera not supported in this browser.");
            return;
        }

        cameraContainer.classList.remove("hidden");
        openBtn.classList.add("hidden");

        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
        } catch (err) {
            alert("Unable to access camera: " + err.message);
        }
    });

    // 📸 Capture Photo
    captureBtn.addEventListener("click", () => {
        const context = canvas.getContext("2d");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob(blob => {
            const file = new File([blob], "captured_photo.jpg", { type: "image/jpeg" });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;
        });

        stopCamera();
        cameraContainer.classList.add("hidden");
        openBtn.classList.remove("hidden");

        Swal.fire({
            title: "Photo Captured!",
            text: "Your photo has been attached successfully.",
            icon: "success",
            confirmButtonColor: "#4F46E5"
        });
    });

    // ❌ Cancel Camera
    cancelBtn.addEventListener("click", () => {
        stopCamera();
        cameraContainer.classList.add("hidden");
        openBtn.classList.remove("hidden");
    });

    // 🛑 Stop camera stream
    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }
});
</script>
