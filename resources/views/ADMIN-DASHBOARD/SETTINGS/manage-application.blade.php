<div class="bg-white p-6 sm:p-8 rounded-lg shadow-xl details-panel" style="height: 650px;">
  <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">Manage Report Application</h1>

  <!-- Report Type Dropdown -->
  <div class="mb-4">
    <label for="report-type" class="text-lg font-semibold text-gray-700">Select Report Type</label>
    <select id="report-type" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
      <option value="fire">Fire Report</option>
      <option value="other">Other Emergency Report</option>
      <option value="ems">Emergency Medical Services Report</option>
    </select>
  </div>

  <!-- Add New Option Section -->
  <div class="mb-6">
    <label for="new-option" class="text-lg font-semibold text-gray-700">Add New Option</label>
    <div class="flex items-center">
      <input id="new-option" type="text" class="mt-2 w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter new option" />
      <button id="add-option-btn" class="mt-2 ml-4 w-1/4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Add Option</button>
    </div>
  </div>

  <!-- Table to Display Report Options -->
  <div class="mt-6">
    <table class="min-w-full table-auto">
      <thead class="sticky top-0 bg-gray-100">
        <tr class="bg-gray-100">
          <th class="px-4 py-2 text-left text-gray-600">#</th>
          <th class="px-4 py-2 text-left text-gray-600">Option</th>
          <th class="px-4 py-2 text-left text-gray-600">Action</th> <!-- Action Column -->
        </tr>
      </thead>
      <tbody id="report-options-body">
        <!-- Data will be dynamically populated here -->
      </tbody>
    </table>
  </div>

</div>

<!-- Firebase SDKs -->
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Firebase configuration
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
    if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);

    const db = firebase.database();
    const reportTypeSelect = document.getElementById('report-type');
    const reportOptionsBody = document.getElementById('report-options-body');
    const newOptionInput = document.getElementById('new-option');
    const addOptionBtn = document.getElementById('add-option-btn');

    const reportRefs = {
      fire: db.ref('TagumCityCentralFireStation/ManageApplication/FireReport/Option'),
      other: db.ref('TagumCityCentralFireStation/ManageApplication/OtherEmergencyReport/Option'),
      ems: db.ref('TagumCityCentralFireStation/ManageApplication/EmergencyMedicalServicesReport/Option')
    };

    // Fetch and display options based on the selected report type
    function loadOptions(reportType) {
      reportRefs[reportType].once('value').then(snapshot => {
        const options = snapshot.val();
        const optionsArray = options ? options.split(',') : []; // Split the comma-separated string into an array
        reportOptionsBody.innerHTML = '';  // Clear existing data

        optionsArray.forEach((option, index) => {
          const row = document.createElement('tr');
          row.classList.add('bg-white', 'border-b', 'hover:bg-gray-50');
          row.innerHTML = `
            <td class="px-4 py-2 text-sm font-medium text-gray-900">${index + 1}</td>
            <td class="px-4 py-2 text-sm text-gray-500">${option.trim()}</td>
            <td class="px-4 py-2 text-sm text-gray-500">
              <!-- Edit Button with Edit Icon -->
              <button class="text-blue-600 hover:text-blue-800" onclick="editOption(${index}, '${option.trim()}')">
                <img src="{{ asset('images/edit.png') }}" alt="Edit" class="w-6 h-6 inline-block">
              </button>
              <!-- Delete Button with Delete Icon -->
              <button class="text-red-600 hover:text-red-800 ml-2" onclick="deleteOption(${index})">
                <img src="{{ asset('images/delete.png') }}" alt="Delete" class="w-6 h-6 inline-block">
              </button>
            </td>
          `;
          reportOptionsBody.appendChild(row);
        });
      });
    }

    // Load initial data for Fire report
    loadOptions('fire');

    // Change options when the report type is changed
    reportTypeSelect.addEventListener('change', function () {
      loadOptions(this.value);
    });

    // Edit option functionality
    window.editOption = function(index, option) {
      const newOption = prompt("Edit Option", option);
      if (newOption) {
        const selectedReportType = reportTypeSelect.value;
        const options = Array.from(reportOptionsBody.querySelectorAll('td:nth-child(2)')).map(td => td.textContent.trim());
        options[index] = newOption; // Update the specific option

        reportRefs[selectedReportType].set(options.join(','));
        loadOptions(selectedReportType);  // Refresh the table with updated options
      }
    };

    // Delete option functionality
    window.deleteOption = function(index) {
      if (confirm("Are you sure you want to delete this option?")) {
        const selectedReportType = reportTypeSelect.value;
        const options = Array.from(reportOptionsBody.querySelectorAll('td:nth-child(2)')).map(td => td.textContent.trim());
        options.splice(index, 1); // Remove the selected option

        reportRefs[selectedReportType].set(options.join(','));
        loadOptions(selectedReportType);  // Refresh the table with updated options
      }
    };

    // Add new option functionality
    addOptionBtn.addEventListener('click', function () {
      const newOption = newOptionInput.value.trim();
      if (newOption) {
        const selectedReportType = reportTypeSelect.value;

        // Fetch the current options and add the new option
        reportRefs[selectedReportType].once('value').then(snapshot => {
          const currentOptions = snapshot.val();
          const optionsArray = currentOptions ? currentOptions.split(',') : [];
          optionsArray.push(newOption); // Add the new option to the array

          reportRefs[selectedReportType].set(optionsArray.join(',')); // Update the Firebase with new options
          loadOptions(selectedReportType);  // Refresh the table with updated options
          newOptionInput.value = ''; // Clear the input field
        });
      } else {
        alert("Please enter a valid option.");
      }
    });

    // Save changes to Firebase (this part can be customized based on how you want to save the data)
    saveBtn.addEventListener('click', function () {
      const selectedReportType = reportTypeSelect.value;
      const options = Array.from(reportOptionsBody.querySelectorAll('td:nth-child(2)')).map(td => td.textContent.trim());

      if (options.length > 0) {
        // Join options as a comma-separated string
        reportRefs[selectedReportType].set(options.join(','));
        alert('Options saved successfully!');
      }
    });
  });
</script>
