<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pagesize = 20;

$argument = "";   // 分頁連結額外參數（目前沒有搜尋條件）
$trace    = 0;
$order    = " ORDER BY `no` DESC";
$where    = "";
$params   = array();

$catClass = new DBClass('billboard_cat', $page, $pagesize);
$result   = $catClass->getAdminList($where, $order, $trace, $params);
$catList  = $result['data'];
$total    = $result['total'];

// 編輯 / 新增 時帶給下一頁的參數
$sh_post = "&page=" . $page . "&pagesize=" . $pagesize . "&total=" . $total;

// 只允許安全的連結（擋掉 javascript: 等）
function safeUrl($url) {
  $url = trim((string)$url);
  if ($url === '' || preg_match('/^\s*(javascript|data|vbscript)\s*:/i', $url)) {
    return '';
  }
  return $url;
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript">
  var sh_post = <?php echo json_encode($sh_post, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

  function onEdit(no) {
    window.location.href = "edit.php?cms_mode=edit&no=" + encodeURIComponent(no) + sh_post;
  }
  function onAdd() {
    window.location.href = "edit.php?cms_mode=add" + sh_post;
  }
  // 進入該類別底下的廣告列表
  function onSub(no, name) {
    window.location.href = "../billboard/list.php?bd_id=" + encodeURIComponent(no) + "&bd_name=" + encodeURIComponent(name);
  }
</script>
</head>
<body>
<div class="layout">
  <div class="layout_header"><?php require_once("../header.php"); ?></div>
  <div class="layout_body">
    <div class="layout_sidebar"><?php require_once("../menu.php"); ?></div>
    <div class="layout_content">
      <div class="panel panel_sm">
        <div class="panel_head tb_menu_title">
          <div>版面管理</div>
          <div><input type="button" value="新增廣告" onClick="onAdd()" /></div>
        </div>
        <div class="panel_body">
<div class="list_scroll">
  <div class="list_table" style="--cols:70px 1fr 110px 120px;">
    <div class="list_row list_head">
      <div class="list_cell tb_list_title">序號</div>
      <div class="list_cell tb_list_title">廣告名稱</div>
      <div class="list_cell tb_list_title">編輯子類別</div>
      <div class="list_cell tb_list_title">編輯</div>
    </div>
    <?php
    $no = ($page - 1) * $pagesize;
    foreach ($catList as $list_info) {
      $no++;
      $no_js   = h(json_encode((string)$list_info['no']));
      $name_js = h(json_encode((string)$list_info['name']));
      $link    = safeUrl($list_info['web_url']);
    ?>
    <div class="list_row">
      <div class="list_cell text_1"><?php echo $no; ?></div>
      <div class="list_cell al_left text_1"><?php echo h($list_info['name']); ?>
        <?php if ($link !== '') { ?>
        <input type="button" class="btn_text" value="連結" onClick="window.open(<?php echo h(json_encode($link)); ?>, '_blank');" />
        <?php } ?>
      </div>
      <div class="list_cell text_1"><input type="button" class="btn_text" value="編輯子類別" onClick="onSub(<?php echo $no_js; ?>, <?php echo $name_js; ?>)" /></div>
      <div class="list_cell text_1"><input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $no_js; ?>)" /></div>
    </div>
    <?php } ?>
    <?php if (empty($catList)) { ?>
    <div class="list_row"><div class="list_cell list_span text_1">目前沒有資料</div></div>
    <?php } ?>
  </div>
</div>
<div class="page pager"><span class="wd_white_12">
  <?php echo $catClass->showPageMenu($argument); ?>
</span></div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
