<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

@header("Content-Type: text/html; charset=utf-8");
// 原本這裡還有一行 require_once("../../config.php")（舊版設定檔，會重複定義並連到舊的資料庫），已移除

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

/* ------------------------------ 小工具 ------------------------------ */

function g($arr, $key) {
  return (is_array($arr) && isset($arr[$key])) ? $arr[$key] : '';
}

function mul($a, $b) {
  return (is_numeric($a) && is_numeric($b)) ? $a * $b : 0;
}

/** 依「逗號分隔的商品編號」查出商品（原本用拼字串的 IN 查詢，而且每個商品還多查了一次用不到的出版社） */
function getProducts($csv, $only_show = false) {
  global $pdo;
  $nums = array_values(array_filter(array_map('trim', explode(",", (string)$csv)), 'strlen'));
  if (!$nums) { return array(); }
  $in  = implode(',', array_fill(0, count($nums), '?'));
  $sql = "SELECT * FROM `product` WHERE `product_num` IN ({$in})" . ($only_show ? " AND `ishow` = '1'" : "");
  $stmt = $pdo->prepare($sql);
  $stmt->execute($nums);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$cell_style = 'border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted;';

/** 輸出一列商品明細。$desc_html 已經是安全的 HTML */
function invRow($barcode, $desc_html, $price_html, $qty, $total, $desc_color = '', $price_color = '') {
  global $cell_style;
  echo '<tr>';
  echo '<td style="' . $cell_style . '">' . h($barcode) . '</td>';
  echo '<td height="24" style="' . $cell_style . ($desc_color ? ' color:' . $desc_color . ';' : '') . '">' . $desc_html . '</td>';
  echo '<td align="right" style="' . $cell_style . ($price_color ? ' color:' . $price_color . ';' : '') . '">' . $price_html . '</td>';
  echo '<td align="right" style="' . $cell_style . '">' . h($qty) . '</td>';
  echo '<td align="right" style="' . $cell_style . '">' . h($total) . '</td>';
  echo '</tr>' . "\n";
}

/* ------------------------------ 讀取訂單 ------------------------------ */

$order_code = isset($_REQUEST['order_code']) ? trim($_REQUEST['order_code']) : '';
$row_order  = false;

if ($order_code !== '' && preg_match('/^[0-9A-Za-z]+$/', $order_code)) {
  $stmt = $pdo->prepare("SELECT * FROM `orderlist` WHERE `order_code` = ?");
  $stmt->execute(array($order_code));
  $row_order = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$row_order) {
  echo '找不到這筆訂單';
  exit;
}

$order_all_list = json_decode($row_order['order_info'], true);
if (!is_array($order_all_list)) { $order_all_list = array(); }

if ($row_order['invoice_country'] == 'tw') {
  $addr = $row_order['invoice_zipcode'] . ' ' . $row_order['invoice_city'] . $row_order['invoice_area'] . $row_order['invoice_addr'];
} else {
  $addr = $row_order['invoice_addr'];
}
?>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="5%" align="left" valign="middle"><img src="http://shopping.windmill.com.tw/imgs/logo.jpg" alt="風車圖書" width="40" height="56" style="border:0; display:block" /></td>
    <td width="69%" height="60" align="left" valign="middle"><span style="font-size: 24px; line-height: 10px; font-family: 'Arial Unicode MS'; color: #366095;"><b>SAN HUEI PUBLISHING CO.,LTD</b></span><br /></td>
    <td width="26%" align="right" style="font-size: 34px; font-family: 'Arial Unicode MS'; color: #95B3D7; font-weight: bold;"><p><b>INVOICE</b></p></td>
  </tr>
</table>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="22" colspan="3" align="left" valign="middle" style="font-size: 14px; font-family: 'Arial Unicode MS';">No.23-1, Ln. 392, Fude 1st Rd., Xizhi Dist., New Taipei City 221, Taiwan (R.O.C.)</td>
    <td width="109" align="left" valign="middle" style="padding-left:10px;font-size: 14px; font-family: 'Arial Unicode MS';">&nbsp;</td>
  </tr>
  <tr>
    <td height="22" colspan="2" align="left" valign="middle" style="font-size: 14px; font-family: 'Arial Unicode MS';">Tel : +886-2-2695-9502</td>
    <td width="109" align="right" valign="middle" style="padding-right:3px;font-size: 14px; font-family: 'Arial Unicode MS';">DATE:</td>
    <td width="109" align="left" valign="middle" style="padding-left:3px; font-size: 14px; font-family: 'Arial Unicode MS'; border:1px #eaeaea solid;"><?php echo h(substr($row_order['buytime'], 0, 10)); ?></td>
  </tr>
  <tr>
    <td height="22" colspan="2" align="left" valign="middle" style="font-size: 14px; font-family: 'Arial Unicode MS';">Fax: +886-2-2695-9510</td>
    <td width="109" align="right" valign="middle" style="padding-right:3px;font-size: 14px; font-family: 'Arial Unicode MS';">INVOICE #:</td>
    <td width="109" align="left" valign="middle" style="padding-left:3px; font-size: 14px; font-family: 'Arial Unicode MS'; border:1px #eaeaea solid;"><?php echo 'C17' . sprintf("%06d", $row_order['no']); ?></td>
  </tr>
  <tr>
    <td height="22" colspan="2" align="left" valign="middle" style="font-size: 14px; font-family: 'Arial Unicode MS';">Website: http://shopping.windmill.com.tw</td>
    <td width="109" align="right" valign="middle" style="padding-right:3px;font-size: 14px; font-family: 'Arial Unicode MS';">CUSTOMER ID:</td>
    <td width="109" align="left" valign="middle" style="padding-left:3px; font-size: 14px; font-family: 'Arial Unicode MS'; border:1px #eaeaea solid;"><?php echo 'W' . h($row_order['member_no']); /* 原本是 sprintf($member_no)，把資料當成格式字串，含 % 的資料會出錯 */ ?></td>
  </tr>
  <tr>
    <td height="24" colspan="2" align="left" valign="middle" bgcolor="#4F81BD" style="font-size: 14px; font-family: 'Arial Unicode MS'; color: #FFF; font-weight: bold; border:1px #eaeaea solid;">BILL TO</td>
    <td align="left" valign="middle" style="padding-left:10px;font-size: 14px; font-family: 'Arial Unicode MS';">&nbsp;</td>
    <td align="left" valign="middle" bgcolor="#DCE6F1" style="padding-left:10px;font-size: 14px; font-family: 'Arial Unicode MS'; border:1px #eaeaea solid;">&nbsp;</td>
  </tr>
  <tr>
    <td width="380" height="24" align="left" valign="middle" style="font-size: 15px;">Name:<span style="padding-left: 5px;"><?php echo h($row_order['invoice_name']); ?></span></td>
    <td width="202" align="left" valign="middle" style="font-size: 15px; font-family: 'Arial Unicode MS';">TEL:<span style="padding-left:5px"><?php echo h($row_order['invoice_tel']); ?></span></td>
    <td align="left" valign="middle" style="padding-left:10px;font-size: 12px; font-family: 'Arial Unicode MS';">&nbsp;</td>
    <td align="left" valign="middle" style="padding-left:10px;font-size: 12px; font-family: 'Arial Unicode MS';">&nbsp;</td>
  </tr>
  <tr>
    <td height="24" colspan="3" align="left" valign="middle" style="font-size: 15px;"> Address: <?php echo h($addr); ?></td>
    <td align="left" valign="middle" style="padding-left:10px;font-size: 12px; font-family: 'Arial Unicode MS';">&nbsp;</td>
  </tr>
  <tr>
    <td height="24" colspan="2" align="left" valign="middle" style="font-size: 15px; font-family: 'Arial Unicode MS';">Email:<span style="padding-left:5px"><?php echo h($row_order['invoice_email']); ?></span></td>
    <td align="left" valign="middle" style="padding-left:10px;font-size: 12px; font-family: 'Arial Unicode MS';">&nbsp;</td>
    <td align="left" valign="middle" style="padding-left:10px;font-size: 12px; font-family: 'Arial Unicode MS';">&nbsp;</td>
  </tr>
</table>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="border:1px #eaeaea solid;table-layout:fixed;word-wrap:break-word;font-size: 15px; font-family: 'Arial Unicode MS';">
  <tr>
    <td width="134" bgcolor="#eaeaea"><b>Bar Code</b></td>
    <td width="427" height="30" bgcolor="#eaeaea"><b>Description</b></td>
    <td width="84" align="right" bgcolor="#eaeaea"><b>Unit Price</b></td>
    <td width="73" align="right" bgcolor="#eaeaea"><b>Pcs.</b></td>
    <td width="80" align="right" bgcolor="#eaeaea"><b>Total</b></td>
  </tr>
<?php
// 一般商品 + 加價購（原本用 key()/next() 手動移動陣列指標，改用 foreach）
$order_list = g($order_all_list, 'order_list');
if (is_array($order_list)) {
  foreach ($order_list as $key_no => $item) {
    $pd_name  = g($item, 'pd_name');
    $pd_price = g($item, 'pd_price');
    $pd_num   = g($item, 'pd_buy_num');
    foreach (getProducts(g($item, 'product_num')) as $row_product) {
      invRow($row_product['bar_code'], h($pd_name), h($pd_price), $pd_num, mul($pd_num, $pd_price));
    }

    $addpd_array = isset($order_all_list['addpd'][$key_no]) ? $order_all_list['addpd'][$key_no] : array();
    if (is_array($addpd_array)) {
      foreach ($addpd_array as $addpd) {
        $addpd_name  = '&nbsp;&nbsp;(加價購)-' . h(g($addpd, 'addpd_name'));
        $addpd_price = g($addpd, 'addpd_price');
        foreach (getProducts(g($addpd, 'product_num')) as $row_product) {
          invRow($row_product['bar_code'], $addpd_name, h($addpd_price), 1, mul(1, $addpd_price));
        }
      }
    }
  }
}

// 商品特賣
$sp_sell = g($order_all_list, 'sp_sell');
if (is_array($sp_sell)) {
  foreach ($sp_sell as $item) {
    $price = g($item, 'pd_price');
    $num   = g($item, 'pd_buy_num');
    foreach (getProducts(g($item, 'product_num')) as $row_product) {
      invRow($row_product['bar_code'], '', h($price), $num, mul($num, $price));
    }
  }
}

// 幾本幾折
$act_sell_info = g($order_all_list, 'act_sell_info');
if (is_array($act_sell_info)) {
  foreach ($act_sell_info as $act_code => $info) {
    $num_sell     = (isset($order_all_list['num_sell'][$act_code]) && is_array($order_all_list['num_sell'][$act_code])) ? $order_all_list['num_sell'][$act_code] : array();
    $num_discount = g($info, 'num_discount');
    $first        = (is_array($num_discount) && isset($num_discount[0])) ? $num_discount[0] : 0;
    $book_num     = g($info, 'book_num');

    foreach ($num_sell as $item) {
      $price = ($book_num < $first) ? g($item, 'mem_price') : g($item, 'pd_price');
      $num   = g($item, 'pd_buy_num');
      foreach (getProducts(g($item, 'product_num')) as $row_product) {
        invRow($row_product['bar_code'], h(g($item, 'pd_name')), h($price), $num, mul($num, $price));
      }
    }
?>
  <tr>
    <td height="24" style="word-break: break-all; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">&nbsp;</td>
    <td height="24" style="word-break: break-all; padding-right: 10px; border-bottom-color: #CCC; border-bottom-width: 1px; border-bottom-style: dotted; color: #F60;">幾本幾折-<?php echo h(g($info, 'act_sell_name')); ?></td>
    <td height="24" style="word-break: break-all; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">&nbsp;</td>
    <td height="24" style="word-break: break-all; border-bottom-color:#CCC; border-bottom-width:1px; border-bottom-style:dotted">&nbsp;</td>
    <td align="right" style="word-break: break-all; border-bottom-color: #CCC; border-bottom-width: 1px; border-bottom-style: dotted; color: #F00;">-<?php echo h(g($info, 'discount_money')); ?></td>
  </tr>
<?php
  }
}

// 滿額加價購
$m_addpd = g($order_all_list, 'm_addpd');
if (is_array($m_addpd)) {
  foreach ($m_addpd as $item) {
    $price = g($item, 'price');
    $num   = g($item, 'pd_buy_num');
    foreach (getProducts(g($item, 'product_num')) as $row_product) {
      invRow($row_product['bar_code'], h(g($item, 'name')), h($price), $num, mul($price, $num), '#F60');
    }
  }
}

// 滿額贈品（原本的 <tr> 沒有結尾，已補上）
$order_addpd = g($order_all_list, 'order_addpd');
if ($order_addpd != '') {
  foreach (getProducts($order_addpd, true) as $row_product) {
    invRow($row_product['bar_code'], h($row_product['name']), '贈品', 1, 0, '', '#F60');
  }
}

// 各項費用與折抵
if (is_numeric($row_order['pay_mode_price']) && $row_order['pay_mode_price'] != 0) {
?>
  <tr>
    <td height="26" align="right">&nbsp;</td>
    <td align="left">超商付款手續費</td>
    <td align="left">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right"><?php echo h($row_order['pay_mode_price']); ?></td>
  </tr>
<?php
}

$promo_total = isset($order_all_list['promo']['promo_total']) ? $order_all_list['promo']['promo_total'] : 0;
if (is_numeric($promo_total) && $promo_total != 0) {
?>
  <tr>
    <td height="26" align="right">&nbsp;</td>
    <td height="26" align="left">活動-滿額折抵</td>
    <td height="26" align="right">&nbsp;</td>
    <td height="26" align="right">&nbsp;</td>
    <td align="right" style="color: #F00;">-<?php echo h($promo_total); ?></td>
  </tr>
<?php
}

$gift_price = isset($order_all_list['gift_card']['gift_price']) ? $order_all_list['gift_card']['gift_price'] : 0;
if (is_numeric($gift_price) && $gift_price != 0) {
?>
  <tr>
    <td height="26" align="right">&nbsp;</td>
    <td height="26" align="left">折價券-<?php echo h(isset($order_all_list['gift_card']['name']) ? $order_all_list['gift_card']['name'] : ''); ?></td>
    <td height="26" align="right">&nbsp;</td>
    <td height="26" align="right">&nbsp;</td>
    <td align="right" style="color: #F00;">-<?php echo h($gift_price); ?></td>
  </tr>
<?php } ?>
</table>
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0" style="border: 1px #eaeaea solid; table-layout: fixed; word-wrap: break-word; font-size: 15px; font-family: 'Arial Unicode MS', '微軟正黑體 Light', '新細明體-ExtB';">
  <tr>
    <td width="562" height="24" align="left">&nbsp;</td>
    <td width="156" height="24" align="right">Subtotal：</td>
    <td width="80" align="right" style="border:1px #eaeaea solid;"><?php echo h($row_order['amount']); ?></td>
  </tr>
  <?php if (is_numeric($row_order['other_price']) && $row_order['other_price'] != 0) { ?>
  <tr>
    <td width="562" height="24" align="left">&nbsp;</td>
    <td height="24" align="right">Discount：</td>
    <td align="right" style="border:1px #eaeaea solid;"><?php echo h($row_order['other_price']); ?></td>
  </tr>
  <?php } ?>
  <tr>
    <td width="562" height="24" align="left">If you have any questions about this invoice, please contact</td>
    <td height="24" align="right">Shipping Fee：</td>
    <td width="80" align="right" style="border:1px #eaeaea solid;"><?php echo h($row_order['transportation']); ?></td>
  </tr>
  <tr>
    <td width="562" height="24" align="left">Ricky_LAN (+886-2-26959502#235)</td>
    <td height="24" align="right"><b>TOTAL：</b></td>
    <td align="right" style="border:1px #eaeaea solid;"><?php echo h($row_order['total_amount']); ?></td>
  </tr>
  <tr>
    <td width="562" height="24" align="left">ricky@sanhuei.com.tw</td>
    <td height="24" colspan="2" align="right" style="font-size: 13px;">Make all checks payable to</td>
  </tr>
  <tr>
    <td width="562" height="24" align="left" style="font-style: italic;font-weight: bold;">Thank You For Your Business!</td>
    <td height="24" colspan="2" align="right" style="font-size: 14px;">SAN HUEI PUBLISHING CO.,LTD</td>
  </tr>
</table>
