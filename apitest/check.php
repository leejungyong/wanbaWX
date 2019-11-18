<?php
session_start();

$arr = json_decode(file_get_contents("php://input"), true);
$mobile=strval($arr['tel']);	
$aid=$arr['aid'];
$code=$arr['code'];
//echo $_SESSION['code']."&&".$code;

//exit;
	if (isset($_SESSION['code']) && !empty($_SESSION['code'])){
		if ($code==$_SESSION['code']){
		$status=true;
		    
		$message="身份验证通过！";
		
		
		
		$arr = array ('status'=>$status,'message'=>$message); 
		$rs = json_encode($arr);
		echo $rs;
		}	
        else{
		$status=false;
		$message="验证码错误！";
		//$message=$_SESSION['code'];
		$arr = array ('status'=>$status,'message'=>$message); 
		$rs = json_encode($arr);
		echo $rs;			
		}		
	}
	else{
		$status=false;
		$message="验证码已过期，请重新获取！";
		//$message=$_SESSION['code'];
		$arr = array ('status'=>$status,'message'=>$message); 
		$rs = json_encode($arr);
		echo $rs;
	} 

?>