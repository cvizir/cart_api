<?php
ob_start();
header("Content-Type: text/html; charset=utf-8");
require_once(dirname(__FILE__) . "/../include/config.inc.php");
require_once(dirname(__FILE__) . "/../include/function.php");
require_once(dirname(__FILE__) . "/auth.inc.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

adminSessionStart();

/*
 * 原本這個頁面完全不用登入，任何人打開網址就能新增一個管理員帳號（等於直接拿到後台），
 * 而且輸入還直接串進 SQL、密碼以明文存放。現在：
 *   1. 資料表裡已經有管理員時，必須先登入才能新增（沒登入會被 session.php 擋下並 exit）。
 *   2. 資料表是空的（第一次建置）才允許不登入建立第一個帳號。
 */
$admin_count = (int)$pdo->query("SELECT COUNT(*) FROM `admin`")->fetchColumn();
if ($admin_count > 0) {
  include_once(dirname(__FILE__) . "/session.php");
}

$msg_err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $admin_id   = isset($_POST['admin_id']) ? trim($_POST['admin_id']) : '';
  $admin_psw  = isset($_POST['admin_psw']) ? (string)$_POST['admin_psw'] : '';
  $admin_mail = isset($_POST['admin_mail']) ? trim($_POST['admin_mail']) : '';
  $token      = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

  if (!adminCsrfCheck($token)) {
    $msg_err = '頁面已逾時，請重新整理後再試';
  } elseif ($admin_id === '' || $admin_psw === '' || $admin_mail === '') {
    $msg_err = '請完整填寫所有欄位';
  } elseif (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $admin_id)) {
    $msg_err = '帳號只能使用英數字、底線、點、連字號，長度 3～30 個字元';
  } elseif (strlen($admin_psw) < 8) {
    $msg_err = '密碼至少需要 8 個字元';
  } elseif (!filter_var($admin_mail, FILTER_VALIDATE_EMAIL)) {
    $msg_err = '請填入正確格式之EMAIL';
  } elseif (!function_exists('password_hash')) {
    $msg_err = '伺服器的 PHP 版本不支援 password_hash（需要 PHP 5.5 以上），為了避免存明文密碼，已停止新增';
  } elseif (!adminPswColumnOk($pdo)) {
    // 欄位太短，雜湊會被截斷，之後就登入不了
    $msg_err = '資料表 admin 的 admin_psw 欄位長度不足，請先執行：ALTER TABLE `admin` MODIFY `admin_psw` VARCHAR(255) NOT NULL;';
  } else {
    try {
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM `admin` WHERE `admin_id` = ?");
      $stmt->execute(array($admin_id));
      if ((int)$stmt->fetchColumn() > 0) {
        $msg_err = '帳號重複!!';
      } else {
        // 與原本一樣依欄位順序寫入（編號、帳號、密碼、信箱），編號給 NULL 讓資料庫自動產生
        $stmt = $pdo->prepare("INSERT INTO `admin` VALUES (NULL, ?, ?, ?)");
        $stmt->execute(array($admin_id, password_hash($admin_psw, PASSWORD_DEFAULT), $admin_mail));
        $go = !empty($_SESSION['admin_id']) ? 'main.php' : 'index.php';
        alertHref('管理者帳號已建立!!', $go);
        exit;
      }
    } catch (PDOException $e) {
      error_log("admin create error: " . $e->getMessage());
      $msg_err = '資料庫執行失敗，請稍後再試';
    }
  }
}

$csrf = adminCsrfToken();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>管理者帳號設定</title>
<script type="text/javascript">
function chk(f) {
  if (f.admin_mail.value !== "" && f.admin_mail.value.indexOf("@") !== -1) {
    return true;
  }
  alert("請填入正確格式之EMAIL");
  return false;
}
</script>
</head>
<body>
<form id="admin" name="admin" method="post" action="" onsubmit="return chk(this);" autocomplete="off">
  <input type="hidden" name="csrf_token" value="<?php echo h($csrf); ?>" />
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2">管理者帳號設定</td>
    </tr>
    <?php if ($msg_err !== '') { ?>
    <tr>
      <td colspan="2" style="color:#d00;"><?php echo h($msg_err); ?></td>
    </tr>
    <?php } ?>
    <tr>
      <td align="right">ID：</td>
      <td><input name="admin_id" type="text" class="form_text" id="admin_id" value="<?php echo h(isset($_POST['admin_id']) ? $_POST['admin_id'] : ''); ?>" /></td>
    </tr>
    <tr>
      <td align="right">PASSWORD：</td>
      <td><input name="admin_psw" type="password" class="form_text" id="admin_psw" /></td>
    </tr>
    <tr>
      <td align="right">E-MAIL：</td>
      <td><input type="text" name="admin_mail" class="form_text" id="admin_mail" value="<?php echo h(isset($_POST['admin_mail']) ? $_POST['admin_mail'] : ''); ?>" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="right"><input name="Submit" type="submit" class="form_button" value="建立帳號" /></td>
    </tr>
  </table>
</form>
</body>
</html>
