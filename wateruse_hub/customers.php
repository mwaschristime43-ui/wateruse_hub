<?php
require_once __DIR__.'/config/database.php'; require_once __DIR__.'/includes/functions.php'; require_role(['admin','staff']); verify_csrf();
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=(int)($_POST['id']??0); $name=trim($_POST['full_name']??''); $phone=trim($_POST['phone']??''); $email=trim($_POST['email']??''); $address=trim($_POST['address']??''); $meter=trim($_POST['meter_number']??''); $status=$_POST['status']??'active';
    if($name===''||$phone===''||$meter===''){ flash('error','Name, phone and meter number are required.'); redirect('customers.php'); }
    try {
      if($id){$s=$pdo->prepare("UPDATE customers SET full_name=?,phone=?,email=?,address=?,meter_number=?,status=? WHERE id=?");$s->execute([$name,$phone,$email?:null,$address,$meter,$status,$id]);audit($pdo,'UPDATE CUSTOMER',"Customer #$id");}
      else {$account='WH'.date('ym').strtoupper(bin2hex(random_bytes(2)));$s=$pdo->prepare("INSERT INTO customers(account_number,full_name,phone,email,address,meter_number,status) VALUES(?,?,?,?,?,?,?)");$s->execute([$account,$name,$phone,$email?:null,$address,$meter,$status]);audit($pdo,'CREATE CUSTOMER',$account);}
      flash('success','Customer record saved successfully.');
    } catch(PDOException $e){flash('error','Could not save customer. Check that the meter number, phone and email are not already registered.');}
    redirect('customers.php');
}
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare("SELECT * FROM customers WHERE id=?");$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$rows=$pdo->query("SELECT * FROM customers ORDER BY created_at DESC")->fetchAll();$pageTitle='Customer management';require 'includes/header.php';
?>
<div class="page-actions"><p class="muted">Register and maintain WaterUse Hub customer accounts.</p><button class="btn primary" type="button" onclick="document.getElementById('customer-form').scrollIntoView({behavior:'smooth'})">＋ Add customer</button></div>
<div class="panel"><div class="panel-head"><h3>Customer accounts</h3><input class="search-input" data-table-search="customers-table" placeholder="Search customers..."></div><div class="table-wrap"><table id="customers-table"><thead><tr><th>Account</th><th>Customer</th><th>Phone</th><th>Meter number</th><th>Address</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?=e($r['account_number'])?></td><td><?=e($r['full_name'])?><small class="table-sub"><?=e($r['email'])?></small></td><td><?=e($r['phone'])?></td><td><?=e($r['meter_number'])?></td><td><?=e($r['address'])?></td><td><span class="pill <?=$r['status']==='active'?'success':'danger'?>"><?=e(ucfirst($r['status']))?></span></td><td><a class="text-link" href="?edit=<?=$r['id']?>#customer-form">Edit</a></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="7" class="empty">No customers registered.</td></tr><?php endif; ?></tbody></table></div></div>
<div class="panel" id="customer-form"><div class="panel-head"><h3><?=$edit?'Edit customer':'Register a customer'?></h3><?php if($edit):?><a href="customers.php">Cancel</a><?php endif;?></div>
<form method="post" class="form-grid"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=e($edit['id']??'')?>"><div><label>Full name *</label><input name="full_name" required value="<?=e($edit['full_name']??'')?>"></div><div><label>Phone number *</label><input name="phone" required value="<?=e($edit['phone']??'')?>"></div><div><label>Email</label><input type="email" name="email" value="<?=e($edit['email']??'')?>"></div><div><label>Meter number *</label><input name="meter_number" required value="<?=e($edit['meter_number']??'')?>"></div><div class="span-2"><label>Physical address / area</label><input name="address" value="<?=e($edit['address']??'')?>"></div><div><label>Account status</label><select name="status"><option value="active" <?=($edit['status']??'active')==='active'?'selected':''?>>Active</option><option value="inactive" <?=($edit['status']??'')==='inactive'?'selected':''?>>Inactive</option></select></div><div class="form-end"><button class="btn primary">Save customer</button></div></form></div>
<?php require 'includes/footer.php'; ?>
