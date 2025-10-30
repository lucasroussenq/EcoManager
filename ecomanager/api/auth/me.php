<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/../bootstrap.php';
requireAuth();

echo json_encode([
  'ok'        => true,
  'id_usuario'=> $_SESSION['id_usuario'] ?? null,
  'email'     => $_SESSION['email']      ?? null,
  'nome'      => $_SESSION['nome']       ?? null,
  'papel'     => $_SESSION['perfil'] 
                  ?? $_SESSION['papel'] 
                  ?? 'USER'
]);
