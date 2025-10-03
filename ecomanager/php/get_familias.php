<?php
header('Content-Type: application/json; charset=utf-8');
require 'auth_guard.php';
require 'conexao.php';

$idUser = (int)$_SESSION['id_usuario'];
$sql = "SELECT f.id_familia, f.nome
          FROM familia f
          JOIN membro_familia m ON m.id_familia = f.id_familia
         WHERE m.id_usuario = ?
         ORDER BY f.nome";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUser);
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
while($r = $res->fetch_assoc()) $rows[] = $r;
echo json_encode(['ok'=>true,'data'=>$rows]);
