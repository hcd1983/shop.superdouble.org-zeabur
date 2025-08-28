<?php



add_filter("GetProductInfo","addGallerytoProductJson",9999,2);
function addGallerytoProductJson($ProductsInfo,$pid){

	$gallery = get_post_meta($pid, "vdw_gallery_id",true);

	$_gallery = [];
//    $_new_gallery = [];

	if(is_array($gallery)){

		foreach ($gallery as $_key => $pic) {
//			$image = wp_get_attachment_image_src( $pic,[500,500] )[0];
			$image = wp_get_attachment_image_src( $pic,'new_gallery_size' )[0];
			$_gallery[] = $image;
//            $_new_gallery[] = wp_get_attachment_image_src( $pic,'new_gallery' )[0];
		}

	}

	// $ProductsInfo["tester"] = wp_get_attachment_image_src( 1942, [800,800] )[0];

	// $ProductsInfo["gids"] = $gallery;
	$ProductsInfo["gallery"] = $_gallery;
//    $ProductsInfo["new_gallery"] = $_new_gallery;

	return $ProductsInfo;
	foreach ($output as $key => $procuct) {
		$output[$key]["teste"] = "cool";
		// $id = $procuct["id"];

		// $gallery = get_post_meta($id, "vdw_gallery_id");

		// $procuct["gallery"] = [];



		// $output[$key] = $procuct;
	}

	return $output;
}
