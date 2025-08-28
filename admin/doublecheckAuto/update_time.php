<?php
//echo __FILE__;
set_time_limit(0);
//This is the file where we save the    information
$fp = fopen (dirname(__FILE__) . '/update.txt', 'w+');
fwrite($fp,$_SERVER['HTTP_USER_AGENT']."\r\n".date('Y-m-d h:i:s'));
fclose($fp);


$url="http://shop.superdouble.org/admin/doublecheckAuto/cron-other.php";
$curl = curl_init(); //开启curl
	//$url.="?super=".$_REQUEST["super"];
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
//curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($curl, CURLOPT_TIMEOUT, 1);	
curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
$obj = curl_exec($curl); //执行curl操作
curl_close($curl);
$info=json_decode($obj,true);
//Here is the file we are downloading, replace spaces with %20
/*
$ch = curl_init(str_replace(" ","%20",$url));
curl_setopt($ch, CURLOPT_TIMEOUT, 50);
// write curl response to file
curl_setopt($ch, CURLOPT_FILE, $fp); 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// get curl response
curl_exec($ch); 
curl_close($ch);


echo "success"."<br>";
echo "<a href='https://atom3dp.com/atom_support.csv'>https://atom3dp.com/atom_support.csv</a>";
*/