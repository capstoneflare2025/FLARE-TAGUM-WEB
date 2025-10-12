@extends('ADMIN-DASHBOARD.app')

@section('title', 'Fire Fighters')

@section('content')
<div class="container mx-auto p-6">

  <h1 class="text-2xl font-bold text-gray-800 mb-6">Fire Fighters</h1>

  <div class="bg-white p-6 shadow rounded-lg">
    <div class="flex items-center justify-between mb-4">
      <button id="addFirefighterBtn" class="px-4 py-2 bg-green-500 text-white rounded-lg">
        Add New Fire Fighter
      </button>
      <div class="text-sm text-gray-600">
        <span class="font-semibold">Rows:</span>
        <span id="rowCount">0</span>
      </div>
    </div>

    <div class="w-full overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead>
          <tr class="bg-gray-100 align-top">
            <th class="px-4 py-2 text-left text-gray-600">Fire Fighter ID</th>
            <th class="px-4 py-2 text-left text-gray-600">Name</th>
            <th class="px-4 py-2 text-left text-gray-600">Email</th>
            <th class="px-4 py-2 text-left text-gray-600">Contact</th>
            <th class="px-4 py-2 text-left text-gray-600">Birthday</th>
            <th class="px-4 py-2 text-left text-gray-600">
              <div class="flex items-center gap-2">
                <span>Station</span>
                <!-- FILTER lives IN the column header -->
                <select id="stationFilter" class="px-2 py-1 border rounded text-sm text-gray-700">
                  <option value="ALL">All Stations</option>
                  <option value="CanocotanFireFighter">Canocotan Fire Station</option>
                  <option value="MabiniFireFighter">Mabini Fire Station</option>
                  <option value="LaFilipinaFireFighter">La Filipina Fire Station</option>
                </select>
              </div>
            </th>
            <th class="px-4 py-2 text-left text-gray-600">Actions</th>
          </tr>
        </thead>
        <tbody id="firefighterTableBody"></tbody>
      </table>
    </div>
  </div>

<!-- View modal (updated to match Add/Edit styling) -->
<div id="viewModal" class="hidden fixed inset-0 bg-gray-500/50 flex items-center justify-center z-50">
  <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 sm:w-1/2">
    <!-- Title -->
    <div class="flex items-start justify-between mb-4">
      <div>
        <h3 class="text-xl font-semibold text-gray-800">Fire Fighter Details</h3>
        <!-- Optional subtext: station name mirrors the Station field -->
        <p id="viewStationBadge" class="text-xs text-gray-500 mt-1"></p>
      </div>

    </div>

    <!-- Body -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div class="space-y-1">
        <div class="text-xs font-semibold text-gray-500 tracking-wide">ID</div>
        <div id="viewFirefighterId" class="text-gray-800"></div>
      </div>

      <div class="space-y-1">
        <div class="text-xs font-semibold text-gray-500 tracking-wide">Name</div>
        <div id="viewFirefighterName" class="text-gray-800"></div>
      </div>

      <div class="space-y-1">
        <div class="text-xs font-semibold text-gray-500 tracking-wide">Email</div>
        <div id="viewFirefighterEmail" class="text-gray-800 break-all"></div>
      </div>

      <div class="space-y-1">
        <div class="text-xs font-semibold text-gray-500 tracking-wide">Contact</div>
        <div id="viewFirefighterContact" class="text-gray-800"></div>
      </div>

      <div class="space-y-1">
        <div class="text-xs font-semibold text-gray-500 tracking-wide">Birthday</div>
        <div id="viewFirefighterBirthday" class="text-gray-800"></div>
      </div>

      <div class="space-y-1">
        <div class="text-xs font-semibold text-gray-500 tracking-wide">Station</div>
        <div id="viewFirefighterStation" class="text-gray-800"></div>
      </div>
    </div>

    <!-- Footer -->
    <div class="mt-6 flex justify-end">
      <button id="closeViewModalBtn"
              class="px-5 py-2 rounded-lg bg-gray-700 text-white hover:bg-gray-800">
        Close
      </button>
    </div>
  </div>
</div>


  <!-- Add/Edit modal -->
  <div id="addModal" class="hidden fixed inset-0 bg-gray-500/50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 sm:w-1/2">
      <h3 class="text-xl font-semibold text-gray-700 mb-4" id="modalTitle">Add New Fire Fighter</h3>
      <form method="POST" id="addFirefighterForm">
        @csrf
        <input type="hidden" id="firefighterRecordKey" />
        <input type="hidden" id="firefighterBucketKey" />

        <div class="mb-4">
          <label class="block text-gray-700 font-semibold">Station:</label>
          <select id="addStationSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
            <option value="CanocotanFireFighter" data-name="Canocotan Fire Station">Canocotan Fire Station</option>
            <option value="MabiniFireFighter" data-name="Mabini Fire Station">Mabini Fire Station</option>
            <option value="LaFilipinaFireFighter" data-name="La Filipina Fire Station">La Filipina Fire Station</option>
          </select>
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 font-semibold">Name:</label>
          <input type="text" id="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold">Email:</label>
          <input type="email" id="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold">Contact:</label>
          <input type="text" id="contact" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold">Birthday:</label>
          <input type="date" id="birthday" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" />
        </div>

        <!-- Display-only: ensure we will save the human-readable name too -->
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold">fireStationName (saved):</label>
          <input type="text" id="fireStationNamePreview" class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50" readonly />
        </div>

        <div class="mt-4">
          <button type="submit" id="submitBtn" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Save</button>
        </div>
      </form>
      <button id="closeAddModalBtn" class="mt-4 px-4 py-2 bg-gray-500 text-white rounded-lg">Close</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>

<script>
/* ------------ Firebase init ------------ */
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

/* ------------ Paths / Buckets ------------ */
const ROOT = "TagumCityCentralFireStation/FireFighter";

const BUCKETS = {
  CanocotanFireFighter:   { name: "Canocotan Fire Station",   key: "CanocotanFireFighter",   prefix: "CANOCOTAN"   },
  MabiniFireFighter:      { name: "Mabini Fire Station",      key: "MabiniFireFighter",      prefix: "MABINI"      },
  LaFilipinaFireFighter:  { name: "La Filipina Fire Station", key: "LaFilipinaFireFighter",  prefix: "LAFILIPINA"  },
};

const bucketKeys = Object.keys(BUCKETS);

/* ------------ Elements ------------ */
const stationFilter        = document.getElementById('stationFilter');
const rowCount             = document.getElementById('rowCount');
const tbody                = document.getElementById('firefighterTableBody');

const addBtn               = document.getElementById('addFirefighterBtn');
const addModal             = document.getElementById('addModal');
const addForm              = document.getElementById('addFirefighterForm');
const submitBtn            = document.getElementById('submitBtn');

const recordKeyInput       = document.getElementById('firefighterRecordKey');
const recordBucketInput    = document.getElementById('firefighterBucketKey');
const addStationSelect     = document.getElementById('addStationSelect');
const fireStationNamePrev  = document.getElementById('fireStationNamePreview');

const closeAddModalBtn     = document.getElementById('closeAddModalBtn');

const viewModal            = document.getElementById('viewModal');
const closeViewModalBtn    = document.getElementById('closeViewModalBtn');

/* ------------ Local state ------------ */
let bucketListeners = {};
let bucketData      = {};  // { bucketKey: { autoKey: firefighterObj } }

/* ------------ Helpers ------------ */
function makeReadableId(prefix) {
  const y = new Date().getFullYear();
  const p = firebase.database().ref('tmp').push();
  const k = p.key || Math.random().toString(36).slice(2);
  p.remove();
  return `${prefix}-${y}-${k.slice(-5).toUpperCase()}`;
}

function currentReadableNameFromSelect(selectEl) {
  const opt = selectEl.options[selectEl.selectedIndex];
  return opt?.dataset?.name || opt?.text || '';
}

function mergedRows() {
  // flatten all buckets into one array with bucket info
  const rows = [];
  for (const bucketKey of bucketKeys) {
    const map = bucketData[bucketKey] || {};
    Object.entries(map).forEach(([autoKey, ff]) => {
      rows.push({
        _autoKey: autoKey,
        _bucket: bucketKey,
        ...ff
      });
    });
  }
  return rows;
}

function applyFilterAndRender() {
  const filterKey = stationFilter.value; // "ALL" or a bucket key
  let rows = mergedRows();

  if (filterKey !== 'ALL') {
    rows = rows.filter(r => r.fireStationKey === filterKey);
  }

  // Sort by name
  rows.sort((a, b) => (a.name || '').localeCompare(b.name || ''));

  // Render
  tbody.innerHTML = '';
  for (const row of rows) {
    const tr = document.createElement('tr');
    tr.className = 'border-b';
    tr.innerHTML = `
      <td class="px-4 py-2">${row.id || ''}</td>
      <td class="px-4 py-2 font-semibold">${row.name || ''}</td>
      <td class="px-4 py-2">${row.email || ''}</td>
      <td class="px-4 py-2">${row.contact || ''}</td>
      <td class="px-4 py-2">${row.birthday || ''}</td>
      <td class="px-4 py-2">${row.fireStationName || BUCKETS[row._bucket]?.name || ''}</td>
      <td class="px-4 py-2 space-x-2">
        <button class="viewBtn text-blue-500 hover:underline"
                data-bucket="${row._bucket}"
                data-key="${row._autoKey}">View</button>
        <button class="editBtn text-yellow-500 hover:underline"
                data-bucket="${row._bucket}"
                data-key="${row._autoKey}">Edit</button>
        <button class="deleteBtn text-red-500 hover:underline"
                data-bucket="${row._bucket}"
                data-key="${row._autoKey}">Delete</button>
      </td>
    `;
    tbody.appendChild(tr);
  }

  rowCount.textContent = rows.length.toString();
  bindRowButtons();
}

/* duplicates across all buckets by name+email */
function checkDuplicate(name, email) {
  const promises = bucketKeys.map(bk =>
    firebase.database().ref(`${ROOT}/${bk}`).once('value').then(s => {
      const v = s.val() || {};
      return Object.values(v).some(ff => ff.name === name && ff.email === email);
    })
  );
  return Promise.all(promises).then(list => list.some(Boolean));
}

/* ------------ Realtime listeners (per bucket) ------------ */
function attachBucket(bucketKey) {
  const ref = firebase.database().ref(`${ROOT}/${bucketKey}`);
  const cb = ref.on('value', snap => {
    bucketData[bucketKey] = snap.val() || {};
    applyFilterAndRender();
  });
  bucketListeners[bucketKey] = { ref, cb };
}

function detachAll() {
  for (const k in bucketListeners) {
    const { ref, cb } = bucketListeners[k];
    try { ref.off('value', cb); } catch(e){}
  }
  bucketListeners = {};
}

/* ------------ Row actions ------------ */
function bindRowButtons() {
  // View
  document.querySelectorAll('.viewBtn').forEach(btn => {
    btn.onclick = () => {
      const bucket = btn.dataset.bucket;
      const key    = btn.dataset.key;
      firebase.database().ref(`${ROOT}/${bucket}/${key}`).once('value').then(s => {
        const d = s.val() || {};
        document.getElementById('viewFirefighterId').innerText       = d.id || '';
        document.getElementById('viewFirefighterName').innerText     = d.name || '';
        document.getElementById('viewFirefighterEmail').innerText    = d.email || '';
        document.getElementById('viewFirefighterContact').innerText  = d.contact || '';
        document.getElementById('viewFirefighterBirthday').innerText = d.birthday || '';
        document.getElementById('viewFirefighterStation').innerText  = d.fireStationName || BUCKETS[bucket]?.name || '';
        viewModal.classList.remove('hidden');
      });
    };
  });

  // Edit (keeps bucket fixed; you can add “move station” later if desired)
  document.querySelectorAll('.editBtn').forEach(btn => {
    btn.onclick = () => {
      const bucket = btn.dataset.bucket;
      const key    = btn.dataset.key;
      firebase.database().ref(`${ROOT}/${bucket}/${key}`).once('value').then(s => {
        const d = s.val() || {};
        document.getElementById('modalTitle').innerText = 'Edit Fire Fighter';
        submitBtn.innerText = 'Update Fire Fighter';

        addForm.reset();
        recordKeyInput.value    = key;
        recordBucketInput.value = bucket;

        // lock station selection (edit does not move between buckets)
        addStationSelect.value = bucket;
        fireStationNamePrev.value = d.fireStationName || BUCKETS[bucket].name;
        addStationSelect.disabled = true;

        document.getElementById('name').value     = d.name || '';
        document.getElementById('email').value    = d.email || '';
        document.getElementById('contact').value  = d.contact || '';
        document.getElementById('birthday').value = d.birthday || '';

        addModal.classList.remove('hidden');
      });
    };
  });

  // Delete
  document.querySelectorAll('.deleteBtn').forEach(btn => {
    btn.onclick = () => {
      const bucket = btn.dataset.bucket;
      const key    = btn.dataset.key;
      if (confirm('Delete this firefighter?')) {
        firebase.database().ref(`${ROOT}/${bucket}/${key}`).remove();
      }
    };
  });
}

/* ------------ Modal open/close ------------ */
addBtn.onclick = () => {
  document.getElementById('modalTitle').innerText = 'Add New Fire Fighter';
  submitBtn.innerText = 'Add Fire Fighter';
  addForm.reset();
  recordKeyInput.value    = '';
  recordBucketInput.value = '';

  // default station in add modal = first option
  addStationSelect.disabled = false;
  fireStationNamePrev.value = currentReadableNameFromSelect(addStationSelect);

  addModal.classList.remove('hidden');
};

addStationSelect.addEventListener('change', () => {
  fireStationNamePrev.value = currentReadableNameFromSelect(addStationSelect);
});

closeAddModalBtn.onclick  = () => addModal.classList.add('hidden');
closeViewModalBtn.onclick = () => viewModal.classList.add('hidden');

/* ------------ Save (Add/Update) ------------ */
addForm.onsubmit = (e) => {
  e.preventDefault();

  const editingKey   = recordKeyInput.value.trim();        // empty means ADD
  const editingBucket= recordBucketInput.value.trim();     // empty means ADD

  const name     = document.getElementById('name').value.trim();
  const email    = document.getElementById('email').value.trim();
  const contact  = document.getElementById('contact').value.trim();
  const birthday = document.getElementById('birthday').value;

  // For ADD
  const addBucketKey = addStationSelect.value;             // e.g., "MabiniFireFighter"
  const addBucket    = BUCKETS[addBucketKey];
  const fireStationName = fireStationNamePrev.value || addBucket?.name || '';

  if (!editingKey) {
    // ADD path
    checkDuplicate(name, email).then(dup => {
      if (dup) { alert('A firefighter with the same name and email already exists.'); return; }

      const ref = firebase.database().ref(`${ROOT}/${addBucketKey}`);
      const newKey = ref.push().key;
      const data = {
        id: makeReadableId(addBucket.prefix),
        name, email, contact, birthday,
        fireStationKey: addBucket.key,
        fireStationName: fireStationName
      };
      ref.child(newKey).set(data).then(() => addModal.classList.add('hidden'));
    });
    return;
  }

  // EDIT path (fixed bucket)
  const path = `${ROOT}/${editingBucket}/${editingKey}`;
  firebase.database().ref(path).once('value').then(s => {
    const old = s.val() || {};
    const changedIdentity = (old.name !== name) || (old.email !== email);

    const proceed = () => {
      const data = {
        id: old.id || null,
        name, email, contact, birthday,
        fireStationKey: old.fireStationKey || editingBucket,
        fireStationName: old.fireStationName || BUCKETS[editingBucket]?.name || ''
      };
      firebase.database().ref(path).set(data).then(() => addModal.classList.add('hidden'));
    };

    if (changedIdentity) {
      checkDuplicate(name, email).then(dup => {
        if (dup) { alert('A firefighter with the same name and email already exists.'); return; }
        proceed();
      });
    } else {
      proceed();
    }
  });
};

/* ------------ Filter change ------------ */
stationFilter.addEventListener('change', applyFilterAndRender);

/* ------------ Bootstrap ------------ */
window.addEventListener('DOMContentLoaded', () => {
  // Attach listeners for all buckets; render merges them
  bucketKeys.forEach(attachBucket);
});
</script>
@endsection
