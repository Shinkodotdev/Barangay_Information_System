<div class="overflow-x-auto bg-white shadow rounded-lg p-4">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Category</th>
                        <th class="px-4 py-2 text-left">Type</th>
                        <th class="px-4 py-2 text-left">Description</th>
                        <th class="px-4 py-2 text-left">Location</th>
                        <th class="px-4 py-2 text-left">Date & Time</th>
                        <th class="px-4 py-2 text-left">Reporter</th>
                        <th class="px-4 py-2 text-left">Photo</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($incidents as $incident): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2"><?php echo $incident['incident_id']; ?></td>
                            <td class="px-4 py-2"><?php echo $incident['category']; ?></td>
                            <td class="px-4 py-2"><?php echo $incident['type']; ?></td>
                            <td class="px-4 py-2"><?php echo $incident['description']; ?></td>
                            <td class="px-4 py-2"><?php echo $incident['location']; ?></td>
                            <td class="px-4 py-2"><?php echo date('M d, Y h:i A', strtotime($incident['date_time'])); ?>
                            </td>
                            <td class="px-4 py-2">
                                <?php
                                if ($incident['resident_fname']) {
                                    echo $incident['resident_fname'] . ' ' . $incident['resident_lname'] . " (Resident)";
                                } elseif ($incident['nonres_fname']) {
                                    echo $incident['nonres_fname'] . ' ' . $incident['nonres_lname'] . " (Non-Resident)";
                                } else {
                                    echo "Unknown";
                                }
                                ?>
                            </td>
                            <td class="px-4 py-2">
                                <?php if ($incident['photo']): ?>
                                    <img src="../../../uploads/incidents/<?php echo $incident['photo']; ?>"
                                        class="w-16 h-16 object-cover rounded" />
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2">
                                <?php echo $incident['is_archived'] ? 'Archived' : 'Active'; ?>
                            </td>
                            <td class="px-4 py-2 relative">
                                <button type="button" class="px-2 py-1 bg-indigo-600 text-white rounded action-btn"
                                    data-dropdown-id="dropdown-<?php echo $incident['incident_id']; ?>">
                                    Action
                                </button>
                                <div id="dropdown-<?php echo $incident['incident_id']; ?>"
                                    class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg hidden z-30">
                                    <button onclick="openViewModal(<?php echo $incident['incident_id']; ?>)"
                                        class="w-full text-left px-4 py-2 hover:bg-gray-100">View</button>
                                    <button onclick='openEditIncidentModal(<?php
    $incidentData = $incident;
    $incidentData["id"] = $incident["incident_id"]; // ✅ add alias
    echo json_encode($incidentData);
?>)'
    class="w-full text-left px-4 py-2 hover:bg-gray-100">
    Update
</button>


                                    <button class="w-full text-left px-4 py-2 hover:bg-gray-100"
                                        onclick="confirmArchive(<?php echo $incident['incident_id']; ?>, <?php echo $incident['is_archived']; ?>)">
                                        <?php echo $incident['is_archived'] ? 'Unarchive' : 'Archive'; ?>
                                    </button>

                                    <button onclick="downloadIncidentPDF(<?php echo $incident['incident_id']; ?>)"
                                        class="w-full text-left px-4 py-2 hover:bg-gray-100">
                                        Download PDF
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-4 flex justify-center space-x-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&archived=<?php echo $archived; ?>"
                        class="px-3 py-1 rounded border <?php echo $i === $page ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>

        </div>