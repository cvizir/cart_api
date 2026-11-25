<?php
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");

$page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pagesize = (isset($_GET['pagesize']) && (int)$_GET['pagesize'] > 0) ? (int)$_GET['pagesize'] : 20;
$total    = isset($_GET['total']) ? (int)$_GET['total'] : 0;
$no       = isset($_GET['no']) ? trim($_GET['no']) : '';

$msg = "";

try {
  if ($no === '') {
    $msg = "缺少資料編號!!";
  } else {
    // 底下還有廣告時不允許刪除
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `billboard` WHERE `bd_id` = ?");
    $stmt->execute(array($no));
    $child = (int)$stmt->fetchColumn();

    if ($child > 0) {
      $msg = "此類別底下還有 {$child} 筆廣告，請先刪除廣告!!";
    } else {
      $stmt = $pdo->prepare("DELETE FROM `billboard_cat` WHERE `no` = ?");
      $stmt->execute(array($no));
      $msg = "刪除成功!";

      // 刪除後若目前頁已沒有資料，退回上一頁
      $total--;
      if ($page > 1 && ($page - 1) * $pagesize >= $total) {
        $page--;
      }
    }
  }
} catch (PDOException $e) {
  error_log("billboard_cat del error: " . $e->getMessage());
  $msg = "資料庫執行失敗，請稍後再試!!";
}
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
alertHref($msg, "list.php?page=" . $page);
