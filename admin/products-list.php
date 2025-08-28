
<?php require_once("functions.php"); ?>
<?php notLogin("login.php"); ?>

<?php require_once("temp/manage-header.php"); ?>

<?php
if(isset($_GET["addnew"])):
	require_once("temp/products-addnew.php");
else:
	require_once("temp/products-list.php"); 
endif;

?>


<?php require_once("temp/manage-footer.php"); ?>