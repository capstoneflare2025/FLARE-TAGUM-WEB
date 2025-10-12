<div class="bg-white p-6 sm:p-8 rounded-lg shadow-xl" style="height: 650px;">
  <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">Manage Users</h1>



  <!-- Table for Users -->
  <div class="overflow-x-auto relative">
    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
      <table class="min-w-full table-auto">
        <thead class="sticky top-0 bg-gray-100">
          <tr class="bg-gray-100">
            <th class="px-4 py-2 text-left text-gray-600">#</th>
            <th class="px-4 py-2 text-left text-gray-600">Name</th>
            <th class="px-4 py-2 text-left text-gray-600">Contact</th>
            <th class="px-4 py-2 text-left text-gray-600">Email</th>
            <th class="px-4 py-2 text-left text-gray-600">Action</th>
          </tr>
        </thead>
        <tbody id="user-table-body">
          <!-- Data will be dynamically populated here -->
        </tbody>
      </table>
      <div id="no-data-message" class="text-center text-gray-500 p-4 hidden">No users available in the database.</div>
    </div>
  </div>
</div>

<!-- Firebase SDKs -->
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
  const firebaseConfig = {
    apiKey: "AIzaSyCrjSyOI-qzCaJptEkWiRfEuaG28ugTmdE",
    authDomain: "capstone-flare-2025.firebaseapp.com",
    databaseURL: "https://capstone-flare-2025-default-rtdb.firebaseio.com",
    projectId: "capstone-flare-2025",
    storageBucket: "capstone-flare-2025.appspot.com",
    messagingSenderId: "685814202928",
    appId: "1:685814202928:web:9b484f04625e5870c9a3f5",
    measurementId: "G-QZ8P5VLHF2"
  };

  // Initialize Firebase
  if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);

  const db = firebase.database();
  const userTableBody = document.getElementById("user-table-body");
  const noDataMessage = document.getElementById("no-data-message");

  const userRef = db.ref('Users');

  // Listen for changes to the Users node in real-time
  userRef.on('value', (snapshot) => {
    const users = snapshot.val();
    userTableBody.innerHTML = '';  // Clear the table before repopulating
    if (users) {
      Object.keys(users).forEach((key, index) => {
        const userData = users[key];
        const row = document.createElement('tr');
        row.classList.add('bg-white', 'border-b', 'hover:bg-gray-50');
        row.innerHTML = `
          <td class="px-4 py-2 text-sm font-medium text-gray-900">${index + 1}</td>
          <td class="px-4 py-2 text-sm font-medium text-gray-900">${userData.name}</td>
          <td class="px-4 py-2 text-sm text-gray-500">${userData.contact}</td>
          <td class="px-4 py-2 text-sm text-gray-500">${userData.email}</td>
          <td class="px-4 py-2 text-sm text-gray-500">
            <!-- View Button with Custom Icon -->
            <button class="text-blue-600 hover:text-blue-800" onclick="openDetailsModal('${key}')">
            <img src="{{ asset('images/details.png') }}" alt="View" class="w-6 h-6 inline-block">
            </button>

            <!-- Delete Button with Custom Icon -->
            <button class="text-red-600 hover:text-red-800 ml-2" onclick="openDeleteModal('${key}')">
            <img src="{{ asset('images/delete.png') }}" alt="Delete" class="w-6 h-6 inline-block">
            </button>
        </td>
        `;
        userTableBody.appendChild(row);
      });
      noDataMessage.classList.add('hidden');
    } else {
      noDataMessage.classList.remove('hidden');
    }
  });
});

let originalData = {}; // To store the original data

// Open Details Modal and show the user data
function openDetailsModal(userId) {
  const userRef = firebase.database().ref('Users/' + userId);
  userRef.once('value', snapshot => {
    const userData = snapshot.val();

    // Store original data to revert on cancel
    originalData = { ...userData };

    // Display user data in the modal (non-editable initially)
    document.getElementById('details-name').textContent = userData.name;
    document.getElementById('details-email').textContent = userData.email;
    document.getElementById('details-contact').textContent = userData.contact;

    // Set profile image (if available)
    const profileImage = userData.profile ? userData.profile : 'default-profile-image-url';  // Default image URL
    document.getElementById('details-profile').src = `data:image/png;base64,${profileImage}`;

    // Show the modal
    document.getElementById('details-modal').classList.remove('hidden');

    // Set the initial button state
    setEditMode(false); // Start with non-editable fields
  });
}

// Toggle between Edit and Save button
function toggleEditMode() {
  const isEditing = document.getElementById('edit-button').textContent === 'Edit';

  if (isEditing) {
    // Change the "Edit" button to "Save"
    document.getElementById('edit-button').textContent = 'Save';

    // Make the fields editable
    document.getElementById('details-name').innerHTML = `<input type="text" id="edit-name" class="w-full px-3 py-2 border rounded-md" value="${document.getElementById('details-name').textContent}">`;
    document.getElementById('details-email').innerHTML = `<input type="email" id="edit-email" class="w-full px-3 py-2 border rounded-md" value="${document.getElementById('details-email').textContent}">`;
    document.getElementById('details-contact').innerHTML = `<input type="text" id="edit-contact" class="w-full px-3 py-2 border rounded-md" value="${document.getElementById('details-contact').textContent}">`;
  } else {
    // Save the changes
    saveChanges();
  }
}

// Save the changes made during editing
function saveChanges() {
  const updatedName = document.getElementById('edit-name').value;
  const updatedEmail = document.getElementById('edit-email').value;
  const updatedContact = document.getElementById('edit-contact').value;

  // Check if any fields have changed
  const isChanged = updatedName !== originalData.name || updatedEmail !== originalData.email || updatedContact !== originalData.contact;

  if (!isChanged) {
    // Show toast notification if nothing changed
    alert("No changes made.");

    // Revert the fields back to non-editable
    document.getElementById('details-name').textContent = originalData.name;
    document.getElementById('details-email').textContent = originalData.email;
    document.getElementById('details-contact').textContent = originalData.contact;

    // Change the button back to "Edit"
    document.getElementById('edit-button').textContent = 'Edit';
    return;
  }

  const userId = originalData.id; // Assuming userId is part of original data

  const userRef = firebase.database().ref('Users');

  // Update the user data
  userRef.child(userId).update({
    name: updatedName,
    email: updatedEmail,
    contact: updatedContact
  }).then(() => {
    // After saving the data, change back to "Edit" mode
    document.getElementById('edit-button').textContent = 'Edit';

    // Update the fields to non-editable
    document.getElementById('details-name').textContent = updatedName;
    document.getElementById('details-email').textContent = updatedEmail;
    document.getElementById('details-contact').textContent = updatedContact;
  }).catch((error) => {
    alert("Error updating user: " + error.message);
  });
}


// Close the Details Modal
function closeDetailsModal() {
  document.getElementById('details-modal').classList.add('hidden');
}

  // Open Delete Modal
  function openDeleteModal(userId) {
    document.getElementById('delete-modal').classList.remove('hidden');
    const userRef = firebase.database().ref('Users/' + userId);
    userRef.once('value', snapshot => {
      const userData = snapshot.val();
      document.getElementById('delete-user-name').textContent = userData.name;
      document.getElementById('delete-user-contact').textContent = userData.contact;
      document.getElementById('delete-user-email').textContent = userData.email;
    });

    const confirmDelete = document.getElementById('confirm-delete');
    confirmDelete.onclick = function() {
      document.getElementById('final-delete-modal').classList.remove('hidden');
      const finalUserRef = firebase.database().ref('Users/' + userId);
      finalUserRef.once('value', snapshot => {
        const finalUserData = snapshot.val();
        document.getElementById('final-delete-user-name').textContent = finalUserData.name;
        document.getElementById('final-delete-user-contact').textContent = finalUserData.contact;
        document.getElementById('final-delete-user-email').textContent = finalUserData.email;
      });

      const confirmFinalDelete = document.getElementById('final-confirm-delete');
      confirmFinalDelete.onclick = function() {
        deleteUserFromDB(userId);
      };
    };
  }

// Delete user from DB and store in DeleteUsers node
function deleteUserFromDB(userId) {
  const db = firebase.database();
  const userRef = db.ref('Users/' + userId);

  userRef.once('value', snapshot => {
    const userData = snapshot.val();

    // Save user data to DeleteUsers node
    const deleteUsersRef = db.ref('DeletedUsers/' + userId);
    deleteUsersRef.set(userData).then(() => {
      // Now remove the user from the Users node
      userRef.remove().then(() => {
        alert("User deleted successfully.");
      }).catch((error) => {
        alert("Error deleting user: " + error.message);
      });
    }).catch((error) => {
      alert("Error saving user to DeleteUsers: " + error.message);
    });
  });

  // Hide the modals
  document.getElementById('delete-modal').classList.add('hidden');
  document.getElementById('final-delete-modal').classList.add('hidden');
}


  function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
  }

  // Enable the delete button when the user types the confirmation text
  document.getElementById('delete-confirm-input').addEventListener('input', function () {
    const inputValue = this.value.trim().toLowerCase();
    const deleteBtn = document.getElementById('final-confirm-delete');
    if (inputValue === "i want to delete this user") {
      deleteBtn.disabled = false;
      deleteBtn.classList.remove('bg-red-300');
      deleteBtn.classList.add('bg-red-600');
    } else {
      deleteBtn.disabled = true;
      deleteBtn.classList.remove('bg-red-600');
      deleteBtn.classList.add('bg-red-300');
    }
  });
</script>

<!-- Details Modal -->
<div id="details-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white p-8 rounded-lg w-96 text-left shadow-xl transform transition-all duration-300 ease-in-out scale-95 hover:scale-100">
    <!-- Modal Title -->
    <h2 class=" flex justify-center text-3xl font-semibold mb-6 text-gray-800 border-b-2 border-gray-200 pb-4">User Details</h2>

    <!-- Profile Picture -->
    <div class="flex justify-center mb-6">
      <img id="details-profile" src="" alt="Profile" class="rounded-full w-32 h-32 object-cover border-4 border-blue-500 shadow-lg transform transition-transform duration-300 hover:scale-105">
    </div>

    <!-- User Details Section -->
    <div class="space-y-5 mb-6">
      <div>
        <strong class="text-xl text-gray-700">Name:</strong>
        <span id="details-name" class="text-gray-600 text-lg"></span>
      </div>
      <div>
        <strong class="text-xl text-gray-700">Email:</strong>
        <span id="details-email" class="text-gray-600 text-lg"></span>
      </div>
      <div>
        <strong class="text-xl text-gray-700">Contact:</strong>
        <span id="details-contact" class="text-gray-600 text-lg"></span>
      </div>
    </div>

    <!-- Action Buttons Section -->
    <div class="flex justify-between" style="margin-left: 130px; margin-top: 100px;">
      <!-- Close Button -->
      <button onclick="closeDetailsModal()" class="px-6 py-3 text-gray-800 border border-gray-300 rounded-lg text-lg font-medium transition-all duration-200 hover:bg-gray-100 focus:outline-none">Close</button>

      <!-- Edit/Save Button -->
      <button id="edit-button" class="px-6 py-3 bg-blue-600 text-white rounded-lg text-lg font-medium transition-all duration-200 hover:bg-blue-700 focus:outline-none" onclick="toggleEditMode()">Edit</button>
    </div>
  </div>
</div>




<!-- Modal for Delete Confirmation -->
<div id="delete-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white p-6 rounded-lg w-96 text-center">
    <!-- Display user info with borders -->
    <div class="text-left mb-4 border-b border-gray-300 pb-4">
      <p><strong>Name:</strong> <span id="delete-user-name"></span></p>
      <p><strong>Contact:</strong> <span id="delete-user-contact"></span></p>
      <p><strong>Email:</strong> <span id="delete-user-email"></span></p>
    </div>
    <!-- Action buttons -->
    <div class="flex justify-center space-x-4 mt-4">
      <button onclick="closeModal('delete-modal')" class="px-4 py-2 bg-gray-300 text-white rounded-md">Cancel</button>
      <button id="confirm-delete" class="px-4 py-2 bg-red-600 text-white rounded-md">Proceed to Final Delete</button>
    </div>
  </div>
</div>

<!-- Final Delete Confirmation Modal -->
<div id="final-delete-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white p-6 rounded-lg w-96 text-center">
    <p class="text-red-500 mt-4 mb-4 border-b border-gray-300 pb-4"><strong>This action can't be undone after deletion!</strong></p>
    <div class="text-left mb-4 border-b border-gray-300 pb-4">
      <p><strong>Name:</strong> <span id="final-delete-user-name"></span></p>
      <p><strong>Contact:</strong> <span id="final-delete-user-contact"></span></p>
      <p><strong>Email:</strong> <span id="final-delete-user-email"></span></p>
    </div>

    <div class="mt-4">
      <label class="block text-sm font-semibold mb-2">Type "I want to delete this user" to confirm:</label>
      <input type="text" id="delete-confirm-input" class="w-full px-3 py-2 border rounded-lg mb-4" placeholder="Type confirmation">
    </div>

    <div class="flex justify-center space-x-4 mt-4">
      <button onclick="closeModal('final-delete-modal')" class="px-4 py-2 bg-gray-300 text-white rounded-md">Cancel</button>
      <button id="final-confirm-delete" class="px-4 py-2 bg-red-300 text-white rounded-md" disabled>Delete Permanently</button>
    </div>
  </div>
</div>
<script>
  document.getElementById('delete-confirm-input').addEventListener('input', function () {
    const inputValue = this.value.trim().toLowerCase();
    const deleteBtn = document.getElementById('final-confirm-delete');
    if (inputValue === "i want to delete this user") {
      deleteBtn.disabled = false;
      deleteBtn.classList.remove('bg-red-300');
      deleteBtn.classList.add('bg-red-600');
    } else {
      deleteBtn.disabled = true;
      deleteBtn.classList.remove('bg-red-600');
      deleteBtn.classList.add('bg-red-300');
    }
  });
</script>

<!-- CSS Styling (Tailwind CSS) -->
<style>

#details-profile {
  object-fit: cover;
  border-radius: 50%;
  width: 150px;
  height: 150px;
}


  .table-auto th {
    background-color: #f7fafc;
  }

  .table-auto td, .table-auto th {
    border: 1px solid #e2e8f0;
  }

  .table-auto td {
    padding: 12px;
  }

  .text-blue-600 {
    color: #2563eb;
  }

  .text-blue-600:hover {
    color: #1d4ed8;
  }

  .text-red-600 {
    color: #e11d48;
  }

  .text-red-600:hover {
    color: #9b1d3e;
  }

  /* Scrollable table */
  .overflow-x-auto {
    max-height: 500px;
    overflow-y: auto;
  }
</style>
