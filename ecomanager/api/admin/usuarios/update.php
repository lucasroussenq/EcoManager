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

$id_usuario = (int)($_POST['id_usuario'] ?? 0);
$perfil     = $_POST['perfil'] ?? 'USER';
$ativo      = (int)($_POST['ativo'] ?? 1);

if ($id_usuario <= 0) {
  echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit;
}

if (!in_array($perfil, ['USER','ADMIN'], true)) {
  $perfil = 'USER';
}

$stmt = $mysqli->prepare("UPDATE usuario SET perfil=?, ativo=? WHERE id_usuario=?");
$stmt->bind_param('sii', $perfil, $ativo, $id_usuario);
$ok = $stmt->execute();

echo json_encode(['ok'=>$ok]);
