<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$page         = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pdcat_m_name = isset($_REQUEST['pdcat_m_name']) ? trim($_REQUEST['pdcat_m_name']) : '';
$pdcat_m_code = isset($_REQUEST['pdcat_m_code']) ? trim($_REQUEST['pdcat_m_code']) : '';
$pdcat_t_name = isset($_REQUEST['pdcat_t_name']) ? trim($_REQUEST['pdcat_t_name']) : '';
$pdcat_t_code = isset($_REQUEST['pdcat_t_code']) ? trim($_REQUEST['pdcat_t_code']) : '';

$pagesize = 20;

// 上層類別參數（分頁連結、編輯、新增都要帶）
$argument = "&pdcat_t_code=" . urlencode($pdcat_t_code)
          . "&pdcat_t_name=" . urlencode($pdcat_t_name)
          . "&pdcat_m_code=" . urlencode($pdcat_m_code)
          . "&pdcat_m_name=" . urlencode($pdcat_m_name);
$trace  = 0;
$order  = " ORDER BY `m_sort` ASC";
$where  = " WHERE `pdcat_m_code` = ?";
$params = array($pdcat_m_code);

$catClass = new DBClass('pdcat', $page, $pagesize);
$result   = $catClass->getAdminList($where, $order, $trace, $params);
$catList  = $result['data'];
$total    = $result['total'];

// 編輯 / 刪除 / 新增 時帶給下一頁的參數（多了頁碼資訊）
$sh_post = $argument . "&page=" . $page . "&pagesize=" . $pagesize . "&total=" . $total;

// 麵包屑連結
$url_t = "../pdcat_t/list.php";
$url_m = "../pdcat_m/list.php?pdcat_t_name=" . urlencode($pdcat_t_name) . "&pdcat_t_code=" . urlencode($pdcat_t_code);
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
      window.location.href = "save.php?cms_mode=del&pdcat_code=" + encodeURIComponent(code) + sh_post;
      return true;
    }
    return false;
  }
  function onEdit(code) {
    window.location.href = "edit.php?cms_mode=edit&pdcat_code=" + encodeURIComponent(code) + sh_post;
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
          <div>商品類別
          <?php
          if ($pdcat_t_name != '' && $pdcat_t_code != '') {
            echo '<a href="' . h($url_t) . '"> &gt; ' . h($pdcat_t_name) . '</a>';
          }
          if ($pdcat_m_name != '' && $pdcat_m_code != '') {
            echo '<a href="' . h($url_m) . '"> &gt; ' . h($pdcat_m_name) . '</a>';
          }
          ?></div>
          <div><input type="button" value="回上一層" onClick="window.location.href=<?php echo h(json_encode($url_m)); ?>" />
          <input type="button" value="新增" onClick="onAdd()" /></div>
        </div>
        <div class="panel_body">
<div class="list_scroll">
  <div class="list_table" style="--cols:70px 1fr 90px 170px;">
    <div class="list_row list_head">
      <div class="list_cell tb_list_title">序號</div>
      <div class="list_cell tb_list_title al_left">商品名稱</div>
      <div class="list_cell tb_list_title">上下架</div>
      <div class="list_cell tb_list_title">編輯</div>
    </div>
    <?php
    $no = ($page - 1) * $pagesize;
    foreach ($catList as $list_info) {
      $no++;
      $code_js = h(json_encode((string)$list_info['pdcat_code']));
      $name_js = h(json_encode((string)$list_info['name']));
    ?>
    <div class="list_row">
      <div class="list_cell tb_list_info"><?php echo $no; ?></div>
      <div class="list_cell tb_list_info al_left"><?php echo h($list_info['name']); ?></div>
      <div class="list_cell tb_list_info"><?php echo getIshow($list_info['ishow']); ?></div>
      <div class="list_cell tb_list_info">
        <input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $code_js; ?>)" />
        <input type="button" class="btn_text" value="刪除" onClick="onDel(<?php echo $code_js; ?>, <?php echo $name_js; ?>)" />
      </div>
    </div>
    <?php } ?>
    <?php if (empty($catList)) { ?>
    <div class="list_row"><div class="list_cell list_span tb_list_info">目前沒有資料</div></div>
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
