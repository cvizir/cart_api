<?php
ini_set("memory_limit", "256M");
set_time_limit(900);

require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once('../../class/PHPExcel.php');
require_once('../../class/PHPExcel/IOFactory.php');

$sh_txt      = isset($_REQUEST['sh_txt']) ? trim($_REQUEST['sh_txt']) : '';
$esend_array = array('0' => '不願意', '1' => '願意');

// 與 list.php 相同的搜尋條件
$sql    = "SELECT `no`, `name`, `email`, `birthday`, `esend` FROM `member`";
$params = array();
if ($sh_txt !== '') {
  $sql   .= " WHERE `name` LIKE ? OR `email` LIKE ?";
  $like   = '%' . $sh_txt . '%';
  $params = array($like, $like);
}
$sql .= " ORDER BY `no` DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($members) < 1) {
  header('Content-Type: text/html; charset=utf-8');
  echo "沒有符合的資料";
  exit;
}

// 組出二維陣列：第一列為標題
$excel_array   = array();
$excel_array[] = array('會員編號', '姓名', 'E-MAIL', '生日', '電子報');
foreach ($members as $row) {
  $esend = isset($esend_array[$row['esend']]) ? $esend_array[$row['esend']] : '';
  $excel_array[] = array($row['no'], $row['name'], $row['email'], $row['birthday'], $esend);
}

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("CART 後台")
  ->setLastModifiedBy("CART 後台")
  ->setTitle("會員資料")
  ->setSubject("會員資料")
  ->setDescription("會員匯出")
  ->setKeywords("member")
  ->setCategory("member");
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();

$col_all_num = count($excel_array[0]);

// 欄位寬度
for ($c = 0; $c < $col_all_num; $c++) {
  $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($c))->setWidth(30);
}

// 寫入資料（一律以字串寫入，避免 0 開頭資料被轉成數字）
foreach ($excel_array as $r => $line) {
  foreach ($line as $c => $value) {
    $cell = PHPExcel_Cell::stringFromColumnIndex($c) . ($r + 1);
    $sheet->setCellValueExplicit($cell, (string)$value, PHPExcel_Cell_DataType::TYPE_STRING);
  }
}

// 清掉前面可能的輸出，避免檔案損毀
while (ob_get_level() > 0) {
  ob_end_clean();
}

$filename = "member_" . date("Ymd_His") . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"{$filename}\"");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
