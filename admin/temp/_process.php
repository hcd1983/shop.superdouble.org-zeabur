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

			if($process=="RemoveSF"){

				ReplaceExistArray($dbset["table"]["orders"],"SendStatus","SF","","OrderNo",$orders);
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