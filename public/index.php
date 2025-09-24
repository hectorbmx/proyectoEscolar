<?php
// public/index.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$perPage = 10;
$page = max(1, (int)($_GET['p'] ?? 1));

// Orden
$sort = $_GET['sort'] ?? 'date';
$dir  = strtolower($_GET['dir'] ?? 'desc');
$dir  = $dir === 'asc' ? 'ASC' : 'DESC';

$sortMap = [
  'title'  => 'a.title',
  'author' => 'au.name',
  'date'   => 'a.published_at'
];
$orderBy = $sortMap[$sort] ?? $sortMap['date'];

// Total publicados
$countStmt = $pdo->query("SELECT COUNT(*) FROM articles WHERE status='published' AND published_at IS NOT NULL");
$total = (int)$countStmt->fetchColumn();

// Datos página
$offset = ($page - 1) * $perPage;

$sql = "
SELECT a.id, a.title, a.body, a.published_at, a.cover_image, au.name AS author
FROM articles a
LEFT JOIN authors au ON a.author_id = au.id
WHERE a.status='published' AND a.published_at IS NOT NULL
ORDER BY $orderBy $dir
LIMIT :limit OFFSET :offset
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();

// Util para togglear ASC/DESC en los headers
function nextDir($current) { return strtolower($current) === 'asc' ? 'desc' : 'asc'; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Portada — Mi Blog</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .thumb { width: 160px; height: 110px; object-fit: cover; }
  </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <?php require_once __DIR__ . '/includes/auth.php'; $u = auth_user(); ?>

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="mb-0">Últimos artículos</h1>
    <div class="d-flex gap-2">
      <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
          Ordenar por
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="?<?= qs(['sort'=>'title','dir'=>($sort==='title'?nextDir($dir):'asc'), 'p'=>1]) ?>">Título <?= $sort==='title' ? strtoupper($dir) : '' ?></a></li>
          <li><a class="dropdown-item" href="?<?= qs(['sort'=>'author','dir'=>($sort==='author'?nextDir($dir):'asc'), 'p'=>1]) ?>">Autor <?= $sort==='author' ? strtoupper($dir) : '' ?></a></li>
          <li><a class="dropdown-item" href="?<?= qs(['sort'=>'date','dir'=>($sort==='date'?nextDir($dir):'desc'), 'p'=>1]) ?>">Fecha <?= $sort==='date' ? strtoupper($dir) : '' ?></a></li>
        </ul>
      </div>
      <?php if ($u): ?>
      <a class="btn btn-success" href="/blog/public/admin/">Panel</a>
      <a class="btn btn-outline-secondary" href="/blog/public/admin/logout.php">Salir</a>
    <?php else: ?>
      <a class="btn btn-primary" href="/blog/public/admin/login.php">Login</a>
    <?php endif; ?>
    </div>
  </div>

  <?php if ($total === 0): ?>
    <div class="alert alert-info">No hay artículos publicados.</div>
  <?php endif; ?>

  <?php foreach ($articles as $art): ?>
    <div class="card mb-3 shadow-sm">
      <div class="row g-0 align-items-center">
        <div class="col-auto p-2">
          <?php if ($art['cover_image']): ?>
            <img class="thumb rounded" src="../<?= h($art['cover_image']) ?>" alt="Miniatura">
          <?php else: ?>
            <img class="thumb rounded" src="https://via.placeholder.com/160x110?text=Sin+imagen" alt="Miniatura">
          <?php endif; ?>
        </div>
        <div class="col">
          <div class="card-body">
            <h5 class="card-title mb-1"><?= h($art['title']) ?></h5>
            <p class="card-text text-muted small mb-2">
              Publicado el <?= h(date("d/m/Y", strtotime($art['published_at']))) ?>
              por <?= h($art['author'] ?? 'Anónimo') ?>
            </p>
            <p class="card-text mb-2"><?= nl2br(h(mb_strimwidth($art['body'], 0, 180, '…'))) ?></p>
            <a class="btn btn-primary btn-sm" href="articulo.php?<?= http_build_query(['id'=>$art['id'],'p'=>$page,'sort'=>$sort,'dir'=>$dir]) ?>">Leer más</a>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <?= paginate($page, $perPage, $total) ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
