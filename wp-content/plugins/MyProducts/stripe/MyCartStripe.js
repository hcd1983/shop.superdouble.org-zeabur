console.log("Stripe plugin active");

MyPixelSetting={
	currency:"USD",
	content_type: 'product',
};

MyCart.numberWithCommas=function(x) {
	
	if(typeof x != "number"){
		var value = Number(x);
		var num = x; 
	}else{
		var value = x;
		var num = x.toString();
	}
	res = num.split(".");  
    
    if(res.length == 1 || res[1].length < 3) { 
        value = value.toFixed(2);
    } 
    value = value.toString();
    
    var rightPart = value.split(".")[1];  
    
    var leftPart = x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    
    leftPart = leftPart.split(".")[0];

    return leftPart+"."+ rightPart ;
}
