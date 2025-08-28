<?php
add_action( 'init', 'my_product_sig_theme_setup' );
function my_product_sig_theme_setup(){
   

    if (function_exists('add_theme_support'))
    {
        
        add_image_size('myproduct', 600,600 , true); // Large Thumbnail
    
    }

}


add_filter( 'image_size_names_choose', 'my_product_image_sizes_choose' );
function my_product_image_sizes_choose( $sizes ) {
    $custom_sizes = array(
        'myproduct' => 'Product'
    );
    return array_merge( $sizes, $custom_sizes );
}
