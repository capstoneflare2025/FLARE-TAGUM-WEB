<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Station Sidebar</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-6 right-6 z-50 space-y-3"></div>

    <!-- Preload Bootstrap Icons Font -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/fonts/bootstrap-icons.woff2" as="font" type="font/woff2" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">


    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        th, td { vertical-align: middle; }
        .toast { background-color: #E00024; color: white; padding: 1rem; border-radius: 0.5rem; box-shadow: 0 5px 15px rgba(0,0,0,0.3); min-width: 250px; max-width: 300px; opacity: 0.95; }
        @keyframes fade-in-down { 0% { opacity: 0; transform: translateY(-10%); } 100% { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-down { animation: fade-in-down 0.3s ease-out; }
        .logo { width: 100px; height: 100px; background: #f4f4f4; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .logo img { width: 80%; height: 80%; object-fit: contain; }
        .sidebar { width: 250px; background-color: #E00024; height: 100vh; position: fixed; top: 0; left: 0; padding-top: 20px; color: white; display: flex; flex-direction: column; }
        .sidebar img.logo { width: 50px; height: auto; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto; }
        .sidebar h2 { text-align: center; font-size: 22px; margin-bottom: 50px; }
        .sidebar a { color: white; text-decoration: none; padding: 15px; font-size: 18px; display: flex; align-items: center; border-bottom: 1px solid white; transition: background 0.3s; }
        .sidebar a:hover { background-color: #E87F2E; }
        .active { background-color: #E87F2E; border-radius: 10px; }
        .logout { margin-top: auto; padding: 15px; background-color: #e74c3c; text-align: center; color: white; cursor: pointer; }
        .logout:hover { background-color: #c0392b; }
        .sidebar img { width: 20px; height: 20px; margin-right: 20px; }
        .main-content{ margin-left: 260px; padding: 20px; margin-top: 20px; }
    </style>
</head>
<body resetReloadTimer()>

    @include('ADMIN-DASHBOARD.sidebar')

    <div class="main-content">
        @yield('content')
    </div>

    @yield('scripts')

    <!-- Firebase SDKs -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

   <!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Routing plugin -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

<script>
/* =========================
 * Globals
 * ========================= */
let canocotanRef, laFilipinaRef, mabiniRef, responseMessageRef;
let fireSound, emergencySound;
const activeToasts = new Map();

/* =========================
 * Helpers: Priming
 * ========================= */
// Prime the last child key so the first live event won't toast old data
async function primeLastKey(ref, storageKey) {
  try {
    const snap = await ref.limitToLast(1).once('value');
    snap.forEach(child => {
      localStorage.setItem(storageKey, child.key);
    });
  } catch (e) {
    console.warn('primeLastKey failed:', storageKey, e);
  }
}

// For replies, prime per-incident so opening the page doesn’t show the latest old reply
async function primeLastReplyKey(messagesRef, storageKey) {
  try {
    const snap = await messagesRef.limitToLast(1).once('value');
    snap.forEach(child => {
      localStorage.setItem(storageKey, child.key);
    });
  } catch (e) {
    console.warn('primeLastReplyKey failed:', storageKey, e);
  }
}

/* =========================
 * Toasts
 * ========================= */
function showQueuedToast({ id, type, reporterName = 'Unknown', message = '', sound = null }) {
  if (!id) return;
  if (activeToasts.has(id)) return;

  const toastContainer = document.getElementById("toastContainer");
  if (!toastContainer) return;

  const toast = document.createElement("div");
  toast.className = "toast flex justify-between items-center gap-4 animate-fade-in-down";
  toast.innerHTML = `
    <div class="text-sm font-semibold leading-snug">
      ${message}<br><span class="text-xs font-normal">Reporter: ${reporterName}</span>
    </div>
    <button class="text-white font-bold text-sm hover:opacity-75" onclick="handleViewIncident('${id}', '${type}')">View</button>
  `;
  toastContainer.appendChild(toast);

  try { sound?.play?.().catch(() => {}); } catch {}

  const timeout = setTimeout(() => removeToast(id), 10000);
  activeToasts.set(id, { element: toast, timeout });
}

function removeToast(id) {
  const toastData = activeToasts.get(id);
  if (!toastData) return;
  toastData.element.classList.add('opacity-0', 'transition-opacity', 'duration-500');
  setTimeout(() => toastData.element.remove(), 500);
  clearTimeout(toastData.timeout);
  activeToasts.delete(id);
}

/* =========================
 * Cross-page safe navigation
 * ========================= */
function handleViewIncident(uniqueId, type) {
  // uniqueId is `${base}|${id}` or `${base}|${id}|msg`
  const parts = String(uniqueId).split('|');
  const id = parts.length >= 2 ? parts[1] : uniqueId;
  if (!id || !type) return;

  const row = document.getElementById(`reportRow${id}`);
  const detectedType = row?.getAttribute('data-type') || type;

  if (type === 'message') {
    // If chat modal exists on this page, open it; else deep-link
    if (typeof openMessageModal === 'function') {
      const guessedType = (detectedType === 'otherEmergency') ? 'otherEmergency' : 'fireReports';
      openMessageModal(id, guessedType);
    } else {
      window.location.href = `/app/incident-reports?incidentId=${encodeURIComponent(id)}&type=${encodeURIComponent('fireReports')}&modal=details`;
    }
    return;
  }

  if (typeof openDetailsModal === 'function' && row) {
    openDetailsModal(id, detectedType);
  } else {
    const url = `/app/incident-reports?incidentId=${encodeURIComponent(id)}&type=${encodeURIComponent(detectedType)}&modal=details`;
    window.location.href = url;
  }
}

/* =========================
 * Main
 * ========================= */
document.addEventListener('DOMContentLoaded', async function () {
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

  // ✅ Guard Firebase init (prevents crash when navigating between pages)
  if (!firebase.apps.length) {
    firebase.initializeApp(firebaseConfig);
  }
  const database = firebase.database();

  // ✅ Singleton guard so listeners aren’t registered multiple times
  if (window.__flareRealtimeStarted) return;
  window.__flareRealtimeStarted = true;

  // Roots (for fallback, profile lookups, etc.)
  canocotanRef  = database.ref('CanocotanFireStation');
  laFilipinaRef = database.ref('LaFilipinaFireStation');
  mabiniRef     = database.ref('MabiniFireStation');

  const sessionEmail = ("{{ session('firebase_user_email') }}" || "").toLowerCase();

  // Sounds
  fireSound = new Audio("{{ asset('sound/alert.mp3') }}");
  emergencySound = new Audio("{{ asset('sound/emergency.mp3') }}");
  fireSound.preload = emergencySound.preload = "auto";

  // Unlock audio on first gesture
  document.addEventListener('click', function unlockAudio() {
    try { fireSound.play().catch(() => {}); emergencySound.play().catch(() => {}); } catch {}
    fireSound.pause(); emergencySound.pause();
    fireSound.currentTime = 0; emergencySound.currentTime = 0;
    document.removeEventListener('click', unlockAudio);
  });

  // ===== Keep your Fire Station name display intact =====
  const stationProfileKey = {
    CanocotanFireStation: 'CanocotanProfile',
    LaFilipinaFireStation: 'LaFilipinaProfile',
    MabiniFireStation: 'MabiniProfile'
  };

  function checkUserInFireStation(station, email) {
    const profileKey = stationProfileKey[station];
    database.ref(`${station}/${profileKey}`).once('value').then(snap => {
      let data = snap.val();
      if (!data) return tryOldRoot();
      const em = (data.email || '').toLowerCase();
      if (em && em === email.toLowerCase()) {
        const el = document.getElementById('station-name');
        if (el) el.innerText = data.name || station;
      }
    }).catch(tryOldRoot);

    function tryOldRoot() {
      database.ref(station).once('value').then(snap => {
        const data = snap.val() || {};
        const em = (data.email || '').toLowerCase();
        if (em && em === email.toLowerCase()) {
          const el = document.getElementById('station-name');
          if (el) el.innerText = data.name || station;
        }
      }).catch(() => {});
    }
  }

  if (sessionEmail) {
    checkUserInFireStation('CanocotanFireStation', sessionEmail);
    checkUserInFireStation('LaFilipinaFireStation', sessionEmail);
    checkUserInFireStation('MabiniFireStation', sessionEmail);
  }
  // ======================================================

  // Menu active state (optional)
  document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', () => {
      document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
      item.classList.add('active');
    });
  });

  // Email -> station nodes
  function nodesFor(base, prefix) {
    return {
      base,
      fire: `${base}/${prefix}FireReport`,
      emergency: `${base}/${prefix}OtherEmergency`,
      response: `${base}/ResponseMessage`
    };
  }

  function getNodesByEmail(email) {
    const e = (email || "").toLowerCase();
    if (e.includes("mabini123@gmail.com"))     return nodesFor("MabiniFireStation", "Mabini");
    if (e.includes("canocotan123@gmail.com"))  return nodesFor("CanocotanFireStation", "Canocotan");
    if (e.includes("lafilipina123@gmail.com")) return nodesFor("LaFilipinaFireStation", "LaFilipina");
    return null;
  }

  let nodes = getNodesByEmail(sessionEmail);

  // ✅ Fallback: attach listeners to ALL 3 stations if session email is missing/mismatched
  const nodeBundles = nodes ? [nodes] : [
    nodesFor("MabiniFireStation", "Mabini"),
    nodesFor("CanocotanFireStation", "Canocotan"),
    nodesFor("LaFilipinaFireStation", "LaFilipina"),
  ];

  // Prime all station bundles BEFORE attaching listeners (prevents showing last historical)
  for (const n of nodeBundles) {
    await primeLastKey(firebase.database().ref(n.fire),      `last_fire_id_${n.base}`);
    await primeLastKey(firebase.database().ref(n.emergency), `last_emergency_id_${n.base}`);
  }

  // Attach listeners
  nodeBundles.forEach(n => attachListenersFor(n));

  // Expose responseMessageRef if needed elsewhere
  responseMessageRef = database.ref((nodeBundles[0] || {}).response);

  /* =========================
   * Listener bundle
   * ========================= */
  function attachListenersFor(n) {
    // FIRE — toast first, then optional page update
    firebase.database().ref(n.fire).limitToLast(1).on('child_added', snap => {
      const v = snap.val(); const id = snap.key;
      if (!v || !id) return;

      const seenKey = `last_fire_id_${n.base}`;
      if (localStorage.getItem(seenKey) === id) return;  // ignore primed last
      localStorage.setItem(seenKey, id);

      v.id = id;

      showQueuedToast({
        id: `${n.base}|${id}`,
        type: 'fireReports',
        reporterName: v.name || 'Unknown',
        message: "Fire Report! A new incident has been added.",
        sound: fireSound
      });

      // Optional page updates (guarded so they don’t block the toast)
      try {
        if (typeof insertNewReportRow === 'function') insertNewReportRow(v, 'fireReports');
        if (typeof renderAllReports === 'function')   renderAllReports();
      } catch(e){ console.warn('[fire optional update]', e); }
    });

    // OTHER EMERGENCY — toast first, then optional update
    firebase.database().ref(n.emergency).limitToLast(1).on('child_added', snap => {
      const v = snap.val(); const id = snap.key;
      if (!v || !id) return;

      const seenKey = `last_emergency_id_${n.base}`;
      if (localStorage.getItem(seenKey) === id) return;  // ignore primed last
      localStorage.setItem(seenKey, id);

      v.id = id;

      showQueuedToast({
        id: `${n.base}|${id}`,
        type: 'otherEmergency',
        reporterName: v.name || 'Unknown',
        message: "Emergency Report! A new incident has been added.",
        sound: emergencySound
      });

      try {
        if (typeof insertNewReportRow === 'function') insertNewReportRow(v, 'otherEmergency');
        if (typeof renderAllReports === 'function')   renderAllReports();
      } catch(e){ console.warn('[other optional update]', e); }
    });

    // Replies under FIRE — prime per incident, then listen
    firebase.database().ref(n.fire).on('child_added', async reportSnap => {
      const incidentId = reportSnap.key;
      const reportData = reportSnap.val();
      const msgsRef  = reportSnap.ref.child('messages');
      const seenKey  = `last_reply_id_${n.base}_${incidentId}`;

      // Prime reply so existing last reply won't toast on first load
      await primeLastReplyKey(msgsRef, seenKey);

      msgsRef.limitToLast(1).on('child_added', msgSnap => {
        const msg = msgSnap.val();
        if (!msg || (String(msg.type||'').toLowerCase() !== 'reply')) return;

        if (localStorage.getItem(seenKey) === msgSnap.key) return; // ignore primed one
        localStorage.setItem(seenKey, msgSnap.key);

        showQueuedToast({
          id: `${n.base}|${incidentId}|msg`,
          type: 'message',
          reporterName: reportData?.name || msg.reporterName || 'Unknown',
          message: "New Message Received. Tap to view the chat.",
          sound: null
        });
      });
    });

    // Replies under OTHER — prime per incident, then listen
    firebase.database().ref(n.emergency).on('child_added', async reportSnap => {
      const incidentId = reportSnap.key;
      const reportData = reportSnap.val();
      const msgsRef = reportSnap.ref.child('messages');
      const seenKey = `last_reply_id_${n.base}_${incidentId}`;

      await primeLastReplyKey(msgsRef, seenKey);

      msgsRef.limitToLast(1).on('child_added', msgSnap => {
        const msg = msgSnap.val();
        if (!msg || (String(msg.type||'').toLowerCase() !== 'reply')) return;

        if (localStorage.getItem(seenKey) === msgSnap.key) return; // ignore primed one
        localStorage.setItem(seenKey, msgSnap.key);

        showQueuedToast({
          id: `${n.base}|${incidentId}|msg2`,
          type: 'message',
          reporterName: reportData?.name || msg.reporterName || 'Unknown',
          message: "New Message Received. Tap to view the chat.",
          sound: null
        });
      });
    });

    // Response status changes — guard optional table updater
    firebase.database().ref(n.response).on('child_changed', snapshot => {
      const updatedReport = snapshot.val() || {};
      try {
        if (typeof updateTableStatus === 'function') {
          updateTableStatus(updatedReport.id, updatedReport.status);
        }
      } catch (e) { console.warn('[updateTableStatus failed]', e); }
    });
  }

}); // DOMContentLoaded
</script>


@stack('scripts')


<script>
  let reloadTimer;

  // Function to reset the reload timer
  function resetReloadTimer() {
    clearTimeout(reloadTimer);
    reloadTimer = setTimeout(() => {
      location.reload();
    }, 300000); // 5 minutes = 300,000 ms
  }

  // Reset timer on page load
  window.onload = resetReloadTimer;

  // Reset timer on user interactions
  ['click', 'mousemove', 'keydown', 'scroll', 'touchstart'].forEach(evt => {
    document.addEventListener(evt, resetReloadTimer, false);
  });
</script>

</body>
</html>
