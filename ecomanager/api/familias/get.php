<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap.php';
requireAuth();

$id_usuario = (int)($_SESSION['id_usuario'] ?? 0);

$sql = "
  SELECT DISTINCT f.id_familia, f.nome
  FROM familia f
  LEFT JOIN membro_familia mf ON mf.id_familia = f.id_familia
  WHERE f.dono_id_usuario = ? OR mf.id_usuario = ?
  ORDER BY f.nome
";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ii', $id_usuario, $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

$familias = [];
while ($row = $res->fetch_assoc()) {
  $familias[] = $row;
}

echo json_encode(['ok' => true, 'data' => $familias]);
