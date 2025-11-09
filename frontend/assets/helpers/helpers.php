<?php
// ✅ Helpers
if (!function_exists('displayOrNA')) {
    function displayOrNA($value) {
        return $value ? htmlspecialchars($value) : "<span class='text-gray-400 italic'>N/A</span>";
    }
}

if (!function_exists('sectionHeader')) {
    function sectionHeader($icon, $title) {
        echo "<h2 class='text-lg font-semibold mb-4 flex items-center text-indigo-700'>
                <i class='fa-solid $icon mr-2'></i> $title
              </h2>";
    }
}
?>
