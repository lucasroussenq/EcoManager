<?php
session_start();
header("Content-type: application/json;charset=utf-8");
session_unset(); session_destroy();
echo json_encode(['ok'=>true]);
