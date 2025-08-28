<?php


class TableCreater {
     
    
	 public $tableTitle;	
     public $tableCon;
     public $tableId="datatable-orderlist";
     public $className="order";
     public $dataName="mydata";
	
/*
		function TableHead(){
			global $TransCode;
			
			$th='<thead><tr>';


			foreach($this->tableTitle as $key => $val){

				$th.='<th>'.$TransCode["orderTitle"][$val].'</th>';

			}

			$th.='</tr></thead>';
                        
                              
			return $th;
		}
*/

		function TableHead(){
			global $TransCode;

			
			if(count($this->tableCon) == 0 ){
				return "";
			}

			$th='<thead><tr>';


			foreach($this->tableCon[0] as $key => $val){

				if(isset($TransCode["orderTitle"][$key])):
					$th.='<th>'.$TransCode["orderTitle"][$key].'</th>';
				else:
					$th.='<th>'.$key.'</th>';
				endif;	

			}

			$th.='</tr></thead>';
                        
                              
			return $th;
			
		}

		function ClassMaker(){
			

			$titles=array();

			foreach($this->tableCon[0] as $key => $val){
					$titles[]=$key;	
			}
			
			$OrderClass='function '.$this->className.'('.join(",",$titles).'){'." \n ";
            foreach ($titles as $key => $val) {
                 $OrderClass.="this.".$val."=".$val." \n ";
            } 

            $OrderClass.="}; \n";           
                              
			return $OrderClass;
			
		}

		function DataMaker(){

			$OrderData=$this->dataName."=["."\n";
								
			$row=array();
			
			foreach( $this->tableCon as $key => $val){
				
				$line=" new ".$this->className."(";	
				
				$content=array();
				foreach($this->tableCon[$key] as $key => $con){

					$con=str_replace("'","\\'",$con);

					$con=trim(preg_replace('/\s+/', ' ', $con));

					$content[]="'".$con."'";

					
					
				
				}
				$line.=join(",",$content);
				$line.=")";

				$row[]=$line;
						

			}

			$OrderData.=join(",\n",$row);

			$OrderData.="]"."\n";			     
                              
			return $OrderData;
			
		}

		function DataTableClumn(){
			

			$titles=array();

			foreach($this->tableCon[0] as $key => $val){
					$titles[]=" { data:'".$key."'}";	
			}

            $output= join(",\n",$titles);        
                              
			return $output;
		}



		function TableContent(){

			
			$i=0;
			$tbody = "";
			foreach( $this->tableCon as $key => $val){
				$tbody.="<tr data-allrover='".$i."' class='allrover_tr_".$i."'>";
				
				foreach($this->tableCon[$key] as $key => $con){
					$tbody.="<td>";
					$tbody.=$con;
					$tbody.="</td>";
				
				}
				

				$tbody.="</tr>";

				$i++;

			}

			

			return $tbody;


		}


		function TableMaker(){

			$table = "";
			$table .='<table id="'.$this->tableId.'" class="stripe row-border order-column" cellspacing="0" width="100%">';
			$table .= $this->TableHead();
			$table .='<tbody>';
			$table .=  $this->TableContent();
			$table .='</tbody>';
			$table .='</table>';

			return $table;

		}


}
