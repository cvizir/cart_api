<?php
// new.php 的功能已完全由 edit.php 取代（新增 / 修改共用同一支表單），
// 這裡只保留舊網址的相容導向，確認沒有任何連結再指向 new.php 之後可以直接刪除此檔。
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$no   = isset($_GET['no']) ? trim($_GET['no']) : '';

if ($no === '') {
  $url = "edit.php?cms_mode=add&page=" . $page;
} else {
  $url = "edit.php?cms_mode=edit&no=" . urlencode($no) . "&page=" . $page;
}

header("Location: " . $url);
exit;
