<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../bootstrap.php';
requireAuth();


$idUser = (int)$_SESSION['id_usuario'];

$sql = "SELECT id_recorrente, id_usuario, id_familia, id_categoria, tipo, valor, descricao, prox_data, periodicidade
          FROM recorrente
         WHERE ativo = 1
           AND id_usuario = ?
           AND prox_data <= CURDATE()";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $idUser);
$stmt->execute();
$res = $stmt->get_result();

$gerados = 0;
$mysqli->begin_transaction();
try {
  while ($r = $res->fetch_assoc()) {
    $idRec  = (int)$r['id_recorrente'];
    $idFam  = $r['id_familia'] !== null ? (int)$r['id_familia'] : null;
    $cat    = (int)$r['id_categoria'];
    $tipo   = $r['tipo'];
    $valor  = (float)$r['valor'];
    $desc   = $r['descricao'];
    $data   = $r['prox_data'];
    $per    = $r['periodicidade']; // 'SEMANAL' | 'MENSAL'

    $stmtIns = $mysqli->prepare(
      "INSERT INTO lancamento
       (id_usuario,id_familia,id_categoria,tipo,valor,descricao,data_mov,criado_em)
       VALUES (?,?,?,?,?,?,?,NOW())"
    );
    $stmtIns->bind_param("iiisdss", $idUser, $idFam, $cat, $tipo, $valor, $desc, $data);
    $stmtIns->execute();
    $gerados++;

    if ($per === 'SEMANAL') {
     $stmtUp = $mysqli->prepare("UPDATE recorrente SET prox_data = DATE_ADD(prox_data, INTERVAL 7 DAY) WHERE id_recorrente = ?");} 
     else {
     $stmtUp = $mysqli->prepare("UPDATE recorrente SET prox_data = DATE_ADD(prox_data, INTERVAL 1 MONTH) WHERE id_recorrente = ?");}
    $stmtUp->bind_param("i", $idRec);
    $stmtUp->execute();
  }

 $mysqli->commit();
  echo json_encode(['ok'=>true,'gerados'=>$gerados]);
} catch (mysqli_sql_exception $e) {
 $mysqli->rollback()
  echo json_encode(['ok'=>false,'msg'=>'Erro ao gerar recorrentes']);
}
