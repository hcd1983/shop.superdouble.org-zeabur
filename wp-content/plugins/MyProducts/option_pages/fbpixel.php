<?php	
	
    $pixelcode=get_option('pixelcode');
?>
<div class="wrap">
<h1>FB PIXEL CODE</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'MyPixel' ); ?>
    
    <table class="form-table">       
        <tr valign="top">
	        <th scope="row">FB pixel</th>
	        
        </tr>
        <tr>
        	<td >
	        	<textarea name="pixelcode"><?php echo $pixelcode;?></textarea>
	        </td>
        </tr>	
        

        
    </table>
    
    <?php submit_button(); ?>

</form>

	
</div>
