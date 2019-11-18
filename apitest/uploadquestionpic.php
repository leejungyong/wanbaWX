<?php

require '../../source/class/class_core.php';
$discuz = &discuz_core::instance();
$discuz -> init();

$questionid=$_POST['questionid'];
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
		$dir='questionpic/';
		//$dir1=$openid.'/pic/';
		mkDirs($dir);
		$fp = $dir. $fm . $fe;
		//$fp1 = $dir1. $fm . $fe;
		move_uploaded_file($file['tmp_name'], $fp);
		$pic=$fm . $fe;

	    
	$data=array('url'=>'https://www.wondfun.com/wanba/apitest/'.$fp,'questionid'=>$questionid,'date'=>time(),'openid'=>$openid,'displayorder'=>$index);
    DB::insert('wanba_question_pic', $data);
    
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