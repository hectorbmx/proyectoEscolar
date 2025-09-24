<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
  // Nombre de sesión más seguro
  session_name('blog_sess');
  session_start();
}

function auth_user() {
  return $_SESSION['user'] ?? null;
}

function auth_check(): bool {
  return isset($_SESSION['user']);
}

function auth_require() {
  if (!auth_check()) {
    header('Location: /blog/public/admin/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
  }
}

function auth_login(PDO $pdo, string $email, string $password): bool {
  $stmt = $pdo->prepare("SELECT id,name,email,password_hash,role FROM users WHERE email = :email LIMIT 1");
  $stmt->execute([':email'=>$email]);
  $user = $stmt->fetch();
  if ($user && password_verify($password, $user['password_hash'])) {
    // Evitar fijación de sesión
    session_regenerate_id(true);
    $_SESSION['user'] = [
      'id'    => (int)$user['id'],
      'name'  => $user['name'],
      'email' => $user['email'],
      'role'  => $user['role'],
    ];
    return true;
  }
  return false;
}

function auth_logout() {
  $_SESSION = [];
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"],
      $params["secure"], $params["httponly"]);
  }
  session_destroy();
}
