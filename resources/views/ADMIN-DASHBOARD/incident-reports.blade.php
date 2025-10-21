    @extends('ADMIN-DASHBOARD.app')
    @section('page_name', 'incident-reports')
    @section('title', 'Incident Reports')

    @section('content')

    <!-- =========================================================
= Container & Page Header
========================================================= -->
<div class="container mx-auto p-6">

  <h1 class="text-2xl font-bold text-gray-800 mb-6">Incident Report Management</h1>

  <!-- =========================================================
  = Toast Notification
  ========================================================= -->
  <div id="toast" style="margin-right: 600px;"
       class="hidden fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg">
    <span id="toastMessage">Report Accepted</span>
    <button id="toastOkButton" class="ml-4 text-white underline">OK</button>
  </div>


  <!-- =========================================================
= Modal: Assign on Receive
========================================================= -->
<div id="assignModal"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden"
     style="z-index: 60;">

  <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg relative">
    <h3 class="text-lg font-semibold mb-4 text-gray-800">Assign to Fire Station</h3>

    <p class="text-sm text-gray-600 mb-3">
      Choose which station will receive this report.
    </p>

    <form id="assignForm" class="space-y-3">
      <label class="flex items-center gap-2">
        <input type="radio" name="station" value="CanocotanFireFighterAccount" class="accent-blue-600" required>
        <span>Tagum City Central Fire Station</span>
      </label>
      <label class="flex items-center gap-2">
        <input type="radio" name="station" value="LaFilipinaFireFighterAccount" class="accent-blue-600">
        <span>La Filipina Fire Sub-Station</span>
      </label>
      <label class="flex items-center gap-2">
        <input type="radio" name="station" value="MabiniFireFighterAccount" class="accent-blue-600">
        <span>Tagum City West Fire Sub-Station</span>
      </label>

      <div class="flex justify-end gap-2 pt-4">
        <button type="button" onclick="closeAssignModal()"
                class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-800">Cancel</button>
        <button type="submit"
                class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Assign & Receive</button>
      </div>
    </form>

    <button onclick="closeAssignModal()"
            class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
  </div>
</div>


  <!-- =========================================================
  = Report Type Selector (Vehicle removed; EMS added)
  ========================================================= -->
  <select id="incidentType" class="px-4 py-2 rounded bg-gray-200" onchange="toggleIncidentTables()">
    <option value="allReports" selected>All Reports</option>
    <option value="fireReports">Fire Reports</option>
    <option value="otherEmergency">Other Emergency</option>
    <option value="emsReports">Emergency Medical Services</option>
    <option value="smsReports">SMS Reports</option>
      <option value="fireFighterChatReports">Fire Fighter Chat</option>
  </select>

  <br><br>

  <!-- =========================================================
  = Section: All Reports
  ========================================================= -->
  <div id="allReportsSection" class="bg-white p-6 shadow rounded-lg">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">All Reports</h2>

    <div class="table-wrap max-h-96 overflow-y-auto">
      <table class="min-w-full table-auto table-min">
        <thead class="sticky top-0 z-10">
          <tr class="bg-gray-100">
            <th class="px-4 py-2 text-left text-gray-600 col-index">#</th>
            <th class="px-4 py-2 text-left text-gray-600">Type</th>
            <th class="px-4 py-2 text-left text-gray-600">Location</th>
            <th class="px-4 py-2 text-left text-gray-600 col-datetime">Date & Time</th>
            <th class="px-4 py-2 text-left text-gray-600">Status</th>
            <th class="px-4 py-2 text-left text-gray-600">Action</th>
          </tr>

          <!-- Filter Row -->
          <tr class="bg-gray-50 text-sm">
            <th></th>
            <th class="px-4 py-2">
              <input type="text" id="allTypeSearch" placeholder="Search Type..."
                     class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                     oninput="filterAllReportsTable()"/>
            </th>
            <th class="px-4 py-2">
              <input type="text" id="allLocationSearch" placeholder="Search Location..."
                     class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                     oninput="filterAllReportsTable()"/>
            </th>
            <th class="px-4 py-2">
              <select id="allDateTimeFilter"
                      class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                      onchange="handleAllDateTimeFilterChange()">
                <option value="all" selected>All</option>
                <option value="date">Date</option>
                <option value="time">Time</option>
              </select>
              <input type="date" id="allDateSearch"
                     class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                     onchange="filterAllReportsTable()"/>
              <input type="time" id="allTimeSearch"
                     class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                     onchange="filterAllReportsTable()"/>
            </th>
            <th class="px-4 py-2">
              <select id="allStatusFilter"
                      class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                      onchange="filterAllReportsTable()">
                <option value="">All</option>
                <option value="Pending">Pending</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Completed">Completed</option>
                <option value="Received">Received</option>
              </select>
            </th>
            <th></th>
          </tr>
        </thead>
        <tbody id="allReportsBody"></tbody>
      </table>
    </div>
  </div>

  <!-- =========================================================
  = Section: Fire Incident Reports (no alert level / houses affected)
  ========================================================= -->
  <div id="fireReportsSection" class="bg-white p-6 shadow rounded-lg hidden">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Fire Incident Reports</h2>
    <div class="table-wrap max-h-96 overflow-y-auto">
      <table class="min-w-full table-auto  table-min">
        <thead class="sticky top-0 z-10">
          <tr class="bg-gray-100">
            <th class="px-4 py-2 text-left text-gray-600 col-index">#</th>
            <th class="px-4 py-2 text-left text-gray-600">Type</th>
            <th class="px-4 py-2 text-left text-gray-600">Location</th>
            <th class="px-4 py-2 text-left text-gray-600 cursor-pointer col-datetime" onclick="focusFireDatePicker()">Date & Time</th>
            <th class="px-4 py-2 text-left text-gray-600">Status</th>
            <th class="px-4 py-2 text-left text-gray-600">Action</th>
          </tr>

          <tr class="bg-gray-50 text-sm">
            <th></th>
            <th class="px-4 py-2">
              <input type="text" id="fireTypeSearch" placeholder="Search Type..."
                     class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                     oninput="filterFireReportTable()"/>
            </th>
            <th class="px-4 py-2">
              <input type="text" id="fireLocationSearch" placeholder="Search Location..."
                     class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                     oninput="filterFireReportTable()"/>
            </th>
            <th class="px-4 py-2">
              <select id="fireDateTimeFilter"
                      class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                      onchange="handleDateTimeFilterChange()">
                <option value="all" selected>All</option>
                <option value="date">Date</option>
                <option value="time">Time</option>
              </select>
              <input type="date" id="fireDateSearch"
                     class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                     onchange="filterFireReportTable()"/>
              <input type="time" id="fireTimeSearch"
                     class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                     onchange="filterFireReportTable()"/>
            </th>
            <th class="px-4 py-2">
              <select id="fireStatusFilter"
                      class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                      onchange="filterFireReportTable()">
                <option value="">All</option>
                <option value="Pending">Pending</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Completed">Completed</option>
                <option value="Received">Received</option>
              </select>
            </th>
            <th></th>
          </tr>
        </thead>

        <tbody id="fireReportsBody">
          @foreach($fireReports as $index => $report)
            @php
              $statusRaw = $report['status'] ?? 'Pending';
              $status    = ucfirst(strtolower($statusRaw));
              $color     = $status === 'Ongoing'   ? 'red'
                         : ($status === 'Completed'? 'green'
                         : ($status === 'Pending'  ? 'orange'
                         : ($status === 'Received' ? 'blue' : 'yellow')));
            @endphp
            <tr id="reportRow{{ $report['id'] }}" class="border-b"
                data-report='@json($report)' data-type="fireReports">
              <td class="px-4 py-2">{{ $index + 1 }}</td>
              <td class="px-4 py-2">{{ $report['type'] ?? 'N/A' }}</td>
              <td class="px-4 py-2 break-anywhere">{{ $report['exactLocation'] ?? 'N/A' }}</td>
              <td class="px-4 py-2">{{ $report['date'] ?? 'N/A' }} {{ $report['reportTime'] ?? 'N/A' }}</td>
              <td class="px-4 py-2 status text-{{ $color }}-500">{{ $status }}</td>
              <td class="px-4 py-2 space-x-2 flex items-center">
                <a href="javascript:void(0);"
                class="msg-btn"
                data-key="fireReports|{{ $report['id'] }}"
                onclick="openMessageModal('{{ $report['id'] }}', 'fireReports')">
                <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                <span class="msg-badge hidden">0</span>
                </a>

                @if(isset($report['latitude'], $report['longitude']) && $report['latitude'] !== null && $report['longitude'] !== null)
                  <a href="javascript:void(0);" onclick="openLocationModal({{ $report['latitude'] }}, {{ $report['longitude'] }})">
                    <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
                  </a>
                @endif
                <a href="javascript:void(0);" onclick="openDetailsModal('{{ $report['id'] }}', 'fireReports')">
                  <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- =========================================================
  = Spinner (Other Emergency Loading)
  ========================================================= -->
  <div id="spinner" class="hidden flex justify-center items-center mt-6">
    <div class="spinner-border animate-spin inline-block w-12 h-12 border-4 border-solid border-gray-200 border-t-gray-600 rounded-full"></div>
  </div>

  <!-- =========================================================
  = Section: Other Emergency Incidents
  ========================================================= -->
  <div id="otherEmergencySection" class="bg-white p-6 shadow rounded-lg mt-6 hidden">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Other Emergency Incidents</h2>
    <div class="table-wrap max-h-96 overflow-y-auto">
      <table class="min-w-full table-auto  table-min">
        <thead class="sticky top-0 z-10">
          <tr class="bg-gray-100">
            <th class="px-4 py-2 text-left text-gray-600 col-index">#</th>
            <th class="px-4 py-2 text-left text-gray-600">Location</th>
            <th class="px-4 py-2 text-left text-gray-600">Emergency Type</th>
            <th class="px-4 py-2 text-left text-gray-600 cursor-pointer col-datetime" onclick="focusOtherDatePicker()">Date & Time</th>
            <th class="px-4 py-2 text-left text-gray-600">Status</th>
            <th class="px-4 py-2 text-left text-gray-600">Action</th>
          </tr>

          <tr class="bg-gray-50 text-sm">
            <th></th>
            <th class="px-4 py-2">
              <input type="text" id="otherLocationSearch" placeholder="Search Location..."
                     class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                     oninput="filterOtherEmergencyTable()"/>
            </th>
            <th class="px-4 py-2">
              <select id="emergencyTypeFilter"
                      class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                      onchange="filterOtherEmergencyTable()">
                <option value="">All</option>
                <option value="Gas Leak">Gas Leak</option>
                <option value="Flooding">Flooding</option>
                <option value="Fallen Tree">Fallen Tree</option>
                <option value="Building Collapse">Building Collapse</option>
              </select>
            </th>
            <th class="px-4 py-2">
              <select id="otherDateTimeFilter"
                      class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                      onchange="handleOtherDateTimeFilterChange()">
                <option value="all" selected>All</option>
                <option value="date">Date</option>
                <option value="time">Time</option>
              </select>
              <input type="date" id="otherDateSearch"
                     class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                     onchange="filterOtherEmergencyTable()"/>
              <input type="time" id="otherTimeSearch"
                     class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                     onchange="filterOtherEmergencyTable()"/>
            </th>
            <th class="px-4 py-2">
              <select id="otherStatusFilter"
                      class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                      onchange="filterOtherEmergencyTable()">
                <option value="">All</option>
                <option value="Pending">Pending</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Completed">Completed</option>
                <option value="Received">Received</option>
              </select>
            </th>
            <th></th>
          </tr>
        </thead>
        <tbody id="otherEmergencyTableBody">
          @foreach($otherEmergencyReports as $index => $report)
            @php
              $statusRaw = $report['status'] ?? 'Pending';
              $status    = ucfirst(strtolower($statusRaw));
              $color     = $status === 'Ongoing'   ? 'red'
                         : ($status === 'Completed'? 'green'
                         : ($status === 'Pending'  ? 'orange'
                         : ($status === 'Received' ? 'blue' : 'yellow')));
            @endphp
            <tr id="reportRow{{ $report['id'] }}" class="border-b"
                data-report='@json($report)' data-type="otherEmergency">
              <td class="px-4 py-2">{{ $index + 1 }}</td>
              <td class="px-4 py-2 break-anywhere">{{ $report['exactLocation'] ?? 'N/A' }}</td>
              <td class="px-4 py-2">{{ $report['emergencyType'] ?? 'N/A' }}</td>
              <td class="px-4 py-2">{{ $report['date'] ?? 'N/A' }} {{ $report['reportTime'] ?? 'N/A' }}</td>
              <td class="px-4 py-2 status text-{{ $color }}-500">{{ $status }}</td>
              <td class="px-4 py-2 flex space-x-4 items-center">
                <a href="javascript:void(0);"
                class="msg-btn"
                data-key="otherEmergency|{{ $report['id'] }}"
                onclick="openMessageModal('{{ $report['id'] }}', 'otherEmergency')">
                <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                <span class="msg-badge hidden">0</span>
                </a>

                @if(isset($report['latitude'], $report['longitude']) && $report['latitude'] !== null && $report['longitude'] !== null)
                  <a href="javascript:void(0);" onclick="openLocationModal({{ $report['latitude'] }}, {{ $report['longitude'] }})">
                    <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
                  </a>
                @endif

                <a href="javascript:void(0);" onclick="openDetailsModal('{{ $report['id'] }}', 'otherEmergency')">
                  <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- =========================================================
  = Section: Emergency Medical Services (EMS)
  ========================================================= -->
  <div id="emsSection" class="bg-white p-6 shadow rounded-lg mt-6 hidden">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Emergency Medical Services</h2>
    <div class="table-wrap max-h-96 overflow-y-auto">
      <table class="min-w-full table-auto table-min">
        <thead class="sticky top-0 z-10">
          <tr class="bg-gray-100">
            <th class="px-4 py-2 text-left text-gray-600 col-index">#</th>
            <th class="px-4 py-2 text-left text-gray-600">Type</th>
            <th class="px-4 py-2 text-left text-gray-600">Location</th>
            <th class="px-4 py-2 text-left text-gray-600 cursor-pointer col-datetime" onclick="focusEmsDatePicker()">Date & Time</th>
            <th class="px-4 py-2 text-left text-gray-600">Status</th>
            <th class="px-4 py-2 text-left text-gray-600">Action</th>
          </tr>

          <tr class="bg-gray-50 text-sm">
            <th></th>
            <th class="px-4 py-2">
              <input type="text" id="emsTypeSearch" placeholder="Search Type..."
                     class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                     oninput="filterEmsTable()"/>
            </th>
            <th class="px-4 py-2">
              <input type="text" id="emsLocationSearch" placeholder="Search Location..."
                     class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                     oninput="filterEmsTable()"/>
            </th>
            <th class="px-4 py-2">
              <select id="emsDateTimeFilter"
                      class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                      onchange="handleEmsDateTimeFilterChange()">
                <option value="all" selected>All</option>
                <option value="date">Date</option>
                <option value="time">Time</option>
              </select>
              <input type="date" id="emsDateSearch"
                     class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                     onchange="filterEmsTable()"/>
              <input type="time" id="emsTimeSearch"
                     class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                     onchange="filterEmsTable()"/>
            </th>
            <th class="px-4 py-2">
              <select id="emsStatusFilter"
                      class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                      onchange="filterEmsTable()">
                <option value="">All</option>
                <option value="Pending">Pending</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Completed">Completed</option>
                <option value="Received">Received</option>
              </select>
            </th>
            <th></th>
          </tr>
        </thead>
        <tbody id="emsBody">
          @foreach(($emsReports ?? []) as $index => $report)
            @php
              $statusRaw = $report['status'] ?? 'Pending';
              $status    = ucfirst(strtolower($statusRaw));
              $color     = $status === 'Ongoing'   ? 'red'
                         : ($status === 'Completed'? 'green'
                         : ($status === 'Pending'  ? 'orange'
                         : ($status === 'Received' ? 'blue' : 'yellow')));
            @endphp
            <tr id="reportRow{{ $report['id'] }}" class="border-b"
                data-report='@json($report)' data-type="emsReports">
              <td class="px-4 py-2">{{ $index + 1 }}</td>
              <td class="px-4 py-2">{{ $report['type'] ?? 'N/A' }}</td>
              <td class="px-4 py-2 break-anywhere">{{ $report['exactLocation'] ?? 'N/A' }}</td>
              <td class="px-4 py-2">{{ $report['date'] ?? 'N/A' }} {{ $report['reportTime'] ?? 'N/A' }}</td>
              <td class="px-4 py-2 status text-{{ $color }}-500">{{ $status }}</td>
              <td class="px-4 py-2 space-x-2 flex items-center">
                <a href="javascript:void(0);"
                class="msg-btn"
                data-key="emsReports|{{ $report['id'] }}"
                onclick="openMessageModal('{{ $report['id'] }}', 'emsReports')">
                <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                <span class="msg-badge hidden">0</span>
                </a>

                @if(isset($report['latitude'], $report['longitude']) && $report['latitude'] !== null && $report['longitude'] !== null)
                  <a href="javascript:void(0);" onclick="openLocationModal({{ $report['latitude'] }}, {{ $report['longitude'] }})">
                    <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
                  </a>
                @endif
                <a href="javascript:void(0);" onclick="openDetailsModal('{{ $report['id'] }}', 'emsReports')">
                  <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- =========================================================
  = Section: SMS Reports
  ========================================================= -->
  <div id="smsReportsSection" class="bg-white p-6 shadow rounded-lg mt-6 hidden">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">SMS Reports</h2>
    <div class="table-wrap max-h-96 overflow-y-auto">
      <table class="min-w-full table-auto table-min">
        <thead class="sticky top-0 z-10">
          <tr class="bg-gray-100">
            <th class="px-4 py-2 text-left text-gray-600 col-index">#</th>
            <th class="px-4 py-2 text-left text-gray-600">Location</th>
            <th class="px-4 py-2 text-left text-gray-600 col-datetime">Date & Time</th>
            <th class="px-4 py-2 text-left text-gray-600">Status</th>
            <th class="px-4 py-2 text-left text-gray-600">Action</th>
          </tr>

          <tr class="bg-gray-50 text-sm">
            <th></th>
            <th class="px-4 py-2">
              <input id="smsLocationSearch" type="text" placeholder="Search location..."
                     class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                     oninput="filterSmsReportsTable()"/>
            </th>
            <th class="px-4 py-2">
              <select id="smsDateTimeFilter"
                      class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                      onchange="handleSmsDateTimeFilterChange()">
                <option value="all" selected>All</option>
                <option value="date">Date</option>
                <option value="time">Time</option>
              </select>
              <input id="smsDateSearch" type="date"
                     class="hidden w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                     onchange="filterSmsReportsTable()"/>
              <input id="smsTimeSearch" type="time"
                     class="hidden w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                     onchange="filterSmsReportsTable()"/>
            </th>
            <th class="px-4 py-2">
              <select id="smsStatusFilter"
                      class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                      onchange="filterSmsReportsTable()">
                <option value="">All</option>
                <option value="Pending">Pending</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Completed">Completed</option>
                <option value="Received">Received</option>
              </select>
            </th>
            <th></th>
          </tr>
        </thead>

        <tbody id="smsReportsBody">
          @foreach(($smsReports ?? []) as $index => $report)
            @php
              $lat = $report['latitude'] ?? null; $lng = $report['longitude'] ?? null;
              $statusRaw = $report['status'] ?? 'Pending';
              $status    = ucfirst(strtolower($statusRaw));
              $color     = $status === 'Ongoing'   ? 'red'
                         : ($status === 'Completed'? 'green'
                         : ($status === 'Pending'  ? 'orange'
                         : ($status === 'Received' ? 'blue' : 'yellow')));
            @endphp
            <tr id="reportRow{{ $report['id'] }}" class="border-b"
                data-report='@json($report)' data-type="smsReports">
              <td class="px-4 py-2">{{ $index + 1 }}</td>
              <td class="px-4 py-2 break-anywhere">{{ $report['location'] ?? 'N/A' }}</td>
              <td class="px-4 py-2">{{ $report['date'] ?? 'N/A' }} {{ $report['time'] ?? 'N/A' }}</td>
              <td class="px-4 py-2 status text-{{ $color }}-500">{{ $status }}</td>
                <td class="px-4 py-2 space-x-2 flex items-center">
                <a href="javascript:void(0);"
                class="msg-btn"
                data-key="smsReports|{{ $report['id'] }}"
                onclick="openMessageModal('{{ $report['id'] }}', 'smsReports')">
                <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                <span class="msg-badge hidden">0</span>
                </a>

                @if(!is_null($lat) && !is_null($lng))
                    <a href="javascript:void(0);" onclick="openLocationModal({{ $lat }}, {{ $lng }})">
                    <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
                    </a>
                @endif
                <a href="javascript:void(0);" onclick="openDetailsModal('{{ $report['id'] }}', 'smsReports')">
                    <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
                </a>
                </td>

            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>


 <!-- =========================================================
  = Section: Fire Fighter Chat Table (Name, Contact, Action)
  ========================================================= -->
<div id="fireFighterChatSection" class="bg-white p-6 shadow rounded-lg mt-6 hidden">
  <h2 class="text-xl font-semibold text-gray-700 mb-4">Fire Fighter Chat Reports</h2>
  <div class="table-wrap max-h-96 overflow-y-auto">
    <table class="min-w-full table-auto table-min">
      <thead class="sticky top-0 z-10">
        <tr class="bg-gray-100">
          <th class="px-4 py-2 text-left text-gray-600">#</th>
          <th class="px-4 py-2 text-left text-gray-600">Name</th>
          <th class="px-4 py-2 text-left text-gray-600">Contact</th>
          <th class="px-4 py-2 text-left text-gray-600">Action</th>
        </tr>

        <tr class="bg-gray-50 text-sm">
          <th></th>
          <th class="px-4 py-2">
            <input id="fireFighterChatNameSearch" type="text" placeholder="Search Name..."
                   class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                   oninput="filterFireFighterChatTable()"/>
          </th>
          <th class="px-4 py-2">
            <input id="fireFighterChatContactSearch" type="text" placeholder="Search Contact..."
                   class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                   oninput="filterFireFighterChatTable()"/>
          </th>
          <th></th>
        </tr>
      </thead>

      <tbody id="fireFighterChatBody">
        @foreach(($fireFighterChatReports ?? []) as $index => $report)
          @php
            $lat = $report['latitude'] ?? null;
            $lng = $report['longitude'] ?? null;
          @endphp
          <tr id="reportRow{{ $report['id'] }}" class="border-b"
              data-report='@json($report)' data-type="fireFighterChatReports">
            <td class="px-4 py-2">{{ $index + 1 }}</td>
            <td class="px-4 py-2">{{ $report['name'] ?? 'N/A' }}</td>
            <td class="px-4 py-2">{{ $report['contact'] ?? 'N/A' }}</td>

            <!-- Action: Message, Location (if coords), Details -->
            <td class="px-4 py-2 space-x-3 flex items-center">
              <!-- Message -->
              <a href="javascript:void(0);"
                 class="msg-btn inline-flex items-center"
                 title="Open messages"
                 aria-label="Open messages"
                 data-key="fireFighterChatReports|{{ $report['id'] }}"
                 onclick="openMessageModal('{{ $report['id'] }}', 'fireFighterChatReports')">
                <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                <span class="msg-badge hidden">0</span>
              </a>

              <!-- Location (only if lat/lng exist) -->
              @if(!is_null($lat) && !is_null($lng))
                <a href="javascript:void(0);"
                   class="inline-flex items-center"
                   title="Open location"
                   aria-label="Open location"
                   onclick="openLocationModal({{ $lat }}, {{ $lng }})">
                  <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
                </a>
              @endif

              <!-- Details -->
              <a href="javascript:void(0);"
                 class="inline-flex items-center"
                 title="View details"
                 aria-label="View details"
                 onclick="openDetailsModal('{{ $report['id'] }}', 'fireFighterChatReports')">
                <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- ===========================
     FF CHAT: MESSAGE MODAL
=========================== -->
<div id="ffChatMessageModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
  <div class="bg-white rounded-lg p-6 w-full max-w-lg shadow-lg relative  modal-panel">
    <h3 class="text-lg font-semibold mb-2 text-gray-800">
      Message Station: <span id="ffChatMsgStationName" class="text-blue-700"></span>
    </h3>
    <!-- Chat message thread with fixed height and scrollable content -->
    <div id="ffChatMsgThread" class="h-64 overflow-y-auto border border-gray-200 p-3 rounded mb-4 bg-gray-50"></div>
    <form id="ffChatMsgForm" class="flex gap-2">
      <input type="hidden" id="ffChatMsgStationKey">
      <input id="ffChatMsgInput" type="text" class="flex-1 border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
             placeholder="Type a message..." required>
      <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded" type="submit">Send</button>
    </form>
    <button onclick="closeFFChatMessageModal()"
            class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
  </div>
</div>


<!-- ===========================
     FF CHAT: LOCATION MODAL
=========================== -->
<div id="ffChatLocationModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
  <div class="bg-white rounded-lg p-6 w-full max-w-2xl shadow-lg relative">
    <h3 class="text-lg font-semibold mb-4 text-gray-800">
      Station Location: <span id="ffChatLocStationName" class="text-blue-700"></span>
    </h3>
    <div id="ffChatLocationInfo" class="text-gray-700 mb-4"></div>
    <div id="ffChatLocationMap" class="w-full h-80 rounded border border-gray-200 overflow-hidden bg-gray-100 flex items-center justify-center">
      <span class="text-gray-500 text-sm">No map to show.</span>
    </div>
    <button onclick="closeFFChatLocationModal()"
            class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
  </div>
</div>

<!-- ===========================
     FF CHAT: DETAILS MODAL
=========================== -->
<div id="ffChatDetailsModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center">
  <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg relative">
    <h3 class="text-lg font-semibold mb-4 text-gray-800">Station Details</h3>
    <div class="space-y-2 text-gray-700">
      <div><strong>Key:</strong> <span id="ffChatDetKey"></span></div>
      <div><strong>Name:</strong> <span id="ffChatDetName"></span></div>
      <div><strong>Contact:</strong> <span id="ffChatDetContact"></span></div>
      <div><strong>Email:</strong> <span id="ffChatDetEmail"></span></div>
    </div>
    <div id="ffChatDetExtra" class="mt-4 text-sm text-gray-600"></div>
    <div class="mt-5 flex justify-end gap-2">
      <button class="px-4 py-2 rounded bg-blue-600 text-white"
              onclick="openFFChatMessageModal(document.getElementById('ffChatDetKey').textContent)">Message</button>
      <button class="px-4 py-2 rounded bg-gray-200"
              onclick="closeFFChatDetailsModal()">Close</button>
    </div>
    <button onclick="closeFFChatDetailsModal()"
            class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
  </div>
</div>



  <!-- =========================================================
  = Modal: Fire Message
  ========================================================= -->
  <div id="fireMessageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-6xl shadow-lg relative  modal-panel">
      <h2 class="text-lg font-semibold mb-4 text-gray-800" id="fireMessageNameValue"></h2>

      <div class="mb-4">
        <p><strong>Incident ID:</strong> <span id="fireMessageIncidentIdValue"></span></p>
        <p><strong>Contact:</strong> <span id="fireMessageContactValue"></span></p>
      </div>

      <div id="fireMessageThread" class="h-64 overflow-y-auto border border-gray-200 p-4 rounded mb-4 bg-gray-50 scroll-smooth" style="height: 500px;"></div>

      <form id="fireMessageForm" class="flex gap-2">
        <input type="hidden" id="fireMessageIncidentInput">
        <input type="text" id="fireMessageInput" class="flex-1 border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type a message..." required>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Send</button>
      </form>
      <button onclick="closeFireMessageModal()" class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-5xl leading-none">&times;</button>
    </div>
  </div>

  <!-- =========================================================
  = Modal: Other Emergency Message
  ========================================================= -->
  <div id="otherEmergencyMessageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl shadow-lg relative  modal-panel">
      <h3 class="text-lg font-semibold mb-4 text-gray-800">Other Emergency Incident Chat
        <span id="otherEmergencyMessageIncidentId" class="text-sm text-gray-500"></span>
      </h3>

      <div id="otherEmergencyMessageThread" class="h-64 overflow-y-auto border border-gray-200 p-4 rounded mb-4 bg-gray-50 scroll-smooth"></div>

      <form id="otherEmergencyMessageForm" class="flex gap-2">
        <input type="hidden" id="otherEmergencyMessageIncidentInput">
        <input type="text" id="otherEmergencyMessageInput" class="flex-1 border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type a message...">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Send</button>
      </form>
      <button onclick="closeOtherEmergencyMessageModal()" class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
    </div>
  </div>

  <!-- =========================================================
  = Modal: EMS Message
  ========================================================= -->
  <div id="emsMessageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl shadow-lg relative  modal-panel">
      <h3 class="text-lg font-semibold mb-4 text-gray-800">EMS Incident Chat
        <span id="emsMessageIncidentId" class="text-sm text-gray-500"></span>
      </h3>

      <div id="emsMessageThread" class="h-64 overflow-y-auto border border-gray-200 p-4 rounded mb-4 bg-gray-50 scroll-smooth"></div>

      <form id="emsMessageForm" class="flex gap-2">
        <input type="hidden" id="emsMessageIncidentInput">
        <input type="text" id="emsMessageInput" class="flex-1 border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type a message...">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Send</button>
      </form>
      <button onclick="closeEmsMessageModal()" class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
    </div>
  </div>

  <!-- =========================================================
  = Modal: Location (Route & Geofence Maps)
  ========================================================= -->
  <div id="locationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg p-6 w-full shadow-lg relative modal-shell"
         style="max-width: 1100px; width: calc(100% - 280px); margin-left: 350px;">
      <h3 class="text-lg font-semibold mb-4 text-gray-800">Report Location</h3>

      <div class="flex gap-4">
        <div style="flex:1; display:flex; flex-direction:column;">
          <div id="routeMap" style="flex:1; height: 470px;"></div>
          <div id="routeInfo"
               class="mt-2 text-sm text-gray-700"
               style="min-height: 40px; max-height:120px; overflow-y:auto;">
            <em>Finding best routes…</em>
          </div>
        </div>

        <div id="fenceMap" style="flex:1; height: 470px;"></div>
      </div>

      <button onclick="closeLocationModal()"
              class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">
        &times;
      </button>
    </div>
  </div>


<!-- =========================================================
= Modal: Details (Fire / Other / EMS / SMS)
========================================================= -->
<div id="detailsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
  <div class="bg-white rounded-lg p-6 w-full max-w-4xl shadow-lg relative">

    <h3 class="text-2xl font-semibold text-gray-800 mb-6">Incident Report Details</h3>

    <div class="space-y-6">

      <!-- ============================
           Fire Report Details
      ============================= -->
      <div id="fireReportDetails" class="hidden">
        <div class="flex flex-col md:flex-row md:items-start md:space-x-8">
          <!-- LEFT: Text -->
          <div class="flex-1 space-y-2">
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Incident ID:</strong>
              <span id="detailIncidentId" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Reporter Name:</strong>
              <span id="detailName" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Contact:</strong>
              <span id="detailContact" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Fire Type:</strong>
              <span id="detailFireType" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Exact Location:</strong>
              <span id="detailLocation" class="text-gray-600 flex-1 truncate" title=""></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Date:</strong>
              <span id="detailDate" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Report Time:</strong>
              <span id="detailReportTime" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Status:</strong>
              <span id="detailStatus" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div>
              <a id="detailFireMapLink" class="text-sm text-blue-600 underline break-all">Open in Maps</a>
            </div>
          </div>

          <!-- RIGHT: Photo -->
          <div class="flex justify-center items-start mt-4 md:mt-0 md:w-[420px]">
            <div id="detailFirePhoto" class="w-full h-[260px] overflow-hidden rounded-lg shadow">
              <img id="detailFirePhotoImg" src="" alt="Fire Photo" class="w-full h-full object-cover rounded-lg">
            </div>
          </div>
        </div>
      </div>

      <!-- ============================
           Other Emergency Details
           (matches Fire)
      ============================= -->
      <div id="otherEmergencyDetails" class="hidden">
        <div class="flex flex-col md:flex-row md:items-start md:space-x-8">
          <!-- LEFT: Text -->
          <div class="flex-1 space-y-2">
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Incident ID:</strong>
              <span id="detailIncidentIdOther" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Reporter Name:</strong>
              <span id="detailNameOther" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Contact:</strong>
              <span id="detailContactOther" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Emergency Type:</strong>
              <span id="detailEmergencyType" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <!-- Exact Location single line -->
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Exact Location:</strong>
              <span id="detailLocationOther" class="text-gray-600 flex-1 truncate" title=""></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Date:</strong>
              <span id="detailDateOther" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Report Time:</strong>
              <span id="detailReportTimeOther" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Status:</strong>
              <span id="detailStatusOther" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div>
              <a id="detailOtherMapLink" class="text-sm text-blue-600 underline break-all">Open in Maps</a>
            </div>
          </div>

          <!-- RIGHT: Photo (same size as Fire) -->
          <div class="flex justify-center items-start mt-4 md:mt-0 md:w-[420px]">
            <div id="detailOtherPhoto" class="w-full h-[260px] overflow-hidden rounded-lg shadow">
              <img id="detailOtherPhotoImg" src="" alt="Incident Photo" class="w-full h-full object-cover rounded-lg">
            </div>
          </div>
        </div>
      </div>

      <!-- ============================
           EMS Report Details
           (matches Fire)
      ============================= -->
      <div id="emsDetails" class="hidden">
        <div class="flex flex-col md:flex-row md:items-start md:space-x-8">
          <!-- LEFT: Text -->
          <div class="flex-1 space-y-2">
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Incident ID:</strong>
              <span id="detailIncidentIdEms" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Reporter Name:</strong>
              <span id="detailNameEms" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Contact:</strong>
              <span id="detailContactEms" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">EMS Type:</strong>
              <span id="detailTypeEms" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <!-- Exact Location single line -->
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Exact Location:</strong>
              <span id="detailLocationEms" class="text-gray-600 flex-1 truncate" title=""></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Date:</strong>
              <span id="detailDateEms" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Report Time:</strong>
              <span id="detailReportTimeEms" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div class="flex">
              <strong class="text-gray-700 w-40 shrink-0">Status:</strong>
              <span id="detailStatusEms" class="text-gray-600 flex-1 break-words"></span>
            </div>
            <br>
            <div>
              <a id="detailEmsMapLink" class="text-sm text-blue-600 underline break-all">Open in Maps</a>
            </div>
          </div>

          <!-- RIGHT: Photo (same size as Fire) -->
          <div class="flex justify-center items-start mt-4 md:mt-0 md:w-[420px]">
            <div id="detailEmsPhoto" class="w-full h-[260px] overflow-hidden rounded-lg shadow">
              <img id="detailEmsPhotoImg" src="" alt="EMS Photo" class="w-full h-full object-cover rounded-lg">
            </div>
          </div>
        </div>
      </div>

            <!-- ============================
        SMS Report Details
    ============================= -->
    <div id="smsDetails" class="hidden">
    <div class="flex flex-col md:flex-row md:items-start md:space-x-8">

        <!-- LEFT: Text -->
        <div class="flex-1 space-y-2">

        <div class="flex">
            <strong class="text-gray-700 w-40 shrink-0">Incident ID:</strong>
            <span id="detailIncidentIdSms" class="text-gray-600 flex-1 break-words"></span>
        </div>
        <br>

        <div class="flex">
            <strong class="text-gray-700 w-40 shrink-0">Reporter Name:</strong>
            <span id="detailNameSms" class="text-gray-600 flex-1 break-words"></span>
        </div>
        <br>

        <div class="flex">
            <strong class="text-gray-700 w-40 shrink-0">Contact:</strong>
            <span id="detailContactSms" class="text-gray-600 flex-1 break-words"></span>
        </div>
        <br>

            <!-- Report text / message -->
        <div class="flex">
            <strong class="text-gray-700 w-40 shrink-0">Report Text:</strong>
            <span id="detailSmsReportText" class="text-gray-600 flex-1 break-words"></span>
        </div>
        <br>

        <div class="flex">
            <strong class="text-gray-700 w-40 shrink-0">Date:</strong>
            <span id="detailDateSms" class="text-gray-600 flex-1 break-words"></span>
        </div>
        <br>

        <div class="flex">
            <strong class="text-gray-700 w-40 shrink-0">Report Time:</strong>
            <span id="detailReportTimeSms" class="text-gray-600 flex-1 break-words"></span>
        </div>
        <br>

        <div class="flex">
            <strong class="text-gray-700 w-40 shrink-0">Status:</strong>
            <span id="detailStatusSms" class="text-gray-600 flex-1 break-words"></span>
        </div>
        <br>

            <!-- Exact Location single line (string location from DB) -->
        <div class="flex">
            <strong class="text-gray-700 w-40 shrink-0">Exact Location:</strong>
            <span id="detailLocationSms" class="text-gray-600 flex-1 truncate" title=""></span>
        </div>
        <br>

        <div class="flex">
            <strong class="text-gray-700 w-40 shrink-0">Distance:</strong>
            <span id="detailNearestDistanceSms" class="text-gray-600 flex-1 break-words"></span>
        </div>
        <br>

        </div>
    </div>
    </div>


      <!-- ============================
           SMS Extra Panel (unchanged)
      ============================= -->
        <div id="smsExtra" class="space-y-4 hidden mt-6">
        <div class="flex justify-between">
            <strong class="text-gray-700">Nearest Station:</strong>
            <span id="detailSmsStation" class="text-gray-600"></span>
        </div>
        <div>
            <strong class="text-gray-700 block mb-1">Report Details:</strong>
            <p id="detailSmsReportText" class="text-gray-600"></p>
        </div>
        </div>



    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end mt-6 space-x-4">
      <button onclick="closeDetailsModal()" style="background-color: #E00024; height:45px; width:90px; margin-top:7px;"
              class="px-6 py-2 text-white rounded-md hover:bg-gray-600">Close</button>
      <div id="statusActionButtons" class="flex gap-2"></div>
    </div>

    <button onclick="closeDetailsModal()"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
  </div>
</div>

</div>


<script>
/* =========================================================
 * LIFECYCLE / ENTRYPOINTS
 * ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  // Render immediately so All Reports isn't empty on first paint
  safeRenderAllReports();
  handleUrlParams();
  safeInitRealtime();

  toggleIncidentTables();
  normalizeInitialTimes();
  renderAllReports();
  safeInitFireFighterAccounts();
  hardLoadFF();
});

/* =========================================================
 * FIRE FIGHTER CHAT: LOAD & LIVE SYNC FROM ACCOUNTS
 * Path: TagumCityCentralFireStation/FireFighter/AllFireFighterAccount
 * ========================================================= */

/* =========================================================
 * FIRE FIGHTER CHAT TABLE
 * Shows the 3 known accounts:
 *   MabiniFireFighterAccount
 *   LaFilipinaFireFighterAccount
 *   CanocotanFireFighterAccount
 * Path: TagumCityCentralFireStation/FireFighter/AllFireFighterAccount
 * ========================================================= */

const STATION_ROOT = 'TagumCityCentralFireStation';
const HQ_PROFILE_PATH = `${STATION_ROOT}/Profile`;

const FIREFIGHTER_PATH =
  `${STATION_ROOT}/FireFighter/AllFireFighterAccount`;

const FF_ACCOUNTS_BASE = FIREFIGHTER_PATH;

function _el(id){ return document.getElementById(id); }
function _show(id){ _el(id)?.classList.remove('hidden'); }
function _hide(id){ _el(id)?.classList.add('hidden'); }
function _safe(v, d='N/A'){ return (v==null || String(v).trim()==='') ? d : String(v); }

/* ---------- HQ coords helper (from Profile) ---------- */
async function getHQCoords(){
  const s = await firebase.database().ref(HQ_PROFILE_PATH).once('value');
  const p = s.val() || {};
  const lat = parseFloat(p.latitude);
  const lng = parseFloat(p.longitude);
  return (Number.isFinite(lat) && Number.isFinite(lng)) ? { lat, lng, raw: p } : null;
}

/* ---------- table load (single source of truth; no duplicate icons) ---------- */
function loadAllFireFighterAccounts() {
  const body = _el('fireFighterChatBody');
  if (!body) return;

  if (!window.firebase || !firebase.apps?.length || typeof firebase.database !== 'function') {
    setTimeout(loadAllFireFighterAccounts, 500);
    return;
  }

  // Clear before rendering to avoid any duplicate rows
  body.innerHTML = '';

  firebase.database().ref(FIREFIGHTER_PATH).once('value')
    .then(snap => {
      if (!snap.exists()) {
        body.innerHTML = `
          <tr>
            <td colspan="4" class="px-4 py-3 text-center text-gray-500">
              No firefighter accounts found.
            </td>
          </tr>`;
        return;
      }

      const data = snap.val() || {};
      const STATIONS = [
        'MabiniFireFighterAccount',
        'LaFilipinaFireFighterAccount',
        'CanocotanFireFighterAccount'
      ];

      const rows = STATIONS.map((key, i) => {
        const v = data[key] || {};
        const name    = v.name    || key;
        const contact = v.contact || 'N/A';

        // ACTIONS: message, location, details
        return `
          <tr class="border-b fire-fighter-row">
            <td class="px-4 py-2">${i + 1}</td>
            <td class="px-4 py-2 name-cell">${name}</td>
            <td class="px-4 py-2 contact-cell">${contact}</td>
            <td class="px-4 py-2 space-x-3 flex items-center">
              <!-- Message (envelope) -->
              <a href="javascript:void(0);" title="Message"
                 class="inline-flex items-center"
                 onclick="openFFChatMessageModal('${key}')">
                <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
              </a>

              <!-- Location (pin) -->
              <a href="javascript:void(0);" title="Location"
                 class="inline-flex items-center"
                 onclick="openFFChatLocationModal('${key}')">
                <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
              </a>

              <!-- Details (document) -->
              <a href="javascript:void(0);" title="Details"
                 class="inline-flex items-center"
                 onclick="openFFChatDetailsModal('${key}')">
                <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
              </a>
            </td>
          </tr>`;
      }).join('');

      body.innerHTML = rows || `
        <tr><td colspan="4" class="px-4 py-3 text-center text-gray-500">
          No firefighter accounts found.
        </td></tr>`;
    })
    .catch(err => {
      console.error('[FF] Read error:', err);
      const msg = err?.code === 'PERMISSION_DENIED'
        ? 'Permission denied by Firebase rules.'
        : (err?.message || 'Error reading data.');
      body.innerHTML = `
        <tr>
          <td colspan="4" class="px-4 py-3 text-center text-red-600">${msg}</td>
        </tr>`;
    });
}

/* ---------- DETAILS MODAL ---------- */
// Pull one station object
function getFFStation(key){
  return firebase.database().ref(`${FF_ACCOUNTS_BASE}/${key}`).once('value')
    .then(s => ({ key, ...(s.val() || {}) }));
}

async function openFFChatDetailsModal(stationKey){
  try{
    const st = await getFFStation(stationKey);
    _el('ffChatDetKey').textContent     = stationKey;
    _el('ffChatDetName').textContent    = _safe(st.name, stationKey);
    _el('ffChatDetContact').textContent = _safe(st.contact);
    _el('ffChatDetEmail').textContent   = _safe(st.email, '—');
    _el('ffChatDetExtra').textContent   = st.notes ? String(st.notes) : '';
    _show('ffChatDetailsModal');
  } catch(e){
    console.error('[FF Chat][Details] load failed', e);
    alert('Could not load station details.');
  }
}
function closeFFChatDetailsModal(){ _hide('ffChatDetailsModal'); }

/* ---------- MESSAGE MODAL (simple live thread) ---------- */
/* ---------- MESSAGE MODAL (text / image / audio) ---------- */
/* ---------- MESSAGE MODAL (text / image / audio) ---------- */
function renderFFChatThread(list = []) {
  const box = _el('ffChatMsgThread');

  const esc = (s='') => s.replace(/[&<>"']/g, c => ({
    '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
  }[c]));

  // Formatters
  const pad = n => String(n).padStart(2,'0');
  const MONTHS = ["JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC"];
  const fmtFull = ms => {
    const d = new Date(ms);
    return `${MONTHS[d.getMonth()]} ${pad(d.getDate())} ${d.getFullYear()} - ${pad(d.getHours())}:${pad(d.getMinutes())}`;
  };
  const fmtTime = ms => {
    const d = new Date(ms);
    return `${pad(d.getHours())}:${pad(d.getMinutes())}`;
  };

  // Show a full header if gap ≥ 6h or date changed
  const SIX_HOURS = 6 * 60 * 60 * 1000;
  const isNewDay = (a,b) => {
    const da = new Date(a), db = new Date(b);
    return da.getFullYear()!==db.getFullYear() || da.getMonth()!==db.getMonth() || da.getDate()!==db.getDate();
  };
  const shouldHeader = (lastHeaderTs, curTs) =>
    lastHeaderTs===null || (curTs - lastHeaderTs) >= SIX_HOURS || isNewDay(lastHeaderTs, curTs);

  const bubbleBody = (m) => {
    if (m.text && m.text.trim().length) {
      return `<div class="whitespace-pre-wrap break-words">${esc(m.text)}</div>`;
    }
    if (m.imageBase64 && m.imageBase64.length) {
      return `
        <a href="data:image/jpeg;base64,${m.imageBase64}" target="_blank" rel="noopener">
          <img
            src="data:image/jpeg;base64,${m.imageBase64}"
            alt="image"
            style="
              display:block;
              width: 260px;
              max-width: 100%;
              height: auto;
              max-height: 700px;
              object-fit: cover;
              border-radius: 8px;
            "
            loading="lazy"
          />
        </a>`;
    }
    if (m.audioBase64 && m.audioBase64.length) {
      return `
        <div class="flex items-center gap-2">
          <audio controls preload="metadata"
                 src="data:audio/mp4;base64,${m.audioBase64}"
                 class="h-9 w-[220px]"></audio>
          <span class="text-xs opacity-80"></span>
        </div>`;
    }
    return `<em class="opacity-70">Unsupported/empty message</em>`;
  };

  // Build with headers
  let lastHeaderTs = null;
  const html = (list || [])
    .sort((a,b)=>(a.timestamp||0)-(b.timestamp||0))
    .map(m => {
      const ts = m.timestamp || Date.now();
      const fromAdmin = m.sender === 'admin';
      const header = shouldHeader(lastHeaderTs, ts)
        ? `<div class="text-center my-3">
             <span class="inline-block text-xs px-3 py-1 rounded-full bg-gray-200 text-gray-700">
               ${fmtFull(ts)}
             </span>
           </div>`
        : '';
      if (header) lastHeaderTs = ts;

      return `
        ${header}
        <div class="mb-2 ${fromAdmin ? 'text-right' : ''}">
          <div class="inline-block px-3 py-2 rounded ${fromAdmin ? 'bg-blue-600 text-white' : 'bg-gray-200'}"
               style="max-width: 75%;">
            ${bubbleBody(m)}
          </div>
          <div class="text-xs text-gray-500 mt-1">${fmtTime(ts)}</div>
        </div>`;
    }).join('');

  box.innerHTML = html;
  box.scrollTop = box.scrollHeight;
}



let __ffChatUnsub = null;

async function openFFChatMessageModal(stationKey){
  const nameSpan = _el('ffChatMsgStationName');
  const inputKey = _el('ffChatMsgStationKey');
  const thread   = _el('ffChatMsgThread');

  try{
    const st = await getFFStation(stationKey);
    nameSpan.textContent = _safe(st.name, stationKey);
    inputKey.value = stationKey;
    thread.innerHTML = '<em class="text-gray-500">Loading messages…</em>';
    _show('ffChatMessageModal');

    // Cancel previous listener if any
    if (__ffChatUnsub && typeof __ffChatUnsub.off === 'function') {
      __ffChatUnsub.off();
      __ffChatUnsub = null;
    }

    const ref = firebase.database().ref(`${FF_ACCOUNTS_BASE}/${stationKey}/AdminMessages`);
    const snapshotToArray = (snap) => {
    const out = [];
    snap.forEach(c => {
        const message = c.val() || {};
        console.log('Raw message:', message);  // Log individual messages before sorting
        out.push({ id: c.key, ...message });
    });
    out.sort((a, b) => (a.timestamp || 0) - (b.timestamp || 0));
    console.log('Sorted messages:', out);  // Log sorted messages to check order
    return out;
    };


    // initial + live
    const init = await ref.once('value');
    renderFFChatThread(snapshotToArray(init));

    ref.on('value', s => renderFFChatThread(snapshotToArray(s)));
    __ffChatUnsub = ref;

  } catch(e){
    console.error('[FF Chat][Message] open failed', e);
    alert('Could not open messages.');
  }
}

function closeFFChatMessageModal(){
  if (__ffChatUnsub && typeof __ffChatUnsub.off === 'function') {
    __ffChatUnsub.off();
    __ffChatUnsub = null;
  }
  _hide('ffChatMessageModal');
}

// send
(() => {
  const form = document.getElementById('ffChatMsgForm');
  if (!form) return;
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const key  = _el('ffChatMsgStationKey').value;
    const text = _el('ffChatMsgInput').value.trim();
    if (!key || !text) return;

    try {
      const now = new Date();
      const pad = n => String(n).padStart(2, '0');
      const date = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`; // YYYY-MM-DD
      const time = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`; // HH:mm:ss (24h)

      const ref = firebase.database().ref(`${FF_ACCOUNTS_BASE}/${key}/AdminMessages`).push();
      await ref.set({
        sender: 'admin',
        text,
        timestamp: now.getTime(),
        date,
        time,
        isRead: false  // 👈 added default unread flag
      });

      _el('ffChatMsgInput').value = '';
    } catch (err) {
      console.error('[FF Chat][Message] send failed', err);
      alert('Send failed.');
    }
  });
})();



/* =========================================================
 * EMAIL → STATION & LIVE GEO WRITE (for firefighter logins)
 * ========================================================= */
const EMAIL_TO_STATION = {
  'tcwestfiresubstation@gmail.com':     'MabiniFireFighterAccount',
  'lafilipinafire@gmail.com': 'LaFilipinaFireFighterAccount',
  'bfp_tagumcity@yahoo.com':  'CanocotanFireFighterAccount'
};

function stationKeyFromEmail(email){
  if(!email) return null;
  const key = EMAIL_TO_STATION[email.trim().toLowerCase()];
  return key || null;
}

let __geoWatchId = null;

function startFirefighterLocationTracking() {
  const email = window.AUTH_EMAIL || null;  // set this from your backend session
  const stationKey = stationKeyFromEmail(email);
  if (!stationKey) return; // not a firefighter → no tracking

  if (!('geolocation' in navigator)) {
    console.warn('[geo] Browser has no Geolocation API');
    return;
  }

  __geoWatchId = navigator.geolocation.watchPosition(
    pos => {
      const { latitude, longitude, accuracy, heading, speed } = pos.coords || {};
      const payload = {
        latitude, longitude,
        accuracy: Number.isFinite(accuracy) ? accuracy : null,
        heading:  Number.isFinite(heading)  ? heading  : null,
        speed:    Number.isFinite(speed)    ? speed    : null,
        updatedAt: Date.now()
      };

      firebase.database()
        .ref(`${FF_ACCOUNTS_BASE}/${stationKey}/liveLocation`)
        .set(payload)
        .catch(err => console.error('[geo] write failed:', err));
    },
    err => {
      console.warn('[geo] watchPosition error:', err);
      // fallback one-shot
      navigator.geolocation.getCurrentPosition(p => {
        const { latitude, longitude, accuracy } = p.coords || {};
        firebase.database()
          .ref(`${FF_ACCOUNTS_BASE}/${stationKey}/liveLocation`)
          .set({ latitude, longitude, accuracy, updatedAt: Date.now() })
          .catch(e => console.error('[geo] oneshot write failed:', e));
      }, () => {}, { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 });
    },
    { enableHighAccuracy: true, timeout: 15000, maximumAge: 5000 }
  );
}

function stopFirefighterLocationTracking(){
  try { if (__geoWatchId != null) navigator.geolocation.clearWatch(__geoWatchId); }
  catch(_) {}
  __geoWatchId = null;
}

// keep this call (no extra DOMContentLoaded is added)
startFirefighterLocationTracking();

/* =========================================================
 * LOCATION MODAL — Leaflet live map (station + HQ via Profile)
 * ========================================================= */

function getIcons() {
  return {
    station: L.icon({
      iconUrl: '/images/fire-truck.png',
      iconSize: [40, 40],
      iconAnchor: [20, 40],
      popupAnchor: [0, -35]
    }),
    hq: L.icon({
      iconUrl: '/images/fire-station.png',
      iconSize: [42, 42],
      iconAnchor: [21, 42],
      popupAnchor: [0, -35]
    })
  };
}

// State
let __ffMap = null;
let __ffMapInitedForKey = null;
let __ffStationMarker = null;
let __ffStationLiveRef = null;
let __hqMarker = null;

// listeners
function stopStationFirebaseListeners() {
  try { __ffStationLiveRef && __ffStationLiveRef.off(); } catch(_) {}
  __ffStationLiveRef = null;
}

/* Live-only listener */
function startStationLiveListener(stationKey, onUpdate) {
  stopStationFirebaseListeners();
  const ref = firebase.database().ref(`${FF_ACCOUNTS_BASE}/${stationKey}/liveLocation`);
  __ffStationLiveRef = ref;

  ref.on('value', snap => {
    const v = snap.val() || {};
    const lat = parseFloat(v.latitude);
    const lng = parseFloat(v.longitude);
    if (Number.isFinite(lat) && Number.isFinite(lng)) {
      onUpdate({ lat, lng, updatedAt: v.updatedAt || null });
    } else {
      console.warn('[liveLocation] invalid lat/lng', v);
    }
  }, err => console.error('[liveLocation] listener error:', err));
}

/* ---------- OPEN / CLOSE ---------- */
async function openFFChatLocationModal(stationKey){
  try{
    _show('ffChatLocationModal');
    _el('ffChatLocStationName').textContent = stationKey;

    const info  = _el('ffChatLocationInfo');
    const mapEl = _el('ffChatLocationMap');
    info.innerHTML = '<span class="text-gray-500">Loading…</span>';

    if (!window.L) { info.textContent = 'Leaflet not loaded.'; return; }

    const { station: ICON_STATION, hq: ICON_HQ } = getIcons();

    if (!__ffMap) {
      __ffMap = L.map(mapEl, { zoomControl: true, attributionControl: true });
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(__ffMap);
    }

    if (__ffMapInitedForKey !== stationKey) {
      __ffMapInitedForKey = stationKey;
      if (__ffStationMarker) { __ffStationMarker.remove(); __ffStationMarker = null; }
    }

    // HQ marker
    const hq = await getHQCoords();
    let hqHTML = '';
    if (hq && Number.isFinite(hq.lat) && Number.isFinite(hq.lng)) {
      if (!__hqMarker) {
        __hqMarker = L.marker([hq.lat, hq.lng], { icon: ICON_HQ })
          .addTo(__ffMap)
          .bindPopup('<b>Tagum City Central Fire Station (HQ)</b>');
      } else {
        __hqMarker.setLatLng([hq.lat, hq.lng]);
      }
      hqHTML = `
        <div class="mt-2 border-t border-gray-300 pt-1">
          <strong>Headquarters:</strong> Tagum City Central Fire Station<br>
          <span class="text-sm text-gray-600">
            Latitude: ${hq.lat.toFixed(6)}, Longitude: ${hq.lng.toFixed(6)}
          </span>
        </div>`;
    }

    // fit helper (use inside live callback)
    function fitToBoth(lat, lng) {
      const bounds = L.latLngBounds([]);
      bounds.extend([lat, lng]);                       // firefighter
      if (__hqMarker) bounds.extend(__hqMarker.getLatLng()); // HQ
      if (bounds.isValid()) __ffMap.fitBounds(bounds, { padding: [20, 20], maxZoom: 15 });
    }

    // Live firefighter coordinates
    startStationLiveListener(stationKey, ({ lat, lng, updatedAt }) => {
      if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

      if (!__ffStationMarker) {
        __ffStationMarker = L.marker([lat, lng], { icon: ICON_STATION })
          .addTo(__ffMap)
          .bindPopup(`<b>${stationKey}</b>`);
      } else {
        __ffStationMarker.setLatLng([lat, lng]);
      }

      info.innerHTML = `
        <div><strong>Station:</strong> ${_safe(stationKey)}</div>
        <div><strong>Coordinates:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
        ${updatedAt ? `<div class="text-xs text-gray-500">Updated: ${new Date(updatedAt).toLocaleString()}</div>` : ''}
        ${hqHTML}
      `;

      fitToBoth(lat, lng);   // <-- call here, after markers exist
    });

    setTimeout(() => { try { __ffMap.invalidateSize(); } catch(_) {} }, 200);

  } catch(e){
    console.error('[FF Chat][Location] open failed:', e);
    alert(`Could not load station location.\n${e?.message || ''}`);
  }
}

function closeFFChatLocationModal() {
  stopStationFirebaseListeners();
  _hide('ffChatLocationModal');
}

/* =========================================================
 * simple search filter
 * ========================================================= */
function filterFireFighterChatTable() {
  const nameFilter    = (_el('fireFighterChatNameSearch')?.value || '').toLowerCase();
  const contactFilter = (_el('fireFighterChatContactSearch')?.value || '').toLowerCase();
  document.querySelectorAll('#fireFighterChatBody .fire-fighter-row').forEach(row => {
    const n = row.querySelector('.name-cell')?.textContent.toLowerCase() || '';
    const c = row.querySelector('.contact-cell')?.textContent.toLowerCase() || '';
    row.style.display = n.includes(nameFilter) && c.includes(contactFilter) ? '' : 'none';
  });
}



/* =========================================================
 * URL PARAMS / NAV DEEP-LINKING
 * ========================================================= */
function handleUrlParams() {
  const params = new URLSearchParams(window.location.search);
  const incidentId = params.get('incidentId');
  const type = params.get('type');

  if (incidentId && type && typeof openDetailsModal === 'function') {
    setTimeout(() => {
      const typeDropdown = document.getElementById('incidentType');
      if (typeDropdown && typeDropdown.value !== type) {
        typeDropdown.value = type;
        toggleIncidentTables();
      }

      const row = document.getElementById(`reportRow${incidentId}`);
      if (row) {
        row.scrollIntoView({ behavior: "smooth", block: "center" });
        row.classList.add('bg-yellow-100');
        setTimeout(() => row.classList.remove('bg-yellow-100'), 3000);
      }

      openDetailsModal(incidentId, type);

      const newUrl = new URL(window.location);
      newUrl.searchParams.delete('incidentId');
      newUrl.searchParams.delete('type');
      window.history.replaceState({}, document.title, newUrl.toString());
    }, 500);
  }
}


/* =========================================================
 * DATA INITIALIZATION / GLOBALS
 * ========================================================= */

let fireReports             = @json($fireReports);
let otherEmergencyReports   = @json($otherEmergencyReports);
let emsReports              = @json($emsReports ?? []);
let smsReports              = @json($smsReports ?? []);

// Globals
let currentReport      = null;
let currentReportType  = 'fireReports';
let liveListeners      = [];
let storedMessages     = [];
let heardSmsIds        = new Set();

// put this near your other module-level vars
const __seenReplyKeys = new Set();
const __seenResponseKeys = new Set();


// Chat bubble coalescing
let __lastBubble       = { type: null, ts: 0, el: null };
const __GROUP_WINDOW_MS = 15000;


/* =========================================================
 * NORMALIZATION HELPERS (DATE/TIME)
 * ========================================================= */

function to24h(t) {
  if (!t) return '';
  const m = String(t).trim().match(/^(\d{1,2}):(\d{2})(?::\d{2})?\s*(AM|PM)?$/i);
  if (!m) return t;
  let [, hh, mm, ap] = m;
  let h = parseInt(hh, 10);
  if (ap) {
    const up = ap.toUpperCase();
    if (up === 'PM' && h !== 12) h += 12;
    if (up === 'AM' && h === 12) h = 0;
  }
  return `${String(h).padStart(2, '0')}:${mm}`;
}

function dateToISO(dmy) {
  if (!dmy) return '';
  const parts = dmy.split('/');
  if (parts.length !== 3) return '';
  const [dd, mm, yy] = parts;
  const yyyy = yy.length === 2 ? `20${yy}` : yy;
  return `${yyyy}-${mm.padStart(2, '0')}-${dd.padStart(2, '0')}`;
}


/* =========================================================
 * STATION CONTEXT / NODES (LOCKED TO CANOCOTAN)
 * ========================================================= */

// Hard-lock to Canocotan Fire Station (ignore session email)
const FORCE_PREFIX = 'TagumCityCentral';

function nodes() {
  const STATION_ROOT = 'TagumCityCentralFireStation';
  const ALL = `${STATION_ROOT}/AllReport`;

  return {
    // kept for compatibility with callers
    prefix: 'TagumCityCentral',

    // new base
    base: ALL,

    // per-type collections
    fireReport: `${ALL}/FireReport`,
    otherEmergency: `${ALL}/OtherEmergencyReport`,
    ems: `${ALL}/EmergencyMedicalServicesReport`,
    sms: `${ALL}/SmsReport`,

    // single SMS path now (no legacy variants)
    smsCandidates: [`${ALL}/SmsReport`],

    // station meta (adjust to your real location if different)
    profile: `${STATION_ROOT}/Profile`,
    firefighters: `${STATION_ROOT}/FireFighters`,
  };
}


async function resolveSmsPathById(id) {
  const n = nodes();
  if (!n) return null;

  for (const base of n.smsCandidates) {
    const snap = await firebase.database().ref(`${base}/${id}`).once('value');
    if (snap.exists()) return base;
  }
  return n.smsCandidates[0] || null;
}


/* =========================================================
 * REAL-TIME LISTENERS (FIRE / OTHER / EMS / SMS)
 * ========================================================= */

function initializeRealTimeListener() {
  const n = nodes();
  if (!n) { console.error("No station prefix."); return; }

  // FIRE
  firebase.database().ref(n.fireReport).on('child_added', (snapshot) => {
    const r = snapshot.val(); if (!r) return;
    const id = snapshot.key;
    if (document.getElementById(`reportRow${id}`)) return;
    r.id = id;
    insertNewReportRow(r, 'fireReports');
    renderAllReports();
  });
  firebase.database().ref(n.fireReport).on('child_changed', (snap) => {
    applyRealtimePatch(snap, 'fireReports');
    renderAllReports();
  });
  firebase.database().ref(n.fireReport).on('child_removed', (snap) => {
    removeRow(snap.key);
    const i = (fireReports || []).findIndex(x => x.id === snap.key);
    if (i !== -1) fireReports.splice(i, 1);
    renderAllReports();
  });

  // OTHER EMERGENCY
  firebase.database().ref(n.otherEmergency).on('child_added', (snapshot) => {
    const r = snapshot.val(); if (!r) return;
    r.id = snapshot.key;
    if (!document.getElementById(`reportRow${r.id}`)) insertNewReportRow(r, 'otherEmergency');
    renderAllReports();
  });
  firebase.database().ref(n.otherEmergency).on('child_changed', (snap) => {
    applyRealtimePatch(snap, 'otherEmergency');
    renderAllReports();
  });
  firebase.database().ref(n.otherEmergency).on('child_removed', (snap) => {
    removeRow(snap.key);
    const i = (otherEmergencyReports || []).findIndex(x => x.id === snap.key);
    if (i !== -1) otherEmergencyReports.splice(i, 1);
    renderAllReports();
  });

  // EMS
  firebase.database().ref(n.ems).on('child_added', (snapshot) => {
    const r = snapshot.val(); if (!r) return;
    r.id = snapshot.key;
    if (!document.getElementById(`reportRow${r.id}`)) insertNewEmsRow(r);
    renderAllReports();
  });
  firebase.database().ref(n.ems).on('child_changed', (snap) => {
    applyRealtimePatchEms(snap);
    renderAllReports();
  });
  firebase.database().ref(n.ems).on('child_removed', (snap) => {
    removeRow(snap.key);
    const i = (emsReports || []).findIndex(x => x.id === snap.key);
    if (i !== -1) emsReports.splice(i, 1);
    renderAllReports();
  });

  // SMS (listen on all candidate paths)
  (n.smsCandidates || []).forEach((path) => {
    const ref = firebase.database().ref(path);

    ref.on('child_added', (snapshot) => {
      const r = snapshot.val(); if (!r) return;
      const id = snapshot.key;
      if (heardSmsIds.has(id)) return;
      heardSmsIds.add(id);
      r.id = id;
      insertNewSmsRow(r);
      renderAllReports();
    });

    ref.on('child_changed', (snap) => {
      applyRealtimePatchSms(snap);
      renderSmsReports();   // keep the SMS table fresh too
      renderAllReports();
    });

    ref.on('child_removed', (snap) => {
      removeRow(snap.key);
      const i = (smsReports || []).findIndex(x => x.id === snap.key);
      if (i !== -1) smsReports.splice(i, 1);
      renderAllReports();
    });
  });
}

function applyRealtimePatchSms(snapshot) {
  const id = snapshot.key;
  const patch = snapshot.val() || {};

  const i = (smsReports || []).findIndex(r => r.id === id);
  if (i !== -1) smsReports[i] = { ...smsReports[i], ...patch, id };

  const row = document.getElementById(`reportRow${id}`);
  if (!row) return;
  row.setAttribute('data-report', JSON.stringify(smsReports[i] || patch));

  if (typeof patch.location !== 'undefined') row.children[1].textContent = patch.location || 'N/A';

  if (typeof patch.date !== 'undefined' || typeof patch.time !== 'undefined') {
    const r = JSON.parse(row.getAttribute('data-report')) || {};
    const t = to24h(r.time) || r.time || 'N/A';
    row.children[2].textContent = `${r.date || 'N/A'} ${t}`;
  }

  if (typeof patch.status !== 'undefined') {
    const s = capStatus(patch.status);
    const statusCell = row.querySelector('.status');
    if (statusCell) {
      statusCell.textContent = s;
      statusCell.className = `px-4 py-2 status text-${statusColor(s)}-500`;
    }
  }
}

function applyRealtimePatchEms(snapshot) {
  const id = snapshot.key;
  const patch = snapshot.val() || {};
  const i = (emsReports || []).findIndex(r => r.id === id);
  if (i !== -1) emsReports[i] = { ...emsReports[i], ...patch, id };

  const row = document.getElementById(`reportRow${id}`);
  if (!row) return;
  row.setAttribute('data-report', JSON.stringify(emsReports[i] || patch));

  // columns: 0 #, 1 type, 2 location, 3 datetime, 4 status, 5 action
  if (typeof patch.type !== 'undefined') row.children[1].textContent = patch.type || 'N/A';
  if (typeof patch.exactLocation !== 'undefined') row.children[2].textContent = patch.exactLocation || 'N/A';

  if (typeof patch.date !== 'undefined' || typeof patch.reportTime !== 'undefined') {
    const r = JSON.parse(row.getAttribute('data-report')) || {};
    row.children[3].textContent = `${r.date || 'N/A'} ${to24h(r.reportTime) || r.reportTime || 'N/A'}`;
  }

  if (typeof patch.status !== 'undefined') {
    const s = capStatus(patch.status);
    const statusCell = row.querySelector('.status');
    if (statusCell) {
      statusCell.textContent = s;
      statusCell.className = `px-4 py-2 status text-${statusColor(s)}-500`;
    }
  }
}



/* =========================================================
 * RENDERING: SMS TABLE (ADD/RENDER HELPERS)
 * ========================================================= */

function renderSmsReports(highlightId = null) {
  const body = document.getElementById('smsReportsBody');
  if (!body) return;

  const arr = asArray(smsReports).slice();
  arr.sort((a,b) => parseDT(b.date, b.time, b.timestamp ?? b.createdAt ?? b.updatedAt) -
                    parseDT(a.date, a.time, a.timestamp ?? a.createdAt ?? a.updatedAt));

  const rows = arr.map((report, index) => {
    const status = capStatus(report.status || 'Pending');
    const color  = statusColor(status);
    const hasLL  = report.latitude != null && report.longitude != null;
    const dt     = `${report.date || 'N/A'} ${to24h(report.time) || report.time || 'N/A'}`;
    const locBtn = hasLL
      ? `<a href="javascript:void(0);" onclick="openLocationModal(${report.latitude}, ${report.longitude})">
           <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
         </a>` : '';
    return `
      <tr id="reportRow${report.id}" class="border-b ${highlightId && report.id===highlightId ? 'bg-yellow-100' : ''}"
          data-report='${JSON.stringify(report)}' data-type="smsReports">
        <td class="px-4 py-2">${index + 1}</td>
        <td class="px-4 py-2">${report.location || 'N/A'}</td>
        <td class="px-4 py-2">${dt}</td>
        <td class="px-4 py-2 status text-${color}-500">${status}</td>
        <td class="px-4 py-2 space-x-2 flex items-center">
        <a href="javascript:void(0);" onclick="openMessageModal('${report.id}', 'smsReports')">
            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
        </a>
        ${locBtn}
        <a href="javascript:void(0);" onclick="openDetailsModal('${report.id}', 'smsReports')">
            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
        </a>
        </td>

      </tr>`;
  }).join('');

  body.innerHTML = rows;
}

function insertNewSmsRow(report) {
  report.date = report.date || new Date().toISOString().slice(0,10);
  report.time = report.time || new Date().toTimeString().slice(0,5);
  const idx = (smsReports || []).findIndex(r => r.id === report.id);
  if (idx === -1) smsReports.unshift(report);
  else smsReports[idx] = { ...smsReports[idx], ...report };
  renderSmsReports(report.id);
    ensureMessageBadges();
}


/* =========================================================
 * PATCH HELPERS (FIRE / OTHER)
 * ========================================================= */

function applyRealtimePatch(snapshot, reportType) {
  const id = snapshot.key;
  const patch = snapshot.val() || {};
  const arr = reportType === 'fireReports' ? fireReports : otherEmergencyReports;

  const i = arr.findIndex(r => r.id === id);
  if (i !== -1) arr[i] = { ...arr[i], ...patch, id };

  const row = document.getElementById(`reportRow${id}`);
  if (!row) return;

  row.setAttribute('data-report', JSON.stringify(arr[i] || patch));

  if (typeof patch.status !== 'undefined') {
    const statusCell = row.querySelector('.status');
    if (statusCell) {
      const s = capStatus(patch.status);
      statusCell.textContent = s;
      statusCell.className = `px-4 py-2 status text-${statusColor(s)}-500`;
    }
  }

  if (reportType === 'fireReports') {
    // 0 #, 1 type, 2 location, 3 datetime, 4 status, 5 action
    if (typeof patch.type !== 'undefined') row.children[1].textContent = patch.type || 'N/A';
    if (typeof patch.exactLocation !== 'undefined') row.children[2].textContent = patch.exactLocation || 'N/A';

    if (typeof patch.date !== 'undefined' || typeof patch.reportTime !== 'undefined') {
      const r = JSON.parse(row.getAttribute('data-report')) || {};
      row.children[3].textContent = `${r.date || 'N/A'} ${to24h(r.reportTime) || r.reportTime || 'N/A'}`;
    }
  } else {
    // other: 0 #, 1 location, 2 emergencyType, 3 datetime, 4 status, 5 action
    if (typeof patch.exactLocation !== 'undefined') row.children[1].textContent = patch.exactLocation || 'N/A';
    if (typeof patch.emergencyType !== 'undefined') row.children[2].textContent = patch.emergencyType || 'N/A';

    if (typeof patch.date !== 'undefined' || typeof patch.reportTime !== 'undefined') {
      const r = JSON.parse(row.getAttribute('data-report')) || {};
      row.children[3].textContent = `${r.date || 'N/A'} ${to24h(r.reportTime) || r.reportTime || 'N/A'}`;
    }
  }
}

function removeRow(id) {
  const el = document.getElementById(`reportRow${id}`);
  if (el && el.parentNode) el.parentNode.removeChild(el);
}


/* =========================================================
 * RENDERING: FIRE / OTHER / EMS TABLES
 * ========================================================= */

function insertNewReportRow(report, reportType) {
  const tableBodyId = reportType === 'fireReports' ? 'fireReportsBody' : 'otherEmergencyTableBody';
  const tableBody = document.getElementById(tableBodyId);
  if (!tableBody) return;
  if (document.getElementById(`reportRow${report.id}`)) return;

  report.date       = report.date || new Date().toLocaleDateString();
  report.reportTime = report.reportTime || new Date().toLocaleTimeString();

  function parseDateTime(dateStr, timeStr) {
    const [day, month, year] = dateStr.split('/');
    const normalizedYear = year && year.length === 2 ? '20' + year : year;
    return new Date(`${normalizedYear}-${month}-${day}T${timeStr}`);
  }

  if (reportType === 'fireReports') {
    fireReports.unshift(report);
    fireReports.sort((a, b) => parseDateTime(b.date, b.reportTime) - parseDateTime(a.date, a.reportTime));
    renderSortedReports(fireReports, 'fireReports', report.id);
  } else {
    otherEmergencyReports.unshift(report);
    otherEmergencyReports.sort((a, b) => parseDateTime(b.date, b.reportTime) - parseDateTime(a.date, a.reportTime));
    renderSortedReports(otherEmergencyReports, 'otherEmergency', report.id);
  }
  renderAllReports();
}

function insertNewEmsRow(report) {
  const body = document.getElementById('emsBody');
  if (!body) return;

  report.timestamp  = Number.isFinite(report.timestamp) ? report.timestamp : Date.now(); // ← ensure freshness in All Reports
  report.date       = report.date || new Date().toLocaleDateString();
  report.reportTime = report.reportTime || new Date().toLocaleTimeString();
  report.createdAt  = report.createdAt ?? report.timestamp;

  const idx = (emsReports || []).findIndex(r => r.id === report.id);
  if (idx === -1) emsReports.unshift(report);
  else emsReports[idx] = { ...emsReports[idx], ...report };

  renderEmsTable(report.id);
}

function renderEmsTable(highlightId = null) {
  const body = document.getElementById('emsBody');
  if (!body) return;

  const arr = asArray(emsReports).slice();
  // sort newest → oldest using datetime
  function parseDateTime(dateStr, timeStr) {
    const [day, month, year] = (dateStr || '').split('/');
    const normalizedYear = year && year.length === 2 ? '20' + year : year;
    const dt = new Date(`${normalizedYear}-${month}-${day}T${timeStr || '00:00'}`);
    return dt.getTime() || 0;
  }
  arr.sort((a,b) => parseDateTime(b.date, b.reportTime) - parseDateTime(a.date, a.reportTime));

  body.innerHTML = arr.map((report, index) => {
    const status = capStatus(report.status || 'Pending');
    const color  = statusColor(status);
    const hasLL  = report.latitude != null && report.longitude != null;

    return `
      <tr id="reportRow${report.id}" class="border-b ${highlightId && report.id===highlightId ? 'bg-yellow-100' : ''}"
          data-report='${JSON.stringify(report)}' data-type="emsReports">
        <td class="px-4 py-2">${index + 1}</td>
        <td class="px-4 py-2">${report.type || 'N/A'}</td>
        <td class="px-4 py-2">${report.exactLocation || 'N/A'}</td>
        <td class="px-4 py-2">${report.date || 'N/A'} ${to24h(report.reportTime) || report.reportTime || 'N/A'}</td>
        <td class="px-4 py-2 status text-${color}-500">${status}</td>
        <td class="px-4 py-2 space-x-2 flex items-center">
        <a href="javascript:void(0);" onclick="openMessageModal('${report.id}', 'emsReports')">
            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
        </a>
        ${hasLL ? `<a href="javascript:void(0);" onclick="openLocationModal(${report.latitude}, ${report.longitude})">
                    <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
                    </a>` : ''}
        <a href="javascript:void(0);" onclick="openDetailsModal('${report.id}', 'emsReports')">
            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
        </a>
        </td>
      </tr>`;
  }).join('');
  ensureMessageBadges();
}

function renderSortedReports(reportsArray, reportType, highlightId = null) {
  const tableBodyId = reportType === 'fireReports' ? 'fireReportsBody' : 'otherEmergencyTableBody';
  const tableBody = document.getElementById(tableBodyId);
  if (!tableBody) return;

  tableBody.style.visibility = 'hidden';
  const fragment = document.createDocumentFragment();

  reportsArray.forEach((report, index) => {
    const rowId = `reportRow${report.id}`;
    const color = statusColor(capStatus(report.status || 'Unknown'));

    const row = document.createElement('tr');
    row.id = rowId;
    row.className = 'border-b';
    row.classList.toggle('bg-yellow-100', !!(highlightId && report.id === highlightId));
    row.setAttribute('data-report', JSON.stringify(report));
    row.setAttribute('data-type', reportType);

    const cells = reportType === 'fireReports'
      ? `
        <td class="px-4 py-2">${index + 1}</td>
        <td class="px-4 py-2">${report.type || 'N/A'}</td>
        <td class="px-4 py-2">${report.exactLocation || 'N/A'}</td>
        <td class="px-4 py-2">${report.date || 'N/A'} ${to24h(report.reportTime) || report.reportTime || 'N/A'}</td>
        <td class="px-4 py-2 status text-${color}-500">${capStatus(report.status || 'Unknown')}</td>
        <td class="px-4 py-2 space-x-2 flex items-center">
          ${Number.isFinite(report.latitude) && Number.isFinite(report.longitude) ? `
            <a href="javascript:void(0);" onclick="openLocationModal(${report.latitude}, ${report.longitude})">
              <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
            </a>` : ''}
          <a href="javascript:void(0);" onclick="openDetailsModal('${report.id}', 'fireReports')">
            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
          </a>
          <a href="javascript:void(0);" onclick="openMessageModal('${report.id}', 'fireReports')">
            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
          </a>
        </td>`
      : `
        <td class="px-4 py-2">${index + 1}</td>
        <td class="px-4 py-2">${report.exactLocation || 'N/A'}</td>
        <td class="px-4 py-2">${report.emergencyType || 'N/A'}</td>
        <td class="px-4 py-2">${report.date || 'N/A'} ${to24h(report.reportTime) || report.reportTime || 'N/A'}</td>
        <td class="px-4 py-2 status text-${color}-500">${capStatus(report.status || 'Unknown')}</td>
        <td class="px-4 py-2 space-x-2 flex items-center">
          ${Number.isFinite(report.latitude) && Number.isFinite(report.longitude) ? `
            <a href="javascript:void(0);" onclick="openLocationModal(${report.latitude}, ${report.longitude})">
              <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
            </a>` : ''}
          <a href="javascript:void(0);" onclick="openDetailsModal('${report.id}', 'otherEmergency')">
            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
          </a>
          <a href="javascript:void(0);" onclick="openMessageModal('${report.id}', 'otherEmergency')">
            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
          </a>
        </td>`;

    row.innerHTML = cells;
    fragment.appendChild(row);
  });

  tableBody.innerHTML = '';
  tableBody.appendChild(fragment);
  tableBody.style.visibility = 'visible';
   ensureMessageBadges();
}


/* =========================================================
 * ALL REPORTS (MERGE + RENDER)
 * ========================================================= */

function parseDT(d, t, fallbackTs, preferMDY = false) {
  // t → 24h
  const time24 = to24h(t || '') || (t || '00:00');

  let best = NaN;
  if (d && d.includes('/')) {
    const [p1, p2, p3] = d.split('/');
    const yyyy = (p3 && p3.length === 2) ? `20${p3}` : (p3 || '');
    const A = String(p1 || '').padStart(2, '0'); // could be day or month
    const B = String(p2 || '').padStart(2, '0'); // could be month or day
    // MDY:  MM/DD/YYYY
    const mdy = Date.parse(`${yyyy}-${A}-${B} ${time24}`);
    // DMY:  DD/MM/YYYY
    const dmy = Date.parse(`${yyyy}-${B}-${A} ${time24}`);

    // choose by preference, but fall back to whichever is valid
    best = preferMDY
      ? (!isNaN(mdy) ? mdy : dmy)
      : (!isNaN(dmy) ? dmy : mdy);
  }

  if (isNaN(best)) {
    const fb = (typeof fallbackTs === 'number') ? fallbackTs : 0;
    return fb;
  }
  return best;
}


function asArray(v) { return Array.isArray(v) ? v : (v ? Object.values(v) : []); }

function firstFinite(...vals) {
  for (const v of vals) {
    const n = Number(v);
    if (Number.isFinite(n)) return n;
  }
  return NaN;
}


function buildAllReports() {
 const fire = asArray(fireReports).map(r => ({
  id: r.id, type: 'fireReports',
  location: r.exactLocation || r.location || 'N/A',
  date: r.date || '', time: r.reportTime || '',
  status: r.status || 'Unknown',
  lat: r.latitude, lng: r.longitude,
  // 1) prefer numeric timestamp; 2) else parse strings (DMY for legacy fire/other/EMS)
  sortTs: (() => {
    const num = firstFinite(r.timestamp, r.createdAt, r.updatedAt);
    return Number.isFinite(num) ? num : parseDT(r.date, r.reportTime, 0, /*preferMDY=*/false);
  })()
}));

const other = asArray(otherEmergencyReports).map(r => ({
  id: r.id, type: 'otherEmergency',
  location: r.exactLocation || r.location || 'N/A',
  date: r.date || '', time: r.reportTime || '',
  status: r.status || 'Unknown',
  lat: r.latitude, lng: r.longitude,
  sortTs: (() => {
    const num = firstFinite(r.timestamp, r.createdAt, r.updatedAt);
    return Number.isFinite(num) ? num : parseDT(r.date, r.reportTime, 0, /*preferMDY=*/false);
  })()
}));

const ems = asArray(emsReports).map(r => ({
  id: r.id, type: 'emsReports',
  location: r.exactLocation || r.location || 'N/A',
  date: r.date || '', time: r.reportTime || '',
  status: r.status || 'Unknown',
  lat: r.latitude, lng: r.longitude,
  sortTs: (() => {
    const num = firstFinite(r.timestamp, r.createdAt, r.updatedAt);
    return Number.isFinite(num) ? num : parseDT(r.date, r.reportTime, 0, /*preferMDY=*/false);
  })()
}));

const sms = asArray(smsReports).map(r => ({
  id: r.id, type: 'smsReports',
  location: r.location || 'N/A',
  date: r.date || '', time: r.time || '',
  status: r.status || 'N/A',
  lat: r.latitude, lng: r.longitude,
  // SMS from Android has millisecond 'timestamp' — use it; else parse as MDY
  sortTs: (() => {
    const num = firstFinite(r.timestamp, r.createdAt, r.updatedAt);
    return Number.isFinite(num) ? num : parseDT(r.date, r.time, 0, /*preferMDY=*/true);
  })()
}));

return [...fire, ...other, ...ems, ...sms].sort((a, b) => b.sortTs - a.sortTs);


}

function statusColor(s) {
  return s === 'Ongoing'   ? 'red'
       : s === 'Completed' ? 'green'
       : s === 'Pending'   ? 'orange'
       : s === 'Received'  ? 'blue'
       : 'yellow';
}

function safeRenderAllReports() { try { renderAllReports(); } catch (e) { console.error('renderAllReports failed:', e); } }

function safeInitRealtime() {
  try {
    if (window.firebase && firebase.apps && firebase.apps.length && typeof firebase.database === 'function') {
      initializeRealTimeListener();
    } else {
      setTimeout(safeInitRealtime, 500);
    }
  } catch (e) {
    console.error('initializeRealTimeListener error:', e);
  }
}

function renderAllReports() {
  const body = document.getElementById('allReportsBody');
  if (!body) return;

  const rows = buildAllReports();
  body.innerHTML = rows.map((r, i) => {
    const time24  = to24h(r.time) || r.time || 'N/A';
    const dateStr = r.date || 'N/A';
    const locBtn  = (r.lat != null && r.lng != null)
      ? `<a href="javascript:void(0);" onclick="openLocationModal(${r.lat}, ${r.lng})"><img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6"></a>`
      : '';

    const statusDisp = capStatus(r.status);
    const colorClass = (r.type === 'smsReports' && statusDisp === 'Pending')
      ? 'text-black'
      : `text-${statusColor(statusDisp)}-500`;

    // NOTE: All Reports columns are (#, Type, Location, DateTime, Status, Action)
    const typeLabel =
      r.type === 'fireReports' ? 'Fire' :
      r.type === 'otherEmergency' ? 'Other Emergency' :
      r.type === 'emsReports' ? 'EMS' :
      'SMS';

    return `<tr class="border-b" data-merged="1" data-type="${r.type}" data-id="${r.id}">
      <td class="px-4 py-2">${i + 1}</td>
      <td class="px-4 py-2">${typeLabel}</td>
      <td class="px-4 py-2">${r.location}</td>
      <td class="px-4 py-2">${dateStr} ${time24}</td>
      <td class="px-4 py-2 status ${colorClass}">${statusDisp}</td>
        <td class="px-4 py-2 space-x-2 flex items-center">
        <a href="javascript:void(0);" onclick="openMessageModal('${r.id}','${r.type}')">
            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
        </a>
        ${locBtn}
        <a href="javascript:void(0);" onclick="openDetailsModal('${r.id}','${r.type}')">
            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
        </a>
        </td>

    </tr>`;
  }).join('');

  if (typeof filterAllReportsTable === 'function') filterAllReportsTable();
   ensureMessageBadges();
}


/* =========================================================
 * FILTERS (FIRE / OTHER / ALL / SMS / EMS)
 * ========================================================= */

function filterFireReportTable() {
  const statusFilter   = document.getElementById('fireStatusFilter').value.toLowerCase();
  const locationSearch = document.getElementById('fireLocationSearch').value.toLowerCase();
  const typeSearch     = document.getElementById('fireTypeSearch').value.toLowerCase();

  const mode       = document.getElementById('fireDateTimeFilter').value;
  const dateSearch = mode === 'date' ? document.getElementById('fireDateSearch').value : '';
  const timeSearch = mode === 'time' ? document.getElementById('fireTimeSearch').value : '';

  const rows = document.querySelectorAll('#fireReportsBody tr');

  rows.forEach(row => {
    const report = JSON.parse(row.getAttribute('data-report'));

    const matchesStatus  = !statusFilter || (report.status && report.status.toLowerCase() === statusFilter);
    const matchesType    = !typeSearch   || ((report.type || '').toLowerCase().includes(typeSearch));
    const matchesLocation= !locationSearch || ((report.exactLocation || '').toLowerCase().includes(locationSearch));

    const reportDateISO  = dateToISO(report.date || '');
    const matchesDate    = !dateSearch || (reportDateISO === dateSearch);

    const matchesTime = (() => {
      if (!timeSearch) return true;
      const t = to24h(report.reportTime || '');
      return !!t && t === timeSearch;
    })();

    row.style.display = (matchesStatus && matchesType && matchesLocation && matchesDate && matchesTime) ? '' : 'none';
  });
}

function handleDateTimeFilterChange() {
  const mode = document.getElementById('fireDateTimeFilter').value;
  const d = document.getElementById('fireDateSearch');
  const t = document.getElementById('fireTimeSearch');

  if (mode === 'date') { d.classList.remove('hidden'); t.classList.add('hidden'); }
  else if (mode === 'time') { t.classList.remove('hidden'); d.classList.add('hidden'); }
  else { d.classList.add('hidden'); t.classList.add('hidden'); d.value = ''; t.value = ''; }
  filterFireReportTable();
}

function handleOtherDateTimeFilterChange() {
  const mode = document.getElementById('otherDateTimeFilter').value;
  const d = document.getElementById('otherDateSearch');
  const t = document.getElementById('otherTimeSearch');

  if (mode === 'date') { d.classList.remove('hidden'); t.classList.add('hidden'); }
  else if (mode === 'time') { t.classList.remove('hidden'); d.classList.add('hidden'); }
  else { d.classList.add('hidden'); t.classList.add('hidden'); d.value = ''; t.value = ''; }
  filterOtherEmergencyTable();
}

function filterOtherEmergencyTable() {
  const typeFilter     = document.getElementById('emergencyTypeFilter').value.toLowerCase();
  const statusFilter   = document.getElementById('otherStatusFilter').value.toLowerCase();
  const locationSearch = document.getElementById('otherLocationSearch').value.toLowerCase();

  const mode       = document.getElementById('otherDateTimeFilter').value;
  const dateSearch = mode === 'date' ? document.getElementById('otherDateSearch').value : '';
  const timeSearch = mode === 'time' ? document.getElementById('otherTimeSearch').value : '';

  const rows = document.querySelectorAll('#otherEmergencyTableBody tr');

  rows.forEach(row => {
    const report = JSON.parse(row.getAttribute('data-report'));
    const matchesType    = !typeFilter  || ((report.emergencyType || '').toLowerCase() === typeFilter);
    const matchesStatus  = !statusFilter|| (report.status && report.status.toLowerCase() === statusFilter);
    const matchesLocation= !locationSearch || ((report.exactLocation || '').toLowerCase().includes(locationSearch));

    const reportDateISO = (() => {
      if (!report.date) return '';
      const parts = report.date.split('/');
      if (parts.length === 3) return `${parts[2].length === 2 ? '20' + parts[2] : parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
      return '';
    })();

    const matchesDate = !dateSearch || (reportDateISO === dateSearch);

    const matchesTime = (() => {
      if (!timeSearch) return true;
      const t = to24h(report.reportTime || '');
      return !!t && t === timeSearch;
    })();

    row.style.display = (matchesType && matchesStatus && matchesLocation && matchesDate && matchesTime) ? '' : 'none';
  });
}

function handleAllDateTimeFilterChange() {
  const mode = document.getElementById('allDateTimeFilter').value;
  const d = document.getElementById('allDateSearch');
  const t = document.getElementById('allTimeSearch');

  if (mode === 'date') { d.classList.remove('hidden'); t.classList.add('hidden'); t.value = ''; }
  else if (mode === 'time') { t.classList.remove('hidden'); d.classList.add('hidden'); d.value = ''; }
  else { d.classList.add('hidden'); t.classList.add('hidden'); d.value = ''; t.value = ''; }

  filterAllReportsTable();
}

function filterAllReportsTable() {
  const typeQ   = (document.getElementById('allTypeSearch')?.value || '').toLowerCase();
  const locQ    = (document.getElementById('allLocationSearch')?.value || '').toLowerCase();
  const statusQ = (document.getElementById('allStatusFilter')?.value || '').toLowerCase();
  const mode    = document.getElementById('allDateTimeFilter')?.value || 'all';
  const dateQ   = mode === 'date' ? (document.getElementById('allDateSearch')?.value || '') : '';
  const timeQ   = mode === 'time' ? (document.getElementById('allTimeSearch')?.value || '') : '';

  const rows = document.querySelectorAll('#allReportsBody tr');
  rows.forEach(row => {
    const tds    = row.querySelectorAll('td');
    const typeTxt= (tds[1]?.textContent || '').toLowerCase();
    const loc    = (tds[2]?.textContent || '').toLowerCase();
    const dtText = (tds[3]?.textContent || '').trim();
    const status = (tds[4]?.textContent || '').toLowerCase();

    let okDate = true;
    if (dateQ) {
      const dmy = (dtText.split(' ')[0] || '');
      const iso = dateToISO(dmy);
      okDate = (iso === dateQ);
    }

    let okTime = true;
    if (timeQ) {
      const rawTime = (dtText.split(' ')[1] || '');
      const norm = to24h(rawTime) || rawTime;
      okTime = (norm === timeQ);
    }

    const okType   = !typeQ   || typeTxt.includes(typeQ);
    const okLoc    = !locQ    || loc.includes(locQ);
    const okStatus = !statusQ || status === statusQ;

    row.style.display = (okType && okLoc && okStatus && okDate && okTime) ? '' : 'none';
  });
}

function handleSmsDateTimeFilterChange() {
  const mode = document.getElementById('smsDateTimeFilter').value;
  const d = document.getElementById('smsDateSearch');
  const t = document.getElementById('smsTimeSearch');

  if (mode === 'date') { d.classList.remove('hidden'); t.classList.add('hidden'); t.value = ''; }
  else if (mode === 'time') { t.classList.remove('hidden'); d.classList.add('hidden'); d.value = ''; }
  else { d.classList.add('hidden'); t.classList.add('hidden'); d.value = ''; t.value = ''; }
  filterSmsReportsTable();
}

function filterSmsReportsTable() {
  const qLoc  = (document.getElementById('smsLocationSearch')?.value || '').toLowerCase();
  const mode  = document.getElementById('smsDateTimeFilter')?.value || 'all';
  const dateQ = mode === 'date' ? (document.getElementById('smsDateSearch')?.value || '') : '';
  const timeQ = mode === 'time' ? (document.getElementById('smsTimeSearch')?.value || '') : '';
  const statQ = (document.getElementById('smsStatusFilter')?.value || '').toLowerCase();

  const rows = document.querySelectorAll('#smsReportsBody tr');
  rows.forEach(row => {
    const tds    = row.querySelectorAll('td');
    const loc    = (tds[1]?.textContent || '').toLowerCase();
    const dtText = (tds[2]?.textContent || '').trim().replace(/\s+/g, ' ').trim();
    const status = (tds[3]?.textContent || '').toLowerCase();

    let okDate = true;
    if (dateQ) {
      const d = (dtText.split(' ')[0] || '');
      okDate = (d === dateQ);
    }

    let okTime = true;
    if (timeQ) {
      const rawTime = (dtText.split(' ')[1] || '');
      okTime = rawTime.startsWith(timeQ);
    }

    const okLoc    = !qLoc || loc.includes(qLoc);
    const okStatus = !statQ || status === statQ;

    row.style.display = (okLoc && okDate && okTime && okStatus) ? '' : 'none';
  });
}


/* =========================================================
 * UX HELPERS (FOCUS PICKERS, NORMALIZE INITIAL)
 * ========================================================= */

function focusFireDatePicker() {
  const sel = document.getElementById('fireDateTimeFilter');
  if (sel) sel.value = 'date';
  handleDateTimeFilterChange();
  const input = document.getElementById('fireDateSearch');
  if (!input) return;
  input.classList.remove('hidden');
  if (typeof input.showPicker === 'function') input.showPicker();
  input.focus();
}

function focusOtherDatePicker() {
  const sel = document.getElementById('otherDateTimeFilter');
  if (sel) sel.value = 'date';
  handleOtherDateTimeFilterChange();
  const input = document.getElementById('otherDateSearch');
  if (!input) return;
  input.classList.remove('hidden');
  if (typeof input.showPicker === 'function') input.showPicker();
  input.focus();
}

function focusEmsDatePicker() {
  const sel = document.getElementById('emsDateTimeFilter');
  if (!sel) return;
  sel.value = 'date';
  handleEmsDateTimeFilterChange();
  const input = document.getElementById('emsDateSearch');
  if (!input) return;
  input.classList.remove('hidden');
  if (typeof input.showPicker === 'function') input.showPicker();
  input.focus();
}

function handleEmsDateTimeFilterChange() {
  const mode = document.getElementById('emsDateTimeFilter').value;
  const d = document.getElementById('emsDateSearch');
  const t = document.getElementById('emsTimeSearch');

  if (mode === 'date') { d.classList.remove('hidden'); t.classList.add('hidden'); }
  else if (mode === 'time') { t.classList.remove('hidden'); d.classList.add('hidden'); }
  else { d.classList.add('hidden'); t.classList.add('hidden'); d.value = ''; t.value = ''; }
  filterEmsTable();
}

function filterEmsTable() {
  const typeQ     = (document.getElementById('emsTypeSearch')?.value || '').toLowerCase();
  const locQ      = (document.getElementById('emsLocationSearch')?.value || '').toLowerCase();
  const statusQ   = (document.getElementById('emsStatusFilter')?.value || '').toLowerCase();
  const mode      = document.getElementById('emsDateTimeFilter')?.value || 'all';
  const dateQ     = mode === 'date' ? (document.getElementById('emsDateSearch')?.value || '') : '';
  const timeQ     = mode === 'time' ? (document.getElementById('emsTimeSearch')?.value || '') : '';

  const rows = document.querySelectorAll('#emsBody tr');
  rows.forEach(row => {
    const rpt = JSON.parse(row.getAttribute('data-report') || '{}');
    const okType   = !typeQ   || ((rpt.type || '').toLowerCase().includes(typeQ));
    const okLoc    = !locQ    || ((rpt.exactLocation || '').toLowerCase().includes(locQ));
    const okStatus = !statusQ || ((rpt.status || '').toLowerCase() === statusQ);

    let okDate = true;
    if (dateQ) {
      const iso = dateToISO(rpt.date || '');
      okDate = (iso === dateQ);
    }

    let okTime = true;
    if (timeQ) {
      const norm = to24h(rpt.reportTime || '') || '';
      okTime = norm === timeQ;
    }

    row.style.display = (okType && okLoc && okStatus && okDate && okTime) ? '' : 'none';
  });
}

function normalizeInitialTimes() {
  // Fire rows
  document.querySelectorAll('#fireReportsBody tr').forEach(row => {
    const report = JSON.parse(row.getAttribute('data-report') || '{}');
    if (report.reportTime) {
      const d = report.date || 'N/A';
      const t = to24h(report.reportTime) || 'N/A';
      row.children[3].textContent = `${d} ${t}`;
    }
  });

  // Other Emergency rows
  document.querySelectorAll('#otherEmergencyTableBody tr').forEach(row => {
    const report = JSON.parse(row.getAttribute('data-report') || '{}');
    if (report.reportTime) {
      const d = report.date || 'N/A';
      const t = to24h(report.reportTime) || 'N/A';
      row.children[3].textContent = `${d} ${t}`;
    }
  });

  // EMS rows
  renderEmsTable();
}


/* =========================================================
 * VISIBILITY / SECTION TOGGLE
 * ========================================================= */

function toggleIncidentTables() {
  const v = document.getElementById('incidentType').value;

  // hide all
  document.getElementById('allReportsSection').classList.add('hidden');
  document.getElementById('fireReportsSection').classList.add('hidden');
  document.getElementById('otherEmergencySection').classList.add('hidden');
  document.getElementById('emsSection').classList.add('hidden');
  document.getElementById('smsReportsSection').classList.add('hidden');
   document.getElementById('fireFighterChatSection').classList.add('hidden');

  // show selected
  if (v === 'allReports') {
    document.getElementById('allReportsSection').classList.remove('hidden');
    renderAllReports();
  } else if (v === 'fireReports') {
    document.getElementById('fireReportsSection').classList.remove('hidden');
  } else if (v === 'otherEmergency') {
    document.getElementById('otherEmergencySection').classList.remove('hidden');
  } else if (v === 'emsReports') {
    document.getElementById('emsSection').classList.remove('hidden');
    renderEmsTable();
  } else if (v === 'smsReports') {
    document.getElementById('smsReportsSection').classList.remove('hidden');
  } else if (v === 'fireFighterChatReports') {
    document.getElementById('fireFighterChatSection').classList.remove('hidden');
      loadAllFireFighterAccounts();
  // <- ensure paint when user opens the tab
  }
}


/* =========================================================
 * DETAILS MODAL (OPEN/CLOSE + STATUS BUTTONS)
 * ========================================================= */

function openDetailsModal(incidentId, reportType) {
  // resolve the full report object
  let full = null;
  if (reportType === 'fireReports')        full = (fireReports || []).find(r => r.id === incidentId);
  else if (reportType === 'otherEmergency') full = (otherEmergencyReports || []).find(r => r.id === incidentId);
  else if (reportType === 'emsReports')     full = (emsReports || []).find(r => r.id === incidentId);
  else if (reportType === 'smsReports')     full = (smsReports || []).find(r => r.id === incidentId);

  if (!full) {
    const rowFromSection =
      document.getElementById(`reportRow${incidentId}`) ||
      document.querySelector(`#allReportsBody tr[data-id="${incidentId}"][data-type="${reportType}"]`);
    if (!rowFromSection) return;
    try { full = JSON.parse(rowFromSection.getAttribute('data-report') || '{}'); }
    catch { full = {}; }
    full.id = full.id || incidentId;
  }

  // helpers
  const pick = (...ks) => {
    for (const k of ks) if (full[k] != null && String(full[k]).trim() !== '') return full[k];
    return 'N/A';
  };
  const t24 = (v) => to24h(v) || v || 'N/A';

  // hide all detail panels first
  document.getElementById('fireReportDetails').classList.add('hidden');
  document.getElementById('otherEmergencyDetails').classList.add('hidden');
  document.getElementById('emsDetails').classList.add('hidden');
  document.getElementById('smsDetails').classList.add('hidden');
  document.getElementById('smsExtra').classList.add('hidden');

  // ----- FIRE -----
  if (reportType === 'fireReports') {
    document.getElementById('detailIncidentId').innerText = full.id || 'N/A';
    document.getElementById('detailName').innerText       = pick('name','reporterName','userName');
    document.getElementById('detailContact').innerText    = pick('contact','phone','phoneNumber','mobile');
    document.getElementById('detailFireType').innerText   = pick('type');
    document.getElementById('detailLocation').innerText   = pick('exactLocation','location','address');
    document.getElementById('detailDate').innerText       = pick('date');
    document.getElementById('detailReportTime').innerText = t24(pick('reportTime'));
    document.getElementById('detailStatus').innerText     = capStatus(pick('status'));

    const mapEl = document.getElementById('detailFireMapLink');
    const murl  = pick('mapLink','location');
    mapEl.innerHTML = (murl && murl !== 'N/A') ? `<a href="${murl}" target="_blank" rel="noopener">Open in Maps</a>` : '';

    const b64 = (full.photoBase64 || '').toString().trim();
    document.getElementById('detailFirePhoto').innerHTML = b64
      ? `<img class="mt-2 rounded max-w-full" src="data:image/jpeg;base64,${b64}" alt="Photo">` : '';

    document.getElementById('fireReportDetails').classList.remove('hidden');
  }

  // ----- OTHER EMERGENCY -----
  else if (reportType === 'otherEmergency') {
    document.getElementById('detailIncidentIdOther').innerText = full.id || 'N/A';
    document.getElementById('detailNameOther').innerText       = pick('name','reporterName','userName');
    document.getElementById('detailContactOther').innerText    = pick('contact','phone','phoneNumber','mobile');
    document.getElementById('detailEmergencyType').innerText   = pick('emergencyType','type');
    document.getElementById('detailLocationOther').innerText   = pick('exactLocation','location','address');
    document.getElementById('detailDateOther').innerText       = pick('date');
    document.getElementById('detailReportTimeOther').innerText = t24(pick('reportTime'));
    document.getElementById('detailStatusOther').innerText     = capStatus(pick('status'));

    const mapEl = document.getElementById('detailOtherMapLink');
    const murl  = pick('location','mapLink');
    mapEl.innerHTML = (murl && murl !== 'N/A') ? `<a href="${murl}" target="_blank" rel="noopener">Open in Maps</a>` : '';

    const b64 = (full.photoBase64 || '').toString().trim();
    document.getElementById('detailOtherPhoto').innerHTML = b64
      ? `<img class="mt-2 rounded max-w-full" src="data:image/jpeg;base64,${b64}" alt="Incident Photo">` : '';

    document.getElementById('otherEmergencyDetails').classList.remove('hidden');
  }

  // ----- EMS -----
  else if (reportType === 'emsReports') {
    document.getElementById('detailIncidentIdEms').innerText = full.id || 'N/A';
    document.getElementById('detailNameEms').innerText       = pick('name','reporterName','userName');
    document.getElementById('detailContactEms').innerText    = pick('contact','phone','phoneNumber','mobile');
    document.getElementById('detailTypeEms').innerText       = pick('type');
    document.getElementById('detailLocationEms').innerText   = pick('exactLocation','location','address');
    document.getElementById('detailDateEms').innerText       = pick('date');
    document.getElementById('detailReportTimeEms').innerText = t24(pick('reportTime'));
    document.getElementById('detailStatusEms').innerText     = capStatus(pick('status'));

    const mapEl = document.getElementById('detailEmsMapLink');
    const murl  = pick('location','mapLink');
    mapEl.innerHTML = (murl && murl !== 'N/A') ? `<a href="${murl}" target="_blank" rel="noopener">Open in Maps</a>` : '';

    const b64 = (full.photoBase64 || '').toString().trim();
    document.getElementById('detailEmsPhoto').innerHTML = b64
      ? `<img class="mt-2 rounded max-w-full" src="data:image/jpeg;base64,${b64}" alt="EMS Photo">` : '';

    document.getElementById('emsDetails').classList.remove('hidden');
  }

  // ----- SMS -----
else if (reportType === 'smsReports') {
  // Basic info
  document.getElementById('detailIncidentIdSms').innerText = full.id || 'N/A';
  document.getElementById('detailNameSms').innerText       = pick('name','reporterName','userName');
  document.getElementById('detailContactSms').innerText    = pick('contact','phone','phoneNumber','mobile');

  // Report text
  document.getElementById('detailSmsReportText').innerText =
    pick('fireReport','message','reportText','details','description');

  // Date / Time / Status
  document.getElementById('detailDateSms').innerText       = pick('date'); // e.g., 10/13/2025
  document.getElementById('detailReportTimeSms').innerText = t24(pick('time','reportTime'));
  document.getElementById('detailStatusSms').innerText     = capStatus(pick('status'));

  // Location
  const loc = pick('location','exactLocation','address');
  const locEl = document.getElementById('detailLocationSms');
  locEl.innerText = loc || 'N/A';
  locEl.title = loc && loc !== 'N/A' ? String(loc) : '';

  // Distance (nearestStationDistanceMeters)
  (function () {
    const raw  = pick('nearestStationDistanceMeters');
    const m    = Number(raw);
    let pretty = 'N/A';
    if (Number.isFinite(m) && m >= 0) {
      pretty = m < 1000 ? `${Math.round(m)} m` : `${(m / 1000).toFixed(2)} km`;
    }
    document.getElementById('detailNearestDistanceSms').innerText = pretty;
  })();

  // Show the details panel
  document.getElementById('smsDetails').classList.remove('hidden');
}


  // ----- Status action button (works for ALL types, including SMS) -----
  // ----- Status action button (works for ALL types, including SMS) -----
const statusActionDiv = document.getElementById('statusActionButtons');
statusActionDiv.innerHTML = '';

const curStatus = capStatus(full.status || 'Pending');

if (curStatus === 'Pending') {
  // RECEIVE → open assignment modal
  const btn = document.createElement('button');
  btn.id = `acceptButton${full.id}`;
  btn.className = 'px-4 py-2 rounded mt-2 text-white';
  btn.style.backgroundColor = '#F3C011';
  btn.onmouseenter = () => (btn.style.backgroundColor = '#d1a500');
  btn.onmouseleave = () => (btn.style.backgroundColor = '#F3C011');
  btn.textContent = 'Receive';
  btn.onclick = () => openAssignModal(full.id, reportType);
  statusActionDiv.appendChild(btn);

} else if (curStatus === 'Ongoing') {
  // DONE → mark Completed
  const btn = document.createElement('button');
  btn.id = `acceptButton${full.id}`;
  btn.className = 'px-4 py-2 rounded mt-2 text-white';
  btn.style.backgroundColor = '#22c55e';
  btn.onmouseenter = () => (btn.style.backgroundColor = '#16a34a');
  btn.onmouseleave = () => (btn.style.backgroundColor = '#22c55e');
  btn.textContent = 'Done';
  btn.onclick = () => updateReportStatus(full.id, reportType, 'Completed');
  statusActionDiv.appendChild(btn);


// Note: Received & Completed → no button


  statusActionDiv.appendChild(btn);
}


  // finally, show the modal
  document.getElementById('detailsModal').classList.remove('hidden');
}

/* =========================================================
 * ASSIGN-ON-RECEIVE: MODAL + WRITE + STATUS FLIP
 * ========================================================= */

let __assignContext = { incidentId: null, reportType: null, reportObject: null };

function typeNodeFor(reportType) {
  return reportType === 'fireReports'      ? 'FireReport'
       : reportType === 'otherEmergency'   ? 'OtherEmergencyReport'
       : reportType === 'emsReports'       ? 'EmergencyMedicalServicesReport'
       : reportType === 'smsReports'       ? 'SmsReport'
       : 'Unknown';
}

function openAssignModal(incidentId, reportType) {
  // pull the freshest row payload to copy over
  const row = document.getElementById(`reportRow${incidentId}`) ||
              document.querySelector(`#allReportsBody tr[data-id="${incidentId}"][data-type="${reportType}"]`);
  let rpt = {};
  try { rpt = JSON.parse(row?.getAttribute('data-report') || '{}'); } catch {}
  rpt.id = rpt.id || incidentId;

  __assignContext = { incidentId, reportType, reportObject: rpt };

  // reset radios & show
  const form = document.getElementById('assignForm');
  if (form) form.reset();
  const modal = document.getElementById('assignModal');
  modal?.classList.remove('hidden');

}

function closeAssignModal() {
  document.getElementById('assignModal')?.classList.add('hidden');
  __assignContext = { incidentId: null, reportType: null, reportObject: null };
}

// submit handler (attach once)
(() => {
  const form = document.getElementById('assignForm');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const stationAccountKey = formData.get('station');
    const { incidentId, reportType, reportObject } = __assignContext || {};
    if (!stationAccountKey || !incidentId || !reportType) return;

    const typeNode = typeNodeFor(reportType);
    const base = 'TagumCityCentralFireStation/FireFighter/AllFireFighterAccount';
    const destRef = firebase.database().ref(
      `${base}/${stationAccountKey}/AllReport/${typeNode}/${incidentId}`
    );

    const payload = {
      ...reportObject,
      assignedStationAccount: stationAccountKey,
      assignedAt: Date.now(),
      status: 'Ongoing'           // <-- your desired status
    };

    let assignedOK = false;

    // --- NETWORK ONLY ---
    try {
      // 1) write to assignee
      await destRef.set(payload);

      // 2) flip original collection
      const n = nodes();
      const srcPath =
        reportType === 'fireReports'    ? n.fireReport :
        reportType === 'otherEmergency' ? n.otherEmergency :
        reportType === 'emsReports'     ? n.ems :
        reportType === 'smsReports'     ? n.sms : null;

      if (srcPath) {
        await firebase.database().ref(`${srcPath}/${incidentId}`).update({ status: 'Ongoing' });
      }

      assignedOK = true;
    } catch (err) {
      console.error('[assign] network error:', err);
      alert('Failed to assign. Check console / network.');
      return; // stop here; don’t run UI updates on failure
    }

    // --- UI (non-fatal) ---
    if (assignedOK) {
      try {
        // If statusColor might be undefined, guard it:
        const safeStatusColor = typeof statusColor === 'function'
          ? statusColor
          : () => 'blue';

        // If your setStatusEverywhere uses statusColor inside,
        // it won’t crash now; or call with just the string:
        setStatusEverywhere?.(incidentId, reportType, 'Ongoing');

        showToast?.('Report assigned and marked as Ongoing');
      } catch (uiErr) {
        console.warn('[assign] UI update warning:', uiErr);
        // No alert — the assignment already succeeded
      } finally {
        // Close both modals robustly
        queueMicrotask(() => {
          closeAssignModal();
          closeDetailsModal();
        });
      }
    }
  }, { once: true });
})();



function setStatusEverywhere(incidentId, reportType, newStatus) {
  const arr =
    reportType === 'fireReports'    ? fireReports :
    reportType === 'otherEmergency' ? otherEmergencyReports :
    reportType === 'emsReports'     ? emsReports :
    reportType === 'smsReports'     ? smsReports : null;

  const disp = capStatus(newStatus);      // normalize label
  const col  = statusColor(disp);         // pick the right Tailwind color

  // update cached array
  if (arr) {
    const i = arr.findIndex(r => r.id === incidentId);
    if (i !== -1) arr[i] = { ...arr[i], status: disp };
  }

  // update the per-type row
  const typeRow = document.getElementById(`reportRow${incidentId}`);
  if (typeRow) {
    const rpt = JSON.parse(typeRow.getAttribute('data-report') || '{}');
    rpt.status = disp;
    typeRow.setAttribute('data-report', JSON.stringify(rpt));
    const st = typeRow.querySelector('.status');
    if (st) {
      st.className = `px-4 py-2 status text-${col}-500`;
      st.textContent = disp;
    }
  }

  // update the merged All Reports row
  const allRow = document.querySelector(
    `#allReportsBody tr[data-id="${incidentId}"][data-type="${reportType}"]`
  );
  if (allRow) {
    const st = allRow.querySelector('.status');
    if (st) {
      st.className = `px-4 py-2 status text-${col}-500`;
      st.textContent = disp;
    }
  }

  renderAllReports?.();
}



async function updateReportStatus(incidentId, reportType, newStatus) {
  const n = nodes();
  if (!n) return;

  const row = document.getElementById(`reportRow${incidentId}`);
  if (!row) return;

  const report = JSON.parse(row.getAttribute('data-report')) || {};
  report.status = newStatus;
  row.setAttribute('data-report', JSON.stringify(report));

  const statusCell = row.querySelector('.status');
  if (statusCell) {
    statusCell.innerText = newStatus;
    statusCell.className = `px-4 py-2 status text-${statusColor(newStatus)}-500`;
  }

let path = null;
if (reportType === 'fireReports')      path = nodes().fireReport;
else if (reportType === 'otherEmergency') path = nodes().otherEmergency;
else if (reportType === 'emsReports')     path = nodes().ems;
else if (reportType === 'smsReports')     path = nodes().sms; // canonical

if (!path) return;

firebase.database().ref(`${path}/${incidentId}`).update({ status: newStatus })
  .then(() => {
    updateTableStatus?.(incidentId, newStatus);
    closeDetailsModal();
    showToast?.(`Status updated to ${newStatus}`);
  })
  .catch(console.error);


  closeDetailsModal();
}

function capStatus(s) {
  if (!s) return 'Unknown';
  const t = String(s).toLowerCase();
  return t === 'pending' ? 'Pending'
       : t === 'ongoing' ? 'Ongoing'
       : t === 'completed' ? 'Completed'
       : t === 'received' ? 'Received'
       : s;
}

function closeDetailsModal() {
  document.getElementById('detailsModal').classList.add('hidden');
}


/* =========================================================
 * MESSAGING (THREADS / RESPONSES / LIVE)
 * ========================================================= */

function stationNodesForReportType(reportType) {
  const STATION_ROOT = 'TagumCityCentralFireStation';
  const ALL = `${STATION_ROOT}/AllReport`;

  const serviceReportType =
    reportType === 'fireReports' ? 'fire' :
    reportType === 'emsReports'  ? 'emergencyMedicalServices' :
    reportType === 'smsReports'  ? 'sms' :
    'otherEmergency';

  // we keep these for form payload compatibility, but UI will no longer use repliesBase directly
  const collectionBase =
    reportType === 'fireReports'      ? `${ALL}/FireReport` :
    reportType === 'emsReports'       ? `${ALL}/EmergencyMedicalServicesReport` :
    reportType === 'smsReports'       ? `${ALL}/SmsReport` :
                                        `${ALL}/OtherEmergencyReport`;

  return {
    repliesBase: collectionBase,         // not used for reading replies anymore
    serviceReportType,                   // payload to backend stays the same
    stationBase: ALL,                    // AllReport root
    prefix: 'TagumCityCentral',          // unchanged in payload
  };
}

function repliesRef(incidentId, _reportType) {
  const ALL = 'TagumCityCentralFireStation/AllReport';
  // Central list of user replies; filter by incidentId
  return firebase.database()
    .ref(`${ALL}/ReplyMessage`)
    .orderByChild('incidentId')
    .equalTo(incidentId);
}

function stationResponsesQuery(incidentId, _reportType) {
  const ALL = 'TagumCityCentralFireStation/AllReport';
  return firebase.database()
    .ref(`${ALL}/ResponseMessage`)
    .orderByChild('incidentId')
    .equalTo(incidentId);
}

function getFireStationNameByEmail(_email, callback) {
  try { return callback("Tagum City Central Fire Station"); }
  catch (_) { return callback("Tagum City Central Fire Station"); }
}



/* =========================================================
 * OPEN MESSAGE MODAL
 * ========================================================= */
function openMessageModal(incidentId, reportType) {
  currentReportType = reportType;

  currentReport =
    reportType === 'fireReports'      ? (fireReports || []).find(r => r.id === incidentId)
  : reportType === 'otherEmergency'   ? (otherEmergencyReports || []).find(r => r.id === incidentId)
  : reportType === 'emsReports'       ? (emsReports || []).find(r => r.id === incidentId)
  : reportType === 'smsReports'       ? (smsReports || []).find(r => r.id === incidentId)
  : null;

  if (!currentReport) return;

  const modal = document.getElementById('fireMessageModal');
  if (!modal) return;
  modal.classList.remove('hidden');

  document.getElementById('fireMessageIncidentIdValue').innerText = currentReport.id || '';
  document.getElementById('fireMessageNameValue').innerText = currentReport.name || 'No Name Provided';
  document.getElementById('fireMessageContactValue').innerText = currentReport.contact || 'N/A';
  document.getElementById('fireMessageIncidentInput').value = currentReport.id || '';

  try {
    getFireStationNameByEmail(null, (n) => { currentReport.fireStationName = n || "Canocotan Fire Station"; });
  } catch (_) { currentReport.fireStationName = "Canocotan Fire Station"; }

  resetChatThread();
  resetListeners();
  storedMessages = [];
  __lastBubble = { type: null, ts: 0, el: null };

  // Enable chat input
  const input = document.getElementById('fireMessageInput');
  const btn = document.querySelector('#fireMessageForm button[type="submit"]');
  input.disabled = false;
  btn.disabled = false;
  input.placeholder = 'Type a message...';
  btn.classList.remove('opacity-50', 'cursor-not-allowed');

  fetchThread(incidentId, reportType);
  subscribeThread(incidentId, reportType);

  // mark all replies read in DB
  markThreadRead(incidentId, reportType).catch(()=>{});

  // instantly clear the badge in UI
  setBadgeCount(`${reportType}|${incidentId}`, 0);
}

function closeFireMessageModal() {
  document.getElementById('fireMessageModal').classList.add('hidden');
  resetListeners();
}

/* =========================================================
 * THREAD FETCH / LIVE SUBSCRIPTION
 * ========================================================= */

function resetChatThread() {
  const thread = document.getElementById('fireMessageThread');
  thread.innerHTML = '';
}

function resetListeners() {
  liveListeners.forEach(ref => { try { ref.off?.(); } catch (_) {} });
  liveListeners = [];
}

/* Base64 helpers */
function cleanB64(b64) { return (b64 || '').toString().replace(/\s+/g,"").replace(/[^A-Za-z0-9+/=]/g,""); }
function guessImageMime(b64) {
  if (!b64) return "image/jpeg";
  const head = b64.slice(0, 12);
  if (/^iVBOR/.test(head)) return "image/png";
  if (/^R0lGOD/.test(head)) return "image/gif";
  if (/^(UklGR|R0lGU)/.test(head)) return "image/webp";
  return "image/jpeg";
}
function guessAudioMime() { return "audio/mp4"; }

function groupMessages(raw) {
  // No grouping, every message is treated as a separate entity
  return raw.map(m => ({
    type: m.type,
    timestamp: m.timestamp,
    parts: [{
      ts: m.timestamp || Date.now(),
      kind: m.audioBase64 ? 'audio' : m.imageBase64 ? 'image' : 'text',
      audioBase64: m.audioBase64 || '',
      imageBase64: m.imageBase64 || '',
      text: m.text || ''
    }]
  }));
}




function fetchThread(incidentId, reportType) {
  const thread = document.getElementById('fireMessageThread');
  thread.innerHTML = '';

  const qResp = stationResponsesQuery(incidentId, reportType);
  const refRep = repliesRef(incidentId, reportType);
  const pulls = [];

  if (qResp) {
    pulls.push(
      qResp.once('value').then(snap => {
        const out = [];
        snap.forEach(c => {
          __seenResponseKeys.add(c.key); // prevent duplicate when live listener fires
          const v = c.val() || {};
          if (v.responseMessage || v.imageBase64 || v.audioBase64) {
            const ts = v.timestamp || Date.parse(`${v.responseDate || ''} ${v.responseTime || ''}`) || 0;
            out.push({ type: 'response', text: v.responseMessage || '', imageBase64: v.imageBase64 || '', audioBase64: v.audioBase64 || '', timestamp: ts });
          }
        });
        return out;
      })
    );
  }

  if (refRep) {
    pulls.push(
      refRep.once('value').then(snap => {
        const out = [];
        snap.forEach(c => {
          __seenReplyKeys.add(c.key); // prevent duplicate when live listener fires
          const v = c.val() || {};
          const text = v.text || v.replyMessage || '';
          const hasAny = text || v.imageBase64 || v.audioBase64;
          const isReply = String(v.type || 'reply').toLowerCase() === 'reply';
          if (isReply && hasAny) {
            out.push({
              type: 'reply',
              text,
              imageBase64: v.imageBase64 || '',
              audioBase64: v.audioBase64 || '',
              timestamp: v.timestamp || 0,
              _key: c.key,
            });
          }
        });
        return out;
      })
    );
  }

  Promise.all(pulls).then(chunks => {
    storedMessages = ([]).concat(...chunks);
    storedMessages.sort((a,b) => (a.timestamp||0)-(b.timestamp||0));
    storedMessages = groupMessages(storedMessages);
    renderMessages(storedMessages);
    thread.scrollTop = thread.scrollHeight;
  });
}


function renderMessages(messages) {
  const thread = document.getElementById('fireMessageThread');
  thread.innerHTML = '';
  __lastBubble = { type: null, ts: 0, el: null };
  messages.forEach(renderBubble);
}

function subscribeThread(incidentId, reportType) {
  // ---- Live user replies (central list) ----
  const repQ = repliesRef(incidentId, reportType);
  if (repQ) {
    repQ.on('child_added', snap => {
      if (__seenReplyKeys.has(snap.key)) return;
      __seenReplyKeys.add(snap.key);

      const v = snap.val() || {};
      const text = v.text || v.replyMessage || '';
      const hasAny = text || v.imageBase64 || v.audioBase64;
      const isReply = String(v.type || 'reply').toLowerCase() === 'reply';
      if (!isReply || !hasAny) return;

      renderBubble({
        type: 'reply',
        text,
        imageBase64: v.imageBase64 || '',
        audioBase64: v.audioBase64 || '',
        timestamp: v.timestamp || 0
      });

      const thread = document.getElementById('fireMessageThread');
      thread.scrollTop = thread.scrollHeight;

      if (!v.isRead) snap.ref.child('isRead').set(true).catch(()=>{});
    });
    liveListeners.push(repQ);
  }

  // ---- Live station responses (central list) ----
  const respQ = stationResponsesQuery(incidentId, reportType);
  if (respQ) {
    respQ.on('child_added', snap => {
      if (__seenResponseKeys.has(snap.key)) return;
      __seenResponseKeys.add(snap.key);

      const v = snap.val() || {};
      const text = v.responseMessage || '';
      const hasAny = text || v.imageBase64 || v.audioBase64;
      if (!hasAny) return;

      const ts =
        v.timestamp ||
        Date.parse(`${v.responseDate || ''} ${v.responseTime || ''}`) ||
        Date.now();

      renderBubble({
        type: 'response',
        text,
        imageBase64: v.imageBase64 || '',
        audioBase64: v.audioBase64 || '',
        timestamp: ts
      });

      const thread = document.getElementById('fireMessageThread');
      thread.scrollTop = thread.scrollHeight;
    });
    liveListeners.push(respQ);
  }
}


function renderBubble(msg) {
  const thread = document.getElementById('fireMessageThread');
  const nowTs = Number(msg.timestamp || Date.now());

  // Always create a new bubble for each message, even if the timestamps are the same
  const shell = document.createElement('div');
  shell.className = (msg.type === 'response')
    ? "message bg-blue-500 text-white p-4 rounded-lg my-2 max-w-xs ml-auto text-right"
    : "message bg-gray-300 text-black p-4 rounded-lg my-2 max-w-xs mr-auto text-left";

  const content = document.createElement('div');
  content.className = 'bubble-content';

  // Create each part of the message (text, image, audio)
  const parts = Array.isArray(msg.parts)
    ? msg.parts
    : [{
        ts: nowTs,
        kind: msg.audioBase64 ? 'audio' : msg.imageBase64 ? 'image' : 'text',
        audioBase64: msg.audioBase64 || '',
        imageBase64: msg.imageBase64 || '',
        text: msg.text || ''
    }];

  // Append content to the bubble based on message type
  parts.forEach(p => {
    if (p.kind === 'text' && p.text) {
      const t = document.createElement('div');
      t.textContent = p.text;
      t.style.whiteSpace = 'pre-line';
      t.style.wordBreak  = 'break-word';
      content.appendChild(t);
    }
    if (p.kind === 'image' && p.imageBase64) {
      const raw = cleanB64(p.imageBase64);
      if (raw) {
        const img = document.createElement('img');
        img.style.marginTop   = '8px';
        img.style.maxWidth    = '100%';
        img.style.borderRadius= '8px';
        img.src = `data:${guessImageMime(raw)};base64,${raw}`;
        img.alt = 'Image';
        content.appendChild(img);
      }
    }
    if (p.kind === 'audio' && p.audioBase64) {
      const raw = cleanB64(p.audioBase64);
      if (raw) {
        const aud = document.createElement('audio');
        aud.controls = true;
        aud.style.display   = 'block';
        aud.style.marginTop = '8px';
        aud.src = `data:${guessAudioMime(raw)};base64,${raw}`;
        content.appendChild(aud);
      }
    }
  });

  // Add timestamp
  const small = document.createElement('small');
  small.className = 'bubble-ts text-xs block mt-1 opacity-80';
  small.textContent = new Date(nowTs).toLocaleString();

  // Append the content and timestamp to the shell
  shell.appendChild(content);
  shell.appendChild(small);

  // Append the message bubble to the thread
  thread.appendChild(shell);

  // Scroll to the bottom
  thread.scrollTop = thread.scrollHeight;
}




/* =========================================================
 * SUBMIT REPLY
 * ========================================================= */
const fireForm = document.getElementById('fireMessageForm');
if (fireForm) {
  fireForm.addEventListener('submit', function(e){
    e.preventDefault();
    if (!currentReport) return;

    const incidentId = document.getElementById('fireMessageIncidentInput').value;
    const responseMessage = document.getElementById('fireMessageInput').value.trim();
    if (!responseMessage) return;

    const nn = stationNodesForReportType(currentReportType);
    if (!nn) return;

    const fireStationName = currentReport.fireStationName || `${nn.prefix} Fire Station`;

    const payload = {
      prefix: nn.prefix,
      reportType: nn.serviceReportType,   // 'fire' | 'otherEmergency' | 'emergencyMedicalServices' | 'sms'
      collectionPath: nn.repliesBase,     // direct Firebase path (kept for backend payload)
      incidentId,
      reporterName: currentReport.name || '',
      contact: currentReport.contact || '',
      fireStationName,
      responseMessage
    };

    fetch('/store-response', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN': '{{ csrf_token() }}'},
      body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(() => { document.getElementById('fireMessageInput').value = ''; })
    .catch(console.error);
  });
}

// put near the top, next to helpers
function contentKind(m) {
  if (m.audioBase64) return (m.type || 'reply') + ':audio';
  if (m.imageBase64) return (m.type || 'reply') + ':image';
  if (m.text)        return (m.type || 'reply') + ':text';
  return (m.type || 'reply') + ':empty';
}



/* =========================================================
 * MAPS / ROUTING / GEOFENCE (LEAFLET + OSRM + OVERPASS)
 * ========================================================= */

async function snapToRoad(lat, lng) {
  try {
    const url = `https://router.project-osrm.org/nearest/v1/car/${lng},${lat}`;
    const res = await fetch(url);
    const data = await res.json();
    const wp = data?.waypoints?.[0]?.location;
    return Array.isArray(wp) ? [wp[1], wp[0]] : [lat, lng];
  } catch {
    return [lat, lng];
  }
}

let __routeMap, __fenceMap, __routeCtrl;

async function openLocationModal(reportLat, reportLng) {
  const n = nodes(); if (!n) return;

  try {
    const snap = await firebase.database().ref(n.profile).once('value');
    const meta = snap.val() || {};
    const stationLat = parseFloat(meta.latitude);
    const stationLng = parseFloat(meta.longitude);

    if (![reportLat, reportLng, stationLat, stationLng].every(Number.isFinite)) {
      console.error("Invalid coordinates.");
      return;
    }

    const modal = document.getElementById('locationModal');
    modal.classList.remove('hidden');

    await updateTwoLeafletMaps({ reportLat, reportLng, stationLat, stationLng });

    setTimeout(() => {
      __routeMap?.invalidateSize();
      __fenceMap?.invalidateSize();
    }, 60);

  } catch (e) {
    console.error('Error fetching station profile:', e);
  }
}

const OVERPASS_ENDPOINTS = [
  "https://overpass-api.de/api/interpreter",
  "https://overpass.kumi.systems/api/interpreter",
  "https://overpass.openstreetmap.ru/api/interpreter"
];

function fetchWithTimeout(url, opts = {}, ms = 12000) {
  const ctrl = new AbortController();
  const t = setTimeout(() => ctrl.abort(), ms);
  return fetch(url, { ...opts, signal: ctrl.signal }).finally(() => clearTimeout(t));
}

async function countBuildingsWithin(lat, lng, radiusMeters) {
  const q = `
    [out:json][timeout:25];
    (
      node["building"](around:${radiusMeters},${lat},${lng});
      way["building"](around:${radiusMeters},${lat},${lng});
      relation["building"](around:${radiusMeters},${lat},${lng});
    );
    out ids;
  `.trim();

  for (const url of OVERPASS_ENDPOINTS) {
    try {
      const res = await fetchWithTimeout(url, { method: "POST", body: q });
      if (!res.ok) continue;
      const data = await res.json();
      const ids = new Set((data.elements || []).map(e => `${e.type}/${e.id}`));
      return ids.size;
    } catch { /* try next mirror */ }
  }
  throw new Error("All Overpass mirrors failed or timed out.");
}

async function updateTwoLeafletMaps({ reportLat, reportLng, stationLat, stationLng }) {
  try { __routeCtrl && __routeCtrl.remove(); } catch {}
  try { __routeMap && __routeMap.remove(); } catch {}
  try { __fenceMap && __fenceMap.remove(); } catch {}

  const mkTile = () =>
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap contributors'
    });

  const stationIcon = L.icon({ iconUrl: 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png', iconSize: [28, 28] });
  const reportIcon  = L.icon({ iconUrl: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',    iconSize: [28, 28] });

  // Left: Routing map
  __routeMap = L.map('routeMap').setView([reportLat, reportLng], 13);
  mkTile().addTo(__routeMap);
  L.marker([stationLat, stationLng], { icon: stationIcon }).addTo(__routeMap).bindPopup('Fire Station');
  L.marker([reportLat, reportLng],   { icon: reportIcon  }).addTo(__routeMap).bindPopup('Report Location');

  const [snapStationLat, snapStationLng] = await snapToRoad(stationLat, stationLng);
  const [snapReportLat, snapReportLng]   = await snapToRoad(reportLat, reportLng);

  __routeCtrl = L.Routing.control({
    waypoints: [ L.latLng(snapStationLat, snapStationLng), L.latLng(snapReportLat, snapReportLng) ],
    router: L.Routing.osrmv1({ serviceUrl: 'https://router.project-osrm.org/route/v1', profile: 'car', options: { geometries: 'geojson', overview: 'full' } }),
    addWaypoints: false, draggableWaypoints: false, routeWhileDragging: false, fitSelectedRoutes: true, showAlternatives: false,
    lineOptions: { color: '#1976d2', weight: 6, opacity: 0.9 }, createMarker: () => null
  }).addTo(__routeMap);

  // Right: Fence map
  __fenceMap = L.map('fenceMap').setView([reportLat, reportLng], 17);
  mkTile().addTo(__fenceMap);
  L.marker([reportLat, reportLng],   { icon: reportIcon  }).addTo(__fenceMap).bindPopup('Report Location').openPopup();
  L.marker([stationLat, stationLng], { icon: stationIcon }).addTo(__fenceMap).bindPopup('Fire Station');

  // Conditional geofencing
  const MIN_BUILDINGS = 5;
  const DEFAULT_RADIUS_METERS = 50;

  try {
    const buildingCount = await countBuildingsWithin(reportLat, reportLng, DEFAULT_RADIUS_METERS);
    if (buildingCount >= MIN_BUILDINGS) {
      L.circle([reportLat, reportLng], {
        radius: DEFAULT_RADIUS_METERS, color: 'red', fillColor: '#f03', fillOpacity: 0.25, weight: 2
      }).addTo(__fenceMap).bindPopup(`Geofence: ~${Math.round(DEFAULT_RADIUS_METERS)} m • ${buildingCount} buildings`);
    }
  } catch (e) {
    console.warn("Fencing skipped (Overpass error):", e.message || e);
  }
}

function closeLocationModal() {
  document.getElementById('locationModal').classList.add('hidden');
}

// ==== Message badge wiring ====
const __badgeSubs = new Map(); // key -> ref
function setBadgeCount(key, count) {
  const el = document.querySelector(`[data-key="${key}"] .msg-badge`);
  if (!el) return;
  if (count > 0) {
    el.textContent = count > 99 ? '99+' : String(count);
    el.classList.remove('hidden');
  } else {
    el.classList.add('hidden');
  }
}

// Count unread replies (type=='reply' && !isRead)
function subscribeBadge(key) {
  if (__badgeSubs.has(key)) return;
  const [type, incidentId] = key.split('|');

  const q = repliesRef(incidentId, type);
  if (!q) return;

  const handler = q.on('value', snap => {
    let unread = 0;
    snap.forEach(c => {
      const v = c.val() || {};
      const isReply = String(v.type || 'reply').toLowerCase() === 'reply';
      if (isReply && !v.isRead) unread++;
    });
    setBadgeCount(key, unread);
  });

  __badgeSubs.set(key, { ref: q, handler });
}




// Call this after (re)rendering any table that contains message buttons
function ensureMessageBadges() {
  document.querySelectorAll('.msg-btn[data-key]').forEach(a => {
    subscribeBadge(a.getAttribute('data-key'));
  });
}

// Optional: mark all unread as read when opening the thread
async function markThreadRead(incidentId, reportType) {
  const q = repliesRef(incidentId, reportType);
  if (!q) return;
  const snap = await q.once('value');
  const updates = {};
  snap.forEach(c => {
    const v = c.val() || {};
    const isReply = String(v.type || 'reply').toLowerCase() === 'reply';
    if (isReply && !v.isRead) updates[`${c.key}/isRead`] = true;
  });
  if (Object.keys(updates).length) {
    await q.ref.update(updates);
  }
}



</script>


    @endsection
