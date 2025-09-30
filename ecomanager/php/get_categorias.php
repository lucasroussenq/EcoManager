<?php
require 'conexao.php';
header("Content-type: application/json;charset=utf-8");

$tipo = $_GET['tipo'] ?? '';
if ($tipo==='RECEITA' || $tipo==='DESPESA') {
  $stmt = $conn->prepare("SELECT id,nome,tipo FROM categoria WHERE ativo=1 AND tipo=? ORDER BY nome");
  $stmt->bind_param('s', $tipo);
} else {
  $stmt = $conn->prepare("SELECT id,nome,tipo FROM categoria WHERE ativo=1 ORDER BY nome");
}
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_all(MYSQLI_ASSOC);
echo json_encode(['ok'=>true,'data'=>$data]);
