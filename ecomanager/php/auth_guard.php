<?php

session_start();
if (!isset($_SESSION['usuario_id'])) {

    if (isset($_GET['json'])) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(401);
    echo json_encode(['ok'=>false,'msg'=>'Não autenticado']);
  } else {

    header('Location: ../auth/login.html');
  }
  exit;
}
