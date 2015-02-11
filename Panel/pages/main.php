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
					<li class="active">
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
				<div class="row">
					<div class="col-lg-12 col-xs-24">
						<h3>Last 5 Installations</h3>
						<table id="lastfive" class="table table-condensed table-hover table-striped table-bordered">
							<thead>
								<tr>
									<th>#</th>
									<th>IP Address</th>
									<th>Country</th>
									<th>Install Date</th>
									<th>Operating System</th>
									<th>Privileges</th>
									<th>Bot Version</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($total != "0")
								{
									$bots = $odb->query("SELECT * FROM bots ORDER BY installdate DESC LIMIT 5");
									while ($b = $bots->fetch(PDO::FETCH_ASSOC))
									{
										$id = $b['id'];
										$ip = $b['ipaddress'];
										$cn = geoip_country_name_by_id($gi, $b['country']);
										$fl = strtolower(geoip_country_code_by_id($gi, $b['country']));
										$in = date("m-d-Y, h:i A", $b['installdate']);
										$os = $b['operatingsys'];
										$pv = $b['privileges'];
										$bv = $b['botversion'];
										echo '<tr><td>'.$id.'</td><td>'.$ip.'</td><td>'.$cn.'&nbsp;&nbsp;<img src="img/flags/'.$fl.'.png" /></td><td>'.$in.'</td><td>'.$os.'</td><td>'.$pv.'</td><td>'.$bv.'</td></tr>';
									}
								}else{
									echo '<tr class="odd"><td colspan="8">No data to display</td></tr>';
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<br><br>
				<div class="row">
					<div class="col-lg-4 col-xs-8">
						<h3>Top 3 Countries</h3>
						<table id="topcountries" class="table table-condensed table-hover table-striped table-bordered">
							<thead>
								<tr>
									<th>Country</th>
									<th># of Bots</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($total != "0")
								{
									$csel = $odb->query("SELECT country, COUNT(*) AS cnt FROM bots GROUP BY country ORDER BY cnt DESC LIMIT 3");
									while ($c = $csel->fetch())
									{
										echo '<tr><td>'.geoip_country_name_by_id($gi, $c[0]).'</td><td>'.number_format($c[1]).'</td></tr>';
									}
								}else{
									echo '<tr class="odd"><td colspan="8">No data to display</td></tr>';
								}
								?>
							</tbody>
						</table>
					</div>
					<div class="col-lg-4 col-xs-8">
						<h3>Top 3 Operating Systems</h3>
						<table id="topcountries" class="table table-condensed table-hover table-striped table-bordered">
							<thead>
								<tr>
									<th>Operating System</th>
									<th># of Bots</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($total != "0")
								{
									$osel = $odb->query("SELECT operatingsys, COUNT(*) AS cnt FROM bots GROUP BY operatingsys ORDER BY cnt DESC LIMIT 3");
									while ($o = $osel->fetch())
									{
										echo '<tr><td>'.$o[0].'</td><td>'.number_format($o[1]).'</td></tr>';
									}
								}else{
									echo '<tr class="odd"><td colspan="8">No data to display</td></tr>';
								}
								?>
							</tbody>
						</table>
					</div>
					<div class="col-lg-4 col-xs-8">
						<h3>Privileges</h3>
						<table id="topcountries" class="table table-condensed table-hover table-striped table-bordered">
							<thead>
								<tr>
									<th>Privilege</th>
									<th># of Bots</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($total != "0")
								{
									$psel = $odb->query("SELECT privileges, COUNT(*) AS cnt FROM bots GROUP BY privileges ORDER BY cnt DESC");
									while ($p = $psel->fetch())
									{
										echo '<tr><td>'.$p[0].'</td><td>'.number_format($p[1]).'</td></tr>';
									}
								}else{
									echo '<tr class="odd"><td colspan="8">No data to display</td></tr>';
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
</body>
</html>