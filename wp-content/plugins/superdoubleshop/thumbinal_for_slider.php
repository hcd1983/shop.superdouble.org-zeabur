<?php
add_filter('manage_main_slider_posts_columns', 'misha_featured_image_column',10,1);
function misha_featured_image_column( $column_array ) {

    // I want to add my column at the beginning, so I use array_slice()
    // in other cases $column_array['featured_image'] = 'Featured Image' will be enough
    $column_array = array_slice( $column_array, 0, 1, true )
    + array('featured_image' => 'Featured Image') // our new column for featured images
    + array_slice( $column_array, 1, NULL, true );
    return $column_array;
}

/*
 * This hook will fill our column with data
 */
add_action('manage_main_slider_posts_custom_column', 'misha_render_the_column', 10, 2);
function misha_render_the_column( $column_name, $post_id ) {

    if( $column_name == 'featured_image' ) {
        $imgId = get_field( "image", $post_id,false );
       
        // if there is no featured image for this post, print the placeholder
        if(  $imgId ) {
            // I know about get_the_post_thumbnail() function but we need data-id attribute here
//            $thumb_id = get_post_thumbnail_id(  $imgId );
            echo '<img data-id="' . $imgId . '" src="' . wp_get_attachment_image_src( $imgId )[0] . '" />';

        }

    }

}
