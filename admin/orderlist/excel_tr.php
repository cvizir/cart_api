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
	$trace=0;
	//print_r($_REQUEST);
	if($start_time!="" && $end_time!="" && $sh_state!=""){
		
   
    if($sh_state=='all'){ 
    	$sql_orderlist="SELECT * FROM `orderlist` WHERE `uptime`>='$start_time' AND `buytime`<='$end_time' ORDER BY buytime ASC ";
	}else{
		$sql_orderlist="SELECT * FROM `orderlist` WHERE `uptime`>='$start_time' AND `buytime`<='$end_time' AND `order_state`='$sh_state' ORDER BY buytime ASC ";	
	}

	
    $rs_orderlist=mysql_query($sql_orderlist);	
	$num_orderlist=mysql_num_rows($rs_orderlist);	
	
    if($trace==1){
	echo"1";	
	echo"$sql_orderlist";
	echo'num_orderlist='."$num_orderlist";
	}
	
	if($num_orderlist>=1){
		
	$excel_array[0]['0'] ='訂單狀態'; 
	$excel_array[0]['1'] ='金額'; 
	$excel_array[0]['2'] ='訂購人';
	$excel_array[0]['3'] ='訂單編碼';
	$excel_array[0]['4'] ='確認日期'; 
	$excel_array[0]['5'] ='訂單日期';
	$excel_array[0]['6'] ='訂單號碼'; 
	$excel_array[0]['7'] ='付款方式';
	$excel_array[0]['8'] ='收件人';
	$excel_array[0]['9'] ='電話'; 
	$excel_array[0]['10'] ='送件地址';
	$excel_array[0]['11'] ='配送方式/發票/訂單備註';

	$J=1;
    while($row_orderlist=mysql_fetch_array($rs_orderlist,MYSQL_ASSOC)){	
	
	$sql_member="SELECT * FROM `member` WHERE `no`='$row_orderlist[member_no]'";
	$rs_member=mysql_query($sql_member);
	$row_member=mysql_fetch_array($rs_member,MYSQL_ASSOC);
	
	
	 //echo '$pd_name='.$pd_name;
	  $excel_array[$J]['0'] =$row_orderlist['order_state']; 
	  $excel_array[$J]['1'] =$row_orderlist['total_amount']; 
	  $excel_array[$J]['2'] =substr($row_member['mem_lv'],-1).'-W'.$row_orderlist['member_no'];
	  $excel_array[$J]['3']=$row_orderlist['order_code'];
	  $excel_array[$J]['4'] =$row_orderlist['uptime'];
	  $excel_array[$J]['5'] =$row_orderlist['buytime'];
  	  $excel_array[$J]['6'] = 'C17'.sprintf("%06d",$row_orderlist['no']); 
      $excel_array[$J]['7'] =$pay_mode_array[$row_orderlist['pay_mode']];
      $excel_array[$J]['8'] =$row_orderlist['invoice_name'];
	  if($row_orderlist['invoice_mphone']!="" && $row_orderlist['invoice_tel']!=""){
			  if($row_orderlist['invoice_mphone']!=$row_orderlist['invoice_tel']){
				  $invoice_mphone=$row_orderlist['invoice_mphone'].'&&'.$row_orderlist['invoice_tel'];
			  }else{
				  $invoice_mphone=$row_orderlist['invoice_mphone'];
			  }
		  }else{
			  $invoice_mphone=$row_orderlist['invoice_mphone'].$row_orderlist['invoice_tel'];
	  }

	  $excel_array[$J]['9']=$invoice_mphone;
	  $excel_array[$J]['10'] =$row_orderlist['invoice_zipcode'].$row_orderlist['invoice_city'].$row_orderlist['invoice_area'].$row_orderlist['invoice_addr']; 
	 
	 if($row_orderlist['transportation_ps']!="" ){$transportation_ps='配送:'.$row_orderlist['transportation_ps'];}else{$transportation_ps='';} 
	 if($row_orderlist['transportation_ps']!=""&&$row_orderlist['invoice_type']==3 ){$n1="\n";}else{$n1='';} 
	 if($row_orderlist['invoice_type']==3 ){$invoice_type='統編：'.$row_orderlist['invoice_num']."\n".'公司：'.$row_orderlist['invoice_title'];}else{$invoice_type='';}
	 if($row_orderlist['transportation_ps']!=""&&$row_orderlist['ps']!="" ){$n2="\n";}else{$n2='';} 
	 if($row_orderlist['ps']!="" ){$ps='備註:'.$row_orderlist['ps'];}else{$ps='';} 
	  
	  $excel_array[$J]['11']=$transportation_ps.$n1.$invoice_type.$n2.$ps;
	
		  
	  $J++;

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
	$col_width=10;
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