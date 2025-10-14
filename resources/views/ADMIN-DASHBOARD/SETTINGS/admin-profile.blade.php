<!-- Admin Profile (Blade) -->
<div class="bg-white p-8 rounded-lg shadow details-panel w-full  mx-auto" style="height: 650px;">
  <h1 class="text-3xl font-bold text-gray-800 mb-6">Admin Profile</h1>

  <form id="admin-form" method="POST">
    @csrf

    <div class="space-y-6">
      <!-- Full Name -->
      <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
        <label for="admin-name" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Full Name</label>
        <input type="text" id="admin-name" name="admin_name"
               class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="Full name" required disabled>
      </div>

      <!-- Email -->
      <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
        <label for="admin-email" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Email</label>
        <input type="email" id="admin-email" name="admin_email"
               class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="Email" required disabled>
      </div>

      <!-- Phone -->
      <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
        <label for="admin-phone" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Phone Number</label>
        <input type="text" id="admin-phone" name="admin_phone"
               class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="e.g. 0999-123-4567" required disabled>
      </div>

      <!-- Station Status -->
      <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
        <label for="admin-status" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Station Status</label>
        <select id="admin-status" name="admin_status"
                class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                disabled>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
      </div>

      <!-- Change Password -->
      <div class="text-xl font-semibold text-gray-800">Change Password</div>

      <!-- Current Password + Verify -->
      <div class="flex flex-col md:flex-row items-center pb-1">
        <label for="current-password" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Current Password</label>
        <div class="ml-4 w-full md:w-3/4 flex gap-3">
          <input type="password" id="current-password"
                 class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                 placeholder="Enter current password" disabled>
          <button type="button" id="verify-btn"
                  class="bg-gray-700 text-white py-2 px-4 rounded-lg hover:bg-gray-800 transition"
                  disabled>Verify</button>
        </div>
      </div>
      <div id="verify-msg" class="ml-0 md:ml-[25%] text-sm h-5 text-gray-500"></div>

      <!-- New/Confirm Password (hidden until verified) -->
      <div id="new-pass-block" class="hidden">
        <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
          <label for="admin-password" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">New Password</label>
          <input type="password" id="admin-password" name="admin_password"
                 class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                 placeholder="Enter new password" disabled>
        </div>
        <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
          <label for="admin-password-confirm" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Confirm Password</label>
          <input type="password" id="admin-password-confirm" name="admin_password_confirm"
                 class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                 placeholder="Confirm new password" disabled>
        </div>
      </div>
    </div>

    <!-- Buttons -->
    <div class="mt-6 flex justify-between">
      <button type="button" id="edit-btn"
              class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 transition">
        Edit
      </button>
      <button type="submit" id="save-btn"
              class="bg-green-600 text-white py-2 px-6 rounded-lg hover:bg-green-700 transition hidden">
        Save Changes
      </button>
    </div>
  </form>
</div>

<!-- Firebase SDKs (v8) -->
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Firebase config
  const firebaseConfig = {
    apiKey: "AIzaSyCrjSyOI-qzCaJptEkWiRfEuaG28ugTmdE",
    authDomain: "capstone-flare-2025.firebaseapp.com",
    databaseURL: "https://capstone-flare-2025-default-rtdb.firebaseio.com",
    projectId: "capstone-flare-2025",
    storageBucket: "capstone-flare-2025.firebasestorage.app",
    messagingSenderId: "685814202928",
    appId: "1:685814202928:web:9b484f04625e5870c9a3f5",
    measurementId: "G-QZ8P5VLHF2"
  };

  if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);
  const db = firebase.database();

  /* ---------- Single node: Tagum City Central Fire Station ---------- */
  const PROFILE_PATH = 'TagumCityCentralFireStation/Profile';
  const profileRef = db.ref(PROFILE_PATH);

  /* ---------- Elements ---------- */
  const form = document.getElementById('admin-form');
  const editBtn = document.getElementById('edit-btn');
  const saveBtn = document.getElementById('save-btn');
  const inputsAndSelects = document.querySelectorAll('input, select');

  const nameEl = document.getElementById('admin-name');
  const emailEl = document.getElementById('admin-email');
  const phoneEl = document.getElementById('admin-phone');
  const statusEl = document.getElementById('admin-status');

  const currentPassEl = document.getElementById('current-password');
  const verifyBtn = document.getElementById('verify-btn');
  const verifyMsg = document.getElementById('verify-msg');

  const newPassBlock = document.getElementById('new-pass-block');
  const passEl = document.getElementById('admin-password');
  const pass2El = document.getElementById('admin-password-confirm');

  // Password verification state
  let passwordVerified = false;
  let verifiedWithPassword = '';

  /* ---------- Load current profile (once) ---------- */
  profileRef.once('value').then(snap => {
    const data = snap.val() || {};
    nameEl.value = (data.name || '').trim();
    emailEl.value = (data.email || '').trim();
    phoneEl.value = (data.contact || '').trim();
    const st = (data.status || '').trim();
    statusEl.value = (st === 'Active' || st === 'Inactive') ? st : 'Active';
  }).catch(err => console.error('Failed to load profile:', err));

  /* ---------- Edit / Cancel ---------- */
  editBtn.addEventListener('click', function () {
    const enabling = (editBtn.textContent === 'Edit');

    // Toggle fields (but keep new-password block hidden until verification)
    inputsAndSelects.forEach(el => el.disabled = !enabling ? true : false);
    if (enabling) {
      editBtn.textContent = 'Cancel';
      saveBtn.classList.remove('hidden');
      // Enable current password + verify button, keep new password hidden/disabled
      currentPassEl.disabled = false;
      verifyBtn.disabled = false;
      newPassBlock.classList.add('hidden');
      passEl.disabled = true;
      pass2El.disabled = true;
      passEl.value = '';
      pass2El.value = '';
      passwordVerified = false;
      verifiedWithPassword = '';
      verifyMsg.textContent = 'Enter your current password, then click Verify.';
      verifyMsg.className = 'ml-0 md:ml-[25%] text-sm h-5 text-gray-600';
    } else {
      // Cancel: reload values & lock everything
      profileRef.once('value').then(snap => {
        const data = snap.val() || {};
        nameEl.value = (data.name || '').trim();
        emailEl.value = (data.email || '').trim();
        phoneEl.value = (data.contact || '').trim();
        const st = (data.status || '').trim();
        statusEl.value = (st === 'Active' || st === 'Inactive') ? st : 'Active';
      }).finally(() => {
        inputsAndSelects.forEach(el => el.disabled = true);
        currentPassEl.value = '';
        passEl.value = '';
        pass2El.value = '';
        newPassBlock.classList.add('hidden');
        verifyMsg.textContent = '';
        editBtn.textContent = 'Edit';
        saveBtn.classList.add('hidden');
        passwordVerified = false;
        verifiedWithPassword = '';
      });
    }
  });

  /* ---------- Verify current password (secondary app) ---------- */
  verifyBtn.addEventListener('click', async function () {
    const email = (emailEl.value || '').trim();
    const pwd = (currentPassEl.value || '').trim();

    if (!email || !pwd) {
      verifyMsg.textContent = 'Enter your current password first.';
      verifyMsg.className = 'ml-0 md:ml-[25%] text-sm h-5 text-red-600';
      return;
    }

    // Create a secondary app to verify without touching main auth state
    let secondaryApp = null;
    try {
      secondaryApp = firebase.apps.find(a => a.name === 'verifyApp') || firebase.initializeApp(firebaseConfig, 'verifyApp');
      const userCred = await secondaryApp.auth().signInWithEmailAndPassword(email, pwd);
      if (!userCred || !userCred.user) throw new Error('No user returned');

      // Success: show new password fields, lock current field, keep password for update step
      passwordVerified = true;
      verifiedWithPassword = pwd;

      verifyMsg.textContent = 'Password verified.';
      verifyMsg.className = 'ml-0 md:ml-[25%] text-sm h-5 text-green-700';

      currentPassEl.disabled = true;
      verifyBtn.disabled = true;

      newPassBlock.classList.remove('hidden');
      passEl.disabled = false;
      pass2El.disabled = false;

      // Clean secondary session
      await secondaryApp.auth().signOut();
    } catch (err) {
      console.error('Verification failed:', err);
      passwordVerified = false;
      verifiedWithPassword = '';
      verifyMsg.textContent = 'Current password is incorrect.';
      verifyMsg.className = 'ml-0 md:ml-[25%] text-sm h-5 text-red-600';
    } finally {
      // do not delete the app every time; reuse speeds up retries
    }
  });

  /* ---------- Save ---------- */
  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    // Build profile update (no plaintext password saved)
    const rawStatus = (statusEl.value || '').trim();
    const status = rawStatus === 'Active' ? 'Active'
                 : rawStatus === 'Inactive' ? 'Inactive'
                 : 'Inactive';

    const updatedData = {
      name: nameEl.value,
      email: emailEl.value,
      contact: phoneEl.value,
      status: status
    };

    // If user provided a new password, require prior verification and then update Firebase Auth
    const p1 = (passEl.value || '').trim();
    const p2 = (pass2El.value || '').trim();

    try {
      if (p1 || p2) {
        if (!passwordVerified) {
          alert('Please verify your current password first.');
          return;
        }
        if (p1.length < 6) { alert('New password must be at least 6 characters.'); return; }
        if (p1 !== p2) { alert('New passwords do not match.'); return; }

        // Use secondary app to reauth & update password
        const email = (emailEl.value || '').trim();
        const secondaryApp = firebase.apps.find(a => a.name === 'verifyApp') || firebase.initializeApp(firebaseConfig, 'verifyApp');
        const cred = await secondaryApp.auth().signInWithEmailAndPassword(email, verifiedWithPassword);
        if (!cred || !cred.user) throw new Error('Reauth failed');
        await cred.user.updatePassword(p1);
        await secondaryApp.auth().signOut();
      }

      // Write profile fields
      await profileRef.update(updatedData);

      alert('Profile updated successfully.');
      // Lock fields again
      inputsAndSelects.forEach(el => el.disabled = true);
      editBtn.textContent = 'Edit';
      saveBtn.classList.add('hidden');

      // Reset password UI
      currentPassEl.value = '';
      passEl.value = '';
      pass2El.value = '';
      newPassBlock.classList.add('hidden');
      verifyMsg.textContent = '';
      passwordVerified = false;
      verifiedWithPassword = '';
      verifyBtn.disabled = true;   // disabled because we left edit mode
    } catch (err) {
      console.error('Save failed:', err);
      alert('Failed to save changes. Check console for details.');
    }
  });

  /* ---------- Enable Verify only in Edit mode ---------- */
  // Keep verify disabled until Edit is pressed (handled in Edit handler)
});
</script>
