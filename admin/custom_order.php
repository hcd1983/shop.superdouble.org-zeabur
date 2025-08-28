<?php
	if(isset($_GET["action"]) && $_GET["action"]=="get_product_blank"){
?>	
	<div class="row product_row" style="margin-top: 10px;">
		<div class="col-xs-4">
			<input name="custom_item_title[]" class="sm-form-control required" type="text" value="" placeholder="品名">			
		</div>	
		<div class="col-xs-2">
			<input name="custom_item_amount[]" class="sm-form-control required" type="number" value="" placeholder="數量">			
		</div>
		<div class="col-xs-4">
			<input name="custom_item_price[]" class="sm-form-control required" type="number" value="" placeholder="單價">			
		</div>
		<div class="col-xs-2">
			<a href="javascript:void(0)" class="button button-rounded button-red" onclick="console.log($(this).text());$(this).closest('.product_row').remove()">刪除列</a>
		</div>	
	</div>
	<div class="clear"></div>	
<?php
		exit();
	}
?>
<?php require_once("functions.php"); ?>
<?php notLogin("login.php"); ?>
<?php require_once("temp/manage-header.php"); ?>
<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">

		

		<div class="container clearfix">

			<div class="col-md-8 divcenter">

				<h2>付款連結產生器</h2>
				<h3>填寫以下資料產生付款連結:</h3>

				<form id="check" name="form" class="nobottommargin" action="insert_custom.php" method="post">

					
					<label>物件:</label>
					<div id="product_list" class="col_full">
					</div>
					<div class="col_half">
						<a href="javascript:void(0)" class="button nomargin  button-large button-border tright" onclick="get_product_blank()"><span>新增物件</span></a>
					</div>	
					<!--
					<div class="col_half">
						<label for="billing-form-name">姓名:<small>*</small></label>
						<input type="text" id="ReceiverName" name="order[ReceiverName]" value="" class="sm-form-control required" />
					</div>

					<div class="col_half col_last">
						<label for="billing-form-phone">電話:<small>*</small></label>
						<input type="text"  maxlength="10" id="ReceiverTel" name="order[ReceiverTel]" value=""  class="sm-form-control tel required" />
					</div>

					<div class="clear"></div>								

					<div class="col_one_fourth">
						<label for="billing-form-address">郵遞區號:</label>
						<input type="text" id="zip" name="buyer[zip]" value="" maxlength="5"  class="sm-form-control" />
					</div>

					<div class="col_three_fourth col_last">
						<label for="billing-form-address">住址:<small>*</small></label>									
						<input type="text"  id="address" name="buyer[address]" value="" class="sm-form-control required" />
					</div>
					<div class="clear"></div>

					<div class="col_full">
						<label for="billing-form-email">Email:<small>*</small></label>
						<input type="email" id="ReceiverEmail" name="order[ReceiverEmail]" value="" class="sm-form-control required email" />
					</div>

					<div class="col_half">
						<label for="billing-form-companyname">統一編號:</label>
						<input type="text"  maxlength="8" minlength="8"  id="receipt" name="buyer[receipt]" value="" class="sm-form-control" />
					</div>

					<div class="col_half col_last">
						<label for="billing-form-lname">公司名稱:</label>
						<input type="text"  id="company" name="buyer[company]" value="" class="sm-form-control" />
					</div>

					<div class="clear"></div>
					<div class="col_full">
						<label for="shipping-form-message">付費方式</label>		
						<div>							
							<input id="radio-4" class="radio-style" name="order[PayType]" type="radio" checked="checked" readonly value="01">
							<label for="radio-4" class="radio-style-2-label radio-small">信用卡</label>
							<input id="radio-5" class="radio-style" name="order[PayType]" type="radio" value="03">
							<label for="radio-5" class="radio-style-2-label radio-small">帳戶轉帳</label>
							<input id="radio-7" class="radio-style" name="order[PayType]" type="radio" value="05">
							<label for="radio-7" class="radio-style-2-label radio-small">i-bon 付款</label>
						</div>
					</div>
					
					<div class="col_full">
						<label for="shipping-form-message">備註</label>
						<textarea class="sm-form-control" id="Note1" name="order[Note1]" rows="6" cols="30"></textarea>
					</div>
					-->	
					<div class="col_full">
						<label for="shipping-form-message">管理備註(注意!前端將無法看到此連結，僅會出現在訂單後台的管理備註)</label>
						<textarea class="sm-form-control" id="Note1" name="order[memo]" rows="6" cols="30"></textarea>
					</div>
					<input type="hidden" name="action" value="CustomOrder" />
					<input type="hidden" name="order[SendStatus]" value="custom">	
					<input type="hidden" id="shippingFee" name="shippingFee" value="0" class="sm-form-control" />

					<div class="clear"></div>
					<div class="tright">
						<a href="javascript:void(0)" class="button button-xlarge  button-rounded tright" id="check" onclick="$('#check').submit()">送出<i class="icon-circle-arrow-right"></i></a>
					</div>
				</form>	
			</div>	

		</div>

	</div>

</section>


<?php require_once("temp/manage-footer.php"); ?>

<script>
	$("#check").on("submit", function(){

		no_fill=0;

		$("#check .required").each(function(i){

			the_val=$.trim($(this).val())

			if(  the_val.length == 0 ){
				
				$(this).css("border","2px solid red");

				no_fill++;

			}else{
				$(this).removeAttr("style");
			}

		})


		if(no_fill !=0 ){
			alert("請填寫必要欄位!");
			return false;
		}
		
		wrong=0;
		$("#check .required.email").each(function(i){

			var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
			if (!testEmail.test($(this).val())){
				$(this).css("border","2px solid red");
				wrong++
				
			}else{
				$(this).removeAttr("style");
			}
		    
		})

		if(wrong !=0 ){
				alert("請填寫正確的 E-MAIL 格式!");
				return false;
		}


		wrong=0;
		$("#check .required.tel").each(function(i){

			var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
			if (isNaN($(this).val())){
				$(this).css("border","2px solid red");
				wrong++
				
			}else{
				$(this).removeAttr("style");
			}
		    
		})

		if(wrong !=0 ){
				alert("電話請填寫數字，不含其他符號!");
				return false;
		}
/*
		var receipt=$("#receipt").val();
		console.log(receipt.length);

		if(receipt.length==0 || receipt.length==8){
			$("#receipt").removeAttr("style");
		}else{
			alert("請確認統編是否正確!");
			$("#receipt").css("border","2px solid red");
			return false;				
		}
*/
		//return false;

	})


const get_product_blank =  async(year,month) => {
  const a = await fetch("?action=get_product_blank");
  const b = await a.text();
  const c = await $("#product_list").append(b);
                  if($(".product_row").length==1){$(".product_row a").hide()}else{$(".product_row a").show()};
  //const d = await cal.setData(b);
  //const c = await cal.setData( {'12-22-2017': "交易成功: 3<br>金額: $7,840"} );
 
  //const b = await fetch(“xxx.xxx.xx.x”)
}

get_product_blank();
</script>	