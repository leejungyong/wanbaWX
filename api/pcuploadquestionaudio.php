<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
require '../../source/class/class_core.php';
$discuz = &discuz_core::instance();
$discuz -> init();

$uid = intval($_POST['uid']);
$questionid = intval($_POST['questionid']);



if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$ret = array('strings' => $_POST, 'error' => '0');

	$fs = array();

	foreach ($_FILES as $name => $file) {

		$fn = $file['name'];
		$ft = strrpos($fn, '.', 0);
		$fm = substr($fn, 0, $ft);
		$fe = substr($fn, $ft);
       $rnd = mt_rand();
        
       $fm = md5(time() . $rnd);
		$dir='audio/';
		//$dir1=$openid.'/pic/';
		//mkDirs($dir);
		$fp = $dir. $fm . $fe;
		//$fp1 = $dir1. $fm . $fe;
		move_uploaded_file($file['tmp_name'], $fp);
		$pic=$fm . $fe;
		// $data=array(
		//   'uid'=>$uid,
		//   'pid'=>$pid,
		//   'date'=>time(),
		//   'pic'=>$fp,
		//   'type'=>$type
        //  );
		// $fileid=DB::insert("project_file",$data,'id');
		$data=array('url'=>'https://www.wondfun.com/wanba/api/'.$fp,'media'=>1);
    DB::update('wanba_question', $data,"questionid=$questionid");
		$fs[$name] = array('questionid'=>$questionid,'media'=>1,'name' => $fn, 'url' => 'https://www.wondfun.com/wanba/api/'.$fp, 'type' => $file['type'], 'size' => $file['size']);
	}

	$ret['files'] = $fs;
	echo json_encode($ret);
} else {
	echo "{'error':'Unsupport GET request!'}";
}
?>