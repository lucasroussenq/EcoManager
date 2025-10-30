<?php
// /ecomanager/api/familias/add_membro.php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../bootstrap.php';
requireAuth();

try {
    if (!isset($_SESSION['id_usuario'])) {
        echo json_encode(['ok' => false, 'msg' => 'Não autenticado']);
        exit;
    }

    $idUsuario = (int)$_SESSION['id_usuario'];

    $id_familia = (int)($_POST['id_familia'] ?? 0);
    $email      = trim($_POST['email'] ?? '');
    $papel      = trim($_POST['papel'] ?? 'MEMBRO');

    if ($id_familia <= 0 || $email === '') {
        echo json_encode(['ok' => false, 'msg' => 'Dados incompletos']);
        exit;
    }

    // 1) Ver se a família existe e se o usuário atual pertence a ela
    $stmt = $mysqli->prepare("SELECT 1 FROM familia WHERE id_familia = ?");
    $stmt->bind_param('i', $id_familia);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
      echo json_encode(['ok' => false, 'msg' => 'Família não encontrada']);
      exit;
    }

    // 2) Buscar usuário por e-mail
    $stmt = $mysqli->prepare("SELECT id_usuario, nome, email FROM usuario WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $resUser = $stmt->get_result();

    if ($resUser->num_rows > 0) {
        // usuário já existe
        $user = $resUser->fetch_assoc();
        $idUserAdd = (int)$user['id_usuario'];
    } else {
        // criar usuário rápido
        $nomeNovo = $email;
        $senhaHash = password_hash('123456', PASSWORD_BCRYPT);

        $stmt = $mysqli->prepare(
            "INSERT INTO usuario (nome, email, senha_hash, perfil, ativo, criado_em)
             VALUES (?, ?, ?, 'USUARIO', 1, NOW())"
        );
        $stmt->bind_param('sss', $nomeNovo, $email, $senhaHash);
        $stmt->execute();

        $idUserAdd = $mysqli->insert_id;
    }

    // 3) Inserir na membro_familia (se ainda não estiver)
    $stmt = $mysqli->prepare(
        "SELECT 1 FROM membro_familia WHERE id_familia = ? AND id_usuario = ?"
    );
    $stmt->bind_param('ii', $id_familia, $idUserAdd);
    $stmt->execute();
    $resMF = $stmt->get_result();

    if ($resMF->num_rows === 0) {
        $stmt = $mysqli->prepare(
            "INSERT INTO membro_familia (id_familia, id_usuario, papel, entrou_em)
             VALUES (?, ?, ?, NOW())"
        );
        $stmt->bind_param('iis', $id_familia, $idUserAdd, $papel);
        $stmt->execute();
    }

    echo json_encode(['ok' => true, 'msg' => 'Membro adicionado.']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
}
