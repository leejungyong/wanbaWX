<?php

require '../../source/class/class_core.php';

$discuz = &discuz_core::instance();
$discuz -> init();

$server='https://www.wondfun.com/wanba/api/';

$teamid=$_POST['teamid'];
$openid=$_POST['openid'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {


   
	foreach ($_FILES as $name => $file) {

		$fn = $file['name'];
		$ft = strrpos($fn, '.', 0);
		$fm = substr($fn, 0, $ft);
		$fe = substr($fn, $ft);
       //$rnd = mt_rand();
        
       //$fm = md5(time() . $rnd);
		$dir='teampic/';
		//$dir1=$openid.'/pic/';
		mkDirs($dir);
		$fp = $dir. $teamid . $fe;
		//$fp1 = $dir1. $fm . $fe;
		$flag=move_uploaded_file($file['tmp_name'], $fp);
		//$pic=$fm . $fe;

	    
    $data=array('url'=>$server.$fp,'teamid'=>$teamid,'date'=>time(),'openid'=>$openid);
    $picid=DB::fetch_first("select url from wf_wanba_team_pic where teamid=$teamid");
    if($picid){
         DB::update("wanba_team_pic",$data,"teamid=$teamid");
    }else{
       DB::insert('wanba_team_pic', $data); 
    }
    
    
	}
     if($flag){
         $u=array('pic'=>$server.$fp);
         DB::update("wanba_team_setting",$u,"id=$teamid");
        $result=array('status'=>true,'msg'=>'上传成功','pic'=>$server.$fp);
       echo  json_encode($result);
     }else{
        //$result=array('status'=>false,'msg'=>'上传失败');
        
     }
	//echo json_encode($result);
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