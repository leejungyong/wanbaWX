<?php
$str='5|10,20';
 $s = preg_replace('/\|/', ',', $str);
 echo $s."<br>";
 $setting = explode(',', $s);
 $redbagrnd = $setting[array_rand($setting)];
 echo $redbagrnd ;
?>