<?php
require_once("../functions.php");

echo $_SERVER["REMOTE_ADDR"]."<br>"; 

echo "max_execution_time: ".ini_get('max_execution_time')."<br>";
echo "memory_limit: ".ini_get('memory_limit')."<br>";


exit();

?>