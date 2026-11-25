<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

function req($key) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
}

// 金額欄位：不是數字就當 0
function num($v) {
  return is_numeric($v) ? $v : 0;
}

// 日期欄位：空白、0000- 開頭或格式不對都存成 NULL
function dateOrNull($v) {
  if ($v === '' || substr($v, 0, 4) === '0000' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
    return null;
  }
  return $v;
}

$cms_mode = req('cms_mode');
$no       = req('no');
$page     = max(1, (int)req('page'));
$pagesize = (int)req('pagesize');
if ($pagesize < 1) { $pagesize = 40; }
$total    = (int)req('total');

$db_name = 'orderlist';
$trace   = 0;
$now     = date("Y-m-d H:i:s");

// 只允許已知的訂單狀態
$order_states = array('未付款', '待付款', '完成訂購', '確認運費', '訂單處理中', '刷卡失敗',
                      '取消訂單', '已出貨', '先出貨待付款', '退貨訂單', '失效訂單');
$order_state  = req('order_state');

/*
 * 可以編輯的欄位（與 edit.php 表單上「沒有 readonly」的欄位一致）。
 * 原本還會把 readonly 的欄位、以及表單上根本沒有的欄位（transaction_Id、receives_*、order_html、invoice_get…）
 * 一起寫回資料庫，結果每存一次，LINE PAY 訂單編碼等資料就被清成空白。
 */
$sql_data = array(
  'uptime'            => $now,
  'order_state'       => $order_state,
  'post_cpy'          => req('post_cpy'),
  'pre_post_date'     => dateOrNull(req('pre_post_date')),
  'post_date'         => dateOrNull(req('post_date')),
  'post_num'          => req('post_num'),
  'order_ps'          => req('order_ps'),
  'atm_num'           => req('atm_num'),
  'store_num'         => req('store_num'),
  'invoice_name'      => req('invoice_name'),     // 原本多做了一次 addslashes，加上預處理會造成資料多出反斜線
  'invoice_tel'       => req('invoice_tel'),
  'invoice_country'   => req('invoice_country'),
  'invoice_zipcode'   => req('invoice_zipcode'),
  'invoice_city'      => req('invoice_city'),
  'invoice_area'      => req('invoice_area'),
  'invoice_addr'      => req('invoice_addr'),
  'pay_mode'          => req('pay_mode'),
  'get_mode'          => req('get_mode'),
  'invoice_type'      => req('invoice_type'),
  'transportation_ps' => req('transportation_ps'),
  'pay_mode_price'    => num(req('pay_mode_price')),
  'other_price'       => num(req('other_price')),
  'other_ps'          => req('other_ps'),
  'total_amount'      => num(req('total_amount')),
  'ps'                => req('ps'),
);

/**
 * 訂單變成「已出貨」時，累計會員消費金額並依門檻升級會員等級。
 * 規則維持原本：lv_1 消費滿 1 升 lv_2、滿 1200 升 lv_3；lv_2 滿 1200 升 lv_3；升級時扣掉門檻金額。
 */
function updateMemberLevel($member_code, $total_amount) {
  global $pdo;

  $stmt = $pdo->prepare("UPDATE `member` SET `buy_total` = `buy_total` + ? WHERE `member_code` = ?");
  $stmt->execute(array($total_amount, $member_code));

  $stmt = $pdo->prepare("SELECT `buy_total`, `mem_lv` FROM `member` WHERE `member_code` = ?");
  $stmt->execute(array($member_code));
  $row_member = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$row_member) { return; }

  $buy_total = $row_member['buy_total'];
  $mem_lv    = $row_member['mem_lv'];
  $set_lv    = '';
  $set_total = 0;

  if ($mem_lv == 'lv_2') {
    if ($buy_total >= 1200) {
      $set_lv    = 'lv_3';
      $set_total = $buy_total - 1200;
    }
  }
  if ($mem_lv == 'lv_1') {
    if ($buy_total >= 1) {
      $set_lv    = 'lv_2';
      $set_total = $buy_total - 1;
    }
    if ($buy_total >= 1200) {
      $set_lv    = 'lv_3';
      $set_total = $buy_total - 1200;
    }
  }

  if ($set_lv == 'lv_2' || $set_lv == 'lv_3') {
    $stmt = $pdo->prepare("UPDATE `member` SET `uptime` = ?, `mem_lv` = ?, `mem_lv_start` = ?, `mem_lv_end` = ?, `buy_total` = ? WHERE `member_code` = ?");
    $stmt->execute(array(
      date("Y-m-d H:i:s"),
      $set_lv,
      date("Y-m-d"),
      date("Y-m-d", strtotime(date("Y-m-d") . "+366 days")),
      $set_total,
      $member_code
    ));
  }
}

$ok  = true;
$msg = "";

try {
  switch ($cms_mode) {
    case 'add':
      if (!in_array($order_state, $order_states, true)) {
        $ok  = false;
        $msg = "請選擇正確的訂單狀態!!";
        break;
      }
      // 新增時才需要寫入原本唯讀的欄位（原本的 buytime 預設值算好了卻沒有使用，這裡改正）
      $sql_data['order_code']   = createOrderCode($db_name, 'order_code', 10);   // 原本誤用未定義的 $tb_name
      $sql_data['buytime']      = $now;
      $sql_data['member_no']    = req('member_no');
      $sql_data['member_code']  = req('member_code');
      $sql_data['invoice_email'] = req('invoice_email');
      $sql_data['invoice_title'] = req('invoice_title');
      $sql_data['invoice_num']  = req('invoice_num');
      $sql_data['amount']       = num(req('amount'));
      $sql_data['transportation'] = num(req('transportation'));
      $sql_data['pay_info']     = isset($_REQUEST['pay_info']) ? $_REQUEST['pay_info'] : '';
      $sql_data['order_info']   = isset($_REQUEST['order_info']) ? $_REQUEST['order_info'] : '';
      insertArray($db_name, $sql_data, $trace);
      $msg = "新增成功!";
      break;

    case 'edit':
      if ($no === '') {
        $ok  = false;
        $msg = "缺少訂單編號!!";
        break;
      }
      if (!in_array($order_state, $order_states, true)) {
        $ok  = false;
        $msg = "請選擇正確的訂單狀態!!";
        break;
      }

      $pdo->beginTransaction();

      // 以資料庫中的狀態與會員為準（原本相信表單送來的 old_order_state / member_code，可以被竄改，也會因為兩人同時編輯而重複累計）
      $stmt = $pdo->prepare("SELECT `order_state`, `member_code` FROM `{$db_name}` WHERE `no` = ? FOR UPDATE");
      $stmt->execute(array($no));
      $old = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$old) {
        $pdo->rollBack();
        $ok  = false;
        $msg = "找不到這筆訂單!!";
        break;
      }

      $sets = array();
      foreach ($sql_data as $key => $value) {
        $sets[] = "`{$key}` = :{$key}";
      }
      $stmt = $pdo->prepare("UPDATE `{$db_name}` SET " . implode(", ", $sets) . " WHERE `no` = :_no");
      foreach ($sql_data as $key => $value) {
        $stmt->bindValue(":{$key}", $value, ($value === null) ? PDO::PARAM_NULL : PDO::PARAM_STR);
      }
      $stmt->bindValue(":_no", $no);
      $stmt->execute();

      // 第一次變成「已出貨」時，累計會員消費並檢查升級
      if ($order_state == '已出貨' && $old['order_state'] != '已出貨' && $old['member_code'] != '') {
        updateMemberLevel($old['member_code'], $sql_data['total_amount']);
      }

      $pdo->commit();
      $msg = "更新成功!!";
      break;

    case 'del':
      if ($no === '') {
        $ok  = false;
        $msg = "缺少訂單編號!!";
        break;
      }
      // 原本用沒定義的 $order_code 當條件（等於 WHERE order_code=''），永遠刪不到；改用訂單編號
      $stmt = $pdo->prepare("DELETE FROM `{$db_name}` WHERE `no` = ?");
      $stmt->execute(array($no));
      $msg = "刪除成功!!";

      $total--;
      if ($page > 1 && ($page - 1) * $pagesize >= $total) {
        $page--;
      }
      break;

    default:
      $ok  = false;
      $msg = "不正確的操作模式!!";
  }
} catch (PDOException $e) {
  if ($pdo->inTransaction()) {
    $pdo->rollBack();
  }
  error_log("orderlist save error: " . $e->getMessage());
  $ok  = false;
  $msg = "資料庫執行失敗，請稍後再試!!";
}

// 回列表時保留原本的搜尋條件
$web_url = "list.php?page=" . $page
         . "&sh_mem=" . urlencode(req('sh_mem'))
         . "&sh_txt=" . urlencode(req('sh_txt'))
         . "&sh_order=" . urlencode(req('sh_order'));
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
// 成功 → 回列表；失敗 → 回上一頁保留表單內容
alertHref($msg, $ok ? $web_url : "");
