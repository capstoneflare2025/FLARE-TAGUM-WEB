<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Station Sidebar</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-6 right-6 z-50 space-y-3"></div>
    <!-- put this in your main layout <head> -->
    <meta name="station-key" content="{{ session('station') ?? 'TagumCityCentralFireStation' }}">

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
        .msg-btn { position: relative; display: inline-block; }
        .msg-badge {
        position: absolute; top: -6px; right: -6px;
        min-width: 16px; height: 16px; padding: 0 4px;
        border-radius: 9999px; font-size: 10px; line-height: 16px;
        background: #ef4444; color: #fff; text-align: center;
        box-shadow: 0 0 0 2px #fff;
        }
        .msg-badge.hidden { display: none; }

        #ffChatMsgThread {
        height: 400px;
        overflow-y: auto;
        }



            /* hamburger button */
    .sidebar-toggle{
    position: fixed; top: 12px; left: 12px;
    z-index: 1100; width: 40px; height: 40px;
    border: none; border-radius: 8px;
    background: #E00024; color:#fff;
    display: none; align-items:center; justify-content:center;
    box-shadow: 0 4px 12px rgba(0,0,0,.25);
    cursor: pointer;
    }

    /* dim background behind drawer */
    .sidebar-backdrop{
    position: fixed; inset: 0;
    background: rgba(0,0,0,.4);
    z-index: 1040; display: none;
    }

    /* always show the button */
    @media (min-width: 769px){
    .sidebar-toggle{ display: none; }   /* <-- hides it on desktop */
    }


/* phone: off-canvas drawer */
@media (max-width: 768px){
  body{ overflow-x: hidden; }

  .sidebar-toggle{ display: inline-flex; }

  .sidebar{
    position: fixed; top: 0; left: 0;
    height: 100vh;
    width: 78vw; max-width: 320px;
    transform: translateX(-100%);
    transition: transform .25s ease;
    z-index: 1050;
  }
  .sidebar.open{ transform: translateX(0); }
  .sidebar-backdrop.show{ display: block; }

  /* content should use full width on mobile */
  .main-content{ margin: 0; padding: 16px; }

  /* small typography tweaks */
  .sidebar h2{ font-size: 18px; margin-bottom: 24px; }
  .sidebar img.logo{ width: 120px; height: auto; }
  .sidebar a{ font-size: 16px; padding: 12px 14px; }
}


/* FIRE FIGHTERS BLADE */

/* --- Table: prevent squish & allow horizontal scroll --- */
.responsive-table {
  min-width: 900px; /* keep columns readable; scrolls on smaller screens */
}

.table-auto th,
.table-auto td {
  text-align: center;
  vertical-align: middle; /* keeps vertical alignment too */
}
/* --- Small-screen tuning --- */
@media (max-width: 640px){
  .table-xs th, .table-xs td { padding: 8px 10px; font-size: 12px; }
  .btn-sm { padding: 6px 10px; font-size: 12px; }
  .chips-xs { font-size: 11px; }
}

/* --- Long text (emails) should wrap instead of overflowing --- */
.break-anywhere {
  overflow-wrap: anywhere; /* modern browsers */
  word-break: break-word;  /* fallback */
}

/* --- Modals: keep within viewport (portrait & landscape) --- */
.modal-panel {
  max-height: 85vh;
  overflow-y: auto;
}


/* Modal overlay above everything */
.modal{
  position: fixed;
  inset: 0;
  display: none;                /* show via JS */
  justify-content: center;
  align-items: center;
  background: rgba(0,0,0,.5);
  z-index: 9999;                /* ⬅ higher than any header */
}

/* Modal panel */
.modal-content{
  position: relative;
  z-index: 10000;               /* just above the overlay */
}

/* Optional: when modal open, stop background scroll */
body.modal-open { overflow: hidden; }



/*
Indicent blade */
body.modal-open { overflow: hidden; }

/* ✅ kill panel scrolling and keep it centered + inside the viewport */
#fireMessageModal .modal-panel,
#otherEmergencyMessageModal .modal-panel,
#emsMessageModal .modal-panel{
  max-height: none !important;        /* override your 85vh */
  overflow: visible !important;        /* no panel scrollbars */
  width: min(96vw, 1100px);            /* never overflow viewport width */
  border-radius: 12px;
}

/* smaller width for the two medium modals */
#otherEmergencyMessageModal .modal-panel,
#emsMessageModal .modal-panel{
  width: min(96vw, 680px);
}

/* only the thread scrolls if messages are long (not the whole modal) */
#fireMessageThread,
#otherEmergencyMessageThread,
#emsMessageThread{
  max-height: 60vh;        /* adjust if you want more/less */
  overflow-y: auto;
}

/* --- Generic table responsiveness --- */
.table-wrap{ overflow-x:auto; -webkit-overflow-scrolling:touch; }
.table-min{ min-width: 900px; }              /* keeps columns readable; scrolls on small screens */
.cell-nowrap{ white-space:nowrap; }
.break-anywhere{ overflow-wrap:anywhere; word-break:break-word; }

/* --- Filter row: stack on phones --- */
.filters-grid{ display:grid; grid-template-columns:1fr; gap:.5rem; }
@media (min-width: 640px){ .filters-grid{ grid-template-columns: repeat(3, 1fr); } }

/* --- Shrink paddings/fonts for very small screens --- */
@media (max-width: 640px){
  th, td{ padding: 8px 10px !important; font-size: 12px; }
}

@media (max-width: 480px){
  .col-datetime{ display: table-cell !important; }               /* keep date/time hidden */
  .col-index{ display: table-cell !important; } /* keep # visible */
}


/* --- Modals never exceed viewport; scroll inside --- */
.modal-panel{ max-height:85vh; overflow-y:auto; }
@media (max-height: 480px) and (orientation: landscape){
  .modal-panel{ max-height:78vh; }
}

/* --- Location modal: override desktop wide layout on mobile --- */
@media (max-width: 768px){
  #locationModal .modal-shell{
    width: 95vw !important;
    margin-left: 0 !important;
    max-width: 95vw !important;
  }
  #routeMap, #fenceMap{ height: 300px !important; }
  #routeInfo{ max-height: 100px !important; }
}

/* --- Toast: don’t collide with sidebar on small screens --- */
@media (max-width: 768px){
  #toast{ right: 1rem !important; margin-right: 0 !important; left: 1rem; }
}



.msg-btn{ position:relative; display:inline-flex; align-items:center; }
.msg-badge{
  position:absolute; top:-6px; right:-6px;
  min-width:18px; height:18px; line-height:18px;
  font-size:12px; border-radius:9999px;
  background:#ef4444; color:#fff; text-align:center; padding:0 5px;
}
.msg-badge.hidden{ display:none; }


    </style>
</head>
<body resetReloadTimer()>

    <!-- Mobile menu button + backdrop (must be OUTSIDE the sidebar) -->
<button style="background-color: #E87F2E" class="sidebar-toggle" aria-label="Open menu" onclick="toggleSidebar()">
  <i  class="fas fa-bars"></i>
</button>
<div id="sidebarBackdrop" class="sidebar-backdrop" onclick="toggleSidebar(false)"></div>


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

     function toggleSidebar(force){
    const isMobile = window.matchMedia('(max-width: 768px)').matches;
    const sidebar  = document.querySelector('.sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (isMobile){
      const willOpen = (typeof force === 'boolean') ? force : !sidebar.classList.contains('open');
      if (willOpen){
        sidebar.classList.add('open');
        backdrop && backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
      } else {
        sidebar.classList.remove('open');
        backdrop && backdrop.classList.remove('show');
        document.body.style.overflow = '';
      }
    } else {
      // desktop: toggle collapsed state on the body
      document.body.classList.toggle('sidebar-closed',
        (typeof force === 'boolean') ? !force : !document.body.classList.contains('sidebar-closed')
      );
    }
  }

  // Close drawer when a sidebar link is clicked (mobile)
  document.addEventListener('click', (e) => {
    if (window.matchMedia('(max-width: 768px)').matches && e.target.closest('.sidebar a')){
      toggleSidebar(false);
    }
  });

  // ESC to close (mobile drawer, or reopen desktop if you want)
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    const isMobile = window.matchMedia('(max-width: 768px)').matches;
    if (isMobile && document.querySelector('.sidebar.open')) toggleSidebar(false);
  });
/* =========================
 * Globals
 * ========================= */

let responseMessageRef;
let fireSound, emergencySound;

const activeToasts = new Map();


// Safe global (won't throw if already defined somewhere else)
window.FF_ACCOUNTS_BASE = window.FF_ACCOUNTS_BASE
  || 'TagumCityCentralFireStation/FireFighter/AllFireFighterAccount';



/* =========================
 * Helpers: Priming
 * ========================= */
// Prime the last child key so the first live event won't toast old data
async function primeLastKey(ref, storageKey) {
  try {
    const snap = await ref.limitToLast(1).once('value');
    snap.forEach(child => { localStorage.setItem(storageKey, child.key); });
  } catch (e) { console.warn('primeLastKey failed:', storageKey, e); }
}

// For replies, prime per-incident so opening the page doesn’t show the latest old reply
async function primeLastReplyKey(messagesRef, storageKey) {
  try {
    const snap = await messagesRef.limitToLast(1).once('value');
    snap.forEach(child => { localStorage.setItem(storageKey, child.key); });
  } catch (e) { console.warn('primeLastReplyKey failed:', storageKey, e); }
}

/* =========================
 * Toasts
 * ========================= */
function showQueuedToast({ id, type, reporterName = 'Unknown', message = '', sound = null }) {
  if (!id || activeToasts.has(id)) return;

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
    if (typeof openMessageModal === 'function') {
      // Map to UI route keys you already use
      const guessedType =
        detectedType === 'otherEmergency' ? 'otherEmergency' :
        detectedType === 'emsReports'     ? 'emsReports'     :
                                            'fireReports';
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


// === ADD: open FF chat modal for admin-messages toasts ===
if (type === 'ffchat') {
  const accountKey = String(uniqueId).split('|')[1]; // id shape: "ffchat|{accountKey}|{messageId}"
  if (accountKey && typeof openFFChatMessageModal === 'function') {
    openFFChatMessageModal(accountKey);
  }
  return;
}


}

// === ADD: preview text for mutually exclusive payloads ===
function ffPreviewFromMessage(m){
  if (m?.imageBase64) return 'Sent a photo';
  if (m?.audioBase64) return 'Sent a voice message';
  if (m?.text)        return (m.text.length > 60 ? m.text.slice(0,57) + '…' : m.text);
  return 'New message';
}

// === ADD: prime per-account so we don't toast historical on first load ===
async function primeLastFFAdminKey(ref, storageKey){
  try {
    const snap = await ref.orderByChild('timestamp').limitToLast(1).once('value');
    snap.forEach(c => localStorage.setItem(storageKey, c.key));
  } catch(e){ console.warn('primeLastFFAdminKey failed', storageKey, e); }
}


// === ADD: watch all FireFighter AdminMessages and toast when firefighter replies ===
async function attachFFAdminMessages(db){
  const baseRef = db.ref(FF_ACCOUNTS_BASE);
  const all = await baseRef.once('value');
  if (!all.exists()) return;

  all.forEach(accSnap => {
    const accountKey = accSnap.key;
    const acc = accSnap.val() || {};
    const displayName = acc.name || accountKey;

    const msgsRef = baseRef.child(accountKey).child('AdminMessages');
    const seenKey = `ff_last_adminmsg_${accountKey}`;

    // prime to avoid the latest historical message
    primeLastFFAdminKey(msgsRef, seenKey).then(() => {
      msgsRef.orderByChild('timestamp').limitToLast(1).on('child_added', snap => {
        const id = snap.key;
        const m  = snap.val() || {};

        // skip the primed historical item
        if (localStorage.getItem(seenKey) === id) {
          localStorage.setItem(seenKey, id);
          return;
        }

        // only notify when the FIREFIGHTER sent it (ignore admin echoes)
        const isFromAdmin = String(m.sender || '').toLowerCase() === 'admin';
        if (isFromAdmin) {
          localStorage.setItem(seenKey, id);
          return;
        }

        // show toast (click opens that account's chat modal)
        showQueuedToast({
          id: `ffchat|${accountKey}|${id}`,
          type: 'ffchat',
          reporterName: displayName,
          message: ffPreviewFromMessage(m),
          sound: null
        });


        localStorage.setItem(seenKey, id);
      });
    });
  });
}



/* =========================
 * Main
 * ========================= */
document.addEventListener('DOMContentLoaded', async function () {


  const nameEl = document.getElementById('station-name');
  if (nameEl) nameEl.textContent = 'Loading…'; // temporary placeholder

  // Resolve the station key from meta or global
  const metaStation = document.querySelector('meta[name="station-key"]');
  const stationKey  = (window.__STATION_KEY__ || metaStation?.content || 'TagumCityCentralFireStation').trim();

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

  // Guard Firebase init
  if (!firebase.apps.length) {
    firebase.initializeApp(firebaseConfig);
  }
  const db = firebase.database();


  // --- NEW: Fetch profile to get the display name (and optional logo) ---
  try {
    const profileSnap = await db.ref(`${stationKey}/Profile`).once('value');
    const profile = profileSnap.val() || {};

    // Try common fields: displayName -> name -> title; else prettify stationKey
    const prettyFromKey = stationKey
      .replace(/([A-Z])/g, ' $1')      // split CamelCase
      .replace(/^\s+/, '')             // trim left
      .replace(/\s+/g, ' ')            // collapse spaces
      .trim();

    const displayName =
      (typeof profile.displayName === 'string' && profile.displayName.trim()) ||
      (typeof profile.name === 'string' && profile.name.trim()) ||
      (typeof profile.title === 'string' && profile.title.trim()) ||
      prettyFromKey;

    if (nameEl) nameEl.textContent = displayName;

    // Optional: if you store a logo URL in the profile, use it
    if (profile.logoUrl) {
      const logoEl = document.querySelector('.sidebar .logo');
      if (logoEl && logoEl.tagName === 'IMG') {
        logoEl.src = profile.logoUrl;
        logoEl.alt = displayName + ' Logo';
      }
    }
  } catch (e) {
    console.warn('Failed to read station profile:', e);
    if (nameEl) {
      const fallback = stationKey.replace(/([A-Z])/g, ' $1').trim();
      nameEl.textContent = fallback || 'Fire Station';
    }
  }

  // Singleton guard so listeners aren’t registered multiple times
  if (window.__flareRealtimeStarted) return;
  window.__flareRealtimeStarted = true;

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

  // =========================
  // Single-station, single-root nodes (ONLY Tagum City Central)
  // =========================
  const BASE_ROOT = 'TagumCityCentralFireStation/AllReport';
  const NODES = {
    base: BASE_ROOT,
    fire:      `${BASE_ROOT}/FireReport`,
    emergency: `${BASE_ROOT}/OtherEmergencyReport`,
    ems:       `${BASE_ROOT}/EmergencyMedicalServicesReport`,
    response:  `${BASE_ROOT}/ResponseMessage`
  };

  // Prime before attaching listeners (prevents showing last historical)
  await primeLastKey(db.ref(NODES.fire),      `last_fire_id_${NODES.base}`);
  await primeLastKey(db.ref(NODES.emergency), `last_emergency_id_${NODES.base}`);
  await primeLastKey(db.ref(NODES.ems),       `last_ems_id_${NODES.base}`);

  // Attach listeners for the single node bundle
  attachListenersFor(NODES);


// === ADD: start AdminMessages toasts (firefighter→admin chat) ===
attachFFAdminMessages(db);


  // Expose responseMessageRef if needed elsewhere
  responseMessageRef = db.ref(NODES.response);

  /* =========================
   * Listener bundle (Tagum City Central only)
   * ========================= */
  function attachListenersFor(n) {
    // ----- FIRE -----
    db.ref(n.fire).limitToLast(1).on('child_added', snap => {
      const v = snap.val(); const id = snap.key;
      if (!v || !id) return;

      const seenKey = `last_fire_id_${n.base}`;
      if (localStorage.getItem(seenKey) === id) return;
      localStorage.setItem(seenKey, id);

      v.id = id;
      v.timestamp = Number.isFinite(v.timestamp) ? v.timestamp : Date.now();
      v.createdAt = v.createdAt ?? v.timestamp;

      showQueuedToast({
        id: `${n.base}|${id}`,
        type: 'fireReports',
        reporterName: v.name || 'Unknown',
        message: "Fire Report! A new incident has been added.",
        sound: fireSound
      });

      try {
        if (typeof insertNewReportRow === 'function') insertNewReportRow(v, 'fireReports');
        if (typeof renderAllReports === 'function')   renderAllReports();
      } catch(e){ console.warn('[fire optional update]', e); }
    });

    // ----- OTHER EMERGENCY -----
    db.ref(n.emergency).limitToLast(1).on('child_added', snap => {
      const v = snap.val(); const id = snap.key;
      if (!v || !id) return;

      const seenKey = `last_emergency_id_${n.base}`;
      if (localStorage.getItem(seenKey) === id) return;
      localStorage.setItem(seenKey, id);

      v.id = id;
      v.timestamp = Number.isFinite(v.timestamp) ? v.timestamp : Date.now();
      v.createdAt = v.createdAt ?? v.timestamp;

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

    // ----- EMS -----
    db.ref(n.ems).limitToLast(1).on('child_added', snap => {
      const v = snap.val(); const id = snap.key;
      if (!v || !id) return;

      const seenKey = `last_ems_id_${n.base}`;
      if (localStorage.getItem(seenKey) === id) return;
      localStorage.setItem(seenKey, id);

      v.id = id;
      v.timestamp = Number.isFinite(v.timestamp) ? v.timestamp : Date.now();
      v.createdAt = v.createdAt ?? v.timestamp;

      showQueuedToast({
        id: `${n.base}|${id}`,
        type: 'emsReports',
        reporterName: v.name || 'Unknown',
        message: "EMS Report! A new incident has been added.",
        sound: emergencySound
      });

      try {
        if (typeof insertNewEmsRow === 'function') insertNewEmsRow(v);
        if (typeof renderAllReports === 'function') renderAllReports();
      } catch(e){ console.warn('[ems optional update]', e); }
    });

    // ----- Replies under FIRE -----
    db.ref(n.fire).on('child_added', async reportSnap => {
      const incidentId = reportSnap.key;
      const reportData = reportSnap.val();
      const msgsRef  = reportSnap.ref.child('messages');
      const seenKey  = `last_reply_id_${n.base}_${incidentId}`;

      await primeLastReplyKey(msgsRef, seenKey);

      msgsRef.limitToLast(1).on('child_added', msgSnap => {
        const msg = msgSnap.val();
        if (!msg || (String(msg.type||'').toLowerCase() !== 'reply')) return;
        if (localStorage.getItem(seenKey) === msgSnap.key) return;
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

    // ----- Replies under OTHER EMERGENCY -----
    db.ref(n.emergency).on('child_added', async reportSnap => {
      const incidentId = reportSnap.key;
      const reportData = reportSnap.val();
      const msgsRef = reportSnap.ref.child('messages');
      const seenKey = `last_reply_id_${n.base}_${incidentId}`;

      await primeLastReplyKey(msgsRef, seenKey);

      msgsRef.limitToLast(1).on('child_added', msgSnap => {
        const msg = msgSnap.val();
        if (!msg || (String(msg.type||'').toLowerCase() !== 'reply')) return;
        if (localStorage.getItem(seenKey) === msgSnap.key) return;
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

    // ----- Replies under EMS -----
    db.ref(n.ems).on('child_added', async reportSnap => {
      const incidentId = reportSnap.key;
      const reportData = reportSnap.val();
      const msgsRef = reportSnap.ref.child('messages');
      const seenKey = `last_reply_id_${n.base}_${incidentId}`;

      await primeLastReplyKey(msgsRef, seenKey);

      msgsRef.limitToLast(1).on('child_added', msgSnap => {
        const msg = msgSnap.val();
        if (!msg || (String(msg.type||'').toLowerCase() !== 'reply')) return;
        if (localStorage.getItem(seenKey) === msgSnap.key) return;
        localStorage.setItem(seenKey, msgSnap.key);

        showQueuedToast({
          id: `${n.base}|${incidentId}|msg3`,
          type: 'message',
          reporterName: reportData?.name || msg.reporterName || 'Unknown',
          message: "New Message Received. Tap to view the chat.",
          sound: null
        });
      });
    });

    // ----- Response status changes (optional) -----
    db.ref(n.response).on('child_changed', snapshot => {
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
  // Auto-reload after 5 minutes of inactivity (unchanged)
  let reloadTimer;
  function resetReloadTimer() {
    clearTimeout(reloadTimer);
    reloadTimer = setTimeout(() => { location.reload(); }, 300000);
  }
  window.onload = resetReloadTimer;
  ['click', 'mousemove', 'keydown', 'scroll', 'touchstart'].forEach(evt => {
    document.addEventListener(evt, resetReloadTimer, false);
  });
</script>

</body>
</html>
