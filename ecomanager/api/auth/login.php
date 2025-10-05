<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap.php';

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

if (!$email || !$senha) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Preencha e-mail e senha']);
    exit;
}

$stmt = $mysqli->prepare('SELECT id_usuario, nome, senha_hash FROM usuario WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'E-mail ou senha incorretos']);
    exit;
}

$stmt->bind_result($id, $nome, $hash);
$stmt->fetch();

if (!password_verify($senha, $hash)) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'E-mail ou senha incorretos']);
    exit;
}

$_SESSION['id_usuario'] = $id;
$_SESSION['nome']       = $nome;

echo json_encode(['ok' => true]);