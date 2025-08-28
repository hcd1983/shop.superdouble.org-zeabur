<?php

$functionUrl= dirname(dirname(__FILE__))."/functions.php";

require_once($functionUrl);

$arrName=$_POST["arrName"];

$arr=$_POST[$arrName];

$tableName=$_POST["tableName"];

echo insert_table($arr,$tableName);


?>