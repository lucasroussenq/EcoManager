<?php
require_once "conexao.php";
require_once "auth_guard.php";

header("Content-Type: application/json");

$uid = $_SESSION["usuario_id"];
$dados = json_decode(file_get_contents("php://input"), true);

$id_categoria = (int)($dados["id_categoria"] ?? 0);
$tipo         = trim($dados["tipo"] ?? "");  // RECEITA|DESPESA
$descricao    = trim($dados["descricao"] ?? "");
$valor        = (float)($dados["valor"] ?? 0);
$periodo      = trim($dados["periodicidade"] ?? "MENSAL"); // SEMANAL|MENSAL|SEMESTRAL|ANUAL
$dia_ref      = isset($dados["dia_ref"]) ? (int)$dados["dia_ref"] : null; // para MENSAL
$id_familia   = isset($dados["id_familia"]) && $dados["id_familia"] !== "" ? (int)$dados["id_familia"] : null;
$prox_data    = trim($dados["prox_data"] ?? ""); // YYYY-MM-DD

if (!$id_categoria || !$tipo || !$descricao || $valor <= 0 || !$prox_data) {
  echo json_encode(["ok"=>false, "msg"=>"Preencha todos os campos obrigatórios."]);
  exit;
}

$sql = "INSERT INTO lancamento_recorrente
(id_usuario, id_familia, id_categoria, tipo, descricao, valor, periodicidade, dia_ref, prox_data, ativo)
VALUES
(:uid, :fam, :cat, :tipo, :desc, :val, :per, :dia, :prox, 1)";

$stmt = $conn->prepare($sql);
$stmt->execute([
  ":uid"=>$uid, ":fam"=>$id_familia, ":cat"=>$id_categoria, ":tipo"=>$tipo,
  ":desc"=>$descricao, ":val"=>$valor, ":per"=>$periodo, ":dia"=>$dia_ref, ":prox"=>$prox_data
]);

echo json_encode(["ok"=>true, "msg"=>"Recorrente criado."]);
