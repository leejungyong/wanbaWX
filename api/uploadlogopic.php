<?php



$aid=$_POST['aid'];
$openid=$_POST['openid'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {


   
	foreach ($_FILES as $name => $file) {

		$fn = $file['name'];
		$ft = strrpos($fn, '.', 0);
		$fm = substr($fn, 0, $ft);
		$fe = substr($fn, $ft);
       //$rnd = mt_rand();
        
       //$fm = md5(time() . $rnd);
		$dir='logopic/';
		//$dir1=$openid.'/pic/';
		mkDirs($dir);
		$fp = $dir. $aid . $fe;
		//$fp1 = $dir1. $fm . $fe;
		$flag=move_uploaded_file($file['tmp_name'], $fp);
		//$pic=$fm . $fe;

	    
	//$data=array('url'=>'https://www.wondball.com/wanba/api/'.$fp,'questionid'=>$questionid,'date'=>time(),'openid'=>$openid,'displayorder'=>$index);
    //DB::insert('wanba_question_pic', $data);
    
	}
     if($flag){
       // $result=array('status'=>true,'msg'=>'上传成功','aid'=>$aid);
       echo $aid;
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