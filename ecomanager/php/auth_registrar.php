<?php
header('Content-Type: application/json; charset=utf-8');
require 'conexao.php';

function getBody() {
  if (!empty($_POST)) return $_POST;
  $raw = file_get_contents('php://input');
  if ($raw) {
    $j = json_decode($raw, true);
    if (is_array($j)) return $j;
    parse_str($raw, $q);
    if (!empty($q)) return $q;
  }
  return [];
}

$B = getBody();
$nome  = trim($B['nome']  ?? '');
$email = trim($B['email'] ?? '');
$senha = (string)($B['senha'] ?? '');

if ($nome==='' || $email==='' || $senha==='') {
  echo json_encode(['ok'=>false,'msg'=>'Preencha nome, e-mail e senha']); exit;
}

$hash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO usuario (nome,email,senha_hash,ativo,criado_em,atualizado_em) VALUES (?,?,?,1,NOW(),NOW())");
$stmt->bind_param("sss", $nome, $email, $hash);
$ok = $stmt->execute();

if (!$ok && $conn->errno == 1062) { echo json_encode(['ok'=>false,'msg'=>'E-mail já cadastrado']); exit; }
if (!$ok) { echo json_encode(['ok'=>false,'msg'=>'Erro ao cadastrar']); exit; }

echo json_encode(['ok'=>true]);
