<?php
require 'core.php';

$hotspot = new Ma_spot;

if($_GET['redir'] == 1):
	echo $hotspot->make_redirect();
else:

	$my_stat = $hotspot->my_status();
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?=$hotspot->hotspot_name();?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="<?=$hotspot->base_url()?>assets/css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link rel="stylesheet" href="<?=$hotspot->base_url()?>assets/css/bootstrap-responsive.min.css">
		<style>
			.main_container{
				margin-top: 55px
			}
			.login-system{
				text-align: center;
				background: #EEE;
				margin: 50px auto;
				width: 335px;
				padding: 45px 25px 60px;
				box-shadow: 0px 2px 3px #BBB;
			}
			.tbl-stat{
				background: #fff;
			}
			.tbl-stat tr td:first-child{
				font-weight: bold;
			}
		</style>
	</head>
	<body>
		<div class="login-system">
			<?php if($my_stat['access'] == true): ?>
				<h1>Hello !</h1>
				<h4>Hotspot dalam masa ujicoba</h4>	
				
				<?php if(strtolower($_GET['action']) == "logout"): $hotspot->remove_host($my_stat['ip_addr']); ?>

					<div class="alert alert-info"><b>Logout</b> Anda telah melakukan Logout</div>
				
				<?php echo $hotspot->redirect_main(); endif; ?>
				
				<table class="table table-bordered tbl-stat">
					<tbody>
						<tr>
							<td>IP Address</td>
							<td><?=$my_stat['ip_addr'];?></td>
						</tr>
						<tr>
							<td>MAC Address</td>
							<td><?=$my_stat['mac_addr'];?></td>
						</tr>
					</tbody>
				</table>
				<?php if(isset($_GET['next'])): ?>
				<a href="<?=htmlspecialchars($_GET['next'])?>">Continue Website you want to visit</a>
				<?php endif; ?>
				<a class="btn btn-large btn-danger" href="<?=$hotspot->base_url()?>?action=logout">Logout Hotspot</a><br/>
			<?php else: ?>
				<h1>Login Hotspot</h1>
				<h4>Hotspot dalam masa ujicoba</h4>

				<?php if(strtolower($_GET['action']) == "login"): $hotspot->register_host($my_stat['ip_addr']);?>
					
					<div class="alert alert-success"><b>Login</b> Anda telah melakukan Login</div>
				
				<?php echo $hotspot->redirect_main(); endif; ?>	
				
				<p><abbr title="Internet Protocol Address">IP</abbr> Perangkat Anda adalah <code><?=$my_stat['ip_addr'];?></code></p>
				<a class="btn btn-large btn-success" href="<?=$hotspot->base_url()?>?action=login">Klik disini Untuk melanjutkan</a>
			<?php endif; ?>
		</div>
		
	</body>
	<script src="<?=$hotspot->base_url()?>assets/js/jquery-1.9.1.js"></script>
	<script src="<?=$hotspot->base_url()?>assets/js/bootstrap.min.js"></script>
</html>

<?php endif; ?>