<?php	
  
  function get_setting_value($_array,$key){
  	if(isset($_array[$key])){
  		echo $_array[$key];
  	}
  	echo  "";
  }	
  $mysetting=get_option('detect_user_login');   
  if($mysetting==false){
  	$mysetting=array();
  }

  


?>
<div class="wrap">
<h1>WP登入偵測設定</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'detect_user_login' ); ?>

    <table class="form-table">       
        <tr valign="top">
	        <th scope="row">金鑰</th>
	        <td>
	        	<td>
	        		<input id="the_key" type="text" name="detect_user_login[key]" value="<?php get_setting_value($mysetting,"key");?>">
	        		<a href="javascript:void(0)" class="button" onclick="generatePassword();">產生金鑰</a>
	        	</td>		
	        </td>
        </tr>

        <tr valign="top">
	        <th scope="row">訂單後台網址</th>
	        <td>
	        	<td>
	        		<input id="the_key" type="text" name="detect_user_login[url]" value="<?php get_setting_value($mysetting,"url");?>">
	        	</td>		
	        </td>
        </tr>
       

        
    </table>
    
    <?php submit_button(); ?>

</form>

	
</div>
<script>
	$=jQuery;
	function generatePassword() {
	    var length = 20,
	        charset = "#%*()!abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
	        retVal = "";
	    for (var i = 0, n = charset.length; i < length; ++i) {
	        retVal += charset.charAt(Math.floor(Math.random() * n));
	    }

	    $("#the_key").val(retVal);
	}
	
</script>	

