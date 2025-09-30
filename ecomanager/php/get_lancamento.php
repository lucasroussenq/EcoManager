<?php
require '_auth_guard.php';
require 'conexao.php';

$yyyymm   = trim($_GET['yyyymm'] ?? '');
$tipo     = trim($_GET['tipo'] ?? '');
$categoria= (int)($_GET['categoria'] ?? 0);

$sql = "SELECT l.id, DATE_FORMAT(l.data_mov,'%Y-%m-%d') AS data_mov, l.tipo,
               COALESCE(c.nome,'') AS categoria, l.descricao, l.valor
        FROM lancamento l
        LEFT JOIN categoria c ON c.id = l.id_categoria
        WHERE l.id_usuario = ?";

$types = 'i'; $params = [$UID];

if ($yyyymm!=='') { $sql.=" AND DATE_FORMAT(l.data_mov,'%Y%m') = ?"; $types.='s'; $params[]=$yyyymm; }
if ($tipo==='RECEITA' || $tipo==='DESPESA') { $sql.=" AND l.tipo = ?"; $types.='s'; $params[]=$tipo; }
if ($categoria>0) { $sql.=" AND l.id_categoria = ?"; $types.='i'; $params[]=$categoria; }

$sql .= " ORDER BY l.data_mov DESC, l.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($r = $res->fetch_assoc()) { $data[] = $r; }

echo json_encode(['ok'=>true,'data'=>$data,'pagina'=>1]);
