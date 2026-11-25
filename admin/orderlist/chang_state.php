<?php
// 列表頁用 AJAX 呼叫，回傳 JSON：{"ok":true} 或 {"ok":false,"msg":"..."}
require_once("../../include/config.inc.php");
include_once("../session.php");     // 原本沒有載入，任何人不用登入就能改訂單狀態
require_once("../../include/function.php");

header('Content-Type: application/json; charset=utf-8');

function out($ok, $msg = '') {
  echo json_encode(array('ok' => $ok, 'msg' => $msg));
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  out(false, '不正確的請求方式');
}

// 與 list.php 相同的權限判斷
$admin_pv = isset($_SESSION["admin_pv"]) ? $_SESSION["admin_pv"] : '';
if (!empty($admin_pv_check) && !strExist($admin_pv, 'orderlist_et')) {
  out(false, '您沒有編輯的權限');
}

$no          = isset($_POST['no']) ? trim($_POST['no']) : '';
$order_code  = isset($_POST['order_code']) ? trim($_POST['order_code']) : '';
$order_state = isset($_POST['order_state']) ? trim($_POST['order_state']) : '';

// 只允許已知的訂單狀態
$order_states = array('未付款', '待付款', '完成訂購', '確認運費', '訂單處理中', '刷卡失敗',
                      '取消訂單', '已出貨', '先出貨待付款', '退貨訂單', '失效訂單');

if ($no === '' || $order_code === '' || !in_array($order_state, $order_states, true)) {
  out(false, '參數不完整或訂單狀態不正確');
}

try {
  $stmt = $pdo->prepare("UPDATE `orderlist` SET `order_state` = ? WHERE `no` = ? AND `order_code` = ?");
  $stmt->execute(array($order_state, $no, $order_code));
  out(true);
} catch (PDOException $e) {
  error_log("chang_state error: " . $e->getMessage());
  out(false, '資料庫執行失敗');
}
