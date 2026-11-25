<?php
ini_set("memory_limit", "256M");
set_time_limit(900);

require_once("../../include/config.inc.php");
include_once("../session.php");     // 原本沒有載入，任何人只要知道網址就能下載訂單明細（含姓名、電話、地址）
require_once("../../include/function.php");
require_once('../../class/PHPExcel.php');
require_once('../../class/PHPExcel/IOFactory.php');

// 付款方式對照表：原本由舊的 config.php 提供，新的 config.inc.php 沒有，這裡補上預設值
if (!isset($pay_mode_array)) {
  $pay_mode_array = array('card' => '信用卡', 'atm' => 'ATM轉帳', 'store_pay' => '超商付款',
                          'line_pay' => 'Line Pay付款', 'post' => '郵政劃撥', 'foreign' => '國外訂購');
}

/* ------------------------------ 小工具 ------------------------------ */

// 取陣列的值，不存在就回傳空字串
function g($arr, $key) {
  return (is_array($arr) && isset($arr[$key])) ? $arr[$key] : '';
}

// 數字相乘；不是數字就當 0
function mul($a, $b) {
  return (is_numeric($a) && is_numeric($b)) ? $a * $b : 0;
}

// 有金額（不是空白也不是 0）
function nz($v) {
  return is_numeric($v) && $v != 0;
}

// 商品、出版社查詢加上快取，同一個商品不會重複查詢（原本每一列都查兩次）
function getProduct($num) {
  global $pdo;
  static $cache = array();
  $num = (string)$num;
  if (!isset($cache[$num])) {
    $stmt = $pdo->prepare("SELECT * FROM `product` WHERE `product_num` = ?");
    $stmt->execute(array($num));
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    $cache[$num] = $r ? $r : array();
  }
  return $cache[$num];
}
function getPublisherName($code) {
  global $pdo;
  static $cache = array();
  $code = (string)$code;
  if ($code === '') { return ''; }
  if (!isset($cache[$code])) {
    $stmt = $pdo->prepare("SELECT `name` FROM `publishing` WHERE `publishing_code` = ?");
    $stmt->execute(array($code));
    $n = $stmt->fetchColumn();
    $cache[$code] = ($n === false) ? '' : $n;
  }
  return $cache[$code];
}

/**
 * 商品明細列（共 18 欄）
 * $base = [訂單日期, 訂單時間, 編號, 付款方式]，$tail = [收件人…備註] 共 8 欄
 */
function itemRow($base, $tail, $label, $product_num, $name, $price, $qty, $subtotal) {
  $p = getProduct($product_num);
  return array_merge(
    $base,
    array(
      isset($p['bar_code']) ? $p['bar_code'] : '',                                  // 書號
      $label . $name,                                                               // 產品名稱
      getPublisherName(isset($p['pd_publishing']) ? $p['pd_publishing'] : ''),      // 出版社
      $price, $qty, $subtotal
    ),
    $tail
  );
}

/** 費用 / 折扣列：只有前幾欄與金額欄有值，其餘補空白（共 18 欄） */
function sumRow($base, $code, $name, $price, $qty, $subtotal) {
  $row = array_pad($base, 18, '');
  $row[4] = $code;
  $row[5] = $name;
  $row[7] = $price;
  $row[8] = $qty;
  $row[9] = $subtotal;
  return $row;
}

/* ------------------------------ 查詢條件 ------------------------------ */

$no         = isset($_REQUEST['no']) ? trim($_REQUEST['no']) : '';
$start_time = isset($_REQUEST['start_time']) ? trim($_REQUEST['start_time']) : '';
$end_time   = isset($_REQUEST['end_time']) ? trim($_REQUEST['end_time']) : '';
$sh_state   = isset($_REQUEST['sh_state']) ? trim($_REQUEST['sh_state']) : '';

$conds  = array();
$params = array();
if ($no !== '') {
  $conds[]  = "`no` = ?";
  $params[] = $no;
} else {
  if ($start_time !== '') { $conds[] = "`buytime` >= ?"; $params[] = $start_time; }
  if ($end_time !== '')   { $conds[] = "`buytime` <= ?"; $params[] = $end_time; }
  // 原本選「全部」(all) 會被當成訂單狀態去查，結果一筆都查不到
  if ($sh_state !== '' && $sh_state !== 'all') { $conds[] = "`order_state` = ?"; $params[] = $sh_state; }
}
$sql = "SELECT * FROM `orderlist`" . ($conds ? " WHERE " . implode(" AND ", $conds) : "") . " ORDER BY `buytime` DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($orders) < 1) {
  header('Content-Type: text/html; charset=utf-8');
  echo "沒有符合的資料";
  exit;
}

/* ------------------------------ 組出明細資料 ------------------------------ */

$excel_array   = array();
$excel_array[] = array('訂單日期', '訂單時間', '編號', '付款方式', '書號', '產品名稱', '出版社', '銷售價', '數量', '小計',
                       '收件人姓名', '收件人電話', '收件人地址', '配送方式', '發票', '抬頭', '統編', '訂單備註');

foreach ($orders as $row_order) {
  $list = json_decode($row_order['order_info'], true);
  if (!is_array($list)) { $list = array(); }

  $order_num  = 'C17' . sprintf("%06d", $row_order['no']);
  $order_addr = $row_order['invoice_zipcode'] . ' ' . $row_order['invoice_city'] . $row_order['invoice_area'] . $row_order['invoice_addr'];
  $order_md   = substr($row_order['buytime'], -14, 5);   // 月-日
  $order_his  = substr($row_order['buytime'], -8);       // 時:分:秒
  $order_pay  = isset($pay_mode_array[$row_order['pay_mode']]) ? $pay_mode_array[$row_order['pay_mode']] : $row_order['pay_mode'];

  $transportation_ps = ($row_order['transportation_ps'] != "") ? '配送:' . $row_order['transportation_ps'] : '';
  // 原本的發票類型沒有預設值，沒填的訂單會沿用上一張訂單的值，這裡每張訂單都重設
  $receipt_type = '';
  if ($row_order['invoice_type'] == 2) { $receipt_type = '二聯'; }
  if ($row_order['invoice_type'] == 3) { $receipt_type = '三聯'; }
  $ps = ($row_order['ps'] != "") ? '備註:' . $row_order['ps'] : '';

  $base = array($order_md, $order_his, $order_num, $order_pay);
  $tail = array($row_order['invoice_name'], $row_order['invoice_tel'], $order_addr, $transportation_ps,
                $receipt_type, $row_order['invoice_title'], $row_order['invoice_num'], $ps);

  $order_book = 0;   // 本張訂單的總數量

  // 一般商品 + 單品加價購
  $order_list = g($list, 'order_list');
  if (is_array($order_list)) {
    foreach ($order_list as $key_no => $item) {
      $pd_buy_num = g($item, 'pd_buy_num');
      $pd_price   = g($item, 'pd_price');
      $order_book += (float)$pd_buy_num;
      $excel_array[] = itemRow($base, $tail, '', g($item, 'product_num'), g($item, 'pd_name'),
                               $pd_price, $pd_buy_num, mul($pd_price, $pd_buy_num));

      $addpd_array = isset($list['addpd'][$key_no]) ? $list['addpd'][$key_no] : array();
      if (is_array($addpd_array)) {
        foreach ($addpd_array as $addpd) {
          $a_num   = g($addpd, 'pd_buy_num');
          $a_price = g($addpd, 'addpd_price');
          $order_book += (float)$a_num;
          // 小計維持原本的算法：直接等於加價購單價
          $excel_array[] = itemRow($base, $tail, '(單品加價)', g($addpd, 'product_num'), g($addpd, 'addpd_name'),
                                   $a_price, $a_num, $a_price);
        }
      }
    }
  }

  // 商品特賣
  $sp_sell = g($list, 'sp_sell');
  if (is_array($sp_sell)) {
    foreach ($sp_sell as $item) {
      $pd_buy_num = g($item, 'pd_buy_num');
      $pd_price   = g($item, 'pd_price');
      $order_book += (float)$pd_buy_num;
      $excel_array[] = itemRow($base, $tail, '(特價活動)', g($item, 'product_num'), g($item, 'pd_name'),
                               $pd_price, $pd_buy_num, mul($pd_price, $pd_buy_num));
    }
  }

  // 幾本幾折
  $act_sell_info = g($list, 'act_sell_info');
  if (is_array($act_sell_info)) {
    foreach ($act_sell_info as $act_code => $info) {
      $num_sell     = (isset($list['num_sell'][$act_code]) && is_array($list['num_sell'][$act_code])) ? $list['num_sell'][$act_code] : array();
      $num_discount = g($info, 'num_discount');
      $first        = (is_array($num_discount) && isset($num_discount[0])) ? $num_discount[0] : 0;
      $book_num     = g($info, 'book_num');

      foreach ($num_sell as $item) {
        $pd_price   = ($book_num < $first) ? g($item, 'mem_price') : g($item, 'pd_price');
        $pd_buy_num = g($item, 'pd_buy_num');
        $order_book += (float)$pd_buy_num;
        $excel_array[] = itemRow($base, $tail, '(幾本幾折)', g($item, 'product_num'), g($item, 'pd_name'),
                                 $pd_price, $pd_buy_num, mul($pd_price, $pd_buy_num));
      }
      $discount = g($info, 'discount_money');
      $excel_array[] = sumRow($base, '9020000003000', '幾本幾折折扣-' . g($info, 'act_sell_name'), '-' . $discount, 1, '-' . $discount);
    }
  }

  // 滿額贈品
  $order_addpd = g($list, 'order_addpd');
  if ($order_addpd != '') {
    $nums = array_values(array_filter(array_map('trim', explode(",", $order_addpd)), 'strlen'));
    if ($nums) {
      $in   = implode(',', array_fill(0, count($nums), '?'));
      $stmt = $pdo->prepare("SELECT * FROM `product` WHERE `product_num` IN ({$in}) AND `ishow` = '1'");
      $stmt->execute($nums);
      $gifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $order_book += count($gifts);
      foreach ($gifts as $gift) {
        $excel_array[] = itemRow($base, $tail, '(滿額贈品)', $gift['product_num'], $gift['name'], 0, 1, 0);
      }
    }
  }

  // 滿額加價購
  $m_addpd = g($list, 'm_addpd');
  if (is_array($m_addpd)) {
    foreach ($m_addpd as $item) {
      $pd_price   = g($item, 'price');
      $pd_buy_num = g($item, 'pd_buy_num');
      $order_book += (float)$pd_buy_num;
      $excel_array[] = itemRow($base, $tail, '(滿額加購)', g($item, 'product_num'), g($item, 'name'),
                               $pd_price, $pd_buy_num, mul($pd_price, $pd_buy_num));
    }
  }

  // 各項費用與折抵
  if (nz($row_order['pay_mode_price'])) {
    $excel_array[] = sumRow($base, '9020000001006', '超商付款手續費', $row_order['pay_mode_price'], 1, $row_order['pay_mode_price']);
  }
  if (nz($row_order['transportation'])) {
    $excel_array[] = sumRow($base, '9020000001006', '運費', $row_order['transportation'], 1, $row_order['transportation']);
  }
  if (nz($row_order['other_price'])) {
    // 原本付款方式誤放在第 7 欄（出版社），這裡改放在第 4 欄與其他列一致
    $excel_array[] = sumRow($base, '', '其他費用-' . $row_order['other_ps'], $row_order['other_price'], 1, $row_order['other_price']);
  }
  $promo_total = isset($list['promo']['promo_total']) ? $list['promo']['promo_total'] : '';
  if (nz($promo_total)) {
    $excel_array[] = sumRow($base, '9020000003000', '活動-滿額折抵', '-' . $promo_total, 1, '-' . $promo_total);
  }
  $gift_price = isset($list['gift_card']['gift_price']) ? $list['gift_card']['gift_price'] : '';
  if (nz($gift_price)) {
    $gift_name = isset($list['gift_card']['name']) ? $list['gift_card']['name'] : '';
    $excel_array[] = sumRow($base, '9020000003000', '折價券折抵-' . $gift_name, '-' . $gift_price, 1, '-' . $gift_price);
  }

  // 訂單合計
  if (nz($row_order['total_amount'])) {
    $total_row    = array_fill(0, 18, '');
    $total_row[8] = $order_book;
    $total_row[9] = $row_order['total_amount'];
    $excel_array[] = $total_row;
  }
}

/* ------------------------------ 輸出 Excel ------------------------------ */

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("CART 後台")
  ->setLastModifiedBy("CART 後台")
  ->setTitle("訂單明細")
  ->setSubject("訂單明細")
  ->setDescription("訂單明細匯出")
  ->setKeywords("orderlist")
  ->setCategory("orderlist");
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();

$col_all_num = count($excel_array[0]);
for ($c = 0; $c < $col_all_num; $c++) {
  $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($c))->setWidth(11);
}

// 一律以字串寫入（欄位代號改用函式產生，原本的陣列少了引號、最後還重複了 AL/AM/AN）
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
$suffix   = ($no !== '') ? preg_replace('/[^0-9A-Za-z_-]/', '', $no) : date("Ymd_His");
$filename = "order_" . $suffix . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"{$filename}\"");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
