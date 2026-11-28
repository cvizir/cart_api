<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pagesize = 5;

$argument = "";   // 分頁連結額外參數（目前沒有搜尋條件）
$trace    = 0;
$params   = array();
$where    = "";
$order    = " ORDER BY `m_sort` ASC, `no` DESC";

$qaClass = new DBClass('qa', $page, $pagesize);
$result  = $qaClass->getAdminList($where, $order, $trace, $params);
$qaList  = $result['data'];
$total   = $result['total'];

// 編輯 / 刪除 / 新增時帶給 save.php 的參數
$sh_post = "&page=" . $page . "&pagesize=" . $pagesize . "&total=" . $total . $argument;
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script type="text/JavaScript">
  var sh_post = <?php echo json_encode($sh_post, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

  function onDel(code, name) {
    if (window.confirm("確定要刪除 [ " + name + " ] 這筆資料嗎?")) {
      window.location.href = "save.php?cms_mode=del&qa_code=" + encodeURIComponent(code) + sh_post;
      return true;
    }
    return false;
  }
  function onEdit(code) {
    window.location.href = "edit.php?cms_mode=edit&qa_code=" + encodeURIComponent(code) + sh_post;
  }
  function onAdd() {
    window.location.href = "edit.php?cms_mode=add" + sh_post;
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
          <div>Q&amp;A管理</div>
          <div><input name="Submit" type="button" value="新增問題" onClick="onAdd()" /></div>
        </div>
        <div class="panel_body">
<div class="list_scroll">
  <div class="list_table" style="--cols:70px 1fr 100px 190px;">
    <div class="list_row list_head">
      <div class="list_cell tb_list_title">序號</div>
      <div class="list_cell tb_list_title">問題</div>
      <div class="list_cell tb_list_title">上下架</div>
      <div class="list_cell tb_list_title">編輯</div>
    </div>
    <?php
    $no = ($page - 1) * $pagesize;
    foreach ($qaList as $list_info) {
      $no++;
      $code_js = h(json_encode((string)$list_info['qa_code']));
      $name_js = h(json_encode((string)$list_info['question']));
    ?>
    <div class="list_row">
      <div class="list_cell tb_list_info"><?php echo $no; ?></div>
      <div class="list_cell tb_list_info"><?php echo h($list_info['question']); ?></div>
      <div class="list_cell tb_list_info"><?php echo getIshow($list_info['ishow']); ?></div>
      <div class="list_cell tb_list_info">
        <input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $code_js; ?>)" />
        <input type="button" class="btn_text" value="刪除" onClick="onDel(<?php echo $code_js; ?>, <?php echo $name_js; ?>)" />
      </div>
    </div>
    <?php } ?>
    <?php if (empty($qaList)) { ?>
    <div class="list_row"><div class="list_cell list_span tb_list_info">目前沒有資料</div></div>
    <?php } ?>
  </div>
</div>
<div class="page pager"><span class="wd_white_12">
  <?php echo $qaClass->showPageMenu($argument); ?>
</span></div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
