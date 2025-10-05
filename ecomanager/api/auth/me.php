<?php
require __DIR__ . '/../bootstrap.php';
requireAuth();

echo json_encode(['ok'=>true, 'usuario'=>($_SESSION['usuario'] ?? null)]);
