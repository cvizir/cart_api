<?php
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

function req($key) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
}

$cms_mode  = req('cms_mode');
$news_code = req('news_code');
$page      = max(1, (int)req('page'));
$pagesize  = (int)req('pagesize');
if ($pagesize < 1) { $pagesize = 25; }
$total     = (int)req('total');

$db_name = 'news';
$trace   = 0;
$now     = date("Y-m-d H:i:s");

$wttime = (req('wttime') === '') ? date("Y-m-d") : req('wttime');

// 上下架時間：空白時給預設值。
// 原本下架預設為 2020-00-00（不是合法日期，而且已經過去，會讓沒填下架日期的訊息全部顯示「已結束」）
$start_time = req('start_time');
if ($start_time === '' || substr($start_time, 0, 4) === '0000') {
  $start_time = '2000-01-01 00:00:00';
}
$end_time = req('end_time');
if ($end_time === '' || substr($end_time, 0, 4) === '0000') {
  $end_time = '2100-12-30 00:00:00';
}

// 只寫入 edit.php 表單上有的欄位
$sql_data = array(
  'uptime'     => $now,
  'wttime'     => $wttime,
  'name'       => req('name'),
  'web_url'    => req('web_url'),
  'summary'    => isset($_REQUEST['summary']) ? $_REQUEST['summary'] : '',   // CKEditor HTML
  'start_time' => $start_time,
  'end_time'   => $end_time,
  'ishow'      => req('ishow'),
);

$ok  = true;
$msg = "";

try {
  switch ($cms_mode) {
    case 'add':
      if ($sql_data['name'] === '') {
        $ok  = false;
        $msg = "請輸入名稱!!";
        break;
      }
      $sql_data['news_code'] = createCode($db_name, 'news_code', 11);   // 原本誤用未定義的 $tb_name
      insertArray($db_name, $sql_data, $trace);
      $msg = "新增成功!";
      break;

    case 'edit':
      if ($news_code === '') {
        $ok  = false;
        $msg = "缺少訊息代碼!!";
        break;
      }
      if ($sql_data['name'] === '') {
        $ok  = false;
        $msg = "請輸入名稱!!";
        break;
      }
      $sets = array();
      foreach ($sql_data as $key => $value) {
        $sets[] = "`{$key}` = :{$key}";
      }
      $stmt = $pdo->prepare("UPDATE `{$db_name}` SET " . implode(", ", $sets) . " WHERE `news_code` = :_news_code");
      foreach ($sql_data as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
      }
      $stmt->bindValue(":_news_code", $news_code);
      $stmt->execute();
      $msg = "更新成功!!";
      break;

    case 'del':
      if ($news_code === '') {
        $ok  = false;
        $msg = "缺少訊息代碼!!";
        break;
      }
      $stmt = $pdo->prepare("DELETE FROM `{$db_name}` WHERE `news_code` = ?");
      $stmt->execute(array($news_code));
      $msg = "刪除成功!!";

      // 刪除後若目前頁已沒有資料，退回上一頁
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
  error_log("news save error: " . $e->getMessage());
  $ok  = false;
  $msg = "資料庫執行失敗，請稍後再試!!";
}

$web_url = "list.php?page=" . $page;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
// 成功 → 回列表；失敗 → 回上一頁保留表單內容
alertHref($msg, $ok ? $web_url : "");
