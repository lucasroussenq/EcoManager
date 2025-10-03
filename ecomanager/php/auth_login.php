<?php
header('Content-Type: application/json; charset=utf-8');
require 'conexao.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('ECOMANAGERSESSID');
    session_start();
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($email === '' || $senha === '') {
    echo json_encode(['ok'=>false,'msg'=>'Preencha e-mail e senha']);
    exit;
}

$stmt = $conn->prepare("SELECT id_usuario, nome, senha_hash FROM usuario WHERE email=? AND ativo=1 LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($id, $nome, $hash);

if (!$stmt->fetch()) {
    echo json_encode(['ok'=>false,'msg'=>'Usuário não encontrado']);
    exit;
}
$stmt->close();

if (!password_verify($senha, $hash)) {
    echo json_encode(['ok'=>false,'msg'=>'Senha incorreta']);
    exit;
}

$_SESSION['id_usuario'] = $id;
$_SESSION['nome']       = $nome;

echo json_encode(['ok'=>true]);
