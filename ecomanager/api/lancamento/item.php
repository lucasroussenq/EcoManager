<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../bootstrap.php';
requireAuth();

$id = (int)($_GET['id'] ?? 0);
$idUsuario = (int)($_SESSION['id_usuario'] ?? 0);

if (!$id || !$idUsuario) {
  echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit;
}

$sql = "SELECT 
          l.id_lancamento, l.id_categoria, l.tipo, l.valor, l.data_mov, l.descricao, l.id_familia
        FROM lancamento l
        WHERE l.id_lancamento = ? AND l.id_usuario = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $id, $idUsuario);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

echo json_encode(['ok' => (bool)$item, 'item' => $item ?: null], JSON_UNESCAPED_UNICODE);
