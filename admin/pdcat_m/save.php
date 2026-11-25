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
function sortWhere($pdcat_t_code) {
  global $pdo;
  return " AND `pdcat_t_code` = " . $pdo->quote($pdcat_t_code);
}

$cms_mode     = req('cms_mode');
$pdcat_m_code = req('pdcat_m_code');
$pdcat_t_code = req('pdcat_t_code');
$pdcat_t_name = req('pdcat_t_name');
$page         = max(1, (int)req('page'));
$pagesize     = (int)req('pagesize');
if ($pagesize < 1) { $pagesize = 20; }
$total        = (int)req('total');

$db_name = 'pdcat_m';
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
      if ($pdcat_t_code === '') {
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
      changSortSet('add', $db_name, sortWhere($pdcat_t_code), 'm_sort', $sql_data['m_sort'], '', $trace);
      $sql_data['pdcat_t_code'] = $pdcat_t_code;
      $sql_data['pdcat_m_code'] = createCode($db_name, 'pdcat_m_code', 6);   // 原本誤用未定義的 $tb_name
      insertArray($db_name, $sql_data, $trace);
      $pdo->commit();
      $msg = "新增成功!";
      break;

    case 'edit':
      $stmt = $pdo->prepare("SELECT `pdcat_t_code`, `m_sort` FROM `{$db_name}` WHERE `pdcat_m_code` = ?");
      $stmt->execute(array($pdcat_m_code));
      $old = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($pdcat_m_code === '' || !$old) {
        $ok  = false;
        $msg = "找不到這筆資料!!";
        break;
      }
      if ($sql_data['name'] === '') {
        $ok  = false;
        $msg = "請輸入名稱!!";
        break;
      }
      $pdcat_t_code = (string)$old['pdcat_t_code'];   // 以資料庫中的上層類別為準

      $pdo->beginTransaction();
      // 以資料庫目前的排序為準（不信任表單送來的舊值）
      changSortSet('edit', $db_name, sortWhere($pdcat_t_code), 'm_sort', $sql_data['m_sort'], $old['m_sort'], $trace);

      $sets = array();
      foreach ($sql_data as $key => $value) {
        $sets[] = "`{$key}` = :{$key}";
      }
      $stmt = $pdo->prepare("UPDATE `{$db_name}` SET " . implode(", ", $sets) . " WHERE `pdcat_m_code` = :_code");
      foreach ($sql_data as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
      }
      $stmt->bindValue(":_code", $pdcat_m_code);
      $stmt->execute();
      $pdo->commit();
      $msg = "更新成功!!";
      break;

    case 'del':
      $stmt = $pdo->prepare("SELECT `pdcat_t_code`, `m_sort` FROM `{$db_name}` WHERE `pdcat_m_code` = ?");
      $stmt->execute(array($pdcat_m_code));
      $old = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($pdcat_m_code === '' || !$old) {
        $ok  = false;
        $msg = "找不到這筆資料!!";
        break;
      }
      $pdcat_t_code = (string)$old['pdcat_t_code'];

      // 先檢查是否有子類別（原本是先調整排序再檢查，有子類別時排序已被改掉卻沒刪除）
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM `pdcat` WHERE `pdcat_m_code` = ?");
      $stmt->execute(array($pdcat_m_code));
      if ((int)$stmt->fetchColumn() > 0) {
        $ok  = false;
        $msg = "還有子類別所以無法刪除!!";
        break;
      }

      $pdo->beginTransaction();
      $stmt = $pdo->prepare("DELETE FROM `{$db_name}` WHERE `pdcat_m_code` = ?");
      $stmt->execute(array($pdcat_m_code));
      // 刪除後把同類別後面的排序往前補
      if ($old['m_sort'] !== '' && $old['m_sort'] !== null && $old['m_sort'] != '0') {
        changSortSet('del', $db_name, sortWhere($pdcat_t_code), 'm_sort', $old['m_sort'], $old['m_sort'], $trace);
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
  error_log("pdcat_m save error: " . $e->getMessage());
  $ok  = false;
  $msg = "資料庫執行失敗，請稍後再試!!";
}

// 回列表需要上層類別名稱，沒帶時由資料庫查出來
if ($pdcat_t_name === '' && $pdcat_t_code !== '') {
  try {
    $stmt = $pdo->prepare("SELECT `name` FROM `pdcat_t` WHERE `pdcat_t_code` = ?");
    $stmt->execute(array($pdcat_t_code));
    $pdcat_t_name = (string)$stmt->fetchColumn();
  } catch (PDOException $e) {
    error_log("pdcat_m save lookup error: " . $e->getMessage());
  }
}

// 原本的 $sh_post 沒定義，轉址沒有帶上層類別，列表會是空的
$web_url = "list.php?pdcat_t_code=" . urlencode($pdcat_t_code)
         . "&pdcat_t_name=" . urlencode($pdcat_t_name)
         . "&page=" . $page;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
// 成功 → 回列表；失敗 → 回上一頁保留表單內容
alertHref($msg, $ok ? $web_url : "");
