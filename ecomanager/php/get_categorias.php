<?php
header('Content-Type: application/json; charset=utf-8');
require 'auth_guard.php';
require 'conexao.php';

$cats = [];
$res = $conn->query("SELECT id_categoria AS id, nome FROM categoria ORDER BY nome");
while ($row = $res->fetch_assoc()) {
    $cats[] = $row;
}
echo json_encode(['ok' => true, 'categorias' => $cats], JSON_UNESCAPED_UNICODE);
