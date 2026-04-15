<?php
require_once __DIR__ . '/autoload.php';

Auth::logout();
header('Location: login.php');
exit();
?>
