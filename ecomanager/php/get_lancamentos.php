<?php
header('Content-Type: application/json; charset=utf-8');
require 'auth_guard.php';
require 'conexao.php';

$idUsuario = (int)($_SESSION['id_usuario'] ?? 0);
if ($idUsuario <= 0) {
  echo json_encode(['ok'=>false,'msg'=>'Não autenticado']); exit;
}

$mes   = trim($_GET['mes'] ?? '');
$tipo  = trim($_GET['tipo'] ?? '');
$idCat = (int)($_GET['id_categoria'] ?? 0);

$sql = "SELECT 
          l.id_lancamento AS id,
          l.data_mov,
          l.tipo,
          l.id_categoria,
          c.nome AS categoria,
          l.descricao,
          l.valor
        FROM lancamento l
        LEFT JOIN categoria c ON c.id_categoria = l.id_categoria
        WHERE l.id_usuario = ?";
$types = 'i';
$params = [$idUsuario];

if ($mes !== '') {
  $sql .= " AND DATE_FORMAT(l.data_mov, '%Y%m') = ?";
  $types .= 's';
  $params[] = $mes;
}
if ($tipo === 'RECEITA' || $tipo === 'DESPESA') {
  $sql .= " AND l.tipo = ?";
  $types .= 's';
  $params[] = $tipo;
}
if ($idCat > 0) {
  $sql .= " AND l.id_categoria = ?";
  $types .= 'i';
  $params[] = $idCat;
}

$sql .= " ORDER BY l.data_mov DESC, l.id_lancamento DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

$linhas = [];
$totRec = 0.0;
$totDesp = 0.0;

while ($r = $res->fetch_assoc()) {
  $r['data_br'] = date('d/m/Y', strtotime($r['data_mov']));
  $r['valor']   = (float)$r['valor'];
  if ($r['tipo'] === 'RECEITA') $totRec += $r['valor']; else $totDesp += $r['valor'];
  $linhas[] = $r;
}

echo json_encode([
  'ok' => true,
  'registros' => $linhas,
  'totais' => [
    'receitas' => $totRec,
    'despesas' => $totDesp,
    'saldo'     => $totRec - $totDesp
  ]
], JSON_UNESCAPED_UNICODE);
