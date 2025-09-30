<?php

header('Content-Type: application/json; charset=utf-8');
session_start();
require_once 'conexao.php';

try {
  $dados = json_decode(file_get_contents('php://input'), true);
  $email = trim($dados['email'] ?? '');
  $senha = trim($dados['senha'] ?? '');

  if ($email === '' || $senha === '') {
    echo json_encode(['ok'=>false,'msg'=>'Preencha todos os campos']); exit;
  }

  $stmt = $conn->prepare("SELECT id_usuario, nome, senha_hash FROM usuario WHERE email = ? LIMIT 1");
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows !== 1) {
    $stmt->close();
    echo json_encode(['ok'=>false,'msg'=>'Usuário não encontrado']); exit;
  }

  $stmt->bind_result($id_usuario, $nome, $senha_hash);
  $stmt->fetch();
  $stmt->close();

  if (!password_verify($senha, $senha_hash)) {
    echo json_encode(['ok'=>false,'msg'=>'Senha incorreta']); exit;
  }

  $_SESSION['usuario_id']   = (int)$id_usuario;
  $_SESSION['usuario_nome'] = $nome;
  $_SESSION['usuario_email']= $email;

  echo json_encode(['ok'=>true,'msg'=>'Login realizado com sucesso']);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'msg'=>'Erro no login','detalhe'=>$e->getMessage()]);
}
