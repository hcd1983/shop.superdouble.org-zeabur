<?php

class Slack{

	function sendMsg($token,$channel,$text,$username,$icon_url,$as_user=false){
		
		$ch = curl_init();

		$post=array(
			"token"=>$token,
			"channel"=>$channel,
			"text"=>$text,
			"username"=>$username,
			"icon_url"=>$icon_url,
			"as_user"=>$as_user,
		);

		// 設定擷取的URL網址
		curl_setopt($ch, CURLOPT_URL, "https://slack.com/api/chat.postMessage");
		//curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
		//將curl_exec()獲取的訊息以文件流的形式返回，而不是直接輸出。
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// 執行
		curl_exec($ch);

		// 關閉CURL連線
		curl_close($ch);

	}

}	

	