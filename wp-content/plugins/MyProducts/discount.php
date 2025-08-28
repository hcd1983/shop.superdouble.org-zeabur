<?php
function MyDiscount($items){

	if($items==NULL){
		return 0;
	}

	$discount=0;
	
	return $discount=apply_filters("fixdiscount",$discount,$items);
}