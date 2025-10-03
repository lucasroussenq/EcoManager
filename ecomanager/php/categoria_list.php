<?php
header('Content-Type: application/json; charset=utf-8');
require 'auth_guard.php';
require 'conexao.php';

$id = (int)($_GET['id'] ?? 0);

$sql = "SELECT c.id_categoria, c.nome, c.id_natureza, n.nome AS natureza
        FROM categoria c 
        JOIN natureza n ON n.id_natureza=c.id_natureza
        WHERE c.id_usuario = ? 
        ".($id?' AND c.id_categoria=? ':'')."
        ORDER BY c.nome";
$stmt = $conn->prepare($sql);
if ($id) $stmt->bind_param("ii", $_SESSION['id_usuario'], $id);
else $stmt->bind_param("i", $_SESSION['id_usuario']);
$stmt->execute();
$res = $stmt->get_result();
$itens = $res->fetch_all(MYSQLI_ASSOC);

echo json_encode(['ok'=>true,'itens'=>$itens]);
