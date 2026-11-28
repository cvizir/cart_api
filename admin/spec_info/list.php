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
// 原本的排序條件被註解掉（$order 沒定義）；表單有排序欄位，所以依排序、再依編號排列，換頁順序才穩定
$order    = " ORDER BY `no` DESC";
$where    = "";
$params   = array();

$specClass = new DBClass('spec_info', $page, $pagesize);
$result    = $specClass->getAdminList($where, $order, $trace, $params);
$specList  = $result['data'];
$total     = $result['total'];

// 編輯 / 刪除 / 新增 時帶給下一頁的參數
$sh_post = "&page=" . $page . "&pagesize=" . $pagesize . "&total=" . $total . $argument;
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<script language="JavaScript" type="text/JavaScript">
  var sh_post = <?php echo json_encode($sh_post, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

  function onDel(code, name) {
    if (window.confirm("確定要刪除 [ " + name + " ] 這筆資料嗎?")) {
      window.location.href = "save.php?cms_mode=del&spec_code=" + encodeURIComponent(code) + sh_post;
      return true;
    }
    return false;
  }
  function onEdit(code) {
    window.location.href = "edit.php?cms_mode=edit&spec_code=" + encodeURIComponent(code) + sh_post;
  }
  function onAdd() {
    window.location.href = "edit.php?cms_mode=add" + sh_post;
  }
</script>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="layout">
  <div class="layout_header"><?php require_once("../header.php"); ?></div>
  <div class="layout_body">
    <div class="layout_sidebar"><?php require_once("../menu.php"); ?></div>
    <div class="layout_content">
      <div class="panel panel_md">
        <div class="panel_head tb_menu_title">
          <div>規格管理</div>
          <div><input name="Submit" type="button" value="新增" onClick="onAdd()" /></div>
        </div>
        <div class="panel_body">
<div class="list_scroll">
  <div class="list_table" style="--cols:70px 1fr 90px 170px;">
    <div class="list_row list_head">
      <div class="list_cell tb_list_title">序號</div>
      <div class="list_cell tb_list_title al_left">規格名稱</div>
      <div class="list_cell tb_list_title">上下架</div>
      <div class="list_cell tb_list_title">編輯</div>
    </div>
    <?php
    $no = ($page - 1) * $pagesize;
    foreach ($specList as $list_info) {
      $no++;
      $code_js = h(json_encode((string)$list_info['spec_code']));
      $name_js = h(json_encode((string)$list_info['text']));
    ?>
    <div class="list_row">
      <div class="list_cell tb_list_info"><?php echo $no; ?></div>
      <div class="list_cell tb_list_info al_left"><?php echo h($list_info['text']); ?></div>
      <div class="list_cell tb_list_info"><?php echo getIshow($list_info['ishow']); ?></div>
      <div class="list_cell tb_list_info">
        <input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $code_js; ?>)" />
        <input type="button" class="btn_text" value="刪除" onClick="onDel(<?php echo $code_js; ?>, <?php echo $name_js; ?>)" />
      </div>
    </div>
    <?php } ?>
    <?php if (empty($specList)) { ?>
    <div class="list_row"><div class="list_cell list_span tb_list_info">目前沒有資料</div></div>
    <?php } ?>
  </div>
</div>
<div class="page pager"><span class="wd_white_12">
  <?php echo $specClass->showPageMenu($argument); ?>
</span></div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
