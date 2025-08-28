<?php
ini_set("display_errors", 1);
require_once("functions.php");
notLogin("login.php"); 
require_once("temp/manage-header.php"); 
require_once("temp/process.php"); 

if(isset($_GET["coupon"]) && $_GET["coupon"] !=""){
	$g_coupon=$_GET["coupon"];
}

$date_query ="SELECT MIN(`reg_date`) AS `first_date`,";
$date_query.="MAX(`reg_date`) AS `last_date`";
$date_query.=" FROM `orders`";
$date_query.=" WHERE `coupon` LIKE '{$g_coupon}'";

?>

<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">
			
			<div class="col-md-12 divcenter">
				<h3>Coupon 逐年統計 : <?php echo $g_coupon;?></h3>				

				<?php
				
				$result=mysqli_query($db_conn,$date_query);
				if(!$result){
				    exit("Failed: ".mysqli_error($db_conn)." ".mysqli_errno($db_conn));
				}
				
				$row=mysqli_fetch_assoc($result);

				if($row["first_date"] !== NULL ){
					$first_date=$row["first_date"];
				}

				if($row["last_date"] !== NULL ){
					$end_date=$row["last_date"];
				}

				$y_start    = (new DateTime($first_date))->modify('first day of january this year');
				$y_end      = (new DateTime($end_date))->modify('last day of december this year');
				
				$y_interval = DateInterval::createFromDateString('1 year');
				$y_period   = new DatePeriod($y_start, $y_interval, $y_end);

				foreach ($y_period as $dt) {
				    echo "<h4>".$dt->format("Y")."年</h4>";
				    echo "<table class=\"table table-striped table-bordered table-hover\">";
					echo "<thead>";
					echo "<tr>";
						echo "<th></th>";
						echo "<th>1 月</th>";
						echo "<th>2 月</th>";
						echo "<th>3 月</th>";
						echo "<th>4 月</th>";
						echo "<th>5 月</th>";
						echo "<th>6 月</th>";
						echo "<th>7 月</th>";
						echo "<th>8 月</th>";
						echo "<th>9 月</th>";
						echo "<th>10 月</th>";
						echo "<th>11 月</th>";
						echo "<th>12 月</th>";
						echo "<th>Total</th>";
					echo "</tr>";
					echo "</thead>";

					$y_row=[];
					for ($i=1; $i <=12 ; $i++) { 
						
						$date_in_loop = $dt->format("Y")."-".$i;
						
						$start_in_loop = (new DateTime($date_in_loop))->modify('first day of this month');
						$end_in_loop   = (new DateTime($date_in_loop))->modify('last day of this month');

						$start = $start_in_loop->format("Y-m-d")." 00:00:01";
						$end   = $end_in_loop->format("Y-m-d")." 23:59:59";

						$query ="SELECT ";
						$query.="count(`coupon`) AS `coupon_count`,";
						//$query.="sum(CASE WHEN `TranStatus` LIKE 'S'  THEN `TotalPrice` ELSE 0 END) AS `sum_totalprice`,";
						$query.="
							 CASE  
							 WHEN sum(CASE WHEN `TranStatus` LIKE 'S'  THEN `TotalPrice` ELSE 0 END) IS NULL THEN 0
							 ELSE sum(CASE WHEN `TranStatus` LIKE 'S'  THEN `TotalPrice` ELSE 0 END)
							 END as `sum_totalprice`,
						"; 
						$query.="COUNT(CASE WHEN `TranStatus` LIKE 'S' THEN 1 END) AS `coupon_count_success`";
						$query.=" FROM `orders`";
						$query.=" WHERE `coupon` LIKE '{$g_coupon}'";
						$query.=" AND `reg_date` >=  '{$start}'";
						$query.=" AND `reg_date` <=  '{$end}'";

						$result = mysqli_query($db_conn,$query);						
						$row=mysqli_fetch_assoc($result);

						$row["orderlistURL"]="orderList.php?DateRange%5B%5D=".$start_in_loop->format("Y-m-d")."&DateRange%5B%5D=".$end_in_loop->format("Y-m-d")."&coupon={$g_coupon}";
						$row["orderlistSuccessURL"]="orderList.php?DateRange%5B%5D=".$start_in_loop->format("Y-m-d")."&DateRange%5B%5D=".$end_in_loop->format("Y-m-d")."&&coupon={$g_coupon}&TransSuccess=S";

						$y_row[]=$row;
																			
					}

					echo "<tr>";
					echo "<th>使用次數</th>";
					$total = 0;
					for ($i=0; $i <=11 ; $i++) {
						$total += $y_row[$i]["coupon_count"];
						echo "<td>"; 
						echo "<a href='".$y_row[$i]["orderlistURL"]."'>";
						echo $y_row[$i]["coupon_count"];
						echo "</a>";
						echo "</td>"; 
					}
					echo "<td>".$total."</td>";
					echo "</tr>";

					echo "<tr>";
					echo "<th>交易成功</th>";
					$total = 0;
					for ($i=0; $i <=11 ; $i++) {
						$total += $y_row[$i]["coupon_count_success"];
						echo "<td>"; 
						echo "<a href='".$y_row[$i]["orderlistSuccessURL"]."'>";
						echo $y_row[$i]["coupon_count_success"];
						echo "</a>";
						echo "</td>"; 
					}
					echo "<td>".$total."</td>";
					echo "</tr>";

					echo "<tr>";
					echo "<th>交易金額</th>";
					$total = 0;
					for ($i=0; $i <=11 ; $i++) {
						$total += $y_row[$i]["sum_totalprice"];
						echo "<td>"; 
						echo $y_row[$i]["sum_totalprice"];
						echo "</td>"; 
					}
					echo "<td>".$total."</td>";
					echo "</tr>";
					

					/*

					while ($row=mysqli_fetch_assoc($result)) {
						$coupons[]=$row;
						$couponSingleUrl="coupon-single.php?coupon={$row["coupon"]}";
						$orderlistURL="orderList.php?DateRange%5B%5D={$start}&DateRange%5B%5D={$end}&coupon={$row["coupon"]}";
						$orderlistSuccessURL="orderList.php?DateRange%5B%5D={$start}&DateRange%5B%5D={$end}&coupon={$row["coupon"]}&TransSuccess=S";

						echo "<tr>";
						echo "<td><a href='{$couponSingleUrl}' target='_blank'>".$row["coupon"]."</a></td>";
						echo "<td><a href='{$orderlistURL}' target='_blank'>".$row["coupon_count"]."</a></td>";
						echo "<td><a href='{$orderlistSuccessURL}' target='_blank'>".$row["coupon_count_success"]."</a></td>";
						echo "<td>".$row["sum_totalprice"]."</td>";
						echo "</tr>";
					}
					*/
					echo "</table>";

				}

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