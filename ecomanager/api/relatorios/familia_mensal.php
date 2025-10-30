<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../bootstrap.php';
requireAuth();

$idUsuario = (int)$_SESSION['id_usuario'];
$mes = preg_replace('/\D/','', $_GET['mes'] ?? '');
$ym  = ($mes && strlen($mes)==6) ? $mes : date('Ym');

// tentar pegar família do cara
$idFamilia = (int)($_GET['id_familia'] ?? 0);
if (!$idFamilia) {
  $st = $mysqli->prepare("SELECT id_familia FROM membro_familia WHERE id_usuario=? LIMIT 1");
  $st->bind_param('i', $idUsuario);
  $st->execute();
  $st->bind_result($idFamilia);
  $st->fetch();
  $st->close();
  if (!$idFamilia) {
    echo json_encode(['ok'=>false,'msg'=>'Usuário não está em nenhuma família.']); 
    exit;
  }
}

$sql = "SELECT 
          SUM(CASE WHEN l.tipo='RECEITA' THEN l.valor ELSE 0 END) AS receitas,
          SUM(CASE WHEN l.tipo='DESPESA' THEN l.valor ELSE 0 END) AS despesas
        FROM lancamento l
        WHERE l.id_familia = ?
          AND DATE_FORMAT(l.data_mov,'%Y%m') = ?";
$st = $mysqli->prepare($sql);
$st->bind_param('is', $idFamilia, $ym);
$st->execute();
$row = $st->get_result()->fetch_assoc();

$receitas = (float)($row['receitas'] ?? 0);
$despesas = (float)($row['despesas'] ?? 0);

echo json_encode([
  'ok'        => true,
  'id_familia'=> $idFamilia,
  'mes'       => $ym,
  'receitas'  => $receitas,
  'despesas'  => $despesas,
  'saldo'     => $receitas - $despesas
]);
