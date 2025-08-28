<?php
ini_set('display_errors', 0);
include("dbset.php");

	$sql = "CREATE TABLE IF NOT EXISTS `".$dbset["db"]."`.`".$dbset["table"]["products"]."`(
	id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,	
	`product_id` VARCHAR(50) NOT NULL,
	`order` VARCHAR(20) NOT NULL,
	`title` VARCHAR(500) NOT NULL,
	`entitle` VARCHAR(500) NOT NULL,
	`img_url` VARCHAR(800) NOT NULL,
	`img_url2` VARCHAR(800) NOT NULL,
	`price` VARCHAR(100) NOT NULL,
	`saleprice` VARCHAR(100) NOT NULL,
	`store` VARCHAR(50) NOT NULL,
	`sold` VARCHAR(50) DEFAULT '0',		
	`coloroptions` VARCHAR(3000) NOT NULL,
	`description` VARCHAR(3000) NOT NULL,
	`options` VARCHAR(1000) NOT NULL,
	`weight` VARCHAR(50) NOT NULL,
	`shipping` VARCHAR(50) NOT NULL,	
	`settings` VARCHAR(1000) NOT NULL,
	`group` VARCHAR(500) NOT NULL,
	`available` VARCHAR(5) NOT NULL,
	`sale_start_date`  TIMESTAMP ,
	`sale_due_date` TIMESTAMP,	
	`reg_date` TIMESTAMP ,	
	
	UNIQUE (`product_id`)	
	
	)";


	if (mysqli_query($db_conn, $sql)) {
	    echo "產品資料庫完成"."<br>";
	} else {
	    echo "產品資料庫建立失敗: " . mysqli_error($db_conn)."<br>";
	    exit();
	}
