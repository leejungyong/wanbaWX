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

$openid=$_POST['openid'];
$aid=$_POST['aid'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


   
	foreach ($_FILES as $name => $file) {

		$data = base64_encode(@file_get_contents($file['tmp_name']));

	    
	//$result=array('data'=>$data);
	//echo json_encode($result);
   


	$url = 'https://aip.baidubce.com/oauth/2.0/token';
    $post_data['grant_type']       = 'client_credentials';
    $post_data['client_id']      = 'bzHgYdadYh77f5DBNgVaajFA';
    $post_data['client_secret'] = 'ifrZ8eLecACz4QWXGswe5zmfsWMVPYoO';
    $o = "";
    foreach ( $post_data as $k => $v ) 
    {
    	$o.= "$k=" . urlencode( $v ). "&" ;
    }
    $post_data = substr($o,0,-1);
    
    $res = request_post($url, $post_data);
    $a=json_decode($res,true);
    $token=$a['access_token'];
    $url = 'https://aip.baidubce.com/rest/2.0/image-classify/v2/advanced_general?access_token=' . $token;
    $bodys = array(
    'image' => $data 
    );
      $res = request_post($url, $bodys);
      $a=json_decode($res,true);
      $result=$a['result'][0];
      $k=$result['keyword'];
      $insert=array('pic'=>'data:image/jpeg;base64,'.$data,'title'=>$k,'aid'=>$aid,'openid'=>$openid,'date'=>time());
    //   $id=DB::insert('wanba_ai_pic',$insert,'id');
    //   if($id){
    //   $arr=array('pic'=>'data:image/jpeg;base64,'.$data,'title'=>$k,'id'=>$id);
    // }else{
    //     $arr=array();
    // }
    echo json_encode($insert);


	}
    
 } else {
	echo "{'error':'Unsupport GET request!'}";
}

function request_post($url = '', $param = '') {
    if (empty($url) || empty($param)) {
        return false;
    }
    
    $postUrl = $url;
    $curlPost = $param;
    $curl = curl_init();//初始化curl
    curl_setopt($curl, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($curl);//运行curl
    curl_close($curl);
    
    return $data;
}
