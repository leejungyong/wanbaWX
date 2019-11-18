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
// $company=$_POST['companyName'];
// $openid=$_POST['openid'];
// $orgCode=$_POST['orgCode'];
// $corporate=$_POST['corporate'];
// $tel=$_POST['telephone'];
// $city=$_POST['address'];
// $address=$_POST['addressDetail'];
$id = $_POST['id'];
$openid = $_POST['openid'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {



    foreach ($_FILES as $name => $file) {

        $fn = $file['name'];
        $ft = strrpos($fn, '.', 0);
        $fm = substr($fn, 0, $ft);
        $fe = substr($fn, $ft);
        $rnd = mt_rand();

        $fm = md5(time() . $rnd);
        $dir = 'agentpic/';
        //$dir1=$openid.'/pic/';
        mkDirs($dir);
        $fp = $dir . $fm . $fe;
        //$fp1 = $dir1. $fm . $fe;
        move_uploaded_file($file['tmp_name'], $fp);
        $pic = $fm . $fe;

        $n = DB::fetch_first("select id from wf_wanba_agent where openid='" . $openid . "'");
        if ($n) {
            $data = array('pic' => 'https://' . $server . '/wanba/api/' . $fp, 'status' => 0);
            DB::update("wanba_agent", $data, "id=$n[id]");
            $result = array('status' => true, 'msg' => '操作成功', 'data' => $data);
        } 
    }

    echo json_encode($result);
} else {
    echo "{'error':'Unsupport GET request!'}";
}

function mkDirs($dir)
{
    if (!is_dir($dir)) {
        if (!mkDirs(dirname($dir))) {
            return false;
        }
        if (!mkdir($dir, 0777)) {
            return false;
        }
    }
    return true;
}
 