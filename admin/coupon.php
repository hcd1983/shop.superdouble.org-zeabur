<?php
ini_set("display_errors", 1);
require_once("functions.php");
notLogin("login.php"); 
require_once("temp/manage-header.php"); 
require_once("temp/process.php"); 



$query ="SELECT DISTINCT `coupon`,";
//$query.="MAX(`reg_date`) AS `last_date`,";
$query.="count(`coupon`) AS `coupon_count`,";
$query.="sum(CASE WHEN `TranStatus` LIKE 'S' THEN `TotalPrice` ELSE 0 END) AS `sum_totalprice`,";
$query.="COUNT(CASE WHEN `TranStatus` LIKE 'S' THEN 1 END) AS `coupon_count_success`";
$query.=" FROM `orders`";
$query.=" WHERE `coupon` IS NOT NULL AND `coupon` NOT LIKE ''";
$start="";
$end="";
if(isset($_GET["coupon_filter"])){

	
	if(isset($_GET["start"]) && $_GET["start"] != ""){
		$start = $_GET["start"];
		$query.=" AND `reg_date` >=  '{$start} 00:00:01'";
	}

	
	if(isset($_GET["end"]) && $_GET["end"] !=""){
		$end=$_GET["end"];
		$query.=" AND `reg_date` <= '{$end}  23:59:59'";
	}
}

$query.=" GROUP BY `coupon`";
$query.=" ORDER BY `id` DESC;";
?>

<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">
			
			<div class="col-md-12 divcenter">
				<h3>Coupon 統計</h3>
				<div class="row clearfix">	
					<div class="col-md-8">
						<form action="" >
							<div class="input-daterange input-group">
								<input type="text" value="<?php echo $start;?>" name='start' class="sm-form-control tleft" placeholder="YYYY-MM-DD" autocomplete="off" >
								<span class="input-group-addon">to</span>
								<input type="text" value="<?php echo $end;?>" name='end' class="sm-form-control tleft" placeholder="YYYY-MM-DD" autocomplete="off" >
							</div>
							<div style="margin-top: 20px;">
								<a href="coupon.php" class="btn btn-danger">Clear</a>
								<input type="submit" class="btn btn-primary" name="coupon_filter" value="Submit">
							</div>
						</form>	
					</div>
					<div class="col-md-4"></div>
				</div>

				<?php
				
				$result=mysqli_query($db_conn,$query);
				if(!$result){
				    exit("Failed: ".mysqli_error($db_conn)." ".mysqli_errno($db_conn));
				}
				$total_rows=mysqli_num_rows($result);

				$coupons=[];

				if($total_rows=="0"):
					echo "無 Coupon 紀錄";
				else:
					
					echo "<table class=\"table table-striped table-bordered table-hover\">";
					echo "<thead>";
					echo "<tr>";
						echo "<th>Coupon</th>";
						echo "<th>輸入次數</th>";
						echo "<th>交易成功</th>";
						echo "<th>消費總金額</th>";
					echo "</tr>";
					echo "</thead>";

					while ($row=mysqli_fetch_assoc($result)) {
						$coupons[]=$row;
						$couponSingleUrl="coupon-single.php?coupon={$row["coupon"]}";
						$orderlistURL="orderList.php?DateRange%5B%5D={$start}&DateRange%5B%5D={$end}&coupon={$row["coupon"]}";
						$orderlistSuccessURL="orderList.php?DateRange%5B%5D={$start}&DateRange%5B%5D={$end}&coupon={$row["coupon"]}&TransSuccess=S";

						echo "<tr>";
						echo "<td><a href='{$couponSingleUrl}' >".$row["coupon"]."</a></td>";
						echo "<td><a href='{$orderlistURL}'>".$row["coupon_count"]."</a></td>";
						echo "<td><a href='{$orderlistSuccessURL}' >".$row["coupon_count_success"]."</a></td>";
						echo "<td>".$row["sum_totalprice"]."</td>";
						echo "</tr>";
					}

					echo "</table>";
				?>

				<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
			    <script type="text/javascript">
			      google.charts.load('current', {'packages':['corechart']});
			      google.charts.setOnLoadCallback(drawChart);

			      function drawChart() {

			        var data = google.visualization.arrayToDataTable([
			          ['Coupon', '成功交易次數'],
			          <?php
			          foreach ($coupons as $key => $row) {
			          	echo "['{$row["coupon"]}',     {$row["coupon_count_success"]}],";
			          }

			          ?>
			        ]);

			        var options = {
			          title: 'Coupon 成功交易次數'
			        };

			        var chart = new google.visualization.PieChart(document.getElementById('piechart_success'));

			        chart.draw(data, options);

			        var data = google.visualization.arrayToDataTable([
			          ['Coupon', '成功交易金額'],
			          <?php
			          foreach ($coupons as $key => $row) {
			          	echo "['{$row["coupon"]}',     {$row["sum_totalprice"]}],";
			          }

			          ?>
			        ]);

			        var options = {
			          title: 'Coupon 成功交易金額'
			        };

			        var chart = new google.visualization.PieChart(document.getElementById('piechart_totalprice'));

			        chart.draw(data, options);
			      }


			    </script>
			    <div class="row">
			    	<div class="col-md-12">
			    		<div id="piechart_success" style="width:100%; height: 500px;"></div>
			    	</div>
			    	<div class="col-md-12">
			    		<div id="piechart_totalprice" style="width:100%; height: 500px;"></div>
			    	</div>
				</div>
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
				SELECT
  p.StartDate,
  p.EndDate,
  h.PersonID
FROM Periods p
  LEFT JOIN History h
    ON h.[From] BETWEEN p.StartDate AND p.EndDate OR
       p.StartDate BETWEEN h.[From] AND ISNULL(h.[To], '30000101');