<?php
header('Content-Type: application/json; charset=utf-8');
require 'auth_guard.php';
require 'conexao.php';

$idUser   = (int)$_SESSION['id_usuario'];
$mes      = trim($_GET['mes'] ?? '');
$tipo     = trim($_GET['tipo'] ?? '');        // RECEITA|DESPESA
$idFam    = (int)($_GET['id_familia'] ?? 0);

$sql = "SELECT c.id_categoria, c.nome AS categoria, SUM(l.valor) AS total
          FROM lancamento l
          JOIN categoria c ON c.id_categoria = l.id_categoria
         WHERE l.id_usuario = ? ";
$types = 'i'; $params = [$idUser];

if ($idFam > 0) { $sql .= " AND l.id_familia = ? "; $types .= 'i'; $params[] = $idFam; }
else           { $sql .= " AND l.id_familia IS NULL "; }

if ($mes !== '') {
  $ano = (int)substr($mes,0,4); $mm = (int)substr($mes,4,2);
  $sql .= " AND YEAR(l.data_mov)=? AND MONTH(l.data_mov)=? ";
  $types .= 'ii'; $params[] = $ano; $params[] = $mm;
}

if ($tipo === 'RECEITA' || $tipo === 'DESPESA') {
  $sql .= " AND l.tipo = ? "; $types .= 's'; $params[] = $tipo;
}

$sql .= " GROUP BY c.id_categoria, c.nome
          ORDER BY total DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($r = $res->fetch_assoc()) $data[] = $r;

echo json_encode(['ok'=>true,'data'=>$data]);
