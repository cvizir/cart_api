<?php
ini_set("memory_limit", "100M");
set_time_limit(900);

include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

function req($key) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
}

/**
 * 排序調整用的條件。
 * 注意：changSortSet() 已經自帶 WHERE，所以這裡必須用 AND 開頭
 * （原本傳入 " WHERE ..." 會變成兩個 WHERE 而語法錯誤）。
 */
function sortWhere($bd_id) {
  global $pdo;
  return " AND `bd_id` = " . $pdo->quote($bd_id);
}

$cms_mode = req('cms_mode');
$no       = req('no');
$bd_id    = req('bd_id');
$bd_name  = req('bd_name');
$page     = max(1, (int)req('page'));
$pagesize = (int)req('pagesize');
if ($pagesize < 1) { $pagesize = 20; }
$total    = (int)req('total');

$db_name = 'billboard';
$trace   = 0;

// 上下架時間（空白時給預設值）
$start_time = req('start_time');
if ($start_time === '' || $start_time === '2000-01-01 00:00:00') {
  $start_time = '0000-12-30 00:00:00';
}
$end_time = req('end_time');
if ($end_time === '' || $end_time === '2000-01-01 00:00:00') {
  $end_time = '2100-12-30 00:00:00';
}

// 圖片尺寸，格式必須是 寬x高
$pic_size = (isset($_REQUEST['pic_size'][0])) ? trim($_REQUEST['pic_size'][0]) : '';
if ($pic_size !== '' && !preg_match('/^\d+x\d+$/i', $pic_size)) {
  $pic_size = '';
}

$sql_data = array(
  'del_sw'     => req('del_sw'),
  'name'       => req('name'),
  'm_type'     => req('m_type'),
  'info'       => isset($_REQUEST['info']) ? $_REQUEST['info'] : '',          // CKEditor HTML
  'pic_size'   => $pic_size,
  'start_time' => $start_time,
  'end_time'   => $end_time,
  'web_code'   => isset($_REQUEST['web_code']) ? $_REQUEST['web_code'] : '',  // 廣告程式碼（HTML）
  'web_url'    => req('web_url'),
  'm_sort'     => max(1, (int)req('m_sort')),
  'ishow'      => req('ishow'),
);

$ok        = true;
$msg       = "";
$insert_id = 0;

try {
  switch ($cms_mode) {
    case 'add':
      if ($bd_id === '') {
        $ok  = false;
        $msg = "缺少廣告類別!!";
        break;
      }
      $pdo->beginTransaction();
      // 先把後面的排序往後推，再新增（順序不能反過來，否則新資料自己也會被推一格）
      changSortSet('add', $db_name, sortWhere($bd_id), 'm_sort', $sql_data['m_sort'], '', $trace);
      $sql_data['bd_id'] = $bd_id;
      $insert_id = insertArray($db_name, $sql_data, $trace);
      $pdo->commit();
      $msg = "新增成功!";
      break;

    case 'edit':
      $stmt = $pdo->prepare("SELECT `bd_id`, `m_sort` FROM `{$db_name}` WHERE `no` = ?");
      $stmt->execute(array($no));
      $old = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($no === '' || !$old) {
        $ok  = false;
        $msg = "找不到這筆資料!!";
        break;
      }

      // 表單上的「廣告類別」下拉選單（cat_id）可以搬移類別
      $new_bd_id = (req('cat_id') !== '') ? req('cat_id') : (string)$old['bd_id'];

      $pdo->beginTransaction();
      if ($new_bd_id !== (string)$old['bd_id']) {
        // 搬到別的類別：舊類別補空位，新類別插入並把後面往後推
        changSortSet('del', $db_name, sortWhere($old['bd_id']), 'm_sort', $old['m_sort'], $old['m_sort'], $trace);
        changSortSet('add', $db_name, sortWhere($new_bd_id), 'm_sort', $sql_data['m_sort'], '', $trace);
      } else {
        // 同類別內移動：以資料庫目前的排序為準（不信任表單送來的舊值）
        changSortSet('edit', $db_name, sortWhere($new_bd_id), 'm_sort', $sql_data['m_sort'], $old['m_sort'], $trace);
      }
      $sql_data['bd_id'] = $new_bd_id;

      $sets = array();
      foreach ($sql_data as $key => $value) {
        $sets[] = "`{$key}` = :{$key}";
      }
      $stmt = $pdo->prepare("UPDATE `{$db_name}` SET " . implode(", ", $sets) . " WHERE `no` = :_no");
      foreach ($sql_data as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
      }
      $stmt->bindValue(":_no", $no);
      $stmt->execute();
      $pdo->commit();

      $insert_id = $no;
      $msg = "更新成功!!";
      break;

    case 'del':
      $stmt = $pdo->prepare("SELECT `bd_id`, `m_sort`, `del_sw` FROM `{$db_name}` WHERE `no` = ?");
      $stmt->execute(array($no));
      $old = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($no === '' || !$old) {
        $ok  = false;
        $msg = "找不到這筆資料!!";
        break;
      }
      if ($old['del_sw'] == '1') {
        $ok  = false;
        $msg = "這筆資料不可刪除!!";
        break;
      }
      $pdo->beginTransaction();
      $stmt = $pdo->prepare("DELETE FROM `{$db_name}` WHERE `no` = ?");
      $stmt->execute(array($no));
      // 刪除後把後面的排序往前補
      changSortSet('del', $db_name, sortWhere($old['bd_id']), 'm_sort', $old['m_sort'], $old['m_sort'], $trace);
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
  error_log("billboard save error: " . $e->getMessage());
  $ok  = false;
  $msg = "資料庫執行失敗，請稍後再試!!";
}

/* --------------------------------- 圖片處理 -------------------------------- */
if ($ok && ($cms_mode == 'add' || $cms_mode == 'edit') && $insert_id) {
  $f = isset($_FILES['add_pic']) ? $_FILES['add_pic'] : null;

  if ($f && isset($f['error'][0]) && $f['error'][0] === UPLOAD_ERR_OK && is_uploaded_file($f['tmp_name'][0])) {
    $tmp     = $f['tmp_name'][0];
    $info    = @getimagesize($tmp);                       // 以實際檔案內容判斷格式，不信任瀏覽器傳來的 type
    $allowed = array('image/jpeg' => 'jpg', 'image/png' => 'png');

    if (!$info || !isset($allowed[$info['mime']])) {
      $msg .= "（圖片格式不支援，僅限 JPG / PNG）";
    } else {
      $mime     = $info['mime'];
      $ext      = $allowed[$mime];
      $xid      = 'billboard_' . sprintf("%06d", $insert_id) . '_1';
      $pic_name = $xid . '.' . $ext;
      $filePath = dirname(dirname(dirname(__FILE__))) . "/images/upload/billboard/";

      // 縮圖模式：a=原尺寸 / w=只限制寬 / wh=指定寬高
      if ($pic_size === '' || $pic_size === '0x0') {
        $small = array(array($pic_name, "a", "", ""));
      } else {
        $wh = explode('x', strtolower($pic_size));
        $w  = (int)$wh[0];
        $hh = (int)$wh[1];
        if ($w == 0 && $hh == 0) {
          $small = array(array($pic_name, "a", "", ""));
        } elseif ($hh == 0) {
          $small = array(array($pic_name, "w", $w, 0));
        } else {
          if ($w == 0) {   // 只限制高度：依原圖比例算出寬度
            $w = max(1, (int)round($info[0] * $hh / $info[1]));
          }
          $small = array(array($pic_name, "wh", $w, $hh));
        }
      }

      uploadedPhotoPathR($tmp, $filePath, $mime, '', $small);
      @unlink($filePath . 'opx.jpg');   // 處理過程的暫存檔

      // 若改了格式（jpg ↔ png），刪掉舊格式的檔案
      $other = $xid . '.' . (($ext == 'jpg') ? 'png' : 'jpg');
      if (file_exists($filePath . $other)) {
        @unlink($filePath . $other);
      }

      // 原本誤寫成更新 order_list 資料表，已改正為 billboard
      try {
        $stmt = $pdo->prepare("UPDATE `{$db_name}` SET `photo` = ? WHERE `no` = ?");
        $stmt->execute(array($pic_name, $insert_id));
      } catch (PDOException $e) {
        error_log("billboard photo update error: " . $e->getMessage());
        $msg .= "（圖片檔名寫入資料庫失敗）";
      }
    }
  } elseif ($f && isset($f['error'][0]) && $f['error'][0] !== UPLOAD_ERR_NO_FILE && $f['error'][0] !== UPLOAD_ERR_OK) {
    $msg .= "（圖片上傳失敗，錯誤代碼 " . (int)$f['error'][0] . "）";
  }
}

$web_url = "list.php?bd_id=" . urlencode($bd_id) . "&bd_name=" . urlencode($bd_name) . "&page=" . $page;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
// 成功 → 回列表；失敗 → 回上一頁保留表單內容
alertHref($msg, $ok ? $web_url : "");
