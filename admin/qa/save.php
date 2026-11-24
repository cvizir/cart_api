<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

function req($key) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
}

$cms_mode = req('cms_mode');
$qa_code  = req('qa_code');
$page     = max(1, (int)req('page'));
$pagesize = max(1, (int)req('pagesize') ?: 5);
$total    = (int)req('total');

$db_name = 'qa';
$trace   = 0;
$uptime  = (req('uptime') === '') ? date("Y-m-d H:i:s") : req('uptime');

// 只允許寫入這些欄位
$sql_data = array(
  'uptime'   => $uptime,
  'cat_id'   => req('cat_id'),
  'question' => req('question'),
  'ans'      => isset($_REQUEST['ans']) ? $_REQUEST['ans'] : '',  // CKEditor 的 HTML 內容，不 trim 以保留格式
  'm_sort'   => (int)req('m_sort'),
  'ishow'    => req('ishow'),
);

$ok  = true;
$msg = "";

try {
  switch ($cms_mode) {
    case 'add':
      $sql_data['qa_code'] = createCode($db_name, 'qa_code', 11);
      insertArray($db_name, $sql_data, $trace);
      $msg = "新增成功!";
      break;

    case 'edit':
      if ($qa_code === '') {
        $ok  = false;
        $msg = "缺少 Q&A 代碼!!";
        break;
      }
      $sets = array();
      foreach ($sql_data as $key => $value) {
        $sets[] = "`{$key}` = :{$key}";
      }
      $stmt = $pdo->prepare("UPDATE `{$db_name}` SET " . implode(", ", $sets) . " WHERE `qa_code` = :_qa_code");
      foreach ($sql_data as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
      }
      $stmt->bindValue(":_qa_code", $qa_code);
      $stmt->execute();
      $msg = "更新成功!!";
      break;

    case 'del':
      if ($qa_code === '') {
        $ok  = false;
        $msg = "缺少 Q&A 代碼!!";
        break;
      }
      $stmt = $pdo->prepare("DELETE FROM `{$db_name}` WHERE `qa_code` = ?");
      $stmt->execute(array($qa_code));
      $msg = "刪除成功!!";

      // 刪除後若目前頁已經沒有資料，退回上一頁
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
  error_log("qa save error: " . $e->getMessage());
  $ok  = false;
  $msg = "資料庫執行失敗，請稍後再試!!";
}

$web_url = "list.php?page=" . $page;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
// 成功 → 回列表；失敗 → 回上一頁保留表單內容
alertHref($msg, $ok ? $web_url : "");
