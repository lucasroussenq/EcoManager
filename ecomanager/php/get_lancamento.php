<?php
header('Content-Type: application/json; charset=utf-8');
require 'auth_guard.php';
require 'conexao.php';

$idUsuario = (int)($_SESSION['id_usuario'] ?? 0);
$id        = (int)($_GET['id'] ?? 0);

if ($idUsuario <= 0 || $id <= 0) {
  echo json_encode(['ok'=>false,'msg'=>'Requisição inválida']); exit;
}

$sql = "SELECT 
          l.id_lancamento,
          l.data_mov,
          l.tipo,
          l.id_categoria,
          c.nome AS categoria,
          l.descricao,
          l.valor
        FROM lancamento l
        LEFT JOIN categoria c ON c.id_categoria = l.id_categoria
        WHERE l.id_lancamento = ? AND l.id_usuario = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $id, $idUsuario);
$stmt->execute();
$res  = $stmt->get_result();
$row  = $res->fetch_assoc();

if (!$row) {
  echo json_encode(['ok'=>false,'msg'=>'Lançamento não encontrado']); exit;
}

$row['valor']    = (float)$row['valor'];
$row['data_mov'] = date('Y-m-d', strtotime($row['data_mov']));

echo json_encode(['ok'=>true,'item'=>$row], JSON_UNESCAPED_UNICODE);
