<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../bootstrap.php';
requireAuth();

$idUsuario   = (int)($_SESSION['id_usuario'] ?? 0);
$id          = (int)($_POST['id'] ?? 0);
$data_mov    = trim($_POST['data_mov'] ?? '');
$descricao   = trim($_POST['descricao'] ?? '');
$id_categoria= (int)($_POST['id_categoria'] ?? 0);
$tipo        = trim($_POST['tipo'] ?? '');
$valor       = (float)($_POST['valor'] ?? 0);
$id_familia  = isset($_POST['id_familia']) && $_POST['id_familia'] !== '' ? (int)$_POST['id_familia'] : null;

if ($idUsuario<=0 || $id<=0 || $data_mov==='' || $descricao==='' || $id_categoria<=0 || $valor<=0 || !in_array($tipo,['RECEITA','DESPESA'],true)) {
  echo json_encode(['ok'=>false,'msg'=>'Dados inválidos']);
  exit;
}

$sql = "UPDATE lancamento 
        SET id_categoria = ?, 
            tipo = ?, 
            valor = ?, 
            data_mov = ?, 
            descricao = ?, 
            id_familia = ?, 
            atualizado_em = NOW()
        WHERE id_lancamento = ? AND id_usuario = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param(
  'isdssiii',
  $id_categoria,
  $tipo,
  $valor,
  $data_mov,
  $descricao,
  $id_familia,  
  $id,
  $idUsuario
);
$ok = $stmt->execute();

echo json_encode(['ok'=>$ok]);
