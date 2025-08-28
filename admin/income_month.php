<?php
ini_set("display_errors", 1);
require_once("functions.php");
notLogin("login.php"); 
require_once("temp/manage-header.php"); 
require_once("temp/process.php"); 

function add_to_cargo_for_list($title,$amount){
	global $cargo_for_list;
	// $key = array_search($title , array_column($cargo_for_list, 'title'));
	$key = false;
	foreach ($cargo_for_list as $_key => $_cargo) {
		if(isset($_cargo["title"]) && $_cargo["title"] == $title){
			$key = $_key;
			break;
		}
	}


	if($key === false ){
		
		$cargo_for_list[] = ["title"=>$title,"amount"=>$amount];

	}else{

		$cargo_for_list[$key]["amount"] += $amount;
	}
}

$query ="SELECT * FROM `orders` ";
//$query.="MAX(`reg_date`) AS `last_date`,";
$query.="WHERE `TranStatus` LIKE 'S' ";


$start="";
$end="";


	
if(isset($_GET["start"]) && $_GET["start"] != ""){
	$start = $_GET["start"];		
}else{
	$start = date('Y-m-01');
}

$query.=" AND `reg_date` >=  '{$start} 00:00:01'";

if(isset($_GET["end"]) && $_GET["end"] !=""){
	$end=$_GET["end"];
	
}else{
	$end = date("Y-m-t");
}

$query.=" AND `reg_date` <= '{$end}  23:59:59'";
// $query.="ORDER BY `id` DESC";

$result=mysqli_query($db_conn,$query);

$total_rows = mysqli_num_rows($result);

if(!$result){
    exit("Failed: ".mysqli_error($db_conn)." ".mysqli_errno($db_conn));
}

$total_income = 0;
$total_cargos = 0;
$cargo_for_list = [];

if($total_rows > 0){
	while ($row = mysqli_fetch_assoc($result)) {
						
		extract($row);
		$total_income += $TotalPrice;
		$_CargoList = unserialize($CargoList);
		
		foreach ($_CargoList as $key => $cargo) {
			
			$total_cargos += $cargo["amount"];

			add_to_cargo_for_list($cargo["title"],$cargo["amount"]);
		
		}
		
	}
}

$title = array_column($cargo_for_list, 'title');
array_multisort($title, SORT_DESC, $cargo_for_list);
// var_dump($cargo_for_list);
?>

<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">
			
			<div class="col-md-12 divcenter">
				<h3>月收入統計</h3>

				<div class="row clearfix">	
					<div class="col-md-8">
						<form action="" >
							<div class="input-daterange input-group">
								<input type="text" value="<?php echo $start;?>" name='start' class="sm-form-control tleft" placeholder="YYYY-MM-DD" autocomplete="off" >
								<span class="input-group-addon">to</span>
								<input type="text" value="<?php echo $end;?>" name='end' class="sm-form-control tleft" placeholder="YYYY-MM-DD" autocomplete="off" >
							</div>
							<div style="margin-top: 20px;">
								<a href="income_month.php" class="btn btn-danger">Clear</a>
								<input type="submit" class="btn btn-primary" name="coupon_filter" value="Submit">
							</div>
						</form>	
					</div>
					<div class="col-md-4"></div>
				</div>
				<h3>查詢日期 <?php echo "$start ~ $end"; ?></h3>
				<?php
				
				$coupons=[];
				
				if($total_rows=="0"):
					echo "無交易紀錄";
				else:
					
					echo "<table class=\"table table-striped table-bordered table-hover\">";
					echo "<thead>";
					echo "<tr>";
						echo "<th>成功交易</th>";
						echo "<th>營業額</th>";
						echo "<th>交易商品總數</th>";
					echo "</tr>";
					echo "</thead>";

					echo "<tr>";
					echo "<td>{$total_rows}</td>";
					echo "<td>{$total_income}</td>";
					echo "<td>{$total_cargos}</td>";

					echo "</tr>";


					echo "</table>";
				?>
					<h3>商品交易統整</h3>
				<?php
					echo "<table class=\"table table-striped table-bordered table-hover\">";
					echo "<thead>";
					echo "<tr>";
						echo "<th>商品名稱</th>";
						echo "<th>交易總數</th>";
					echo "</tr>";
					echo "</thead>";
					foreach ($cargo_for_list as $key => $cargo) {

						echo "<tr>";
						echo "<td>".urldecode($cargo["title"])."</td>";
						echo "<td>".$cargo["amount"]."</td>";
						echo "</tr>";
					}
					


					echo "</table>";
				
				?>	
				
				<?php
				endif;
				?>

				

			</div>

		</div>

	</div>

</section><!-- #content end -->


<?php require_once("temp/manage-footer.php"); ?>
<script type="text/javascript">
	$('.input-daterange').datepicker({ 
		   autoclose: true,
		   format: 'yyyy-mm-dd',
		   language:'zh'
	});    
</script>

<?php exit;
?>