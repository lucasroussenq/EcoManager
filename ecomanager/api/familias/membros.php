<?php
declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';
requireAuth();

$id_usuario_logado = (int)($_SESSION['id_usuario'] ?? 0);
$id_familia        = (int)($_GET['id_familia'] ?? 0);

if ($id_familia <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Família não informada']);
    exit;
}

$sql = "
    SELECT
        mf.id_usuario,
        u.nome,
        u.email,
        mf.papel
    FROM membro_familia mf
    INNER JOIN usuario u ON u.id_usuario = mf.id_usuario
    WHERE mf.id_familia = ?
    ORDER BY u.nome
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $id_familia);
$stmt->execute();
$res = $stmt->get_result();

$lista = [];
while ($row = $res->fetch_assoc()) {
    $lista[] = $row;
}

echo json_encode([
    'ok'   => true,
    'data' => $lista
]);
