<?php
// 重寄訂單 Email。edit.php 以 AJAX 呼叫，回傳 JSON：{"ok":true} 或 {"ok":false,"msg":"..."}
require_once("../../include/config.inc.php");
include_once("../session.php");     // 原本沒有載入，任何人都能觸發寄信
require_once("../../include/function.php");

header('Content-Type: application/json; charset=utf-8');

function out($ok, $msg = '') {
  echo json_encode(array('ok' => $ok, 'msg' => $msg));
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  out(false, '不正確的請求方式');
}

$member_code = isset($_POST['member_code']) ? trim($_POST['member_code']) : '';
$order_code  = isset($_POST['order_code']) ? trim($_POST['order_code']) : '';
$no          = isset($_POST['no']) ? trim($_POST['no']) : '';

if ($no === '' || $order_code === '') {
  out(false, '參數不完整');
}

try {
  $stmt = $pdo->prepare("SELECT * FROM `orderlist` WHERE `no` = ? AND `order_code` = ? AND `member_code` = ?");
  $stmt->execute(array($no, $order_code, $member_code));
  $row_orderlist = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  error_log("resend query error: " . $e->getMessage());
  out(false, '資料庫執行失敗');
}

if (!$row_orderlist) {
  out(false, '找不到這筆訂單');
}

// 收件信箱必須是合法的 Email（同時避免標頭注入）
$to = trim($row_orderlist['invoice_email']);
if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
  out(false, '訂單信箱格式不正確');
}

$order_num = 'C17' . sprintf("%06d", $row_orderlist['no']);

// 向前台取得訂單明細的 HTML
$q  = "order_code=" . urlencode($order_code) . "&member_code=" . urlencode($member_code);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://shopping.windmill.com.tw/email_order.php?" . $q);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $q);
// 原本的逾時只有 900 毫秒，前台稍慢就會取不到明細，信裡就少了訂單內容；改成 15 秒
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$html_code_order = curl_exec($ch);
curl_close($ch);

if ($html_code_order === false || $html_code_order === '') {
  out(false, '無法取得訂單明細，信件未寄出');
}

$from_name = '『風車寶貝』 線上購物網';
$from      = '=?UTF-8?B?' . base64_encode($from_name) . '?= <service@windmill.com.tw>';
$subject   = '=?UTF-8?B?' . base64_encode('完成運費審核，待付款通知-訂單編號: ' . $order_num) . '?=';

$html  = '感謝您訂購『風車寶貝』線上購物網的商品，您可 <a href="https://shopping.windmill.com.tw/inquireContent.php?order_code=' . urlencode($order_code) . '">點此查看</a> 訂單明細。<br /><br />';
$html .= '此信件為訂單通知，待付款金額確認無誤後，商品預計3~5個工作天送達；若您為國外訂單，待確認運費後，請至「訂單查詢」頁面選擇付款，確認無誤後即為您安排出貨。';
$html .= $html_code_order;
$html  = '<div style="color:#000;">' . $html . '</div>';

$headers  = "From: " . $from . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=utf-8\r\n";

// 原本寄信前有一行 sleep(5)，沒有任何作用只會讓使用者多等 5 秒，已移除
if (mail($to, $subject, $html, $headers)) {
  out(true);
}
out(false, '寄信失敗，請檢查郵件伺服器設定');
