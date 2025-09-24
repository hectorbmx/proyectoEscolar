<?php
// public/articulo.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); die('Artículo inválido'); }

// Carga artículo
$stmt = $pdo->prepare("
  SELECT a.*, au.name AS author
  FROM articles a
  LEFT JOIN authors au ON a.author_id = au.id
  WHERE a.id = :id AND a.status='published'
");
$stmt->execute([':id'=>$id]);
$art = $stmt->fetch();
if (!$art) { http_response_code(404); die('Artículo no encontrado'); }

// Prev/Next por fecha de publicación
$prev = $pdo->prepare("
  SELECT id, title FROM articles
  WHERE status='published' AND published_at > :pub
  ORDER BY published_at ASC LIMIT 1
");
$prev->execute([':pub'=>$art['published_at']]);
$prev = $prev->fetch();

$next = $pdo->prepare("
  SELECT id, title FROM articles
  WHERE status='published' AND published_at < :pub
  ORDER BY published_at DESC LIMIT 1
");
$next->execute([':pub'=>$art['published_at']]);
$next = $next->fetch();

// Preserva parámetros de portada
$p   = (int)($_GET['p'] ?? 1);
$sort= $_GET['sort'] ?? 'date';
$dir = $_GET['dir'] ?? 'desc';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= h($art['title']) ?> — Mi Blog</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <a class="btn btn-outline-secondary btn-sm" href="index.php?<?= http_build_query(['p'=>$p,'sort'=>$sort,'dir'=>$dir]) ?>">← Volver a la portada</a>
    <div class="btn-group">
      <?php if ($prev): ?>
        <a class="btn btn-outline-primary btn-sm" href="articulo.php?<?= http_build_query(['id'=>$prev['id'],'p'=>$p,'sort'=>$sort,'dir'=>$dir]) ?>">‹ <?= h(mb_strimwidth($prev['title'],0,18,'…')) ?></a>
      <?php else: ?>
        <button class="btn btn-outline-secondary btn-sm" disabled>‹ Anterior</button>
      <?php endif; ?>
      <?php if ($next): ?>
        <a class="btn btn-outline-primary btn-sm" href="articulo.php?<?= http_build_query(['id'=>$next['id'],'p'=>$p,'sort'=>$sort,'dir'=>$dir]) ?>"><?= h(mb_strimwidth($next['title'],0,18,'…')) ?> ›</a>
      <?php else: ?>
        <button class="btn btn-outline-secondary btn-sm" disabled>Siguiente ›</button>
      <?php endif; ?>
    </div>
  </div>

  <article class="card shadow-sm">
    <?php if ($art['cover_image']): ?>
      <img src="../<?= h($art['cover_image']) ?>" class="card-img-top" alt="Portada">
    <?php endif; ?>
    <div class="card-body">
      <h1 class="card-title"><?= h($art['title']) ?></h1>
      <p class="text-muted">
        Publicado el <?= h(date("d/m/Y", strtotime($art['published_at']))) ?>
        por <?= h($art['author'] ?? 'Anónimo') ?>
      </p>
      <div class="card-text"><?= nl2br(h($art['body'])) ?></div>
    </div>
  </article>

  <div class="d-flex justify-content-between mt-3">
    <a class="btn btn-outline-secondary" href="index.php?<?= http_build_query(['p'=>$p,'sort'=>$sort,'dir'=>$dir]) ?>">← Volver a la portada</a>
    <div>
      <?php if ($prev): ?>
        <a class="btn btn-outline-primary" href="articulo.php?<?= http_build_query(['id'=>$prev['id'],'p'=>$p,'sort'=>$sort,'dir'=>$dir]) ?>">‹ Artículo anterior</a>
      <?php endif; ?>
      <?php if ($next): ?>
        <a class="btn btn-primary" href="articulo.php?<?= http_build_query(['id'=>$next['id'],'p'=>$p,'sort'=>$sort,'dir'=>$dir]) ?>">Siguiente artículo ›</a>
      <?php endif; ?>
    </div>
  </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
