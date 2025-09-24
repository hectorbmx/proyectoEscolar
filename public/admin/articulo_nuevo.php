<?php
require_once __DIR__.'/../includes/db.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/csrf.php';
auth_require();

$err=[]; $msg=null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!csrf_validate($_POST['csrf'] ?? '')) { $err[]='CSRF inválido'; }
  $title=trim($_POST['title']??'');
  $body =trim($_POST['body']??'');
  $status=$_POST['status']??'draft';

  if ($title==='') $err[]='Título requerido';
  if ($body==='')  $err[]='Cuerpo requerido';
  if (!in_array($status,['draft','published'],true)) $err[]='Estado inválido';

  // Autor = usuario logueado (o crea authors si no existe)
  $uid = auth_user()['id'];
  // asegurar author
  $aid = $pdo->query("SELECT id FROM authors WHERE email=(SELECT email FROM users WHERE id=$uid) LIMIT 1")->fetchColumn();
  if (!$aid) {
    $u = $pdo->query("SELECT name,email FROM users WHERE id=$uid")->fetch();
    $st = $pdo->prepare("INSERT INTO authors(name,email) VALUES(:n,:e)");
    $st->execute([':n'=>$u['name'],':e'=>$u['email']]);
    $aid = $pdo->lastInsertId();
  }

  // Subida de imagen (opcional)
  $coverPath = null;
  if (!empty($_FILES['cover']['name']) && $_FILES['cover']['error']===UPLOAD_ERR_OK) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $_FILES['cover']['tmp_name']);
    finfo_close($finfo);
    $ok = in_array($mime, ['image/jpeg','image/png','image/webp','image/gif'], true);
    if (!$ok) $err[]='Imagen no válida (solo jpg/png/webp/gif)';
    if ($_FILES['cover']['size'] > 2*1024*1024) $err[]='Imagen > 2MB';

    if (!$err) {
      $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
      $name= bin2hex(random_bytes(8)).'.'.strtolower($ext);
      $dest = __DIR__ . '/../../storage/uploads/'.$name;
      if (!move_uploaded_file($_FILES['cover']['tmp_name'], $dest)) {
        $err[]='No se pudo guardar la imagen';
      } else {
        $coverPath = 'storage/uploads/'.$name; // se almacena ruta relativa
      }
    }
  }

  if (!$err) {
    $published_at = ($status==='published') ? date('Y-m-d H:i:s') : null;
    $stmt = $pdo->prepare("INSERT INTO articles(author_id,title,body,published_at,cover_image,status)
      VALUES(:aid,:t,:b,:p,:c,:s)");
    $stmt->execute([
      ':aid'=>$aid, ':t'=>$title, ':b'=>$body,
      ':p'=>$published_at, ':c'=>$coverPath, ':s'=>$status
    ]);
    header('Location: /blog/public/admin/articulos.php');
    exit;
  }
}
?>
<!DOCTYPE html><html lang="es"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Nuevo artículo — Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="bg-light"><div class="container py-4" style="max-width:900px;">
  <h1 class="mb-3">Nuevo artículo</h1>
  <?php if ($err): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach($err as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data" class="card p-3 shadow-sm">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label">Título</label>
      <input name="title" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Cuerpo</label>
      <textarea name="body" rows="8" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Imagen (opcional, máx 2MB)</label>
      <input type="file" name="cover" accept="image/*" class="form-control">
    </div>
    <div class="mb-3">
      <label class="form-label">Estado</label>
      <select name="status" class="form-select">
        <option value="draft">Borrador</option>
        <option value="published">Publicado</option>
      </select>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-primary">Guardar</button>
      <a class="btn btn-outline-secondary" href="/blog/public/admin/articulos.php">Cancelar</a>
    </div>
  </form>
</div></body></html>
