<?php
declare(strict_types=1);

ob_start();

require __DIR__ . '/../bootstrap.php';
requireAuth();

// só ADMIN
if (($_SESSION['perfil'] ?? '') !== 'ADMIN') {
    ob_end_clean();
    http_response_code(403);
    echo 'Acesso negado.';
    exit;
}

// carrega nosso FPDF simplificado
$fpdfPath = __DIR__ . '/../lib/fpdf.php';
if (!file_exists($fpdfPath)) {
    ob_end_clean();
    echo 'FPDF não encontrado em ' . $fpdfPath;
    exit;
}
require $fpdfPath;

// ====== BUSCAR DADOS ======
$mysqli->set_charset('utf8');

$totUsuarios = (int)($mysqli->query("SELECT COUNT(*) AS c FROM usuario")->fetch_assoc()['c'] ?? 0);
$totFamilias = (int)($mysqli->query("SELECT COUNT(*) AS c FROM familia")->fetch_assoc()['c'] ?? 0);

$mesAtual = date('Ym');

$sqlLan = "
    SELECT
        COUNT(*) AS c,
        SUM(CASE WHEN tipo='RECEITA' THEN valor ELSE 0 END) AS receitas,
        SUM(CASE WHEN tipo='DESPESA' THEN valor ELSE 0 END) AS despesas
    FROM lancamento
    WHERE DATE_FORMAT(data_mov, '%Y%m') = ?
";
$st = $mysqli->prepare($sqlLan);
$st->bind_param('s', $mesAtual);
$st->execute();
$dadosMes = $st->get_result()->fetch_assoc() ?? [];

$lanMes   = (int)($dadosMes['c'] ?? 0);
$receitas = (float)($dadosMes['receitas'] ?? 0);
$despesas = (float)($dadosMes['despesas'] ?? 0);
$saldo    = $receitas - $despesas;

// limpa buffer antes de mandar PDF
ob_end_clean();

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="relatorio-consolidado.pdf"');

// ====== MONTAR PDF ======
$pdf = new FPDF();
$pdf->AddPage();

// título
$pdf->SetXY(50, 760);            // (x, y) – quanto menor o y mais pra cima
$pdf->Cell(0, 10, 'EcoManager - Relatorio Consolidado', 0, 1);

// info do topo
$pdf->SetXY(50, 740);
$pdf->Cell(0, 8, 'Gerado em: ' . date('d/m/Y H:i'), 0, 1);

$pdf->SetXY(50, 725);
$pdf->Cell(0, 8, 'Administrador: ' . ($_SESSION['nome'] ?? 'Admin'), 0, 1);

// bloco "visao geral"
$pdf->SetXY(50, 700);
$pdf->Cell(0, 8, 'Visao geral do sistema', 0, 1);

// dados
$y = 680;

$pdf->SetXY(50, $y);
$pdf->Cell(0, 8, 'Total de usuarios: ' . $totUsuarios, 0, 1);
$y -= 15;

$pdf->SetXY(50, $y);
$pdf->Cell(0, 8, 'Total de familias: ' . $totFamilias, 0, 1);
$y -= 15;

$pdf->SetXY(50, $y);
$pdf->Cell(0, 8, 'Lancamentos do mes (' . $mesAtual . '): ' . $lanMes, 0, 1);
$y -= 15;

$pdf->SetXY(50, $y);
$pdf->Cell(0, 8, 'Receitas (mes): R$ ' . number_format($receitas, 2, ',', '.'), 0, 1);
$y -= 15;

$pdf->SetXY(50, $y);
$pdf->Cell(0, 8, 'Despesas (mes): R$ ' . number_format($despesas, 2, ',', '.'), 0, 1);
$y -= 15;

$pdf->SetXY(50, $y);
$pdf->Cell(0, 8, 'Saldo (mes): R$ ' . number_format($saldo, 2, ',', '.'), 0, 1);

// envia
$pdf->Output('I', 'relatorio-consolidado.pdf');
exit;
