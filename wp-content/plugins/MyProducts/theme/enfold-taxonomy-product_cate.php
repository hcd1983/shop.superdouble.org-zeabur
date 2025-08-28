<?php
	if ( !defined('ABSPATH') ){ die(); }
	
	global $avia_config;

	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	 get_header();


 	 echo avia_title(array('title' => avia_which_archive()));
 	 
 	 do_action( 'ava_after_main_title' );
	 ?>

		<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

			<div class='container'>

				<main class='template-page template-portfolio content  <?php avia_layout_class( 'content' ); ?> units'>

                    <div class="entry-content-wrapper clearfix">

                        <div class="category-term-description">
                            <?php echo term_description(); ?>
                        </div>

                    <?php
					$term=get_query_var( 'term' );
					echo do_shortcode('[my_product_list posts_per_page="-1" paginate=0 cat="'.$term.'"]');
                    ?>
                    </div>

                <!--end content-->
                </main>
				<?php

				//get the sidebar
				$avia_config['currently_viewing'] = 'portfolio';
				get_sidebar();

				?>

			</div><!--end container-->

		</div><!-- close default .container_wrap element -->


<?php get_footer(); ?>