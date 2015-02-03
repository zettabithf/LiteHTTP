<?php
session_start();
include_once("../securimage/securimage.php");
$sec = new Securimage();

if (isset($_POST['doLogin']))
{
	$captcha_code = $_POST['captcha_code'];
	if ($sec->check($captcha_code) != false)
	{
		include '../inc/config.php';
		$username = $_POST['username'];
		$password = hash("sha256", $_POST['password']);
		if (ctype_alnum($username))
		{
			$sel = $odb->prepare("SELECT password FROM users WHERE username = :user");
			$sel->execute(array(":user" => $username));
			$pass = $sel->fetchColumn(0);
			if ($pass != "" || $pass != NULL)
			{
				if ($password == $pass)
				{
					$_SESSION['LiteHTTP'] = $username.":".md5($password);
					header("Location: ../");
				}
			}
		}
	}
}
?>
<!DOCTYPE html>
<html class="bg-black">
<head>
	<meta charset="UTF-8">
	<title>Login</title>
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="../css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="../css/main.css" rel="stylesheet" type="text/css" />
	<!--[if lt IE 9]>
		<script src="../js/html5shiv.js"></script>
		<script src="../js/respond.min.js"></script>
	<![endif]-->
</head>
<body class="bg-black">
	<div class="form-box" id="login-box">
		<form action="" method="POST">
			<div class="header">Login</div>
			<div class="body bg-gray">
				<div class="form-group">
					<input type="text" name="username" class="form-control" placeholder="Username">
				</div>
				<div class="form-group">
					<input type="password" name="password" class="form-control" placeholder="Password">
				</div>
				<div class="form-group">
					<input type="text" name="captcha_code" class="form-control" maxlength="6" placeholder="Captcha Code">
				</div>
				<center><img id="captcha" src="../securimage/securimage_show.php" alt="CAPTCHA Image" /></center>
			</div>
			<div class="footer">
				<button type="submit" name="doLogin" class="btn btn-success btn-block">Login</button>
			</div>
		</form>
	</div>
</body>
</html>