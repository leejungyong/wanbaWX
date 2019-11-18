<?php
//10_YXYjKPUeu_ZMtN49zbtGZQFkqJMbvGS4SPEvWSf2y-XJXK7DpFmdQJu1qJJDriH35lkB8ncImO9ZZprQhe4t-TCK5tjsuJpffAWMrUL10pYjkryoPC_HjRqjfyrfJFlDFPUiCmgSJ5ZASmIpCEHgADANPS
$appid = "wx1258f9280fe92bf8";
$appsecret = "816e98cd41cd946a951f87f1276893e6";
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
$result = https_request($url);
$jsoninfo = json_decode($result, true);
$access_token = $jsoninfo["access_token"];
$jsonmenu = '{
      "button":[
      {
            "name":"玩霸攻略",
			"type":"view",
			"url":"https://mp.weixin.qq.com/s/jMIYONfHjR0i7K31MbLH5A"
      },
	   
      {
             
        "name":"团建宝典",
        "type":"view",
        "url":"http://www.wondfun.com"
},
       {
           "name":"亲子活动",
           "type":"view",
           "url":"https://mp.weixin.qq.com/s/bQ_oV1Nfx7wSi4Iy9h6oXg"
       

       }]
 }';

$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $access_token;
$result = https_request($url, $jsonmenu);
var_dump($result);

function https_request($url, $data = null) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	if (!empty($data)) {
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}
