<?php
$u = explode(":", $_SESSION['LiteHTTP']);
$username = $u[0];

$i = $odb->prepare("INSERT INTO plogs VALUES(NULL, :u, :ip, 'Logged out', UNIX_TIMESTAMP())");
$i->execute(array(":u" => $username, ":ip" => $_SERVER['REMOTE_ADDR']));

unset($_SESSION['LiteHTTP']);
session_destroy();
header("Location: login/");
?>