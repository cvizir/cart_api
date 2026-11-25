<?php
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

function req($key) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
}

$cms_mode = req('cms_mode');
$no       = req('no');
$page     = max(1, (int)req('page'));
$pagesize = (int)req('pagesize');
if ($pagesize < 1) { $pagesize = 20; }
$total    = (int)req('total');

$db_name = 'billboard_cat';
$trace   = 0;

// 只寫入 edit.php 表單上有的欄位
$sql_data = array(
  'name'    => req('name'),
  'web_url' => req('web_url'),
  'ps'      => isset($_REQUEST['ps']) ? trim($_REQUEST['ps']) : '',
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
      insertArray($db_name, $sql_data, $trace);
      $msg = "新增成功!";
      break;

    case 'edit':
      if ($no === '') {
        $ok  = false;
        $msg = "缺少資料編號!!";
        break;
      }
      if ($sql_data['name'] === '') {
        $ok  = false;
        $msg = "請輸入名稱!!";
        break;
      }
      $stmt = $pdo->prepare("UPDATE `{$db_name}` SET `name` = :name, `web_url` = :web_url, `ps` = :ps WHERE `no` = :_no");
      $stmt->bindValue(":name", $sql_data['name']);
      $stmt->bindValue(":web_url", $sql_data['web_url']);
      $stmt->bindValue(":ps", $sql_data['ps']);
      $stmt->bindValue(":_no", $no);
      $stmt->execute();
      $msg = "更新成功!!";
      break;

    case 'del':
      if ($no === '') {
        $ok  = false;
        $msg = "缺少資料編號!!";
        break;
      }
      // 底下還有廣告時不允許刪除，避免產生找不到類別的孤兒資料
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM `billboard` WHERE `bd_id` = ?");
      $stmt->execute(array($no));
      $child = (int)$stmt->fetchColumn();
      if ($child > 0) {
        $ok  = false;
        $msg = "此類別底下還有 {$child} 筆廣告，請先刪除廣告!!";
        break;
      }
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
  error_log("billboard_cat save error: " . $e->getMessage());
  $ok  = false;
  $msg = "資料庫執行失敗，請稍後再試!!";
}

$web_url = "list.php?page=" . $page;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
// 成功 → 回列表；失敗 → 回上一頁保留表單內容
alertHref($msg, $ok ? $web_url : "");
