<?php

include("dbset.php");

	$sql = "CREATE TABLE IF NOT EXISTS `".$dbset["db"]."`.`".$dbset["table"]["settings"]."`(
	`id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`setting` VARCHAR(500) NOT NULL,
	`val` VARCHAR(500) NOT NULL,
	UNIQUE (setting)
	)";

	if (mysqli_query($db_conn, $sql)) {
	    echo "設定資料庫完成"."<br>";
	} else {
	    echo "設定資料庫建立失敗: " . mysqli_error($db_conn)."<br>";
	     exit();
	}


?>