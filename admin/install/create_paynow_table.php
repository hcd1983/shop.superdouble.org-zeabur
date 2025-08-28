<?php

include("dbset.php");

	$sql = "CREATE TABLE IF NOT EXISTS `".$dbset["db"]."`.`".$dbset["table"]["orders"]."`(
	`id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	`OrderNo` VARCHAR(20) NOT NULL,
	`OrderType` VARCHAR (10) NOT NULL,
	BuysafeNo VARCHAR(20) NOT NULL,
	PassCode VARCHAR(300) NOT NULL,

	`buyer` VARCHAR(1000)  CHARACTER SET utf8 NOT NULL,
	`receiver` VARCHAR(1000)  CHARACTER SET utf8 NOT NULL,
	`TotalPrice` VARCHAR(50) NOT NULL, 
	`discount` VARCHAR(10) NOT NULL,
	`coupon` VARCHAR(50),
	`ReturnPrice` VARCHAR(50) DEFAULT '0',



	OrderInfo  VARCHAR(1000)  CHARACTER SET utf8 NOT NULL,


	ReceiverTel VARCHAR(50)  CHARACTER SET utf8 NOT NULL ,
	ReceiverName VARCHAR(50) NOT NULL,
	ReceiverEmail VARCHAR(300) CHARACTER SET utf8 NOT NULL,
	Note1  VARCHAR(600) CHARACTER SET utf8 NOT NULL,
	Note2  VARCHAR(1000) CHARACTER SET utf8 NOT NULL,
	PayType VARCHAR(10) NOT NULL,
		
	ErrDesc VARCHAR(150) NOT NULL,
	`Installment` VARCHAR(5) NOT NULL, 

	BankCode VARCHAR(5) NOT NULL,
	ATMNo VARCHAR(30) NOT NULL,
	IBONNO  VARCHAR(30) NOT NULL,
	
	BarCode1 VARCHAR(20) NOT NULL,
	BarCode2 VARCHAR(30) NOT NULL,
	BarCode3 VARCHAR(30) NOT NULL,

	NewDate VARCHAR(40) NOT NULL,
	DueDate VARCHAR(40) NOT NULL,

	TranStatus VARCHAR(5) NOT NULL,
	ShippingNum VARCHAR(100) NOT NULL, 

	`CargoList` VARCHAR(1000) NOT NULL,
	`CargoSentList` VARCHAR(1000) NOT NULL,
	`shippingFee` VARCHAR(6) NOT NULL,
	`SentLog` VARCHAR(2000) NOT NULL,
	SendStatus VARCHAR(100) NOT NULL,
	memo VARCHAR(10000) CHARACTER SET utf8 NOT NULL,

	`callback` VARCHAR(300),
	`callbackstatus` VARCHAR(10) NOT NULL,
	reg_date TIMESTAMP,
	`hidden` VARCHAR(10) NOT NULL ,
	`slackcheck` VARCHAR(5) NOT NULL,
	`return_url` VARCHAR(300) NOT NULL,
	UNIQUE (`OrderNo`)
	)";

	if (mysqli_query($db_conn, $sql)) {
	    echo "訂單資料庫完成"."<br>";
	} else {
	    echo "訂單資料庫建立失敗: " . mysqli_error($db_conn)."<br>";
	     exit();
	}


?>