<?php
require_once __DIR__ . '/../includes/db.php';

$name  = 'Admin';
$email = 'admin@example.com';
$pass  = 'admin123'; // cámbialo luego desde la BD o con otro script

$hash = password_hash($pass, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash,role) VALUES (:n,:e,:h,'admin')");
$stmt->execute([':n'=>$name, ':e'=>$email, ':h'=>$hash]);

echo "Admin creado: $email / $pass";
