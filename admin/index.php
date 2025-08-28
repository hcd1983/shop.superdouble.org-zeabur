<?php

require_once("functions.php");


if(isset($db_conn) && $db_conn!=false):


	gotoUrl("login.php");


else:	
	gotoUrl("install.php");

endif;