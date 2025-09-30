<?php
require '_auth_guard.php';
require 'conexao.php';

$id           = (int)($_POST['id'] ?? 0);
$data_mov     = $_POST['data_mov'] ?? '';
$descricao    = trim($_POST['descricao'] ?? '');
$id_categoria = (int)($_POST['id_categoria'] ?? 0);
$tipo         = $_POST['tipo'] ?? '';
$valor        = (float)($_POST['valor'] ?? 0);

$stmt = $conn->prepare("UPDATE lancamento
                        SET data_mov=?, descricao=?, id_categoria=?, tipo=?, valor=?
                        WHERE id=? AND id_usuario=?");
$stmt->bind_param('ssisdii', $data_mov, $descricao, $id_categoria, $tipo, $valor, $id, $UID);
$ok = $stmt->execute();

echo json_encode(['ok'=>$ok,'id'=>$id]);
