<?php
// hook into the init action and call create_book_taxonomies when it fires
add_action( 'init', 'create_shipping_taxonomies', 0 );

// create two taxonomies, genres and writers for the post type "book"
function create_shipping_taxonomies() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => "運費群組",
		'singular_name'     => "運費種類",
		'search_items'      => "搜尋運費類別",
		'all_items'         => "所有運費類別",
		'parent_item'       => "副種類",
		'parent_item_colon' => "副種類",
		'edit_item'         => "編輯運費類別",
		'update_item'       => "更新運費類別",
		'add_new_item'      => "新增運費類別",
		'new_item_name'     => "新類別名稱",
		'menu_name'         => "運費群組管理",
	);

	$args = array(
		'public'			=> false,
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'SDshippingGroup' ),
	);

	register_taxonomy( 'SDshippingGroup', array( 'myproducts','group_product' ), $args );

}
/*
add_action( 'admin_menu', function() {
  global $submenu;
  $found = FALSE;
  $before = $after = array();
  $tax_slug = 'SDshippingGroup'; // change your taxonomy name here
  $another_tax_slug="product_cate";
  foreach ( $submenu['edit.php?post_type=myproducts'] as $item ) {
    if ( ! $found || $item[2] === 'edit-tags.php?taxonomy=' . $tax_slug ) {

      $before[] = $item;
    } else {
      $after[] = $item;
    }
    if( $item[2] === 'edit-tags.php?taxonomy='.$another_tax_slug ){
    	$found = TRUE;
    } 
  }
  $submenu['edit.php'] = array_values( array_merge( $before, $after ) );
}, 0 );
*/