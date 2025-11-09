<!-- Incident Modal -->
<div id="incidentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative overflow-y-auto max-h-[90vh]">
    <!-- Close Button -->
    <button onclick="closeIncidentModal()" class="absolute top-2 right-2 text-gray-600 hover:text-black text-2xl">
      &times;
    </button>

    <h2 class="text-2xl font-bold mb-4 text-center">Report an Incident</h2>

    <!-- ✅ Non-Resident Form -->
    <form id="nonResidentForm" class="mt-4 space-y-6 block">
      <h3 class="text-lg font-semibold text-gray-800 text-center sm:text-left">
        Non-Resident Details
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <input type="text" name="f_name" placeholder="First Name" required
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 uppercase" />
        <input type="text" name="m_name" placeholder="Middle Name (Optional)"
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 uppercase" />
        <input type="text" name="l_name" placeholder="Last Name" required
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 uppercase" />

        <select name="ext_name"
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 uppercase">
          <option value="" selected disabled>Extension (e.g., Jr., Sr., III)</option>
          <option value="JR">Jr.</option>
          <option value="SR">Sr.</option>
          <option value="II">II</option>
          <option value="III">III</option>
          <option value="IV">IV</option>
          <option value="V">V</option>
          <option value="NONE">None</option>
        </select>

        <input type="email" name="email" placeholder="Email Address" required
          class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500" />
        <div class="flex">
          <span class="inline-flex items-center px-3 border border-r-0 rounded-l-lg bg-gray-100 text-gray-600 text-sm">+63</span>
          <input type="text" name="contact_no" placeholder="9XXXXXXXXX" required
            class="w-full border border-gray-300 rounded-r-lg p-2 focus:ring-2 focus:ring-indigo-500" />
        </div>
      </div>

      <input type="text" name="address" placeholder="Complete Address" required
        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 uppercase" />

      <button type="button" onclick="verifyEmail()" id="verifyBtn"
        class="w-full sm:w-auto sm:px-8 bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition block sm:ml-auto">
        Verify Email
      </button>
    </form>

    <!-- OTP Verification -->
    <form id="otpForm" class="hidden mt-4">
      <p class="mb-2 font-semibold">
        Enter the verification code sent to your email
      </p>
      <input type="text" name="otp" class="w-full border rounded p-2 mb-3" placeholder="Enter OTP" required />
      <button type="button" onclick="submitOtp()" id="otpBtn"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        Continue
      </button>
      <button type="button" onclick="resendOtp()" id="resendBtn"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full sm:w-auto mt-2" disabled>
        Resend OTP (Wait 5:00)
      </button>
    </form>

    <!-- INCIDENT FORM -->
    <?php include('Incident_form_modal.php');?>


  </div>
</div>