<?php
 // $listInfo = ["FirstName","LastName","phone","email","address","city","state","zip","countryname"];
  $listInfo = ["FirstName","LastName","phone","email","address","shipping_method","Note"];
  
  $list_html="";
  foreach ($listInfo as $key => $value) {

    

    if(isset($_POST[$value]) && $_POST[$value] != ""){

      switch ($value) {
        case 'countryname':
           $WordTransIndex = "country";
           $htmlValue = $_POST[$value];
          break;

        case 'phone':
           $WordTransIndex = "phoneNumber";
           $htmlValue = $_POST[$value];
          break;
        case 'address':
          $WordTransIndex = $value;
          $htmlValue =  $_POST["address"]."<br>";
          //$htmlValue .= $_POST["city"]." ".$_POST["state"].", ".$_POST["zip"].", ".$_POST["countryname"];
          $htmlValue .= join(" ",[$_POST["city"],$_POST["state"].",",$_POST["zip"].",",$_POST["countryname"]]);
          break;
        case 'shipping_method':
           $WordTransIndex = "ShippingOptions";
           $htmlValue = $_POST[$value];
          break;  
        case 'Note':
          $WordTransIndex = "note"; 
          $htmlValue = $_POST[$value];     
          break;
        default:
          $WordTransIndex = $value;
          $htmlValue = $_POST[$value];
          break;
      }

      $list_html.="<li class=\"shipping-info-{$WordTransIndex}\">"."<span>".MyCartWords($WordTransIndex)."</span> <span>".$htmlValue."</span></li>";
    }
  }

?>
<script type="text/javascript">
  var DetectItem=MyCart.GetCartItems();
  if( typeof DetectItem == "undefined" || DetectItem.length == 0 ){
     // location.replace("<?php echo get_home_url();?>");
  }
</script>
<style type="text/css">
  #CreditCardRow{
    transition:all 0.5s ease;
    position: relative;
  }
 
  #CreditCardRow .blocker{
    transition:all 0.5s ease;
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    text-align: center;
    left: 0;
    background-color: rgba(255,255,255,0.9);
    line-height: 1.5;
    font-size: 24px;
    visibility: hidden;
    opacity: 0;
    display: flex;
    align-items: center;
  }

  #CreditCardRow.loading .blocker {
     visibility: visible; 
     opacity: 1;
  }

  #CreditCardRow.loading .blocker > div{
    margin:auto;
  }

</style>
<form  id="StripePayform" action="">
  <div class="row">
    <h3><?php echo MyCartWords("ShippingInfo");?></h3>
    <ul id="shipping-info">
      <?php echo $list_html;?>  
    </ul>  
  </div>
  <div>
    <h3><?php echo MyCartWords("items");?></h3>
    <table>
      <tr>
        <th><?php echo MyCartWords("ProductName");?></th>
        <th><?php echo MyCartWords("amount");?></th>
        <th><?php echo MyCartWords("cost");?></th>
      </tr>
      <?php
        $items=str_replace('\"', '"', $_POST["OrderInfo"]);
        $items=json_decode($items,true);
        $total=0;
        foreach ($items as $key => $item) {
            echo "<tr>";
            echo "<td>".$item["title"]."</td>";
            echo "<td>".$item["amount"]."</td>";
            echo "<td>".PriceFormat("",$item["amount"]*$item["price"])."</td>";
            echo "</tr>";
            $total += $item["amount"]*$item["price"];
        }

        $havetopay=$total+$_POST["shippingFee"]-$_POST["discount"];
      ?>
    </table>
    <ul id="pay-info">
      <li><span><?php echo MyCartWords("ProductTotal");?></span> <?php echo PriceFormat("",$total); ?></li>
      <?php 
      if($_POST["shippingFee"] > 0):
      ?>  
      <li><span><?php echo MyCartWords("shippingFee");?></span> <?php echo PriceFormat("",$_POST["shippingFee"]); ?></li>
      <?php
      endif;
      ?>
       <?php 
      if($_POST["discount"] > 0):
      ?> 
      <li><span><?php echo MyCartWords("discount");?></span> <?php echo PriceFormat("",$_POST["discount"]); ?></li>
      <?php
      endif;
      ?>
      <li><span><?php echo MyCartWords("CheckTotal");?></span> <?php echo PriceFormat("",$havetopay); ?></li>
    </ul>  
  </div>  
  <div class="row" id="CreditCardRow">
    <h3><?php echo MyCartWords("CreditCard");?></h3>
    <div class="col_full">
      <label for="example2-card-number" data-tid="elements_examples.form.card_number_label">Card number</label>
      <div id="example2-card-number" class="input empty"></div>
    </div>
    <div class="clear"></div>

    <div class="col_half">

      <label for="example2-card-expiry" data-tid="elements_examples.form.card_expiry_label">Expiration</label>
      <div id="example2-card-expiry" class="input empty"></div>

    </div>

    <div class="col_half col_last">
      <label for="example2-card-cvc" data-tid="elements_examples.form.card_cvc_label">CVC</label>
      <div id="example2-card-cvc" class="input empty"></div>
    </div>

    <div class="clear"></div>
    <div style="text-align: center;">
      <button type="submit" data-tid="elements_examples.form.pay_button" class="button"><?php echo MyCartWords("pay");?> <?php echo PriceFormat("$",$havetopay); ?></button>
    </div>
    <div class="blocker"><div>Connecting...<br><span><i class="fas fa-spinner fa-spin"></i></span></div></div>  
  </div>

  <div class="success" style="text-align: center;display: none;">
    <div class="icon">
      <svg width="84px" height="84px" viewBox="0 0 84 84" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <circle class="border" cx="42" cy="42" r="40" stroke-linecap="round" stroke-width="4" stroke="#000" fill="none"></circle>
        <path class="checkmark" stroke-linecap="round" stroke-linejoin="round" d="M23.375 42.5488281 36.8840688 56.0578969 64.891932 28.0500338" stroke-width="4" stroke="#000" fill="none"></path>
      </svg>
    </div>
    <h3 class="title" data-tid="elements_examples.success.title">Payment successful</h3>
    <p class="message"></p>
    <a href="<?php echo get_home_url();?>" class="button">OK</a>          
  </div>
  
</form>

<?php
$infos=$_POST;
$infos["total"]=$havetopay;
$infos=json_encode($infos);
?>
<script>
  var infos=<?php echo $infos;?>;
  var shippingfee=Number(infos.shippingFee);
  var discount=Number(infos.discount);
</script>  
