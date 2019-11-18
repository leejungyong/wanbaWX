<?php

require '../../source/class/class_core.php';
$discuz = &discuz_core::instance();
$discuz -> init();

//$aid = $_POST['aid'];
//$teamid=$_POST['teamid'];
$picid = $_POST['picid'];
//$nick=$_POST['nick'];
//$pics=array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	foreach ($_FILES as $name => $file) {

		$fn = $file['name'];
		$ft = strrpos($fn, '.', 0);
		$fm = substr($fn, 0, $ft);
		$fe = substr($fn, $ft);
		$radom = mt_rand();
		$fm = md5(time() . $radom);

		$fp = 'upload/' . $fm . $fe;

		move_uploaded_file($file['tmp_name'], $fp);
		$pic = $fm . $fe;
		$data = array('url' => $pic);
		DB::update('wondfuncity_pic', $data, "pid=$picid");
	}

	echo 'success';
} else {
	echo "{'error':'Unsupport GET request!'}";
}
?>