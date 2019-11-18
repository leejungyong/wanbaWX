<?php
//10_YXYjKPUeu_ZMtN49zbtGZQFkqJMbvGS4SPEvWSf2y-XJXK7DpFmdQJu1qJJDriH35lkB8ncImO9ZZprQhe4t-TCK5tjsuJpffAWMrUL10pYjkryoPC_HjRqjfyrfJFlDFPUiCmgSJ5ZASmIpCEHgADANPS
$appid = "wx446c0c47f246141c";
$appsecret = "b5af990582931b58e9be51f7c57c46d0";
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
$result = https_request($url);
$jsoninfo = json_decode($result, true);
// var_dump($jsoninfo);
// exit();
$access_token = $jsoninfo["access_token"];

$jsonmenu = '{
      "button":[
      {
            "name":"团建游戏",
			"type":"view",
			"url":"http://www.wondfun.com/calendar2019/#/"
      },
      {
        "name":"关于我们",
        "type":"view",
        "url":"https://mp.weixin.qq.com/s/is1T5ZmZNzh9xy9oD0IRVw"
  }]
 }';

$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $access_token;
$result = https_request($url, $jsonmenu);
var_dump($result);

function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
?>