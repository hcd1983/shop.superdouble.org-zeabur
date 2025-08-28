console.log("sendmemail init");

$.fn.serializeObject = function()
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
   return o;
};

function SendmemailLoaded(myself){
   
    

   var iframe = $(myself).contents();

   var NewHeight=iframe.find("html").height()

   $(myself).height(NewHeight);
   
   setTimeout(function(){
   		$(".sendmemail_mfp .mfp-content").height(NewHeight);
   		$(".mfp-iframe,.mfp-iframe-holder .mfp-close").css('visibility', 'visible');
   		   		
   },300)
  
   $(".mfp-loading").fadeOut();
   //var $iframe = $(myself);
  

    iframe.find("#sendmemailForm").submit(function(){ 
    	var targetUrl = window.location.href;       
    	var theData =$(this).serializeObject();
    	
        jQuery.ajax({
	        url: targetUrl,
	        dataType: 'text',
	        data: theData,
	        type: 'POST'
	    }).done(function( data ) {
	    	if(data == 1){
	    		var inform_message = MyCartWords("inform_me_text");
	    		iframe.find("#sendmemailFormContainer h3").html("您的資料已送出，我們將會儘快告知您產品的最新消息!");
	    		iframe.find("#sendmemailForm").remove();
	    		setTimeout(function(){
			   		jQuery.magnificPopup.close();   		
			    },2000)
	    		
	    		
	    	}else{
	    		alert("發生不明錯誤");
	    	}
		});
		
        return false;
    });

    	
}

function Open_Email_url(_url){
	jQuery.magnificPopup.open({
	  preloader: false,
	  callbacks: {
	    open: function() {
	    	
	    	$(".mfp-loading").fadeIn();
			$(".mfp-iframe,.mfp-iframe-holder .mfp-close").css('visibility', 'hidden');
	      //alert("open");
	      // Will fire when this exact popup is opened
	      // this - is Magnific Popup object
	    },
	    close: function() {
	    	//alert("Close");
	      // Will fire when popup is closed
	    },
	    beforeOpen: function() {
		  //  console.log('Start of popup initialization');
		  },
	  }, 	
	  items: {
	    src: _url, // can be a HTML string, jQuery object, or CSS selector
	    //type: 'iframe'
	  },

	  mainClass: 'mfp-fade sendmemail_mfp',
	  type: 'iframe',
	  //key: 'Ajax-link-key',
	  //srcAction: 'iframe_data-cool',

	  iframe: {
	  	markup: '<div class="mfp-iframe-scaler myAjax">'+
	            '<div class="mfp-close"></div>'+
	            '<div class="mfp-loading"><i class="fas fa-spinner fa-spin"></i><br>Loading</div>'+
	            '<iframe class="mfp-iframe" frameborder="0" allowfullscreen onload="SendmemailLoaded(this);">Loading...</iframe>'+
	          '</div>', 
	    patterns: {
	      dailymotion: {
	       
	        //index: 'www.instagram.com',
	        
	        src: _url
	       
	        
	      }
	    }
	  }
	}) 

}


$(document).on("click",".addtocart.nostore",function(e){
	var p_id = $(this).data("id");
	var _url=window.location.href+"?product_id="+p_id+"&getmail";
	Open_Email_url(_url);
})
