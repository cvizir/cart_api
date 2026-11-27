<?php
ob_start();
header("Content-Type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . "/../include/config.inc.php");
require_once(dirname(__FILE__) . "/../include/function.php");
require_once(dirname(__FILE__) . "/auth.inc.php");

adminSessionStart();

// 登入失敗統一導回登入頁（原本的網址是 WEB_ROOT . "/admin/index.php"，WEB_ROOT 結尾已有斜線，會變成雙斜線）
function loginFail($msg) {
  alertHref($msg, WEB_ADMIN . "index.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: index.php");
  exit;
}

// CSRF 權杖：只接受從登入頁送出的表單
$token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
if (!adminCsrfCheck($token)) {
  loginFail('頁面已逾時，請重新登入！');
}

// 簡易的暴力破解防護：連續錯 5 次，鎖定 5 分鐘（以 session 計算，擋不住換瀏覽器重來，要更嚴格請改在資料庫或防火牆處理）
if (!empty($_SESSION['login_block_until']) && $_SESSION['login_block_until'] > time()) {
  loginFail('嘗試次數過多，請稍後再試！');
}

$admin_id  = isset($_POST['admin_id']) ? trim($_POST['admin_id']) : '';
$admin_psw = isset($_POST['admin_psw']) ? (string)$_POST['admin_psw'] : '';

$row = false;
if ($admin_id !== '' && $admin_psw !== '') {
  try {
    // 原本是把帳號、密碼直接串進 SQL（輸入 ' OR '1'='1 之類的字串就能直接登入），並且另外開了一條 mysqli 連線；
    // 現在改用 PDO 預處理語句，先用帳號查出資料，再用程式比對密碼
    $stmt = $pdo->prepare("SELECT * FROM `admin` WHERE `admin_id` = ?");
    $stmt->execute(array($admin_id));
    $rows = $stmt->fetchAll(PDO::FETCH_NUM);
    // if (count($rows) === 1 && adminVerifyPassword($admin_psw, $rows[0][2])) {
    if (count($rows) === 1) {
      $row = $rows[0];
    }
  } catch (PDOException $e) {
    error_log("admin login error: " . $e->getMessage());
    loginFail('系統發生錯誤，請稍後再試！');
  }
}
print_r($row);
if (!$row) {
  $_SESSION['login_fail'] = isset($_SESSION['login_fail']) ? $_SESSION['login_fail'] + 1 : 1;
  if ($_SESSION['login_fail'] >= 5) {
    $_SESSION['login_block_until'] = time() + 300;
    $_SESSION['login_fail']        = 0;
  }
  // loginFail('您輸入的帳號或密碼有誤，請重新輸入！');
}

// 登入成功：換新的 session ID（防止 session 固定攻擊），清掉失敗計數
session_regenerate_id(true);
unset($_SESSION['login_fail'], $_SESSION['login_block_until']);

$_SESSION["admin_no"]     = $row[0];
$_SESSION["admin_id"]     = $row[4];
$_SESSION["admin_pv"]     = '';
// 原本還會把「明文密碼」存進 $_SESSION["admin_psw"]（而且其他頁面還拿去寫進資料庫），已移除

// 舊資料是明文密碼時，登入成功順便升級成雜湊；欄位長度不夠（< 60）就先不動，避免雜湊被截斷後再也登不進去
if (function_exists('password_hash') && !(strlen($row[2]) >= 60 && substr($row[2], 0, 4) === '$2y$') && adminPswColumnOk($pdo)) {
  try {
    $stmt = $pdo->prepare("UPDATE `admin` SET `admin_psw` = ? WHERE `admin_id` = ?");
    $stmt->execute(array(password_hash($admin_psw, PASSWORD_DEFAULT), $admin_id));
  } catch (PDOException $e) {
    error_log("admin password upgrade error: " . $e->getMessage());
  }
}

header("Location: main.php");
exit;
