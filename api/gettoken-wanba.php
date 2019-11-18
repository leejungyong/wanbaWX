<?php
$appid = 'wx13a69e8d86bf8f77';
$secret = '7ec99d5f49fbcf48303f6d8f040c8ff9';
$code = $_GET["code"];
$url ='https://api.weixin.qq.com/cgi-bin/token?appid='. $appid . '&secret=' . $secret . '&js_code=' . $code . '&grant_type=client_credential';
//$info = file_get_contents($url);//发送HTTPs请求并获取返回的数据，推荐使用curl  
//$json = json_decode($info);//对json数据解码  
//$arr = get_object_vars($json); 
$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 500);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_URL, $url);

$res = curl_exec($curl);
curl_close($curl);

$json_obj = json_decode($res, true);
// $openid = $json_obj["openid"];
// $data['openid'] = $openid;

echo json_encode($json_obj['access_token']);
?>