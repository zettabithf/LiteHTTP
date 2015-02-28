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
					<br>
					<h4><b>What is 'Mark'?</b></h4>
					<p>A <b>Mark</b> is a way to tell if the bot is known as 'Clean' or 'Dirty'. Dirty bots usually have other malware loaded onto the system besides LiteHTTP's bot. The user (you) marks the bot as dirty when running a command on the panel. You can also mark the bot as clean for future reference.</p>
					<br>
					<h4><b>What do I enter for a parameter?</b></h4>
					<p>Parameters are only required for a few of the commands listed on the page. For example, if you wanted to execute the Download & Execute command, your parameter would be the link to the file you want downloaded & executed. Another example, if you wanted to view a webpage, your parameter would be the link to the page you want to view. There are only 3 categories that require parameters, and that is <b>Downloads, Webpages, and Bot Management (Update)</b>.</p>
					<br>
					<h4><b>How do I use filters?</b></h4>
					<p><b>Filters</b> are used to include specific bots in a command, or exclude bots from a command. To use filters, you must specify what kind of filter it is. The format is as follows: <b>filter:filter parameters;</b>. For example, if you only wanted bots from the United States to execute your command, your filter would look something like this: <b>country:united states;</b>. The semicolon represents the end of this filter. If you wanted more than one value per filter, you would separate them with a comma. For example: <b>country:united states,canada;</b>. You can also have more than one filter at the same time. An example: <b>country:canada;privileges:admin;</b>. If you are still having trouble, feel free to contact <a href="http://www.hackforums.net/member.php?action=profile&uid=1972967">Zettabit</a> on HF for help.
				</div>
				<div class="col-lg-6 col-xs-12">
					<h2>Bot Help</h2>
					<br>
					<h4><b>What language is LiteHTTP programmed in?</b></h4>
					<p>LiteHTTP is programmed in <b>C#, using the .NET 2.0 Framework</b>.</p>
				</div>
			</section>
		</aside>
	</div>
	<script src="js/jquery.min.js" type="text/javascript"></script>
	<script src="js/bootstrap.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui.min.js" type="text/javascript"></script>
</body>
</html>