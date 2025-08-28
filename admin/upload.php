<?php 

require_once("functions.php");

notLogin("login.php"); 
	
require_once("temp/manage-header.php"); 


$action=$_GET["action"];

switch ($action) {
	case 'ktjno':
		
		$requireURL="uploadfunctions/ktjno.php";
		break;
	
	default:
		$requireURL="";
		break;
}




?>





<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">

		

			<div class="row clearfix">
				<div class="col-md-8 divcenter">

				<?php

					if($requireURL!=""){
						require_once($requireURL);
					}

				?>

					<div class="topmargin-sm">

						<a href="orderList.php" class="btn btn-primary">OK</a>
						
					</div>
				</div>
				
			</div>	
				
			


					

		</div>

	</div>

</section><!-- #content end -->


<?php require_once("temp/manage-footer.php"); ?>

