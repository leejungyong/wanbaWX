<?php

$server="www.wondfun.com";
if($server=="www.wondfun.com"){
 require '../../source/class/class_core.php';
}else{
require '../../../default/d/source/class/class_core.php';
}
$discuz = &discuz_core::instance();
$discuz -> init();

$aid = $_POST['aid'];
//$teamid=$_POST['teamid'];
$logid=$_POST['logid'];
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
		$dir='upload/'.$aid.'/';
		$dir1=$aid.'/';
		mkDirs($dir);
		$fp = $dir. $fm . $fe;
		$fp1 = $dir1. $fm . $fe;
		move_uploaded_file($file['tmp_name'], $fp);
		$pic=$fm . $fe;

		//$data=array('pic'=>$fm . $fe,'aid'=>$aid,'teamid'=>$teamid,'taskid'=>$taskid,'creator'=>$nick);
		//DB::insert('wondfuncity_score', $data);
		//DB::update('wondfuncity_task',$data,"temppic='".$temppic ."'");

		//$data=array('path'=>$fp,'uid'=>$uid,'modelid'=>$modelid,'uploadtime'=>time());
		//$id=DB::insert('oh_pic', $data,'id');
		//$fs[$name] = array('id'=>$id,'uid'=>$uid,'name' => $fn, 'url' => $fp, 'type' => $file['type'], 'size' => $file['size']);
	    
	$data=array('url'=>$fp1,'logid'=>$logid,'date'=>time(),'aid'=>$aid);
	  DB::insert('wanba_log_pic', $data);
	}
      
	  
	//$ret['file'] = $fp;
	// echo $fs[$name]['url'];
	//echo json_encode($ret);
	echo 'success';
} else {
	echo "{'error':'Unsupport GET request!'}";
}

function mkDirs($dir){
    if(!is_dir($dir)){
        if(!mkDirs(dirname($dir))){
            return false;
        }
        if(!mkdir($dir,0777)){
            return false;
        }
    }
    return true;
}
?>