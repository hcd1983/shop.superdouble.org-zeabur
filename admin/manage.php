<?php require_once("functions.php"); ?>
<?php notLogin("login.php"); ?>
<?php
function EventsOutput($val){
	if(!is_array($val)){
		return "";
	}

	$output ="交易成功: ".$val["Count"];
	$output.="<br>";
    if(is_string($val["TotalPrice"])){
       $val["TotalPrice"] = intval($val["TotalPrice"]);
    }
	$output.="金額: $".number_format($val["TotalPrice"]);
	return $output;
}

$postdata = file_get_contents("php://input",'r');
if($postdata != ""){
	//echo $postdata;
	$ajax=true;
	$postdata  = json_decode($postdata,true);		
	$NowDate=$postdata["year"].'-'.$postdata["month"].'-1';
	$StartDate=$NowDate;
	$NowDatefix=date_create($NowDate);
	$EndDate= date_format($NowDatefix,'Y-m-t');
	//echo $postdata["year"];
	//exit();
}else{
	$ajax=false;
	$NowtDate=new DateTime(date('Y-m-d'));
	$StartDate=date('Y-m')."-01";
	$EndDate=$NowtDate->format('Y-m-t');
}

//$NowtDate=new DateTime(date('Y-m-d'));

//$sql="SELECT SUM(`TotalPrice`) AS `Total`,COUNT(*) AS `Count` FROM `".$dbset["table"]["orders"]."` WHERE (`reg_date` BETWEEN '".$StartDate." 00:00:00' AND '".$EndDate." 23:59:59') AND `TranStatus` LIKE 'S' ";
$sql ="SELECT `reg_date`,`TotalPrice` FROM `".$dbset["table"]["orders"]."` WHERE (`reg_date` BETWEEN '".$StartDate." 00:00:00' AND '".$EndDate." 23:59:59') AND `TranStatus` LIKE 'S' ORDER BY `reg_date` ASC";
//echo $sql;
$orderinfo=doSQLgetRow($sql);
//var_dump($orderinfo);
$events=array();
foreach($orderinfo as $key => $val){

    $date = new DateTime($val["reg_date"]);
	$datekey= $date->format('m-d-Y');
	if(isset($events[$datekey])){		
		$events[$datekey]["TotalPrice"]=$events[$datekey]["TotalPrice"]+$val["TotalPrice"];
		$events[$datekey]["Count"]++;
	}else{
		$events[$datekey]=array();
		$events[$datekey]["TotalPrice"]=$val["TotalPrice"];
		$events[$datekey]["Count"]=1;
	}	
}

$events=array_map("EventsOutput", $events);

if($ajax==true){
	$events=json_encode($events);
	echo $events;
	exit();
}
?>
<?php require_once("temp/manage-header.php"); ?>
<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">

			<div class="col-md-10 divcenter">

				<div class="events-calendar">
					<div class="events-calendar-header clearfix">
						<h2>逐月銷售量</h2>
						<h3 class="calendar-month-year">
							<span id="calendar-month" class="calendar-month"></span>
							<span id="calendar-year" class="calendar-year"></span>
							<nav>
								<span id="calendar-prev" class="calendar-prev"><i class="icon-chevron-left"></i></span>
								<span id="calendar-next" class="calendar-next"><i class="icon-chevron-right"></i></span>
								<span id="calendar-current" class="calendar-current" title="Got to current date"><i class="icon-reload"></i></span>
							</nav>
						</h3>
					</div>
					<div id="calendar" class="fc-calendar-container"></div>
				</div>
			</div>	
		</div>

	</div>

</section>
<script>
	Events=<?php echo json_encode($events);?>;
</script>	
<!-- #content end -->
<?php
$script='
	<link rel="stylesheet" href="css/calendar.css" type="text/css" />
	<script type="text/javascript" src="js/jquery.calendario.js"></script>
'; 
$script.="
<script>
		var cal = $( '#calendar' ).calendario( {
			onDayClick : function( \$el, \$contentEl, dateProperties ) {
				var y=dateProperties.year;
				var m=dateProperties.month;
				var d=dateProperties.day;
				var the_date=y+'-'+m+'-'+d;
				console.log(the_date);
				window.location='orderList.php?DateRange%5B%5D='+the_date+'&DateRange%5B%5D='+the_date+'&TransSuccess=S';
				
				/*for( var key in dateProperties ) {
					console.log( key + ' = ' + dateProperties[ key ] );
				}*/

			},
			caldata : Events
		} ),
		\$month = $( '#calendar-month' ).html( cal.getMonthName() ),
		\$year = $( '#calendar-year' ).html( cal.getYear() );

		$( '#calendar-next' ).on( 'click', function() {
			cal.gotoNextMonth( updateMonthYear );
		} );
		$( '#calendar-prev' ).on( 'click', function() {
			cal.gotoPreviousMonth( updateMonthYear );
		} );
		$( '#calendar-current' ).on( 'click', function() {
			cal.gotoNow( updateMonthYear );
		} );
		function updateMonthYear() {
			\$month.html( cal.getMonthName() );
			\$year.html( cal.getYear() );
			//AjaxOrders(cal.getYear(),cal.getMonth());
			getData(cal.getYear(),cal.getMonth());
			
		}


</script>		
";
require_once("temp/manage-footer.php"); ?>

<script>
/*
fetch('manage.php?year=2018&month=1',{credentials: 'include'})
  .then(function(response) {
    return response.text()
  }).then(function(text) {
      console.log(text)
  }).catch(function(err) {
      // Error :(
  })

*/



const getData =  async(year,month) => {
  const a = await fetch("manage.php",{
	    method: "POST",
	    credentials: 'include',
	    headers: {
	        'Accept': 'application/json',
	        'Content-Type': 'application/json'
	      },
	   body: JSON.stringify({
	        year: year,
	        month: month
	    })
	});
  const b = await a.json();
  const c = await console.log(b);
  const d = await cal.setData(b);
  //const c = await cal.setData( {'12-22-2017': "交易成功: 3<br>金額: $7,840"} );
 
  //const b = await fetch(“xxx.xxx.xx.x”)
}


function AjaxOrders(year,month){
	fetch("manage.php",{
	    method: "POST",
	    credentials: 'include',
	    headers: {
	        'Accept': 'application/json',
	        'Content-Type': 'application/json'
	      },
	   body: JSON.stringify({
	        year: year,
	        month: month
	    })
	}).then(function(response) {
	    return response.json()
	  }).then(function(j) {
	      console.log(j);
	      return j;
	  }).then(function(j){
	  	cal.setData( j );
	  })

}



</script>	