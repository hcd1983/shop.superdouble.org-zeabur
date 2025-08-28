<?php

    $StripePayPage=get_option('StripePayPage');

    $StripeToken = get_option("StripeToken")===false?"":get_option("StripeToken");
    $StripeSecretToken = get_option("StripeSecretToken")===false?"":get_option("StripeSecretToken");

    $StripeTokenTest = get_option("StripeTokenTest")===false?"":get_option("StripeTokenTest");
    $StripeSecretTokenTest = get_option("StripeSecretTokenTest")===false?"":get_option("StripeSecretTokenTest");
    $StripeTest =  get_option("StripeTest")===false?0:get_option("StripeTest");
    $StripeInsertUrl = get_option("StripeInsertUrl")===false?"":get_option("StripeInsertUrl");

    $UsedPage=array();
    if($StripePayPage !="" && $StripePayPage != false){
        $UsedPage[]=$StripePayPage;
    }
?>
<div class="wrap">
<h1>Stripe 設定</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'MyStripe' ); ?>
   

    <table class="form-table"> 

        <tr valign="top">
            <th scope="row">Stripe 結帳頁面</th>
            <td>
                <select name="StripePayPage">
               <?php
             
                $not_in=$UsedPage;
                if (($key = array_search($StripePayPage, $not_in)) !== false) {
                    unset($not_in[$key]);
                }
                postpicker::generate_post_select("page", $StripePayPage ,$not_in);
                ?>
                </select>       
            </td>
        </tr>
        <?php
        if($StripePayPage==""){
        ?>
            <th scope="row"></th>
            <td>
                <a href="javascript:void(0)" class="button" onclick="SetStripePayPage();">產生一個</a>
            </td>   
        <?php       
        }
        ?>

         <tr valign="top">
            <th scope="row">Publishable key</th>
            <td>
            <input name="StripeToken" value="<?php echo $StripeToken;?>">
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">Secret key</th>
            <td>
            <input name="StripeSecretToken" value="<?php echo $StripeSecretToken;?>">
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">Publishable key for test </th>
            <td>
            <input name="StripeTokenTest" value="<?php echo $StripeTokenTest;?>">
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">Secret key for test</th>
            <td>
            <input name="StripeSecretTokenTest" value="<?php echo $StripeSecretTokenTest;?>">
            </td>
        </tr> 

        <tr valign="top">
            <th scope="row">測試模式</th>
            <td>
            <select name="StripeTest">
               <option value="1" <?php if ( $StripeTest == 1 ) echo 'selected="selected"'; ?>>是</option>
                <option value="0" <?php if ( $StripeTest == 0 ) echo 'selected="selected"'; ?>>否</option>
            </select>   
            </td>
        </tr> 

        <tr valign="top">
            <th scope="row">Stripe Api 接口</th>
            <td>
            <input value="<?php echo $StripeInsertUrl;?>" name="StripeInsertUrl" > 
            </td>
        </tr> 

               

    </table>
    
    <?php submit_button(); ?>

</form>

	
</div>

<script>
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    function SetStripePayPage(){
        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'SetStripePayPage'
            },
            type: 'GET'
        }).done(function( data ) {
            
              location.reload();
            
          });

        return false;
    }

   
</script>   
