<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('ECOMANAGERSESSID');
    session_start();
}

require_once __DIR__ . '/../config/conexao.php';

if (!isset($mysqli)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'msg' => 'Conexão com banco não configurada']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

function requireAuth(): void
{
    if (empty($_SESSION['id_usuario'])) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'msg' => 'Não autenticado'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}