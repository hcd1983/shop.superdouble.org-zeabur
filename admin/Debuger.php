<?php
require_once("functions.php");
require_once("email-tpl/main.php");
notLogin("login.php"); 

$theme_setting_style_1=array(
  "title"=>"Here is the title",
);

$temp=new email_tpl_creator();
$temp->theme_setting = $theme_setting_style_1;
ob_start();
$temp->render();
$message = ob_get_contents();
ob_end_clean();

echo $message;
//sendmail($sentto="hcd@mojopot.com",$receiver="Dean Huang",$subject="title",$message);
//echo Stripe_sendmail($sentto="hcd@mojopot.com",$receiver="Dean Huang",$subject="title",$message,$type="test",$custom_id=null);
exit;
ob_start();
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
$summary = ob_get_contents();
ob_end_clean();

ob_start();
?>
<table class="row footer" style="width: 100%; border-spacing: 0; border-collapse: collapse; border-top-width: 1px; border-top-color: #e5e5e5; border-top-style: solid;">
        <tbody><tr>
          <td class="footer__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding: 35px 0;">
            <center>
              <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                <tbody><tr>
                  <td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">
                    <p class="disclaimer__subtext" style="color: #121111; line-height: 1.5; font-size: 16px; font-weight: normal; margin: 0;">If you have any questions, send us an email at <a href="mailto:usa@polaroidoriginals.com?subject=" style="font-size: 16px; text-decoration: underline; color: #a5a8a8;" target="_blank">service@spinbox.cc</a> and we’ll be happy to help.</p>
                    <table class="row actions" style="width: 100%; border-spacing: 0; border-collapse: collapse; margin-top: 20px;">
                      <tbody><tr>
                        <td class="actions__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;" valign="bottom">
                          <table class="button main-action-cell" style="border-spacing: 0; border-collapse: collapse; float: left; margin-right: 15px; width: auto; margin-top: 0; margin-bottom: 15px;">
                            <tbody><tr>
                              <td class="button__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; border-radius: 0; border: 1px solid #050505;" align="center" bgcolor="#050505"><a href="#" class="button__text" style="font-size: 18px; text-decoration: none; display: block; color: white; line-height: 24px; padding: 5px 30px;" target="_blank">View your order →</a></td>
                            </tr>
                          </tbody></table>
                          
                          <table class="link secondary-action-cell" style="border-spacing: 0; border-collapse: collapse; margin-top: 6px; width: auto; margin-bottom: 15px;">
                            <tbody><tr>
                              <td class="link__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; font-size: 18px; line-height: 1.33; font-weight: normal; color: #a5a8a8;">or <a href="https://us.polaroidoriginals.com?utm_source=autoresponder&amp;utm_medium=email&amp;utm_campaign=Order%20Confirmation" class="link__text" style="font-size: 18px; text-decoration: underline; line-height: 1.33; font-weight: normal; color: #a5a8a8; margin-left: 5px;" target="_blank">Visit our store</a>
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
$footer = ob_get_contents();
ob_end_clean();
?>

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
                        <td class="masthead__cell" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding-bottom: 20px;">
                          <img src="https://cdn.shopify.com/s/files/1/1135/7914/t/67/assets/email_header_01.gif?10234766619349777705" alt="We’re on it. " style="max-width: 100%;">
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

                    <h2 style="font-weight: normal; font-size: 32px; line-height: 1.13; color: #050505; margin: 0 0 10px;">Receipt from AIO CONCEPT INC</h2>
                    <p style="color: #050505; line-height: 1.33; font-size: 18px; font-weight: normal; padding-top: 25px; margin: 0;">
  
    Hey Smiles Davis, <br><br>
    You're receiving this email because you made a purchase at AIO CONCEPT INC. AIO CONCEPT INC partners with Stripe to provide secure invoicing and payments processing.<br><br>
  
</p>
                    
                  
                  </td>
                </tr>
              </tbody></table>
            </center>
            <center>
              <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                <tbody><tr>
                  <td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">
                    <h3 style="font-weight: normal; font-size: 22px; line-height: 1.09; color: #050505; margin: 0 0 25px;">Customer information</h3>
                  </td>
                </tr>
              </tbody></table>
              <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                <tbody><tr>
                  <td style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif;">

                    <table class="row" style="width: 100%; border-spacing: 0; border-collapse: collapse;">
                      <tbody><tr>
                        
                        <td class="customer-info__item" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding-bottom: 40px; width: 50%;">
                          <h4 style="font-weight: 600; font-size: 18px; color: #050505; line-height: 1.33; margin: 0 0 5px;">AMOUNT PAID</h4>
                          <p style="color: #050505; line-height: 1.33; font-size: 18px; font-weight: normal; margin: 0;">
                            $5.00
                          </p>

                        </td>
                        
                        
                        <td class="customer-info__item" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding-bottom: 40px; width: 50%;">
                          <h4 style="font-weight: 600; font-size: 18px; color: #050505; line-height: 1.33; margin: 0 0 5px;">DATE PAID</h4>
                          <p style="color: #050505; line-height: 1.33; font-size: 18px; font-weight: normal; margin: 0;">
                            June 20, 2018
                          </p>

                        </td>
                        
                      </tr>
                    </tbody></table>
                    <table class="row" style="width: 100%; border-spacing: 0; border-collapse: collapse;">
                      <tbody><tr>
                        
                        <td class="customer-info__item" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding-bottom: 40px; width: 50%;">
                          <h4 style="font-weight: 600; font-size: 18px; color: #050505; line-height: 1.33; margin: 0 0 5px;">PAYMENT METHOD</h4>
                          <p style="color: #050505; line-height: 1.33; font-size: 18px; font-weight: normal; margin: 0;">
                                VISA <span> – 4242</span></p>
                        </td>
                        
                        
                        
                        <td class="customer-info__item" style="font-family: -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, &quot;Roboto&quot;, &quot;Oxygen&quot;, &quot;Ubuntu&quot;, &quot;Cantarell&quot;, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; padding-bottom: 40px; width: 50%;">
                                                    
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

      <?php echo $summary;?>

      

      <?php echo $footer;?>

    

    </td>
  </tr>
</tbody></table>