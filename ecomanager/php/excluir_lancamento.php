<?php
header('Content-Type: application/json; charset=utf-8');
require 'auth_guard.php';
require 'conexao.php';

$idUser = (int)$_SESSION['id_usuario'];
$id     = (int)($_POST['id'] ?? 0);

if ($id <= 0) { echo json_encode(['ok'=>false]); exit; }

$stmt = $conn->prepare("DELETE FROM lancamento WHERE id_lancamento = ? AND id_usuario = ?");
$stmt->bind_param("ii", $id, $idUser);
$ok = $stmt->execute();
echo json_encode(['ok'=>$ok]);
