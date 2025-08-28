<?php

class inputsMaker{



	//public $arr;
	function MakInputs($arr){
		global $script,$AjaxScript;
		//$arr=$this->arr;

		$output="";

		$exist=array();

		foreach($arr as $key => $val){

			
			//$exist[$val["type"]]++;

			$input=new input;
			$input->val=$val;
			$output.=$input->IpuntType();

		}



		$ThisScript="";

		if(isset($exist["DateRange"]) && $exist["DateRange"] > 0){

			$ThisScript="
						$('.input-daterange').datepicker({ 
             			   autoclose: true,
             			   format: 'yyyy-mm-dd',
             			   language:'zh'
        				});        				  
        			    ";

		}


				


		if($ThisScript !=""){
			$script.="<script>".$ThisScript."</script>";
			$AjaxScript.="<script>".$ThisScript."</script>";		
		
		}


		$output;	

		return $output;


	}
}



class input{

	public $val;
	public $default=array(
					"type"=>"text",
					"name"=>"test",
					"readonly"=>false,
					"required"=>false,
					"class"=>"sm-form-control"
				);

	

	function LabelCreater($val){
				
		if($val["label"] !=""):
			$label="<label>".$val["label"]."</label>";
		else:
			$label="";			
		endif;

		return $label;
	}

	function TagCreater($val){

		$tags=array();


		foreach($val as $ind => $value):
			
			switch ($ind) {

				case in_array($ind, ["label"]):
					break;
			    case in_array($ind, ["readonly","required"]):		        		        
			        	
		        	if($value==true):
		        		$tags[]=$ind;	
		        	endif;	
			        break;
			    default:
			    	if(!is_array($value) && $value !=""):
			        	$tags[]=$ind."='".$value."'";
			        endif;
			        break;    		      
		  
			}
		endforeach;

		$tagsOutput=implode(" ", $tags);

		return $tagsOutput;

	}


	function IpuntType(){



		$val=array_merge($this->default,$this->val);
		$label=$this->LabelCreater($val);
		$tagsOutput=$this->TagCreater($val);
		

		


		if(in_array($val["type"], ["text","password","number","email"])){


			$optionsoutput="<input ".$tagsOutput." />";

		}


		if($val["type"]=="DateRange"):

			$optionsoutput='<div class="input-daterange input-group">
										<input type="text" value="" name='.$val["name"].' class="sm-form-control tleft" placeholder="YYYY-MM-DD">
										<span class="input-group-addon">to</span>
										<input type="text" value="" name='.$val["name"].' class="sm-form-control tleft" placeholder="YYYY-MM-DD">
							</div>';
		endif;	

		
		if($val["type"]=="textarea"):

			$optionsoutput="<textarea ".$tagsOutput." >".$val["value"]."</textarea>";
		
		endif;	

		

		if($val["type"]=="options"):
			
			$optionsoutput="<select class='selectpicker' name='".$val["name"]."' >";

			  if(!isset($val["value"]) ){
			  	$optionsoutput.='<option value="" selected>選擇動作</option>';
			  }	

			  foreach ($val["options"] as $key => $opt){
			  	

			  	if(isset($val["value"]) && $key == $val["value"] ){

			  		$optionsoutput.='<option value="'.$key.'" selected>'.$opt.'</option>';
			  	}else{
			  		$optionsoutput.='<option value="'.$key.'">'.$opt.'</option>';
			  	}



			  }	
			  
			$optionsoutput.="</select>";
		endif;


		if($val["type"]=="checkbox" ):
			
			  $optionsArray=array();
			  
			  foreach ($val["options"] as $key => $opt){

			  	if( isset($val["value"]) ){
			  		if(in_array( $key , $val["value"])){
			  			$optionsArray[]='<input type="checkbox" name="'.$val["name"].'" value="'.$key.'" checked> '.$opt;
			  		}else{
			  			$optionsArray[]='<input type="checkbox" name="'.$val["name"].'" value="'.$key.'" > '.$opt;
			  		}
			  		
			  	}else{
			  		$optionsArray[]='<input type="checkbox" name="'.$val["name"].'" value="'.$key.'" > '.$opt;
			  	}

			  }	

			  $optionsoutput=join("&emsp;",$optionsArray);
			  
		endif;



		if($val["label"]!=false){

			switch ($val) {
			    case in_array($val["type"], ["text","password","number","email"]):
			        $section= $label."<input ".$tagsOutput." />";
			        $section="<div class='col_full'>".$section."</div>\r\n";
			        break;
			    case in_array($val["type"], ["options","DateRange"]):
			        $section= $label."<br>".$optionsoutput;
			        $section="<div class='col_full'>".$section."</div>\r\n";
			        break; 
			    case in_array($val["type"], ["checkbox"]):
			        $section= $label."<br>".$optionsoutput;
			        $section="<div class='col_full'>".$section."</div>\r\n";
			        break;        
			    case in_array($val["type"], ["textarea"]):
			        $section= $label."<textarea ".$tagsOutput." >".$val["value"]."</textarea>";
			        $section="<div class='col_full'>".$section."</div>\r\n";
			        break;    
			    case in_array($val["type"], ["hidden"]):
			        $section= $label."<input ".$tagsOutput." />";
			        break; 
			    case in_array($val["type"],["coloroptions"]):
			    	$section= $label.$coloroptionsoutput;
			    	$section="<div class='col_full' id='".$val["id"]."'>".$section."</div>\r\n";
			    	break; 

			    }	

			    $output=$section;	
		}else{

				$output=$optionsoutput;
		}



		return $output;

	}

	


}







?>