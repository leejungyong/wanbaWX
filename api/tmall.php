<?php
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('PRC');

 require '../../source/class/class_core.php';
//require '../../../default/d/source/class/class_core.php';
$discuz = &discuz_core::instance();
$discuz->init();
$arr = json_decode(file_get_contents("php://input"), true);

$act = $_GET['act'];
switch ($act) {

    case 'checkNum':
         $openid=$arr['openid'];
         $num=$arr['num'];
         $r=DB::fetch_first("select tel,p1,p2,p3,p4,p5 from wf_tmall_party where tel ='".$num."'");
         if($r){
            $d=array('openid'=>$openid,'lastlogin'=>time());
              DB::update("tmall_party",$d,"tel='".$num."'");
              $config=$r['p1'].','.$r['p2'].','.$r['p3'].','.$r['p4'].','.$r['p5'];
              $result=array('status'=>true,'msg'=>$config);
         }else{
             $result=array('status'=>false,'msg'=>'未匹配到手机号码，请联系组委会');
         }
         echo json_encode($result);
        break;
    
    default:
        echo json_encode($arr);
}

 