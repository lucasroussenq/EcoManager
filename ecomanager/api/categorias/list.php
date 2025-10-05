<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
header('Content-Type: application/json; charset=utf-8');

try {
    require __DIR__ . '/../bootstrap.php';
    if (session_status() === PHP_SESSION_NONE) session_start();

   
    $tipo       = $_GET['tipo']       ?? '';
    $idNatureza = $_GET['id_natureza'] ?? '';

    $uid = $_SESSION['id_usuario'] ?? null;

 
    $sql   = "SELECT id_categoria AS id, nome, id_natureza FROM categoria";
    $where = [];
    $args  = [];
    $types = '';

   
    if ($tipo === 'RECEITA')  $where[] = "id_natureza = 1";
    if ($tipo === 'DESPESA')  $where[] = "id_natureza = 2";

    if ($idNatureza !== '') {
        $where[] = "id_natureza = ?";
        $types  .= 'i';
        $args[]   = (int)$idNatureza;
    }

  
    if ($uid !== null) {
        
        $chk = $mysqli->prepare(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME   = 'categoria'
               AND COLUMN_NAME  = 'id_usuario'"
        );
        $chk->execute();
        $chk->bind_result($cnt);
        $chk->fetch();
        $chk->close();

        if ($cnt) {
            $where[] = "(id_usuario = ? OR id_usuario IS NULL)";
            $types  .= 'i';
            $args[]   = (int)$uid;
        }
    }

    if ($where) $sql .= " WHERE " . implode(' AND ', $where);
    $sql .= " ORDER BY nome";

 
    $stmt = $mysqli->prepare($sql);
    if ($types) $stmt->bind_param($types, ...$args);
    $stmt->execute();
    $res = $stmt->get_result();

    $cats = [];
    while ($row = $res->fetch_assoc()) {
        $cats[] = ['id' => (int)$row['id'], 'nome' => $row['nome']];
    }
    $stmt->close();

    echo json_encode(['ok' => true, 'categorias' => $cats], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
   
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
}