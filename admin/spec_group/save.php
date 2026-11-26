<?php
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

// 原本這裡有 print_r($_REQUEST) 與 print_r(spec_group_code) 兩行除錯輸出，會把所有送出的資料印在畫面上，已移除

function req($key) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
}

$cms_mode        = req('cms_mode');
$spec_group_code = req('spec_group_code');
$page            = max(1, (int)req('page'));
$pagesize        = (int)req('pagesize');
if ($pagesize < 1) { $pagesize = 20; }
$total           = (int)req('total');

$db_name = 'spec_group';
$trace   = 0;   // 原本是 1，會把 SQL 印出來

// 勾選的規格代碼：沒有勾選時 $_REQUEST['spec_code'] 不存在，原本的 join() 會報錯；
// 這裡只保留合法的代碼（英數字、底線、連字號）
$spec_codes = array();
if (isset($_REQUEST['spec_code']) && is_array($_REQUEST['spec_code'])) {
  foreach ($_REQUEST['spec_code'] as $c) {
    $c = trim((string)$c);
    if ($c !== '' && preg_match('/^[A-Za-z0-9_-]+$/', $c)) {
      $spec_codes[] = $c;
    }
  }
}

// 只寫入 edit.php 表單上有的欄位
$sql_data = array(
  'pdcat_code' => req('pdcat_code'),
  'spec_code'  => implode(",", array_unique($spec_codes)),
  'name'       => req('name'),
  'ishow'      => (req('ishow') === '') ? '0' : req('ishow'),
);

$ok  = true;
$msg = "";

try {
  switch ($cms_mode) {
    case 'add':
      if ($sql_data['name'] === '') {
        $ok  = false;
        $msg = "請輸入規格名稱!!";
        break;
      }
      $sql_data['spec_group_code'] = createCode($db_name, 'spec_group_code', 6);   // 原本誤用未定義的 $tb_name
      insertArray($db_name, $sql_data, $trace);
      $msg = "新增成功!";
      break;

    case 'edit':
      // 原本 WHERE 條件用了沒定義的 $spec_group_code，等於 WHERE spec_group_code=''，永遠更新不到
      if ($spec_group_code === '') {
        $ok  = false;
        $msg = "缺少規格群組代碼!!";
        break;
      }
      if ($sql_data['name'] === '') {
        $ok  = false;
        $msg = "請輸入規格名稱!!";
        break;
      }
      $sets = array();
      foreach ($sql_data as $key => $value) {
        $sets[] = "`{$key}` = :{$key}";
      }
      $stmt = $pdo->prepare("UPDATE `{$db_name}` SET " . implode(", ", $sets) . " WHERE `spec_group_code` = :_code");
      foreach ($sql_data as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
      }
      $stmt->bindValue(":_code", $spec_group_code);
      $stmt->execute();
      $msg = "更新成功!!";
      break;

    case 'del':
      if ($spec_group_code === '') {
        $ok  = false;
        $msg = "缺少規格群組代碼!!";
        break;
      }
      $stmt = $pdo->prepare("DELETE FROM `{$db_name}` WHERE `spec_group_code` = ?");
      $stmt->execute(array($spec_group_code));
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
  error_log("spec_group save error: " . $e->getMessage());
  $ok  = false;
  $msg = "資料庫執行失敗，請稍後再試!!";
}

// 原本的轉址被註解掉了（//window.location.href），存檔後會停在空白頁；這裡恢復
$web_url = "list.php?page=" . $page;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
// 成功 → 回列表；失敗 → 回上一頁保留表單內容
alertHref($msg, $ok ? $web_url : "");
