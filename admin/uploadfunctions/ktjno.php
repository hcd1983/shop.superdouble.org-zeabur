
<?php

$functionUrl= dirname(dirname(__FILE__))."/functions.php";
require_once($functionUrl);





$uploaddir = 'uploads/';
$inputname = "file";

$pathfixURL = iconv("utf-8", "big5",$uploaddir.urlencode($_FILES["file"]["name"]));
$pathfix = iconv("utf-8", "big5",$uploaddir.$_FILES["file"]["name"]);
move_uploaded_file($_FILES[$inputname]["tmp_name"],$pathfix);


echo '<a href="'.$pathfixURL.'">'.'<h1>'.iconv("big5","utf-8",$pathfix).'</h1>'.'</a>'."</br>";



$fileName=$pathfix;


$array=csvToArray($fileName, ",");


//print_r($array);

if(!isset($array[0]["OrderNo"]) || !isset($array[0]["ShippingNum"])  ){

	echo "檔案格式錯誤! 請檢察檔案格式!";
	exit();
}




$ResaultCount=array();
$ResaultCount["S"]=0;
$ResaultCount["F"]=0;

foreach ($array as $key => $val) {



	$val["OrderNo"]=trim($val["OrderNo"]);

	$exist=check_value($val["OrderNo"],"OrderNo",$dbset["table"]["orders"]);


	if($exist==0){
		$ResaultCount["F"]++;
		echo "<div style='color:red'>"."訂單編號:".$val["OrderNo"]." "."託運單號:".$val["ShippingNum"]."<br>"."此訂單不存在"."<hr>"."</div>";

	}else{
		$ResaultCount["S"]++;
		echo "訂單編號:".$val["OrderNo"]." "."託運單號:".$val["ShippingNum"]."<hr>";
		AddIfNotExist($dbset["table"]["orders"],"ShippingNum",$val["ShippingNum"],"OrderNo",$val["OrderNo"]);

	}	
}
//echo $sql;


$msg="完成，共 ".count($array)." 筆資料! 失敗: ".$ResaultCount["F"]."筆";


echo $msg;

?>

<script>
alert("<?php echo $msg; ?>");
</script>

