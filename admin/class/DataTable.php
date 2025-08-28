<?php

class DataTable
{
	public $datas=array();
	public $dataTrans=array();
	public $Table_id="datatable";

	function __construct()
	{
		//echo "START";
	}

	function Render(){
		$datas=$this->datas;
		$dataTrans=$this->dataTrans;
		$Table_id=$this->Table_id;

		if(count($datas)==0){
			echo "No Datas!";
			return ;
		}
		$th=array();
		
		foreach ($datas[0] as $key => $val) {
			if(isset($dataTrans[$key])){
				$th[]=$dataTrans[$key];
			}else{
				$th[]=$key;
			}
			
		}

		$th_html="";
		foreach($th as $key => $val){
			$th_html.="<th>".$val."</th>";
		}

		$tbody_html="";

		foreach ($datas as $key => $val) {
			$tbody_html.="<tr>";
				foreach($val as $subkey => $output){
					$tbody_html.="<td class='".$Table_id."-".$subkey."'>".$output."</td>";
				}			
			$tbody_html.="</tr>";

		}

?>		
	<table id="<?php echo $Table_id;?>">
		<tr>
			<thead>
				<?php echo $th_html;?>
			</thead>
		</tr>
		
		<tbody>
			<?php echo $tbody_html;?>
		</tbody>
		
	</table>	

<?php
	}


}