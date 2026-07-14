<?php

ini_set("memory_limit", "100M");
set_time_limit(900);

require_once("../../config.php");
require_once("../../include/function.php");
require_once('../../class/PHPExcel.php');
require_once('../../class/PHPExcel/Writer/Excel5.php');
require_once('../../class/PHPExcel/IOFactory.php');


$start_time = $_REQUEST["start_time"];
$end_time = $_REQUEST["end_time"];
$sh_state = $_REQUEST["sh_state"];
$esend_array = array('0' => '不願意', '1' => '願意',);
$sql_member = "SELECT * FROM `member` ";


//echo"$sql_member";
$rs_member = mysql_query($sql_member);
$num_member = mysql_num_rows($rs_member);
//echo'num_member='."$num_member";
if ($num_member >= 1) {

  $excel_array[0]['0'] = '會員編號';
  $excel_array[0]['1'] = '姓名';
  $excel_array[0]['2'] = 'E-MAIL';
  $excel_array[0]['3'] = '生日';
  $excel_array[0]['4'] = '電子報';
  $J = 1;
  while ($row_member = mysql_fetch_array($rs_member, MYSQL_ASSOC)) {
    $excel_array[$J]['0'] = $row_member['no'];
    $excel_array[$J]['1'] = $row_member['name'];
    $excel_array[$J]['2'] = $row_member['email'];
    $excel_array[$J]['3'] = $row_member['birthday'];
    $excel_array[$J]['4'] = $esend_array[$row_member['esend']];
    $J++;
  }

  //print_r($excel_array);

  // Create new PHPExcel object
  $objPHPExcel = new PHPExcel();
  // 設置屬性
  $objPHPExcel->getProperties()->setCreator("測試作者") //作者
    ->setLastModifiedBy("測試修改者") //最後修改者
    ->setTitle("測試標題") //標題
    ->setSubject("測試主旨") //主旨
    ->setDescription("測試註解") //註解
    ->setKeywords("測試標記") //標記
    ->setCategory("測試類別"); //類別
  //Create a first sheet
  $objPHPExcel->setActiveSheetIndex(0);

  $col_name_array = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                          'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
  //echo'<br /><br />';

  //print_r($col_name_array);
  $col_all_num = sizeof($excel_array['0']);


  //設定欄位寬度
  $col_width = 30;
  for ($I = 0; $I < $col_all_num; $I++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($col_name_array[$I])->setWidth($col_width);
  }

  //echo'$col_all_num='.$col_all_num;
  //產生列
  for ($J = 1; $J <= sizeof($excel_array); $J++) {
    for ($K = 0; $K < $col_all_num; $K++) {
      $type = PHPExcel_Cell_DataType::TYPE_STRING;
      //$objPHPExcel->getCellByColumnAndRow($col_name_array[$K], $J)->setValueExplicit($excel_array[$J][$K], $type);
      $type = PHPExcel_Cell_DataType::TYPE_STRING;
      $objPHPExcel->getActiveSheet()->getCell($col_name_array[$K] . "$J")->setValueExplicit($excel_array[($J - 1)][$K], $type);
      //$objPHPExcel->getActiveSheet()->setCellValue($col_name_array[$K]."$J", $excel_array[$J][$K]);
    }
  }

  //Excel檔名
  $filename = "member.xls";

  //產生header
  header("Content-type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=$filename");
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
} else {
  echo "沒有符合的資料";
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