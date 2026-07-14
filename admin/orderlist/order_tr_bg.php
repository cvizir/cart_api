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

    $no=$_REQUEST['no'];
    $sql_order="SELECT * FROM `orderlist` WHERE `no`='$no'";
	//echo"$sql_order";
    $rs_order=mysql_query($sql_order);	
	$num_order=mysql_num_rows($rs_order);	
	//echo'num_order='."$num_order";
	if($num_order>=1){
	$excel_array[0][] ='訂單日期'; 
	$excel_array[0][] ='訂單編號'; 
	$excel_array[0][] ='付款方式'; 
	$excel_array[0][] ='書號'; 
	$excel_array[0][] ='產品名稱'; 
	
	$excel_array[0][] ='出版社'; 
	$excel_array[0][] ='銷售價'; 
	$excel_array[0][] ='數量'; 
	$excel_array[0][] ='小計'; 
	$excel_array[0][] ='重量'; 
	
	$excel_array[0][] ='收件人'; 
	$excel_array[0][] ='電話'; 
	$excel_array[0][] ='收件人地址'; 
	$excel_array[0][] ='配送方式'; 
	$excel_array[0][] ='統編'; 
	
	$excel_array[0][] ='購物車備註'; 

	

	
	
	$J=1;
    $row_order=mysql_fetch_array($rs_order,MYSQL_ASSOC);
	$order_all_list=json_decode($row_order['order_info'],true);	  
		  

	if($test_mode==1){
	print_r($row_order);	
	echo'<br /><br />';
	print_r($order_all_list);
	}
	
	
    for( $I=0; $I < (@sizeof($order_all_list['order_list'])); $I++ ){
    $key_no=@key($order_all_list['order_list']);  
    //echo '$key_no='.$key_no;
    if($key_no!=''){

    $pd_name=@$order_all_list['order_list']["$key_no"]['pd_name'];
	$pd_price=@$order_all_list['order_list']["$key_no"]['pd_price'];
	$pd_buy_num=@$order_all_list['order_list']["$key_no"]['pd_buy_num'];
	$product_num=@$order_all_list['order_list']["$key_no"]['product_num'];
	
	$sql_product="SELECT * FROM `product` WHERE `product_num`='$product_num'";
    $rs_product=@mysql_query($sql_product);
    $row_product=@mysql_fetch_array($rs_product,MYSQL_ASSOC);
	
	$sql_publishing="SELECT * FROM `publishing` WHERE `publishing_code`='".$row_product['pd_publishing']."'";
    $rs_publishing=@mysql_query($sql_publishing);
    $row_publishing=@mysql_fetch_array($rs_publishing,MYSQL_ASSOC);
	
	
	  
	$excel_array[$J][] =$row_order['buytime']; //訂單日期
	$excel_array[$J][] =$row_order['order_code']; //訂單編號
	$excel_array[$J][] =$row_order['pay_mode']; //付款方式
	$excel_array[$J][] =$row_product['bar_code']; //書號
	$excel_array[$J][] =$pd_name; //產品名稱
	
	$excel_array[$J][] =$row_publishing['name']; //出版社
	$excel_array[$J][] =$pd_price; //銷售價
	$excel_array[$J][] =$pd_buy_num; //數量
	$excel_array[$J][] =$pd_price*$pd_buy_num; //小計
	$excel_array[$J][] =$row_order['name']; //重量
	
	$excel_array[$J][] =$row_order['invoice_name']; //收件人
	$excel_array[$J][] =$row_order['invoice_tel']; //電話
	$excel_array[$J][] =$row_order['invoice_zipcode'].$row_order['invoice_city'].$row_order['invoice_area'].$row_order['invoice_addr']; //收件人地址
	$excel_array[$J][] =$row_order['name']; //配送方式
	$excel_array[$J][] =$row_order['invoice_num']; //統編
	
	$excel_array[$J][] =$row_order['ps']; //購物車備註



		if(@$order_all_list['addpd'][$key_no]!=''){
        $addpd_array=$order_all_list['addpd'][$key_no];
			for( $J=0; $J < @sizeof($addpd_array); $J++ ){
			$key_no2=@key($addpd_array); 
		
			$addpd_name='&nbsp;&nbsp;(加價購)-'.$addpd_array["$key_no2"]['addpd_name'];
			$addpd_price=$addpd_array["$key_no2"]['addpd_price'];
			$addpd_num=1;
	
			$excel_array[$J][] =$row_order['buytime']; //訂單日期
			$excel_array[$J][] =$row_order['order_code']; //訂單編號
			$excel_array[$J][] =$row_order['pay_mode']; //付款方式
			$excel_array[$J][] =$product_num; //書號
			$excel_array[$J][] =$addpd_name; //產品名稱
			
			$excel_array[$J][] =$row_order['name']; //出版社
			$excel_array[$J][] =$addpd_price; //銷售價
			$excel_array[$J][] =1; //數量
			$excel_array[$J][] =$addpd_price; //小計
			$excel_array[$J][] =$row_order['name']; //重量
			
			$excel_array[$J][] =$row_order['invoice_name']; //收件人
			$excel_array[$J][] =$row_order['invoice_tel']; //電話
			$excel_array[$J][] =$row_order['invoice_zipcode'].$row_order['invoice_city'].$row_order['invoice_area'].$row_order['invoice_addr']; //收件人地址
			$excel_array[$J][] =$row_order['name']; //配送方式
			$excel_array[$J][] =$row_order['invoice_num']; //統編
			
			$excel_array[$J][] =$row_order['ps']; //購物車備註
			
			
			@next($addpd_array);
			$J++;
			}
		}



			$addpd_name='&nbsp;&nbsp;(加價購)-'.$addpd_array["$key_no2"]['addpd_name'];
			$addpd_price=$addpd_array["$key_no2"]['addpd_price'];
			$addpd_num=1;
	
			$excel_array[$J][] =$row_order['buytime']; //訂單日期
			$excel_array[$J][] =$row_order['order_code']; //訂單編號
			$excel_array[$J][] =$row_order['pay_mode']; //付款方式
			$excel_array[$J][] =$product_num; //書號
			$excel_array[$J][] =$addpd_name; //產品名稱
			
			$excel_array[$J][] =$row_order['name']; //出版社
			$excel_array[$J][] =$addpd_price; //銷售價
			$excel_array[$J][] =1; //數量
			$excel_array[$J][] =$addpd_price; //小計
			$excel_array[$J][] =$row_order['name']; //重量
			
			$excel_array[$J][] =$row_order['invoice_name']; //收件人
			$excel_array[$J][] =$row_order['invoice_tel']; //電話
			$excel_array[$J][] =$row_order['invoice_zipcode'].$row_order['invoice_city'].$row_order['invoice_area'].$row_order['invoice_addr']; //收件人地址
			$excel_array[$J][] =$row_order['name']; //配送方式
			$excel_array[$J][] =$row_order['invoice_num']; //統編
			
			$excel_array[$J][] =$row_order['ps']; //購物車備註

		  	  
	$J++;
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
		  
	$excel_array[$J][] =$row_order['buytime']; //訂單日期
	$excel_array[$J][] =$row_order['order_code']; //訂單編號
	$excel_array[$J][] =$row_order['pay_mode']; //付款方式
	$excel_array[$J][] =$product_num; //書號
	$excel_array[$J][] =$pd_name; //產品名稱
	
	$excel_array[$J][] =$row_order['name']; //出版社
	$excel_array[$J][] =$pd_price; //銷售價
	$excel_array[$J][] =$pd_buy_num; //數量
	$excel_array[$J][] =$pd_price*$pd_buy_num; //小計
	$excel_array[$J][] =$row_order['name']; //重量
	
	$excel_array[$J][] =$row_order['invoice_name']; //收件人
	$excel_array[$J][] =$row_order['invoice_tel']; //電話
	$excel_array[$J][] =$row_order['invoice_zipcode'].$row_order['invoice_city'].$row_order['invoice_area'].$row_order['invoice_addr']; //收件人地址
	$excel_array[$J][] =$row_order['name']; //配送方式
	$excel_array[$J][] =$row_order['invoice_num']; //統編
	
	$excel_array[$J][] =$row_order['ps']; //購物車備註


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
			for( $J=0; $J < @sizeof($order_all_list['num_sell'][$act_sell_code]); $J++ ){ 
			$product_code=@key($order_all_list['num_sell'][$act_sell_code]);
				if($product_code!=''){
				$pd_name=@$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['pd_name']; 
				$pd_price=@$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['pd_price']; 
				$pd_buy_num=@$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['pd_buy_num']; 
				$product_num=@$order_all_list['num_sell']["$act_sell_code"]["$product_code"]['product_num']; ;
					  
				$excel_array[$J][] =$row_order['buytime']; //訂單日期
				$excel_array[$J][] =$row_order['order_code']; //訂單編號
				$excel_array[$J][] =$row_order['pay_mode']; //付款方式
				$excel_array[$J][] =$product_num; //書號
				$excel_array[$J][] =$pd_name; //產品名稱
				
				$excel_array[$J][] =$row_order['name']; //出版社
				$excel_array[$J][] =$pd_price; //銷售價
				$excel_array[$J][] =$pd_buy_num; //數量
				$excel_array[$J][] =$pd_price*$pd_buy_num; //小計
				$excel_array[$J][] =$row_order['name']; //重量
				
				$excel_array[$J][] =$row_order['invoice_name']; //收件人
				$excel_array[$J][] =$row_order['invoice_tel']; //電話
				$excel_array[$J][] =$row_order['invoice_zipcode'].$row_order['invoice_city'].$row_order['invoice_area'].$row_order['invoice_addr']; //收件人地址
				$excel_array[$J][] =$row_order['name']; //配送方式
				$excel_array[$J][] =$row_order['invoice_num']; //統編
				
				$excel_array[$J][] =$row_order['ps']; //購物車備註
			
				$J++;
				}
			@next($order_all_list['num_sell'][$act_sell_code]);
			}
		}


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
		while($row_product=mysql_fetch_array($rs_product,MYSQL_ASSOC)){  
		
				$pd_name=@$row_product['name']; 
				$pd_price=0; 
				$pd_buy_num=1; 
				$product_num=$row_product['product_num']; ;
					  
				$excel_array[$J][] =$row_order['buytime']; //訂單日期
				$excel_array[$J][] =$row_order['order_code']; //訂單編號
				$excel_array[$J][] =$row_order['pay_mode']; //付款方式
				$excel_array[$J][] =$product_num; //書號
				$excel_array[$J][] ='滿額贈品-'.$pd_name; //產品名稱
				
				$excel_array[$J][] =$row_order['name']; //出版社
				$excel_array[$J][] =$pd_price; //銷售價
				$excel_array[$J][] =$pd_buy_num; //數量
				$excel_array[$J][] =$pd_price*$pd_buy_num; //小計
				$excel_array[$J][] =$row_order['name']; //重量
				
				$excel_array[$J][] =$row_order['invoice_name']; //收件人
				$excel_array[$J][] =$row_order['invoice_tel']; //電話
				$excel_array[$J][] =$row_order['invoice_zipcode'].$row_order['invoice_city'].$row_order['invoice_area'].$row_order['invoice_addr']; //收件人地址
				$excel_array[$J][] =$row_order['name']; //配送方式
				$excel_array[$J][] =$row_order['invoice_num']; //統編
				
				$excel_array[$J][] =$row_order['ps']; //購物車備註
				$J++;
		
		}
	}
	
	/*---------------------------  滿額贈品 END  --------------------------------*/
	
	$excel_array[$J][7] ='訂單金額';  //小計
	$excel_array[$J][8] =$row_order['amount'];  //小計
	$J++;
	
	$excel_array[$J][7] ='超商付款手續費';  //小計
	$excel_array[$J][8] =$row_order['pay_mode_price'];  //小計
	$J++;

	$excel_array[$J][7] ='運費';  //小計
	$excel_array[$J][8] =$row_order['transportation'];  //小計
	$J++;

	$excel_array[$J][7] ='其他費用';  //小計
	$excel_array[$J][8] =$row_order['other_price'];  //小計
	$J++;

	$excel_array[$J][7] ='訂單金額';  //小計
	$excel_array[$J][8] =$row_order['total_amount'];  //小計
	$J++;	
	
	
		

	
	//print_r($excel_array);
	


	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	// 設置屬性
	$objPHPExcel->getProperties()->setCreator("測試作者")//作者
	->setLastModifiedBy("測試修改者")//最後修改者
	->setTitle("測試標題")//標題
	->setSubject("測試主旨")//主旨
	->setDescription("測試註解")//註解
	->setKeywords("測試標記")//標記
	->setCategory("測試類別");//類別
	//Create a first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	
	$col_name_array=array(A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,AA,AB,AC,AD,AE,AF,AG,AH,AI,AJ,AK,AL,AM,AN,AO,AP,AQ,AL,AM,AN);
	//echo'<br /><br />';

	//print_r($col_name_array);
	$col_all_num=sizeof($excel_array['0']);
	
	
	//設定欄位寬度
	$col_width=30;
	for( $I=0; $I < $col_all_num; $I++ ){
	$objPHPExcel->getActiveSheet()->getColumnDimension($col_name_array[$I])->setWidth($col_width);
	}
	
	//echo'$col_all_num='.$col_all_num;
	//產生列
	for( $J=1; $J <= sizeof($excel_array); $J++ ){
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
	header("Content-Disposition: attachment; filename=$filename" );
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	header("Pragma: public"); 
 
	//Save Excel 5 file 保存
	require_once('../../class/PHPExcel/Writer/Excel5.php');
	$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
	$objWriter->save($filename);
	
	//產生Excel下載檔
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');//20003格式
	
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