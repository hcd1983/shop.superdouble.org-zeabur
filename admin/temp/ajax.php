<?php require_once( dirname(dirname(__FILE__))."/functions.php"); ?>
<?php

$OrderNo=$_GET["OrderNo"];
$sql="SELECT * FROM `".$dbset["table"]["orders"]."` WHERE `OrderNo` LIKE '".$OrderNo."'";


function GetReceiverData($receiver,$tag){
    if(isset($receiver[$tag])){
        return $receiver[$tag];
    }else{
        return "";
    }
}

$orderinfo=doSQLgetRow($sql);

$row=$orderinfo[0];

$receiver = unserialize($row["receiver"]);



//print_r($orderinfo);

$thisOrder= new PaynowOrder;
$thisOrder -> orderinfo = $row;

$forCheckbox=$TransCode["SendStatusHand"];




$arr=array(
            array(                
                "type"=>"hidden",
                "name"=>"OrderNo",
                "value"=>$thisOrder -> orderinfo["OrderNo"],
                "required"=>true
            ),
            array(
                "label"=>"PayNow單號",
                "type"=>"text",
                "name"=>"BuysafeNo",
                "value"=>urldecode( $thisOrder -> orderinfo["BuysafeNo"] ),
            ),
            array(
                "label"=>"出貨單號",
                "type"=>"text",
                "name"=>"ShippingNum",
                "value"=>urldecode( $thisOrder -> orderinfo["ShippingNum"] ),
            ),
             array(
                "label"=>"訂單狀態",
                "type"=>"checkbox",
                "name"=>"SendStatus[]",
                "options"=>$forCheckbox,
                "value"=> explode(" ",urldecode( $thisOrder -> orderinfo["SendStatus"] )),
            ),
            array(
                "label"=>"付款狀態",
                "type"=>"options",
                "name"=>"TranStatus",
                "options"=>$TransCode["TranStatus"],
                "value"=>urldecode( $thisOrder -> orderinfo["TranStatus"] ),
            ),
            array(
                "label"=>"後台備註",
                "type"=>"textarea",
                "name"=>"memo",
                "value"=>urldecode( $thisOrder -> orderinfo["memo"] ),
            ),

            array(
                "label"=>"收件人",
                "type"=>"text",
                "name"=>"rname",
                "value"=>urldecode( GetReceiverData($receiver,"rname") ),
            ),
            array(
                "label"=>"收件人電話",
                "type"=>"text",
                "name"=>"rphone",
                "value"=>urldecode( GetReceiverData($receiver,"rphone") ),
            ),
            array(
                "label"=>"收件人電話",
                "type"=>"text",
                "name"=>"remail",
                "value"=>urldecode( GetReceiverData($receiver,"remail") ),
            ),
            array(
                "label"=>"收件人郵遞區號",
                "type"=>"text",
                "name"=>"rzip",
                "value"=>urldecode( GetReceiverData($receiver,"rzip") ),
            ),
            array(
                "label"=>"收件人地址",
                "type"=>"text",
                "name"=>"raddress",
                "value"=>urldecode( GetReceiverData($receiver,"raddress") ),
            )

           
            
        );

    $inputs=inputCreater($arr);
?>   



                <div class="single-product shop-quick-view-ajax clearfix">

                    <div class="ajax-modal-title">
                        <h2>訂單編號 <?php echo $thisOrder -> orderinfo["OrderNo"]; ?></h2>
                    </div>

                    <div class="product modal-padding clearfix">
                        <form id="updateForm">

                            <?php echo $inputs; ?>
                
                            <a href="javascript:void(0)" class="button fright" onclick="updateThisRow()">送出</a>


                        </form>



                    </div>   

                </div>
