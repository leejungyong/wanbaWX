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
			"url":"https://mp.weixin.qq.com/s/defrkLfeV-zAbwH7lELUbg"
      },
	   
        {
            "name":"戈壁成人礼",
			"type":"view",
			"url":"https://mp.weixin.qq.com/s/g_KZga0n9iJdahux3cN90A"
      },
       {
           "name":"关于我们",
           "sub_button":[
            {
             
              "name":"官方网站",
              "type":"view",
              "url":"http://www.wondfun.com"
          
     

      },
            {
             
                "name":"团建游戏",
                "type":"view",
                "url":"http://www.wondfun.com/gamecalendar"
            
       
 
        },

		               {
               "type":"view",
               "name":"历史文章",
               "url":"http://mp.weixin.qq.com/mp/homepage?__biz=MjM5ODg1OTkwOA==&hid=3&sn=a1b484a0ad3c2c7297f4b21d0b16702e&scene=18#wechat_redirect
"
            },
            {
              "type":"view",
              "name":"玩品推荐",
              "url":"https://mp.weixin.qq.com/s/mpUThUdjXYQ42-ekhDPugg"
           },
            
		               {
               "type":"view",
               "name":"关于我们",
               "url":"https://mp.weixin.qq.com/s/xoxYYTVzVLuRwiSs3qYkhQ"
"
            }
            			
			]
       

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
?>