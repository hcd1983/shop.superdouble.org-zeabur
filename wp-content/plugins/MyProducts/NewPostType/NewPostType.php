<?php
class MyCustomPostType{
    public $type_name;
    public $post_type;
    public $args=array(
            'public' => true,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'page-attributes'
            ), // Go to Dashboard Custom HTML5 Blank post for supports
            'can_export' => true, // Allows export in Tools > Export
            "show_ui"=>true,
            "show_in_nav_menus"=>true,
            "exclude_from_search"=>false,
            "publicly_queryable" =>true,
            'hierarchical' => true, // Allows your posts to behave like Hierarchy Pages
            'has_archive' => true,
        );

    function MyCustomPostType( $post_type="",$type_name="",$args=array()){
        if(post_type_exists($post_type) || $post_type==""){
            return ;
        }

        $this->type_name=$type_name;    
        $this->post_type=$post_type;
        $this->args["labels"]=$this->labels(); 
             
        $this->args=wp_parse_args( $args, $this->args ); 

        add_action('init', array($this,'create_post_type'));


    }

    function labels(){

        if($this->type_name==""){
            $type_name=$this->post_type;
        } else{
            $type_name=$this->type_name;
        }   

        return  array(
                'name' => __("所有".$type_name, 'MyCustomPostType'),
                'singular_name' => __($type_name, 'MyCustomPostType'),
                'add_new' =>  __("新".$type_name, 'MyCustomPostType'),
                'add_new_item' => __("新增".$type_name, 'MyCustomPostType'),
                'edit' =>  __("Edit", 'MyCustomPostType'),
                'edit_item' =>  __("Edit ".$type_name, 'MyCustomPostType'),
                'new_item' => __("New ".$type_name, 'MyCustomPostType'),
                'view' =>  __("View ".$type_name, 'MyCustomPostType'),
                'view_item' =>  __("View ".$type_name, 'MyCustomPostType'),
                'search_items' => __("Search ".$type_name, 'MyCustomPostType'),
                'not_found' =>  __("No Result", 'MyCustomPostType'),
                'not_found_in_trash' =>  __("Noting found.", 'MyCustomPostType'),
              //  'archives'=>__('KIMU Products', 'sb_collection'),
            );

    }


    function create_post_type(){
        register_post_type($this->post_type,$this->args);

    }
}
