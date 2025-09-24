<?php
require_once __DIR__.'/../includes/db.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/csrf.php';
auth_require();

$id = (int)($_GET['id'] ?? 0);
$art = $pdo->prepare("SELECT * FROM articles WHERE id=:id");
$art->execute([':id'=>$id]);
$art = $art->fetch();
if (!$art) { http_response_code(404); die('No encontrado'); }

$err=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!csrf_validate($_POST['csrf'] ?? '')) { $err[]='CSRF inválido'; }
  $title=trim($_POST['title']??'');
  $body =trim($_POST['body']??'');
  $status=$_POST['status']??'draft';
  if ($title==='') $err[]='Título requerido';
  if ($body==='')  $err[]='Cuerpo requerido';
  if (!in_array($status,['draft','published'],true)) $err[]='Estado inválido';

  // Imagen (opcional)
  $cover = $art['cover_image'];
  if (!empty($_FILES['cover']['name']) && $_FILES['cover']['error']===UPLOAD_ERR_OK) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $_FILES['cover']['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime,['image/jpeg','image/png','image/webp','image/gif'],true)) $err[]='Imagen inválida';
    if ($_FILES['cover']['size']>2*1024*1024) $err[]='Imagen > 2MB';
    if (!$err) {
      $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
      $name= bin2hex(random_bytes(8)).'.'.strtolower($ext);
      $dest = __DIR__ . '/../../storage/uploads/'.$name;
      if (!move_uploaded_file($_FILES['cover']['tmp_name'], $dest)) {
        $err[]='No se pudo guardar la imagen';
      } else {
        $cover = 'storage/uploads/'.$name;
      }
    }
  }
  if (isset($_POST['borrar_imagen'])) { $cover = null; }

  if (!$err) {
    // publicar si antes era borrador
    $published_at = $art['published_at'];
    if ($status==='published' && !$published_at) $published_at = date('Y-m-d H:i:s');
    if ($status==='draft') $published_at = null;

    $stmt = $pdo->prepare("UPDATE articles
      SET title=:t, body=:b, status=:s, published_at=:p, cover_image=:c
      WHERE id=:id");
    $stmt->execute([
      ':t'=>$title, ':b'=>$body, ':s'=>$status,
      ':p'=>$published_at, ':c'=>$cover, ':id'=>$id
    ]);
    header('Location: /blog/public/admin/articulos.php');
    exit;
  }
}
?>
<!DOCTYPE html><html lang="es"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Editar artículo — Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light"><div class="container py-4" style="max-width:900px;">
  <h1 class="mb-3">Editar artículo</h1>
  <?php if ($err): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($err as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data" class="card p-3 shadow-sm">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label">Título</label>
      <input name="title" class="form-control" required value="<?= htmlspecialchars($art['title']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Cuerpo</label>
      <textarea name="body" rows="8" class="form-control" required><?= htmlspecialchars($art['body']) ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Imagen (opcional)</label>
      <?php if ($art['cover_image']): ?>
        <div class="mb-2">
          <img src="/blog/<?= htmlspecialchars($art['cover_image']) ?>" style="max-width:200px" class="img-thumbnail">
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" name="borrar_imagen" id="borrar">
          <label class="form-check-label" for="borrar">Borrar imagen actual</label>
        </div>
      <?php endif; ?>
      <input type="file" name="cover" accept="image/*" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Estado</label>
      <select name="status" class="form-select">
        <option value="draft"     <?= $art['status']==='draft'?'selected':'' ?>>Borrador</option>
        <option value="published" <?= $art['status']==='published'?'selected':'' ?>>Publicado</option>
      </select>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-primary">Guardar</button>
      <a class="btn btn-outline-secondary" href="/blog/public/admin/articulos.php">Cancelar</a>
    </div>
  </form>
</div></body></html>
