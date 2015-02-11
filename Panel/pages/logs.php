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
					<li class="active">
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
						<div id="alerts">
							<?php
							if (isset($_POST['doClear']))
							{
								$odb->query("TRUNCATE plogs");
								echo '<div class="alert alert-success">All logs have been cleared.</div>';
							}
							?>
						</div>
						<table id="botlist" class="table table-condensed table-hover table-striped table-bordered">
							<thead>
								<tr>
									<th>#</th>
									<th>Username</th>
									<th>IP Address</th>
									<th>Action</th>
									<th>Date</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$logs = $odb->query("SELECT * FROM plogs ORDER BY date DESC");
								while ($l = $logs->fetch(PDO::FETCH_ASSOC))
								{
									$id = $l['id'];
									$user = $l['username'];
									$ip = $l['ipaddress'];
									$action = $l['action'];
									$ldate = $l['date'];
									$date = date("m-d-Y, h:i A", $ldate);
									$flag = strtolower(geoip_country_code_by_addr($gi, $ip));
									echo '<tr><td>'.$id.'</td><td>'.$user.'</td><td>'.$ip.'&nbsp;<img src="img/flags/'.$flag.'.png" /></td><td>'.$action.'</td><td data-order="'.$ldate.'">'.$date.'</td></tr>';
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
				"order": [[ 4, "desc" ]],
				"iDisplayLength": 25,
				"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				"sDom": '<"ui-toolbar ui-widget-header ui-corner-tl ui-corner-tr ui-helper-clearfix"lfr>R<C><"#clearall">T<"clear">t<"ui-toolbar ui-widget-header ui-corner-bl ui-corner-br ui-helper-clearfix"p>',
				"oLanguage": {
					"sEmptyTable": "No data to display"
				}
			});
			$("#clearall").html('<center><form action="" method="POST"><input type="submit" class="btn btn-danger" name="doClear" value="Clear All Logs"></form></center>');
		});
	</script>
</body>
</html>