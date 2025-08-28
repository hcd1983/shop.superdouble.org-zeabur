<?php
if(!class_exists ( "ImageUploader" )):
class ImageUploader{
	
	public $meta_key;
	public $imagesize='thumbnail';
	public $type='post';

/*
	function __construct(){
		
		//add_action( 'admin_enqueue_scripts', array($this,'misha_include_myuploadscript') );
		//add_action( 'save_post', array($this,'misha_save') );
	}
*/
	function ImageUploader($meta_key,$imagesize='thumbnail',$type='post',$value=''){
		global $post;
		$this->meta_key=$meta_key;
		$this->imagesize=$imagesize;
		$this->type=$type;
		if($type=="post"){
	    	$this->misha_print_box( $post);
	    }

	    if($type=="options"){
	    	$this->misha_print_options_box($value);
	    }

	    if($type=="cate"){
	    	$this->misha_print_cate_box($value);
	    }
		
	}

	
	

	function misha_image_uploader_field( $name, $value = '') {
	    $image = ' button">Upload image';
	    $image_size = $this->imagesize; // it would be better to use thumbnail size here (150x150 or so)
	    $display = 'none'; // display state ot the "Remove image" button

	    if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {

	        // $image_attributes[0] - image URL
	        // $image_attributes[1] - image width
	        // $image_attributes[2] - image height

	        $image = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
	        $display = 'inline-block';

	    } 

	    return '
	    <div>
	        <a href="#" class="misha_upload_image_button' . $image . '</a>
	        <input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
	        <a href="#" class="misha_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
	    </div>
	    <script>
	    	var __imagesize = "'.$image_size.'";
	    </script>
	    ';
	}

	function misha_print_box( $post ) {
	    $meta_key = $this->meta_key;
	    $value=get_post_meta($post->ID, $meta_key, true);
	   
	   
	    echo $this->misha_image_uploader_field( $meta_key, $value );
	}

	function misha_print_options_box( $value ) {
	   $meta_key = $this->meta_key;
	    echo $this->misha_image_uploader_field( $meta_key, $value );
	}

	function misha_print_cate_box( $value ) {
	   $meta_key = $this->meta_key;
	    echo $this->misha_image_uploader_field( $meta_key, $value );
	}


}

add_action( 'admin_enqueue_scripts', 'misha_include_myuploadscript' );
function misha_include_myuploadscript($hook) {
	//var_dump($hook);
    /*
     * I recommend to add additional conditions just to not to load the scipts on each page
     * like:
     * if ( !in_array('post-new.php','post.php') ) return;
     */
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
    //echo plugins_url( 'js/imageuplaoder.js', __FILE__ );
    wp_enqueue_script( 'myuploadscript',plugins_url( 'js/imageuploader.js', __FILE__ ), array('jquery'), null, false );
}
endif;


