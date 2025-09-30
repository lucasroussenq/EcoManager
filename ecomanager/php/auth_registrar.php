<?php

header('Content-Type: application/json; charset=UTF-8');
require_once 'conexao.php'; // $conn é mysqli

try {
  $dados = [];
  $raw = file_get_contents('php://input');
  if ($raw) {
    $tmp = json_decode($raw, true);
    if (is_array($tmp)) { $dados = $tmp; }
  }
  if (empty($dados)) { $dados = $_POST; } // fallback

  $nome  = trim($dados['nome']  ?? '');
  $email = trim($dados['email'] ?? '');
  $senha = trim($dados['senha'] ?? '');

  if ($nome === '' || $email === '' || $senha === '') {
    echo json_encode(['ok'=>false,'msg'=>'Preencha nome, e-mail e senha.']); exit;
  }

  $stmt = $conn->prepare("SELECT 1 FROM usuario WHERE email = ? LIMIT 1");
  if (!$stmt) { throw new Exception('Prepare falhou (SELECT): '.$conn->error); }
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows > 0) {
    $stmt->close();
    echo json_encode(['ok'=>false,'msg'=>'E-mail já cadastrado.']); exit;
  }
  $stmt->close();

  $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO usuario (nome, email, senha_hash) VALUES (?,?,?)");
  if (!$stmt) { throw new Exception('Prepare falhou (INSERT): '.$conn->error); }
  $stmt->bind_param('sss', $nome, $email, $senha_hash);
  $stmt->execute();
  $stmt->close();

  echo json_encode(['ok'=>true,'msg'=>'Usuário registrado com sucesso!']);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'msg'=>'Erro ao cadastrar','detalhe'=>$e->getMessage()]);
}
