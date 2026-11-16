<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$role = current_user()['role'];
$pageTitle = 'Dashboard';
if ($role === 'customer') {
    $cid = (int)current_user()['customer_id'];
    $q=$pdo->prepare("SELECT COUNT(*) FROM bills WHERE customer_id=?"); $q->execute([$cid]); $billCount=(int)$q->fetchColumn();
    $q=$pdo->prepare("SELECT COALESCE(SUM(balance),0) FROM bills WHERE customer_id=?"); $q->execute([$cid]); $outstanding=(float)$q->fetchColumn();
    $q=$pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE customer_id=?"); $q->execute([$cid]); $paid=(float)$q->fetchColumn();
    $q=$pdo->prepare("SELECT b.*,c.account_number FROM bills b JOIN customers c ON c.id=b.customer_id WHERE b.customer_id=? ORDER BY b.created_at DESC LIMIT 5"); $q->execute([$cid]); $recent=$q->fetchAll();
} else {
    $customers=(int)$pdo->query("SELECT COUNT(*) FROM customers WHERE status='active'")->fetchColumn();
    $q=$pdo->query("SELECT COUNT(*) FROM bills WHERE balance>0"); $unpaid=(int)$q->fetchColumn();
    $revenue=(float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments")->fetchColumn();
    $consumption=(float)$pdo->query("SELECT COALESCE(SUM(consumption),0) FROM bills")->fetchColumn();
    $recent=$pdo->query("SELECT b.*,c.full_name,c.account_number FROM bills b JOIN customers c ON c.id=b.customer_id ORDER BY b.created_at DESC LIMIT 6")->fetchAll();
    $openComplaints=(int)$pdo->query("SELECT COUNT(*) FROM complaints WHERE status IN ('open','in_progress')")->fetchColumn();
}
require 'includes/header.php';
?>
<?php if($role==='customer'): ?>
<div class="welcome-panel"><div><span class="eyebrow light">CUSTOMER PORTAL</span><h2>Hello, <?= e(current_user()['name']) ?>.</h2><p>Here is a quick view of your water account.</p></div><div class="water-art">💧</div></div>
<div class="stats-grid"><div class="stat-card"><span>Total bills</span><strong><?= $billCount ?></strong><small>All billing periods</small></div><div class="stat-card"><span>Outstanding balance</span><strong><?= money($outstanding) ?></strong><small>Amount remaining</small></div><div class="stat-card"><span>Total payments</span><strong><?= money($paid) ?></strong><small>Payments recorded</small></div></div>
<div class="panel"><div class="panel-head"><h3>Recent bills</h3><a href="my_bills.php">View all</a></div>
<table><thead><tr><th>Bill</th><th>Period</th><th>Amount</th><th>Balance</th><th>Status</th></tr></thead><tbody><?php foreach($recent as $r): ?><tr><td><?= e($r['bill_number']) ?></td><td><?= e($r['billing_period']) ?></td><td><?= money($r['total_amount']) ?></td><td><?= money($r['balance']) ?></td><td><span class="pill <?= $r['balance']<=0?'success':'warning' ?>"><?= $r['balance']<=0?'Paid':'Pending' ?></span></td></tr><?php endforeach; ?><?php if(!$recent): ?><tr><td colspan="5" class="empty">No bills available yet.</td></tr><?php endif; ?></tbody></table></div>
<?php else: ?>
<div class="welcome-panel"><div><span class="eyebrow light">OPERATIONS OVERVIEW</span><h2>Good day, <?= e(current_user()['name']) ?>.</h2><p>Monitor customer accounts, consumption and billing activity.</p></div><div class="water-art">💧</div></div>
<div class="stats-grid"><div class="stat-card"><span>Active customers</span><strong><?= $customers ?></strong><small>Registered accounts</small></div><div class="stat-card"><span>Outstanding bills</span><strong><?= $unpaid ?></strong><small>With an unpaid balance</small></div><div class="stat-card"><span>Payments received</span><strong><?= money($revenue) ?></strong><small>All recorded payments</small></div><div class="stat-card"><span>Consumption billed</span><strong><?= number_format($consumption,2) ?> m³</strong><small>Total billed consumption</small></div></div>
<div class="stats-grid compact"><div class="stat-card"><span>Open complaints</span><strong><?= $openComplaints ?></strong><small>Awaiting resolution</small></div><div class="stat-card"><span>Billing rate</span><strong><?= money((float)setting($pdo,'rate_per_m3','100')) ?>/m³</strong><small>Current configurable rate</small></div></div>
<div class="panel"><div class="panel-head"><h3>Recently generated bills</h3><a href="bills.php">View all</a></div><table><thead><tr><th>Bill number</th><th>Customer</th><th>Period</th><th>Consumption</th><th>Amount</th><th>Status</th></tr></thead><tbody><?php foreach($recent as $r): ?><tr><td><?= e($r['bill_number']) ?></td><td><?= e($r['full_name']) ?><small class="table-sub"><?= e($r['account_number']) ?></small></td><td><?= e($r['billing_period']) ?></td><td><?= number_format((float)$r['consumption'],2) ?> m³</td><td><?= money($r['total_amount']) ?></td><td><span class="pill <?= $r['balance']<=0?'success':'warning' ?>"><?= $r['balance']<=0?'Paid':'Pending' ?></span></td></tr><?php endforeach; ?><?php if(!$recent): ?><tr><td colspan="6" class="empty">No bills have been generated yet.</td></tr><?php endif; ?></tbody></table></div>
<?php endif; ?>
<?php require 'includes/footer.php'; ?>
