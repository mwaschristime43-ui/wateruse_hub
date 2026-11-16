<?php
require_once __DIR__.'/config/database.php';
require_once __DIR__.'/includes/functions.php';
$count=(int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
$message='';
if($count===0){
 $hash=password_hash('Admin@123',PASSWORD_DEFAULT);
 $s=$pdo->prepare("INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,'admin')");
 $s->execute(['System Administrator','admin@wateruse.local',$hash]);
 $message='Administrator account created.';
}else{$message='An administrator account already exists. No changes made.';}
?>
<!doctype html><html><head><meta charset="utf-8"><link rel="stylesheet" href="assets/css/style.css"><title>WaterUse Hub setup</title></head><body class="login-page"><div class="login-wrap"><div class="panel"><h2><?=e($message)?></h2><p>Login email: <b>admin@wateruse.local</b></p><p>Temporary password: <b>Admin@123</b></p><p class="alert error">Security: delete setup_admin.php from your project folder immediately after running this page.</p><a class="btn primary" href="login.php">Go to login</a></div></div></body></html>
