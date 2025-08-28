<?php
add_action('wp_head','redirect_to_new_site',1);
function redirect_to_new_site(){
	$id = get_the_ID();
	
	if($id){
		if(get_post_type($id) == "myproducts"){
		?>
			<script type="text/javascript">
				window.location.replace("https://superdouble.org/product/<?php echo $id;?>");
			</script>
		<?php	
			exit();
		}elseif(get_post_type($id) == "project"){
			$post = get_post($post_id); 
		?>
			<script type="text/javascript">	
				window.location.replace("https://superdouble.org/<?php echo $post->post_name; ?>");
			</script>	
		<?php	
			exit();
		}elseif($id == 22 || get_post_type($id) == "post"){
		?>
			<script type="text/javascript">	
				window.location.replace("https://superdouble.org/");
			</script>	
		<?php
			exit();	
		}	
	}

}