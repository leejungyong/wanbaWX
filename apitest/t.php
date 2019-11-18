<?php
	//$aid = $arr['aid'];
    //$openid = $arr['openid'];
    $aid=1;
	$dir = 'upload/'. $aid . '/';
	mkDirs($dir);
	$zipfilename = $dir .'photos_'. $aid . ".zip";
	//@unlink($zipfilename);
    $zip = new ZipArchive();
		if ($zip->open($zipfilename, ZIPARCHIVE::CREATE) !== true) {
			exit('无法打开文件，或者文件创建失败');
		}
	$path = realpath('./upload/'.$aid.'/');
    $result=getDir($path);
    var_dump($result);
foreach($result as $k=>$v){
			$zip->addFile($v, basename($v));
		}
		$zip->close(); // 关闭
		$downurl = "http://www.wondball.com/wanba/api/" . $zipfilename;
    echo json_encode($downurl);

function getRandomString($length = 42)
{
	/*
	 * Use OpenSSL (if available)
	 */
	if (function_exists('openssl_random_pseudo_bytes')) {
		$bytes = openssl_random_pseudo_bytes($length * 2);

		if ($bytes === false)
			throw new RuntimeException('Unable to generate a random string');

		return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
	}

	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
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
function getDir($path)
{

    //判断目录是否为空
    if(!file_exists($path)) {
        return [];
    }

    $fileItem = [];

    //切换如当前目录
    chdir($path);

    foreach(glob('*.jpg') as $v) {
        $newPath = $path . DIRECTORY_SEPARATOR . $v;
        if(is_dir($newPath)) {
            $fileItem = array_merge($fileItem,getDir($newPath));
        }else if(is_file($newPath)) {

            $fileItem[] = $newPath;
        }
    }

    return $fileItem;
}

?>