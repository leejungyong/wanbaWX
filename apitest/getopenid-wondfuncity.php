<?php
$appid = 'wxdc0cd3f95d8c1dfa';
$secret = '141ff73999c1c599fb14aa2ade9b0b9a';
$code = $_GET["code"];
$url ='https://api.weixin.qq.com/sns/jscode2session?appid='. $appid . '&secret=' . $secret . '&js_code=' . $code . '&grant_type=authorization_code';
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
$openid = $json_obj["openid"];
$data['openid'] = $openid;
//return json_encode($data);
echo json_encode($json_obj);
?>