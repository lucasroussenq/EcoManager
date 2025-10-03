<?php
header('Content-Type: application/json; charset=utf-8');
require 'auth_guard.php';
require 'conexao.php';

$id = (int)($_GET['id'] ?? 0);
$idUsuario = (int)($_SESSION['id_usuario'] ?? 0);

if (!$id) { echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit; }

$sql = "SELECT 
          l.id_lancamento,
          l.id_usuario,
          l.id_categoria,
          l.id_familia,
          l.tipo,
          l.valor,
          DATE(l.data_mov) AS data_mov,
          l.descricao
        FROM lancamento l
        WHERE l.id_lancamento = ? AND l.id_usuario = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $id, $idUsuario);
$stmt->execute();
$res = $stmt->get_result();
$item = $res->fetch_assoc();

if (!$item) { echo json_encode(['ok'=>false,'msg'=>'Lançamento não encontrado.']); exit; }

$item['valor'] = (float)$item['valor'];

echo json_encode(['ok'=>true,'item'=>$item], JSON_UNESCAPED_UNICODE);
