<?php

include("dbset.php");

	$sql = "CREATE TABLE IF NOT EXISTS `".$dbset["db"]."`.`".$dbset["table"]["users"]."`(
	`id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`userid` VARCHAR(200) NOT NULL,
	`name` VARCHAR(200) NOT NULL,
	`email` VARCHAR(300) NOT NULL,
	`password` VARCHAR(200) NOT NULL,
	`role` VARCHAR(200) NOT NULL,
	UNIQUE (`userid`)
	)";

	if (mysqli_query($db_conn, $sql)) {
	    echo "使用者資料庫完成"."<br>";
	} else {
	    echo "使用者資料庫建立失敗: " . mysqli_error($db_conn)."<br>";
	     exit();
	}


?>