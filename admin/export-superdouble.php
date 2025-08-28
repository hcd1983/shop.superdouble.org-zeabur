<?php require_once("functions.php"); ?>
<?php

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
			return "BS19010102BL";
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

	if(mb_substr($address, 0,2) === "香港"){
		return;
	}

	foreach ($Cargos as $key => $cargo) {	
		$cargo_id = $cargo["id"];
	}

	if(!in_array($cargo_id , [1323,1360,1336])){
		return;
	}

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

	if(count($_memo) > 1){
		if($_memo[0] == $_memo[1]){
			$CargoSn = Get_Cargo_Sn($_memo[0]);
			$CargoTitle= Get_Cargo_name($_memo[0]);
			$amount=2;
			tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn,$CargoTitle,$amount,$_Note1,$other,$class);
		}else{
			$CargoSn = Get_Cargo_Sn($_memo[0]);
			$CargoTitle= Get_Cargo_name($_memo[0]);
			$amount=1;
			tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn,$CargoTitle,$amount,$_Note1,$other,$class);
			$CargoSn = Get_Cargo_Sn($_memo[1]);
			$CargoTitle= Get_Cargo_name($_memo[1]);
			$amount=1;
			tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn,$CargoTitle,$amount,$_Note1,$other,$class);
		}

	}else{
		$CargoSn = Get_Cargo_Sn($_memo[0]);
		$CargoTitle= Get_Cargo_name($_memo[0]);
		$amount=1;
		tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn,$CargoTitle,$amount,$_Note1,$other,$class);
	}

	
	
	$other = "";		
	switch ($cargo_id) {
			
		case 1323:
			// 一手包辦獨享包

			// tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010201BK",$CargoTitle="快卡小包-萬用款",$amount=1,$_Note1,$other,$class);
			tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="YL19010307BK",$CargoTitle="快卡背包-配件-登山鑰匙圈",$amount=1,$_Note1,$other,$class);
			tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010303GR",$CargoTitle="贈品-羊毛氈小包",$amount=1,$_Note1,$other,$class);

			break;
		case 1360:
			// 一包大全配
			// tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010201BK",$CargoTitle="快卡小包-萬用款",$amount=1,$_Note1,$other,$class);
			tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010202BK",$CargoTitle="快卡小包-保溫款",$amount=1,$_Note1,$other,$class);
			tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010301BK",$CargoTitle="掛車卡扣組",$amount=1,$_Note1,$other,$class);
			tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="YL19010307BK",$CargoTitle="快卡背包-配件-登山鑰匙圈",$amount=1,$_Note1,$other,$class);
			tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010303GR",$CargoTitle="贈品-羊毛氈小包",$amount=1,$_Note1,$other,$class);

			break;
		case 1336:
			// 兩個對包
			tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010201BK",$CargoTitle="快卡小包-萬用款",$amount=2,$_Note1,$other,$class);
			tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="YL19010307BK",$CargoTitle="快卡背包-配件-登山鑰匙圈",$amount=2,$_Note1,$other,$class);
			tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010303GR",$CargoTitle="贈品-羊毛氈小包",$amount=2,$_Note1,$other,$class);

			break;		
		
		default:
			# code...

			break;
	}
	

}




function tr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="",$CargoTitle="",$amount=1,$Note1="",$other="",$class=""){
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
			case '出貨單備註':
				echo "<td>".$Note1."</td>";
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


$sql="SELECT * FROM `orders` WHERE `TranStatus` LIKE '%S%' AND `reg_date` > '2019-05-27'";    
$orderinfo = doSQLgetRow($sql);
$th = "出貨單號 姓名 郵遞區號 地址 電話 商品編號 商品名稱 銷售金額(單價) 折扣金額 統一編號 發票收件人姓名 發票郵遞區號 發票收件人地址 急件程度 是否安裝DM 併件編號 發票列印方式 發票號碼 發票檢查號碼 數量 發票備註 發票日期 客戶訂單編號 發票抬頭 夜間電話 行動電話 供應廠商代號 供應廠商email 流水號 會員編號 會員名稱 發票未稅金額合計 發票稅額合計 發票金額合計 代收貨款總金額 空值1 空值2 空值3 出貨單備註";
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
<h1>台灣的單</h1>
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

function Get_Cargo_hktr($order){
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

	if(mb_substr($address, 0,2) !== "香港"){
		return;
	}

	foreach ($Cargos as $key => $cargo) {	
		$cargo_id = $cargo["id"];
	}

	if(!in_array($cargo_id , [1323,1360,1336])){
		return;
	}

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

	if(count($_memo) > 1){
		if($_memo[0] == $_memo[1]){
			$CargoSn = Get_Cargo_Sn($_memo[0]);
			$CargoTitle= Get_Cargo_name($_memo[0]);
			$amount=2;
			hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn,$CargoTitle,$amount,$_Note1,$other,$class);
		}else{
			$CargoSn = Get_Cargo_Sn($_memo[0]);
			$CargoTitle= Get_Cargo_name($_memo[0]);
			$amount=1;
			hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn,$CargoTitle,$amount,$_Note1,$other,$class);
			$CargoSn = Get_Cargo_Sn($_memo[1]);
			$CargoTitle= Get_Cargo_name($_memo[1]);
			$amount=1;
			hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn,$CargoTitle,$amount,$_Note1,$other,$class);
		}

	}else{
		$CargoSn = Get_Cargo_Sn($_memo[0]);
		$CargoTitle= Get_Cargo_name($_memo[0]);
		$amount=1;
		hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn,$CargoTitle,$amount,$_Note1,$other,$class);
	}

	
	
	$other = "";		
	switch ($cargo_id) {
			
		case 1323:
			// 一手包辦獨享包

			// hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010201BK",$CargoTitle="快卡小包-萬用款",$amount=1,$_Note1,$other,$class);
			hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="YL19010307BK",$CargoTitle="快卡背包-配件-登山鑰匙圈",$amount=1,$_Note1,$other,$class);
			hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010303GR",$CargoTitle="贈品-羊毛氈小包",$amount=1,$_Note1,$other,$class);

			break;
		case 1360:
			// 一包大全配
			// hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010201BK",$CargoTitle="快卡小包-萬用款",$amount=1,$_Note1,$other,$class);
			hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010202BK",$CargoTitle="快卡小包-保溫款",$amount=1,$_Note1,$other,$class);
			hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010301BK",$CargoTitle="掛車卡扣組",$amount=1,$_Note1,$other,$class);
			hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="YL19010307BK",$CargoTitle="快卡背包-配件-登山鑰匙圈",$amount=1,$_Note1,$other,$class);
			hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010303GR",$CargoTitle="贈品-羊毛氈小包",$amount=1,$_Note1,$other,$class);

			break;
		case 1336:
			// 兩個對包
			// hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010201BK",$CargoTitle="快卡小包-萬用款",$amount=2,$_Note1,$other,$class);
			hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="YL19010307BK",$CargoTitle="快卡背包-配件-登山鑰匙圈",$amount=2,$_Note1,$other,$class);
			hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="18010303GR",$CargoTitle="贈品-羊毛氈小包",$amount=2,$_Note1,$other,$class);

			break;		
		
		default:
			# code...

			break;
	}
	

}

function hktr_maker($OrderNo,$name,$zip,$address,$tel,$CargoSn="",$CargoTitle="",$amount=1,$Note1="",$other="",$class=""){
	global $_hk_th;
	echo "<tr class='$class'>";
	foreach ($_hk_th as $key => $head) {
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
			case '產品代號':
				echo "<td>".$CargoSn."</td>";
				break;
			case '產品名稱':
				echo "<td>".$CargoTitle."</td>";
				break;
			case '數量':
				echo "<td>".$amount."</td>";
				break;
			case '出貨單備註':
				echo "<td>".$Note1."</td>";
				break;	
			case '合約編號':
				echo "<td>"."'220041103003"."</td>";
				break;
			case '寄件人姓名':
				echo "<td>"."superdouble"."</td>";
				break;
			
			case '寄件人地址':
				echo "<td>"."2F., No.122, Sec. 2, Minsheng E. Rd., Zhongshan Dist., Taipei City 104, Taiwan"."</td>";
				break;	
			case '寄件人郵遞區號':
				echo "<td>"."10467"."</td>";
				break;
			case '寄件人電話':
				echo "<td>"."02-25310121"."</td>";
				break;
			case '國家':
				echo "<td>"."香港"."</td>";
				break;																		
			
			default:
				echo "<td></td>";
				break;
		}
	}
		
	// echo "<td>".$other."</td>";
	echo "</tr>";
}
$hk_th = "出貨單號 姓名 郵遞區號 地址 電話 產品代號 產品名稱 銷售金額(單品總價值) 折扣金額 統一編號 發票收件人姓名 發票郵遞區號 發票收件人地址 急件程度 是否安裝DM 併件編號 發票列印方式 發票號碼 發票檢查號碼 數量 發票備註 發票日期 客戶訂單編號 發票抬頭 夜間電話 行動電話 供應廠商代號 供應廠商email 流水號 會員編號 會員名稱 發票未稅金額合計 發票稅額合計 發票金額合計 代收貨款金額 空值1 空值2 空值3 出貨單備註 合約編號 寄件人姓名 寄件人地址 寄件人郵遞區號 寄件人電話 交寄日期(年) 交寄日期(月) 交寄日期(日) 國家";

$_hk_th = explode(" ", $hk_th);
?>
<h1>香港的單</h1>
<table>
	<tr>
		<th>
			<?php
			echo join("</th><th>",$_hk_th);
			?>
		</th>
	</tr>

<?php

foreach ($orderinfo as $key => $order) {
	
	Get_Cargo_hktr($order);
	continue;


}
?>
</table>
<?php
exit;
