<?php
header('Content-Type: application/json; charset=utf-8');
require 'auth_guard.php';
require 'conexao.php';

$idUser = (int)$_SESSION['id_usuario'];
$nome   = trim($_POST['nome'] ?? '');

if ($nome === '') { echo json_encode(['ok'=>false,'msg'=>'Informe o nome da família']); exit; }

$conn->begin_transaction();
try {
  $stmt = $conn->prepare("INSERT INTO familia (nome, dono_id_usuario, criado_em) VALUES (?, ?, NOW())");
  $stmt->bind_param("si", $nome, $idUser);
  $stmt->execute();
  $idFamilia = $conn->insert_id;

  $papel = 'ADMIN';
  $stmt2 = $conn->prepare("INSERT INTO membro_familia (id_familia, id_usuario, papel, entrou_em) VALUES (?, ?, ?, NOW())");
  $stmt2->bind_param("iis", $idFamilia, $idUser, $papel);
  $stmt2->execute();

  $conn->commit();
  echo json_encode(['ok'=>true,'id_familia'=>$idFamilia]);
} catch (mysqli_sql_exception $e) {
  $conn->rollback();
  echo json_encode(['ok'=>false,'msg'=>'Erro ao criar família']);
}
