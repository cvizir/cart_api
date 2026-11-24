<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

function req($key) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
}

$cms_mode     = req('cms_mode');
$contact_code = req('contact_code');
$page         = max(1, (int)req('page'));
$pagesize     = (int)req('pagesize');
if ($pagesize < 1) { $pagesize = 20; }
$total        = (int)req('total');

$db_name = 'contact';
$trace   = 0;
$now     = date("Y-m-d H:i:s");

// 只允許寫入表單上有的欄位
$sql_data = array(
  'uptime'  => $now,
  'subject' => req('subject'),
  'name'    => req('name'),
  'email'   => req('email'),
  'msg'     => req('msg'),
);

$ok  = true;
$msg = "";

try {
  switch ($cms_mode) {
    case 'add':
      $sql_data['post_time']    = $now;
      $sql_data['contact_code'] = createCode($db_name, 'contact_code', 11);
      insertArray($db_name, $sql_data, $trace);
      $msg = "新增成功!";
      break;

    case 'edit':
      if ($contact_code === '') {
        $ok  = false;
        $msg = "缺少聯絡代碼!!";
        break;
      }
      // 修改時不更動 post_time（訊息原始時間）與 contact_code
      $sets = array();
      foreach ($sql_data as $key => $value) {
        $sets[] = "`{$key}` = :{$key}";
      }
      $stmt = $pdo->prepare("UPDATE `{$db_name}` SET " . implode(", ", $sets) . " WHERE `contact_code` = :_contact_code");
      foreach ($sql_data as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
      }
      $stmt->bindValue(":_contact_code", $contact_code);
      $stmt->execute();
      $msg = "更新成功!!";
      break;

    case 'del':
      if ($contact_code === '') {
        $ok  = false;
        $msg = "缺少聯絡代碼!!";
        break;
      }
      $stmt = $pdo->prepare("DELETE FROM `{$db_name}` WHERE `contact_code` = ?");
      $stmt->execute(array($contact_code));
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
  error_log("contact save error: " . $e->getMessage());
  $ok  = false;
  $msg = "資料庫執行失敗，請稍後再試!!";
}

$web_url = "list.php?page=" . $page;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
// 成功 → 回列表；失敗 → 回上一頁保留表單內容
alertHref($msg, $ok ? $web_url : "");
