<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../bootstrap.php';
requireAuth();

$id_usuario   = (int)($_SESSION['id_usuario']   ?? 0);
$data_mov     = $_POST['data_mov']              ?? '';
$descricao    = trim($_POST['descricao']        ?? '');
$id_categoria = (int)($_POST['id_categoria']    ?? 0);
$tipo         = $_POST['tipo']                  ?? '';
$valor        = (float)($_POST['valor']         ?? 0);

/* ----------  auto-family  ---------- */
$stmt = $mysqli->prepare('SELECT id_familia FROM membro_familia WHERE id_usuario = ? LIMIT 1');
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$stmt->bind_result($id_familia);
$stmt->fetch();
$stmt->close();

if (!$id_familia) {
    // usuário sem família → cria uma automaticamente
    $nomeFamilia = 'Minha Família';
    $stmt = $mysqli->prepare('INSERT INTO familia (nome, dono_id_usuario, criado_em) VALUES (?, ?, NOW())');
    $stmt->bind_param('si', $nomeFamilia, $id_usuario);
    $stmt->execute();
    $id_familia = $mysqli->insert_id;

    // coloca o criador como ADMIN
    $papel = 'ADMIN';
    $stmt2 = $mysqli->prepare('INSERT INTO membro_familia (id_familia, id_usuario, papel, entrou_em) VALUES (?, ?, ?, NOW())');
    $stmt2->bind_param('iis', $id_familia, $id_usuario, $papel);
    $stmt2->execute();
}
/* ----------  end auto-family  ---------- */

// ---- validação ----
if ($id_usuario <= 0 || $data_mov === '' || $descricao === '' || $id_categoria <= 0 || $valor <= 0 ||
    !in_array($tipo, ['RECEITA', 'DESPESA'], true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Preencha todos os campos corretamente.']);
    exit;
}

$sql = "INSERT INTO lancamento
          (id_usuario, id_familia, id_categoria, tipo, valor, data_mov, descricao, criado_em)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('iiisdss', $id_usuario, $id_familia, $id_categoria, $tipo, $valor, $data_mov, $descricao);
$ok   = $stmt->execute();

echo json_encode([
    'ok'  => $ok,
    'id'  => $ok ? $mysqli->insert_id : 0
]);