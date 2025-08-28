<?php
/*
set_error_handler(function($errno, $errstr, $errfile, $errline){
				echo "<b>Custom error:</b> [$errno] $errstr<br>";
     			echo " Error on line $errline in $errfile<br>";
			});
trigger_error("A custom error has been triggered");
*/
define('TempPath',dirname(__FILE__));
define('TempFileName',"temp.php");
ini_set("display_errors",1);
class email_tpl_creator{
	
	public $style="style_1";
	public $filepath = TempPath;	
	public $filename = TempFileName;
	public $theme_setting=array();
	
	function __construct($style=null){
		if($style!==null){
			$this->style=$style;
		}
		//echo "<h3>Template Path: ";
		//echo $TempPath=$this->filepath."/".$this->style."/".$this->filename;
		//echo "</h3>";				
	}

	function Render(){

		$TempPath=$this->filepath."/".$this->style."/".$this->filename;
		if(file_exists($TempPath)){
			require_once $TempPath;	
		}else{
			trigger_error("檔案不存在!");
		}
	}

}



//$temp=new email_tpl_creator();
//$temp->theme_setting = $theme_setting_style_1;
//$temp->render();