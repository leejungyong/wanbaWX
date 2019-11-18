<?php
$keyword = '天安门';
$r = '北京';
$url = "https://apis.map.qq.com/ws/place/v1/search?boundary=region(" .$r .",0)&page_size=20&page_index=1&keyword=" .$keyword. "&orderby=_distance&key=TP5BZ-Q4TWW-NMSRE-RR3OO-UXIJK-L2F2Q";
echo $url;
$s = searchPoi($url);
echo json_encode($s);
function searchPoi($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    $json_obj = json_decode($res, true);
    return $json_obj;
}
