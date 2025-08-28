<?php
ini_set("display_errors", 1);
require_once("functions.php");
notLogin("login.php");
require_once("temp/manage-header.php");
require_once("temp/process.php");

$filter=$_GET;

$condition=new condition;
$condition->condtions_post=$filter;
$sql = $condition->Mysql();


$exportSql=$sql;
$orderinfo=doSQLgetRow($sql);
$orderNum=count($orderinfo);
//NUMBER > 0===============================================


$AllOrders=array();

foreach($orderinfo as $key => $row){

	$SingleOrder=new OrderListRow;
	$SingleOrder=$SingleOrder->CreateRowArray($row);
	$AllOrders[]=$SingleOrder;
}


$orderlist = new TableCreater ;
$orderlist->tableCon = $AllOrders;
//$orderlist->tableCon[]="receiver";

$forProcess=$TransCode["process"];


if( isRole(["soldier"],false) ){

	unset($forProcess["remove"]);

}




$processArr=array(

            array(
                "label"=>false,
                "type"=>"options",
                "name"=>"process",
                "options"=>$forProcess
            )

        );

$processOption=new inputsMaker;

$processIntput=$processOption-> MakInputs($processArr);

?>

<style type="text/css">
	body{
		min-height: 100vh;
	}
	th, td { white-space: nowrap; }
    div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }

    ul.orderinfo {
	    padding-left: 20px;
	}




	.content-wrap.fullscreen {
	    padding: 0;
	}

	.content-wrap.fullscreen .container.clearfix {
	    width: 100%;
	    /*height: 100vh;*/
	}

	tr.active td{
		background-color:#c0dae4 !important;
	}



</style>



<!-- Content
============================================= -->

<script>

var ActiveRow;

function SetActiveRow(e){

	var url=e.data("href");

	$("#TargetLink").attr("href",url);

	$("#TargetLink").click();

	$("tr").removeClass("active");

	ActiveRow=e.closest("tr").data("dt-row");

	var row=$("[data-allrover="+ActiveRow+"]");

	row.addClass("active");

	console.log(ActiveRow);

}


function removeThisRow(e){

	if (confirm('當真?')) {
	    // Save it!

	} else {
	    return false;
	}

	id=e.data("orderno");

	allrovernum=e.closest("tr").data("allrover");

	allrovernumClass=".allrover_tr_"+allrovernum;

	console.log(allrovernum);

	//return false;

	myform={
		"tableName":"<?php echo $dbset["table"]["orders"];?>",
		"column":"OrderNo",
		"valarr[]":id
	}

	 Sfunction=function (i){
	 					//alert(i);
	 					//console.log(i);
	 					//$("#admin-bar").append(alert_box("red",i));
	 					//return false;
						switch(i) {
						    case "S":
						        //e.closest("tr").remove();
						        //  $(allrovernumClass).remove();
						        table.row( e.closest("tr") )
						        .remove()
						        .draw();

						        break;
						    case "F":
						        $("#admin-bar").append(alert_box("red","寫入失敗，可能是資料庫連線有問題!"));
						        break;
						    default:
						    	console.log(i);
						        $("#admin-bar").append(alert_box("red","不明原因造成錯誤!"));

						}

					}


	do_sql(myform,'sqlfunction/remove.php',Sfunction);




}

function ExportNow(){
	$("#mainTable").attr("action","export.php?type=now");
	$("#mainTable").attr("method","POST");
	$("#mainTable").submit();

}


function SuperSearch(){
	var super_val=$("#super_val").val();
	window.location = "orderList.php?super="+super_val;
}

function runScript(e) {
    //See notes about 'which' and 'key'
    if (e.keyCode == 13) {
        const super_val=$("#super_val").val();
        window.location = "orderList.php?super="+super_val;
        console.log('nice',e)
        return false;
    }
}

function DoProcess(){
	$("#mainTable").attr("action","#");
	$("#mainTable").attr("method","POST");

	if (confirm('當真?')) {
	    // Save it!
	    $("#mainTable").submit();

	} else {
	    return false;
	}
}

</script>
<section id="content">

	<div class="content-wrap">
		<div class="container clearfix">
			<div class="col-md-12 divcenter">
				<form id="mainTable" action="#" method="POST" class="nomargin">
					<div id="admin-bar">
						<div class="row clearfix">
							<div class="col-md-6">
								<h2><?php echo $condition->condtions_title; ?></h2>
                                <div class="bottommargin-sm">
                                    <label for="limit">最多顯示數量</label>
                                    <select id="limit">
                                        <option value="50"
                                            <?php echo $condition -> limit == '50' ? 'selected' : '' ?>
                                        >
                                            50
                                        </option>
                                        <option value="100"
                                            <?php echo $condition -> limit == '100' ? 'selected' : '' ?>
                                        >100</option>
                                        <option value="250"
                                            <?php echo $condition -> limit == '250' ? 'selected' : '' ?>
                                        >250</option>
                                        <option value="500"
                                            <?php echo $condition -> limit == '500' ? 'selected' : '' ?>
                                        >500</option>
                                        <option value="1000"
                                            <?php echo $condition -> limit == '1000' ? 'selected' : '' ?>
                                        >1000</option>
                                        <option value="2000"
                                            <?php echo $condition -> limit == '2000' ? 'selected' : '' ?>
                                        >2000</option>
                                    </select>
                                </div>
							</div>
							<div class="col-md-6 tright">
								<h2> <?php echo $orderNum; ?> 筆資料</h2>
							</div>
						</div>
						<div class="bottommargin-sm">
							<a id="fullscreenGo" href="javascript:void(0)" onclick="Togglefullscreen()" class="btn btn-default">全螢幕/ 解除全螢幕</a>
							<a id="TargetLink" href="temp/ajax.php?OrderNo=AA0423auW" data-lightbox="ajax"  class="hidden" >編輯</a>
						</div>
						<?php
							if(isRole(["admin","soldier","wp_user"])):
						?>
							<div class="row clearfix">
								<div class="col-md-8">
									<!--<a href="?TransSuccess=S&HasShippingNum=F" class="btn btn-default">無出貨單號</a>-->
									<a href="?TransSuccess=S&HasShippingNum=F&SendStatusNo%5B%5D=KTJ&SendStatusNo%5B%5D=SF" class="btn btn-default">待交物流公司</a>
									<a href="temp/ajax-filter.php" data-lightbox="ajax" class="btn btn-primary" style="margin-left: 20px;">條件篩選器</a>
									<a href="orderList.php"  class="btn btn-danger" >停用篩選器</a>
								</div>
								<div class="col-md-4 tright">

<!--									<a href="export.php?type=all" class="btn btn-default">全部匯出</a>-->
									<a href="export.php?type=ktj" class="btn btn-default">匯出未出貨訂單</a>
									<input type="hidden" value="<?php echo urlencode($exportSql); ?>" name="sql">
									<a href="#" class="btn btn-danger" onclick="ExportNow()">匯出目前狀態</a>

								</div>
							</div>


							<div class="row clearfix topmargin-sm">

								<div class="col-md-8">
									<?php echo $processIntput; ?>
									<input type="hidden" value="process" name="action">
									<!--<input type="submit" class="btn btn-default" value="批次處理">-->
									<a  class="btn btn-default"  href="#" onclick="DoProcess()">批次處理</a>




								</div>
								<div class="col-md-4 tright">
									<input
                                            type="text"
                                            name="super"
                                            id="super_val"
                                            class="sm-form-control"
                                            style="max-width: 250px;display: inline-block;"
                                            onkeypress="return runScript(event)"
                                            value="<?php echo isset($_GET['super']) ? $_GET['super'] : '' ?>"
                                    >
									<a  class="btn btn-default"  href="#" onclick="SuperSearch()">搜索</a>
								</div>
							</div>

						<?php
							endif;
						?>
					</div>
					<div class="clear"></div>

					<div style="margin-top:10px;">
						<?php
							if($orderNum > 0):
								echo $orderlist->TableMaker();
							else:
								echo "無資料";
							endif;
						?>
					</div>


				</form>



			</div>




		</div>

	</div>

</section><!-- #content end -->


<?php require_once("temp/manage-footer.php"); ?>

<script>
	function SelectRemoveAll(){

		var status=0;

		if(this.status==0){

			$('table input[type="checkbox"]').prop('checked','checked');
			this.status=1;
			//console.log(status);
		}else{

			$('table input[type="checkbox"]').prop('checked','');
			this.status=0;
			//console.log(status);
		}

		//return this.status;

	}


	function Togglefullscreen(){
		//var fullscreen=true;


			$(".content-wrap").toggleClass("fullscreen")
			$("body").toggleClass("side-header-open");
			$("#header-trigger").toggle();
			$("footer").toggle();
			table.draw();

	}

	tablehei="500px";
	windowH=$(window).height();

	function tableHeight(){

		adminH=$("#admin-bar").height();
		windowH=$(window).height();
		footerH=$("footer").height();
		newScrollH=windowH-adminH-footerH-80;
		tablehei=newScrollH+"px";

		return windowH-200+"px";
    }



$(document).ready(function() {
    table = $('#datatable-orderlist').DataTable( {
        scrollY:        tableHeight(),
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
        searching: false,
        columns: [
    	<?php echo $orderlist->DataTableClumn(); ?>
    	],
    	columnDefs: [
		    { "orderable": false, "targets": 0 }
		  ],
       // ordering: false,
        fixedColumns:   {
            leftColumns: 2,
            rightColumns: 1
        },
        "order": [[11,'asc'],[ 3, 'desc' ]],
    } );


    $("#fullscreenGo").click();

});







</script>

<script>

    function updateThisRow(){


        //return false;

        myform=$("#updateForm").serializeObject();

       console.log(myform);

        $.ajax({
            type: "POST",
            url: "sqlfunction/insertIntoOrderlist.php",
            data: myform,
            dataType: "json", // Set the data type so jQuery can parse it for you
            success: function (data) {
                console.log("S");
                console.log(data);
                console.log(ActiveRow);

                table.row(ActiveRow).data(data);
                table.draw();

                $(".mfp-close").click();

              //  SEMICOLON.initialize.lightbox();

            }
        });


        //do_sql(myform,'sqlfunction/insertIntoOrderlist.php',Sfunction);



    }


    function updateThisRow2(){
        myform=$("#updateForm").serializeObject();

        $.ajax({
            type: "POST",
            url: "sqlfunction/insertIntoOrderlist.php",
            data: myform,
            dataType: "html", // Set the data type so jQuery can parse it for you
            success: function (data) {
                console.log("S");
                console.log(data);
                console.log(ActiveRow);



            }
        });
        //do_sql(myform,'sqlfunction/insertIntoOrderlist.php',Sfunction);
    }

    const _limit = document.getElementById('limit')
    _limit.onchange = function (e) {
        const val = e.target.value
        const currentURL = new URL(location.href)
        currentURL.searchParams.set('limit', val)
        location.href = currentURL.href
    }
</script>
