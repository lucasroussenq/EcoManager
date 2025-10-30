<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
requireAuth();

$id_usuario = (int)($_SESSION['id_usuario'] ?? 0);
$nome       = trim($_POST['nome'] ?? '');

if ($id_usuario <= 0 || $nome === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Dados inválidos']);
    exit;
}

$sql = "INSERT INTO familia (nome, dono_id_usuario, criado_em)
        VALUES (?, ?, NOW())";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('si', $nome, $id_usuario);
$ok = $stmt->execute();

if (!$ok) {
    http_response_code(500);
    echo json_encode([
        'ok'  => false,
        'msg' => 'Erro ao criar família: ' . $stmt->error
    ]);
    exit;
}

echo json_encode([
    'ok'        => true,
    'id_familia'=> $mysqli->insert_id,
    'nome'      => $nome
]);
