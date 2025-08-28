<?php
// add_action("wp_head","IfAjaxOnTablet",1);
function IfAjaxOnTablet(){
  global $post;
  if(get_post_type($post->ID) !== get_option('AjaxPage_PostType')){
    return;
  }

  if(wp_is_mobile()){
    return;
  }

  if(isset($_GET["MyAjax"]) && $_GET["MyAjax"]==true){  
    return;
  }
  $url=get_permalink( 22 )."?AjaxOpen=".urlencode(get_permalink( $post->ID ));
?>
    <script>
      location.replace("<?php echo $url;?>");
    </script>  
<?php  
  exit;
}

// add_action("MyAjaxPageFooter","ajax_footer_superdouble");
function ajax_footer_superdouble(){
?>
<style type="text/css">
	div#dontbreak .container {
		max-width: 505px !important;
	}
		
</style>
<?php	
}
