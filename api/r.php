<?php
session_start();
require '../../source/class/class_core.php';
$discuz = &discuz_core::instance();
$discuz->init();
$arr = json_decode(file_get_contents("php://input"), true);
$act = $_GET['act'];

echo $act.'<br>';
echo $_SESSION['views'].'<br>';
?>