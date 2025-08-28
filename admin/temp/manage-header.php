<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
	<?php

		function is_wplogin(){
			if(isset($_SESSION["user"]["wp_login"]) && $_SESSION["user"]["wp_login"] == 1){
				return true;
			}else{
				return false;
			}
		}

		function NavListCreater($arr){

			$role=isLogin()["role"];
			if(is_wplogin() == true){
				$role = "wp_user";
			}
			$output="<ul>";

			foreach ($arr as $key => $val){

				if(isset($val["role"]) && is_array($val["role"]) && count($val["role"]) > 0 ){


					if(!in_array($role, $val["role"]) && $role !="super"){
						continue;
					};
				}

				if(isset($val["target"])){
					$target='target="'.$val["target"].'"';
				}else{
					$target="";
				}

				$li='<li><a href="'.$val["url"].'" '.$target.'><div>'.$val["title"].'</div></a>';



				if(isset($val["sub"]) && is_array($val["sub"])){
					$li.=NavListCreater($val["sub"]);
				};

				$li.="</li>";

				$output.=$li;

			};

			$output.="</ul>";

			return $output;

		};

		if(isset($stripe_setting) && $stripe_setting != false){
			$stripe_role=["admin","super"];
		}else{
			$stripe_role=["super"];
		}


		$navMenu=array(

				array(
					"url" => "orderList.php",
					"title"=>"訂單管理",
					"role"=>["admin","wp_user"]
				),
                array(
                    "url" => "tester-shipping-uploader.php",
                    "title"=>"物流單號上傳",
                    "role"=>["admin","wp_user"]
                ),
                array(
					"url" => "dash-shipping-2.php",
					"title"=>"輕輕背預購 (第二次)",
					"role"=>["admin","wp_user"]
				),

				array(
					"url" => "manage.php",
					"title"=>"月歷顯示",
					"role"=>["admin","wp_user"]
				),



				array(
					"url" => "income_month.php",
					"title"=>"營收查詢",
					"role"=>[]
				),

				array(
					"url" => "setting-buymail.php",
					"title"=>"Email 設定",
					"role"=>["admin","wp_user"]
				),
				/*
				array(
					"url" => "manage.php",
					"title"=>"訂單管理",

					"sub"=>array(
							array("url" => "manage.php",
								  "title" => "每日訂單",
								  "role"=>["admin","wp_user"]
								),
							array("url" => "orderList.php",
								  "title" => "訂單列表",
								  "role"=>["admin","wp_user"]
								),
							array("url" => "uploader.php",
								  "title" => "各種上傳",
								  "role"=>["super"]
								),

							)
					),
					*/
				/*
				array(
					"url" => "http://shop.spinbox.cc/dj-admin/",
					"title"=>"Stripe 訂單管理",
					),
				*/
				/*
				array(
					"url" => "stripeList.php",
					"title"=>"Stripe 付款狀態",
					"role"=>$stripe_role,
					),
				*/
				array(
					"url" => "custom_order.php",
					"title"=>"自訂付款連結",
					 "role"=>["super","admin"],
					),

				array(
					"url" => "coupon.php",
					"title"=>"Coupon 使用狀況",
					 "role"=>["super","admin","wp_user"],
					),

				array(
					"url" => "custom_stripe.php",
					"title"=>"Stripe 付款連結",
					"role"=>$stripe_role,
					),

				array(
					"url" => "member_export.php",
					"title"=>"匯出會員",
					"target"=>"_blank",
					"role" => array("super"),
					),

				array(
					"url" => "userList.php",
					"title"=>"使用者管理",
					"role"=>["super","admin"],
					"sub"=>array(
							array("url" => "userList.php",
								  "title" => "使用者列表",
								),
							array("url" => "newUser.php",
								  "title" => "新增使用者",
								  "role"=>array("super","admin")
								),


							)
					),
				array(
					"url" =>"#",
					"title" => "設定",
					"role" => array("super"),
					"sub"=>array(
							array(
							"url" =>"setting-basic.php",
							"title" =>"基本設定",
							),
							array(
							"url" =>"setting-wordpress.php",
							"title" =>"wordpress 設定",
							),
							array(
							"url" =>"setting-domain.php",
							"title" =>"domain 設定",
							),
							array(
							"url" =>"setting-phpmailer.php",
							"title" =>"phpmailer 設定",
							),
							array(
							"url" =>"setting-paynow.php",
							"title" =>"paynow 設定",
							),
							array(
							"url" =>"setting-slack.php",
							"title" =>"Slack 設定",
							),
							array(
							"url" =>"setting-stripe.php",
							"title" =>"stripe 設定",
							),

						)
					),

				array(
					"url"=>"login.php?logout",
					"title"=>"登出"
					)
			);


	?>
	<?php require_once("inputcss.php"); ?>
</head>

<body class="stretched side-header open-header side-header-open ">

	<!-- Document Wrapper
	============================================= -->
	<div id="wrapper" class="clearfix">

		<div id="header-trigger"><i class="icon-line-menu"></i><i class="icon-line-cross"></i></div>

		<!-- Header
		============================================= -->
		<header id="header" class="no-sticky">

			<div id="header-wrap">

				<div class="container clearfix">

					<div id="primary-menu-trigger"><i class="icon-reorder"></i></div>

					<!-- Logo
					============================================= -->
					<div id="logo" class="nobottomborder">
						<h2>Hello, <?php echo isLogin()["name"]; ?></h2>
					</div><!-- #logo end -->

					<!-- Primary Navigation
					============================================= -->
					<nav id="primary-menu">


						<?php echo NavListCreater($navMenu); ?>

					<!--<ul>
							<li>
								<a href="login.php?logout"><div>統計資料</div></a>
							</li>
							<li><a href="index.html"><div>訂單</div></a>
								<ul>
									<li><a href="index-wedding.html"><div>篩選器</div></a></li>
									<li><a href="index-wedding.html"><div>所有訂單</div></a></li>
									<li><a href="index-restaurant.html"><div>逐月檢視</div></a></li>
								</ul>
							</li>

							<li><a href="products-list.php"><div>產品</div></a>
								<ul>
									<li><a href="products-list.php"><div>所有產品</div></a></li>
									<li><a href="products-addnew.php"><div>新增產品</div></a></li>
								</ul>
							</li>

							<li><a href="email-list.php"><div>E-Mail 列表</div></a></li>

							<li><a href="index.html"><div>使用者</div></a>
								<ul>
									<li><a href="index-wedding.html"><div>管理使用者</div></a></li>
									<li><a href="index-restaurant.html"><div>新增使用者</div></a></li>
								</ul>
							</li>
							<li>
								<a href="index.html"><div>設定</div></a>
								<ul>
									<li><a href="index-wedding.html"><div>網站設定</div></a></li>
									<li><a href="setting-phpmailer.php"><div>phpmailer設定</div></a></li>
									<li><a href="setting-paynow.php"><div>Paynow設定</div></a></li>
								</ul>
							</li>

							<li>
								<a href="login.php?logout"><div>登出</div></a>
							</li>
						</ul>-->

					</nav><!-- #primary-menu end -->



				</div>

			</div>

		</header><!-- #header end -->
