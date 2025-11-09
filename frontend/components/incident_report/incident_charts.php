<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
            <div class="bg-white shadow rounded-lg p-4 w-full h-64 sm:h-80 md:h-96">
                <h3 class="text-lg font-semibold mb-2">Incidents by Category</h3>
                <canvas id="categoryChart"></canvas>
            </div>
            <div class="bg-white shadow rounded-lg p-4 w-full h-64 sm:h-80 md:h-96">
                <h3 class="text-lg font-semibold mb-2">Incidents by Type</h3>
                <canvas id="typeChart"></canvas>
            </div>
        </div>
        <div class="flex justify-end mb-4 space-x-2">
            <a href="?archived=0"
                class="px-4 py-2 rounded <?php echo $archived ? 'bg-white text-gray-700' : 'bg-indigo-600 text-white'; ?>">Active</a>
            <a href="?archived=1"
                class="px-4 py-2 rounded <?php echo $archived ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'; ?>">Archived</a>
        </div>