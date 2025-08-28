<?php require_once("functions.php"); ?>
<?php notLogin("login.php"); 
	
?>
<?php require_once("temp/manage-header.php"); ?>
<?php
								
	
	


	class UserListTable{
		public $USERS;
		public $titles=array("ID","暱稱","Mail","角色","操作");

		function TableHead(){

			$th='<thead><tr>';


			foreach($this->titles as $key => $val){

				$th.='<th>'.$val.'</th>';

			}

			$th.='</tr></thead>';
                        
                              
			return $th;
		}


		function roleConfirm($row){
			
			if(isLogin()["id"]==$row["id"]){

				return true;

			}

			if(isLogin()["role"]=="super"){

				return true;

			}

			if(isLogin()["role"]=="admin" &&  in_array($row["row"],array("super","admin"))){

				return true;

			}

			return false;
		
		}

		function SingleRow($row){

			global $TransCode;

			$row=urldecodeArray($row);

			$tr="<tr>";

			$tr.="<td>".$row["userid"]."</td>";
			$tr.="<td>".$row["name"]."</td>";
			$tr.="<td>".$row["email"]."</td>";
			$tr.="<td>".$TransCode["role"][$row["role"]]."</td>";

			$role=$row["role"];
			if( $this->roleConfirm($row) == true ){
				$btn1='<a href="editUser.php?id='.$row["id"].'" class="btn btn-primary">編輯</a>';
				$btn2=' <button type="button" class="btn btn-danger" onclick="removeThisRow($(this))" data-id="'.$row["id"].'">刪除</button>';
				$tr.="<td>".$btn1.$btn2."</td>";
			}else{
				$tr.="<td></td>";
			}

			$tr.="</tr>";

			return $tr;

		}


		function TableContent(){

			$tbody="";

			foreach( $this->USERS as $key => $val){

				$tbody.= $this->SingleRow($val);

			}

			return $tbody;


		}


		function TableMaker(){
			$table ='<table id="datatable-checkbox" class="table table-striped table-bordered bulk_action">';
			$table .= $this->TableHead();
			$table .='<tbody>';
			$table .=  $this->TableContent();
			$table .='</tbody>';
			$table .='</table>';

			return $table;

		}

	}


	$sql="SELECT * FROM `".$dbset["table"]["users"]."`  ORDER BY `id` DESC";
	$USERS=doSQLgetRow($sql);

	



	$userTable = new UserListTable;

	$userTable -> USERS = $USERS;


?>

<!-- Content
============================================= -->

<script>
function removeThisRow(e){



	if (confirm('確定刪除?')) {
	    // Save it!
	
	} else {
	    return false;
	}


	id=e.data("id");

	
	myform={
		"tableName":"<?php echo $dbset["table"]["users"];?>",
		"column":"id",
		"valarr[]":id
	}

	 Sfunction=function (i){

						switch(i) {
						    case "S":
						        e.closest("tr").remove();

						        break;
						    case "F":
						        $("form").prepend(alert_box("red","寫入失敗，可能是資料庫連線有問題!"));
						        break;
						    default:
						        $("form").prepend(alert_box("red","不明原因造成錯誤!"));
						}        

					}


	do_sql(myform,'sqlfunction/remove.php',Sfunction);

}

</script>
<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">

			<div class="col-md-10 divcenter">

				<h2>使用者列表</h2>

				<?php

					if(isRole(array("admin"))):

				?>
					<div class="bottommargin-sm">
						<a href="newUser.php" class="btn btn-default">新增</a>
					</div>
				<?php
					endif;
				?>

				<div>
					<?php echo $userTable->TableMaker(); ?>
				</div>
			</div>	

		</div>

	</div>

</section><!-- #content end -->


<?php require_once("temp/manage-footer.php"); ?>