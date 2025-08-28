<?php
if(isset($_SERVER['HTTP_ORIGIN'])){
	header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
	//header('Access-Control-Allow-Origin: http://*.spinbox.cc');
	header('Access-Control-Allow-Credentials: true');

	header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept');

}

function filter_ptags_on_images($content)
{
    $content = preg_replace("/<img[^>]+\>/i", " ", $content);          
	//$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]>', $content);
	return $content;
}

function my_get_image_size_links($att_id) {

	/* If not viewing an image attachment page, return. */
	if ( !wp_attachment_is_image( $att_id ) )
		return;

	/* Set up an empty array for the links. */
	$links = array();

	/* Get the intermediate image sizes and add the full size to the array. */
	$sizes = get_intermediate_image_sizes();
	$sizes[] = 'full';

	/* Loop through each of the image sizes. */
	foreach ( $sizes as $size ) {

		/* Get the image source, width, height, and whether it's intermediate. */
		$image = wp_get_attachment_image_src( $att_id, $size );

		/* Add the link to the array if there's an image and if $is_intermediate (4th array value) is true or full size. */
		if ( !empty( $image ) && ( true == $image[3] || 'full' == $size ) )
			$links[$size] = array();
			$links[$size]["url"]=$image[0];
			$links[$size]["width"]=$image[1];
			$links[$size]["height"]=$image[2];
			//"<a class='image-size-link' href='{$image[0]}'>{$image[1]} &times; {$image[2]}</a>";
	}

	/* Join the links in a string and return. */
	return $links;
}

$payload = file_get_contents('php://input');

if($payload !=""){
	$data = json_decode($payload,true);
	//$setting=array_merge($_REQUEST,$data);
	$setting=$data;
}else{
	$setting=$_REQUEST;

}
//echo json_encode(["AAA"=>$setting]);
//exit;



$picsize="full";

if(isset($setting["picsize"]) ){
	$picsize=$setting["picsize"];
}

$contentimage=1;
if(isset($setting["contentimage"]) && $setting["contentimage"]==0){
	$contentimage=$setting["contentimage"];
}



$default=array(
	"numberposts"=>-1,
	'post_type'=>"myproducts",
	'post_status'=> 'publish',
	'fields'=>'ids',

);


if(isset($setting["args"]) && is_array($setting["args"])){
	$args=array_merge($default,$setting["args"]);
}else{
	$args=$default;
}


$posts=get_posts($args);


$output=array();

foreach ($posts as $key => $post_id) {
	
	$parent_id=wp_get_post_parent_id( $post_id );
	$title=get_the_title($post_id);
	if($parent_id){
		$parent_title = get_the_title($parent_id);
	}else{
		$parent_title = "";
	}

	$ProductsInfo = GetProductInfo($post_id);

	$ProductsInfo["id"]=$post_id;	
	$ProductsInfo["title"]=$title;
	$ProductsInfo["parent_title"]=$parent_title;
	$ProductsInfo["imageUrl"]=get_the_post_thumbnail_url($post_id,$picsize);
	$ProductsInfo["class"]=wp_get_post_terms( $post_id, "product_cate",array('fields'=>'slugs') );
	$thumb2_ID=MultiPostThumbnails::get_post_thumbnail_id('myproducts', 'secondary-image', $post_id);
	$ProductsInfo["imageUrl2"]=wp_get_attachment_image_src($thumb2_ID,$picsize)[0];
	$ProductsInfo["description"]=get_post_field('post_content', $post_id);
	$ProductsInfo["description"]=get_the_excerpt( $post_id );
	$ProductsInfo["description"]=nl2br($ProductsInfo["description"]);
	$ProductsInfo["parent"]=wp_get_post_parent_id( $post_id ); //false when none;
	if($ProductsInfo["onsale"] == 1 || $ProductsInfo["price"]==""){
		$ProductsInfo["price"]=$ProductsInfo["saleprice"];
	}

	$ProductsInfo["store"] = GetMyStore($post_id);
/*
	if($ProductsInfo["store"]==""){
		$ProductsInfo["store"]=0;
	}
*/
	$att_id=get_post_thumbnail_id( $post_id );
	$ProductsInfo["all_image_size"]=my_get_image_size_links($att_id);
	$ProductsInfo["all_image_size2"]=my_get_image_size_links($thumb2_ID);
	
	$ProductsInfo["permalink"]=get_permalink( $post_id );
	/*
	if($contentimage==0){
		$ProductsInfo["description"]=filter_ptags_on_images($ProductsInfo["description"]);
	}
	
	$ProductsInfo["description"]=$noimage."<br>".apply_filters('the_content', $ProductsInfo["description"]);
	*/
	$output[] = $ProductsInfo;
}

if(is_array($setting["fields"]) && count($setting["fields"]) > 0){

	$output=array_map(function($ProductsInfo){
		global $setting;
		foreach($ProductsInfo as $key => $field){
			
			if(!in_array($key, $setting["fields"])){
				
				unset($ProductsInfo[$key]);
			}
		}
		
		return $ProductsInfo;

	}, $output);
		
}

$ouptut = apply_filters("product_jason_output",$output);
echo json_encode($output,true);