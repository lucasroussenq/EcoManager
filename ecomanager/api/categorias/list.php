<?php
// /ecomanager/api/categorias/list.php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header('Content-Type: application/json; charset=utf-8');

try {
    require __DIR__ . '/../bootstrap.php';

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // pode vir ?tipo=Saida  ou  ?tipo=DESPESA  ou  ?id_natureza=2
    $tipo       = $_GET['tipo']        ?? '';
    $idNatureza = $_GET['id_natureza'] ?? '';

    // normaliza o texto
    $tipoNorm = strtoupper(trim($tipo));  // "Saída" -> "SAÍDA"
    $tipoNorm = str_replace('Í', 'I', $tipoNorm); // "SAÍDA" -> "SAIDA"

    $sql   = "SELECT id_categoria AS id, nome, id_natureza FROM categoria";
    $where = [];
    $args  = [];
    $types = '';

    // se veio por texto
    if ($tipoNorm === 'RECEITA' || $tipoNorm === 'ENTRADA') {
        $where[] = "id_natureza = 1";
    } elseif ($tipoNorm === 'DESPESA' || $tipoNorm === 'SAIDA') {
        $where[] = "id_natureza = 2";
    }

    // se veio id_natureza direto
    if ($idNatureza !== '') {
        $where[] = "id_natureza = ?";
        $types  .= 'i';
        $args[]  = (int)$idNatureza;
    }

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY nome';

    $stmt = $mysqli->prepare($sql);

    if ($types !== '') {
        $stmt->bind_param($types, ...$args);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    $cats = [];
    while ($row = $res->fetch_assoc()) {
        $cats[] = [
            'id'          => (int)$row['id'],
            'nome'        => $row['nome'],
            'id_natureza' => (int)$row['id_natureza'],
        ];
    }

    echo json_encode([
        'ok'         => true,
        'categorias' => $cats,
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok'   => false,
        'erro' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
