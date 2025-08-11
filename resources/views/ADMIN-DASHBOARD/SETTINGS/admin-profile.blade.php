<!-- Admin Profile (Blade) -->
<div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Admin Profile</h1>

    <form method="POST">
        @csrf
        <div class="space-y-6">
            <!-- Admin Name -->
            <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
                <label for="admin-name" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Full Name</label>
                <input type="text" id="admin-name" name="admin_name" class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="John Doe" required>
            </div>

            <!-- Admin Email -->
            <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
                <label for="admin-email" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Email</label>
                <input type="email" id="admin-email" name="admin_email" class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="johndoe@example.com" required>
            </div>

            <!-- Phone Number -->
            <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
                <label for="admin-phone" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Phone Number</label>
                <input type="text" id="admin-phone" name="admin_phone" class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="123-456-7890" required>
            </div>

            <!-- Change Password -->
            <div class="text-xl font-semibold text-gray-800 mb-4">Change Password</div>

            <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
                <label for="admin-password" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">New Password</label>
                <input type="password" id="admin-password" name="admin_password" class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter new password">
            </div>

            <div class="flex flex-col md:flex-row items-center border-b border-gray-300 pb-4">
                <label for="admin-password-confirm" class="w-full md:w-1/4 text-lg font-semibold text-gray-700">Confirm Password</label>
                <input type="password" id="admin-password-confirm" name="admin_password_confirm" class="ml-4 w-full md:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Confirm new password">
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 transition">Save Changes</button>
        </div>
    </form>
</div>

<!-- Firebase SDKs (match versions used elsewhere) -->
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>

<script>
/**
 * Mirrors the sidebar logic: detect which Fire Station node matches the logged-in email,
 * then populate the profile form with that station's contact name, email, and phone.
 */

document.addEventListener('DOMContentLoaded', function () {
    // Firebase config
    const firebaseConfig = {
        apiKey: "AIzaSyC1CtwiZwi120k6VrJwbFGKIY4pEJyxHxU",
        authDomain: "flare-capstone-c029d.firebaseapp.com",
        databaseURL: "https://flare-capstone-c029d-default-rtdb.firebaseio.com",
        projectId: "flare-capstone-c029d",
        storageBucket: "flare-capstone-c029d.firebasestorage.app",
        messagingSenderId: "683706660470",
        appId: "1:683706660470:web:d1302ac460f71bf8929157",
        measurementId: "G-QDWQJKCYPN"
    };

    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }
    const db = firebase.database();

    const userEmail = "{{ session('firebase_user_email') }}";
    if (!userEmail) return;

    const stations = [
        { name: 'CanocotanFireStation', profile: 'CanocotanProfile' },
        { name: 'LaFilipinaFireStation', profile: 'LaFilipinaProfile' },
        { name: 'MabiniFireStation', profile: 'MabiniProfile' }
    ];

    stations.forEach(station => {
        const profileRef = `${station.name}/${station.profile}`;

        db.ref(profileRef).once('value').then(snap => {
            const data = snap.val();
            if (!data) return;

            const stationEmail = String(data.email || '').trim().toLowerCase();
            if (stationEmail && stationEmail === String(userEmail).trim().toLowerCase()) {
                applyStationDataToForm(data);
            }
        }).catch(err => {
            console.error('Error fetching data for ' + station.name + ':', err);
        });
    });

    function applyStationDataToForm(data) {
        const contactName = firstNonEmpty(
            data.name  // Corrected field for name (not contactName)
        );

        const email = firstNonEmpty(
            data.email  // Correct field for email
        );

        const phone = firstNonEmpty(
            data.contact  // Corrected field for contact (not phone)
        );

        setValue('admin-name', contactName);
        setValue('admin-email', email);
        setValue('admin-phone', phone);

        tryLockFields(['admin-name', 'admin-email', 'admin-phone']);
    }

    function firstNonEmpty(/* ...vals */) {
        for (let i = 0; i < arguments.length; i++) {
            const v = arguments[i];
            if (v !== undefined && v !== null && String(v).trim() !== '') return String(v).trim();
        }
        return '';
    }

    function setValue(id, val) {
        const el = document.getElementById(id);
        if (el && typeof val === 'string' && val.trim() !== '') el.value = val;
    }

    function tryLockFields(ids) {
        ids.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.readOnly = true;
        });
    }
});
</script>
