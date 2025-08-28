<?php

include("dbset.php");

	$sql = "CREATE TABLE IF NOT EXISTS `".$dbset["db"]."`.`".$dbset["table"]["email"]."`(
	`id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`email` VARCHAR(200) NOT NULL,
	`status` VARCHAR(10) NOT NULL,
	UNIQUE (`email`)
	)";

	if (mysqli_query($db_conn, $sql)) {
	    echo "E-MAIL資料庫完成"."<br>";
	} else {
	    echo "設定資料庫建立失敗: " . mysqli_error($db_conn)."<br>";
	     exit();
	}


?>