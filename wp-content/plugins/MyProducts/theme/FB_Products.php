<?php
header("Content-Type:text/html; charset=utf-8");
function changeval($val){

	//$val=mb_convert_encoding($val, 'big5', 'UTF-8');
	$val=trim(preg_replace('/\s+/', ' ', $val));
	//$val=trim(preg_replace("/\s+/", "\r\n", $val));
	return $val;
}

function array_to_csv_download($array) {

	$delimiter=",";

	$time=date('ymdHis');

	$f = fopen('php://output', 'w');



	foreach ($array as $line) {

		foreach ($line as $key => $val) {

			
			$line[$key]=changeval($val);

		}
		
		fputcsv($f, $line, $delimiter);
	}
}


function unshiftArrayKey($array){

    	$header=array();


    	foreach ($array[0] as $key => $val){
    		$header[]=$key;    		
    	}


    	array_unshift($array , $header);
		return $array;   	
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





$picsize="myproduct";


$contentimage=0;


$default=array(
	"numberposts"=>-1,
	'post_type'=>"myproducts",
	'post_status'=> 'publish',
	'fields'=>'ids'
);



$args=$default;



$posts = get_posts($args);

$posts = apply_filters("product_list_for_fb",$posts);

$output=array();

foreach ($posts as $key => $post_id) {
	$parent_id = wp_get_post_parent_id( $post_id );
	
	if($parent_id){
		continue;	
	}

	$stock = "in stock";
	if(get_total_store_by_id($post_id) == 0){
		$stock = "out of stock";
	}
	$ProductsInfo=get_post_meta( $post_id, "ProductsInfo", $single = true );
	$ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$post_id);
	if($ProductsInfo["price"]==""){
		continue;
	}

	if($ProductsInfo["onsale"] == 1 && $ProductsInfo["saleprice"]){
		// $sale_price = $ProductsInfo["saleprice"];
		$price = $ProductsInfo["saleprice"];
	}else{
		// $sale_price = $ProductsInfo["price"];
		$price = $ProductsInfo["price"];
	}

	$FBInfo["id"]=$post_id;
	
	$FBInfo["condition"]="new";
	$FBInfo["availability"]=$stock;
	$FBInfo["image_link"]=get_the_post_thumbnail_url($post_id,$picsize);
	$thumb2_ID=MultiPostThumbnails::get_post_thumbnail_id('myproducts', 'secondary-image', $post_id);
	$FBInfo["additional_image_link"]=wp_get_attachment_image_src($thumb2_ID,$picsize)[0];
	$ProductsInfo["description"]=get_the_excerpt( $post_id );
	$FBInfo["description"]=$ProductsInfo["description"];
	if($FBInfo["description"]==""){
		$FBInfo["description"]="尚無說明";
	}
	$FBInfo["link"]=get_permalink( $post_id );
	$FBInfo["title"]=get_the_title($post_id);
	$FBInfo["price"]=$price." TWD";
	// $FBInfo["sale_price"]=$sale_price." TWD";
	$FBInfo["brand"]=get_option('MyBrand');
	$FBInfo["google_product_category"]=get_option('MyGoogleProductCategory');

	//$FBInfo["google_product_category"]="Media > Music & Sound Recordings ";
	//$single["gender"]="unisex";
	//$single["color"]=$color["title"];

/*
	$ProductsInfo=get_post_meta( $post_id, "ProductsInfo", $single = true );
	$ProductsInfo = apply_filters("GetProductInfo",$ProductsInfo,$post_id);
	$ProductsInfo["id"]=$post_id;	
	$ProductsInfo["title"]=get_the_title($post_id);
	$ProductsInfo["imageUrl"]=get_the_post_thumbnail_url($post_id,$picsize);
	$ProductsInfo["class"]=wp_get_post_terms( $post_id, "product_cate",array('fields'=>'slugs') );
	$thumb2_ID=MultiPostThumbnails::get_post_thumbnail_id('myproducts', 'secondary-image', $post_id);
	$ProductsInfo["imageUrl2"]=wp_get_attachment_image_src($thumb2_ID,$picsize)[0];
	$ProductsInfo["description"]=get_post_field('post_content', $post_id);
	$ProductsInfo["description"]=get_the_excerpt( $post_id );
	$ProductsInfo["description"]=nl2br($ProductsInfo["description"]);
	$ProductsInfo["parent"]=wp_get_post_parent_id( $post_id ); //false when none;
	if($ProductsInfo["onsale"] == 1 ){
		$ProductsInfo["price"]=$ProductsInfo["saleprice"];
	}

	$att_id=get_post_thumbnail_id( $post_id );
	$ProductsInfo["all_image_size"]=my_get_image_size_links($att_id);
*/	
	$output[] = $FBInfo;
}

$output=unshiftArrayKey($output);
array_to_csv_download($output);
//echo json_encode($output,true);