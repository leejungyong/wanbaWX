<?php
$lifeTime = 7000; 
session_set_cookie_params($lifeTime); 
session_start();

$arr = json_decode(file_get_contents("php://input"), true);
//echo "session:". $_SESSION["access_token_wx13a69e8d86bf8f77"];

if($_SESSION["access_token_wx13a69e8d86bf8f77"]){
    $access_token = $_SESSION["access_token"];
}else{
$access_token = get_token_access();
$_SESSION["access_token_wx13a69e8d86bf8f77"]=$access_token;
}
$page=$arr['page'];
$scene=$arr['scene'];
// $page = "pages/game/splash";
// $scene = "aid=79";
$url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $access_token;

$data = array('page' => $page, 'scene' => $scene);


$r = curl_post_https($url, json_encode($data));
$pic = data_uri($r, 'image/png');
//echo json_encode($r);
echo $pic;
// echo '<image src='.$pic.'></image>';
//echo '<img src=' . $pic . '>';


/* PHP CURL HTTPS POST */
function curl_post_https($url, $data)
{ // 模拟提交数据函数
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno' . curl_error($curl); //捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据，json格式
}

function data_uri($contents, $mime)
{
    $base64   = base64_encode($contents);
    return ('data:' . $mime . ';base64,' . $base64);
}

function get_token_access()
{
    $appid = 'wx13a69e8d86bf8f77';
    $secret = '7ec99d5f49fbcf48303f6d8f040c8ff9';
    $url = 'https://api.weixin.qq.com/cgi-bin/token?appid=' . $appid . '&secret=' . $secret .  '&grant_type=client_credential';
    
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

    return $json_obj['access_token'];
}
 