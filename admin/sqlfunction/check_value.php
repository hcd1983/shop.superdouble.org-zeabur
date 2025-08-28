<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept');
header("Content-Type:text/html; charset=utf-8");

$functionUrl= dirname(dirname(__FILE__))."/functions.php";

require_once($functionUrl);

$val=$_POST["val"];
$column=$_POST["column"];
$tableName=$_POST["tableName"];

echo check_value($val,$column,$tableName);


?>