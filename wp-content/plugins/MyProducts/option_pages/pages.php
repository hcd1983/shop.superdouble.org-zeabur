<?php	

    $MyOpts=get_option('ProductsPages');
    $MyCheck=get_option('MyCheckPage');
    $MyCart=get_option('MyCartPage');
    $MyFundingCartPage = get_option('MyFundingCartPage');
    $MyThanks=get_option('MyThanksPage');
    $MyInsertPort=get_option('MyInsertPort');
    $MyOrderApi=get_option('MyOrderApi');
    $MyFbProducts=get_option('MyFbProducts');
    $MyBrand=get_option('MyBrand');

    $DifferentReceiver = get_option('DifferentReceiver') === false? 0 :get_option('DifferentReceiver');

    $product_selector_single = get_option('product_selector_single') === false? 0 :get_option('product_selector_single');
    $product_selector_list = get_option('product_selector_list') === false? 0 :get_option('product_selector_list');

    $MyGoogleProductCategory=get_option('MyGoogleProductCategory');
    $MyOrderSearchPage=get_option("MyOrderSearchPage");
    $UseProductCate=get_option("UseProductCate")===false?1:get_option("UseProductCate");
    $CollectMailWhenLack = get_option("CollectMailWhenLack")===false?0:get_option("CollectMailWhenLack");
    $UseStripe = get_option("UseStripe")===false?0:get_option("UseStripe");
    $MyCartLang = get_option("MyCartLang")===false?"zh":get_option("MyCartLang ");
    $MyDebugger = get_option("MyDebugger")===false?0:get_option("MyDebugger");

    $MyPaytype = get_option("MyPaytype")===false?["01","03","05"]:get_option("MyPaytype");

    $FundingMode = get_option("FundingMode")===false?0:get_option("FundingMode");



   
    $UsedPage=array();
    if($MyOpts !=""){
    	$UsedPage[]=$MyOpts;
    }

    if($MyCheck !=""){
    	$UsedPage[]=$MyCheck;
    }

    if($MyCart !=""){
    	$UsedPage[]=$MyCart;
    }

    if($MyThanks !=""){
    	$UsedPage[]=$MyThanks;
    }

    if($MyFbProducts !=""){
      $UsedPage[]=$MyFbProducts;
    }

    if($MyOrderSearchPage !=""){
      $UsedPage[]=$MyOrderSearchPage;
    }

    if($MyFundingCartPage !=""){
      $UsedPage[]=$MyFundingCartPage;
    }


    

    


?>
<div class="wrap">
<h1>產品頁面設定</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'MyProducts' ); ?>
    <?php //do_settings_sections( 'MyProducts' ); ?>
    <?php //do_settings_sections( 'ProductsPages' ); ?>
    <?php //do_settings_sections( 'CheckPage' ); ?>

    <table class="form-table">       
        <tr valign="top">
	        <th scope="row">產品 Json 頁面</th>
	        <td>
	        	<select name="ProductsPages">
	           <?php
	         
	           	$not_in=$UsedPage;
	           	if (($key = array_search($MyOpts, $not_in)) !== false) {
				    unset($not_in[$key]);
				}
                postpicker::generate_post_select("page", $MyOpts ,$not_in);
                ?>
                </select>		
	        </td>
        </tr>
        <?php
        if($MyOpts==""){
        ?>
        	<th scope="row"></th>
        	<td>
        		<a href="javascript:void(0)" class="button" onclick="SetPage();">產生一個</a>
        	</td>	
        <?php		
        }
        ?>

        <tr valign="top">
	        <th scope="row">購物車</th>
	        <td>

	        	<select name="MyCartPage">
	           <?php
	           	$not_in=$UsedPage;
	           	
	           	if (($key = array_search($MyCart, $not_in)) !== false) {
				    unset($not_in[$key]);
				}
                postpicker::generate_post_select("page", $MyCart ,$not_in);
                ?>
                </select>	
	        </td>
        </tr>
        <?php
        if($MyCart==""){
        ?>
        <tr valign="top">
        	<th scope="row"></th>
        	<td>
        		<a href="javascript:void(0)" class="button" onclick="SetCartPage();">產生一個</a><br>
        		* 如果自訂，須包表單代碼 [funding_cart_form]
        	</td>
        </tr>	
        <?php		
        }
        ?>

        <tr valign="top">
            <th scope="row">募資模式購物車</th>
            <td>

                <select name="MyFundingCartPage">
               <?php
                $not_in=$UsedPage;
                
                if (($key = array_search($MyFundingCartPage, $not_in)) !== false) {
                    unset($not_in[$key]);
                }
                postpicker::generate_post_select("page", $MyFundingCartPage ,$not_in);
                ?>
                </select>   
            </td>
        </tr>

        <?php
        if($MyFundingCartPage==""){
        ?>
        <tr valign="top">
            <th scope="row"></th>
            <td>
                <a href="javascript:void(0)" class="button" onclick="SetFundingCartPagePage();">產生一個</a><br>
                * 如果自訂，須包表單代碼 [mycartpage]
            </td>
        </tr>   
        <?php       
        }
        ?>



        <tr valign="top">
            <th scope="row">募資模式(方案點擊後進入結帳頁面)</th>
            <td>

                <select name="FundingMode">                
                    <option value="1" <?php if ( $FundingMode == 1 ) echo 'selected="selected"'; ?>>使用</option>
                    <option value="0" <?php if ( $FundingMode == 0 ) echo 'selected="selected"'; ?>>不使用</option>
                </select>   
            </td>
        </tr>
       
        <tr valign="top">
	        <th scope="row">結帳頁面</th>
	        <td>

	        	<select name="MyCheckPage">
	           <?php
	           	$not_in=$UsedPage;
	           	
	           	if (($key = array_search($MyCheck, $not_in)) !== false) {
				    unset($not_in[$key]);
				}
                postpicker::generate_post_select("page", $MyCheck ,$not_in);
                ?>
                </select>	
	        </td>
        </tr>

        <?php
        if($MyCheck==""){
        ?>
        <tr valign="top">
        	<th scope="row"></th>
        	<td>
        		<a href="javascript:void(0)" class="button" onclick="SetCheckPage();">產生一個</a><br>
        		* 如果自訂，須包表單代碼 [mycheckpage]、出貨表單 [mycheckform]、Coupon 表單[mycouponform]
        	</td>	
        </tr>	
        <?php		
        }
        ?>

        <tr valign="top">
	        <th scope="row">感謝頁面</th>
	        <td>

	        	<select name="MyThanksPage">
	           <?php
	           	$not_in=$UsedPage;
	           	
	           	if (($key = array_search($MyThanks, $not_in)) !== false) {
				    unset($not_in[$key]);
				}
                postpicker::generate_post_select("page", $MyThanks ,$not_in);
                ?>
                </select><br>
                * 如果自訂，須包表單代碼 [paynow][buyer][orderlist]<br>
        		或是使用[full_Orderinfo col='欄位名']方式帶入	
	        </td>
        </tr>

        <?php
        if($MyThanks==""){
        ?>
        <tr valign="top">
        	<th scope="row"></th>
        	<td>
        		<a href="javascript:void(0)" class="button" onclick="SetThanksPage();">產生一個</a><br>
        		
        	</td>	
        </tr>	
        <?php		
        }
        ?>

         <tr valign="top">
	        <th scope="row">訂單搜尋結果頁面</th>
	        <td>

	        	<select name="MyOrderSearchPage">
	           <?php
	           	$not_in=$UsedPage;
	           	
	           	if (($key = array_search($MyOrderSearchPage, $not_in)) !== false) {
				    unset($not_in[$key]);
				}
                postpicker::generate_post_select("page", $MyOrderSearchPage ,$not_in);
                ?>
                </select><br>	
                * 如果自訂，須包表單代碼 [paynow][buyer][orderlist]<br>
        		或是使用[full_Orderinfo col='欄位名']方式帶入<br>
        		[searchform]放在搜索表單頁面
	        </td>
        </tr>

        <?php
        if($MyOrderSearchPage==""){
        ?>
        <tr valign="top">
        	<th scope="row"></th>
        	<td>
        		<a href="javascript:void(0)" class="button" onclick="SetMyOrderSearchPage();">產生一個</a><br>
        		
        	</td>	
        </tr>	
        <?php		
        }
        ?>

        <tr valign="top">
	        <th scope="row">FB 產品目錄</th>
	        <td>
	        	<select name="MyFbProducts">
	           <?php
	           	$not_in=$UsedPage;
	           	
	           	if (($key = array_search($MyFbProducts, $not_in)) !== false) {
				    unset($not_in[$key]);
				}
                postpicker::generate_post_select("page", $MyFbProducts ,$not_in);
                ?>
                </select>	
	        </td>
        </tr>
        <?php
        if($MyFbProducts !==""){
        ?>
        <tr valign="top">
	        <th scope="row">使用 FB 產品目錄</th>
	        <td>
	        <select name="UseProductCate">
	           <option value="1" <?php if ( $UseProductCate == 1 ) echo 'selected="selected"'; ?>>使用</option>
    			<option value="0" <?php if ( $UseProductCate == 0 ) echo 'selected="selected"'; ?>>不使用</option>
            </select>	
	        </td>
        </tr>
        <?php	
        }

        ?>

        <?php
        if($MyFbProducts==""){
        ?>
        <tr valign="top">
        	<th scope="row"></th>
        	<td>
        		<a href="javascript:void(0)" class="button" onclick="SetFbProductsPage();">產生一個</a><br>
        	</td>	
        </tr>	
        <?php		
        }
        ?>

        <?php
        if($MyFbProducts!=""){
        ?>	
        <tr valign="top">
	        <th scope="row">Brand</th>
	        <td>
	        	<input type="text" name="MyBrand" value="<?php echo $MyBrand;?>">
	        </td>
        </tr>

        <tr valign="top">
	        <th scope="row">google_product_category [Google 產品類別]</th>
	        <td>
	        	<input type="text" name="MyGoogleProductCategory" value="<?php echo $MyGoogleProductCategory;?>">
	        	<br>*參考這邊<a href="https://support.google.com/merchants/answer/6324436?hl=zh-Hant" target="_blank">https://support.google.com/merchants/answer/6324436?hl=zh-Hant</a>
	        </td>
        </tr>

        
        <?php	
        }
        	
        ?>

        <tr valign="top">
	        <th scope="row">插入訂單接口網址</th>
	        <td>
	        	<input type="text" name="MyInsertPort" value="<?php echo $MyInsertPort;?>">
	        </td>
        </tr>

        <tr valign="top">
	        <th scope="row">查詢訂單API</th>
	        <td>
	        	<input type="text" name="MyOrderApi" value="<?php echo $MyOrderApi;?>">
	        </td>
        </tr>
        <tr valign="top">
        	<th scope="row"></th>
        	<td>
        		輸入: GET 或 POST : super (單號)<br>
        		輸出: Array "buyer","paynow","receiver","orderlist"
        	</td>	
        </tr>

        <tr valign="top">
            <th scope="row">缺貨時蒐集Email</th>
            <td>
            <select name="CollectMailWhenLack">
               <option value="1" <?php if ( $CollectMailWhenLack == 1 ) echo 'selected="selected"'; ?>>是</option>
                <option value="0" <?php if ( $CollectMailWhenLack == 0 ) echo 'selected="selected"'; ?>>否</option>
            </select>   
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">Paynow 付款方式</th>
            <td>
                <input type="checkbox" name="MyPaytype[]" value="01" <?php if(in_array("01", $MyPaytype)) echo "checked";?>><label>信用卡</label><br>
                <input type="checkbox" name="MyPaytype[]" value="11" <?php if(in_array("11", $MyPaytype)) echo "checked";?>><label>信用卡分期</label><br>
                <input type="checkbox" name="MyPaytype[]" value="03" <?php if(in_array("03", $MyPaytype)) echo "checked";?>><label>帳戶轉帳</label><br>
                <input type="checkbox" name="MyPaytype[]" value="05" <?php if(in_array("05", $MyPaytype)) echo "checked";?>><label> i-bon 付款</label><br>
                <input type="checkbox" name="MyPaytype[]" value="10" <?php if(in_array("10", $MyPaytype)) echo "checked";?>><label>超商條碼</label><br>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">允許不同收貨人</th>
            <td>
            <select name="DifferentReceiver">
               <option value="0" <?php if ( $DifferentReceiver == "0" ) echo 'selected="selected"'; ?>>不允許</option>
                <option value="1" <?php if ( $DifferentReceiver == "1" ) echo 'selected="selected"'; ?>>允許</option>
            </select>   
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">購物車語言</th>
            <td>
            <select name="MyCartLang">
               <option value="zh" <?php if ( $MyCartLang == "zh" ) echo 'selected="selected"'; ?>>中文</option>
                <option value="en" <?php if ( $MyCartLang == "en" ) echo 'selected="selected"'; ?>>英文</option>
            </select>   
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">使用 Stripe (使用美金收費)</th>
            <td>
            <select name="UseStripe">
               <option value="1" <?php if ( $UseStripe == 1 ) echo 'selected="selected"'; ?>>是</option>
                <option value="0" <?php if ( $UseStripe == 0 ) echo 'selected="selected"'; ?>>否</option>
            </select>   
            </td>
        </tr>


        <tr valign="top">
            <th scope="row">多選項時使用下拉選單</th>
            <td>
            <label>單頁</label> <br>  
            <select name="product_selector_single">
               <option value="1" <?php if ( $product_selector_single == 1 ) echo 'selected="selected"'; ?>>使用</option>
                <option value="0" <?php if ( $product_selector_single == 0 ) echo 'selected="selected"'; ?>>不使用</option>
            </select> <br>  
             <label>列表</label>   <br>  
            <select name="product_selector_list">
               <option value="1" <?php if ( $product_selector_list == 1 ) echo 'selected="selected"'; ?>>使用</option>
                <option value="0" <?php if ( $product_selector_list == 0 ) echo 'selected="selected"'; ?>>不使用</option>
            </select>   <br>  

            </td>
        </tr>


        <tr valign="top">
            <th scope="row">Debug 模式，登入才看的到購物車</th>
            <td>
            <select name="MyDebugger">
               <option value="1" <?php if ( $MyDebugger == 1 ) echo 'selected="selected"'; ?>>是</option>
                <option value="0" <?php if ( $MyDebugger == 0 ) echo 'selected="selected"'; ?>>否</option>
            </select>   
            </td>
        </tr>

         

    </table>
    
    <?php submit_button(); ?>

</form>

	
</div>
<script>
	var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
	function SetPage(){
		jQuery.ajax({
	        url: ajaxurl,
	        data: {
	            action: 'SetPage'
	        },
	        type: 'GET'
	    }).done(function( data ) {
		    
		      location.reload();
		    
		  });

		return false;
	}

	function SetCheckPage(){
		jQuery.ajax({
	        url: ajaxurl,
	        data: {
	            action: 'SetCheckPage'
	        },
	        type: 'GET'
	    }).done(function( data ) {
		    
		      location.reload();
		    
		  });

		return false;
	}

	function SetCartPage(){
		jQuery.ajax({
	        url: ajaxurl,
	        data: {
	            action: 'SetCartPage'
	        },
	        type: 'GET'
	    }).done(function( data ) {
		    
		      location.reload();
		    
		  });

		return false;
	}

	function SetThanksPage(){
		jQuery.ajax({
	        url: ajaxurl,
	        data: {
	            action: 'SetThanksPage'
	        },
	        type: 'GET'
	    }).done(function( data ) {
		    
		      location.reload();
		    
		  });

		return false;
	}


	function SetFbProductsPage(){
		jQuery.ajax({
	        url: ajaxurl,
	        data: {
	            action: 'SetFbProductsPage'
	        },
	        type: 'GET'
	    }).done(function( data ) {
		    
		      location.reload();
		    
		  });

		return false;
	}

	function SetMyOrderSearchPage(){
		jQuery.ajax({
	        url: ajaxurl,
	        data: {
	            action: 'SetMyOrderSearchPage'
	        },
	        type: 'GET'
	    }).done(function( data ) {
		    
		      location.reload();
		    
		  });

		return false;
	}

    

    function SetFundingCartPagePage(){
        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'SetFundingCartPagePage'
            },
            type: 'GET'
        }).done(function( data ) {
            
              location.reload();
            
          });

        return false;
    }
</script>	

