<?php
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

function req($key) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
}

/**
 * 排序調整用的條件：只在同一個上層類別底下重新排序。
 * changSortSet() 已自帶 WHERE，所以要用 AND 開頭。
 */
function sortWhere($pdcat_m_code) {
  global $pdo;
  return " AND `pdcat_m_code` = " . $pdo->quote($pdcat_m_code);
}

$cms_mode     = req('cms_mode');
$pdcat_code   = req('pdcat_code');
$pdcat_m_code = req('pdcat_m_code');
$pdcat_m_name = req('pdcat_m_name');
$pdcat_t_code = req('pdcat_t_code');
$pdcat_t_name = req('pdcat_t_name');
$page         = max(1, (int)req('page'));
$pagesize     = (int)req('pagesize');
if ($pagesize < 1) { $pagesize = 20; }
$total        = (int)req('total');

$db_name = 'pdcat';
$trace   = 0;

// 只寫入 edit.php 表單上有的欄位
$sql_data = array(
  'uptime'    => date("Y-m-d H:i:s"),
  'name'      => req('name'),
  'url'       => isset($_REQUEST['url']) ? $_REQUEST['url'] : '',   // CKEditor HTML
  'link_type' => req('link_type'),
  'm_sort'    => max(1, (int)req('m_sort')),
  'ishow'     => req('ishow'),
);

$ok  = true;
$msg = "";

try {
  switch ($cms_mode) {
    case 'add':
      if ($pdcat_m_code === '') {
        $ok  = false;
        $msg = "缺少上層類別!!";
        break;
      }
      if ($sql_data['name'] === '') {
        $ok  = false;
        $msg = "請輸入名稱!!";
        break;
      }
      $pdo->beginTransaction();
      // 先把同類別後面的排序往後推，再新增
      changSortSet('add', $db_name, sortWhere($pdcat_m_code), 'm_sort', $sql_data['m_sort'], '', $trace);
      $sql_data['pdcat_m_code'] = $pdcat_m_code;
      $sql_data['pdcat_code']   = createCode($db_name, 'pdcat_code', 6);   // 原本誤用未定義的 $tb_name
      insertArray($db_name, $sql_data, $trace);
      $pdo->commit();
      $msg = "新增成功!";
      break;

    case 'edit':
      $stmt = $pdo->prepare("SELECT `pdcat_m_code`, `m_sort` FROM `{$db_name}` WHERE `pdcat_code` = ?");
      $stmt->execute(array($pdcat_code));
      $old = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($pdcat_code === '' || !$old) {
        $ok  = false;
        $msg = "找不到這筆資料!!";
        break;
      }
      if ($sql_data['name'] === '') {
        $ok  = false;
        $msg = "請輸入名稱!!";
        break;
      }
      $pdcat_m_code = (string)$old['pdcat_m_code'];   // 以資料庫中的上層類別為準

      $pdo->beginTransaction();
      // 以資料庫目前的排序為準（不信任表單送來的舊值）
      changSortSet('edit', $db_name, sortWhere($pdcat_m_code), 'm_sort', $sql_data['m_sort'], $old['m_sort'], $trace);

      $sets = array();
      foreach ($sql_data as $key => $value) {
        $sets[] = "`{$key}` = :{$key}";
      }
      $stmt = $pdo->prepare("UPDATE `{$db_name}` SET " . implode(", ", $sets) . " WHERE `pdcat_code` = :_code");
      foreach ($sql_data as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
      }
      $stmt->bindValue(":_code", $pdcat_code);
      $stmt->execute();
      $pdo->commit();
      $msg = "更新成功!!";
      break;

    case 'del':
      $stmt = $pdo->prepare("SELECT `pdcat_m_code`, `m_sort` FROM `{$db_name}` WHERE `pdcat_code` = ?");
      $stmt->execute(array($pdcat_code));
      $old = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($pdcat_code === '' || !$old) {
        $ok  = false;
        $msg = "找不到這筆資料!!";
        break;
      }
      $pdcat_m_code = (string)$old['pdcat_m_code'];

      // 原本判斷的 $num_pdcat_m 從頭到尾沒有定義，所以永遠刪不掉；
      // 這一層是最底層，沒有子類別，直接刪除即可
      $pdo->beginTransaction();
      $stmt = $pdo->prepare("DELETE FROM `{$db_name}` WHERE `pdcat_code` = ?");
      $stmt->execute(array($pdcat_code));
      // 刪除後把同類別後面的排序往前補
      if ($old['m_sort'] !== '' && $old['m_sort'] !== null && $old['m_sort'] != '0') {
        changSortSet('del', $db_name, sortWhere($pdcat_m_code), 'm_sort', $old['m_sort'], $old['m_sort'], $trace);
      }
      $pdo->commit();
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
  if ($pdo->inTransaction()) {
    $pdo->rollBack();
  }
  error_log("pdcat save error: " . $e->getMessage());
  $ok  = false;
  $msg = "資料庫執行失敗，請稍後再試!!";
}

// 回列表需要上層類別的名稱與代碼，沒帶時由資料庫補齊
try {
  if ($pdcat_m_code !== '' && ($pdcat_m_name === '' || $pdcat_t_code === '')) {
    $stmt = $pdo->prepare("SELECT `name`, `pdcat_t_code` FROM `pdcat_m` WHERE `pdcat_m_code` = ?");
    $stmt->execute(array($pdcat_m_code));
    $m = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($m) {
      if ($pdcat_m_name === '') { $pdcat_m_name = (string)$m['name']; }
      if ($pdcat_t_code === '') { $pdcat_t_code = (string)$m['pdcat_t_code']; }
    }
  }
  if ($pdcat_t_code !== '' && $pdcat_t_name === '') {
    $stmt = $pdo->prepare("SELECT `name` FROM `pdcat_t` WHERE `pdcat_t_code` = ?");
    $stmt->execute(array($pdcat_t_code));
    $pdcat_t_name = (string)$stmt->fetchColumn();
  }
} catch (PDOException $e) {
  error_log("pdcat save lookup error: " . $e->getMessage());
}

// 原本的轉址是 "list.php?..." 後面又接 "?..."，變成兩個問號；這裡改成單一乾淨的網址
$web_url = "list.php?pdcat_t_code=" . urlencode($pdcat_t_code)
         . "&pdcat_t_name=" . urlencode($pdcat_t_name)
         . "&pdcat_m_code=" . urlencode($pdcat_m_code)
         . "&pdcat_m_name=" . urlencode($pdcat_m_name)
         . "&page=" . $page;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
// 成功 → 回列表；失敗 → 回上一頁保留表單內容
alertHref($msg, $ok ? $web_url : "");
