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
$sh_txt   = isset($_REQUEST['sh_txt']) ? trim($_REQUEST['sh_txt']) : '';

// 上下頁 / 編輯 / 刪除 要帶的參數
$argument = "&sh_txt=" . urlencode($sh_txt);
$sh_post  = "&page=" . $page . $argument;
$trace    = 0;

$order  = " ORDER BY `no` DESC";
$where  = "";
$params = array();
if ($sh_txt !== "") {
  $where  = " WHERE `name` LIKE ? OR `email` LIKE ? ";
  $like   = '%' . $sh_txt . '%';
  $params = array($like, $like);
}

$memberClass = new DBClass('member', $page, $pagesize);
$result      = $memberClass->getAdminList($where, $order, $trace, $params);
$memberList  = $result['data'];
$total       = $result['total'];
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
      window.location.href = "save.php?cms_mode=del&total=<?php echo (int)$total; ?>&member_code=" + encodeURIComponent(code) + sh_post;
      return true;
    }
    return false;
  }
  function onEdit(code) {
    window.location.href = "edit.php?cms_mode=edit&member_code=" + encodeURIComponent(code) + sh_post;
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
          <div>會員管理</div>
          <div><input name="Submit" type="button" value="新增" onClick="onAdd()" /></div>
        </div>
        <div class="panel_body">
<div class="toolbar">
  <form name="form2" method="get" action="">
    <input name="sh_txt" type="text" value="<?php echo h($sh_txt); ?>" placeholder="姓名 / Email">
    <input type="submit" value="送出">
  </form>
  <form name="form3" method="post" action="excel_tr.php">
    <input type="hidden" name="sh_txt" value="<?php echo h($sh_txt); ?>">
    <input type="submit" value="匯出">
  </form>
</div>
<div class="list_scroll">
  <div class="list_table" style="--cols:70px 1fr 1fr 170px;">
    <div class="list_row list_head">
      <div class="list_cell tb_list_title">序號</div>
      <div class="list_cell tb_list_title al_left">會員名稱</div>
      <div class="list_cell tb_list_title al_left">信箱</div>
      <div class="list_cell tb_list_title">編輯</div>
    </div>
    <?php
    $no = ($page - 1) * $pagesize;
    foreach ($memberList as $list_info) {
      $no++;
      $code_js = h(json_encode((string)$list_info['member_code']));
      $name_js = h(json_encode((string)$list_info['name']));
    ?>
    <div class="list_row">
      <div class="list_cell tb_list_info"><?php echo $no; ?></div>
      <div class="list_cell tb_list_info al_left"><?php echo h($list_info['name']); ?></div>
      <div class="list_cell tb_list_info al_left"><?php echo h($list_info['email']); ?></div>
      <div class="list_cell tb_list_info">
        <input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $code_js; ?>)" />
        <input type="button" class="btn_text" value="刪除" onClick="onDel(<?php echo $code_js; ?>, <?php echo $name_js; ?>)" />
      </div>
    </div>
    <?php } ?>
    <?php if (empty($memberList)) { ?>
    <div class="list_row"><div class="list_cell list_span tb_list_info">沒有符合的資料</div></div>
    <?php } ?>
  </div>
</div>
<div class="page pager"><span class="wd_white_12">
  <?php echo $memberClass->showPageMenu($argument); ?>
</span></div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
