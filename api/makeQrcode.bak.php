<?php
$arr = json_decode(file_get_contents("php://input"), true);
//$access_token = $_GET["access_token"];
$access_token ='20_Kh5XXyJh4xSehGBsmeZ9fulaVybyo_zTZh5ByQkUiNl-kU_x4oiHW2iXrUM0R_NDsbo_2pIzzS_At6eWWSvyYtLo5x_zrUEi4tXDMDsX8LODvFsPeuKSdFPKJ46usaLMNngQ9__NpXFqMPjYTSViAIAEDG';
$page=$arr['page'];
$scene=$arr['scene'];
//$page='pages/game/splash';
//$scene="aid=79";
$url ='https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $access_token ;

$data=array('page'=>$page,'scene'=>$scene);


$r=curl_post_https($url,json_encode($data));
$pic= data_uri($r,'image/png');
  //  return '<image src='.$result.'></image>';
echo '<img src='.$pic.'>';


/* PHP CURL HTTPS POST */
function curl_post_https($url,$data){ // 模拟提交数据函数
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
        echo 'Errno'.curl_error($curl);//捕抓异常
    }
    curl_close($curl); // 关闭CURL会话
    return $tmpInfo; // 返回数据，json格式
}

function data_uri($contents, $mime)
{
$base64   = base64_encode($contents);
return ('data:' . $mime . ';base64,' . $base64);
}
?>