<?php
//if (!isset($_POST['bhwid']))
//{
//	include 'inc/404.php';
//	die();
//}
include 'inc/geo/geoip.inc';
$gi = geoip_open("inc/geo/GeoIP.dat");
$ip = $_SERVER['REMOTE_ADDR'];

$cntry_code = geoip_country_id_by_addr($gi, $ip);
$cntry = geoip_country_name_by_id($gi, $cntry_code);
$cntr = geoip_country_code_by_id($gi, $cntry_code);

echo $cntry_code." ".$cntry." ".$cntr;
?>