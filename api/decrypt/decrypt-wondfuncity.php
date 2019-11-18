<?php

include_once "wxBizDataCrypt.php";
$arr = json_decode(file_get_contents("php://input"), true);

$appid = 'wxdc0cd3f95d8c1dfa';	
$sessionKey = $arr["session_key"];
$encryptedData=$arr["encryptedData"];
$iv =$arr["iv"];

$pc = new WXBizDataCrypt($appid, $sessionKey);
$errCode = $pc->decryptData($encryptedData, $iv, $data );

if ($errCode == 0) {
    echo json_encode($data);
} else {
    echo json_encode($errCode);
}
?>
