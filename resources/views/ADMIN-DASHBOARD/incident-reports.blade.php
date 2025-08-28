 @extends('ADMIN-DASHBOARD.app')
 @section('page_name', 'incident-reports')
 @section('title', 'Incident Reports')

 @section('content')

    <div class="container mx-auto p-6">

        <h1 class="text-2xl font-bold text-gray-800 mb-6">Incident Report Management</h1>

            <!-- Toast Notification -->
            <div id="toast" style="margin-right: 600px;" class="hidden fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg">
                <span id="toastMessage">Report Accepted</span>
                    <button id="toastOkButton" class="ml-4 text-white underline">OK</button>
            </div>


            <select id="incidentType" class="px-4 py-2 rounded bg-gray-200" onchange="toggleIncidentTables()">
                        <option value="allReports" selected>All Reports</option>
                        <option value="fireReports">Fire Reports</option>
                        <option value="otherEmergency">Other Emergency</option>
                        <option value="smsReports">SMS Reports</option>
            </select>

    <br>
    <br>

    <div id="allReportsSection" class="bg-white p-6 shadow rounded-lg">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">All Reports</h2>

        <!-- Filters (below header, same pattern as Fire) -->
        <div class="max-h-96 overflow-y-auto">
            <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-100">
                <th class="px-4 py-2 text-left text-gray-600">#</th>
                <th class="px-4 py-2 text-left text-gray-600">Location</th>
                <th class="px-4 py-2 text-left text-gray-600">Date & Time</th>
                <th class="px-4 py-2 text-left text-gray-600">Status</th>
                <th class="px-4 py-2 text-left text-gray-600">Action</th>
                </tr>

                <!-- Filter Row -->
                <tr class="bg-gray-50 text-sm">
                <th></th>
                <th class="px-4 py-2">
                    <input
                    type="text"
                    id="allLocationSearch"
                    placeholder="Search Location..."
                    class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                    oninput="filterAllReportsTable()"
                    />
                </th>
                <th class="px-4 py-2">
                    <select id="allDateTimeFilter"
                    class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                    onchange="handleAllDateTimeFilterChange()">
                    <option value="all" selected>All</option>
                    <option value="date">Date</option>
                    <option value="time">Time</option>
                    </select>
                    <input
                    type="date"
                    id="allDateSearch"
                    class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                    onchange="filterAllReportsTable()"
                    />
                    <input
                    type="time"
                    id="allTimeSearch"
                    class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                    onchange="filterAllReportsTable()"
                    />
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

                       <!-- Recent Fire Incidents Table -->
    <div id="fireReportsSection" class="bg-white p-6 shadow rounded-lg hidden">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Fire Incident Reports</h2>
            <div class="max-h-96 overflow-y-auto">
                <table class="min-w-full table-auto">
                    <thead>
                            <!-- Header Row -->
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 text-left text-gray-600">#</th>
                            <th class="px-4 py-2 text-left text-gray-600">Location</th>
                            {{-- <th class="px-4 py-2 text-left text-gray-600">Level</th> --}}
                            <th class="px-4 py-2 text-left text-gray-600 cursor-pointer" onclick="focusFireDatePicker()">Date & Time</th>
                            <th class="px-4 py-2 text-left text-gray-600">Status</th>
                            <th class="px-4 py-2 text-left text-gray-600">Action</th>
                        </tr>

                            <!-- Filter Dropdown Row -->
                        <tr class="bg-gray-50 text-sm">
                            <th></th>
                            <th class="px-4 py-2">
                                <input
                                type="text"
                                id="fireLocationSearch"
                                placeholder="Search Location..."
                                class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                oninput="filterFireReportTable()"
                                />
                            </th>
                            <th class="px-4 py-2">
                                <select id="fireLevelFilter"
                                class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                onchange="filterFireReportTable()">
                                <option value="">All</option>
                                <option value="1st Alarm">1st Alarm</option>
                                <option value="2nd Alarm">2nd Alarm</option>
                                </select>
                            </th>
                            <th class="px-4 py-2">
                                <select id="fireDateTimeFilter" class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                onchange="handleDateTimeFilterChange()">
                                <option value="all" selected>All</option>
                                <option value="date">Date</option>
                                <option value="time">Time</option>
                                </select>
                                <!-- Hidden inputs: show only when relevant -->
                                <input
                                type="date"
                                id="fireDateSearch"
                                class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                                onchange="filterFireReportTable()"
                                />
                                <input
                                type="time"
                                id="fireTimeSearch"
                                class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                                onchange="filterFireReportTable()"
                                />
                            </th>
                            <th class="px-4 py-2">
                                <select id="fireStatusFilter"
                                class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                                onchange="filterFireReportTable()">
                                <option value="">All</option>
                                <option value="Pending">Pending</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                                </select>
                            </th>
                            <th></th>
                        </tr>

                    </thead>

                        <tbody id="fireReportsBody">
                            @foreach($fireReports as $index => $report)
                            <tr id="reportRow{{ $report['id'] }}" class="border-b" data-report='@json($report)' data-type="fireReports">
                                <td class="px-4 py-2">{{ $index + 1 }}</td>
                                <td class="px-4 py-2">{{ $report['exactLocation'] ?? 'N/A' }}</td>
                                {{-- <td class="px-4 py-2">{{ $report['alertLevel'] ?? 'Unknown' }}</td> --}}
                                <td class="px-4 py-2">{{ $report['date'] ?? 'N/A' }} {{ $report['reportTime'] ?? 'N/A' }}</td>
                                <td class="px-4 py-2 status text-{{ $report['status'] === 'Ongoing' ? 'red' : ($report['status'] === 'Completed' ? 'green' : ($report['status'] === 'Pending' ? 'orange' : ($report['status'] === 'Received' ? 'blue' : 'yellow'))) }}-500">
                                {{ $report['status'] ?? 'Unknown' }}
                                </td>
                                <td class="px-4 py-2 space-x-2 flex items-center">
                                <a href="javascript:void(0);" onclick="openMessageModal('{{ $report['id'] }}', 'fireReports')">
                                    <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                                </a>
                                <a href="javascript:void(0);" onclick="openLocationModal({{ $report['latitude'] }}, {{ $report['longitude'] }})">
                                    <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
                                </a>
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

            <!-- Spinner for loading Other Emergency Incidents -->
            <div id="spinner" class="hidden flex justify-center items-center mt-6">
                <div class="spinner-border animate-spin inline-block w-12 h-12 border-4 border-solid border-gray-200 border-t-gray-600 rounded-full"></div>
            </div>

    <!-- Other Emergency Incidents Table -->
    <div id="otherEmergencySection" class="bg-white p-6 shadow rounded-lg mt-6 hidden">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Other Emergency Incidents</h2>
        <div class="max-h-96 overflow-y-auto">
            <table class="min-w-full table-auto">
            <thead>
                <!-- Header Row -->
                <tr class="bg-gray-100">
                <th class="px-4 py-2 text-left text-gray-600">#</th>
                <th class="px-4 py-2 text-left text-gray-600">Location</th>
                <th class="px-4 py-2 text-left text-gray-600">Emergency Type</th>
                <th class="px-4 py-2 text-left text-gray-600 cursor-pointer" onclick="focusOtherDatePicker()">Date & Time</th>
                <th class="px-4 py-2 text-left text-gray-600">Status</th>
                <th class="px-4 py-2 text-left text-gray-600">Action</th>
                </tr>

                <!-- Filter Dropdown Row -->
                <tr class="bg-gray-50 text-sm">
                <th></th>
                <th class="px-4 py-2">
                    <input
                    type="text"
                    id="otherLocationSearch"
                    placeholder="Search Location..."
                    class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                    oninput="filterOtherEmergencyTable()"
                    />
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
                    <select id="otherDateTimeFilter" class="w-full px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                    onchange="handleOtherDateTimeFilterChange()">
                    <option value="all" selected>All</option>
                    <option value="date">Date</option>
                    <option value="time">Time</option>
                    </select>
                    <input
                    type="date"
                    id="otherDateSearch"
                    class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                    onchange="filterOtherEmergencyTable()"
                    />
                    <input
                    type="time"
                    id="otherTimeSearch"
                    class="w-full mt-1 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm hidden"
                    onchange="filterOtherEmergencyTable()"
                    />
                </th>
                <th class="px-4 py-2">
                    <select id="otherStatusFilter"
                    class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-sm"
                    onchange="filterOtherEmergencyTable()">
                    <option value="">All</option>
                    <option value="Pending">Pending</option>
                    <option value="Ongoing">Ongoing</option>
                    <option value="Completed">Completed</option>
                    </select>
                </th>
                <th></th>
                </tr>
            </thead>
            <tbody id="otherEmergencyTableBody">
                @foreach($otherEmergencyReports as $index => $report)
                <tr id="reportRow{{ $report['id'] }}" class="border-b" data-report='@json($report)' data-type="otherEmergency">
                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                    <td class="px-4 py-2">{{ $report['exactLocation'] ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $report['emergencyType'] ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $report['date'] ?? 'N/A' }} {{ $report['reportTime'] ?? 'N/A' }}</td>
                    <td class="px-4 py-2 status text-{{ $report['status'] === 'Ongoing' ? 'red' : ($report['status'] === 'Completed' ? 'green' : ($report['status'] === 'Pending' ? 'orange' : ($report['status'] === 'Received' ? 'blue' : 'yellow'))) }}-500">
                    {{ $report['status'] ?? 'Unknown' }}
                    </td>
                    <td class="px-4 py-2 flex space-x-4">
                    <a href="javascript:void(0);" onclick="openMessageModal('{{ $report['id'] }}', 'otherEmergency')">
                        <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                    </a>
                    <a href="javascript:void(0);" onclick="openLocationModal({{ $report['latitude'] }}, {{ $report['longitude'] }})">
                        <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
                    </a>
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


    <!-- SMS Reports Table (visible only when selector = SMS) -->
    <div id="smsReportsSection" class="bg-white p-6 shadow rounded-lg mt-6 hidden">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">SMS Reports</h2>
        <div class="max-h-96 overflow-y-auto">
            <table class="min-w-full table-auto">
            <thead>
        <tr class="bg-gray-100">
            <th class="px-4 py-2 text-left text-gray-600">#</th>
            <th class="px-4 py-2 text-left text-gray-600">Location</th>
            <th class="px-4 py-2 text-left text-gray-600">Date & Time</th>
            <th class="px-4 py-2 text-left text-gray-600">Status</th>
            <th class="px-4 py-2 text-left text-gray-600">Action</th>
        </tr>

        <!-- Filters -->
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

            <!-- NEW: Status filter -->
            <th class="px-4 py-2">
                <select id="smsStatusFilter"
                        class="w-full px-2 py-1 border border-gray-300 bg-white rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
                        onchange="filterSmsReportsTable()">
                <option value="">All</option>
                <option value="Pending">Pending</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Completed">Completed</option>

                </select>
            </th>

            <!-- Keep Action column aligned -->
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
            <tr id="reportRow{{ $report['id'] }}" class="border-b" data-report='@json($report)' data-type="smsReports">
            <td class="px-4 py-2">{{ $index + 1 }}</td>
            <td class="px-4 py-2">{{ $report['location'] ?? 'N/A' }}</td>
            <td class="px-4 py-2">{{ $report['date'] ?? 'N/A' }} {{ $report['time'] ?? 'N/A' }}</td>

            {{-- STATUS column --}}
            <td class="px-4 py-2 status text-{{ $color }}-500">{{ $status }}</td>

            {{-- ACTION column --}}
            <td class="px-4 py-2 space-x-2 flex items-center">
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


    <!-- Message Modal for Fire Reports -->
    <div id="fireMessageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl shadow-lg relative">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">
                Fire Incident Chat
            </h3>

                <!-- Display ID, Name, and Contact -->
                <div class="mb-4">
                    <p><strong>Incident ID:</strong> <span id="fireMessageIncidentIdValue"></span></p>
                    <p><strong>Reporter Name:</strong> <span id="fireMessageNameValue"></span></p>
                    <p><strong>Contact:</strong> <span id="fireMessageContactValue"></span></p>
                </div>

                    <!-- Scrollable chat area -->
                    <div id="fireMessageThread" class="h-64 overflow-y-auto border border-gray-200 p-4 rounded mb-4 bg-gray-50 scroll-smooth">
                        <!-- Chat messages will be dynamically inserted here -->
                    </div>

                    <!-- Message input -->
                    <form id="fireMessageForm" class="flex gap-2">
                        <input type="hidden" id="fireMessageIncidentInput">
                        <input type="text" id="fireMessageInput" class="flex-1 border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type a message..." required>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Send</button>
                    </form>
                            <button onclick="closeFireMessageModal()" class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
            </div>
    </div>


    <!-- Message Modal for Other Emergency Reports -->
    <div id="otherEmergencyMessageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl shadow-lg relative">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Other Emergency Incident Chat <span id="otherEmergencyMessageIncidentId" class="text-sm text-gray-500"></span></h3>

                <!-- Scrollable chat area -->
                <div id="otherEmergencyMessageThread" class="h-64 overflow-y-auto border border-gray-200 p-4 rounded mb-4 bg-gray-50 scroll-smooth">
                        <!-- Chat messages will be dynamically inserted here -->
                </div>

                    <!-- Message input -->
                    <form id="otherEmergencyMessageForm" class="flex gap-2">
                        <input type="hidden" id="otherEmergencyMessageIncidentInput">
                            <input type="text" id="otherEmergencyMessageInput" class="flex-1 border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type a message...">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Send</button>
                    </form>
                            <button onclick="closeOtherEmergencyMessageModal()" class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>
    </div>

                    <!-- Location Modal -->
                <!-- Location Modal -->
                        <div id="locationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                            <div style="width: 1000px; margin-left:250px;" class="bg-white rounded-lg p-6 w-full shadow-lg relative" style="max-height: 80vh; overflow-y:auto;">
                                <h3 class="text-lg font-semibold mb-4 text-gray-800">Report Location</h3>

                                <!-- Map Container (Flexbox layout) -->
                                <div class="flex space-x-4">
                                    <!-- Google Maps 1 (iframe for route) -->
                                    <div style="flex: 1;">
                                        <iframe id="mapIframe" src="" width="100%" height="470" style="border:0;" allowfullscreen loading="lazy"></iframe>
                                    </div>

                                    <!-- Google Maps 2 (div for geofencing) -->
                                    <div id="mapContainer" style="flex: 1; height: 470px;"></div>
                                </div>

                                <button onclick="closeLocationModal()" class="absolute top-3 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
                            </div>
                        </div>



                    <!-- Details Modal -->
                    <div id="detailsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                        <div class="bg-white rounded-lg p-6 w-full max-w-2xl shadow-lg relative">
                            <h3 class="text-2xl font-semibold text-gray-800 mb-6">Incident Report Details</h3>

                            <!-- Incident Info Section -->
                            <div class="space-y-6">
                                <!-- Fire Report Details -->
                                <div id="fireReportDetails" class="space-y-4">
                                    <div class="flex justify-between">
                                        <strong class="text-gray-700">Incident ID:</strong> <span id="detailIncidentId" class="text-gray-600"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <strong class="text-gray-700">Reporter Name:</strong> <span id="detailName" class="text-gray-600"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <strong class="text-gray-700">Contact:</strong> <span id="detailContact" class="text-gray-600"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <strong class="text-gray-700">Alert Level:</strong> <span id="detailLevel" class="text-gray-600"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <strong class="text-gray-700">Houses Affected:</strong> <span id="detailHousesAffected" class="text-gray-600"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <strong class="text-gray-700">Start Time:</strong> <span id="detailStartTime" class="text-gray-600"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <strong class="text-gray-700">Status:</strong> <span id="detailStatus" class="text-gray-600"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <strong class="text-gray-700">Exact Location:</strong> <span id="detailLocation" class="text-gray-600"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <strong class="text-gray-700">Date:</strong> <span id="detailDate" class="text-gray-600"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <strong class="text-gray-700">Report Time:</strong> <span id="detailReportTime" class="text-gray-600"></span>
                                    </div>
                                </div>

                                <!-- Other Emergency Report Details -->
                        <div id="otherEmergencyDetails" class="space-y-4 hidden">
                            <div class="flex justify-between">
                                <strong class="text-gray-700">Incident ID:</strong> <span id="detailIncidentIdOther" class="text-gray-600"></span>
                            </div>
                            <div class="flex justify-between">
                                <strong class="text-gray-700">Reporter Name:</strong> <span id="detailNameOther" class="text-gray-600"></span>
                            </div>
                            <div class="flex justify-between">
                                <strong class="text-gray-700">Contact:</strong> <span id="detailContactOther" class="text-gray-600"></span>
                            </div>
                            <div class="flex justify-between">
                                <strong class="text-gray-700">Emergency Type:</strong> <span id="detailEmergencyType" class="text-gray-600"></span>
                            </div>
                            <div class="flex justify-between">
                                <strong class="text-gray-700">Exact Location:</strong> <span id="detailLocationOther" class="text-gray-600"></span>
                            </div>
                            <div class="flex justify-between">
                                <strong class="text-gray-700">Date:</strong> <span id="detailDateOther" class="text-gray-600"></span>
                            </div>
                            <div class="flex justify-between">
                                <strong class="text-gray-700">Report Time:</strong> <span id="detailReportTimeOther" class="text-gray-600"></span>
                            </div>
                        </div>

                                    <!-- inside #detailsModal, below the details panels -->
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




                            <!-- Action Buttons -->
                            <div class="flex justify-end mt-6 space-x-4">
                        <button onclick="closeDetailsModal()" style="background-color: #E00024; height:45px; width:90px; margin-top:7px;" class="px-6 py-2 text-white rounded-md hover:bg-gray-600">Close</button>
                        <div id="statusActionButtons" class="flex gap-2"></div>
                    </div>


                            <!-- Close Icon -->
                            <button onclick="closeDetailsModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
                        </div>
                    </div>


                <script>
                // ==================================================
                // URL Parameter Handling and DOM Ready
                // ==================================================
                document.addEventListener("DOMContentLoaded", () => {

                      // Render immediately so All Reports is never empty on first paint
                safeRenderAllReports();
                handleUrlParams();
                safeInitRealtime();        // guarded init
                initializeRealTimeListener();

                toggleIncidentTables();
                normalizeInitialTimes();
                renderAllReports();
                });

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

                // ==================================================
                // Data Initialization
                // ==================================================
              let fireReports = @json($fireReports);
                let otherEmergencyReports = @json($otherEmergencyReports);
                let smsReports = @json($smsReports ?? []);


                // Globals
                let currentReport = null;
                let replyListenerRef = null;
                let leafletMap = null;
                let leafletLayersGroup = null;
                let heardSmsIds = new Set();

                function to24h(t) {
                if (!t) return '';
                // Accept "H:MM", "HH:MM", optional seconds, optional AM/PM
                const m = String(t).trim().match(/^(\d{1,2}):(\d{2})(?::\d{2})?\s*(AM|PM)?$/i);
                if (!m) return t; // unknown format, return as-is
                let [ , hh, mm, ap ] = m;
                let h = parseInt(hh, 10);
                if (ap) {
                    const up = ap.toUpperCase();
                    if (up === 'PM' && h !== 12) h += 12;
                    if (up === 'AM' && h === 12) h = 0;
                }
                return `${String(h).padStart(2,'0')}:${mm}`;
                }

                function dateToISO(dmy) {
                // expects dd/mm/yyyy or dd/mm/yy
                if (!dmy) return '';
                const parts = dmy.split('/');
                if (parts.length !== 3) return '';
                const [dd, mm, yy] = parts;
                const yyyy = yy.length === 2 ? `20${yy}` : yy;
                return `${yyyy}-${mm.padStart(2,'0')}-${dd.padStart(2,'0')}`;
                }


                // ==================================================
                // Station Helpers
                // ==================================================
                const SESSION_EMAIL = ("{{ session('firebase_user_email') }}" || "").toLowerCase();

                function stationPrefixFromEmail(email) {
                if (!email) return null;
                if (email === 'mabini123@gmail.com') return 'Mabini';
                if (email === 'lafilipina123@gmail.com') return 'LaFilipina';
                if (email === 'canocotan123@gmail.com') return 'Canocotan';
                return null;
                }

               function nodes() {
            const p = stationPrefixFromEmail(SESSION_EMAIL);
            if (!p) return null;
            return {
                prefix: p,
                base: `${p}FireStation`,
                fireReport: `${p}FireStation/${p}FireReport`,
                otherEmergency: `${p}FireStation/${p}OtherEmergency`,
                // SMS may be saved in either of these (or very old root fallback)
                smsCandidates: [
                `${p}FireStation/${p}SmsReport`,
                `${p}FireStation/SmsReport`,
                `SmsReport`
                ],
                profile: `${p}FireStation/${p}Profile`,
                firefighters: `${p}FireStation/${p}FireFighters`
            };
            }

            async function resolveSmsPathById(id) {
            const n = nodes();
            if (!n) return null;
            for (const base of n.smsCandidates) {
                const snap = await firebase.database().ref(`${base}/${id}`).once('value');
                if (snap.exists()) return base; // found the node that holds this incident
            }
            // not found — default to the first candidate
            return n.smsCandidates[0] || null;
            }





                // ==================================================
                // Real-time Listeners
                // ==================================================
   function initializeRealTimeListener() {
  const n = nodes();
  if (!n) { console.error("No station prefix resolved from session email."); return; }

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

  // update in-memory
  const i = (smsReports || []).findIndex(r => r.id === id);
  if (i !== -1) smsReports[i] = { ...smsReports[i], ...patch, id };

  // patch DOM row if present (SMS table)
  const row = document.getElementById(`reportRow${id}`);
  if (!row) return;

  row.setAttribute('data-report', JSON.stringify(smsReports[i] || patch));

  // cells: [#, location, dateTime, status, action]
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
      statusCell.classList.remove('text-red-500','text-green-500','text-orange-500','text-blue-500','text-yellow-500','text-black');
      // SMS table uses colors; "All Reports" rendering already handles black-for-Pending there.
      const color = statusColor(s);
      statusCell.classList.add(`text-${color}-500`);
    }
  }
}





// 3) AFTER renderSortedReports(...) FUNCTION, ADD:
function renderSmsReports(highlightId = null) {
  const body = document.getElementById('smsReportsBody');
  if (!body) return;

  // sort by merged timestamp
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
          ${locBtn}
          <a href="javascript:void(0);" onclick="openDetailsModal('${report.id}', 'smsReports')">
            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
          </a>
        </td>
      </tr>`;
  }).join('');

  body.innerHTML = rows;
}




// 4) AFTER insertNewReportRow(...) FUNCTION, ADD:
function insertNewSmsRow(report) {
  // normalize minimal fields used by table/renderers
  report.date = report.date || new Date().toISOString().slice(0,10);
  report.time = report.time || new Date().toTimeString().slice(0,5);

  // de-dup by id
  const idx = (smsReports || []).findIndex(r => r.id === report.id);
  if (idx === -1) {
    smsReports.unshift(report);
  } else {
    smsReports[idx] = { ...smsReports[idx], ...report };
  }

  // render sections
  renderSmsReports(report.id);
}


                                // ==================================================
                // Patch helpers
                // ==================================================
                function applyRealtimePatch(snapshot, reportType) {
                const id = snapshot.key;
                const patch = snapshot.val() || {};
                const arr = reportType === 'fireReports' ? fireReports : otherEmergencyReports;

                // update in-memory array
                const i = arr.findIndex(r => r.id === id);
                if (i !== -1) arr[i] = { ...arr[i], ...patch, id };

                // patch DOM row if it exists
                const row = document.getElementById(`reportRow${id}`);
                if (!row) return;



                // update data-report payload
                row.setAttribute('data-report', JSON.stringify(arr[i] || patch));

                // update status cell if status changed
                if (typeof patch.status !== 'undefined') {
                    const statusCell = row.querySelector('.status');
                    if (statusCell) {
                    statusCell.textContent = patch.status;
                    // reset color classes
                    statusCell.classList.remove(
                        'text-red-500','text-green-500','text-orange-500','text-blue-500','text-yellow-500'
                    );
                    const color =
                        patch.status === 'Ongoing'   ? 'red'    :
                        patch.status === 'Completed' ? 'green'  :
                        patch.status === 'Pending'   ? 'orange' :
                        patch.status === 'Received'  ? 'blue'   : 'yellow';
                    statusCell.classList.add(`text-${color}-500`);
                    }
                }

                // update other visible cells if relevant fields changed
                if (reportType === 'fireReports') {
                    if (typeof patch.exactLocation !== 'undefined')
                    row.children[1].textContent = patch.exactLocation || 'N/A';
                    if (typeof patch.alertLevel !== 'undefined')
                    row.children[2].textContent = patch.alertLevel || 'Unknown';
                    if (typeof patch.date !== 'undefined' || typeof patch.reportTime !== 'undefined') {
                    const r = JSON.parse(row.getAttribute('data-report')) || {};
                    row.children[3].textContent = `${r.date || 'N/A'} ${r.reportTime || 'N/A'}`;
                    }
                } else {
                    if (typeof patch.exactLocation !== 'undefined')
                    row.children[1].textContent = patch.exactLocation || 'N/A';
                    if (typeof patch.emergencyType !== 'undefined')
                    row.children[2].textContent = patch.emergencyType || 'N/A';
                    if (typeof patch.date !== 'undefined' || typeof patch.reportTime !== 'undefined') {
                    const r = JSON.parse(row.getAttribute('data-report')) || {};
                    row.children[3].textContent = `${r.date || 'N/A'} ${r.reportTime || 'N/A'}`;
                    }
                }

                if (typeof patch.date !== 'undefined' || typeof patch.reportTime !== 'undefined') {
                const r = JSON.parse(row.getAttribute('data-report')) || {};
                row.children[3].textContent = `${r.date || 'N/A'} ${to24h(r.reportTime) || 'N/A'}`;
                }


                // optional: re-sort if your ordering depends on date/time
                // if (typeof patch.status !== 'undefined' || typeof patch.date !== 'undefined' || typeof patch.reportTime !== 'undefined') {
                //   renderSortedReports(arr, reportType);
                // }
                }

                function removeRow(id) {
                const el = document.getElementById(`reportRow${id}`);
                if (el && el.parentNode) el.parentNode.removeChild(el);
                }



                // ==================================================
                // Report Table Rendering
                // ==================================================
                function insertNewReportRow(report, reportType) {
                const tableBodyId = reportType === 'fireReports' ? 'fireReportsBody' : 'otherEmergencyTableBody';
                const tableBody = document.getElementById(tableBodyId);
                if (!tableBody) return;
                if (document.getElementById(`reportRow${report.id}`)) return;

                report.date = report.date || new Date().toLocaleDateString();
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
                      renderAllReports();
                } else {
                    otherEmergencyReports.unshift(report);
                    otherEmergencyReports.sort((a, b) => parseDateTime(b.date, b.reportTime) - parseDateTime(a.date, a.reportTime));
                    renderSortedReports(otherEmergencyReports, 'otherEmergency', report.id);
                      renderAllReports();
                }
                }

                function renderSortedReports(reportsArray, reportType, highlightId = null) {
                const tableBodyId = reportType === 'fireReports' ? 'fireReportsBody' : 'otherEmergencyTableBody';
                const tableBody = document.getElementById(tableBodyId);
                if (!tableBody) return;

                tableBody.style.visibility = 'hidden';
                const fragment = document.createDocumentFragment();

                reportsArray.forEach((report, index) => {
                    const rowId = `reportRow${report.id}`;
                    const statusColor = report.status === 'Ongoing' ? 'red' :
                                        report.status === 'Completed' ? 'green' :
                                        report.status === 'Pending' ? 'orange' :
                                        report.status === 'Received' ? 'blue' : 'yellow';

                    const row = document.createElement('tr');
                    row.id = rowId;
                    row.className = 'border-b';
                    row.classList.toggle('bg-yellow-100', !!(highlightId && report.id === highlightId));
                    row.setAttribute('data-report', JSON.stringify(report));
                    row.setAttribute('data-type', reportType);

                    const cells = reportType === 'fireReports'
                    ? `
                        <td class="px-4 py-2">${index + 1}</td>
                        <td class="px-4 py-2">${report.exactLocation || 'N/A'}</td>
                        <td class="px-4 py-2">${report.alertLevel || 'Unknown'}</td>
                        <td class="px-4 py-2">${report.date || 'N/A'} ${report.reportTime || 'N/A'}</td>
                        <td class="px-4 py-2 status text-${statusColor}-500">${report.status || 'Unknown'}</td>
                        <td class="px-4 py-2 space-x-2 flex items-center">
                        <a href="javascript:void(0);" onclick="openMessageModal('${report.id}', 'fireReports')">
                            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                        </a>
                        <a href="javascript:void(0);" onclick="openLocationModal(${report.latitude}, ${report.longitude})">
                            <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
                        </a>
                        <a href="javascript:void(0);" onclick="openDetailsModal('${report.id}', 'fireReports')">
                            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
                        </a>
                        </td>`
                    : `
                        <td class="px-4 py-2">${index + 1}</td>
                        <td class="px-4 py-2">${report.exactLocation || 'N/A'}</td>
                        <td class="px-4 py-2">${report.emergencyType || 'N/A'}</td>
                        <td class="px-4 py-2">${report.date || 'N/A'} ${report.reportTime || 'N/A'}</td>
                        <td class="px-4 py-2 status text-${statusColor}-500">${report.status || 'Unknown'}</td>
                        <td class="px-4 py-2 space-x-2 flex items-center">
                        <a href="javascript:void(0);" onclick="openMessageModal('${report.id}', 'otherEmergency')">
                            <img src="{{ asset('images/message.png') }}" alt="Message" class="w-6 h-6">
                        </a>
                        <a href="javascript:void(0);" onclick="openLocationModal(${report.latitude}, ${report.longitude})">
                            <img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6">
                        </a>
                        <a href="javascript:void(0);" onclick="openDetailsModal('${report.id}', 'otherEmergency')">
                            <img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6">
                        </a>
                        </td>`;

                    row.innerHTML = cells;
                    fragment.appendChild(row);
                });

                tableBody.innerHTML = '';
                tableBody.appendChild(fragment);
                tableBody.style.visibility = 'visible';
                }

                            // ============================
                // All Reports (merge + render)
                // ============================
                function parseDT(d, t, fallbackTs) {
                // Normalize date
                let isoDate = '';
                if (!d) {
                    // use fallback numeric ts if present
                    return typeof fallbackTs === 'number' ? fallbackTs : 0;
                }
                if (d.includes('/')) {
                    // dd/mm/yyyy or dd/mm/yy
                    isoDate = dateToISO(d); // your helper already handles this
                } else {
                    // assume already ISO-ish (yyyy-mm-dd)
                    isoDate = d;
                }

                // Normalize time (supports H:MM, HH:MM, optional :SS and AM/PM)
                const normTime = to24h(t || '') || (t || '00:00');

                const ts = Date.parse(`${isoDate} ${normTime}`);
                if (!isNaN(ts)) return ts;

                // Last resort: fallback numeric timestamp if provided
                return typeof fallbackTs === 'number' ? fallbackTs : 0;
                }


                function asArray(v){ return Array.isArray(v) ? v : (v ? Object.values(v) : []); }

           function buildAllReports() {
  const fire = asArray(fireReports).map(r => ({
    id: r.id, type: 'fireReports',
    location: r.exactLocation || r.location || 'N/A',
    date: r.date || '', time: r.reportTime || '',
    status: r.status || 'Unknown',
    lat: r.latitude, lng: r.longitude,
    sortTs: parseDT(r.date, r.reportTime, r.timestamp ?? r.createdAt ?? r.updatedAt)
  }));

  const other = asArray(otherEmergencyReports).map(r => ({
    id: r.id, type: 'otherEmergency',
    location: r.exactLocation || r.location || 'N/A',
    date: r.date || '', time: r.reportTime || '',
    status: r.status || 'Unknown',
    lat: r.latitude, lng: r.longitude,
    sortTs: parseDT(r.date, r.reportTime, r.timestamp ?? r.createdAt ?? r.updatedAt)
  }));

  const sms = asArray(smsReports).map(r => ({
    id: r.id, type: 'smsReports',
    location: r.location || 'N/A',
    date: r.date || '', time: r.time || '',
    status: r.status || 'N/A',
    lat: r.latitude, lng: r.longitude,
    sortTs: parseDT(r.date, r.time, r.timestamp ?? r.createdAt ?? r.updatedAt)
  }));

  return [...fire, ...other, ...sms].sort((a,b)=> b.sortTs - a.sortTs);
}

function statusColor(s) {
  return s === 'Ongoing' ? 'red' :
         s === 'Completed' ? 'green' :
         s === 'Pending' ? 'orange' :
         s === 'Received' ? 'blue' : 'yellow';
}


                        function safeRenderAllReports() {
            try { renderAllReports(); }
            catch (e) { console.error('renderAllReports failed:', e); }
            }

            function safeInitRealtime() {
            try {
                // Only start listeners when Firebase is actually ready
                if (window.firebase && firebase.apps && firebase.apps.length && typeof firebase.database === 'function') {
                initializeRealTimeListener();
                } else {
                // Retry a moment later (Firebase script may still be loading)
                setTimeout(safeInitRealtime, 500);
                }
            } catch (e) {
                console.error('initializeRealTimeListener error:', e);
            }
            }


              // after renderAllReports() sets tbody HTML:


function renderAllReports() {
  const body = document.getElementById('allReportsBody');
  if (!body) return;

  const rows = buildAllReports();
  body.innerHTML = rows.map((r, i) => {
    const time24 = to24h(r.time) || r.time || 'N/A';
    const dateStr = r.date || 'N/A';
    const locBtn = (r.lat != null && r.lng != null)
      ? `<a href="javascript:void(0);" onclick="openLocationModal(${r.lat}, ${r.lng})"><img src="{{ asset('images/location.png') }}" alt="Location" class="w-6 h-6"></a>`
      : '';

    const statusDisp = capStatus(r.status);
    const colorClass = (r.type === 'smsReports' && statusDisp === 'Pending')
      ? 'text-black'
      : `text-${statusColor(statusDisp)}-500`;

    return `<tr class="border-b" data-merged="1" data-type="${r.type}" data-id="${r.id}">
      <td class="px-4 py-2">${i + 1}</td>
      <td class="px-4 py-2">${r.location}</td>
      <td class="px-4 py-2">${dateStr} ${time24}</td>
      <td class="px-4 py-2 status ${colorClass}">${statusDisp}</td>
      <td class="px-4 py-2 space-x-2 flex items-center">
        ${locBtn}
        <a href="javascript:void(0);" onclick="openDetailsModal('${r.id}','${r.type}')"><img src="{{ asset('images/details.png') }}" alt="Details" class="w-6 h-6"></a>
      </td>
    </tr>`;
  }).join('');

  if (typeof filterAllReportsTable === 'function') filterAllReportsTable();
}




                // ==================================================
                // Filters
                // ==================================================
                function filterFireReportTable() {
                 const levelFilter = document.getElementById('fireLevelFilter').value.toLowerCase();
                 const statusFilter = document.getElementById('fireStatusFilter').value.toLowerCase();
                 const locationSearch = document.getElementById('fireLocationSearch').value.toLowerCase();


                const mode = document.getElementById('fireDateTimeFilter').value;
                const dateSearch = mode === 'date' ? document.getElementById('fireDateSearch').value : '';
                const timeSearch = mode === 'time' ? document.getElementById('fireTimeSearch').value : '';


                const rows = document.querySelectorAll('#fireReportsBody tr');

                rows.forEach(row => {
                    const report = JSON.parse(row.getAttribute('data-report'));
                    const matchesLevel = !levelFilter || (report.alertLevel && report.alertLevel.toLowerCase() === levelFilter);
                    const matchesStatus = !statusFilter || (report.status && report.status.toLowerCase() === statusFilter);

                    const location = report.exactLocation ? report.exactLocation.toLowerCase() : '';
                    const matchesLocation = !locationSearch || location.includes(locationSearch);
                    const reportDateISO = dateToISO(report.date || '');
                    const matchesDate = !dateSearch || (reportDateISO === dateSearch);


                   const matchesTime = (() => {
                    if (!timeSearch) return true;
                    const t = to24h(report.reportTime || '');
                    return !!t && t === timeSearch;
                    })();

                    row.style.display = (matchesLevel && matchesStatus && matchesLocation && matchesDate && matchesTime) ? '' : 'none';
                });
                }

                        function handleDateTimeFilterChange() {
                const mode = document.getElementById('fireDateTimeFilter').value;
                const d = document.getElementById('fireDateSearch');
                const t = document.getElementById('fireTimeSearch');
                if (mode === 'date') {
                    d.classList.remove('hidden'); t.classList.add('hidden');
                } else if (mode === 'time') {
                    t.classList.remove('hidden'); d.classList.add('hidden');
                } else {
                    d.classList.add('hidden'); t.classList.add('hidden');
                    d.value = ''; t.value = '';            // clear stale values
                }
                filterFireReportTable();                 // always re-evaluate
                }

            function handleOtherDateTimeFilterChange() {
            const mode = document.getElementById('otherDateTimeFilter').value;
            const d = document.getElementById('otherDateSearch');
            const t = document.getElementById('otherTimeSearch');
            if (mode === 'date') {
                d.classList.remove('hidden'); t.classList.add('hidden');
            } else if (mode === 'time') {
                t.classList.remove('hidden'); d.classList.add('hidden');
            } else {
                d.classList.add('hidden'); t.classList.add('hidden');
                d.value = ''; t.value = '';            // clear stale values
            }
            filterOtherEmergencyTable();             // always re-evaluate
            }

            function handleAllDateTimeFilterChange() {
  const mode = document.getElementById('allDateTimeFilter').value;
  const d = document.getElementById('allDateSearch');
  const t = document.getElementById('allTimeSearch');
  if (mode === 'date') { d.classList.remove('hidden'); t.classList.add('hidden'); t.value=''; }
  else if (mode === 'time') { t.classList.remove('hidden'); d.classList.add('hidden'); d.value=''; }
  else { d.classList.add('hidden'); t.classList.add('hidden'); d.value=''; t.value=''; }
  filterAllReportsTable();
}

function filterAllReportsTable() {
  const locQ = (document.getElementById('allLocationSearch')?.value || '').toLowerCase();
  const statusQ = (document.getElementById('allStatusFilter')?.value || '').toLowerCase();
  const mode = document.getElementById('allDateTimeFilter')?.value || 'all';
  const dateQ = mode === 'date' ? (document.getElementById('allDateSearch')?.value || '') : '';
  const timeQ = mode === 'time' ? (document.getElementById('allTimeSearch')?.value || '') : '';

  const rows = document.querySelectorAll('#allReportsBody tr');
  rows.forEach(row => {
    const tds = row.querySelectorAll('td');
    const loc = (tds[1]?.textContent || '').toLowerCase();
    const dtText = (tds[2]?.textContent || '').trim();
    const status = (tds[3]?.textContent || '').toLowerCase();

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

    const okLoc = !locQ || loc.includes(locQ);
    const okStatus = !statusQ || status === statusQ.toLowerCase();

    row.style.display = (okLoc && okStatus && okDate && okTime) ? '' : 'none';
  });
}


function handleSmsDateTimeFilterChange() {
  const mode = document.getElementById('smsDateTimeFilter').value;
  const d = document.getElementById('smsDateSearch');
  const t = document.getElementById('smsTimeSearch');
  if (mode === 'date') {
    d.classList.remove('hidden'); t.classList.add('hidden'); t.value = '';
  } else if (mode === 'time') {
    t.classList.remove('hidden'); d.classList.add('hidden'); d.value = '';
  } else {
    d.classList.add('hidden'); t.classList.add('hidden'); d.value = ''; t.value = '';
  }
  filterSmsReportsTable();
}

function filterSmsReportsTable() {
  const qLoc   = (document.getElementById('smsLocationSearch')?.value || '').toLowerCase();
  const mode   = document.getElementById('smsDateTimeFilter')?.value || 'all';
  const dateQ  = mode === 'date' ? (document.getElementById('smsDateSearch')?.value || '') : '';
  const timeQ  = mode === 'time' ? (document.getElementById('smsTimeSearch')?.value || '') : '';
  const statQ  = (document.getElementById('smsStatusFilter')?.value || '').toLowerCase();

  const rows = document.querySelectorAll('#smsReportsBody tr');
  rows.forEach(row => {
    const tds    = row.querySelectorAll('td');
    const loc    = (tds[1]?.textContent || '').toLowerCase();
    const dtText = (tds[2]?.textContent || '').trim().replace(/\s+/g,' ').trim(); // "yyyy-mm-dd HH:MM[:SS]"
    const status = (tds[3]?.textContent || '').toLowerCase();

    // date check
    let okDate = true;
    if (dateQ) {
      const d = (dtText.split(' ')[0] || '');
      // sms date already yyyy-mm-dd from server; compare directly
      okDate = (d === dateQ);
    }

    // time check
    let okTime = true;
    if (timeQ) {
      const rawTime = (dtText.split(' ')[1] || '');
      // keep seconds if present; compare prefix HH:MM for robustness
      okTime = rawTime.startsWith(timeQ);
    }

    // location + status
    const okLoc    = !qLoc || loc.includes(qLoc);
    const okStatus = !statQ || status === statQ;

    row.style.display = (okLoc && okDate && okTime && okStatus) ? '' : 'none';
  });
}





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

                function normalizeInitialTimes() {
                // Fire rows
                document.querySelectorAll('#fireReportsBody tr').forEach(row => {
                    const report = JSON.parse(row.getAttribute('data-report') || '{}');
                    if (report.reportTime) {
                    // Cell index 3 contains "date time"
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
                }


                function filterOtherEmergencyTable() {
                  const typeFilter = document.getElementById('emergencyTypeFilter').value.toLowerCase();
                    const statusFilter = document.getElementById('otherStatusFilter').value.toLowerCase();
                    const locationSearch = document.getElementById('otherLocationSearch').value.toLowerCase();

                    const mode = document.getElementById('otherDateTimeFilter').value;
                    const dateSearch = mode === 'date' ? document.getElementById('otherDateSearch').value : '';
                    const timeSearch = mode === 'time' ? document.getElementById('otherTimeSearch').value : '';
                    const rows = document.querySelectorAll('#otherEmergencyTableBody tr');

                rows.forEach(row => {
                    const report = JSON.parse(row.getAttribute('data-report'));
                    const matchesType = !typeFilter || (report.emergencyType && report.emergencyType.toLowerCase() === typeFilter);
                    const matchesStatus = !statusFilter || (report.status && report.status.toLowerCase() === statusFilter);

                    const location = report.exactLocation ? report.exactLocation.toLowerCase() : '';
                    const matchesLocation = !locationSearch || location.includes(locationSearch);

                    const reportDateISO = (() => {
                    if (!report.date) return '';
                    const parts = report.date.split('/');
                    if (parts.length === 3) {
                        return `${parts[2].length === 2 ? '20'+parts[2] : parts[2]}-${parts[1].padStart(2,'0')}-${parts[0].padStart(2,'0')}`;
                    }
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

                // ==================================================
                // Incident Type Toggle
                // ==================================================
               function toggleIncidentTables() {
                const v = document.getElementById('incidentType').value;

                // hide all
                document.getElementById('allReportsSection').classList.add('hidden');
                document.getElementById('fireReportsSection').classList.add('hidden');
                document.getElementById('otherEmergencySection').classList.add('hidden');
                document.getElementById('smsReportsSection').classList.add('hidden');

                // show selected
                if (v === 'allReports') {
                    document.getElementById('allReportsSection').classList.remove('hidden');
                    renderAllReports(); // keep All fresh
                } else if (v === 'fireReports') {
                    document.getElementById('fireReportsSection').classList.remove('hidden');
                } else if (v === 'otherEmergency') {
                    document.getElementById('otherEmergencySection').classList.remove('hidden');
                } else if (v === 'smsReports') {
                    document.getElementById('smsReportsSection').classList.remove('hidden');
                }
                }


                // ==================================================
                // Details Modal
                // ==================================================
                                function openDetailsModal(incidentId, reportType) {
                const row = document.getElementById(`reportRow${incidentId}`);
                if (!row) return;

                row.style.backgroundColor = '';
                row.style.color = '';
                const report = JSON.parse(row.getAttribute('data-report'));
                if (!report) return;

                // default: hide SMS extra block
                document.getElementById('smsExtra').classList.add('hidden');

                if (reportType === 'fireReports') {
                    document.getElementById('detailIncidentId').innerText = report.id || 'N/A';
                    document.getElementById('detailName').innerText = report.name || 'N/A';
                    document.getElementById('detailContact').innerText = report.contact || 'N/A';
                    document.getElementById('detailLevel').innerText = report.alertLevel || 'N/A';
                    document.getElementById('detailHousesAffected').innerText = report.numberOfHousesAffected || 'N/A';
                    document.getElementById('detailStartTime').innerText = report.fireStartTime || 'N/A';
                    document.getElementById('detailStatus').innerText = report.status || 'N/A';
                    document.getElementById('detailLocation').innerText = report.exactLocation || 'N/A';
                    document.getElementById('detailDate').innerText = report.date || 'N/A';
                    document.getElementById('detailReportTime').innerText = to24h(report.reportTime) || 'N/A';
                    document.getElementById('fireReportDetails').classList.remove('hidden');
                    document.getElementById('otherEmergencyDetails').classList.add('hidden');

                } else if (reportType === 'otherEmergency') {
                    document.getElementById('detailIncidentIdOther').innerText = report.id || 'N/A';
                    document.getElementById('detailNameOther').innerText = report.name || 'N/A';
                    document.getElementById('detailContactOther').innerText = report.contact || 'N/A';
                    document.getElementById('detailEmergencyType').innerText = report.emergencyType || 'N/A';
                    document.getElementById('detailLocationOther').innerText = report.exactLocation || 'N/A';
                    document.getElementById('detailDateOther').innerText = report.date || 'N/A';
                    document.getElementById('detailReportTimeOther').innerText = to24h(report.reportTime) || 'N/A';
                    document.getElementById('fireReportDetails').classList.add('hidden');
                    document.getElementById('otherEmergencyDetails').classList.remove('hidden');

               } else if (reportType === 'smsReports') {
                    // reuse "other" panel for simple fields
                    document.getElementById('detailIncidentIdOther').innerText = report.id || 'N/A';
                    document.getElementById('detailNameOther').innerText = report.name || 'N/A';
                    document.getElementById('detailContactOther').innerText = report.contact || 'N/A';
                    document.getElementById('detailEmergencyType').innerText = (report.emergencyType || 'SMS Report');
                    document.getElementById('detailLocationOther').innerText = (report.location || report.exactLocation || 'N/A');
                    document.getElementById('detailDateOther').innerText = report.date || 'N/A';
                    document.getElementById('detailReportTimeOther').innerText = to24h(report.time) || report.time || 'N/A';

                    // show panels
                    document.getElementById('fireReportDetails').classList.add('hidden');
                    document.getElementById('otherEmergencyDetails').classList.remove('hidden');
                    document.getElementById('smsExtra').classList.remove('hidden');

                    // SMS extras (no map)
                    document.getElementById('detailSmsStation').innerText = report.fireStationName || 'N/A';
                    document.getElementById('detailSmsReportText').innerText = report.fireReport || report.message || 'N/A';
                    }


                // status buttons
                const statusActionDiv = document.getElementById('statusActionButtons');
                statusActionDiv.innerHTML = '';
                if (report.status !== 'Completed') {
                    const button = document.createElement('button');
                    button.id = `acceptButton${report.id}`;
                    button.className = 'acceptButton px-4 py-2 rounded mt-2 text-white';
                    if (report.status === 'Ongoing') {
                    button.style.backgroundColor = '#22c55e';
                    button.addEventListener('mouseenter', () => button.style.backgroundColor = '#16a34a');
                    button.addEventListener('mouseleave', () => button.style.backgroundColor = '#22c55e');
                    button.textContent = 'Done';
                    } else {
                    button.style.backgroundColor = '#F3C011';
                    button.addEventListener('mouseenter', () => button.style.backgroundColor = '#d1a500');
                    button.addEventListener('mouseleave', () => button.style.backgroundColor = '#F3C011');
                    button.textContent = 'Receive';
                    }
                    button.onclick = () => updateReportStatus(report.id, reportType, report.status === 'Ongoing' ? 'Completed' : 'Ongoing');
                    statusActionDiv.appendChild(button);
                }

                document.getElementById('detailsModal').classList.remove('hidden');
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
                    statusCell.innerText = newStatus;
                    statusCell.classList.remove('text-yellow-500','text-red-500','text-green-500','text-orange-500','text-blue-500');
                    statusCell.classList.add(`text-${newStatus === 'Ongoing' ? 'red' : newStatus === 'Completed' ? 'green' : newStatus === 'Pending' ? 'orange' : newStatus === 'Received' ? 'blue' : 'yellow'}-500`);

                    // choose node
                    let path = null;
                    if (reportType === 'fireReports') path = n.fireReport;
                    else if (reportType === 'otherEmergency') path = n.otherEmergency;
                    else if (reportType === 'smsReports') path = await resolveSmsPathById(incidentId);

                    if (!path) return;

                    firebase.database().ref(`${path}/${incidentId}`).update({ status: newStatus })
                        .then(() => {
                        updateTableStatus?.(incidentId, newStatus);
                        closeDetailsModal();
                        showToast?.(`Status updated to ${newStatus}`);
                        })
                        .catch(console.error);
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
            // ==============================
            // Messaging (station ResponseMessage + incident replies)
            // ==============================

            function stationNodesForReportType(reportType) {
                const n = nodes();
                if (!n) return null;
                return {
                    repliesBase: reportType === 'fireReports' ? `${n.fireReport}` : `${n.otherEmergency}`,
                    stationBase: `${n.base}`,
                    prefix: n.prefix
                };
            }

            function repliesRef(incidentId, reportType) {
                const nn = stationNodesForReportType(reportType);
                if (!nn) return null;
                return firebase.database().ref(`${nn.repliesBase}/${incidentId}/messages`);
            }

            function stationResponsesQuery(incidentId, reportType) {
                const nn = stationNodesForReportType(reportType);
                if (!nn) return null;
                // {Prefix}FireStation/ResponseMessage filtered by incidentId
                return firebase.database()
                    .ref(`${nn.stationBase}/ResponseMessage`)
                    .orderByChild('incidentId')
                    .equalTo(incidentId);
            }

            function getFireStationNameByEmail(email, callback) {
                try {
                    const p = stationPrefixFromEmail(email);
                    if (!p) return callback("Unknown Fire Station");
                    return callback(`${p} Fire Station`);
                } catch (_) {
                    return callback("Unknown Fire Station");
                }
            }

            let currentReportType = 'fireReports';
            let liveListeners = []; // refs to detach on close

            function openMessageModal(incidentId, reportType) {
                currentReportType = reportType;

                currentReport = reportType === 'fireReports'
                    ? (fireReports || []).find(r => r.id === incidentId)
                    : (otherEmergencyReports || []).find(r => r.id === incidentId);

                if (!currentReport) return;

                const modal = document.getElementById('fireMessageModal');
                if (!modal) return;
                modal.classList.remove('hidden');

                document.getElementById('fireMessageIncidentIdValue').innerText = currentReport.id || '';
                document.getElementById('fireMessageNameValue').innerText = currentReport.name || 'No Name Provided';
                document.getElementById('fireMessageContactValue').innerText = currentReport.contact || 'N/A';
                document.getElementById('fireMessageIncidentInput').value = currentReport.id || '';

                try {
                    getFireStationNameByEmail(SESSION_EMAIL, (n) => { currentReport.fireStationName = n || "Unknown Fire Station"; });
                }
                catch (_) { currentReport.fireStationName = "Unknown Fire Station"; }

                // Reset the chat thread to prevent duplication
                resetChatThread();

                // Reset listeners to avoid duplication
                resetListeners();

                try {
                    fetchThread(incidentId, reportType);
                    subscribeThread(incidentId, reportType);
                } catch (_) {}
            }

            function closeFireMessageModal() {
                document.getElementById('fireMessageModal').classList.add('hidden');
                // Detach listeners to prevent duplicates
                resetListeners();
            }

            // Reset the chat thread (empty messages before fetching new ones)
            function resetChatThread() {
                const thread = document.getElementById('fireMessageThread');
                thread.innerHTML = '';  // Clear the thread before rendering new messages
            }

            // Reset listeners to prevent stacking of subscriptions
            function resetListeners() {
                liveListeners.forEach(ref => {
                    try {
                        ref.off();  // Remove the Firebase listener
                    } catch (_) {}
                });
                liveListeners = [];  // Clear the listeners array
            }

            let storedMessages = []; // Store messages after the first fetch

            function fetchThread(incidentId, reportType) {
                // If messages have already been fetched, use the stored messages
                if (storedMessages.length > 0) {
                    renderMessages(storedMessages);
                    return;
                }

                const thread = document.getElementById('fireMessageThread');
                thread.innerHTML = '';  // Clear the thread before fetching new messages

                const qResp = stationResponsesQuery(incidentId, reportType);
                const refRep = repliesRef(incidentId, reportType);

                const pulls = [];

                // Fetch station responses
                if (qResp) {
                    pulls.push(qResp.once('value').then(s => {
                        const out = [];
                        s.forEach(c => {
                            const v = c.val() || {};
                            if (v.responseMessage || v.imageBase64) {
                                const ts = v.timestamp || Date.parse(`${v.responseDate || ''} ${v.responseTime || ''}`) || 0;
                                out.push({ type: 'response', text: v.responseMessage || '', imageBase64: v.imageBase64 || '', audioBase64: v.audioBase64 || '', timestamp: ts });
                            }
                        });
                        return out;
                    }));
                }

                // Fetch citizen replies
                if (refRep) {
                    pulls.push(refRep.orderByChild('timestamp').once('value').then(s => {
                        const out = [];
                        s.forEach(c => {
                            const v = c.val() || {};
                            if (v.type && v.type.toLowerCase() === 'reply') {
                                out.push({ type: 'reply', text: v.text || '', imageBase64: v.imageBase64 || '', audioBase64: v.audioBase64 || '', timestamp: v.timestamp || 0 });
                            }
                        });
                        return out;
                    }));
                }

                // Combine responses and replies, then sort by timestamp
                Promise.all(pulls).then(chunks => {
                    storedMessages = ([]).concat(...chunks);

                    // Sort messages by timestamp (older messages at the top, newer at the bottom)
                    storedMessages.sort((a, b) => (a.timestamp || 0) - (b.timestamp || 0));

                    // Render the sorted messages
                    renderMessages(storedMessages);

                    // Scroll to the bottom of the thread (new messages)
                    thread.scrollTop = thread.scrollHeight;
                });
            }

            // Function to render the stored messages in the correct order
            function renderMessages(messages) {
                const thread = document.getElementById('fireMessageThread');
                thread.innerHTML = '';  // Clear the thread before rendering new messages

                messages.forEach(renderBubble);
            }


            function subscribeThread(incidentId, reportType) {
                // station responses
                const qResp = stationResponsesQuery(incidentId, reportType);
                if (qResp) {
                    const ref = firebase.database().ref(qResp.ref.toString().replace(/^https?:\/\/[^/]+\/|^\/+/, '')); // normalize
                    qResp.on('child_added', snap => {
                        const v = snap.val() || {};
                        if (!v) return;
                        renderBubble({
                            type: 'response',
                            text: v.responseMessage || '',
                            imageBase64: v.imageBase64 || '',
                            audioBase64: v.audioBase64 || '',
                            timestamp: v.timestamp || Date.parse(`${v.responseDate || ''} ${v.responseTime || ''}`) || Date.now()
                        });
                        const thread = document.getElementById('fireMessageThread');
                        thread.scrollTop = thread.scrollHeight;
                    });
                    liveListeners.push(ref);
                }

                // citizen replies
                const repRef = repliesRef(incidentId, reportType);
                if (repRef) {
                    repRef.orderByChild('timestamp').on('child_added', snap => {
                        const v = snap.val() || {};
                        if ((v.type || '').toLowerCase() !== 'reply') return;
                        renderBubble({
                            type: 'reply',
                            text: v.text || '',
                            imageBase64: v.imageBase64 || '',
                            audioBase64: v.audioBase64 || '',
                            timestamp: v.timestamp || 0
                        });
                        const thread = document.getElementById('fireMessageThread');
                        thread.scrollTop = thread.scrollHeight;
                        if (!v.isRead) { snap.ref.child('isRead').set(true).catch(() => {}); }
                    });
                    liveListeners.push(repRef);
                }
            }

            function renderBubble(msg) {
                const thread = document.getElementById('fireMessageThread');
                const existingMessages = thread.querySelectorAll('.message');

                // Prevent duplicate messages by checking content
                const messageExists = Array.from(existingMessages).some(existingMsg => {
                    return existingMsg.innerHTML.includes(msg.text); // Check if the message content is already rendered
                });

                if (messageExists) return; // Don't render if the message already exists

                const el = document.createElement('div');
                el.className = (msg.type === 'response')
                    ? "message bg-blue-500 text-white p-4 rounded-lg my-2 max-w-xs ml-auto text-right" // Fire Station
                    : "message bg-gray-300 text-black p-4 rounded-lg my-2 max-w-xs mr-auto text-left"; // User

                let inner = '';
                if (msg.text) inner += `<div>${escapeHtml(msg.text)}</div>`;
                if (msg.imageBase64) inner += `<img src="data:image/jpeg;base64,${msg.imageBase64}" alt="Image" style="margin-top:8px; max-width:100%; border-radius:8px;" />`;
                if (msg.audioBase64) inner += `<audio controls><source src="data:audio/mp4;base64,${msg.audioBase64}" type="audio/mp4" /></audio>`;

                const ts = msg.timestamp ? new Date(msg.timestamp) : new Date();
                inner += `<small class="text-xs block mt-1">${ts.toLocaleString()}</small>`;
                el.innerHTML = inner;

                thread.appendChild(el);
            }

            function escapeHtml(s) {
                return s.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
            }

            // Submit station reply
            const fireForm = document.getElementById('fireMessageForm');
            if (fireForm) {
                fireForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    if (!currentReport) return;

                    const incidentId = document.getElementById('fireMessageIncidentInput').value;
                    const responseMessage = document.getElementById('fireMessageInput').value.trim();
                    if (!responseMessage) return;

                    const nn = stationNodesForReportType(currentReportType);
                    if (!nn) return;

                    // Pretty station name from your earlier code (set in openMessageModal)
                    const fireStationName = currentReport.fireStationName || `${nn.prefix} Fire Station`;

                    const payload = {
                        prefix: nn.prefix,                        // "Mabini" / "LaFilipina" / "Canocotan"
                        reportType: currentReportType,            // "fireReports" | "otherEmergency"
                        incidentId: incidentId,
                        reporterName: currentReport.name || '',
                        contact: currentReport.contact || '',
                        fireStationName: fireStationName,
                        responseMessage: responseMessage
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


            // ==================================================
            // Location Modal and Map (Using Google Maps for Routing and Geofencing)
            // ==================================================
            async function openLocationModal(reportLat, reportLng) {
                const n = nodes();
                if (!n) return;

                try {
                    const snap = await firebase.database().ref(n.profile).once('value');
                    const meta = snap.val() || {};
                    const stationLatitude = meta.latitude;
                    const stationLongitude = meta.longitude;
                    if (stationLatitude == null || stationLongitude == null) return;

                    updateMapAndGoogle(reportLat, reportLng, stationLatitude, stationLongitude);
                    document.getElementById('locationModal').classList.remove('hidden');
                } catch (e) {
                    console.error('Error fetching station profile:', e);
                }
            }


            // Function to update the map and use Google Maps for routing and geofencing
            function updateMapAndGoogle(reportLat, reportLng, stationLat, stationLng) {
                const mapContainer = document.getElementById('mapContainer');

                // Initialize the Google Map
                const googleMap = new google.maps.Map(mapContainer, {
                    center: { lat: reportLat, lng: reportLng },
                    zoom: 12,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                });

                // Create custom icons for report and station locations
                const reportIcon = {
                    url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png",  // Custom red icon
                    scaledSize: new google.maps.Size(32, 32),  // Adjust size
                };

                const stationIcon = {
                    url: "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png",  // Custom yellow icon
                    scaledSize: new google.maps.Size(32, 32),  // Adjust size
                };

                // Add a marker for the report location with the red icon
                const reportMarker = new google.maps.Marker({
                    position: { lat: reportLat, lng: reportLng },
                    map: googleMap,
                    title: "Report Location",
                    icon: reportIcon,  // Use the red icon for report location
                });

                // Add a marker for the fire station location with the yellow icon
                const stationMarker = new google.maps.Marker({
                    position: { lat: stationLat, lng: stationLng },
                    map: googleMap,
                    title: "Fire Station Location",
                    icon: stationIcon,  // Use the yellow icon for fire station
                });

                // Add a geofence circle around the report location
                const geofenceCircle = new google.maps.Circle({
                    map: googleMap,
                    center: { lat: reportLat, lng: reportLng },
                    radius: 50,  // 1 km radius for geofencing
                    fillColor: "#FF0000",
                    fillOpacity: 0.3,
                    strokeColor: "#FF0000",
                    strokeWeight: 2,
                });

                // Calculate and display the route between the report and fire station
                const directionsService = new google.maps.DirectionsService();
                const directionsRenderer = new google.maps.DirectionsRenderer({
                    map: googleMap,
                    suppressMarkers: true, // We have custom markers
                });

                const request = {
                    origin: { lat: reportLat, lng: reportLng },
                    destination: { lat: stationLat, lng: stationLng },
                    travelMode: google.maps.TravelMode.DRIVING,
                };

                directionsService.route(request, (result, status) => {
                    if (status === google.maps.DirectionsStatus.OK) {
                        directionsRenderer.setDirections(result);
                    } else {
                        console.error("Directions request failed due to " + status);
                    }
                });



                // Google Maps iframe for route (still keeps the route as it is in the iframe)
                const mapSrc = `https://www.google.com/maps/embed/v1/directions?origin=${reportLat},${reportLng}&destination=${stationLat},${stationLng}&key=AIzaSyCjyZfmle9R0c6HJkScAnkjJ0vOs5_a-6E`;
                document.getElementById('mapIframe').setAttribute('src', mapSrc);
            }

            // Close the location modal
            function closeLocationModal() {
                document.getElementById('locationModal').classList.add('hidden');
            }

                </script>



                    @endsection
