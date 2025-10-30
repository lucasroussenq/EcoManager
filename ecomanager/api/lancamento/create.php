<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../bootstrap.php';
requireAuth();

try {
    if (!isset($_SESSION['id_usuario']) || (int)$_SESSION['id_usuario'] <= 0) {
        echo json_encode(['ok' => false, 'msg' => 'Usuário não autenticado ou sessão expirada']);
        exit;
    }

    $id_usuario   = (int)($_SESSION['id_usuario']   ?? 0);
    $data_mov     = $_POST['data_mov']              ?? '';
    $descricao    = trim($_POST['descricao']        ?? '');
    $id_categoria = (int)($_POST['id_categoria']    ?? 0);
    $tipo         = $_POST['tipo']                  ?? '';
    $valor        = isset($_POST['valor']) ? (float)$_POST['valor'] : 0.0;

    // família pode vir vazia
    $id_familia = (isset($_POST['id_familia']) && $_POST['id_familia'] !== '')
        ? (int)$_POST['id_familia']
        : null;

    // normalizar tipo
    $tipo = strtoupper($tipo);
    if ($tipo === 'SAIDA')   $tipo = 'DESPESA';
    if ($tipo === 'ENTRADA') $tipo = 'RECEITA';

    // validação básica
    if (
        $id_usuario <= 0 ||
        $data_mov === '' ||
        $descricao === '' ||
        $id_categoria <= 0 ||
        $valor <= 0 ||
        !in_array($tipo, ['DESPESA', 'RECEITA'], true)
    ) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'msg' => 'Preencha todos os campos corretamente.']);
        exit;
    }

    if ($id_familia === null) {
        // sem família
        $sql = "INSERT INTO lancamento
                  (id_usuario, id_categoria, tipo, valor, data_mov, descricao, criado_em)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param(
            'iisdss',
            $id_usuario,
            $id_categoria,
            $tipo,
            $valor,
            $data_mov,
            $descricao
        );
    } else {
        // com família
        $sql = "INSERT INTO lancamento
                  (id_usuario, id_familia, id_categoria, tipo, valor, data_mov, descricao, criado_em)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param(
            'iiisdss',
            $id_usuario,
            $id_familia,
            $id_categoria,
            $tipo,
            $valor,
            $data_mov,
            $descricao
        );
    }

    $ok = $stmt->execute();

    echo json_encode([
        'ok' => $ok,
        'id' => $ok ? $mysqli->insert_id : 0
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'msg' => $e->getMessage()
    ]);
}
