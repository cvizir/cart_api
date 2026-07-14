<?php

	ini_set("memory_limit","100M");
	set_time_limit(900);


	
 	require_once("../../config.php");
	require_once("../../include/function.php");
	require_once('../../class/PHPExcel.php');
	require_once('../../class/PHPExcel/Writer/Excel5.php');
	require_once('../../class/PHPExcel/IOFactory.php');
	
	
	$start_time = $_REQUEST["start_time"];
	$end_time = $_REQUEST["end_time"];
	$sh_state = $_REQUEST["sh_state"];
	
	if($start_time!="" && $end_time!="" && $sh_state!=""){
		
   
    if($sh_state=='all'){ 
    	$sql_orderlist="SELECT * FROM `orderlist` WHERE `buytime`>='$start_time' AND `buytime`<='$end_time' ORDER BY buytime ASC ";
	}else{
		$sql_orderlist="SELECT * FROM `orderlist` WHERE `buytime`>='$start_time' AND `buytime`<='$end_time' AND `order_state`='$sh_state' ORDER BY buytime ASC ";	
	}
    
	//echo"$sql_orderlist";
    $rs_orderlist=mysql_query($sql_orderlist);	
	$num_orderlist=mysql_num_rows($rs_orderlist);	
	//echo'num_orderlist='."$num_orderlist";
	if($num_orderlist>=1){
		
	$excel_array[0]['0'] ='購買日期'; 
	$excel_array[0]['1'] ='訂單狀態'; 
	$excel_array[0]['2'] ='訂單編號'; 
	$excel_array[0]['3'] ='顧客姓名'; 
	$excel_array[0]['4'] ='送件地址'; 
	$excel_array[0]['5'] ='電話';
	$excel_array[0]['6'] ='Item'; 
	$excel_array[0]['7'] ='價格'; 
	$excel_array[0]['8'] ='訂單備註'; 

	$J=1;
    while($row_orderlist=mysql_fetch_array($rs_orderlist,MYSQL_ASSOC)){  
 		  $order_array=json_decode($row_orderlist['order_info'],true);
		  //echo sizeof($order_array);
		  //print_r($order_array);
		  for( $I=1; $I <= (sizeof($order_array)); $I++ ){
		  $key_no=key($order_array);  
		  
		  //echo '$key_no='.$key_no;
		  $pd_name=$order_array["$key_no"]['pd_name'].' '.$order_array["$key_no"]['pd_size_name'].' '.$order_array["$key_no"]['pd_color_name'];
		  $pd_num=$order_array["$key_no"]['pd_num'];
		  
		  $excel_array[$J]['0'] =$row_orderlist['buytime']; 
		  $excel_array[$J]['1'] =$row_orderlist['order_state']; 
		  $excel_array[$J]['2'] =$row_orderlist['order_code']; 
		  $excel_array[$J]['3'] =$row_orderlist['receives_name']; 
		  $excel_array[$J]['4'] =$row_orderlist['receives_country'].$row_orderlist['receives_zipcode'].$row_orderlist['receives_city'].$row_orderlist['receives_area'].$row_orderlist['receives_addr']; 
		  if($row_orderlist['receives_mphone']!="" && $row_orderlist['receives_tel']!=""){
				  if($row_orderlist['receives_mphone']!=$row_orderlist['receives_tel']){
					  $receives_mphone=$row_orderlist['receives_mphone'].'&&'.$row_orderlist['receives_tel'];
				  }else{
					  $receives_mphone=$row_orderlist['receives_mphone'];
				  }
			  }else{
				  $receives_mphone=$row_orderlist['receives_mphone'].$row_orderlist['receives_tel'];
		  }
		  $excel_array[$J]['5'] =$receives_mphone;
		  $excel_array[$J]['6'] =$pd_name.' x '.$pd_num; 
		  $excel_array[$J]['7'] =$row_orderlist['total_amount']; 
		  $excel_array[$J]['8'] =$row_orderlist['order_ps']; 
		  $J++;
		  next($order_array);
		  } 


	} 
	


	
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

	//Excel檔名
	$filename = "orderlist.xls";

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


	}else{
		echo"沒有符合的資料";	
    }
	
		
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