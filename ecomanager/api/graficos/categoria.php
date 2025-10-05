<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../bootstrap.php';
requireAuth();


$mes = preg_replace('/\D/','', $_GET['mes'] ?? '');
$ym  = ($mes && strlen($mes)==6) ? $mes : date('Ym');


$sql = "SELECT c.nome AS categoria, 
               SUM(l.valor) AS total
        FROM lancamento l
        JOIN categoria c ON c.id_categoria = l.id_categoria
        WHERE l.id_usuario = ?
          AND DATE_FORMAT(l.data_mov,'%Y%m') = ?
          AND l.tipo='DESPESA'
        GROUP BY c.id_categoria, c.nome
        ORDER BY total DESC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("is", $_SESSION['id_usuario'], $ym);
$stmt->execute();
$res = $stmt->get_result();

$labels = [];
$valores = [];
while ($row = $res->fetch_assoc()) {
  $labels[] = $row['categoria'];
  $valores[] = (float)$row['total'];
}

echo json_encode(['ok'=>true,'labels'=>$labels,'valores'=>$valores]);
