<?php require_once( dirname(dirname(__FILE__))."/functions.php"); ?>
<?php
$forCheckbox=$TransCode["SendStatusHand"];
$arr=array(

            array(
                "label"=>"日期區間",
                "type"=>"DateRange",
                "name"=>"DateRange[]"
            ),

            array(
                "label"=>"付款方式",
                "type"=>"options",
                "name"=>"PayType",
                "options"=>$TransCode["PayType"]
            ),

            array(
                "label"=>"交易狀態",
                "type"=>"options",
                "name"=>"TransSuccess",
                "options"=>array(
                        ""=>"不使用",
                        "S"=>"完成結帳",
                        "F"=>"交易失敗"
                        )
            ),
            array(
                "label"=>"有無出貨單號",
                "type"=>"options",
                "name"=>"HasShippingNum",
                "options"=>array(
                        ""=>"不使用",
                        "S"=>"有出貨單號",
                        "F"=>"無出貨單號"
                        )
            ),
            array(
                "label"=>"狀態包含",
                "type"=>"checkbox",
                "name"=>"SendStatus[]",
                "options"=>$forCheckbox,
            ),

             array(
                "label"=>"狀態不包含",
                "type"=>"checkbox",
                "name"=>"SendStatusNo[]",
                "options"=>$forCheckbox,
            ),

        );

    $filterOpts=new inputsMaker;

    $inputs=$filterOpts-> MakInputs($arr);

   // $inputs=inputCreater($arr);
?>
                <div class="single-product shop-quick-view-ajax clearfix">
                    <div class="ajax-modal-title">
                        <h2>條件篩選器</h2>
                    </div>
                    <div class="product modal-padding clearfix">
                        <form action="?" method="get">
                            <?php echo $inputs; ?>
                            <input type="hidden" name="limit" value="2000">
                            <input type="submit" class="button fright" value="送出">
                        </form>
                    </div>

                </div>


<?php
    $AjaxScript=isset($AjaxScript)?$AjaxScript:"";
    echo $AjaxScript;
?>

