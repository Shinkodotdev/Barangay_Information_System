<?php
require_once "../../config/db.php";

if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$q = "%" . $_GET['q'] . "%";

try {
    $stmt = $pdo->prepare("
        SELECT u.user_id, ud.f_name, ud.m_name, ud.l_name, ud.ext_name
        FROM users u
        INNER JOIN user_details ud ON u.user_id = ud.user_id
        WHERE ud.f_name LIKE ? OR ud.l_name LIKE ? OR ud.m_name LIKE ?
        ORDER BY ud.l_name ASC, ud.f_name ASC
        LIMIT 10
    ");
    $stmt->execute([$q, $q, $q]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [];
    foreach ($results as $row) {
        $m_initial = $row['m_name'] ? strtoupper(substr($row['m_name'], 0, 1)) . "." : "";
        $ext = $row['ext_name'] ? " " . $row['ext_name'] : "";
        $full_name = "{$row['l_name']}, {$row['f_name']} {$m_initial}{$ext}";

        $data[] = [
            "id" => $row['user_id'],
            "text" => $full_name
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($data);
} catch (PDOException $e) {
    echo json_encode([]);
}
