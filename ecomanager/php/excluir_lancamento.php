<?php
require '_auth_guard.php';
require 'conexao.php';

$id = (int)($_POST['id'] ?? 0);
$stmt = $conn->prepare("DELETE FROM lancamento WHERE id=? AND id_usuario=?");
$stmt->bind_param('ii', $id, $UID);
$ok = $stmt->execute();

echo json_encode(['ok'=>$ok]);
