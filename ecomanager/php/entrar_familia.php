<?php
require_once "conexao.php";
require_once "auth_guard.php";
header("Content-Type: application/json");

$uid = $_SESSION["usuario_id"];
$dados = json_decode(file_get_contents("php://input"), true);
$id_familia = (int)($dados["id_familia"] ?? 0);

if (!$id_familia) { echo json_encode(["ok"=>false,"msg"=>"Informe id_familia"]); exit; }

$stmt = $conn->prepare("SELECT 1 FROM familia WHERE id_familia = :f");
$stmt->execute([":f"=>$id_familia]);
if (!$stmt->fetch()) { echo json_encode(["ok"=>false,"msg"=>"Família não encontrada."]); exit; }

$ins = $conn->prepare("INSERT IGNORE INTO membro_familia (id_familia, id_usuario, papel, entrou_em) VALUES (:f,:u,'MEMBRO',NOW())");
$ins->execute([":f"=>$id_familia, ":u"=>$uid]);

echo json_encode(["ok"=>true,"msg"=>"Você entrou na família."]);
