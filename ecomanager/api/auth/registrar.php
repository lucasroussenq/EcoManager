<?php
declare(strict_types=1);
require_once __DIR__ . '/../bootstrap.php';

$nome  = $_POST['nome']  ?? '';
$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

if (!$nome || !$email || !$senha) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Preencha todos os campos']);
    exit;
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

try {
   
    $check = $mysqli->prepare('SELECT id_usuario FROM usuario WHERE email = ? LIMIT 1');
    $check->bind_param('s', $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        http_response_code(409);
        echo json_encode(['ok' => false, 'msg' => 'E-mail já cadastrado']);
        exit;
    }
    $check->close();

    
    $stmt = $mysqli->prepare('INSERT INTO usuario (nome, email, senha_hash) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $nome, $email, $senhaHash);
    $stmt->execute();

    echo json_encode(['ok' => true]);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Erro ao cadastrar']);
}