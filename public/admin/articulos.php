<?php
require_once __DIR__.'/../includes/db.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';  
auth_require();

$sort = $_GET['sort'] ?? 'date';
$dir  = strtolower($_GET['dir'] ?? 'desc');
$dir  = $dir === 'asc' ? 'ASC' : 'DESC';
$sortMap = ['title'=>'a.title','author'=>'au.name','date'=>'a.published_at','status'=>'a.status'];
$orderBy = $sortMap[$sort] ?? $sortMap['date'];

$stmt = $pdo->query("
  SELECT a.id, a.title, a.status, a.published_at, au.name AS author
  FROM articles a
  LEFT JOIN authors au ON au.id=a.author_id
  ORDER BY $orderBy $dir, a.id DESC
");
$rows = $stmt->fetchAll();

function tdir($d){ return strtolower($d)==='asc'?'desc':'asc'; }
?>
<!DOCTYPE html><html lang="es"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Artículos — Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light"><div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Artículos</h1>
    <div>
      <a class="btn btn-secondary me-2" href="/blog/public/admin/">Panel</a>
      <a class="btn btn-success" href="/blog/public/admin/articulo_nuevo.php">+ Nuevo</a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead>
        <tr>
          <th><a href="?<?= http_build_query(['sort'=>'title','dir'=>($sort==='title'?tdir($dir):'asc')]) ?>">Título</a></th>
          <th><a href="?<?= http_build_query(['sort'=>'author','dir'=>($sort==='author'?tdir($dir):'asc')]) ?>">Autor</a></th>
          <th><a href="?<?= http_build_query(['sort'=>'date','dir'=>($sort==='date'?tdir($dir):'desc')]) ?>">Publicación</a></th>
          <th><a href="?<?= http_build_query(['sort'=>'status','dir'=>($sort==='status'?tdir($dir):'asc')]) ?>">Estado</a></th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['author'] ?? '—') ?></td>
            <td><?= $r['published_at'] ? date('Y-m-d H:i', strtotime($r['published_at'])) : '—' ?></td>
            <td><span class="badge bg-<?= $r['status']==='published'?'success':'secondary' ?>"><?= $r['status'] ?></span></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="/blog/public/admin/articulo_editar.php?id=<?= $r['id'] ?>">Editar</a>
              <form class="d-inline" method="post" action="/blog/public/admin/articulo_eliminar.php" onsubmit="return confirm('¿Eliminar definitivamente?');">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-outline-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <a class="btn btn-outline-secondary" href="/blog/public/">← Ver portada</a>
</div></body></html>
