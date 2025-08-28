<?php
add_action('DoubleCheckPaynowAll', 'DoubleCheckPaynowAll');
function DoubleCheckPaynowAll(){

	$url="https://shop.superdouble.org/admin/doublecheckAuto/cron-other.php";
	$curl = curl_init(); //开启curl
	//$url.="?super=".$_REQUEST["super"];
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		
	curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
	$obj = curl_exec($curl); //执行curl操作
	curl_close($curl);
	$info=json_decode($obj,true);

	$url="https://shop.superdouble.org/admin/doublecheckAuto/cron-creditcard.php";
	$curl = curl_init(); //开启curl
	//$url.="?super=".$_REQUEST["super"];
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		
	curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
	$obj = curl_exec($curl); //执行curl操作
	curl_close($curl);
	$info=json_decode($obj,true);

	$url="http://www.atom3dp.com/paynow/autocheck.php?html";
	$curl = curl_init(); //开启curl
	//$url.="?super=".$_REQUEST["super"];
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		
	curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
	$obj = curl_exec($curl); //执行curl操作
	curl_close($curl);
	$info=json_decode($obj,true);	

	$url="https://wow.atom3dp.com/admin/doublecheckAuto/cron-other.php";
	$curl = curl_init(); //开启curl
	//$url.="?super=".$_REQUEST["super"];
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		
	curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
	$obj = curl_exec($curl); //执行curl操作
	curl_close($curl);
	$info=json_decode($obj,true);

	$url="https://wow.atom3dp.com/admin/doublecheckAuto/cron-creditcard.php";
	$curl = curl_init(); //开启curl
	//$url.="?super=".$_REQUEST["super"];
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		
	curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
	$obj = curl_exec($curl); //执行curl操作
	curl_close($curl);
	$info=json_decode($obj,true);	
}