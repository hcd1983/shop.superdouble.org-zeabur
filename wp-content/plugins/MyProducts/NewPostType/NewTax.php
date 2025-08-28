<?php

class MyCustomTax{
    public $tax;
    public $tax_name;
    public $post_type;
    public $args = array(
        //'labels'            => $labels,
        'public'            =>  true,
        'hierarchical'      =>  true,
        'show_in_nav_menus' =>  true,
        'has_archive'       =>  true,
       // 'rewrite'           =>  array('slug' => 'product_category', 'with_front' => true),
    );

    function MyCustomTax( $tax="",$tax_name="",$post_type="",$args=array()){
       
        if(taxonomy_exists($tax) || $tax==""){
            return ;
        }

        $this->tax_name=$tax_name;    
        $this->post_type=$post_type;
        $this->tax=$tax;
        $this->args["labels"]=$this->labels();            
        $this->args=wp_parse_args( $args, $this->args ); 
        add_action( 'init', array($this,'create_tax') );


    }

    function labels(){

        if($this->tax_name==""){
            $tax_name=$this->tax;
        } else{
            $tax_name=$this->tax_name;
        }   

        return  array(
            'name'              => __($tax_name, 'MyCustomPostType'),
            'singular_name'     => __($tax_name, 'MyCustomPostType')
        ); 

    }


    function create_tax(){
        register_taxonomy($this->tax,$this->post_type,$this->args);
    }
    
}