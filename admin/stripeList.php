<?php
require_once("functions.php");
notLogin("login.php"); 
require_once("temp/manage-header.php"); 
require_once("temp/process.php"); 

function trMaker($val){
  global $stripe_setting;
  $refunded=json_decode($val["refunded"],true);
?>
              <tr id="<?php echo $val["custom_id"];?>">

                <td class="stripe_id" style="background:<?php echo $bg=$val["stripe_id"]==""?"transparent":"aquamarine"?>;">
                  <div>
                    <?php echo $val["stripe_id"]=$val["stripe_id"]==""?"Not generated yet":$val["stripe_id"];?>
                  </div> 
                  <div>
                  <?php echo date("y-m-d", strtotime($val["time"]));?>
                  </div>
                  <input type="text" readonly="" value="<?php echo $stripe_setting["url"]."?c_id=".$val["custom_id"];?>">
                  <div onclick='$(this).prev("").select();document.execCommand("copy");' class="inline-block">
                    <a href="javascript:void(0)" class='button button-mini button-black nomargin' >Copy</a>
                  </div>
                </td>
                <td class="buyer">
                  <div><?php echo $val["name"];?></div>
                  <div><?php echo $val["email"];?></div>
                </td>
                <td class="amount">$ <?php echo number_format($val["amount"]/100,2);?><br>USD</td>
                <td class="status">
                  <?php 
                    if($refunded["status"]=="succeeded"){
                      echo "<span style='color:red'>refunded</div>";
                    }elseif($val["status"]=="succeeded"){
                      echo "<span style='color:green'>".$val["status"]."</div>";
                    }else{
                      echo "<span style='color:orange'>".$val["status"]."</div>";
                    }
                  ?>
                </td>
                <td class="memo"><?php echo nl2br($val["memo"]);?></td>
                <td class="admin_memo"><?php echo nl2br($val["admin_memo"]);?></td>
                <td class="mail_log">
                  <?php echo '<div><a href="javascript:void(0)" class="button button-primary button-small" onclick="stripe_maillog(\''.$val["custom_id"].'\')">Mail Log</a></div>';?> 
                  <?php echo '<div><a href="javascript:void(0)" class="button button-blue button-small" onclick="send_stripe_message(\''.$val["custom_id"].'\')">Send Mail</a></div>';?>                  
                </td>
                <td class="center stripe_options">
                  
                  <?php 

                  //if($val["status"] != "succeeded"){
                    echo '<div><a href="javascript:void(0)" class="button button-blue button-small" onclick="stripe_edit(\''.$val["custom_id"].'\')">Edit</a></div>';
                  //}
                  
                  ?>

                  <?php 

                  if($val["status"] != ""){
                    echo '<div><a href="javascript:void(0)" class="button button-small" onclick="stripe_detail(\''.$val["custom_id"].'\')">Detail</a></div>';
                  }
                  
                  ?>

                  <?php 

                  if($val["status"] == "succeeded" && $refunded["status"] !="succeeded"){
                    echo '<div class="refund_btn"><a href="javascript:void(0)" class="button button-red button-small" onclick="stripe_refund(\''.$val["custom_id"].'\')">Refund</a></div>';
                  }
                  
                  ?>
                  
                  
                </td>
              </tr>
<?php
}


function StripeMetaSubSqlMaker($meta){
  $sql="(SELECT `value` FROM `stripe_meta` WHERE `meta` LIKE '$meta' AND `stripe_meta`.`custom_id` LIKE `stripe`.`custom_id` LIMIT 1) AS `$meta`";
  return $sql;
}
								

$sql="SELECT `time`,`custom_id`,`stripe_id`,`amount`,`status`,`refunded`,`memo`,`admin_memo` FROM `stripe` ORDER BY `status` WHERE `mode` LIKE '".$stripe_setting["mode"]."' DESC ,`id` DESC LIMIT 5000";
$sql="SELECT `time`,`custom_id`,`stripe_id`,`amount`,`status`,`refunded`,`memo`,`admin_memo`,".StripeMetaSubSqlMaker("name").",".StripeMetaSubSqlMaker("email")."  FROM `stripe` WHERE `mode` LIKE '".$stripe_setting["mode"]."' ORDER BY `id` DESC LIMIT 5000";
$orderinfo=doSQLgetRow($sql);
$orderNum=count($orderinfo);

?>

<style type="text/css">
	body{
		min-height: 100vh;
	}
	/*th, td { white-space: nowrap; }*/
    table#stripe_table{
    	width: 100%;
    }

    table#stripe_table th{
    	text-align: center;
    	border-bottom-width:5px; 
    	border-bottom-color:#000;
    	background: #333;
    	color:#FFF; 
    }

    table#stripe_table td,table#stripe_table th{
    	border-top:1px solid #efefef;
    	border-left:1px solid #efefef;
    	border-right:1px solid #efefef;
    	border-bottom:1px solid #efefef;
    	padding:3px;
    	
    }

    table#stripe_table td.status{
    	/*color:green;*/
    }

    table#stripe_table td.refunded{
    	/*color:red;*/
    }

    table#stripe_table td.admin_memo,table#stripe_table td.memo{
    	max-width: 150px;
      word-break: break-all;
    }
    table#stripe_table td.stripe_id{
    	max-width: 220px;
    }
	.stripe_options a.button.button-small{
		min-width: 100px;
	}
	
</style>



<!-- Content
============================================= -->


<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">
			
			<div class="col-md-12 divcenter">
				<form id="mainTable" action="#" method="POST" class="nomargin">	
				
					<div id="admin-bar">

						<div class="row clearfix">
							
							<div class="col-md-12 tright">
								<h2> <?php echo $orderNum; ?> 筆資料</h2>
							</div>
						</div>	
					
						
					</div>
					<div>
						<?php if($orderNum=="0"){echo "無資料";}?>
						<?php 
						if($orderNum>"0"){
						?>
						<table id="stripe_table">
							<tr>
								<th>Stripe ID/<br>Create Date/<br>Payment Url</th>
                <th>Customer</th>
								<th>Amount</th>
								<th>Status</th>               
								<th>Description</th>
								<th>Admin Memo</th>
                <th>Mail</th>
								<th>Options</th>
							</tr>
							<?php
								foreach ($orderinfo as $key => $val) {
                  trMaker($val);							
								}
							?>
						</table>	
						<?php	
						}
						?>  
					</div>	
					<div class="clear"></div>	

					<div style="margin-top:10px;">
						
					</div>


				</form>		

				

			</div>

			


		</div>

	</div>

</section><!-- #content end -->

<div class="modal fade" id="SetEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="edit_form" >
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Stripe id: </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
        <div class="modal-body for_show">
        
          <div class="col_full">
            <label for="shipping-form-message">付款人</label>
            <input id="stripe_name" class="sm-form-control" type="text" name="stripe[name]" value="">
          </div>
          <div class="col_full">
            <label for="shipping-form-message">聯絡信箱</label>
            <input id="stripe_email" class="sm-form-control" type="email" name="stripe[email]" value="">
          </div>
            
          <div class="col_full">
      			<label for="shipping-form-message">付款金額 (USD, 最小 0.5 USD)</label>
      			<input id="stripe_amount" class="sm-form-control" type="number"  min="0.5" step="0.01" name="stripe[amount]" value="">
      		</div>
    						
      		<div class="col_full">
      			<label for="shipping-form-message">付款訊息，給付款人看的備註</label>
      			<textarea id="stripe_memo"  class="sm-form-control" name="stripe[memo]" rows="6" cols="30"></textarea>
      		</div>	
    		
      		<div class="col_full">
      			<label for="shipping-form-message">管理備註(注意!前端將無法看到此連結，僅會出現在訂單後台的管理備註)</label>
      			<textarea id="stripe_admin_memo" class="sm-form-control" name="stripe[admin_memo]" rows="6" cols="30"></textarea>
      		</div>
    		  <input id="stripe_custom_id" class="sm-form-control" type="hidden"  name="stripe[custom_id]" value="">	
        </div>
        <div class="modal-footer">        
          <button  type="submit" class="for_show btn button">Submit</button>
        </div>
        <div class="modal-body for_loading" style="display: none;">
         <h3>Proccessing...</h3>
      </div>      
      </form>  
    </div>
    
  </div>
</div>

<div class="modal fade" id="SetDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Stripe id: </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <pre>        	
        </pre>	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="MailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Stripe id: </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <pre>         
        </pre>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<?php require_once("temp/manage-footer.php"); ?>



<script>
	 api_url="stripe_api.php";

   $("#edit_form").submit(function(){
    UpdateTableLoading();
    stripe_update();
    return false;
   })
	 
	 const stripe_detail = async(c_id)=>{
      const a = await fetch(api_url+"?action=stripe_detail",{
          method: "POST",
          credentials: 'include',
          headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
         body: JSON.stringify({
             "c_id":c_id,
          })
      });
      //const b = await a.json();
      const b = await a.text();
      const c = await SetDetailModal(c_id,b);
      //const d = await setProductList(b);
      //const e = await callbackFn(callback,b);
    }  

	function SetDetailModal(c_id,datas){
		//console.log(datas);
		$("#SetDetailModal .modal-title").text("Custom ID: "+c_id);
		$("#SetDetailModal .modal-body pre").text(datas);
		$("#SetDetailModal").modal("show");
	}



	function stripe_refund(c_id){
		if (confirm('認真的?')) {
	    	 stripe_refund_process(c_id);
		}
	}
	

	const stripe_refund_process = async(c_id)=>{
      const a = await fetch(api_url+"?action=stripe_refund",{
          method: "POST",
          credentials: 'include',
          headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
         body: JSON.stringify({
             "c_id":c_id,
          })
      });
      const b = await fetch(api_url+"?action=return_row_by_id",{
          method: "POST",
          credentials: 'include',
          headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
         body: JSON.stringify({
             "c_id":c_id,
          })
      });
      const c = await b.json();
      const d = await replaceRow(c_id,c.html);

      //const b = await a.json();
      //const c = await UpdateTable(c_id,"refunded",b.status);
      //const d = await UpdateOption(c_id,b.status);

    } 

    function UpdateTable(r_id,d_class,value){
    	$("#"+r_id).find("."+d_class).html(value);
    }

    function replaceRow(r_id,html){
      $("#"+r_id).replaceWith(html);
    }
    function UpdateOption(r_id,value){
    	if(value=="succeeded"){
    		$("#"+r_id).find(".refund_btn").remove();
    	}
    }

    function stripe_edit(c_id){
    	stripe_edit_process(c_id);
    }

    const stripe_edit_process = async(c_id)=>{
      const a = await fetch(api_url+"?action=stripe_edit",{
          method: "POST",
          credentials: 'include',
          headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
         body: JSON.stringify({
             "c_id":c_id,
          })
      });
      const b = await a.json();
      //const b = await a.text();
      const c = await SetEditModal(c_id,b);
      //const d = await setProductList(b);
      //const e = await callbackFn(callback,b);
    }


  function SetEditModal(c_id,datas){
      console.log("showedit",datas);
    	$("#SetEditModal .for_show").show();
    	$("#SetEditModal .modal-body.for_loading").hide();

		$("#SetEditModal .modal-title").text("Custom ID: "+c_id);

    $("#SetEditModal #stripe_name").val(datas["name"]);
    $("#SetEditModal #stripe_email").val(datas["email"]);

		$("#SetEditModal #stripe_amount").val(datas.amount/100);

		if(datas.status=="succeeded"){
			$("#SetEditModal #stripe_amount").attr("readonly","readonly");
      $("#SetEditModal #stripe_name").attr("readonly","readonly");
		}else{
			$("#SetEditModal #stripe_amount").removeAttr("readonly");
      $("#SetEditModal #stripe_name").removeAttr("readonly");
		}


    $("#SetEditModal #stripe_memo").val(datas.memo);
    $("#SetEditModal #stripe_admin_memo").val(datas.admin_memo);

    //$("#SetEditModal #stripe_memo").html("MMMMMMMMMMMM");
    //$("#SetEditModal #stripe_admin_memo").html("AAAAA");

		$("#SetEditModal #stripe_custom_id").val(datas.custom_id);		
		$("#SetEditModal").modal("show");
	}


	const stripe_update = async()=>{
      const a = await fetch(api_url+"?action=stripe_update",{
          method: "POST",
          credentials: 'include',
          headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
         body: JSON.stringify({
             "custom_id":$("#SetEditModal #stripe_custom_id").val(),
             "memo":$("#SetEditModal #stripe_memo").val(),
             "amount":$("#SetEditModal #stripe_amount").val(),
             "admin_memo":$("#SetEditModal #stripe_admin_memo").val(),
             "name":$("#SetEditModal #stripe_name").val(),
             "email":$("#SetEditModal #stripe_email").val(),
          })
      });
      const b = await a.json();
      //const b = await a.text();
      const c = await UpdateTableAfterEdit(b);
      //const d = await setProductList(b);
      //const e = await callbackFn(callback,b);
    }

  const send_stripe_message =async(c_id)=>{
    const a = await fetch(api_url+"?action=send_stripe_message",{
          method: "POST",
          credentials: 'include',
          headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
         body: JSON.stringify({
             "c_id":c_id,
             "type":"invoice",
          })
    });
    const b=await a.text();
    const c=await console.log(b);
  }


    function UpdateTableLoading(){
      $("#SetEditModal .modal-body.for_loading").show();
      $("#SetEditModal .for_show").hide();
    }  
    function UpdateTableAfterEdit(b){
      console.log("updated",b);
      var c_id=b.custom_id;
      replaceRow(c_id,b.html);
      $("#SetEditModal").modal("hide");
      return;
      /*
    	var c_id=b.custom_id;
    	$("#SetDetailModal .modal-body.for_loading").show();
    	$("#SetDetailModal .modal-body.for_show").hide();
    	UpdateTable(c_id,"amount",b.amount);
    	UpdateTable(c_id,"memo",b.memo);
    	UpdateTable(c_id,"admin_memo",b.admin_memo);
      UpdateTable(c_id,"buyer","<div>"+b["name"]+"</div><div>"+b.email+"</div>");
    	$("#SetEditModal").modal("hide");
      */
    }


    function stripe_mail(c_id){
      $("#MailModal").modal("show");
    }


</script>	
