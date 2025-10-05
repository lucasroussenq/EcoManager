<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../bootstrap.php';
requireAuth();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('ECOMANAGERSESSID');
    session_start();
}
if (empty($_SESSION['id_usuario'])) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(401);
    echo json_encode(['ok' => false, 'msg' => 'Não autenticado']);
    exit;
}
