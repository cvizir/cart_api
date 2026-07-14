<?php

	ini_set("memory_limit","100M");
	set_time_limit(900);

	$test_mode=0;
 	require_once("../../config.php");
	require_once("../../include/function.php");
	require_once('../../class/PHPExcel.php');
	require_once('../../class/PHPExcel/Writer/Excel5.php');
	require_once('../../class/PHPExcel/IOFactory.php');
	

	$esend_array=array('0'=>'不願意','1'=>'願意',); 	

	$no= $_REQUEST['no'];
	$start_time= $_REQUEST['start_time'];
	$end_time= $_REQUEST['end_time'];
	$sh_state= $_REQUEST['sh_state'];
	
	
	

	if($no!=''){
	    $where=" WHERE `no`='$no'";
	}else{
		
		if($start_time!='' || $end_time!='' || $sh_state!=''){
			$where=" WHERE";
			
			if($start_time!=''){
				$where= $where." AND `buytime`>='".$start_time."'";
			}
			if($end_time!=''){
				$where= $where." AND `buytime`<='".$end_time."'";
			}
			if($sh_state!=''){
				$where= $where." AND `order_state`='".$sh_state."'";
			}	
			$where=str_replace("WHERE AND", "WHERE",$where);
		}
		
			
	}
	$sql_order="SELECT * FROM `orderlist` ".$where." ORDER BY buytime DESC";
	
	//$sql_order="SELECT * FROM `orderlist` ORDER BY no DESC LIMIT 5";
	// echo"$sql_order";

	if($test_mode==1){
		echo'$sql_order='."$sql_order".'<br />';
	}
    $rs_order=mysql_query($sql_order);	
	$num_order=mysql_num_rows($rs_order);	
	//echo'num_order='."$num_order";
	$J=0;
	if($num_order>=1){

	$excel_array[$J][] ='訂單日期';
	$excel_array[$J][] ='訂單時間'; 
	$excel_array[$J][] ='編號'; 
	$excel_array[$J][] ='付款方式'; 
	$excel_array[$J][] ='書號'; 
	$excel_array[$J][] ='產品名稱'; 
	
	$excel_array[$J][] ='出版社'; 
	$excel_array[$J][] ='銷售價'; 
	$excel_array[$J][] ='數量'; 
	$excel_array[$J][] ='小計'; 

	$excel_array[$J][] ='收件人姓名'; 
	$excel_array[$J][] ='收件人電話'; 
	$excel_array[$J][] ='收件人地址'; 
	$excel_array[$J][] ='配送方式'; 
	$excel_array[$J][] ='發票'; 
	$excel_array[$J][] ='抬頭';
	$excel_array[$J][] ='統編'; 
	$excel_array[$J][] ='訂單備註'; 

	$J++;
		
	while($row_order=mysql_fetch_array($rs_order,MYSQL_ASSOC)){
	
	


	
	
	

	$order_all_list=json_decode($row_order['order_info'],true);	  
	$order_num='C17'.sprintf("%06d",$row_order['no']);	
	$order_addr= $row_order['invoice_zipcode'].' '.$row_order['invoice_city'].$row_order['invoice_area'].$row_order['invoice_addr'];	
	if($row_order['pre_post_date']=='0000-00-00'){ $row_order['pre_post_date']=''; }

	$order_md = substr($row_order['buytime'],-14, 5);
	$order_his = substr($row_order['buytime'],-8);

	//付款方式
	$order_pay_mode= $pay_mode_array[$row_order['pay_mode']];
	//配送方式
	if($row_order['transportation_ps']!="" ){$transportation_ps='配送:'.$row_order['transportation_ps'];}else{$transportation_ps='';}
	//發票
	if($row_order['invoice_type'] == 2){
		$receipt_type = '二聯';
	}
	if($row_order['invoice_type'] == 3){
		$receipt_type = '三聯';
	}
	
	$receipt_title = $row_order['invoice_title'];
	$receipt_code = $row_order['invoice_num'];
	//備註
	if($row_order['ps']!="" ){$ps='備註:'.$row_order['ps'];}else{$ps='';} 
	//跳行
	if($row_order['transportation_ps']!=""&&$row_order['invoice_type']==3 ){$n1="\n";}else{$n1='';} 
	if($row_order['transportation_ps']!=""&&$row_order['ps']!="" ){$n2="\n";}else{$n2='';}
	
	$order_book=0;	

	if($test_mode==1){
	/* 	
	print_r($row_order);	
	echo'<br /><br />';
	print_r($order_all_list);
	 */
	}
	

	
	for( $I=0; $I < (@sizeof($order_all_list['order_list'])); $I++ ){
	$key_no=@key($order_all_list['order_list']);  
	//echo '$key_no='.$key_no;
	if($key_no!=''){

	$pd_name=@$order_all_list['order_list']["$key_no"]['pd_name'];
	$pd_price=@$order_all_list['order_list']["$key_no"]['pd_price'];
	$pd_buy_num=@$order_all_list['order_list']["$key_no"]['pd_buy_num'];
	$product_num=@$order_all_list['order_list']["$key_no"]['product_num'];
	$order_book= $order_book+$pd_buy_num;
	
	
	$sql_product="SELECT * FROM `product` WHERE `product_num`='$product_num'";
	$rs_product=@mysql_query($sql_product);
	$row_product=@mysql_fetch_array($rs_product,MYSQL_ASSOC);
	
	$sql_publishing="SELECT * FROM `publishing` WHERE `publishing_code`='".$row_product['pd_publishing']."'";
	$rs_publishing=@mysql_query($sql_publishing);
	$row_publishing=@mysql_fetch_array($rs_publishing,MYSQL_ASSOC);
	

	$excel_array[$J][] = $order_md; //訂單日期
	$excel_array[$J][] = $order_his; //訂單時間
	$excel_array[$J][] = $order_num; //編號
	$excel_array[$J][] = $order_pay_mode; //付款方式
	$excel_array[$J][] = $row_product['bar_code']; //書號
	$excel_array[$J][] = $pd_name; //產品名稱
	
	$excel_array[$J][] = $row_publishing['name']; //出版社
	$excel_array[$J][] = $pd_price; //銷售價
	$excel_array[$J][] = $pd_buy_num; //數量
	$excel_array[$J][] = $pd_price*$pd_buy_num; //小計
	
	$excel_array[$J][] = $row_order['invoice_name']; //收件人姓名
	$excel_array[$J][] = $row_order['invoice_tel']; //收件人電話
	$excel_array[$J][] = $order_addr; //收件人地址
	$excel_array[$J][] = $transportation_ps; //配送方式
	$excel_array[$J][] = $receipt_type; //發票類型
	$excel_array[$J][] = $receipt_title; //發票抬頭
	$excel_array[$J][] = $receipt_code; //發票統編
	$excel_array[$J][] = $ps; //訂單備註

	$J++;
	}
		if(@$order_all_list['addpd'][$key_no]!=''){
        $addpd_array= $order_all_list['addpd'][$key_no];
        for( $A=0; $A < @sizeof($addpd_array); $A++ ){
					$key_no2=@key($addpd_array); 

					$addpd_name= $addpd_array["$key_no2"]['addpd_name'];
					$addpd_price= $addpd_array["$key_no2"]['addpd_price'];
					$pd_buy_num= $addpd_array["$key_no2"]['pd_buy_num'];
					$product_num= $addpd_array["$key_no2"]['product_num'];

					$order_book= $order_book+$pd_buy_num;

					$sql_product="SELECT * FROM `product` WHERE `product_num`='$product_num'";
					$rs_product=@mysql_query($sql_product);
					$row_product=@mysql_fetch_array($rs_product,MYSQL_ASSOC);

					$sql_publishing="SELECT * FROM `publishing` WHERE `publishing_code`='".$row_product['pd_publishing']."'";
					$rs_publishing=@mysql_query($sql_publishing);
					$row_publishing=@mysql_fetch_array($rs_publishing,MYSQL_ASSOC);


					$excel_array[$J][] = $order_md; //訂單日期
					$excel_array[$J][] = $order_his; //訂單時間
					$excel_array[$J][] = $order_num; //編號
					$excel_array[$J][] = $order_pay_mode; //付款方式
					$excel_array[$J][] = $row_product['bar_code']; //書號
					$excel_array[$J][] ='(單品加價)'.$addpd_name; //產品名稱

					$excel_array[$J][] = $row_publishing['name']; //出版社
					$excel_array[$J][] = $addpd_price; //銷售價
					$excel_array[$J][] = $pd_buy_num; //數量
					$excel_array[$J][] = $addpd_price; //小計

					$excel_array[$J][] = $row_order['invoice_name']; //收件人姓名
					$excel_array[$J][] = $row_order['invoice_tel']; //收件人電話
					$excel_array[$J][] = $order_addr; //收件人地址
					$excel_array[$J][] = $transportation_ps; //配送方式
					$excel_array[$J][] = $receipt_type; //發票類型
					$excel_array[$J][] = $receipt_title; //發票抬頭
					$excel_array[$J][] = $receipt_code; //發票統編
					$excel_array[$J][] = $ps; //訂單備註


					@next($addpd_array);
					$J++;
	
		}
		  	  

	}
	@next($order_all_list['order_list']);
	} 
	
	
	/*----------------------------   商品特賣   -------------------------------*/


    for( $I=0; $I < (sizeof($order_all_list['sp_sell'])); $I++ ){
    $key_no=@key($order_all_list['sp_sell']);  
    if($key_no!=''){
    //echo '$key_no='.$key_no;
			$pd_name=@$order_all_list['sp_sell']["$key_no"]['pd_name'];
			$pd_price=@$order_all_list['sp_sell']["$key_no"]['pd_price'];
			$pd_buy_num=@$order_all_list['sp_sell']["$key_no"]['pd_buy_num'];
			$product_num=@$order_all_list['sp_sell']["$key_no"]['product_num'];
			$order_book= $order_book+$pd_buy_num;

			$sql_product="SELECT * FROM `product` WHERE `product_num`='$product_num'";
			$rs_product=@mysql_query($sql_product);
			$row_product=@mysql_fetch_array($rs_product,MYSQL_ASSOC);

			$sql_publishing="SELECT * FROM `publishing` WHERE `publishing_code`='".$row_product['pd_publishing']."'";
			$rs_publishing=@mysql_query($sql_publishing);
			$row_publishing=@mysql_fetch_array($rs_publishing,MYSQL_ASSOC);


			$excel_array[$J][] = $order_md; //訂單日期
			$excel_array[$J][] = $order_his; //訂單時間
			$excel_array[$J][] = $order_num; //編號
			$excel_array[$J][] = $order_pay_mode; //付款方式
			$excel_array[$J][] = $row_product['bar_code']; //書號
			$excel_array[$J][] ='(特價活動)'.$pd_name; //產品名稱

			$excel_array[$J][] = $row_publishing['name']; //出版社
			$excel_array[$J][] = $pd_price; //銷售價
			$excel_array[$J][] = $pd_buy_num; //數量
			$excel_array[$J][] = $pd_price*$pd_buy_num; //小計

			$excel_array[$J][] = $row_order['invoice_name']; //收件人姓名
			$excel_array[$J][] = $row_order['invoice_tel']; //收件人電話
			$excel_array[$J][] = $order_addr; //收件人地址
			$excel_array[$J][] = $transportation_ps; //配送方式
			$excel_array[$J][] = $receipt_type; //發票類型
			$excel_array[$J][] = $receipt_title; //發票抬頭
			$excel_array[$J][] = $receipt_code; //發票統編
			$excel_array[$J][] = $ps; //訂單備註

	$J++;
	}
	@next($order_all_list['sp_sell']);
    }

	
	/*--------------------------- 商品特賣 END --------------------------------*/
	
	
	
	/*----------------------------   幾本幾折   -------------------------------*/
	 
    if($order_all_list['act_sell_info']!=''){ 

	for( $I=0; $I <(@sizeof($order_all_list['act_sell_info'])); $I++ ){
	$act_sell_code=@key($order_all_list['act_sell_info']); 


		if($act_sell_code!=''){
			for( $K=0; $K < @sizeof($order_all_list['num_sell'][$act_sell_code]); $K++ ){ 
			$product_code=@key($order_all_list['num_sell'][$act_sell_code]);
				if($product_code!=''){
				$pd_name=@$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['pd_name']; 
							
	 $num_discount=@$order_all_list['act_sell_info'][$act_sell_code]['num_discount'];
		 
		 $book_num=@$order_all_list['act_sell_info'][$act_sell_code]['book_num'];
		 			
	if ($book_num<$num_discount[0])
		{$pd_price=@$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['mem_price'];}else 
		{$pd_price=@$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['pd_price'];}			


		$pd_buy_num=@$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['pd_buy_num']; 
		$product_num=@$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['product_num']; ;
		$order_book= $order_book+$pd_buy_num;

		$sql_product="SELECT * FROM `product` WHERE `product_num`='$product_num'";
		$rs_product=@mysql_query($sql_product);
		$row_product=@mysql_fetch_array($rs_product,MYSQL_ASSOC);

		$sql_publishing="SELECT * FROM `publishing` WHERE `publishing_code`='".$row_product['pd_publishing']."'";
		$rs_publishing=@mysql_query($sql_publishing);
		$row_publishing=@mysql_fetch_array($rs_publishing,MYSQL_ASSOC);

		$excel_array[$J][] = $order_md; //訂單日期
		$excel_array[$J][] = $order_his; //訂單時間
		$excel_array[$J][] = $order_num; //編號
		$excel_array[$J][] = $order_pay_mode; //付款方式
		$excel_array[$J][] = $row_product['bar_code']; //書號
		$excel_array[$J][] ='(幾本幾折)'.$pd_name; //產品名稱

		$excel_array[$J][] = $row_publishing['name']; //出版社
		$excel_array[$J][] = $pd_price; //銷售價
		$excel_array[$J][] = $pd_buy_num; //數量
		$excel_array[$J][] = $pd_price*$pd_buy_num; //小計

		$excel_array[$J][] = $row_order['invoice_name']; //收件人姓名
		$excel_array[$J][] = $row_order['invoice_tel']; //收件人電話
		$excel_array[$J][] = $order_addr; //收件人地址
		$excel_array[$J][] = $transportation_ps; //配送方式
		$excel_array[$J][] = $receipt_type; //發票類型
		$excel_array[$J][] = $receipt_title; //發票抬頭
		$excel_array[$J][] = $receipt_code; //發票統編
		$excel_array[$J][] = $ps; //訂單備註

		$J++;
		}
		@next($order_all_list['num_sell'][$act_sell_code]);
		}
		}

		$excel_array[$J][0] = $order_md; //訂單日期
		$excel_array[$J][1] = $order_his; //訂單時間
		$excel_array[$J][2] = $order_num; //編號
		$excel_array[$J][3] = $order_pay_mode; //付款方式
		$excel_array[$J][4] = '9020000003000'; //編號
		$excel_array[$J][5] = '幾本幾折折扣-'.@$order_all_list['act_sell_info'][$act_sell_code]['act_sell_name']; ;  
		$excel_array[$J][7] = '-'.@$order_all_list['act_sell_info'][$act_sell_code]['discount_money'];  	
		$excel_array[$J][8] = 1;  
		$excel_array[$J][9] = '-'.@$order_all_list['act_sell_info'][$act_sell_code]['discount_money'];  
		$J++;
		@next($order_all_list['act_sell_info']);
    }
	}


	/*--------------------------- 幾本幾折 END --------------------------------*/


	
	/*----------------------------    滿額贈品    -------------------------------*/
	
	
	if($order_all_list['order_addpd']!=''){
		$sql_product="SELECT * FROM `product` WHERE `product_num` IN ('".join("','",explode(",",$order_all_list['order_addpd']))."') AND `ishow`='1'";
		//echo"$sql_product";
		$rs_product=mysql_query($sql_product);
		$num_product=mysql_num_rows($rs_product);
		$order_book= $order_book+$num_product;
		while($row_product=mysql_fetch_array($rs_product,MYSQL_ASSOC)){  
		
			$pd_name=@$row_product['name']; 
			$pd_price=0; 
			$pd_buy_num=1; 
			$product_num= $row_product['product_num']; ;
					
			$sql_product2="SELECT * FROM `product` WHERE `product_num`='$product_num'";
			$rs_product2=@mysql_query($sql_product2);
			$row_product2=@mysql_fetch_array($rs_product2,MYSQL_ASSOC);

			$sql_publishing="SELECT * FROM `publishing` WHERE `publishing_code`='".$row_product['pd_publishing']."'";
			$rs_publishing=@mysql_query($sql_publishing);
			$row_publishing=@mysql_fetch_array($rs_publishing,MYSQL_ASSOC);

					
					
			$excel_array[$J][] = $order_md; //訂單日期
			$excel_array[$J][] = $order_his; //訂單時間
			$excel_array[$J][] = $order_num; //編號
			$excel_array[$J][] = $order_pay_mode; //付款方式
			$excel_array[$J][] = $row_product['bar_code']; //書號
			$excel_array[$J][] ='(滿額贈品)'.$pd_name; //產品名稱

			$excel_array[$J][] = $row_publishing['name']; //出版社
			$excel_array[$J][] = $pd_price; //銷售價
			$excel_array[$J][] = $pd_buy_num; //數量
			$excel_array[$J][] = $pd_price*$pd_buy_num; //小計

			$excel_array[$J][] = $row_order['invoice_name']; //收件人姓名
			$excel_array[$J][] = $row_order['invoice_tel']; //收件人電話
			$excel_array[$J][] = $order_addr; //收件人地址
			$excel_array[$J][] = $transportation_ps; //配送方式
			$excel_array[$J][] = $receipt_type; //發票類型
			$excel_array[$J][] = $receipt_title; //發票抬頭
			$excel_array[$J][] = $receipt_code; //發票統編
			$excel_array[$J][] = $ps; //訂單備註
			$J++;
		
		}
	}
	
	/*---------------------------  滿額贈品 END  --------------------------------*/
	
	
	/*----------------------------    滿額加價購    -------------------------------*/
	
	
	if($order_all_list['m_addpd']!=''){
		
		for( $I=0; $I < @sizeof($order_all_list['m_addpd']); $I++ ){ 
		$key=@key($order_all_list['m_addpd']);
		
		$pd_name=@$order_all_list['m_addpd']["$key"]['name']; 
		$pd_price=@$order_all_list['m_addpd']["$key"]['price']; 
		$pd_buy_num=@$order_all_list['m_addpd']["$key"]['pd_buy_num'];
		$product_num=@$order_all_list['m_addpd']["$key"]['product_num'];
		$order_book= $order_book+$pd_buy_num;

		$sql_product="SELECT * FROM `product` WHERE `product_num`='$product_num'";
		$rs_product=@mysql_query($sql_product);
		$row_product=@mysql_fetch_array($rs_product,MYSQL_ASSOC);

		$sql_publishing="SELECT * FROM `publishing` WHERE `publishing_code`='".$row_product['pd_publishing']."'";
		$rs_publishing=@mysql_query($sql_publishing);
		$row_publishing=@mysql_fetch_array($rs_publishing,MYSQL_ASSOC);


		$excel_array[$J][] = $order_md; //訂單日期
		$excel_array[$J][] = $order_his; //訂單時間
		$excel_array[$J][] = $order_num; //編號
		$excel_array[$J][] = $order_pay_mode; //付款方式
		$excel_array[$J][] = $row_product['bar_code']; //書號
		$excel_array[$J][] ='(滿額加購)'.$pd_name; //產品名稱

		$excel_array[$J][] = $row_publishing['name']; //出版社
		$excel_array[$J][] = $pd_price; //銷售價
		$excel_array[$J][] = $pd_buy_num; //數量
		$excel_array[$J][] = $pd_price*$pd_buy_num; //小計

		$excel_array[$J][] = $row_order['invoice_name']; //收件人姓名
		$excel_array[$J][] = $row_order['invoice_tel']; //收件人電話
		$excel_array[$J][] = $order_addr; //收件人地址
		$excel_array[$J][] = $transportation_ps; //配送方式
		$excel_array[$J][] = $receipt_type; //發票類型
		$excel_array[$J][] = $receipt_title; //發票抬頭
		$excel_array[$J][] = $receipt_code; //發票統編
		$excel_array[$J][] = $ps; //訂單備註
		$J++;
		@next($order_all_list['m_addpd']);
		}
		
	}
	
	/*---------------------------  滿額贈品 END  --------------------------------*/
	
	
	

    
    
	/*     
	if($order_all_list['act_sell_info']!=''){ 
		@reset($order_all_list['act_sell_info']);
		for( $I=0; $I <(@sizeof($order_all_list['act_sell_info'])); $I++ ){
		$act_sell_code=@key($order_all_list['act_sell_info']);  
		$excel_array[$J][7] ='幾本幾折折扣-'.@$order_all_list['act_sell_info'][$act_sell_code]['act_sell_name']; ;  
		$excel_array[$J][8] ='-'.@$order_all_list['act_sell_info'][$act_sell_code]['discount_money'];  
		$J++;
		@next($order_all_list['act_sell_info']);
		}
    }	 
	*/
	
	

	
	if($row_order['pay_mode_price']!='' && $row_order['pay_mode_price']!=0){
	$excel_array[$J][0] = $order_md; //訂單日期
	$excel_array[$J][1] = $order_his; //訂單時間
	$excel_array[$J][2] = $order_num; //編號
	$excel_array[$J][3] = $order_pay_mode; //付款方式
	$excel_array[$J][4] = '9020000001006'; //編號
	$excel_array[$J][5] ='超商付款手續費'; 
	$excel_array[$J][7] = $row_order['pay_mode_price']; 	 	
	$excel_array[$J][8] = 1; 
	$excel_array[$J][9] = $row_order['pay_mode_price']; 	
	$J++;
	}

	if($row_order['transportation']!='' && $row_order['transportation']!=0){
	$excel_array[$J][0] = $order_md; //訂單日期
	$excel_array[$J][1] = $order_his; //訂單時間
	$excel_array[$J][2] = $order_num; //編號
	$excel_array[$J][3] = $order_pay_mode; //付款方式
	$excel_array[$J][4] = '9020000001006'; //編號
	$excel_array[$J][5] ='運費';  
	$excel_array[$J][7] = $row_order['transportation']; 		
	$excel_array[$J][8] = 1; 
	$excel_array[$J][9] = $row_order['transportation']; 	
	$J++;
	}

	if($row_order['other_price']!='' && $row_order['other_price']!=0){
	$excel_array[$J][0] = $order_md; //訂單日期
	$excel_array[$J][1] = $order_his; //訂單時間
	$excel_array[$J][2] = $order_num; //編號
	$excel_array[$J][6] = $order_pay_mode; //付款方式
	$excel_array[$J][5] ='其他費用-'.@$row_order['other_ps'];
	$excel_array[$J][7] = $row_order['other_price']; 	
	$excel_array[$J][8] = 1; 	
	$excel_array[$J][9] = $row_order['other_price'];  
	$J++;
	}

	

	if($order_all_list['promo']['promo_total']!='' && $order_all_list['promo']['promo_total']!=0){
	$excel_array[$J][0] = $order_md; //訂單日期
	$excel_array[$J][1] = $order_his; //訂單時間
	$excel_array[$J][2] = $order_num; //編號
	$excel_array[$J][3] = $order_pay_mode; //付款方式
	$excel_array[$J][4] = '9020000003000'; //編號
	$excel_array[$J][5] ='活動-滿額折抵';  
	$excel_array[$J][7] = '-'.$order_all_list['promo']['promo_total'];  
	$excel_array[$J][8] = 1; 
	$excel_array[$J][9] = '-'.$order_all_list['promo']['promo_total'];  
	$J++;
	}

	if($order_all_list['gift_card']['gift_price']!='' && $order_all_list['gift_card']['gift_price']!=0){  
	$excel_array[$J][0] = $order_md; //訂單日期
	$excel_array[$J][1] = $order_his; //訂單時間
	$excel_array[$J][2] = $order_num; //編號
	$excel_array[$J][3] = $order_pay_mode; //付款方式
	$excel_array[$J][4] = '9020000003000'; //編號
	$excel_array[$J][5] ='折價券折抵-'.@$order_all_list['gift_card']['name'];  
	$excel_array[$J][7] = '-'.$order_all_list['gift_card']['gift_price'];  
	$excel_array[$J][8] = 1;  
	$excel_array[$J][9] = '-'.$order_all_list['gift_card']['gift_price'];  
	$J++;
	}
    
	if($row_order['total_amount']!='' && $row_order['total_amount']!=0){
	//$excel_array[$J][6] ='小計：';  
	$excel_array[$J][8] = $order_book;  
	$excel_array[$J][9] = $row_order['total_amount'];  
	$J++;	
	}
	/* 
	$J++;	
	$J++;
	 */
	} //while END
	
	$total= $J;
	//print_r($excel_array);
	
	if($test_mode==1){
		
		print_r($excel_array);
		echo'共有幾列='.sizeof($excel_array).'<br />';
	}

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	// 設置屬性
	$objPHPExcel->getProperties()->setCreator("測試作者")//作者
	->setLastModifiedBy("測試修改者")//最後修改者
	->setTitle("測試標題")//標題
	->setSubject("測試主旨")//主旨
	->setDescription("測試註解")//註解
	->setKeywords("測試標記")//標記
	->setCategory("測試類別"); //類別
	//Create a first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	
	$col_name_array=array(A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,AA,AB,AC,AD,AE,AF,AG,AH,AI,AJ,AK,AL,AM,AN,AO,AP,AQ,AL,AM,AN);
	//echo'<br /><br />';

	//print_r($col_name_array);
	$col_all_num=sizeof($excel_array['0']);
	
	
	//設定欄位寬度
	$col_width=11;
	for( $I=0; $I < $col_all_num; $I++ ){
	$objPHPExcel->getActiveSheet()->getColumnDimension($col_name_array[$I])->setWidth($col_width);
	}
	
	//echo'$col_all_num='.$col_all_num;
	//產生列
	
	//for( $J=1; $J <= sizeof($excel_array); $J++ ){
	for( $J=1; $J <= $total; $J++ ){
		for( $K=0; $K < $col_all_num; $K++ ){
		$type = PHPExcel_Cell_DataType::TYPE_STRING;
        //$objPHPExcel->getCellByColumnAndRow($col_name_array[$K], $J)->setValueExplicit($excel_array[$J][$K], $type);
		$type = PHPExcel_Cell_DataType::TYPE_STRING;
		$objPHPExcel->getActiveSheet()->getCell($col_name_array[$K]."$J")->setValueExplicit($excel_array[($J-1)][$K], $type);
		//$objPHPExcel->getActiveSheet()->setCellValue($col_name_array[$K]."$J", $excel_array[$J][$K]);
		}
	} 

	if($test_mode==0){ 
		//Excel檔名
		$filename = "order_".$no.".xls";
	
		//產生header
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename= $filename" );
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public"); 
	 
		//Save Excel 5 file 保存
		require_once('../../class/PHPExcel/Writer/Excel5.php');
		$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
		$objWriter->save($filename);
		
		//產生Excel下載檔
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); //20003格式
		
		$objWriter->save('php://output');

	}
	
	}else{
		echo"沒有符合的資料";	
    }
	
		

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>無標題文件</title>
</head>

<body>
</body>
</html>