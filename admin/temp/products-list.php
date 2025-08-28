
<?php

$sql="SELECT * FROM `".$dbset["table"]["products"]."`";
$result=mysqli_query($db_conn, $sql);
$row=mysqli_fetch_array($result);
				
	if( mysqli_num_rows($result) ==0):
		$content='<p>目前尚無任何產品!</p>
			  	 <a href="?addnew" class="button">新增一個</a>
			  	 <div class="clear"></div>';
	else:
		$content='有產品';
	endif;



$script='
<script>
$( function() {
    $( "#products" ).sortable();
    $( "#products" ).disableSelection();
  } );
</script>
';
?>

<style>
.product-box{
	display: inline-block;
	float: none;
}
</style>
<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">

			<div class="col-md-12">

				<h2>產品列表</h2>



				<?php 

					echo $content;
				
				?>
								<!-- Portfolio Filter
				============================================= -->
				<ul class="portfolio-filter clearfix" data-container="#products">

					<li class="activeFilter"><a href="#" data-filter="*">Show All</a></li>
					<li><a href="#" data-filter=".pf-icons">Icons</a></li>
					<li><a href="#" data-filter=".pf-illustrations">Illustrations</a></li>
					<li><a href="#" data-filter=".pf-uielements">UI Elements</a></li>
					<li><a href="#" data-filter=".pf-media">Media</a></li>
					<li><a href="#" data-filter=".pf-graphics">Graphics</a></li>

				</ul><!-- #portfolio-filter end -->



				<div class="clear"></div>

				<!-- Portfolio Items
				============================================= -->
				<div id="products" class=" clearfix">

					<div class="col-md-3 col-sm-6 product-box">
						<div class="entry-image">
							<img src="../images/blog/grid/17.jpg" alt="Standard Post with Image" >
						</div>
						<h2><a href="blog-single.html">This iStandard post with a Preview Image</a></h2>
					</div>
					<div class="col-md-3 col-sm-6 product-box">
						<div class="entry-image">
							<img src="../images/blog/grid/17.jpg" alt="Standard Post with Image" >
						</div>
						<h2><a href="blog-single.html">This is a Stost with a Preview Image</a></h2>
					</div>
					<div class="col-md-3 col-sm-6 product-box">
						<div class="entry-image">
							<img src="../images/blog/grid/17.jpg" alt="Standard Post with Image" >
						</div>
						<h2><a href="blog-single.html">This is aost with a Preview Image</a></h2>
					</div>
					<div class="col-md-3 col-sm-6 product-box">
						<div class="entry-image">
							<img src="../images/blog/grid/17.jpg" alt="Standard Post with Image" >
						</div>
						<h2><a href="blog-single.html">This ipost with a Preview Image</a></h2>
					</div>
					<div class="col-md-3 col-sm-6 product-box">
						<div class="entry-image">
							<img src="../images/blog/grid/17.jpg" alt="Standard Post with Image" >
						</div>
						<h2><a href="blog-single.html">This is at with a Preview Image</a></h2>
					</div>
					

					

				</div><!-- #portfolio end -->

				<div class="clear"></div>

				
			</div>	

		</div>

	</div>

</section><!-- #content end -->



