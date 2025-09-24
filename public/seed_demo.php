<?php
require_once __DIR__ . '/includes/db.php';

$authorId = $pdo->query("SELECT id FROM authors LIMIT 1")->fetchColumn();
if (!$authorId) {
  $pdo->exec("INSERT INTO authors (name,email) VALUES ('Autor Demo','autor@example.com')");
  $authorId = $pdo->lastInsertId();
}

for ($i=1; $i<=30; $i++) {
  $stmt = $pdo->prepare("INSERT INTO articles (author_id,title,body,published_at,cover_image,status)
  VALUES (:aid,:t,:b,DATE_SUB(NOW(), INTERVAL :d DAY),NULL,'published')");
  $stmt->execute([
    ':aid'=>$authorId,
    ':t'=>"Artículo demo $i",
    ':b'=>"Contenido de ejemplo del artículo $i.\nEste es texto de demostración.",
    ':d'=>$i
  ]);
}
echo "OK, insertados 30 artículos.";
