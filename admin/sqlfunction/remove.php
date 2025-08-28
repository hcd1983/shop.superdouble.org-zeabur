<?php

$functionUrl= dirname(dirname(__FILE__))."/functions.php";

require_once($functionUrl);

$valarr=$_POST["valarr"];
$column=$_POST["column"];
$tableName=$_POST["tableName"];

echo removeFromTable($column,$valarr,$tableName);


?>