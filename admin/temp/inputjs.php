

	<!-- External JavaScripts
	============================================= -->
	
	<script type="text/javascript" src="js/plugins.js"></script>
	<script type="text/javascript" src="jquery-ui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/components/bs-select.js"></script>
	<script type="text/javascript" src="js/components/selectsplitter.js"></script>


	<!--<script type="text/javascript" src="js/components/bs-datatable.js"></script>-->
	<script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.2/js/dataTables.fixedColumns.min.js"></script>


	<!-- Date & Time Picker JS -->
	<script type="text/javascript" src="js/components/moment.js"></script>
	<script type="text/javascript" src="js/components/datepicker.js"></script>
	<script type="text/javascript" src="js/components/timepicker.js"></script>

	<!-- Include Date Range Picker -->
	<script type="text/javascript" src="js/components/daterangepicker.js"></script>



	<script type="text/javascript" src="js/components/bs-filestyle.js"></script>




	

	
	<script type="text/javascript" src="js/mysql.js?ver=1.2"></script>


	<!-- Footer Scripts
	============================================= -->
	<script type="text/javascript" src="js/functions.js"></script>
	<script>
	window.isMatch=function(fValue,fOriginal) {
		//Check if value, otherwise return true.
		if(fValue){if(fOriginal.indexOf(fValue)!='-1'){return true;}else{return false;}}else{return true;}
	}
	</script>

	<?php

	//var_dump($wordpress_setting);
	
	if (session_status() == PHP_SESSION_NONE) {
	    session_start();
	}    
	if(isset($wordpress_setting) && $wordpress_setting != false && $wordpress_setting["active"]==1){
		
	    if(isset($_SESSION["user"]["wp_login"]) && $_SESSION["user"]["wp_login"]==1){
	?>
		<script>
			jQuery.ajax({
			    url: "<?php echo urldecode($wordpress_setting["url"]);?>",
			    data: {
			        login_key: '<?php echo urldecode($wordpress_setting["key"]);?>'
			    },
			    type: 'POST'
			}).done(function( data ) {
			    data=JSON.parse(data);		
			    if(data.status=="F"){
			    	window.location.replace("login.php?logout");
			    }    
			});
		</script>	
	<?php    		
	}	    
	}

