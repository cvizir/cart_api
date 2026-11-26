<?php
require_once(dirname(__FILE__) . "/../include/config.inc.php");
require_once(dirname(__FILE__) . "/auth.inc.php");

adminSessionStart();

// 清空 session 資料並刪除瀏覽器上的 session Cookie
$_SESSION = array();
if (ini_get("session.use_cookies")) {
  $p = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
}
session_destroy();

header("Content-Type: text/html; charset=utf-8");
?>
<script type="text/javascript">
  top.location.href = <?php echo json_encode(WEB_ADMIN); ?>;
</script>
