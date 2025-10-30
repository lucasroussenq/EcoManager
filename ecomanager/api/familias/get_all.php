<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../bootstrap.php';
requireAuth();

$id_usuario = (int) $_SESSION['id_usuario'];

$sql = "SELECT f.id_familia, f.nome, m.papel
        FROM familia f
        JOIN membro_familia m ON m.id_familia = f.id_familia
        WHERE m.id_usuario = ?
        ORDER BY f.nome";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

$familias = $res->fetch_all(MYSQLI_ASSOC);

echo json_encode(['ok'=>true,'data'=>$familias], JSON_UNESCAPED_UNICODE);
