<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../bootstrap.php';
requireAuth();

$uid = (int)$_SESSION['id_usuario'];
$id_familia = (int)($_POST['id_familia'] ?? 0);

if (!$id_familia) { echo json_encode(['ok'=>false,'msg'=>'Informe id_familia']); exit; }

// existe?
$st = $mysqli->prepare('SELECT 1 FROM familia WHERE id_familia=?');
$st->bind_param('i',$id_familia);
$st->execute();
if (!$st->get_result()->fetch_row()) {
  echo json_encode(['ok'=>false,'msg'=>'Família não encontrada']); exit;
}

// inserir se não existir
$st = $mysqli->prepare('INSERT IGNORE INTO membro_familia (id_familia,id_usuario,papel,entrou_em) VALUES (?,?,\'MEMBRO\',NOW())');
$st->bind_param('ii',$id_familia,$uid);
$st->execute();

echo json_encode(['ok'=>true,'msg'=>'Você entrou na família.']);
