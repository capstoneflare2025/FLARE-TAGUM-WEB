<!-- Fire Station Details Form (Responsive) -->
<div class="bg-white p-6 sm:p-8 rounded-lg shadow-xl details-panel" style="height: 650px;">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6">Fire Station Details</h1>
    <form method="POST">
        @csrf
        <div class="space-y-6">

            <!-- Our Mission -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center border-b border-gray-300 pb-4">
                <label for="station-mission" class="text-lg font-semibold text-gray-700 mb-2 sm:mb-0 sm:w-1/4">Our Mission</label>
                <textarea id="station-mission" name="station_mission" class="mt-2 sm:mt-0 ml-0 sm:ml-4 w-full sm:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter the mission of the fire station" rows="4" readonly>Our mission is to protect life, property, and the environment by providing high-quality emergency services, fire prevention, and education. We are dedicated to responding to emergencies with the utmost efficiency, professionalism, and compassion, ensuring the safety and well-being of our community 24/7.</textarea>
            </div>

            <!-- Our Vision -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center border-b border-gray-300 pb-4">
                <label for="station-vission" class="text-lg font-semibold text-gray-700 mb-2 sm:mb-0 sm:w-1/4">Our Vision</label>
                <textarea id="station-vission" name="vission" class="mt-2 sm:mt-0 ml-0 sm:ml-4 w-full sm:w-3/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter the vision of the fire station" rows="4" readonly>Our vision is to be a leader in fire and emergency services, recognized for our excellence, innovation, and commitment to public safety. We aim to create a safer and more resilient community by continually enhancing our response capabilities, fostering a culture of professionalism, and implementing state-of-the-art technologies and practices.</textarea>
            </div>

        </div>
    </form>
</div>
