<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
if (!empty($_SESSION['user'])) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT id,name,email,password_hash,role,customer_id,status FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if ($u && $u['status'] === 'active' && password_verify($password, $u['password_hash'])) {
        session_regenerate_id(true);
        unset($u['password_hash'], $u['status']);
        $_SESSION['user'] = $u;
        audit($pdo, 'LOGIN', 'Successful login');
        redirect('dashboard.php');
    }
    $error = 'The email or password is incorrect, or the account is inactive.';
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sign in | WaterUse Hub</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body class="login-page"><div class="login-wrap"><div class="login-brand"><div class="brand-mark big">💧</div><h1>WaterUse <b>Hub</b></h1><p>Water usage tracking & billing</p></div>
<form method="post" class="login-card"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><span class="eyebrow">WELCOME BACK</span><h2>Sign in to your account</h2><p class="muted">Access your WaterUse Hub workspace.</p>
<?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<label>Email address</label><input type="email" name="email" required autocomplete="username" placeholder="you@example.com">
<label>Password</label><input type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
<button class="btn primary full" type="submit">Sign in</button><p class="login-note">Demo administrator: admin@wateruse.local / Admin@123</p></form><small class="muted">WaterUse Hub · Kisii, Kenya</small></div></body></html>
