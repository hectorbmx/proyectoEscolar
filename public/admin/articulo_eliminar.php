<?php
require_once __DIR__.'/../includes/db.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/csrf.php';
auth_require();

if ($_SERVER['REQUEST_METHOD']!=='POST' || !csrf_validate($_POST['csrf'] ?? '')) {
  http_response_code(400); die('Solicitud inválida');
}
$id = (int)($_POST['id'] ?? 0);
if ($id<=0) { http_response_code(400); die('ID inválido'); }

$pdo->prepare("DELETE FROM articles WHERE id=:id")->execute([':id'=>$id]);
header('Location: /blog/public/admin/articulos.php');
exit;
