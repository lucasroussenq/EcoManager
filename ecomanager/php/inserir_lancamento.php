<?php
require '_auth_guard.php';
require 'conexao.php';

$data_mov     = $_POST['data_mov'] ?? '';
$descricao    = trim($_POST['descricao'] ?? '');
$id_categoria = (int)($_POST['id_categoria'] ?? 0);
$tipo         = $_POST['tipo'] ?? '';
$valor        = (float)($_POST['valor'] ?? 0);

$stmt = $conn->prepare("INSERT INTO lancamento (id_usuario,data_mov,descricao,id_categoria,tipo,valor)
                        VALUES (?,?,?,?,?,?)");
$stmt->bind_param('ississ', $UID, $data_mov, $descricao, $id_categoria, $tipo, $valor);
$ok = $stmt->execute();

echo json_encode(['ok'=>$ok, 'id'=>$ok ? $conn->insert_id : 0]);
