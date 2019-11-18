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
$questionid = $_POST['questionid'];
$openid = $_POST['openid'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {



    foreach ($_FILES as $name => $file) {

        $fn = $file['name'];
        $ft = strrpos($fn, '.', 0);
        $fm = substr($fn, 0, $ft);
        $fe = substr($fn, $ft);
        $rnd = mt_rand();

        $fm = md5(time() . $rnd);
        $dir = 'questionpic/';
        //$dir1=$openid.'/pic/';
        mkDirs($dir);
        $fp = $dir . $fm . $fe;
        //$fp1 = $dir1. $fm . $fe;
        move_uploaded_file($file['tmp_name'], $fp);
        $pic = $fm . $fe;


        $data = array('url' => 'https://' . $server . '/wanba/api/' . $fp, 'questionid' => $questionid, 'date' => time(), 'openid' => $openid);
        $picid= DB::insert("wanba_question_pic", $data,'picid');
        $d = array('url' => 'https://' . $server . '/wanba/api/' . $fp, 'questionid' => $questionid, 'date' => time(), 'openid' => $openid,'picid'=>$picid);
        
    }

    echo json_encode($d);
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
