<?php
$host   = 'localhost';
$user   = 'root';
$pass   = '';
$dbname = 'ecomanager';

$mysqli = new mysqli($host, $user, $pass, $dbname);

if ($mysqli->connect_error) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok'    => false,
        'msg'   => 'Falha na conexão com o banco de dados.'
    ]);
    exit;
}

$mysqli->set_charset('utf8mb4');