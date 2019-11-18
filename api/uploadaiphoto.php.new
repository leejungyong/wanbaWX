<?php

$server = "www.wondfun.com";
if ($server == "www.wondfun.com") {
    require '../../source/class/class_core.php';
} else {
    require '../../../default/d/source/class/class_core.php';
}
$discuz = &discuz_core::instance();
$discuz->init();
$server = "www.wondfun.com";

$openid = $_POST['openid'];
$aid = $_POST['aid'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {



    foreach ($_FILES as $name => $file) {

        $data = base64_encode(@file_get_contents($file['tmp_name']));


        //$result=array('data'=>$data);
        //echo json_encode($result);



        $url = 'https://aip.baidubce.com/oauth/2.0/token';
        $post_data['grant_type']       = 'client_credentials';
        $post_data['client_id']      = 'bzHgYdadYh77f5DBNgVaajFA';
        $post_data['client_secret'] = 'ifrZ8eLecACz4QWXGswe5zmfsWMVPYoO';
        $o = "";
        foreach ($post_data as $k => $v) {
            $o .= "$k=" . urlencode($v) . "&";
        }
        $post_data = substr($o, 0, -1);

        $res = request_post($url, $post_data);
        $a = json_decode($res, true);
        $token = $a['access_token'];
        $url = 'https://aip.baidubce.com/rest/2.0/image-classify/v2/advanced_general?access_token=' . $token;
        $bodys = array(
            'image' => $data
        );
        $res = request_post($url, $bodys);
        $a = json_decode($res, true);
        $result = $a['result'][0];
        $k = $result['keyword'];
        echo json_encode($k);
        //echo $k;
        //得到宝石池宝石数量
       // $stones = DB::result_first("select count(aid) c from wf_wanba_stone_list where aid=$aid");
        //得到宝石爆率基础设置
       // $setting = DB::fetch_first("select ai_duration,ai_random,ai_lasttime from wf_wanba_act where aid=$aid");

        //得到ai特征图库
        //$aipics = DB::fetch_all("select pic from wf_wanba_ai_pic where title like '%" . $k . "%'");
        //宝石爆率算法
        // status -2 宝石已经被找完了  -1 宝石爆出时间未到 0 获得宝石 1 运气不好未获得宝石
        // if ($stones > 0) {

        //     $duration = time() - $setting['ai_lasttime'];
        //     //到了设定的时间
        //     if ($duration > $setting['ai_duration'] * 60) {
        //         //匹配到
        //         if ($aipics) {
        //             $result = array('status' => 0);
        //             //更新最新爆出时间
        //             $d = array('ai_lasttime' => time());
        //             DB::update('wanba_act', $d, "aid=$aid");
        //         } else {
        //             //即使没匹配到，也有几率得到宝石
        //             if ($setting['ai_random'] > 0) {
        //                 //随机出来小于设定
        //                 $rand = mt_rand(0, 100);
        //                 if ($rand < $setting['ai_random']) {
        //                     $result = array('status' => 0);
        //                     //更新最新爆出时间
        //                     $d = array('ai_lasttime' => time());
        //                     DB::update('wanba_act', $d, "aid=$aid");
        //                 } else {
        //                     $result = array('status' => 1);
        //                 }
        //             } else {
        //                 $result = array('status' => 1);
        //             }
        //         }
        //     } else {
        //         $result = array('status' => -1);
        //     }
        // } else {
        //     $result = array('status' => -2);
        // }
        // echo json_encode($result);
    }
} else {
    echo "{'error':'Unsupport GET request!'}";
}

function request_post($url = '', $param = '')
{
    if (empty($url) || empty($param)) {
        return false;
    }

    $postUrl = $url;
    $curlPost = $param;
    $curl = curl_init(); //初始化curl
    curl_setopt($curl, CURLOPT_URL, $postUrl); //抓取指定网页
    curl_setopt($curl, CURLOPT_HEADER, 0); //设置header
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
    curl_setopt($curl, CURLOPT_POST, 1); //post提交方式
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($curl); //运行curl
    curl_close($curl);

    return $data;
}
