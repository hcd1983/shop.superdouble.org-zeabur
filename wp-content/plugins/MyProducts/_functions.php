<?php
//Debugger

function myproducts_debugger() {
	$MyDebugger = get_option("MyDebugger")===false?0:get_option("MyDebugger");
	if($MyDebugger!=1){
		return false;
	}

    $user = wp_get_current_user();
    $allowed_roles = array( 'administrator');
     if( array_intersect($allowed_roles, $user->roles ) ) {  
      return false;
    }else{
      return true;
    }

}

//url with get =====================================================

function UrlWithGetVal($url="",$values=array()){
    if($url==""){
       $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; 
   }else{
       $actual_link=$url; 
   }

   $pure_link = explode( '?', $actual_link );
   $pure_link = $pure_link[0];

   $queryString="";
   if(is_array($values) && count($values) > 0){
     array_walk($values,function(&$value,$key){
        $value="$key=$value";
     });
     $queryString="?".join("&",$values);
   }

   $fixedlink=$pure_link.$queryString;
   
   return $fixedlink;
}

//if is IE===========================================================

function is_ie(){
  //return false;
  $ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
  if (preg_match('~MSIE|Internet Explorer~i', $ua) || (strpos($ua, 'Trident/7.0; rv:11.0') !== false) || (strpos($ua, 'Trident/7.0') !== false)) {
    return true;
  }else{
    return false;
  }
}

//money logo==============================================================
function GetTheMoneyLogo(){
  $logo="$";
  return apply_filters("themoneylogo",$logo);
}

function PriceFormat($f="",$n =""){
	if(!$n){
    return $f.number_format(0);
  }
	$price = $f.number_format($n);
	return apply_filters("PriceFormat",$price,$f,$n);
}

//Locales=================================================================
function MyCartWords($index,$changeto=array()){
	global $MycartLang;
	if(isset($MycartLang[$index])){
		$res = $MycartLang[$index];
		if(is_array($changeto) && count($changeto) > 0){
			foreach ($changeto as $key => $value) {
				if(isset($value["search"]) && isset($value["replace"])){
					$res = str_replace($value["search"], $value["replace"], $res);
				}			
			}
		}
	}else{
		$res ="";
	}
	return apply_filters("MyCartWords",$res,$index);
}

//轉址到主產品 =============================================

add_action("wp_head","option_product_page",1);
function option_product_page(){
  if(is_admin()){
    return;
  }
  $p_id = get_the_ID();
  $post_type = get_post_type( $p_id );

  if($post_type == "myproducts"){
    $parent_id = wp_get_post_parent_id( $p_id ); 
    if($parent_id){
      $location = get_permalink($parent_id);
?>
    <script type="text/javascript">
      location.replace("<?php echo $location;?>");
    </script>
<?php
      exit;
    }
  }
}