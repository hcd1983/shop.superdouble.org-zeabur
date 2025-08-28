<?php

$message_for_test="Dear Client,

We are contacting you in regard to a new invoice # 1 that has been created on your account. You may find the invoice attached. Please pay the balance of $1,000 by June 21.

You can pay by mailing a check to:

Acme Corp,

123 Main St.

Austin, TX 78701

We look forward to conducting future business with you.

Kind Regards,

Jared

Acme Corp.";
$customer_information_for_test=array(
  "title"=>"Payment Info",
  "infos"=>[
      [
        ["title"=>"bbb","content"=>"content"],
        ["title"=>"CCC","content"=>"GGG"],
        ["title"=>"CCC","content"=>"GGG"],
      ],
      [
        ["title"=>"HAHAHA","content"=>"Amazon payments — $130.50"],
      ],
          
  ],
);

$default=array(
  "title"=>"This is title",
  "email"=>"service@spinbox.cc",
  "message_top"=>$message_for_test,
  "image_url_1"=>"https://shop.spinbox.cc/images/left_logo.png",
  "image_url_2"=>"https://tw.spinbox.cc/wp-content/uploads/2018/04/spinbox-1026-600x600.jpg",
  "customer_information"=>$customer_information_for_test,
  "shoppinglog"=>array(),
  "payment_link"=>array(
    "title"=>"Payment Link →",
    "url"=>"#"
  ),
);

$theme_setting=array_merge($default,$this->theme_setting);
extract($theme_setting);

$message_top=nl2br($message_top);
//$message_top=nl2br($message_top);
//$message_top=str_replace("<br>", str_repeat("<br>", 2), $message_top);
//string to hyperlink

//$message_top=preg_replace('/^.+\n/', '', $message_top);

$message_top=preg_replace(
    "~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~",
    "<a href=\"\\0\" style='font-size: 16px; text-decoration: underline; color: #a5a8a8;' target='_blank'>\\0</a>", 
    $message_top);

?>
<head>
  <title><?php echo $title;?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width">
  
  
<style>body {
margin: 0;
}
h1 a:hover {
font-size: 30px; color: #333;
}
h1 a:active {
font-size: 30px; color: #333;
}
h1 a:visited {
font-size: 30px; color: #333;
}
a:hover {
text-decoration: none;
}
a:active {
text-decoration: none;
}
a:visited {
text-decoration: none;
}
.button__text:hover {
color: #fff; text-decoration: none;
}
.button__text:active {
color: #fff; text-decoration: none;
}
.button__text:visited {
color: #fff; text-decoration: none;
}
.button__text:hover {
color: white;
}
.button__text:active {
color: white;
}
.button__text:visited {
color: white;
}
a:hover {
color: #a5a8a8; text-decoration: underline;
}
a:active {
color: #a5a8a8; text-decoration: underline;
}
a:visited {
color: #a5a8a8; text-decoration: underline;
}
@media (max-width: 600px) {
  .container {
    width: 94% !important;
  }
  .main-action-cell {
    float: none !important; margin-right: 0 !important;
  }
  .secondary-action-cell {
    text-align: center; width: 100%;
  }
  .header {
    margin-top: 20px !important; margin-bottom: 2px !important;
  }
  .shop-name__cell {
    display: block;
  }
  .order-number__cell {
    display: block; text-align: left !important; margin-top: 20px;
  }
  .button {
    width: 100%;
  }
  .or {
    margin-right: 0 !important;
  }
  .apple-wallet-button {
    text-align: center;
  }
  .customer-info__item {
    display: block; width: 100% !important;
  }
  .spacer {
    display: none;
  }
  .subtotal-spacer {
    display: none;
  }
}
</style>
</head>

<body style="margin: 0;">
<table class="body" style="height: 100% !important; width: 100% !important; border-spacing: 0; border-collapse: collapse;">
  <tbody><tr>
    <td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">
      <table class="header row" style="width: 100%; border-spacing: 0; border-collapse: collapse; margin: 40px 0 20px;">
        <tbody><tr>
          <td class="header__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">
            <center>
              <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                <tbody><tr>
                  <td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">

                    <table class="row" style="width: 100%; border-spacing: 0; border-collapse: collapse;">
                      <tbody><tr>
                        <td class="shop-name__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding-bottom: 20px;" align="center">
                          <img src="<?php echo $image_url_1;?>"  width="269">
                        </td>
                      </tr>
                    </tbody></table>

                    <table class="row" style="width: 100%; border-spacing: 0; border-collapse: collapse;">
                      <tbody><tr>
                        <td class="masthead__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding-bottom: 20px;">
                          <img src="<?php echo $image_url_2;?>" alt="<?php echo $title;?>" style="max-width: 100%;">
                        </td>
                      </tr>
                    </tbody></table>

                  </td>
                </tr>
              </tbody></table>

            </center>
          </td>
        </tr>
      </tbody></table>

      <table class="row content" style="width: 100%; border-spacing: 0; border-collapse: collapse;">
        <tbody><tr>
          <td class="content__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding-bottom: 40px;">
            <center>
              <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                <tbody><tr>
                  <td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">

                    <?php
                      if($title!=""):
                    ?>    
                    <h2 style="font-weight: normal; font-size: 32px; line-height: 1.13; color: #050505; margin: 0 0 10px;">
                      <?php echo $title; ?></h2>
                    <?php
                      endif;
                    ?>
                    <?php if(!empty($message_top)):  ?>
                    <p style="color: #050505; line-height: 1.33; font-size: 18px; font-weight: normal; padding-top: 25px; margin: 0;"><?php echo $message_top;?></p>
                    <?php endif; ?>

                    <table class="row actions" style="width: 100%; border-spacing: 0; border-collapse: collapse; margin-top: 20px;">
                      <tbody><tr>
                        <td class="actions__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;" valign="bottom">
                        <?php
                        if(!empty($payment_link)):
                        ?>  
                          <table class="button main-action-cell" style="border-spacing: 0; border-collapse: collapse; float: left; margin-right: 15px; width: auto; margin-top: 0; margin-bottom: 15px;">
                            <tbody><tr>
                              <td class="button__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; border-radius: 0; border: 1px solid #050505;" align="center" bgcolor="#050505"><a href="<?php echo $payment_link["url"];?>" class="button__text" style="font-size: 18px; text-decoration: none; display: block; color: white; line-height: 24px; padding: 5px 30px;" target="_blank"><?php echo $payment_link["title"];?></a></td>
                            </tr>
                          </tbody></table>
                        <?php
                        endif;
                        ;?>
                        
                        </td>
                      </tr>
                    </tbody></table>

                    

                  </td>
                </tr>
              </tbody></table>
            </center>
          </td>
        </tr>
      </tbody></table>
<?php
//shoppinglog;========================================================================
  if(is_array($shoppinglog) && count($shoppinglog)>0):
?>  
      <table class="row section" style="width: 100%; border-spacing: 0; border-collapse: collapse; border-top-width: 1px; border-top-color: #e5e5e5; border-top-style: solid;">
        <tbody><tr>
          <td class="section__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding: 40px 0;">
            <center>
              <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                <tbody><tr>
                  <td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">
                    <h3 style="font-weight: normal; font-size: 22px; line-height: 1.09; color: #050505; margin: 0 0 25px;">Order US650050 summary</h3>
                  </td>
                </tr>
              </tbody></table>
              <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                <tbody><tr>
                  <td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">

                    
<table class="row" style="width: 100%; border-spacing: 0; border-collapse: collapse;">
  

    

    
    

    

  <tbody><tr class="order-list__item" style="width: 100%;">
    <td class="order-list__item__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding-bottom: 15px;">
      <table style="border-spacing: 0; border-collapse: collapse;">
        <tbody><tr><td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">
          
          <img src="https://cdn.shopify.com/s/files/1/1135/7914/products/02cd6ebc94b34066a9193d9eeeb3d964_19a0d271-01ca-42ad-bb07-af16916de59c_compact_cropped.jpg?v=1516887412" align="left" width="60" height="60" class="order-list__product-image" style="margin-right: 15px; border-radius: 0; border: 1px none #e5e5e5;">
          
        </td>
        <td class="order-list__product-description-cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; width: 100%;">
          

          

          <span class="order-list__item-title" style="font-size: 18px; font-weight: normal; line-height: 1.33; color: #050505;">i-Type Blackout Film Triple Pack&nbsp;×&nbsp;1</span><br>
          
        </td>
        <td class="order-list__price-cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; white-space: nowrap;">
          
          <p class="order-list__item-price" style="color: #555; line-height: 1.33; font-size: 18px; font-weight: 600; margin: 0 0 0 15px;" align="right">$45.00</p>
        </td>
      </tr></tbody></table>
    </td>
  </tr>

    

    
    

    

  <tr class="order-list__item" style="width: 100%; border-top-width: 1px; border-top-color: #e5e5e5; border-top-style: solid;">
    <td class="order-list__item__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding-top: 15px;">
      <table style="border-spacing: 0; border-collapse: collapse;">
        <tbody><tr><td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">
          
          <img src="https://cdn.shopify.com/s/files/1/1135/7914/products/500c4f6cdddc4398b2cd6cb6af2a1c79_compact_cropped.jpg?v=1510690677" align="left" width="60" height="60" class="order-list__product-image" style="margin-right: 15px; border-radius: 0; border: 1px none #e5e5e5;">
          
        </td>
        <td class="order-list__product-description-cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; width: 100%;">
          

          

          <span class="order-list__item-title" style="font-size: 18px; font-weight: normal; line-height: 1.33; color: #050505;">OneStep 2 i-Type Camera&nbsp;×&nbsp;1</span><br>
          
          <span class="order-list__item-variant" style="font-size: 14px; color: #999; line-height: 1.33; font-weight: normal;">White</span>
          
        </td>
        <td class="order-list__price-cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; white-space: nowrap;">
          
          <p class="order-list__item-price" style="color: #555; line-height: 1.33; font-size: 18px; font-weight: 600; margin: 0 0 0 15px;" align="right">$99.99</p>
        </td>
      </tr></tbody></table>
    </td>
  </tr>
</tbody></table>


                    <table class="row subtotal-lines" style="width: 100%; border-spacing: 0; border-collapse: collapse; margin-top: 15px; border-top-width: 1px; border-top-color: #e5e5e5; border-top-style: solid;">
                      <tbody><tr>
                        <td class="subtotal-spacer" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; width: 40%;"></td>
                        <td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">
                          <table class="row subtotal-table" style="width: 100%; border-spacing: 0; border-collapse: collapse; margin-top: 20px;">
                            
                            

                            <tbody><tr class="subtotal-line">
                              <td class="subtotal-line__title" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding: 5px 0;">
                                <p style="color: #050505; line-height: 1.2em; font-size: 18px; font-weight: normal; margin: 0;">
                                  <span style="font-size: 18px; line-height: 1.33; color: #050505; font-weight: normal;">Discount (ORIGINAL10)</span>
                                </p>
                              </td>
                              <td class="subtotal-line__value" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; font-size: 32px; line-height: 1.13; color: #050505; padding: 5px 0;" align="right">
                                <strong style="font-size: 16px; color: #555; font-weight: 600;">$-14.49</strong>
                              </td>
                            </tr>

                            


                            <tr class="subtotal-line">
                              <td class="subtotal-line__title" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding: 5px 0;">
                                <p style="color: #050505; line-height: 1.2em; font-size: 18px; font-weight: normal; margin: 0;">
                                  <span style="font-size: 18px; line-height: 1.33; color: #050505; font-weight: normal;">Subtotal</span>
                                </p>
                              </td>
                              <td class="subtotal-line__value" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; font-size: 32px; line-height: 1.13; color: #050505; padding: 5px 0;" align="right">
                                <strong style="font-size: 16px; color: #555; font-weight: 600;">$130.50</strong>
                              </td>
                            </tr>


                            <tr class="subtotal-line">
                              <td class="subtotal-line__title" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding: 5px 0;">
                                <p style="color: #050505; line-height: 1.2em; font-size: 18px; font-weight: normal; margin: 0;">
                                  <span style="font-size: 18px; line-height: 1.33; color: #050505; font-weight: normal;">Shipping</span>
                                </p>
                              </td>
                              <td class="subtotal-line__value" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; font-size: 32px; line-height: 1.13; color: #050505; padding: 5px 0;" align="right">
                                <strong style="font-size: 16px; color: #555; font-weight: 600;">$0.00</strong>
                              </td>
                            </tr>


                            
                          </tbody></table>
                          <table class="row subtotal-table subtotal-table--total" style="width: 100%; border-spacing: 0; border-collapse: collapse; margin-top: 20px; border-top-width: 2px; border-top-color: #e5e5e5; border-top-style: solid;">

                            <tbody><tr class="subtotal-line">
                              <td class="subtotal-line__title" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding: 20px 0 0;">
                                <p style="color: #050505; line-height: 1.2em; font-size: 18px; font-weight: normal; margin: 0;">
                                  <span style="font-size: 18px; line-height: 1.33; color: #050505; font-weight: normal;">Total</span>
                                </p>
                              </td>
                              <td class="subtotal-line__value" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; font-size: 32px; line-height: 1.13; color: #050505; padding: 20px 0 0;" align="right">
                                <strong style="font-size: 24px; color: #555; font-weight: 600;">$130.50 USD</strong>
                              </td>
                            </tr>

                          </tbody></table>

                          
                          

                          
                        </td>
                      </tr>
                    </tbody></table>


                  </td>
                </tr>
              </tbody></table>
            </center>
          </td>
        </tr>
      </tbody></table>

<?php
//end of shoppinglog;========================================================================
  endif;
?> 
<?php
//Customer information========================================================================
  if(!empty($customer_information)):
?>
      <table class="row section" style="width: 100%; border-spacing: 0; border-collapse: collapse; border-top-width: 1px; border-top-color: #e5e5e5; border-top-style: solid;">
        <tbody><tr>
          <td class="section__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding: 40px 0;">
            <center>
              <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                <tbody><tr>
                  <td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">
                    <h3 style="font-weight: normal; font-size: 22px; line-height: 1.09; color: #050505; margin: 0 0 25px;"><?php echo $customer_information["title"];?></h3>
                  </td>
                </tr>
              </tbody></table>
              <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                <tbody><tr>
                  <td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">

                  <?php
                  if(!empty($customer_information["infos"])): 
                  foreach ($customer_information["infos"] as $key => $row):
                    if(!is_array($row) || empty($row)):
                     continue;
                    endif;                    
                  ?>  
                    <table class="row" style="width: 100%; border-spacing: 0; border-collapse: collapse;margin-bottom:40px;">
                      <tbody><tr>
                        <?php
                        foreach ($row as $_key => $val):
                          $c = pow(10, 2);
                          $width=floor((100/count($row))*$c)/$c;
                        ?>  
                        <td class="customer-info__item" style="padding-bottom: 40px;width:<?php echo $width;?> %;">
                          <h4 style="font-weight: 600; font-size: 18px; color: #050505; line-height: 1.33; margin: 0 0 5px;"><?php echo $val["title"];?></h4>
                          <p style="color: #050505; line-height: 1.33; font-size: 18px; font-weight: normal; margin: 0;">
                            <?php echo nl2br($val["content"]); ?>
                          </p>

                        </td>
                        <?php
                        endforeach;
                        ?>
                                                
                      </tr>

                    </tbody></table>
                  <?php
                  endforeach;
                  endif;
                  ;?>  
                    
                  </td>
                </tr>
              </tbody></table>
            </center>
          </td>
        </tr>
      </tbody></table>
<?php
//end of Customer information========================================================================
  endif;
?>

      <table class="row footer" style="width: 100%; border-spacing: 0; border-collapse: collapse; border-top-width: 1px; border-top-color: #e5e5e5; border-top-style: solid;">
        <tbody><tr>
          <td class="footer__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding: 35px 0;">
            <center>
              <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                <tbody><tr>
                  <td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">
                    
                    <p class="disclaimer__subtext" style="color: #121111; line-height: 1.5; font-size: 16px; font-weight: normal; margin: 0;">
                      <?php if(!empty($email)):?>
  If you have any questions, send us an email at <a href="mailto:<?php echo $email;?>" style="font-size: 16px; text-decoration: underline; color: #a5a8a8;" target="_blank"><?php echo $email;?></a> and we’ll be happy to help.
                      <?php endif;?>
                    </p>
                  </td>
                </tr>
              </tbody></table>
            </center>
          </td>
        </tr>
      </tbody></table>

    

    </td>
  </tr>
</tbody></table>


</body>