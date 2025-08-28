<?php require_once("functions.php"); ?>
<?php
exit;
// YL19020101BK	快卡背包-運動款-碳黑色
// BS19010101BL	快卡背包-運動款-深藍色
// BS19010101GR	快卡背包-運動款-鈦灰色

// YL19020104BK	快卡背包-時尚款-終極時尚黑
// YL19020102BK	快卡背包-時尚款-時尚黑
// BS19010102BL	快卡背包-時尚款-時尚藍
// BS19010102GR	快卡背包-時尚款-時尚灰

// BS19010304BK	濕紙巾夾層
// 18010201BK	快卡小包-萬用款
// 18010202BK	快卡小包-保溫款


// YL19010307BK	快卡背包-配件-登山鑰匙圈
// 18010303GR	贈品-羊毛氈小包
function Get_Cargo_Sn($name){

	switch ($name) {
		case "快卡背包時尚款 – 終極黑":
			return "YL19020104BK";
			break;

		case "快卡背包運動款 – 碳黑色":
			return "YL19020101BK";
			break;

		case "快卡背包時尚款 – 碳黑色":
			return "YL19020102BK";
			break;	

		case "快卡背包運動款 – 深藍色":
			return "BS19010101BL";
			break;	

		case "快卡背包時尚款 – 深藍色":
			return "BS19010102B";
			break;	

		case "快卡背包運動款 – 鈦灰色":
			return "BS19010101GR";
			break;	

		case "快卡背包時尚款 – 鈦灰色":
			return "BS19010102GR";
			break;						
		
		default:
			return "";
			break;
	}

}

function Get_Cargo_name($name){

	switch ($name) {
		case "快卡背包時尚款 – 終極黑":
			return "快卡背包-時尚款-終極時尚黑";
			break;

		case "快卡背包運動款 – 碳黑色":
			return "快卡背包-運動款-碳黑色";
			break;

		case "快卡背包時尚款 – 碳黑色":
			return "快卡背包-時尚款-時尚黑";
			break;	

		case "快卡背包運動款 – 深藍色":
			return "快卡背包-運動款-深藍色";
			break;	

		case "快卡背包時尚款 – 深藍色":
			return "快卡背包-時尚款-時尚藍";
			break;	

		case "快卡背包運動款 – 鈦灰色":
			return "快卡背包-運動款-鈦灰色";
			break;	

		case "快卡背包時尚款 – 鈦灰色":
			return "快卡背包-時尚款-時尚灰";
			break;						
		
		default:
			return "";
			break;
	}
}

function replace_Note($note){
	$values  = ["快卡背包時尚款 – 終極黑","快卡背包運動款 – 碳黑色","快卡背包時尚款 – 碳黑色","快卡背包運動款 – 深藍色","快卡背包時尚款 – 深藍色","快卡背包運動款 – 鈦灰色","快卡背包時尚款 – 鈦灰色"];
	
	foreach ($values as $key => $value) {
		$note = str_replace($value, "", $note);
	}

	$note = str_replace("\r\n", "", $note);
	return $note;
}

function Get_Cargo_tr($order){
	extract($order);
	$Cargos = unserialize($CargoList);
	$_buyer = unserialize($buyer);
	$_receiver = unserialize($receiver);

	if(count($_receiver) > 0 && isset($_receiver["rname"]) && $_receiver["rname"] != ""){
		$name = urldecode($_receiver["rname"]);
		$tel = urldecode($_receiver["rphone"]);
		$zip = urldecode($_receiver["rzip"]);
		$address = urldecode($_receiver["raddress"]);
	}else{
		$name = urldecode($_buyer["bname"]);
		$tel = urldecode($_buyer["bphone"]);
		$zip = urldecode($_buyer["zip"]);
		$address = urldecode($_buyer["address"]);
	}

	// if(mb_substr($address, 0,2) === "香港"){
	// 	return;
	// }

	foreach ($Cargos as $key => $cargo) {	
		$cargo_id = $cargo["id"];
	}

	if(!in_array($cargo_id , [1336])){
		return;
	}

	$_email = urldecode($ReceiverEmail);
	$_memo = urldecode($memo);
	$_memo = explode("\r\n", $_memo);	
	$_Note1 = replace_Note(urldecode($Note1));		
	$_Note1 = nl2br($_Note1);
	

	$other = nl2br(urldecode($memo));
	$class = "";
	foreach ($_memo as $key => $value) {
		if( !in_optArray($value)){
			$class = "wrong";
		}
	}

	if(strrpos( $address , "popup") !== false){
		$class = "wrong";
	}

	if(strrpos( urldecode($memo) , "黑") === false){
		return;
	}

	if(count($_memo) > 1){
		if($_memo[0] == $_memo[1]){
			$CargoSn = Get_Cargo_Sn($_memo[0]);
			$CargoTitle= Get_Cargo_name($_memo[0]);
			$amount=2;
			// tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn,$CargoTitle,$amount,$_Note1,$other,$class);
		}else{
			$CargoSn = Get_Cargo_Sn($_memo[0]);
			$CargoTitle= Get_Cargo_name($_memo[0]);
			$amount=1;
			// tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn,$CargoTitle,$amount,$_Note1,$other,$class);
			$CargoSn = Get_Cargo_Sn($_memo[1]);
			$CargoTitle= Get_Cargo_name($_memo[1]);
			$amount=1;
			// tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn,$CargoTitle,$amount,$_Note1,$other,$class);
		}

	}else{
		$CargoSn = Get_Cargo_Sn($_memo[0]);
		$CargoTitle= Get_Cargo_name($_memo[0]);
		$amount=1;
		// tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn,$CargoTitle,$amount,$_Note1,$other,$class);
	}

	
	
	// $other = "";		
	switch ($cargo_id) {
			
		
		case 1336:
			// 兩個對包
			tr_maker($OrderNo,$name,$zip,$address,$tel,$_email,$_Note1,$other,$class);


			break;		
		
		default:
			# code...

			break;
	}
	

}




function tr_maker($OrderNo,$name,$zip,$address,$tel,$email,$Note1="",$other="",$class=""){
	global $_th;
	echo "<tr class='$class'>";
	foreach ($_th as $key => $head) {
		switch ($head) {
			case '出貨單號':
				echo "<td>".$OrderNo."</td>";
				break;
			case '姓名':
				echo "<td>".$name."</td>";
				break;
			case '郵遞區號':
				echo "<td>".$zip."</td>";
				break;
			case '地址':
				echo "<td>".$address."</td>";
				break;
			case '電話':
				echo "<td>'".$tel."</td>";
				break;
			case '商品編號':
				echo "<td>".$CargoSn."</td>";
				break;
			case '商品名稱':
				echo "<td>".$CargoTitle."</td>";
				break;
			case '數量':
				echo "<td>".$amount."</td>";
				break;
			case '備註':
				echo "<td>".$Note1."</td>";
				break;
			case 'Email':
				echo "<td>".$email."</td>";
				break;	
			case '客服備註':
				echo "<td>";
				// echo $cargo_id;
				echo $other;
				echo "</td>";
				break;												
			
			default:
				echo "<td></td>";
				break;
		}
	}
		
	// echo "<td>".$other."</td>";
	echo "</tr>";
}

function in_optArray($value){
	$values  = ["快卡背包時尚款 – 終極黑","快卡背包運動款 – 碳黑色","快卡背包時尚款 – 碳黑色","快卡背包運動款 – 深藍色","快卡背包時尚款 – 深藍色","快卡背包運動款 – 鈦灰色","快卡背包時尚款 – 鈦灰色"];
	return in_array($value, $values);
}


$sql="SELECT * FROM `orders` WHERE `TranStatus` LIKE '%S%' ";    
$orderinfo = doSQLgetRow($sql);
$th = "出貨單號 姓名 郵遞區號 地址 電話 Email 備註 客服備註";
$_th = explode(" ", $th);
?>
<style type="text/css">
	table td,table th{
		border: 1px solid #efefef;
	}
	tr.wrong td{
		color:red;
	}
</style>
<h1>兩個對包有黑色</h1>
<table>
	<tr>
		<th>
			<?php
			echo join("</th><th>",$_th);
			?>
		</th>
	</tr>

<?php

foreach ($orderinfo as $key => $order) {
	
	Get_Cargo_tr($order);
	continue;


}
?>
</table>
<?php
exit;