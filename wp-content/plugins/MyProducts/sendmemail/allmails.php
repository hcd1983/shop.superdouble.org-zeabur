<?php
global $wpdb;
global $SendMeMail;
$user = wp_get_current_user();
$table_name= $SendMeMail["table_name"];
$post_per_page=100;
$values = $_GET;

unset($values["delete_mail"]);
$redrictPage=UrlWithGetVal($url="admin.php",$values);

if(isset($_GET["delete_mail"])){
	
	$allowed_roles = array('editor', 'administrator');
	if( array_intersect($allowed_roles, $user->roles ) ): 
	    $mail_id=$_GET["delete_mail"];
	    $where = ["mail_id"=>$mail_id];
	    $result=$wpdb->delete( $table_name, $where );
	    if($result==false){
	    	echo "<script>alert('Delete Fail!')</script>";
	    	exit;
	    }
	    header("Location:{$redrictPage}");
	else:
		echo "<script>alert('你沒有權限這麼做!')</script>"; 
	endif;        
}

if(isset($_POST["checkBoxArray"])){
	$allowed_roles = array('editor', 'administrator');
	if( array_intersect($allowed_roles, $user->roles ) ): 
	    $bulk_options=$_POST["bulk_options"];

	    foreach ($_POST["checkBoxArray"] as $key => $mail_id) {
	       switch ($bulk_options) {          
	          case 'delete':               
	               $where = ["mail_id"=>$mail_id];
	    		   $result=$wpdb->delete( $table_name, $where );
	    		   if($result==false){
				    	//echo "<script>alert('Delete Fail! id:{$mail_id}')</script>";
				    	exit;
				    }
	               break;
	                                
	           default:
	               # code...
	               break;
	       }
	    }
	else:
	echo "<script>alert('你沒有權限這麼做!')</script>";     
	endif;    
}

if(isset($_GET["product_id"]) && $_GET["product_id"] != ""){
	$product_id=$_GET["product_id"];
	$whereQuery = "WHERE `product_id` LIKE '{$product_id}'";
	$p_title = "<h3>目前顯示商品為: ".get_the_title($product_id)."</h3>";
}else{
	$whereQuery = "";
	$p_title ="";
	unset($values["product_id"]);
}

$sql = "SELECT count(*) AS `total` FROM `{$table_name}` ".$whereQuery.";";
$count_result = $wpdb->get_results($sql,"ARRAY_A");
$totalRow =  $count_result[0]["total"];
$total_pages=ceil($totalRow/$post_per_page);

if(isset($_GET["p"])){                
    $page=$_GET["p"];

}else{
    $page=1;
}

if($page < 1){
	$page=1;
}

if($page > $total_pages){
	$page=$total_pages;
}

if($page=="" || $page== 1){
    $start=0;
}else{
    $start=$page*$post_per_page - $post_per_page;
}



$sql = "SELECT DISTINCT(`product_id`) AS `product_id` FROM `{$table_name}` ;";
$p_result = $wpdb->get_results($sql,"ARRAY_A");

$sql="SELECT * FROM `{$table_name}` ".$whereQuery." ORDER BY `mail_id` DESC LIMIT {$start},{$post_per_page}";
$result = $wpdb->get_results($sql,"ARRAY_A");

$values["p"]=1;
$FirstPageUrl=UrlWithGetVal($url="admin.php",$values);
$values["p"]=$total_pages;
$LastPageUrl=UrlWithGetVal($url="admin.php",$values);

$nextPage=$page + 1 > $total_pages?$total_pages:$page + 1;
$values["p"]=$nextPage;
$NextPageUrl=UrlWithGetVal($url="admin.php",$values);

$prevPage=$page - 1 < 1?1:$page - 1;
$values["p"]=$prevPage;
$prevPageUrl=UrlWithGetVal($url="admin.php",$values);
?>
<div class="wrap">
<h1 class="wp-heading-inline">蒐集到的 Email</h1>
<?php echo $p_title; ?>
<div id="product_filter">
	<form action="admin.php" method="GET">
		<label for="product_id">商品過濾:</label>
	    <select name="product_id" class="form-control">
	        <option value="">所有商品</option>
	        <?php
	        foreach ($p_result as $key => $row) {
	        	$product_id = $row["product_id"];
				$product_title = get_the_title($product_id);
	        	echo "<option value=\"{$product_id}\">{$product_title}</option>";
	        }
	        ?>
	    </select>
	    <input type="hidden" name="page" value="sendmemail">
	    <button type="submit">過濾</button>
    </form>	
</div>
<br class="clear">

<form id="emails" method="POST">
	<div id="bulkOptionContainer">
        <select name="bulk_options" class="form-control">
            <option value="">Select Options</option>
            <option value="delete">Delete</option>
        </select>
        <input type="submit" class="button action" value="Apply">
    </div>
    <br class="clear">
	<div class="tablenav-pages" style="">
		<span class="displaying-num"><?php echo $totalRow;?> items</span>
		<span class="pagination-links">
			<a href="<?php echo $FirstPageUrl;?>"><span class="tablenav-pages-navspan" aria-hidden="true">«</span></a>
			<a href="<?php echo $prevPageUrl;?>"><span class="tablenav-pages-navspan" aria-hidden="true">‹</span></a>
			<span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input">
			<span class="tablenav-paging-text"><?php echo $page;?> of <span class="total-pages"><?php echo $total_pages;?></span></span>
			<a href="<?php echo $NextPageUrl;?>"><span class="tablenav-pages-navspan" aria-hidden="true">›</span></a>
			<a href="<?php echo $LastPageUrl;?>">
				<span class="tablenav-pages-navspan" aria-hidden="true">»</span>
			</a>	
		</span>	
	</div>
<table class="widefat fixed striped">
	<thead>
		<tr>
			<th><input id="selectAllboxes" type="checkbox"></th>
			<th>Email</th>
			<th>需求商品</th>
			<th>日期</th>
			<th>Delete</th>
		</tr>
	</thead>
	<tbody>
		<?php			
			foreach ($result as $key => $row) {
				$product_id = $row["product_id"];
				$product_title = get_the_title($product_id);
				$values = $_GET;
				$values["delete_mail"]=$row["mail_id"];
				$deleteUrl =UrlWithGetVal($url="admin.php",$values);
				echo "<tr>";
				echo "<td>"."<input class=\"checkBoxes\" name=\"checkBoxArray[]\" type=\"checkbox\" value=\"{$row["mail_id"]}\">"."</td>";	
				echo "<td>".$row["email"]."</td>";	
				echo "<td>".$product_title."</td>";	
				echo "<td>".$row["mail_date"]."</td>";
				echo "<td>"."<a href='{$deleteUrl}'>刪除</div>"."</td>";	
				echo "</tr>";
			}
		?>
	</tbody>
</table>
	

</form>


<br class="clear">

<div class="tablenav-pages">
	<span class="displaying-num"><?php echo $totalRow;?> items</span>
	<span class="pagination-links">
		<a href="<?php echo $FirstPageUrl;?>"><span class="tablenav-pages-navspan" aria-hidden="true">«</span></a>
		<a href="<?php echo $prevPageUrl;?>"><span class="tablenav-pages-navspan" aria-hidden="true">‹</span></a>
		<span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input">
		<span class="tablenav-paging-text"><?php echo $page;?> of <span class="total-pages"><?php echo $total_pages;?></span></span>
		<a href="<?php echo $NextPageUrl;?>"><span class="tablenav-pages-navspan" aria-hidden="true">›</span></a>
		<a href="<?php echo $LastPageUrl;?>">
			<span class="tablenav-pages-navspan" aria-hidden="true">»</span>
		</a>	
	</span>	
</div>

<script type="text/javascript">
	//SELECT ALL
    $("#selectAllboxes").change(function(){
        if(this.checked){
            $(".checkBoxes").each(function(i){
                this.checked=true;
            })
        }else{
            $(".checkBoxes").each(function(i){
                this.checked=false;
            })
        }
    })
</script>