<?php 

require_once("functions.php");

notLogin("login.php"); 
	
require_once("temp/manage-header.php"); 


$uploader=new uploader;


?>





<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">

			<h2>各種上傳</h2>

			<div class="row clearfix">
				<div class="col-md-4">


					<div>
						<h4>大榮託運單號</h4>
						<?php echo $uploader->uploadForm(); ?>
					</div>

				</div>
				<div class="col-md-8">
				</div>
			</div>	
				
			


					

		</div>

	</div>

</section><!-- #content end -->


<?php require_once("temp/manage-footer.php"); ?>

