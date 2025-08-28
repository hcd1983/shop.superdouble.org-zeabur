<?php	
  
  function get_setting_value($_array,$key){
  	if(isset($_array[$key])){
  		echo $_array[$key];
  	}
  	echo  "";
  }

   function return_setting_value($_array,$key){
  	if(isset($_array[$key])){
  		return $_array[$key];
  	}
  	return  "";
  }	
  $mysetting=get_option('shop_mail');   
  if($mysetting==false){
  	$mysetting=array();
  }

  $msg=get_option('shop_mail_msg');

  $logo=return_setting_value($mysetting,"logo");

?>
<div class="wrap">
<h1>訂單寄件設定</h1>

<form id="shop_mail_form" method="post" action="options.php">
    <?php settings_fields( 'shop_mail' ); ?>

    <table class="form-table">       
        <tr valign="top">
	        <th scope="row">標題</th>
	        <td>
	        	<td>
	        		<input style="width: 100%; max-width: 500px;" type="text" name="shop_mail[subject]" value="<?php get_setting_value($mysetting,"subject");?>">
	        		<p>[OrderNo] 代表單號<br>[Receiver] 代表使用者姓名<br>[OrderInfo] 插入購賣明細表</p>
	        	</td>		
	        </td>
        </tr>
        <tr valign="top">
	        <th scope="row">置頂圖片</th>
	        <td>
	        	<td>
	        		<?php
	        		$image_upload=new ImageUploader($meta_key="shop_mail[logo]",$imagesize='thumbnail',$type='options',$logo);
	        		?>
	        	</td>		
	        </td>
        </tr>
        <tr valign="top">
	        <th scope="row">信件內文</th>
	        <td>
	        	<td>

	        		<?php 
	        		
	        		wp_editor($msg, "shop_mail_msg");

	        		?>
	        		
	        	</td>
	        	
	        </td>
        </tr>
        
       

        
    </table>
    
    <?php submit_button(); ?>

</form>

<div>
<?php
	$OrderInfo=array(
		"OrderNo"=>"AA12345678",
		"BuysafeNo"=>"1234567890",
		"name"=>"老司機",
		"reg_date"=>"2018-05-30",
		"shippingFee"=>50,
		"TotalPrice"=>"2,300",
		"PayType"=>"信用卡",
		"CargoList"=>"<ul  style='width:450px;max-width:100%;'>
						<li>產品一 x 10<span style='float:right'>2,000</span>"."</li>
						<li>產品二 x 1<span style='float:right'>300</span>"."</li>
						</ul>",
		"buymsg"=>"",
		"Err"=>"",
		"TranStatus"=>"<span style='color:green;'>交易完成</span>",
		"buyerInfo"=>"
			<strong style='margin-right:20px;'>姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名</strong> <span>老司機</span>
			<div style='margin-top:10px'></div>
			<strong style='margin-right:20px;'>電&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;話</strong> 0912345678</span>
			<div style='margin-top:10px'></div>
			<strong style='margin-right:20px;'>電子信箱</strong> old-driver@in-my-car.com</span>
			<div style='margin-top:10px'></div>
			<strong style='margin-right:20px;'>郵遞區號</strong> 12345</span>
			<div style='margin-top:10px'></div>
			<strong style='margin-right:20px;'>地&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;址</strong> <span>老司機居無定所</span>
			",
		"receiverInfo"=>"<strong>同購買人</strong>",
		"Note1"=>"這是測試範例"
	);


	$url=admin_url('admin-ajax.php');
	//$url="https://shop.superdouble.org/wp-admin/admin-ajax.php";
	$postdata=array("action"=>"MailTpls",
					"tpl"=>"buy_mail",
					"OrderInfo"=>$OrderInfo,
			);
	$curl = curl_init(); //开启curl	
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);		
	curl_setopt($curl, CURLOPT_URL, $url); //设置请求地址
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postdata));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  //是否输出 1 or true 是不输出 0  or false输出
	$obj = curl_exec($curl); //执行curl操作
	curl_close($curl);
	$obj=json_decode($obj,true);
	echo "<h3>信件標題: ".$obj["subject"]."</h3>";
	echo $obj["msg"];
?>
	
</div>

<script>
	/*
	jQuery.ajax({
	        url: "https://shop.superdouble.org/wp-admin/admin-ajax.php",
	        data: {
	            action: "MailTpls",
	            tpl:"buy_mail"
	        },
	        type: 'POST'
	    }).done(function( data ) {
		    
		      console.log(data);
		    
		  });
	*/	  
</script>	