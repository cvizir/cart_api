<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

function req($key) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
}

$cms_mode    = req('cms_mode');
$member_code = req('member_code');
$page        = max(1, (int)req('page'));
$sh_txt      = req('sh_txt');
$now         = date("Y-m-d H:i:s");

$db_name = 'member';
$web_url = "list.php?page=" . $page . "&sh_txt=" . urlencode($sh_txt);
$trace   = 0;

// 只允許寫入這些欄位（與 edit.php 表單一致）
$sql_data = array(
  'name'      => req('name'),
  'nickname'  => req('nickname'),
  'psw'       => req('psw'),
  'sex'       => req('sex'),
  'email'     => req('email'),
  'tel'       => req('tel'),
  'phone'    => req('phone'),
  'addr'      => req('addr'),
  'birthday'  => (req('birthday') === '') ? null : req('birthday'),  // 空日期存 NULL，避免嚴格模式報錯
  'esend'     => req('esend'),
  'cat_gp_id' => (req('cat_gp_id') === '') ? null : req('cat_gp_id'),
  'uptime'    => $now,
);

$ok  = true;
$msg = "";

try {
  switch ($cms_mode) {
    case 'add':
      $sql_data['regtime']     = $now;
      $sql_data['member_code'] = createOrderCode($db_name, 'member_code', 11);
      insertArray($db_name, $sql_data, $trace);
      $msg = "新增成功!";
      break;

    case 'edit':
      if ($member_code === '') {
        $ok  = false;
        $msg = "缺少會員代碼!!";
        break;
      }
      $sets = array();
      foreach ($sql_data as $key => $value) {
        $sets[] = "`{$key}` = :{$key}";
      }
      $sql  = "UPDATE `{$db_name}` SET " . implode(", ", $sets) . " WHERE `member_code` = :_member_code";
      $stmt = $pdo->prepare($sql);
      foreach ($sql_data as $key => $value) {
        $stmt->bindValue(":{$key}", $value, ($value === null) ? PDO::PARAM_NULL : PDO::PARAM_STR);
      }
      $stmt->bindValue(":_member_code", $member_code, PDO::PARAM_STR);
      $stmt->execute();
      $msg = "更新成功!!";
      break;

    case 'del':
      if ($member_code === '') {
        $ok  = false;
        $msg = "缺少會員代碼!!";
        break;
      }
      $stmt = $pdo->prepare("DELETE FROM `{$db_name}` WHERE `member_code` = ?");
      $stmt->execute(array($member_code));
      $msg = "刪除成功!!";
      break;

    default:
      $ok  = false;
      $msg = "不正確的操作模式!!";
  }
} catch (PDOException $e) {
  error_log("member save error: " . $e->getMessage());
  $ok  = false;
  $msg = "資料庫執行失敗，請稍後再試!!";
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
// 成功 → 回列表；失敗 → 回上一頁保留表單內容
alertHref($msg, $ok ? $web_url : "");
