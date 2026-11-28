<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pagesize = 25;

$argument = "";   // 分頁連結額外參數（目前沒有搜尋條件）
$trace    = 0;
$order    = " ORDER BY `ishow` DESC, `start_time` DESC ";
$where    = "";
$params   = array();

$newsClass = new DBClass('article', $page, $pagesize);
$result    = $newsClass->getAdminList($where, $order, $trace, $params);
$newsList  = $result['data'];
$total     = $result['total'];

// 編輯 / 刪除 / 新增 時帶給下一頁的參數
$sh_post = "&page=" . $page . "&pagesize=" . $pagesize . "&total=" . $total . $argument;

$can_edit = empty($admin_pv_check) || strExist($admin_pv, 'news_et');
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<script language="JavaScript" type="text/JavaScript">
  var sh_post = <?php echo json_encode($sh_post, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

  function onEdit(code) {
    window.location.href = "edit.php?cms_mode=edit&news_code=" + encodeURIComponent(code) + sh_post;
  }
  <?php if ($can_edit) { ?>
  function onDel(code, name) {
    if (window.confirm("確定要刪除 [ " + name + " ] 這筆資料嗎?")) {
      window.location.href = "save.php?cms_mode=del&news_code=" + encodeURIComponent(code) + sh_post;
      return true;
    }
    return false;
  }
  function onAdd() {
    window.location.href = "edit.php?cms_mode=add" + sh_post;
  }
  <?php } else { ?>
  function onAdd() { alert("您沒有編輯的權限"); }
  function onDel() { alert("您沒有編輯的權限"); }
  <?php } ?>
</script>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="layout">
  <div class="layout_header"><?php require_once("../header.php"); ?></div>
  <div class="layout_body">
    <div class="layout_sidebar"><?php require_once("../menu.php"); ?></div>
    <div class="layout_content">
      <div class="panel panel_lg">
        <div class="panel_head tb_menu_title">
          <div>寶貝分享區文章</div>
          <div><input name="Submit" type="button" value="新增" onClick="onAdd()" /></div>
        </div>
        <div class="panel_body">
<div class="list_scroll">
  <div class="list_table" style="--cols:55px 140px 1fr 250px 70px 165px;">
    <div class="list_row list_head">
      <div class="list_cell tb_list_title">序號</div>
      <div class="list_cell tb_list_title">撰寫日期</div>
      <div class="list_cell tb_list_title al_left">名稱</div>
      <div class="list_cell tb_list_title al_left">宣傳時間</div>
      <div class="list_cell tb_list_title">上下架</div>
      <div class="list_cell tb_list_title">編輯</div>
    </div>
    <?php
    $no  = ($page - 1) * $pagesize;
    $now = time();
    foreach ($newsList as $list_info) {
      $no++;
      $code_js = h(json_encode((string)$list_info['news_code']));
      $name_js = h(json_encode((string)$list_info['name']));

      // 沒有設定下架日期（空白或 0000-）視為永不結束
      $end_raw = (string)$list_info['end_time'];
      $ended   = false;
      if ($end_raw !== '' && substr($end_raw, 0, 4) !== '0000') {
        $end_ts = strtotime($end_raw);
        $ended  = ($end_ts !== false && $end_ts < $now);
      }
    ?>
    <div class="list_row">
      <div class="list_cell tb_list_info"><?php echo $no; ?></div>
      <div class="list_cell tb_list_info"><?php echo h($list_info['wttime']); ?></div>
      <div class="list_cell tb_list_info al_left"><?php echo h($list_info['name']); ?></div>
      <div class="list_cell tb_list_info al_left"><?php if ($ended) { echo '已結束'; } else { echo h($list_info['start_time']) . ' ~ ' . h($list_info['end_time']); } ?></div>
      <div class="list_cell tb_list_info"><?php echo getIshow($list_info['ishow']); ?></div>
      <div class="list_cell tb_list_info">
        <input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $code_js; ?>)" />
        <input type="button" class="btn_text" value="刪除" onClick="onDel(<?php echo $code_js; ?>, <?php echo $name_js; ?>)" />
      </div>
    </div>
    <?php } ?>
    <?php if (empty($newsList)) { ?>
    <div class="list_row"><div class="list_cell list_span tb_list_info">目前沒有資料</div></div>
    <?php } ?>
  </div>
</div>
<div class="page pager"><span class="wd_white_12">
  <?php echo $newsClass->showPageMenu($argument); ?>
</span></div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
