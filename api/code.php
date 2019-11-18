<?php
session_start();
//$sessionId=session_id();
$arr = json_decode(file_get_contents("php://input"), true);
//$action=$_POST['act'];
$mobile=strval($arr['tel']);	
 //$mobile='18857878397';
 $aid=$arr['aid'];
 //$aid=79;	
  $tel=checkMobile($mobile,$aid);    
 if ($tel){  
include "sms/TopSdk.php";
$sms_tempalte="SMS_60195446";
$appkey="23354531";
$secret="3ed19138ee9ff4f5ab12e6225dfb2a73";
$product="【玩霸江湖】";
$signname="学会玩";
$extend="";
$type="normal";
$code=strval(rand(1000,9999));

$_SESSION['code']=$code;
$arr = array ('code'=>$_SESSION['code'],'product'=>$product); 
$SmsParam = json_encode($arr);
//print_r($SmsParam);
$c = new TopClient;
$c->appkey = $appkey;
$c->secretKey = $secret;
$req = new AlibabaAliqinFcSmsNumSendRequest;
$req->setExtend($extend);
$req->setSmsType($type);
$req->setSmsFreeSignName($signname);
$req->setSmsParam($SmsParam);
$req->setRecNum($mobile);
$req->setSmsTemplateCode($sms_tempalte);
$resp = $c->execute($req);
// print_r($resp) ;
 $r=json_encode($resp);
 $r=json_decode($r,true);
 //$message="验证码发送成功";
 $status=$r['result']['success'];
 $message=($status) ?'验证码发送成功':'验证码发送超过限制，请稍后再试';

$arr = array ('status'=>$status,'message'=>$message); 
$rs = json_encode($arr);
echo $rs;
 }
 else{
	 $arr = array ('status'=>false,'message'=>'该手机号码无权登录'); 
     $rs = json_encode($arr);
	 echo $rs;
 } 


// function checkMobile($mobile,$aid){
// require_once("../../config/config_global.php");
// $db_connection = mysql_connect ($_config['db']['1']['dbhost'], $_config['db']['1']['dbuser'], $_config['db']['1']['dbpw']) OR die (mysql_error());  
// $db_select = mysql_select_db ($_config['db']['1']['dbname']) or die (mysql_error());
// $db_table = $_config['db']['1']['tablepre'] .'wanba_act';
// mysql_query("set names utf8");
// $query = "SELECT coach FROM ".$db_table." where aid=$aid and coach like '%".$mobile."%'";
// $query_result = mysql_query ($query);
// $r = mysql_fetch_array($query_result);
// return $r['coach'] ? true:false;
// }
function checkMobile($mobile,$aid){
		$conn = new mysqli("127.0.0.1:3306", "amourz", "Zw198587", "wondfun");
	if ($conn != null) {
		$conn -> query('set names utf8;');
		$query_result = $conn -> query("SELECT coach FROM wf_wanba_act where aid=$aid and coach like '%".$mobile."%'");
		$r = mysqli_fetch_array($query_result);
        return $r['coach'] ? true:false;		
        $conn -> close();
	}
}

?>