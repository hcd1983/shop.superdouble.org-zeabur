<?php


//ini_set('display_errors', '1');

$db_url = "localhost";

$db_user = "shuttlee__pochih";

$db_pass = "12qwaszx";

$db_name = "shuttlee_petpetco_allpay";

$db_table="allrover";

$db_conn = mysqli_connect($db_url, $db_user, $db_pass,$db_name);

$selectkeys=["time","treadno","paynowno","name","tel","email","address","item","payway","amount","status","sentnum"];

$selectkeys="`".join("`,`",$selectkeys)."`";

$sql = "SELECT ".$selectkeys." FROM `".$db_table."` ".$db_name." ORDER BY `id` DESC";

$result=mysqli_query($db_conn, $sql);

$output=array();	

$payway=array(
	"01"=>"信用卡",
	"03"=>"轉帳",
	"10"=>"超商"
);	

$titleTrans=array(
	"time"=>"下單時間",
	"treadno"=>"單號",
	"paynowno"=>"PayNow 單號",
	"name"=>"姓名",
	"tel"=>"電話",
	"email"=>"Email",
	"address"=>"地址",
	"item"=>"購賣物",
	"payway"=>"付款方式",
	"amount"=>"總金額",
	"status"=>"交易結果",
	"sentnum"=>"出貨號"
);

while($row = mysqli_fetch_assoc($result)):                                   

		$output[]=$row;
    
 endwhile;

$tableTitle=array();
foreach ($output[0] as $key => $val){
	
	if(isset($titleTrans[$key])){
		$tableTitle[]=$titleTrans[$key];
	}else{
		$tableTitle[]=$key;
	}
}

?>
<table>
	<tr>
		<?php
			foreach ($tableTitle as $key => $value) {
				echo "<th>".$value."</th>";
			}
		?>
	</tr>

	<?php 
		foreach ($output as $key => $val) {
			echo "<tr>";
				foreach ($val as $key => $cell) {

					if(in_array($key, ["tel","paynowno"])){
						$cell="'".$cell;
					}

					if($key=="payway"){
						$cell=$payway[$cell];
					}
					
					echo "<td>".$cell."</td>";
				
				}
			echo "</tr>";
		}
	?>	
</table>

               