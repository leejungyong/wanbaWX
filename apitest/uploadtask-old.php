<?php

require '../../source/class/class_core.php';
$discuz = &discuz_core::instance();
$discuz -> init();

$aid = $_POST['aid'];
$teamid=$_POST['teamid'];
$taskid=$_POST['taskid'];
$nick=$_POST['nick'];

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
		$data=array('pic'=>$fm . $fe,'aid'=>$aid,'teamid'=>$teamid,'taskid'=>$taskid,'creator'=>$nick);
		DB::insert('wondfuncity_score', $data);
		//DB::update('wondfuncity_task',$data,"temppic='".$temppic ."'");

		//$data=array('path'=>$fp,'uid'=>$uid,'modelid'=>$modelid,'uploadtime'=>time());
		//$id=DB::insert('oh_pic', $data,'id');
		//$fs[$name] = array('id'=>$id,'uid'=>$uid,'name' => $fn, 'url' => $fp, 'type' => $file['type'], 'size' => $file['size']);
	    
	}

	$ret['file'] = $fp;
	// echo $fs[$name]['url'];
	//echo json_encode($ret);
	echo $fp;
} else {
	echo "{'error':'Unsupport GET request!'}";
}
?>