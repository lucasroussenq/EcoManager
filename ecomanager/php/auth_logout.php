<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
$_SESSION = [];
session_destroy();
echo json_encode(['ok'=>true]);
