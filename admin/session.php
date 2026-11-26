<?php
/**
 * 後台登入檢查：每個後台頁面最上面都會 include 這支。
 *
 * 原本的寫法只是輸出一段「請先登入」的 JavaScript，後面的程式碼卻會繼續執行。
 * JavaScript 只有瀏覽器才會照做，直接用程式送出請求（curl 等）的人根本不受影響，
 * 結果是沒登入也能執行各模組的 save.php（新增、修改、刪除資料）。現在檢查失敗會立刻 exit。
 */
if (!defined('WEB_ADMIN')) {
  // 有些頁面是先 include 這支、才 include config.inc.php，所以這裡自己確保設定已載入
  require_once(dirname(__FILE__) . '/../include/config.inc.php');
}
require_once(dirname(__FILE__) . '/auth.inc.php');

adminSessionStart();

if (empty($_SESSION['admin_id'])) {
  if (!headers_sent()) {
    header('Content-Type: text/html; charset=utf-8');
  }
  echo '<script type="text/javascript">alert("請先登入"); top.location.href=' . json_encode(WEB_ADMIN) . ';</script>';
  exit;
}

/* ------------------------------------------------------------------------------
 * 權限相關（各模組呼叫的 checkAdmimPv / addAdminLog / $admin_pv_check）
 *
 * 這三個東西在你提供的檔案裡都找不到定義，直接呼叫會因為函式不存在而出現致命錯誤，
 * 所以在這裡補上。預設「不啟用權限控管」（所有登入的管理員都有完整權限），
 * 與原本畫面上 `!$admin_pv_check || ...` 的判斷方式一致。
 * 之後要啟用時，把 $admin_pv_check 設成 true，並讓登入時把權限字串寫進 $_SESSION['admin_pv']
 * （以逗號分隔，例如 "orderlist_et,news_et"）。
 * ------------------------------------------------------------------------------ */
if (!isset($admin_pv_check)) {
  $admin_pv_check = false;
}
if (!isset($_SESSION['admin_pv'])) {
  $_SESSION['admin_pv'] = '';
}

if (!function_exists('checkAdmimPv')) {
  /**
   * 檢查操作權限：新增 / 修改 / 刪除需要「模組名稱_et」權限，讀取不限制。
   * 注意：文章模組呼叫時用的是 'article'，但畫面上判斷的是 'news_et'，兩邊的名稱要自行統一。
   */
  function checkAdmimPv($pv, $check_txt, $check_type, $trace = 0) {
    global $admin_pv_check;
    if (empty($admin_pv_check)) {
      return true;
    }
    if (in_array($check_type, array('add', 'edit', 'del'), true) && !strExist((string)$pv, $check_txt . '_et')) {
      echo '<script type="text/javascript">alert("您沒有編輯的權限"); history.back();</script>';
      exit;
    }
    return true;
  }
}

if (!function_exists('addAdminLog')) {
  /** 後台操作紀錄：先寫進伺服器的 error log，之後要存資料庫再改這裡 */
  function addAdminLog($admin_id, $info) {
    error_log('[admin-log] ' . $admin_id . ' ' . $info);
  }
}
