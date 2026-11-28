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
$bd_name  = isset($_REQUEST['bd_name']) ? trim($_REQUEST['bd_name']) : '';
$bd_id    = isset($_REQUEST['bd_id']) ? trim($_REQUEST['bd_id']) : '';

// 分頁連結參數
$argument = "&bd_name=" . urlencode($bd_name) . "&bd_id=" . urlencode($bd_id);
$trace    = 0;
$order    = " ORDER BY `no` DESC";
$where    = " WHERE `bd_id` = ?";
$params   = array($bd_id);

$billboardClass = new DBClass('billboard', $page, $pagesize);
$result         = $billboardClass->getAdminList($where, $order, $trace, $params);
$billboardList  = $result['data'];
$total          = $result['total'];

// 編輯 / 刪除 / 新增 時帶給下一頁的參數
$sh_post = $argument . "&page=" . $page . "&pagesize=" . $pagesize . "&total=" . $total;

$billboard_ishow = array('0' => '下架', '1' => '上架', '3' => '依時間上下架');

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

  function onDel(no, name) {
    if (window.confirm("確定要刪除 [ " + name + " ] 這筆資料嗎?")) {
      window.location.href = "save.php?cms_mode=del&no=" + encodeURIComponent(no) + sh_post;
      return true;
    }
    return false;
  }
  function onEdit(no) {
    window.location.href = "edit.php?cms_mode=edit&no=" + encodeURIComponent(no) + sh_post;
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
          <div><?php echo h($bd_name); ?>-版面管理</div>
          <div><input type="button" value="回主目錄" onClick="window.location.href='../billboard_cat/list.php'" />
          <input type="button" value="新增廣告" onClick="onAdd()" /></div>
        </div>
        <div class="panel_body">
<div class="list_scroll">
  <div class="list_table" style="--cols:70px 1fr 100px 190px;">
    <div class="list_row list_head">
      <div class="list_cell tb_list_title">序號</div>
      <div class="list_cell tb_list_title">名稱</div>
      <div class="list_cell tb_list_title">上下架</div>
      <div class="list_cell tb_list_title">編輯</div>
    </div>
    <?php
    foreach ($billboardList as $list_info) {
      $no_js   = h(json_encode((string)$list_info['no']));
      $name_js = h(json_encode((string)$list_info['name']));
      $link    = safeUrl($list_info['web_url']);
      $ishow   = isset($billboard_ishow[$list_info['ishow']]) ? $billboard_ishow[$list_info['ishow']] : '';
    ?>
    <div class="list_row">
      <div class="list_cell text_1"><?php echo h($list_info['no']); ?></div>
      <div class="list_cell text_1"><?php echo h($list_info['name']); ?>
        <?php if ($link !== '') { ?>
        <input type="button" class="btn_text" value="連結" onClick="window.open(<?php echo h(json_encode($link)); ?>, '_blank');" />
        <?php } ?>
      </div>
      <div class="list_cell text_1"><?php echo h($ishow); ?></div>
      <div class="list_cell text_1">
        <input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $no_js; ?>)" />
        <?php if ($list_info['del_sw'] == '0') { ?>
        <input type="button" class="btn_text" value="刪除" onClick="onDel(<?php echo $no_js; ?>, <?php echo $name_js; ?>)" />
        <?php } ?>
      </div>
    </div>
    <?php } ?>
    <?php if (empty($billboardList)) { ?>
    <div class="list_row"><div class="list_cell list_span text_1">目前沒有資料</div></div>
    <?php } ?>
  </div>
</div>
<div class="page pager"><span class="wd_white_12">
  <?php echo $billboardClass->showPageMenu($argument); ?>
</span></div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
