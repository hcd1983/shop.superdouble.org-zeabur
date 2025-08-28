<?php require_once("functions.php"); ?>
<?php notLogin("login.php"); ?>
<?php require_once("temp/manage-header.php"); ?>
<!-- Content
============================================= -->
<section id="content">

	<div class="content-wrap">


		<div class="container clearfix">

			<div class="col-md-8 divcenter">

				<h2>STRIPE 付款連結產生器</h2>
				<h3>填寫以下資料產生付款連結:</h3>

				
				<form id="meta" onsubmit="return false;">	
					<div class="col_full">
						<label for="shipping-form-message">Buyer</label>
						<input class="sm-form-control" type="text"  name="name" >
					</div>
					<div class="col_full">
						<label for="shipping-form-message">Receitpt Email</label>
						<input class="sm-form-control" type="email"  name="email" >
					</div>
				</form>	
				<form id="check" name="form" class="nobottommargin" action="insert_stripe.php" method="post">	
					<div class="col_full">
						<label for="shipping-form-message">付款金額 (USD, 最小 0.5 USD)*</label>
						<input class="sm-form-control required" type="number" min="0.5" step="0.01" name="amount" value="0.5" required="required">
					</div>
									
					<div class="col_full">
						<label for="shipping-form-message">付款訊息，給付款人看的備註</label>
						<textarea class="sm-form-control" name="memo" rows="6" cols="30"></textarea>
					</div>	
					
					<div class="col_full">
						<label for="shipping-form-message">管理備註(注意!前端將無法看到此連結，僅會出現在訂單後台的管理備註)</label>
						<textarea class="sm-form-control" name="admin_memo" rows="6" cols="30"></textarea>
					</div>
					
					
					
					<div class="clear"></div>
					<div class="tright">
						<button type="submit" class="button button-xlarge  button-rounded tright">送出<i class="icon-circle-arrow-right"></i></button>
						<!--<a href="javascript:void(0)" class="button button-xlarge  button-rounded tright" id="check" onclick="$('#check').submit()">送出<i class="icon-circle-arrow-right"></i></a>-->
					</div>
				</form>	
				<div id="added_info">
				</div>	
			</div>	

		</div>

	</div>

</section>


<?php require_once("temp/manage-footer.php"); ?>

<script>

	$.fn.serializeObjectWithParent = function(parent="result")
	{
	   var o = {};
	   var a = this.serializeArray();
	   $.each(a, function() {
	       if (o[this.name]) {
	           if (!o[this.name].push) {
	               o[this.name] = [o[this.name]];
	           }
	           o[this.name].push(this.value || '');
	       } else {
	           o[this.name] = this.value || '';
	       }
	   });

	   var q = {};
	   q[parent]=o;
	   return q;
	};

	function insert_strip(){
		var stripe_data=$("#check").serializeObjectWithParent("stripe");
		var meta_data=$("#meta").serializeObjectWithParent("meta");
		var datas=Object.assign({},stripe_data,meta_data);
		//console.log(datas);
		insert_stripe_proccess(datas);

	}

	function return_result(b){
		var message=b.url;
		var input="<input readonly type='text' class='hidden' id='input_"+b.custom_id+"' value='"+b.url+"'>"
		var html='<div class="style-msg2 successmsg">\
					<div class="msgtitle">New Payment link Created: '+input+' '+"<span style='color:#FFF;text-shadow:none;margin-left:10px;'>"+b.time+'</span>'+'</div>\
					<div class="sb-msg">'+b.url+" <button class='button button-mini button-black' style='margin-left:10px;' onclick='copyToCliPboardbyId(\"input_"+b.custom_id+"\")'>複製</button>"+'</div>\
				  </div>';

		$("#added_info").append(html);				
	}
	
	api_url="insert_stripe.php";
	const insert_stripe_proccess = async(datas)=>{
      const a = await fetch(api_url+"?action=CustomStripeCharge",{
          method: "POST",
          credentials: 'include',
          headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
         body: JSON.stringify(datas)
      });
      const b = await a.json();
      //const b = await a.text();
      const c = await return_result(b);
      //const d = await setProductList(b);
      //const e = await callbackFn(callback,b);
    }

	$("#check").on("submit", function(){
		insert_strip();
		return false;		 
	})

	$("#check").submit(function(){
		//e.preventDefault();
		//return false;	
	})

	function copyToCliPboardbyId(id){
		var $temp = $("<input>");
    	$("body").append($temp);
    	var value=$("#"+id).val();
    	$temp.val(value).select();		
    	document.execCommand("copy");
    	$temp.remove();
	};



</script>	