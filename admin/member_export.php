<?php 
ini_set('display_errors', 0);
require_once("functions.php");
notLogin("login.php");
class exportMember {
     
    public $type;
    public $title="member.csv";
    public $delimiter=",";
    public $Tarray;


    function exportCSV(){
    	global $dbset;
    	$time=date('ymdHis');
    	$new_db_conn = mysqli_connect($dbset["url"], $dbset["ur"], $dbset["pw"],"spinboxc_members");	


    	$sql="SELECT * FROM `members` WHERE `role` LIKE 'normal' ORDER BY FIELD(`country`,'') ASC,start DESC";
			
    	$result=mysqli_query($new_db_conn, $sql);
		
		$outputArray=array();	
		
		while($row = mysqli_fetch_assoc($result)):                                 
			$output=array();
			$output["country"]=urldecode($row["country"]);
			if($row["system"] ==0.8){
				$output["name"]=urldecode($row["name"]);
				$output["FirstName"]="";
	 			$output["LastName"]="";
			}else{
				$output["name"]="";
				$output["FirstName"]=urldecode($row["FirstName"]);
	 			$output["LastName"]=urldecode($row["LastName"]);
			}	
	 			 		
	 		$output["email"]=urldecode($row["email"]);
	 		$output["tel"]="'".urldecode($row["tel"]);
	 		$output["zip"]="'".urldecode($row["zip"]);
	 		$output["address"]=urldecode($row["address"]);
	 		$output["start"]=$row["start"];
	 		$output["update_time"]=$row["start"];
	 		$outputArray[]=$output;	
	        
	     endwhile;


    	$this->Tarray =	$outputArray;
    	$this->title="member_".$time.".csv";
    	$this->unshiftArrayKey();    

    }





    function unshiftArrayKey(){
    	
    	$TransCode=array();
    	$TransCode["name"]="姓名(舊版本)";	
    	$TransCode["start"]="註冊時間";	
    	$TransCode["update_time"]="最後更新";	

    	$header=array();
    	$headerTrans=array();
    	
    	foreach ($this->Tarray[0] as $key => $val){
    		$header[]=$key;
    		if(isset($TransCode[$key])):
				$headerTrans[]=$TransCode[$key];
			else:
				$headerTrans[]=$key;
			endif;
    		
    	}


    	//array_unshift($this->Tarray , $header);
    	array_unshift($this->Tarray , $headerTrans);
		   	
    }

    function changeval($val){
		//$val= '"'.str_replace(array(',','&nbsp;','<br>','<br/>','<br />'),array('，',' ',PHP_EOL,PHP_EOL,PHP_EOL),$val).'"';
		$val=mb_convert_encoding($val, 'big5', 'UTF-8');
		//$val=trim(preg_replace('/\s+/', ' ', $val));
		//$val=trim(preg_replace('/\s+/', "\r\n", $val));
		
		return $val;
	} 	
    

    function array_to_csv_download() {
		//header("content-type:application/csv;charset=UTF-8");
		//header('Content-Disposition: attachment; filename="'.$filename.'";');

		$array=$this->Tarray;
		$delimiter=$this->delimiter;

		header("Content-type:text/csv"); 
		header("Content-Disposition:attachment;filename=".$this->title); 
		header("Content-Type: application/vnd.ms-excel;");
		header('Cache-Control:must-revalidate,post-check=0,pre-check=0'); 
		header('Expires:0'); 
		header('Pragma:public');

		// open the "output" stream
		// see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
		$f = fopen('php://output', 'w');



		foreach ($array as $line) {

			foreach ($line as $key => $val) {

				
				$line[$key]=$this->changeval($val);

			}
			
			fputcsv($f, $line, $delimiter);
		}
	} 

	
     

}




$export=new exportMember;
$export ->exportCSV();
$export ->array_to_csv_download();