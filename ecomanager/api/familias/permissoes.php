<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap.php';
requireAuth();

$id_familia = (int)($_GET['id_familia'] ?? 0);
header('Content-Type: application/json; charset=utf-8');

if ($id_familia <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'Família inválida']);
    exit;
}

$res = $mysqli->query("SELECT * FROM familia_permissao WHERE id_familia = {$id_familia}");
$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    'ok' => true,
    'data' => $data
]);
