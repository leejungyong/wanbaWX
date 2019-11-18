<?php
$appid = "wx8b5645afee9a4baf";
$appsecret = "859a8bb922784f6d7061933ee58199b9";
$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
$result = https_request($url);
$jsoninfo = json_decode($result, true);
$access_token = $jsoninfo["access_token"];
$jsonmenu = '{
      "button":[
      {
            "name":"精选目录",
			"type":"view",
			"url":"http://mp.weixin.qq.com/mp/homepage?__biz=MjM5Njk3MzMzOA==&hid=3&sn=a3a215d7eee3bee7636b98acf5f49ade#wechat_redirect"
      },
	   {
             
               "name":"团建游戏",
			   "type":"view",
			   "url":"http://www.wondfun.com/gamecalendar"
           
      

       },
       {
        "name":"亲子活动",
        "type":"view",
        "url":"https://mp.weixin.qq.com/s/bQ_oV1Nfx7wSi4Iy9h6oXg"
       

       }]
 }';


$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
$result = https_request($url, $jsonmenu);
var_dump($result);

function https_request($url,$data = null){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

?>