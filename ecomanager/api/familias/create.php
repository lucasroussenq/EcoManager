<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap.php';
requireAuth();

$id_usuario   = (int)($_SESSION['id_usuario']   ?? 0);
$data_mov     = $_POST['data_mov']              ?? '';
$descricao    = trim($_POST['descricao']        ?? '');
$id_categoria = (int)($_POST['id_categoria']    ?? 0);
$tipo         = $_POST['tipo']                  ?? '';
$valor        = (float)($_POST['valor']         ?? 0);


if ($id_usuario <= 0 || $data_mov === '' || $descricao === '' || $id_categoria <= 0 || $valor <= 0 ||
    !in_array($tipo, ['ENTRADA', 'SAÍDA'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Preencha todos os campos corretamente.']);
    exit;
}

$sql = "INSERT INTO lancamento
          (id_usuario, id_categoria, tipo, valor, data_mov, descricao, criado_em)
        VALUES (?, ?, ?, ?, ?, ?, NOW())";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('iisdss', $id_usuario, $id_categoria, $tipo, $valor, $data_mov, $descricao);
$ok   = $stmt->execute();

echo json_encode([
    'ok'  => $ok,
    'id'  => $ok ? $mysqli->insert_id : 0
]);