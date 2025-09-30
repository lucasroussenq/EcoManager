<?php
// php/conexao.php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';            // ajuste se usar senha
$DB_NAME = 'ecomanager';  // confirme o nome do BD

try {
  $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
  $conn->set_charset('utf8mb4');
} catch (Throwable $e) {
  http_response_code(500);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok'=>false,'msg'=>'Falha na conexão com o banco','detalhe'=>$e->getMessage()]);
  exit;
}
