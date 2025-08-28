<?php
class MyMetaBox{
    public $meta_slug;
    public $meta_name;
    public $post_type;
    public $callback;
    public $context;
        

    function MyMetaBox( $meta_slug="",$meta_name="",$post_type="",$callback,$savefun,$context="advanced"){
      
      if($post_type=="" ){
        return ;
      }

      if($meta_slug=="" ){
        return ;
      }

      if($meta_name==""){
        $meta_name=$meta_slug;
      }
      $this->meta_slug=$meta_slug;
      $this->meta_name=$meta_name;
      $this->post_type=$post_type;
      $this->callback=$callback;
      $this->context=$context;
      
      add_action( 'add_meta_boxes', array($this,"AddMetaBoxes") );
      add_action( 'save_post', $savefun );

      
    
    }

    function AddMetaBoxes(){
      $callback=$this->callback;
      if($callback=="" || !function_exists($callback)){
        return;
      }
      $meta_slug=$this->meta_slug;
      $meta_name=$this->meta_name;
      $screen=$this->post_type;
      $context = $this->context;

      add_meta_box(
              $meta_slug,
              $meta_name,
              $callback,
              $screen,
              $context
          );
    }

    
}









