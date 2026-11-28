<?php
// 原本是 require "../config.php" 再 include session.php；統一成與各模組相同的載入方式
require_once(dirname(__FILE__) . "/../include/config.inc.php");
require_once(dirname(__FILE__) . "/../include/function.php");
include_once(dirname(__FILE__) . "/session.php");   // 沒登入會直接 exit

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}
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
<div class="layout">
  <div class="layout_header">
    <?php require_once("header.php"); ?>
  </div>
  <div class="layout_body">
    <div class="layout_sidebar">
      <?php require_once("menu.php"); ?>
    </div>
    <div class="layout_content">
      <div class="welcome">
        <p>歡迎進入後台管理介面，<?php echo h(isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : ''); ?></p>
      </div>
    </div>
  </div>
</div>
</body>
</html>
