<?php

	//print_r($_POST);

	if(isset($_POST["action"])){

		$action=$_POST["action"];
		$orders=$_POST["order"];
		$process=$_POST["process"];

		if($action="process"){

			if($process=="KTJ"){

				AddIfNotExistArray($dbset["table"]["orders"],"SendStatus","KTJ","OrderNo",$orders);
			}

			
			if($process=="RemoveKTJ"){

				ReplaceExistArray($dbset["table"]["orders"],"SendStatus","KTJ","","OrderNo",$orders);
			}

			if($process=="SF"){
				AddIfNotExistArray($dbset["table"]["orders"],"SendStatus","SF","OrderNo",$orders);
                $ids = join("','",$orders);
				$sql = "UPDATE `orders` SET `isShipped` = 'S' WHERE `orders`.`OrderNo` IN ('".$ids."')";
                $result=mysqli_query($db_conn, $sql);
			}


			if($process=="RemoveSF"){
				ReplaceExistArray($dbset["table"]["orders"],"SendStatus","SF","","OrderNo",$orders);
                $ids = join("','",$orders);
                $sql = "UPDATE `orders` SET `isShipped` = 'F' WHERE `orders`.`OrderNo` IN ('".$ids."')";
                $result=mysqli_query($db_conn, $sql);
			}

			if($process=="remove"){

				removeFromTable("OrderNo",$orders,$dbset["table"]["orders"]);
				
			}

			if($process=="RemoveDbChecked"){

				ReplaceExistArray($dbset["table"]["orders"],"SendStatus","DbChecked","","OrderNo",$orders);
				
			}



			//$sql = "SELECT * FROM galleries WHERE id IN ('$ids')";


		}



	}

?>