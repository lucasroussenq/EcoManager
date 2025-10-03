<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
if (empty($_SESSION['id_usuario'])) { echo json_encode(['ok'=>false]); exit; }
echo json_encode(['ok'=>true,'id'=>$_SESSION['id_usuario'],'nome'=>($_SESSION['nome']??'')]);
