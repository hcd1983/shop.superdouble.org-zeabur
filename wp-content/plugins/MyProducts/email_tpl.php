<?php

class MyMailTpls{

	function MyMailTpls($tpl){		
		
		$path=MyProductsPluginPath."email_tpls/".$tpl.".php";
		if(file_exists($path )){						
			require_once $path;
		}else{
			echo json_encode(array("status"=>"F"));
			exit;
		}	
		
		
	}

}

add_action( 'wp_ajax_nopriv_MailTpls', 'ajax_mailTpls' );
add_action( 'wp_ajax_MailTpls', 'ajax_mailTpls' );
function ajax_mailTpls() {
	
    if(isset($_POST["tpl"]) && $_POST["tpl"]!=""){
    	$output=new MyMailTpls($_POST["tpl"]);
    	exit;
    }else{
    	echo json_encode(array("status"=>"F"));
    	exit;
    }
    
}



