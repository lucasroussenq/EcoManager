<?php

header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(['ok'=>false,'logado'=>false]); exit;
}
echo json_encode([
  'ok'=>true,
  'logado'=>true,
  'id_usuario'=>$_SESSION['usuario_id'],
  'nome'=>$_SESSION['usuario_nome'] ?? '',
  'email'=>$_SESSION['usuario_email'] ?? ''
]);
