<?php
add_filter('avf_builder_boxes', 'add_builder_to_posttype');

function add_builder_to_posttype($metabox)
{
  foreach($metabox as &$meta)
  {
    if($meta['id'] == 'avia_builder' || $meta['id'] == 'layout')
    {
      $meta['page'][] = 'myproducts'; /*instead add the name of the custom post type here*/
    }
  }
  
  return $metabox;
}

add_filter('the_content',"content_with_out_advance_builder");

function content_with_out_advance_builder($the_content){
  global $avia_config;
  
  $pid=get_the_ID();
  if(get_post_type($pid)!="myproducts" && is_single($pid)==false){
  	
  	return $the_content;
  }
  /*
  if(!isset($avia_config['conditionals'])){
  	return $the_content;
  }
	*/

  if($avia_config['conditionals']['is_builder'] == true){

   return $the_content;
  }

  

  $thumb2_ID=MultiPostThumbnails::get_post_thumbnail_id('myproducts', 'secondary-image', $pid);
  $thumb2_URL=wp_get_attachment_image_src($thumb2_ID,"entry_without_sidebar")[0];

  if($thumb2_URL !=""){
  	$bigpic_style="
  	.avia_transform a:hover span.image-overlay.overlay-type-image {
	    background-image: url(".$thumb2_URL.");
	    opacity: 1 !important;
	    background-size: cover;
	    background-position: center;
	}

  	";
  }else{
  	$bigpic_style="";
  }

  $bigpic_style="";
  

  $ProductsInfo=get_post_meta($pid,"ProductsInfo",true);

  $ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$pid);
  
  $price="[showprice class='' id='$pid']";

  $images=get_post_meta(get_the_ID(),"vdw_gallery_id",true);
  if(!is_array($images)){
  	$num=0;
  }else{
  	$num=count($images);
  }

  
  if($num>0){
		$images=join(",",$images);

		if($num < 5){
			$col=$num;
		}else{
			$col=5;
		}
		
		/*$shortcode="[av_masonry_gallery ids='".$images."' items='".$num."' columns='5' paginate='none' size='flex' orientation='' gap='no' overlay_fx='active' container_links='active' id='' caption_elements='none' caption_styling='' caption_display='always' color='' custom_bg='' av-medium-columns='' av-small-columns='' av-mini-columns='']";
		*/
		$shortcode="[av_gallery ids='".$images."' style='thumbnails' preview_size='portfolio' crop_big_preview_thumbnail='avia-gallery-big-crop-thumb' thumb_size='medium' columns='".$col."' imagelink='lightbox' lazyload='avia_lazyload' admin_preview_bg='']";
		$gallery_content = "<div class='mycart-infos-gallery'>".$shortcode."</div>";
		//$custom_content = $content.$custom_content;
  }else{
		$gallery_content ="";
  }
	
  

  if(wp_is_mobile()==false){
  	$mobile_style="
		.mycart-infos-price {
		    float: left;
		}

		.mycart-infos-btn{
			float: right;
		}
  	";
  }else{
  	$mobile_style="
  		.mycart-infos-btn{
			margin-top:20px;
		}
  	";
  }

  $style="
  		<style>

  		.mycart-infos-container:after {
		    content: '';
		    display: block;
		    clear: both;
		}
  		span.post-meta-infos {
		    display: none;
		}



		.mycart-infos-price {
		    
		    font-size: 22px;
		}

		

		.mycart-infos-gallery {
		    margin-top: 30px;
		}

		span.image-overlay-inside {
		    display: none;
		}

		".$mobile_style."

		".$bigpic_style."
  		</style>
  		";

  $html="<div class='mycart-infos-container'>
  			<div class='mycart-infos-price'>".$price."</div>
  			<div class='mycart-infos-btn'>[addtocart id=".$pid."]</div>
  		 </div>	
  		";

  $the_content=$style.$html.$the_content.$gallery_content;
   
  return $the_content;
}

//add_filter( 'template_include', 'enfold_product_template', 99 );
function enfold_product_template( $template ) {
  global $avia_config;
  
  $pid=get_the_ID();
  
  if(get_post_type($pid)!="myproducts" && is_single($pid)==false){  	
  	return $template;
  }

  if($avia_config['conditionals']['is_builder'] == true){
   return $template;
  }


  $template=MyProductsPluginPath."theme/"."enfold-single-product.php";
   
  return $template;
}