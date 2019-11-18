<?php

$server="www.wondfun.com";
if($server=="www.wondfun.com"){
 require '../../source/class/class_core.php';
}else{
require '../../../default/d/source/class/class_core.php';
}
$discuz = &discuz_core::instance();
$discuz -> init();
$server="www.wondfun.com";
$pointid=$_POST['pointid'];
$openid=$_POST['openid'];
$index=$_POST['index'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


   
	foreach ($_FILES as $name => $file) {

		$fn = $file['name'];
		$ft = strrpos($fn, '.', 0);
		$fm = substr($fn, 0, $ft);
		$fe = substr($fn, $ft);
       $rnd = mt_rand();
        
       $fm = md5(time() . $rnd);
		$dir='pointpic/';
		//$dir1=$openid.'/pic/';
		mkDirs($dir);
		$fp = $dir. $fm . $fe;
		//$fp1 = $dir1. $fm . $fe;
		move_uploaded_file($file['tmp_name'], $fp);
		$pic=$fm . $fe;

	    
     $data=array('url'=>'https://'.$server.'/wanba/api/'.$fp,'pointid'=>$pointid);
    DB::insert('wanba_point_pic', $data);
    
	}
 
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