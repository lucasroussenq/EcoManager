<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../bootstrap.php';
requireAuth();


$id  = (int)($_POST['id_categoria'] ?? 0);
$nome= trim($_POST['nome'] ?? '');
$id_natureza = (int)($_POST['id_natureza'] ?? 0);

if ($nome==='' || !in_array($id_natureza,[1,2])) {
  echo json_encode(['ok'=>false,'msg'=>'Dados inválidos']); exit;
}

if ($id) {
 $stmt = $mysqli->prepare("UPDATE categoria SET nome=?, id_natureza=? WHERE id_categoria=? AND id_usuario=?");
  $stmt->bind_param("siii", $nome, $id_natureza, $id, $_SESSION['id_usuario']);
  $ok = $stmt->execute();
} else {
  $stmt = $mysqli->prepare("INSERT INTO categoria (id_usuario, id_natureza, nome) VALUES (?,?,?)");
  $stmt->bind_param("iis", $_SESSION['id_usuario'], $id_natureza, $nome);
  $ok = $stmt->execute();
}
echo json_encode(['ok'=>$ok]);
