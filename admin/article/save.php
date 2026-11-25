<?php
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

function req($key) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
}

/**
 * 把上傳的圖片存成 JPG。
 * 以檔案實際內容判斷格式（只接受 JPEG / PNG）；PNG 會轉成真正的 JPG，
 * 這樣前台一律讀 article_代碼_1.jpg 就不會出問題。
 * 成功回傳 true，失敗回傳錯誤訊息字串。
 */
function saveArticleImage($tmp, $dest) {
  $info = @getimagesize($tmp);
  if (!$info || ($info['mime'] !== 'image/jpeg' && $info['mime'] !== 'image/png')) {
    return "圖片格式不支援，僅限 JPG / PNG";
  }
  $dir = dirname($dest);
  if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
    return "無法建立圖片資料夾";
  }

  if ($info['mime'] === 'image/jpeg') {
    if (!move_uploaded_file($tmp, $dest)) {
      return "圖片儲存失敗";
    }
  } else {
    $src = @imagecreatefrompng($tmp);
    if (!$src) {
      return "PNG 圖片讀取失敗";
    }
    $w   = imagesx($src);
    $h   = imagesy($src);
    $dst = imagecreatetruecolor($w, $h);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));   // 透明處補白底
    imagecopy($dst, $src, 0, 0, 0, 0, $w, $h);
    $okw = imagejpeg($dst, $dest, 90);
    imagedestroy($src);
    imagedestroy($dst);
    if (!$okw) {
      return "圖片儲存失敗";
    }
  }
  @chmod($dest, 0644);
  return true;
}

$cms_mode  = req('cms_mode');
$news_code = req('news_code');
$page      = max(1, (int)req('page'));
$pagesize  = (int)req('pagesize');
if ($pagesize < 1) { $pagesize = 25; }
$total     = (int)req('total');

$db_name = 'article';
$trace   = 0;
$now     = date("Y-m-d H:i:s");

// 權限檢查（維持原本的呼叫方式）
$check_txt  = 'article';
$check_type = $cms_mode;
checkAdmimPv($_SESSION["admin_pv"], $check_txt, $check_type, $trace);

// 上下架時間：空白時給預設值（原本的 0000-00-00 與 2020-00-00 都不是合法日期，
// 而且 2020 已經過去，會讓沒填下架日期的文章全部顯示「已結束」）
$start_time = req('start_time');
if ($start_time === '' || substr($start_time, 0, 4) === '0000') {
  $start_time = '1000-01-01 00:00:00';
}
$end_time = req('end_time');
if ($end_time === '' || substr($end_time, 0, 4) === '0000') {
  $end_time = '2100-12-30 00:00:00';
}
$wttime = (req('wttime') === '') ? $now : req('wttime');

// 只寫入 edit.php 表單上有的欄位
$sql_data = array(
  'uptime'     => $now,
  'wttime'     => $wttime,
  'author'     => req('author'),
  'name'       => req('name'),
  'web_url'    => req('web_url'),
  'summary'    => isset($_REQUEST['summary']) ? trim($_REQUEST['summary']) : '',
  'info'       => isset($_REQUEST['info']) ? $_REQUEST['info'] : '',   // CKEditor HTML
  'start_time' => $start_time,
  'end_time'   => $end_time,
  'ishow'      => req('ishow'),
);

$filePath = dirname(dirname(dirname(__FILE__))) . "/images/upload/article/";
$ok       = true;
$msg      = "";

try {
  switch ($cms_mode) {
    case 'add':
      if ($sql_data['name'] === '') {
        $ok  = false;
        $msg = "請輸入名稱!!";
        break;
      }
      $news_code = createCode($db_name, 'news_code', 11);   // 原本誤用未定義的 $tb_name
      $sql_data['news_code'] = $news_code;
      insertArray($db_name, $sql_data, $trace);
      $msg = "新增成功!";
      break;

    case 'edit':
      if ($news_code === '') {
        $ok  = false;
        $msg = "缺少文章代碼!!";
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
        $msg = "缺少文章代碼!!";
        break;
      }
      $stmt = $pdo->prepare("DELETE FROM `{$db_name}` WHERE `news_code` = ?");
      $stmt->execute(array($news_code));
      $msg = "刪除成功!!";

      // 一併刪除圖片檔
      if (preg_match('/^[A-Za-z0-9]+$/', $news_code)) {
        $img = $filePath . 'article_' . $news_code . '_1.jpg';
        if (file_exists($img)) {
          @unlink($img);
        }
      }

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
  error_log("article save error: " . $e->getMessage());
  $ok  = false;
  $msg = "資料庫執行失敗，請稍後再試!!";
}

/* ------------------------------------ 圖片處理 ---------------------------------------- */
if ($ok && ($cms_mode == 'add' || $cms_mode == 'edit') && preg_match('/^[A-Za-z0-9]+$/', $news_code)) {
  $photo_name  = 'article_' . $news_code . '_1.jpg';
  $photo_path  = $filePath . $photo_name;
  $photo_value = null;   // null 代表圖片沒有變動，不更新 photo 欄位

  $f = isset($_FILES['add_pic']) ? $_FILES['add_pic'] : null;
  $has_upload = ($f && isset($f['error'][0]) && $f['error'][0] === UPLOAD_ERR_OK && is_uploaded_file($f['tmp_name'][0]));
  $del_pic    = isset($_POST['del_pic']) ? $_POST['del_pic'] : array();

  if ($has_upload) {
    $r = saveArticleImage($f['tmp_name'][0], $photo_path);
    if ($r === true) {
      $photo_value = $photo_name;
    } else {
      $msg .= "（" . $r . "）";
    }
  } elseif (!empty($del_pic[0]) && $del_pic[0] == "1") {
    if (file_exists($photo_path)) {
      @unlink($photo_path);
    }
    $photo_value = '';
  } elseif ($f && isset($f['error'][0]) && $f['error'][0] !== UPLOAD_ERR_NO_FILE && $f['error'][0] !== UPLOAD_ERR_OK) {
    $msg .= "（圖片上傳失敗，錯誤代碼 " . (int)$f['error'][0] . "）";
  }

  if ($photo_value !== null) {
    try {
      // 原本誤寫成更新 news 資料表，已改正為 article
      $stmt = $pdo->prepare("UPDATE `{$db_name}` SET `photo` = ? WHERE `news_code` = ?");
      $stmt->execute(array($photo_value, $news_code));
    } catch (PDOException $e) {
      error_log("article photo update error: " . $e->getMessage());
      $msg .= "（圖片檔名寫入資料庫失敗）";
    }
  }
}

$web_url = "list.php?page=" . $page;
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
// 後台操作紀錄
if (function_exists('addAdminLog')) {
  $log_info = '最新消息管理 <' . $msg . '> [news_code=' . $news_code . ']';
  addAdminLog(isset($_SESSION["admin_id"]) ? $_SESSION["admin_id"] : '', $log_info);
}

// 成功 → 回列表；失敗 → 回上一頁保留表單內容
alertHref($msg, $ok ? $web_url : "");
