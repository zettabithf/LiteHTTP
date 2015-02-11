<?php
unset($_SESSION['LiteHTTP']);
session_destroy();
header("Location: login/");
?>