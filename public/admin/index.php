<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
auth_require();
$user = auth_user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel — Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Panel de administración</h1>
    <div>
      <span class="me-3">Hola, <?= htmlspecialchars($user['name']) ?></span>
      <a class="btn btn-outline-secondary btn-sm" href="/blog/public/admin/logout.php">Salir</a>
    </div>
  </div>

  <div class="list-group">
    <a class="list-group-item list-group-item-action" href="/blog/public/admin/articulos.php">Gestionar artículos</a>
    <a class="list-group-item list-group-item-action" href="/blog/public/">Ver portada del blog</a>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
