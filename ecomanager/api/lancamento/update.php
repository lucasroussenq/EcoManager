<?php
// /ecomanager/api/lancamento/update.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../bootstrap.php';
requireAuth();

$idUsuario = (int)($_SESSION['id_usuario'] ?? 0);

$id          = (int)($_POST['id'] ?? 0);
$data_mov    = trim($_POST['data_mov'] ?? '');
$descricao   = trim($_POST['descricao'] ?? '');
$id_categoria= (int)($_POST['id_categoria'] ?? 0);
$tipo        = trim($_POST['tipo'] ?? '');
$valorRaw    = $_POST['valor'] ?? '';
$id_familia  = isset($_POST['id_familia']) && $_POST['id_familia'] !== '' ? (int)$_POST['id_familia'] : null;

// 1) valida sessão e campos básicos
if ($idUsuario <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'Usuário não autenticado']);
    exit;
}

// normalizar valor (caso venha "2.500,00")
$valorNorm = str_replace(['.', ' '], '', $valorRaw);
$valorNorm = str_replace(',', '.', $valorNorm);
$valor = (float)$valorNorm;

if (
    $id <= 0 ||
    $data_mov === '' ||
    $descricao === '' ||
    $id_categoria <= 0 ||
    $valor <= 0 ||
    !in_array($tipo, ['RECEITA', 'DESPESA'], true)
) {
    echo json_encode(['ok' => false, 'msg' => 'Dados inválidos']);
    exit;
}

// 2) montar SQL sem atualizado_em (tua tabela não tem)
$sql = "UPDATE lancamento 
           SET id_categoria = ?, 
               tipo = ?, 
               valor = ?, 
               data_mov = ?, 
               descricao = ?, 
               id_familia = ?
         WHERE id_lancamento = ? 
           AND id_usuario = ?";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['ok' => false, 'msg' => 'Erro ao preparar: ' . $mysqli->error]);
    exit;
}

/*
  Ordem dos ? no SQL:
    1 id_categoria   (i)
    2 tipo           (s)
    3 valor          (d)
    4 data_mov       (s)
    5 descricao      (s)
    6 id_familia     (i) ou NULL
    7 id_lancamento  (i)
    8 id_usuario     (i)
*/

// se não tiver família, precisamos bindar null do jeito certo
if ($id_familia === null) {
    // vamos usar bind_param normal mas passando NULL como int
    // o MySQLi aceita NULL para campo int se o campo permite NULL (e o teu permite)
    $stmt->bind_param(
        'isdssiii',
        $id_categoria,
        $tipo,
        $valor,
        $data_mov,
        $descricao,
        $id_familia, // será NULL
        $id,
        $idUsuario
    );
} else {
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
}

$ok = $stmt->execute();
if (!$ok) {
    echo json_encode(['ok' => false, 'msg' => 'Erro ao atualizar: ' . $stmt->error]);
    exit;
}

if ($stmt->affected_rows === 0) {
    // pode ser porque não mudou nada ou porque o lançamento não é do usuário
    echo json_encode(['ok' => true, 'msg' => 'Nada alterado ou registro não encontrado.']);
    exit;
}

echo json_encode(['ok' => true, 'msg' => 'Lançamento atualizado com sucesso.']);
