<?php
ini_set("memory_limit", "256M");
set_time_limit(900);

require_once("../../include/config.inc.php");
include_once("../session.php");     // 原本沒有載入，任何人只要知道網址就能下載全部訂單（含姓名、電話、地址）
require_once("../../include/function.php");
require_once('../../class/PHPExcel.php');
require_once('../../class/PHPExcel/IOFactory.php');

// 付款方式對照表：原本由舊的 config.php 提供，新的 config.inc.php 沒有，這裡補上預設值
if (!isset($pay_mode_array)) {
  $pay_mode_array = array('card' => '信用卡', 'atm' => 'ATM轉帳', 'store_pay' => '超商付款',
                          'line_pay' => 'Line Pay付款', 'post' => '郵政劃撥', 'foreign' => '國外訂購');
}

$start_time = isset($_REQUEST['start_time']) ? trim($_REQUEST['start_time']) : '';
$end_time   = isset($_REQUEST['end_time']) ? trim($_REQUEST['end_time']) : '';
$sh_state   = isset($_REQUEST['sh_state']) ? trim($_REQUEST['sh_state']) : '';

if ($start_time === '' || $end_time === '' || $sh_state === '') {
  header('Content-Type: text/html; charset=utf-8');
  echo "請選擇訂單狀態與日期區間";
  exit;
}

/*
 * 日期區間條件。原本是 uptime >= 開始 AND buytime <= 結束（開始用「最後修改時間」、結束用「訂單時間」），
 * 兩端用的不是同一個欄位，結果會漏單或多出單；這裡統一用訂單時間 buytime。
 */
$sql    = "SELECT * FROM `orderlist` WHERE `buytime` >= ? AND `buytime` <= ?";
$params = array($start_time, $end_time);
if ($sh_state !== 'all') {
  $sql     .= " AND `order_state` = ?";
  $params[] = $sh_state;
}
$sql .= " ORDER BY `buytime` ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($orders) < 1) {
  header('Content-Type: text/html; charset=utf-8');
  echo "沒有符合的資料";
  exit;
}

// 一次查出所有相關會員（原本每一筆訂單查一次）
$members = array();
$ids = array();
foreach ($orders as $o) {
  if ($o['member_no'] !== '' && $o['member_no'] !== null) { $ids[$o['member_no']] = true; }
}
if ($ids) {
  $idl  = array_keys($ids);
  $in   = implode(',', array_fill(0, count($idl), '?'));
  $stmt = $pdo->prepare("SELECT `no`, `mem_lv` FROM `member` WHERE `no` IN ({$in})");
  $stmt->execute($idl);
  foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $m) { $members[$m['no']] = $m; }
}

$excel_array   = array();
$excel_array[] = array('訂單狀態', '金額', '訂購人', '訂單編碼', '確認日期', '訂單日期',
                       '訂單號碼', '付款方式', '收件人', '電話', '送件地址', '配送方式/發票/訂單備註');

foreach ($orders as $row) {
  $mem_lv = isset($members[$row['member_no']]['mem_lv']) ? $members[$row['member_no']]['mem_lv'] : '';

  // 電話：手機與電話都有且不同時，用 && 連起來
  $mphone = isset($row['invoice_mphone']) ? $row['invoice_mphone'] : '';
  $tel    = $row['invoice_tel'];
  if ($mphone != "" && $tel != "") {
    $phone = ($mphone != $tel) ? $mphone . '&&' . $tel : $mphone;
  } else {
    $phone = $mphone . $tel;
  }

  // 配送方式 / 發票 / 備註（有內容才換行串接）
  $parts = array();
  if ($row['transportation_ps'] != "") { $parts[] = '配送:' . $row['transportation_ps']; }
  if ($row['invoice_type'] == 3)       { $parts[] = '統編：' . $row['invoice_num'] . "\n" . '公司：' . $row['invoice_title']; }
  if ($row['ps'] != "")                { $parts[] = '備註:' . $row['ps']; }

  $excel_array[] = array(
    $row['order_state'],
    $row['total_amount'],
    substr($mem_lv, -1) . '-W' . $row['member_no'],
    $row['order_code'],
    $row['uptime'],
    $row['buytime'],
    'C17' . sprintf("%06d", $row['no']),
    isset($pay_mode_array[$row['pay_mode']]) ? $pay_mode_array[$row['pay_mode']] : $row['pay_mode'],
    $row['invoice_name'],
    $phone,
    $row['invoice_zipcode'] . $row['invoice_city'] . $row['invoice_area'] . $row['invoice_addr'],
    implode("\n", $parts),
  );
}

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("CART 後台")
  ->setLastModifiedBy("CART 後台")
  ->setTitle("訂單彙整")
  ->setSubject("訂單彙整")
  ->setDescription("訂單彙整匯出")
  ->setKeywords("orderlist")
  ->setCategory("orderlist");
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();

$col_all_num = count($excel_array[0]);
for ($c = 0; $c < $col_all_num; $c++) {
  $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($c))->setWidth(10);
}

// 一律以字串寫入（原本的欄位名稱陣列少了引號、最後還重複了 AL/AM/AN，已改用函式產生欄位代號）
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

// 原本還會先把檔案存到伺服器上（$objWriter->save($filename)），而且檔尾多了一段空白 HTML 會被附加進 xls，都已移除
$filename = "orderlist_" . date("Ymd_His") . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"{$filename}\"");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
