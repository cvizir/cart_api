<?php
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

function req($key) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
}

$cms_mode  = req('cms_mode');
$spec_code = req('spec_code');
$page      = max(1, (int)req('page'));
$pagesize  = (int)req('pagesize');
if ($pagesize < 1) { $pagesize = 20; }
$total     = (int)req('total');

$db_name        = 'spec_info';
$trace          = 0;   // 原本是 1，會把 SQL 與排序資訊印在畫面上
$sort_sql_where = "";  // 這張表不分群組，排序調整不需要額外條件（原本是未定義的變數）
$img_dir        = dirname(dirname(dirname(__FILE__))) . "/images/upload/spec_info/";

// 規格類型 / 顯示類型只允許已知的值
$spec_type = req('spec_type');
if (!in_array($spec_type, array('color', 'capacity', 'resolution', 'size'), true)) { $spec_type = ''; }
$show_type = req('show_type');
if (!in_array($show_type, array('text', 'code', 'image'), true)) { $show_type = ''; }

// 只寫入 edit.php 表單上有的欄位（img_url 由下方圖片處理另外更新）
$sql_data = array(
  'text'      => req('text'),
  'color'     => req('color'),
  'spec_type' => $spec_type,
  'show_type' => $show_type,
  'm_sort'    => max(1, (int)req('m_sort')),
  'ishow'     => (req('ishow') === '') ? '0' : req('ishow'),
);

/** 刪除圖片檔：只接受單純檔名，且限定在規格圖片資料夾內 */
function removeImage($img_dir, $img_url) {
  $name = basename((string)$img_url);
  if ($name !== '' && file_exists($img_dir . $name)) {
    @unlink($img_dir . $name);
  }
}

$ok        = true;
$msg       = "";
$old_img   = '';
$new_code  = '';   // 需要處理圖片的規格代碼

try {
  switch ($cms_mode) {
    case 'add':
      if ($sql_data['text'] === '') {
        $ok  = false;
        $msg = "請輸入規格名稱!!";
        break;
      }
      $pdo->beginTransaction();
      // 先把後面的排序往後推，再新增
      changSortSet('add', $db_name, $sort_sql_where, 'm_sort', $sql_data['m_sort'], '', $trace);
      $sql_data['spec_code'] = createCode($db_name, 'spec_code', 6);   // 原本誤用未定義的 $tb_name
      insertArray($db_name, $sql_data, $trace);
      $pdo->commit();
      $new_code = $sql_data['spec_code'];
      $msg = "新增成功!";
      break;

    case 'edit':
      $stmt = $pdo->prepare("SELECT `m_sort`, `img_url` FROM `{$db_name}` WHERE `spec_code` = ?");
      $stmt->execute(array($spec_code));
      $old = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($spec_code === '' || !$old) {
        $ok  = false;
        $msg = "找不到這筆資料!!";
        break;
      }
      if ($sql_data['text'] === '') {
        $ok  = false;
        $msg = "請輸入規格名稱!!";
        break;
      }
      $old_img = $old['img_url'];

      $pdo->beginTransaction();
      // 以資料庫目前的排序為準（不信任表單送來的舊值）
      changSortSet('edit', $db_name, $sort_sql_where, 'm_sort', $sql_data['m_sort'], $old['m_sort'], $trace);

      $sets = array();
      foreach ($sql_data as $key => $value) {
        $sets[] = "`{$key}` = :{$key}";
      }
      // 原本 WHERE 條件用沒定義的變數，且 spec_code 也被當成要更新的欄位；現在以預處理語句更新
      $stmt = $pdo->prepare("UPDATE `{$db_name}` SET " . implode(", ", $sets) . " WHERE `spec_code` = :_code");
      foreach ($sql_data as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
      }
      $stmt->bindValue(":_code", $spec_code);
      $stmt->execute();
      $pdo->commit();
      $new_code = $spec_code;
      $msg = "更新成功!!";
      break;

    case 'del':
      $stmt = $pdo->prepare("SELECT `m_sort`, `img_url` FROM `{$db_name}` WHERE `spec_code` = ?");
      $stmt->execute(array($spec_code));
      $old = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($spec_code === '' || !$old) {
        $ok  = false;
        $msg = "找不到這筆資料!!";
        break;
      }

      // 規格群組的 spec_code 欄位是以逗號串起來的規格代碼，仍被使用時不允許刪除
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM `spec_group` WHERE FIND_IN_SET(?, `spec_code`) > 0");
      $stmt->execute(array($spec_code));
      $used = (int)$stmt->fetchColumn();
      if ($used > 0) {
        $ok  = false;
        $msg = "此規格仍被 {$used} 個規格群組使用，無法刪除!!";
        break;
      }

      $pdo->beginTransaction();
      $stmt = $pdo->prepare("DELETE FROM `{$db_name}` WHERE `spec_code` = ?");
      $stmt->execute(array($spec_code));
      // 刪除後把後面的排序往前補
      if ($old['m_sort'] !== '' && $old['m_sort'] !== null && $old['m_sort'] != '0') {
        changSortSet('del', $db_name, $sort_sql_where, 'm_sort', $old['m_sort'], $old['m_sort'], $trace);
      }
      $pdo->commit();
      removeImage($img_dir, $old['img_url']);   // 一併刪除圖片檔
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
  error_log("spec_info save error: " . $e->getMessage());
  $ok  = false;
  $msg = "資料庫執行失敗，請稍後再試!!";
}

/* ------------------------------------ 圖片處理 ----------------------------------------
 * 原本的圖片處理有多處問題：$filePath 沒定義、檔名用了不相干的 product_ 前綴與沒定義的 $insert_id，
 * 算出來的檔名更是從頭到尾沒有寫進資料庫的 img_url 欄位，所以上傳圖片其實完全沒有作用。
 */
if ($ok && $new_code !== '' && preg_match('/^[A-Za-z0-9]+$/', $new_code)) {
  $f          = isset($_FILES['add_pic']) ? $_FILES['add_pic'] : null;
  $del_pic    = isset($_POST['del_pic']) ? $_POST['del_pic'] : array();
  $has_upload = ($f && isset($f['error'][0]) && $f['error'][0] === UPLOAD_ERR_OK && is_uploaded_file($f['tmp_name'][0]));
  $img_value  = null;   // null 代表圖片沒有變動，不更新 img_url

  if ($has_upload) {
    $tmp     = $f['tmp_name'][0];
    $info    = @getimagesize($tmp);   // 以實際檔案內容判斷格式，不信任瀏覽器傳來的 type
    $allowed = array('image/jpeg' => 'jpg', 'image/png' => 'png');

    if (!$info || !isset($allowed[$info['mime']])) {
      $msg .= "（圖片格式不支援，僅限 JPG / PNG）";
    } else {
      $name = 'spec_' . $new_code . '.' . $allowed[$info['mime']];
      if (!is_dir($img_dir)) { @mkdir($img_dir, 0755, true); }
      if (move_uploaded_file($tmp, $img_dir . $name)) {
        @chmod($img_dir . $name, 0644);
        // 換了格式（jpg ↔ png）時，刪掉舊檔
        if ($old_img !== '' && basename($old_img) !== $name) {
          removeImage($img_dir, $old_img);
        }
        $img_value = $name;
      } else {
        $msg .= "（圖片儲存失敗）";
      }
    }
  } elseif (!empty($del_pic[0]) && $del_pic[0] == "1") {
    removeImage($img_dir, $old_img);
    $img_value = '';
  } elseif ($f && isset($f['error'][0]) && $f['error'][0] !== UPLOAD_ERR_NO_FILE && $f['error'][0] !== UPLOAD_ERR_OK) {
    $msg .= "（圖片上傳失敗，錯誤代碼 " . (int)$f['error'][0] . "）";
  }

  if ($img_value !== null) {
    try {
      $stmt = $pdo->prepare("UPDATE `{$db_name}` SET `img_url` = ? WHERE `spec_code` = ?");
      $stmt->execute(array($img_value, $new_code));
    } catch (PDOException $e) {
      error_log("spec_info img update error: " . $e->getMessage());
      $msg .= "（圖片檔名寫入資料庫失敗）";
    }
  }
}

// 原本的轉址被註解掉了（//window.location.href），存檔後會停在空白頁；這裡恢復
$web_url = "list.php?page=" . $page;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
// 成功 → 回列表；失敗 → 回上一頁保留表單內容
alertHref($msg, $ok ? $web_url : "");
