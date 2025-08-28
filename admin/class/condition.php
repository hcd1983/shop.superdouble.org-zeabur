<?php


	class condition{

		public $conditions = array();
		public $condtions_post;
		public $condtions_des=array();
		public $condtions_title;
		public $limit=250;

        function __construct(){
            if (isset($_GET['limit']) && $_GET['limit']) {
                $this->limit = $_GET['limit'];
            }
        }
		function AndCondition(){

			global $TransCode;
			$post=$this->condtions_post;


			// 超級搜索
			if(isset($post["super"])){

				$this->condtions_des[]="搜索文字: ".$post["super"];
				$targets=["OrderNo","BuysafeNo","CargoList","buyer","receiver","ShippingNum","Note1","memo"];
				$val=$post["super"];
				$or=array();
				foreach($targets as $key => $target){

					$or[]="(`".$target."` LIKE '%".urlencode($val)."%')";

				}
				$this->conditions[]= "(".join(" OR ",$or).")";
			}
			//交易日期

			if(isset($post["DateRange"])){




				$formDate=$post["DateRange"][0];
				$endDate=$post["DateRange"][1];

				if($formDate !=""){
					$this->conditions[] = "`reg_date` > '".$formDate."'";
					$this->condtions_des[]="開始日期: ".$post["DateRange"][0];
				}

				if($endDate !=""){
					$endDate=$endDate.' 23:59:59';
					$this->conditions[] = "`reg_date` <= '".$endDate."'";
					$this->condtions_des[]="截止日期: ".$post["DateRange"][1];
				}


			}


			//結帳方式
			if(isset($post["PayType"]) && $post["PayType"] !=""){
				$val=$post["PayType"];

				$this->condtions_des[]="結帳方式: ".$TransCode["PayType"][$val];

			    $this->conditions[] = "`PayType` LIKE '".$val."'";
			}




			//是否交易成功

			if(isset($post["TransSuccess"])){

				$val = $post["TransSuccess"];
				if($val == "S") {
					$this->condtions_des[]="交易狀態: 成功";
			    	$this->conditions[] = "`TranStatus` LIKE 'S'";
			    }elseif($val == "F"){
			    	$this->condtions_des[]="交易狀態: 失敗或未回傳";
			    	$this->conditions[] = "(`TranStatus` LIKE 'F' OR `TranStatus` LIKE '')";
			    }

			}else {
                $this->condtions_des[]="交易狀態: 成功";
                $this->conditions[] = "`TranStatus` LIKE 'S'";
            }


			//coupon

			if(isset($post["coupon"]) && $post["coupon"] !=""){

				$val = $post["coupon"];
				$this->condtions_des[]="使用 coupon: ".$post["coupon"];
			    $this->conditions[] = "`coupon` LIKE '{$post["coupon"]}'";

			}


			//是否有出貨單號

			if(isset($post["HasShippingNum"])){

				$val=$post["HasShippingNum"];

				if($val == "S") {

					$this->condtions_des[]="出貨單號: 有";
			    	$this->conditions[] = "`ShippingNum` NOT LIKE ''";
			    }elseif($val == "F"){
			    	$this->condtions_des[]="出貨單號: 無";
			    	$this->conditions[] = "`ShippingNum` LIKE ''";
			    }

			}


			//出貨狀態包含 ... array

			if(isset($post["SendStatus"])){

				$vals=$post["SendStatus"];

				$or=array();
				foreach($vals as $key => $val){


					$this->condtions_des[]=$TransCode["SendStatus"][$val];


					$or[]="(`SendStatus` LIKE '%".$val."%')";

				}

				$this->conditions[]= "(".join(" OR ",$or).")";

				//$vals = join("','",$val);
				//$this->conditions[] = "`SendStatus` IN ('$vals')";

			}


			//出貨狀態不包含 ... array

			if(isset($post["SendStatusNo"])){

				$vals=$post["SendStatusNo"];

				$or=array();
				foreach($vals as $key => $val){


					$this->condtions_des[]="排除".$TransCode["SendStatus"][$val];


					$and[]="(`SendStatus` NOT LIKE '%".$val."%')";

				}

				$this->conditions[]= "(".join(" AND ",$and).")";

				//$vals = join("','",$val);
				//$this->conditions[] = "`SendStatus` IN ('$vals')";

			}







		}

		function MakeCondition(){
			$this->AndCondition();

		}





		function Mysql() {

		   global $dbset;

		   $this->MakeCondition();

		   $title="訂單管理";

		   $conditions=$this->conditions;

		   $fliters="";

		   if (count($conditions) > 0) {
		      $fliters .= " WHERE " . implode(' AND ', $conditions);
		    }


		    if(is_numeric($this->limit)){
		    	$limit="";
		    	$limit.= "LIMIT ".$this->limit;
		    	$title.=" ( 上限 ".$this->limit." 筆)";
		    }

		    if(count($this->condtions_des) > 0){

		    	$condtions_des=$this->condtions_des;
				$this->condtions_title=join(" 、 ",$condtions_des);

				$this->condtions_title=$title."<br>"."<small>".$this->condtions_title."</small>";

		    }else{
		    	$this->condtions_title=$title;
		    }





		    $sql = "SELECT * FROM `".$dbset["table"]["orders"]."` ".$fliters." ORDER BY `id` DESC ".$limit;
		    return $sql;
		}


	}

?>
