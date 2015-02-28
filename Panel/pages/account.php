<?php
$u = explode(":", $_SESSION['LiteHTTP']);
$username = $u[0];

include 'inc/stats.php';
include 'inc/geo/geoip.inc';
$gi = geoip_open("inc/geo/GeoIP.dat", "");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Control Panel</title>
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
	<link href="css/main.css" rel="stylesheet" type="text/css" />
	<!--[if lt IE 9]>
		<script src="js/html5shiv.js"></script>
		<script src="js/respond.min.js"></script>
	<![endif]-->
</head>
<body class="wysihtml5-supported skin-black fixed">
	<header class="header">
		<a href="?p=main" class="logo">
			LiteHTTP
		</a>
		<nav class="navbar navbar-static-top" role="navigation">
			<div class="navbar-right">
				<ul class="nav navbar-nav">
					<li class="dropdown user user-menu">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<i class="glyphicon glyphicon-user"></i>
							<span><?php echo $username; ?> <i class="caret"></i></span>
						</a>
						<ul class="dropdown-menu">
							<li class="user-footer">
								<div class="pull-left">
									<a href="?p=account" class="btn btn-default btn-flat">Edit Account</a>
								</div>
								<div class="pull-right">
									<a href="?p=logout" class="btn btn-default btn-flat">Logout</a>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</nav>
	</header>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<aside class="left-side sidebar-offcanvas">
			<section class="sidebar">
				<ul class="sidebar-menu">
					<li>
						<a href="?p=main">
							<i class="fa fa-dashboard"></i> <span>Dashboard</span>
						</a>
					</li>
					<li>
						<a href="?p=bots">
							<i class="fa fa-list"></i> <span>Bots</span>
							<small class="badge pull-right bg-green"><?php echo $online; ?></small>
						</a>
					</li>
					<li>
						<a href="?p=tasks">
							<i class="fa fa-bar-chart"></i> <span>Tasks</span>
						</a>
					</li>
					<li>
						<a href="?p=settings">
							<i class="fa fa-cog"></i> <span>Settings</span>
						</a>
					</li>
					<li>
						<a href="?p=users">
							<i class="fa fa-users"></i> <span>Users</span>
						</a>
					</li>
					<li>
						<a href="?p=logs">
							<i class="fa fa-list-alt"></i> <span>Panel Logs</span>
						</a>
					</li>
					<li>
						<a href="?p=help">
							<i class="fa fa-question"></i> <span>Help</span>
						</a>
					</li>
				</ul>
			</section>
		</aside>
		<aside class="right-side">
			<br>
			<div class="col-lg-12 col-xs-24">
				<?php
				if (isset($_POST['doChange']))
				{
					$oldpass = $_POST['oldpass'];
					$newpass = $_POST['newpass'];
					$newpas2 = $_POST['newpass2'];
					if (empty($oldpass) || empty($newpass) || empty($newpas2))
					{
						echo '<div class="alert alert-danger">One of the fields were empty.</div>';
					}else{
						if ($newpass == $newpas2)
						{
							$oh = hash("sha256", $oldpass);
							$op_sql = $odb->prepare("SELECT password FROM users WHERE username = :u");
							$op_sql->execute(array(":u" => $username));
							$op = $op_sql->fetchColumn(0);
							if ($oh == $op)
							{
								$nh = hash("sha256", $newpass);
								$up = $odb->prepare("UPDATE users SET password = :p WHERE username = :u");
								$up->execute(array(":p" => $nh, ":u" => $username));
								echo '<div class="alert alert-success">Password has been changed successfully. Reloading...</div><meta http-equiv="refresh" content="2">';
							}else{
								echo '<div class="alert alert-danger">Current password was incorrect.</div>';
							}
						}else{
							echo '<div class="alert alert-danger">New password did not match.</div>';
						}
					}
				}
				?>
			</div>
			<div class="clearfix"></div>
			<section class="content invoice">
				<div class="row">
					<div class="col-xs-12">
						<h2 class="page-header">
							<?php
							$ls = $odb->prepare("SELECT ipaddress FROM plogs WHERE username = :u AND action = 'Logged in' ORDER BY date DESC LIMIT 1,1");
							$ls->execute(array(":u" => $username));
							$l = $ls->fetchColumn(0);
							if ($l == "" || $l == NULL)
							{
								$l = "Unknown";
							}else{
								$l .= '&nbsp;<img src="img/flags/'.strtolower(geoip_country_code_by_addr($gi, $l)).'.png">';
							}
							?>
							<i class="fa fa-user"></i> <?php echo $username; ?>
							<small class="pull-right">Last Known IP: <b><?php echo $l; ?></b></small>
						</h2>
					</div>
				</div>
				<div class="row invoice-info">
					<div class="col-sm-3 invoice-col">
						<h4>Change Password</h4>
						<hr>
						<form action="" method="POST">
							<label>Current Password</label>
							<input type="password" class="form-control" name="oldpass">
							<br>
							<label>New Password</label>
							<input type="password" class="form-control" name="newpass">
							<br>
							<label>New Password Confirm</label>
							<input type="password" class="form-control" name="newpass2">
							<br>
							<center><input type="submit" class="btn btn-danger" name="doChange" value="Change Password"></center>
						</form>
					</div>
					<div class="col-sm-6 invoice-col">
						<h4>Last 5 Logs</h4>
						<hr>
						<table class="table table-condensed table-bordered table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>IP Address</th>
									<th>Action</th>
									<th>Date</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$lgs = $odb->prepare("SELECT * FROM plogs WHERE username = :u ORDER BY date DESC LIMIT 5");
								$lgs->execute(array(":u" => $username));
								while ($lg = $lgs->fetch(PDO::FETCH_ASSOC))
								{
									echo '<tr><td>'.$lg['id'].'</td><td>'.$lg['ipaddress'].'&nbsp;<img src="img/flags/'.strtolower(geoip_country_code_by_addr($gi, $lg['ipaddress'])).'.png"></td><td>'.$lg['action'].'</td><td>'.date("m-d-Y, h:i A", $lg['date']).'</td></tr>';
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</section>
		</aside>
	</div>
	<script src="js/jquery.min.js" type="text/javascript"></script>
	<script src="js/bootstrap.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui.min.js" type="text/javascript"></script>
</body>
</html>