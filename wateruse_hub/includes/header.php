<?php
require_once __DIR__ . '/functions.php';
$user = current_user();
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | WaterUse Hub</title>
<link rel="stylesheet" href="assets/css/style.css">
<script defer src="assets/js/app.js"></script>
</head>
<body>
<div class="app-shell">
<aside class="sidebar">
  <a class="brand" href="dashboard.php"><span class="brand-mark">💧</span><span>WaterUse <b>Hub</b><small>Kisii Water Utility</small></span></a>
  <div class="nav-label">WORKSPACE</div>
  <nav>
    <a href="dashboard.php">▦ <span>Dashboard</span></a>
    <?php if (in_array($user['role'] ?? '', ['admin','staff'], true)): ?>
      <a href="customers.php">♙ <span>Customers</span></a>
      <a href="readings.php">◷ <span>Meter readings</span></a>
      <a href="bills.php">▤ <span>Billing</span></a>
      <a href="payments.php">₭ <span>Payments</span></a>
      <a href="service.php">⚙ <span>Service actions</span></a>
      <a href="complaints.php">☏ <span>Complaints</span></a>
      <a href="reports.php">▥ <span>Reports</span></a>
    <?php else: ?>
      <a href="my_bills.php">▤ <span>My bills</span></a>
      <a href="my_payments.php">₭ <span>My payments</span></a>
      <a href="my_complaints.php">☏ <span>My complaints</span></a>
    <?php endif; ?>
    <a href="notifications.php">♧ <span>Notifications</span></a>
    <?php if (($user['role'] ?? '') === 'admin'): ?>
      <div class="nav-label">ADMINISTRATION</div>
      <a href="users.php">♙ <span>System users</span></a>
      <a href="settings.php">⚙ <span>Settings</span></a>
    <?php endif; ?>
  </nav>
  <div class="sidebar-bottom"><div class="avatar"><?= e(strtoupper(substr($user['name'] ?? 'U',0,1))) ?></div><div><b><?= e($user['name'] ?? '') ?></b><small><?= e(ucfirst($user['role'] ?? '')) ?></small></div><a class="logout-link" href="logout.php" title="Log out">↗</a></div>
</aside>
<main class="main-area">
<header class="topbar"><div><span class="eyebrow">WATERUSE HUB / KISII</span><h1><?= e($pageTitle) ?></h1></div><div class="top-user"><span class="status-dot"></span><?= e($user['name'] ?? '') ?></div></header>
<div class="content">
<?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
<div class="alert <?= e($f['type']) ?>"><?= e($f['message']) ?></div>
<?php endif; ?>
