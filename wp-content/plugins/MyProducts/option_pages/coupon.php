<?php	

    $MyOpts=get_option('MyCoupon');
    function get_setting_value($_array,$key){
  	if(isset($_array[$key])){
  		return $_array[$key];
  	}
  	return  "";
  }	

?>
<div class="wrap">
<h1>Coupon 設定</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'MyCoupon' ); ?>
    <table class="form-table">       
        <tr valign="top">
	        <th scope="row">啟用 Coupon</th>
	        <td>
	        	<select name="MyCoupon[active]">
	           		<option value="1" <?php if ( get_setting_value($MyOpts,"active") == 1 ) echo 'selected="selected"'; ?>>啟用</option>
    				<option value="0" <?php if ( get_setting_value($MyOpts,"active") == 0 ) echo 'selected="selected"'; ?>>停用</option>
                </select>		
	        </td>
        </tr>
              
    </table>
    
    <?php submit_button(); ?>

</form>

	
</div>
<script>
	
</script>	

