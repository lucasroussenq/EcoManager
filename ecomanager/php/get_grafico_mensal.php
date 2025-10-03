<?php
header('Content-Type: application/json; charset=utf-8');
require 'auth_guard.php';
require 'conexao.php';

$mes = preg_replace('/\D/','', $_GET['mes'] ?? '');
$ym  = ($mes && strlen($mes)==6) ? $mes : date('Ym');

$sql = "SELECT 
          SUM(CASE WHEN l.tipo='RECEITA' THEN l.valor ELSE 0 END) AS receita,
          SUM(CASE WHEN l.tipo='DESPESA' THEN l.valor ELSE 0 END) AS despesa
        FROM lancamento l
        WHERE l.id_usuario=? AND DATE_FORMAT(l.data_mov,'%Y%m') = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $_SESSION['id_usuario'], $ym);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

echo json_encode([
  'ok'=>true,
  'receita'=>(float)($row['receita']??0),
  'despesa'=>(float)($row['despesa']??0)
]);
