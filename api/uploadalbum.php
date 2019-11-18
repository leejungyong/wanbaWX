<?php

$server = "https://img.wondfun.com";
if ($server == "https://img.wondfun.com") {
    require '../../source/class/class_core.php';
} else {
    require '../../../default/d/source/class/class_core.php';
}
$discuz = &discuz_core::instance();
$discuz->init();
$aid=$_POST['aid'];
$openid=$_POST['openid'];
$teamid=$_POST['teamid'];
$bonus=$_POST['bonus'];
$server = "https://img.wondfun.com";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {


   
	foreach ($_FILES as $name => $file) {

		$fn = $file['name'];
		$ft = strrpos($fn, '.', 0);
		$fm = substr($fn, 0, $ft);
		$fe = substr($fn, $ft);
       $rnd = mt_rand();
        
       $fm = md5(time() . $rnd);
		$dir='album';
		$dir1=$dir.'/'.$aid.'/';
		mkDirs($dir1);
		$fp1 = $dir1. $fm . $fe;
		move_uploaded_file($file['tmp_name'], $fp1);
		//$pic=$fm . $fe;

	    
	$data=array('url'=>$server.'/wanba/api/'.$fp1,'teamid'=>$teamid,'date'=>time(),'openid'=>$openid,'aid'=>$aid);
    DB::insert('wanba_album', $data);
    if($bonus>0){
    $log=array('score'=>$bonus,'status'=>0,'teamid'=>$teamid,'date'=>time(),'creator'=>$openid,'aid'=>$aid,'taskid'=>-2,'event'=>'上传照片获得财富'.$bonus,'memo'=>$server.'/wanba/api/'.$fp1);
    DB::insert('wanba_logs', $log);
}
	}
    //  if($flag){
    //     $result=array('status'=>true,'msg'=>'上传成功','aid'=>$aid);
    //    //echo $fp1;
    //  }else{
    //     $result=array('status'=>false,'msg'=>'上传失败');
        
    //  }
     $result=array('status'=>true,'msg'=>'上传成功');
	echo json_encode($result);
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