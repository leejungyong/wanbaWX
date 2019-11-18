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
            "name":"资讯",
           "sub_button":[
            {
               "type":"view",
               "name":"【往期回顾】",
               "url":"http://mp.weixin.qq.com/mp/getmasssendmsg?__biz=MjM5Njk3MzMzOA==#wechat_webview_type=1&wechat_redirect"
            },
            {
               "type":"view",
               "name":"【小范贱】",
               "url":"http://wap.koudaitong.com/v2/feature/16rqobts3"
            },
            {
               "type":"view",
               "name":"【小吃货】",
               "url":"http://wap.koudaitong.com/v2/feature/92ygotwo"
            },
            {
               "type":"view",
               "name":"【玩小报】",
               "url":"http://wap.koudaitong.com/v2/feature/m4tsanv2"
            },
            {
                "type":"view",
                "name":"【玩客记】",
                "url":"http://wap.koudaitong.com/v2/feature/1g88ybn4d"
            }]
      

       },
	   {
             "name":"体验",
           "sub_button":[
            {
               "type":"view",
               "name":"活动mall",
               "url":"http://wap.koudaitong.com/v2/feature/o5req4jo"
            },
            {
               "type":"view",
               "name":"团队outing",
               "url":"http://eqxiu.com/s/7yQ9CSwq"
            },
            {
               "type":"view",
               "name":"坐直升机",
               "url":"http://mp.weixin.qq.com/s?__biz=MjM5Njk3MzMzOA==&mid=211993480&idx=1&sn=099c2db7994fe6e2ec15d6d866994c58"
            },
			{
               "type":"view",
               "name":"荒岛求生",
               "url":"http://mp.weixin.qq.com/s?__biz=MjM5Njk3MzMzOA==&mid=211560266&idx=1&sn=eab4318d1bb95ba876835e897659c4cd"
            }
			]
      

       },
       {
           "name":"玩客",
           "sub_button":[
            {
               "type":"view",
               "name":"招兵买疯子",
               "url":"http://eqxiu.com/s/J0CKGq"
            },
            {
               "type":"view",
               "name":"玩范小疯队",
               "url":"http://mp.weixin.qq.com/s?__biz=MjM5Njk3MzMzOA==&mid=210428278&idx=1&sn=6032401067264e440562bb077606e26f#rd"
            },
            {
               "type":"view",
               "name":"我们是谁",
               "url":"http://wap.koudaitong.com/v2/feature/13ipiq1yl"
            }			
			]
       

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