<?php
header('Content-Type: application/json; charset=utf-8');
require 'auth_guard.php';
require 'conexao.php';

$id_usuario = $_SESSION['id_usuario'] ?? 0;

$data_mov    = $_POST['data_mov']    ?? '';
$descricao   = trim($_POST['descricao'] ?? '');
$id_categoria= (int)($_POST['id_categoria'] ?? 0);
$tipo        = $_POST['tipo']        ?? '';
$valor       = (float)($_POST['valor'] ?? 0);

if ($data_mov === '' || $descricao === '' || !$id_categoria || $valor <= 0 || ($tipo !== 'RECEITA' && $tipo !== 'DESPESA')) {
  echo json_encode(['ok'=>false,'msg'=>'Preencha todos os campos corretamente.']);
  exit;
}

$sql = "INSERT INTO lancamento
          (id_usuario, id_categoria, tipo, valor, data_mov, descricao, criado_em)
        VALUES (?,?,?,?,?,?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iisdss", $id_usuario, $id_categoria, $tipo, $valor, $data_mov, $descricao);

$ok = $stmt->execute();
echo json_encode(['ok'=>$ok, 'id' => $ok ? $conn->insert_id : 0]);
