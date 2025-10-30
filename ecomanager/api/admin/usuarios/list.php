<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../../bootstrap.php';
requireAuth();

$perfilSessao = $_SESSION['perfil'] ?? $_SESSION['papel'] ?? 'USER';
if ($perfilSessao !== 'ADMIN') {
  http_response_code(403);
  echo json_encode(['ok'=>false,'msg'=>'Apenas ADMIN']); 
  exit;
}

$res = $mysqli->query("SELECT id_usuario, nome, email, perfil, ativo, criado_em FROM usuario ORDER BY nome");
$usuarios = $res->fetch_all(MYSQLI_ASSOC);

echo json_encode(['ok'=>true,'usuarios'=>$usuarios], JSON_UNESCAPED_UNICODE);
