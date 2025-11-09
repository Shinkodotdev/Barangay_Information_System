<div class="bg-white shadow-md rounded-xl p-6 mt-8"> 
    <h2 class="text-xl font-bold mb-4">Recent Document Requests</h2>

    <?php if (!empty($documentRequests)): ?>
        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-left p-2">Document</th>
                        <th class="text-left p-2">Status</th>
                        <th class="text-left p-2">Requested At</th>
                        <th class="text-left p-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documentRequests as $doc): ?>
                        <tr class="border-t">
                            <td class="p-2"><?= htmlspecialchars($doc['document_name'] ?? 'N/A') ?></td>
                            <td class="p-2"><?= htmlspecialchars($doc['status'] ?? 'N/A') ?></td>
                            <td class="p-2">
                                <?= !empty($doc['requested_at']) ? date('M d, Y', strtotime($doc['requested_at'])) : 'N/A' ?>
                            </td>
                            <td class="p-2">
                                <?php if (strtolower($doc['status']) === 'approved'): ?>
                                    <a
                                        href="../../components/document_format.php?request_id=<?= urlencode($doc['request_id']) ?>"
                                        target="_blank"
                                        class="inline-block bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">
                                        View
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-600 font-medium">
                                        <?= htmlspecialchars($doc['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-500">No recent document requests.</p>
    <?php endif; ?>
</div>
