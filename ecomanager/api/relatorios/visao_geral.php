<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../bootstrap.php';
requireAuth();

// garante que existe
$perfil = $_SESSION['perfil'] ?? 'USER';
if ($perfil !== 'ADMIN') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Apenas ADMIN pode ver este relatório.']);
    exit;
}

// 1) total de usuários
$res1 = $mysqli->query("SELECT COUNT(*) AS tot FROM usuario");
$row1 = $res1->fetch_assoc();
$totUsuarios = (int)($row1['tot'] ?? 0);

// 2) total de famílias
$res2 = $mysqli->query("SELECT COUNT(*) AS tot FROM familia");
$row2 = $res2->fetch_assoc();
$totFamilias = (int)($row2['tot'] ?? 0);

// 3) mês atual (AAAAMM)
$mesAtual = date('Ym');

// 4) lançamentos do mês (todos, não só família)
$stmt3 = $mysqli->prepare("
    SELECT COUNT(*) AS qtde
    FROM lancamento
    WHERE DATE_FORMAT(data_mov,'%Y%m') = ?
");
$stmt3->bind_param('s', $mesAtual);
$stmt3->execute();
$row3 = $stmt3->get_result()->fetch_assoc();
$lancMes = (int)($row3['qtde'] ?? 0);

// 5) receitas e despesas do mês
$stmt4 = $mysqli->prepare("
    SELECT
      SUM(CASE WHEN tipo='RECEITA' THEN valor ELSE 0 END) AS receitas,
      SUM(CASE WHEN tipo='DESPESA' THEN valor ELSE 0 END) AS despesas
    FROM lancamento
    WHERE DATE_FORMAT(data_mov,'%Y%m') = ?
");
$stmt4->bind_param('s', $mesAtual);
$stmt4->execute();
$row4 = $stmt4->get_result()->fetch_assoc();

$receitasMes = (float)($row4['receitas'] ?? 0);
$despesasMes = (float)($row4['despesas'] ?? 0);
$saldoMes    = $receitasMes - $despesasMes;

// resposta final
echo json_encode([
    'ok'               => true,
    'tot_usuarios'     => $totUsuarios,
    'tot_familias'     => $totFamilias,
    'lancamentos_mes'  => $lancMes,
    'receitas_mes'     => $receitasMes,
    'despesas_mes'     => $despesasMes,
    'saldo_mes'        => $saldoMes,
    'mes'              => $mesAtual,
], JSON_UNESCAPED_UNICODE);
