<?php
class postpicker{
	//頁面選擇器
	function generate_post_select( $post_type, $selected = "",$unset=array()) {
        $post_type_object = get_post_type_object($post_type);
        $label = $post_type_object->label;
        $posts = get_posts(array('post_type'=> $post_type, 'post_status'=> 'publish', 'suppress_filters' => false, 'posts_per_page'=>-1));

        echo '<option value = "" >All '.$label.' </option>';
        foreach ($posts as $post) {
        	if(in_array($post->ID, $unset) && $post->ID != $selected){
	    		continue;
	    	}
            echo '<option value="', $post->ID, '"', $selected == $post->ID ? ' selected="selected"' : '', '>', $post->post_title, '</option>';
        }

    }


}


 