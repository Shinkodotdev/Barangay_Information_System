<div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Announcements & Events -->
            <a href="announcement_events.php"
                class="bg-yellow-50 border-l-4 border-yellow-400 shadow-lg rounded-xl p-5 flex items-center justify-between hover:shadow-xl transition">
                <div>
                    <h2 class="text-xs sm:text-sm font-medium text-yellow-700 uppercase">Announcements & Events</h2>
                    <p class="text-2xl sm:text-3xl font-extrabold text-yellow-800 mt-2">View</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full text-yellow-500 text-3xl">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
            </a>
            <!-- Health Survey Reports -->
            <a href="health_reports.php"
                class="bg-green-50 border-l-4 border-green-400 shadow-lg rounded-xl p-5 flex items-center justify-between hover:shadow-xl transition">
                <div>
                    <h2 class="text-xs sm:text-sm font-medium text-green-700 uppercase">Health Reports</h2>
                    <p class="text-2xl sm:text-3xl font-extrabold text-green-800 mt-2">View</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full text-green-500 text-3xl">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </a>

            <!-- Request Documents -->
            <div onclick="openRequestModal()"
                class="bg-blue-50 border-l-4 border-blue-400 shadow-lg rounded-xl p-5 flex items-center justify-between cursor-pointer hover:shadow-xl transition">
                <div>
                    <h2 class="text-xs sm:text-sm font-medium text-blue-700 uppercase">Request Document</h2>
                    <p class="text-2xl sm:text-3xl font-extrabold text-blue-800 mt-2">New</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full text-blue-500 text-3xl">
                    <i class="fa-solid fa-file-circle-plus"></i>
                </div>
            </div>
            <!-- Profile -->
            <a href="profile.php"
                class="bg-gray-50 border-l-4 border-gray-400 shadow-lg rounded-xl p-5 flex items-center justify-between hover:shadow-xl transition">
                <div>
                    <h2 class="text-xs sm:text-sm font-medium text-gray-700 uppercase">Profile</h2>
                    <p class="text-2xl sm:text-3xl font-extrabold text-gray-800 mt-2">View</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-full text-gray-500 text-3xl">
                    <i class="fa-solid fa-user"></i>
                </div>
            </a>
            <!-- ✅ Report -->
            <div onclick="openCreateIncidentModal()"
                class="bg-pink-50 border-l-4 border-red-500 shadow-lg rounded-xl p-5 flex items-center justify-between cursor-pointer hover:shadow-xl transition">
                <div>
                    <h2 class="text-xs sm:text-sm font-semibold text-red-700 uppercase tracking-wide">
                        Report Incident
                    </h2>
                    <p class="text-2xl sm:text-3xl font-extrabold text-red-800 mt-1">
                        Report Now
                    </p>
                </div>
                <div class="bg-pink-100 p-4 rounded-full text-red-500">
                    <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                </div>
            </div>
        </div>