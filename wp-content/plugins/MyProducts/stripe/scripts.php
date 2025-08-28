<?php 
$api_key=GetStripeApiKey();
//$create_charge_Url= get_home_url()."?CreateStripeCharge";
$StripeInsertUrl = get_option("StripeInsertUrl")===false?"#":get_option("StripeInsertUrl")."?action=createStripeCharge"; 
ob_start();
?>
<script>
console.log("Scripts for Stripe loaded");
var stripe = Stripe('<?php echo $api_key;?>');

const create_charge = async(token)=>{
  const a = await fetch("<?php echo $StripeInsertUrl;?>",{
      method: "POST",
      credentials: 'include',
      headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
     body: JSON.stringify({
         "datas":infos,
         "stripeToken":token
      })
  });
  
  const b = await a.json();
  const c = await function (b){
      console.log(b);
      if(b.error){
        alert(b.error.message);
       // $("#CreditCardRow").fadeIn(300);
        $("#CreditCardRow").removeClass("loading");
      }

      if(b.success){
        console.log(b.success.message);
        $("#CreditCardRow").hide();
        $(".success .message").html(b.success.message);
        $(".success").fadeIn(300);
        MyCart.FBpixel('Purchase',items);
        MyCart.clear();

        if(typeof stripe_callback == "function"){
          stripe_callback(b.success); 
        }        
        
      }

            
  }(b);

}

(function() {
	var elementStyles = {
    base: {
      color: '#777',
      fontWeight: 500,
      fontFamily: "\"HelveticaNeue\", \"Helvetica Neue\", Helvetica, Arial, sans-serif",
      fontSize: '14px',
      fontSmoothing: 'antialiased',
      '::placeholder': {
        color: '#CFD7DF',
      },
      ':-webkit-autofill': {
        color: '#e39f48',
      },
    },
    invalid: {
      color: '#E25950',

      '::placeholder': {
        color: '#FFCCA5',
      },
    },
  };

	var elementClasses = {
	    focus: 'focused',
	    empty: 'empty',
	    invalid: 'invalid',
	  };

    var elements = stripe.elements({
      fonts: [
        {
          cssSrc: 'https://fonts.googleapis.com/css?family=Source+Code+Pro',
        },
      ],
      locale:"en"
    });
    var cardNumber = elements.create('cardNumber', {
       style: elementStyles,
       classes: elementClasses,
    });
    cardNumber.mount('#example2-card-number');

    var cardExpiry = elements.create('cardExpiry', {
     
    });
    cardExpiry.mount('#example2-card-expiry');

    var cardCvc = elements.create('cardCvc', {
     
    });
    cardCvc.mount('#example2-card-cvc');

    $("form").submit(function(e){
      
      $("#CreditCardRow").addClass("loading");
      
      e.preventDefault();
      elements=[cardNumber, cardExpiry, cardCvc];
      stripe.createToken(elements[0]).then(function(result) {
        console.log(result);

        if(result.error){
          alert(result.error.message);
          $("#CreditCardRow").removeClass("loading");
          //$("#CreditCardRow").fadeIn(300);
          return false;
        }
        if (result.token) {
          console.log(result.token);
          //document.querySelector("input[name=stripeToken]").value=result.token.id; 
          create_charge(result.token.id);


        } else {
          // Otherwise, un-disable inputs.
          //enableInputs();
          //return false;
        }
      });

      return false;
    });
    /*
    const ABCD{token, error} = await stripe.createToken('bank_account', {
      country: 'US',
      currency: 'usd',
      routing_number: '110000000',
      account_number: '000123456789',
      account_holder_name: 'Jenny Rosen',
      account_holder_type: 'individual',
    });
*/

})()




<?php
$content=ob_get_contents();
$content=str_replace("<script>","", $content);
ob_clean();	
echo $content;

