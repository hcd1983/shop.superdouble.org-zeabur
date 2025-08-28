<?php	
  
  function get_setting_value($_array,$key){
  	if(isset($_array[$key])){
  		echo $_array[$key];
  	}
  	echo  "";
  }	
  $mysetting=get_option('shipping_fee');   
  if($mysetting==false){
  	$mysetting=array();
  }

  


?>
<div class="wrap">
<h1>運費設計</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'shipping_fee' ); ?>

    <table class="form-table">       
        <tr valign="top">
	        <th scope="row">基本運費</th>
	        <td>
	        	<td>
	        		<input  type="number" min="0" name="shipping_fee[basic]" value="<?php get_setting_value($mysetting,"basic");?>">
	        	</td>		
	        </td>
        </tr>

        <tr valign="top">
	        <th scope="row">折扣門檻 (超過__元免運) * -1門檻無上限</th>
	        <td>
	        	<td>
	        		<input  type="number" min="0" name="shipping_fee[over]" value="<?php get_setting_value($mysetting,"over");?>">
	        	</td>		
	        </td>
        </tr>       
        
    </table>
    
    <?php submit_button(); ?>

</form>

	
</div>
<script>
	$=jQuery;
	
	
</script>	

