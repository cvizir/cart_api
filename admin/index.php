<?php
require_once(dirname(__FILE__) . "/../include/config.inc.php");
require_once(dirname(__FILE__) . "/../include/function.php");
require_once(dirname(__FILE__) . "/auth.inc.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

adminSessionStart();

// 已經登入就直接進後台
if (!empty($_SESSION['admin_id'])) {
  header("Location: main.php");
  exit;
}

$csrf = adminCsrfToken();
header("Content-Type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo h(ADMIN_TITLE); ?></title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
  <div class="login_topbar"></div>
  <div class="login_board">
    <form id="form1" name="form1" method="post" action="login.php" autocomplete="off" class="login_form">
      <input type="hidden" name="csrf_token" value="<?php echo h($csrf); ?>" />
      <div class="index_title"><?php echo h(ADMIN_TITLE); ?></div>
      <div class="login_row">
        <label for="admin_id" class="index_input_text">ID：</label>
        <input name="admin_id" type="text" id="admin_id" />
      </div>
      <div class="login_row">
        <label for="admin_psw" class="index_input_text">PASSWORD：</label>
        <input name="admin_psw" type="password" id="admin_psw" />
      </div>
      <hr />
      <div class="login_actions">
        <input name="Submit" type="submit" class="form_button" value="LOGIN" />
      </div>
    </form>
  </div>
</body>
</html>
