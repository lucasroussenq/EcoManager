<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../bootstrap.php';
requireAuth();


$id = (int)($_POST['id'] ?? 0);
if (!$id) { echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit; }

$stmt = $mysqli->prepare("DELETE FROM categoria WHERE id_categoria=? AND id_usuario=? LIMIT 1");
$stmt->bind_param("ii", $id, $_SESSION['id_usuario']);
$ok = $stmt->execute();

echo json_encode(['ok'=>$ok]);
