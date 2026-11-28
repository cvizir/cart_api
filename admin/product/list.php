<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");
if (!$pdo instanceof PDO) {
  die("Database connection failed.");
}
// 接收 GET 參數
$del_pd_file = isset($_GET['del_pd_file']) ? $_GET['del_pd_file'] : '';
$page = !empty($_GET["page"]) ? (int)$_GET["page"] : 1;
$pagesize = 50;
$trace = 0;
$params = [];
$where = '';

$sh_pdcat_code = isset($_REQUEST["sh_pdcat_code"]) ? $_REQUEST["sh_pdcat_code"] : '';
$sh_publishing_code = isset($_REQUEST["sh_publishing_code"]) ? $_REQUEST["sh_publishing_code"] : '';
$sh_status = isset($_REQUEST["sh_status"]) ? $_REQUEST["sh_status"] : '';
$sh_qty = isset($_REQUEST["sh_qty"]) ? $_REQUEST["sh_qty"] : '';
$sh_txt = isset($_REQUEST["sh_txt"]) ? $_REQUEST["sh_txt"] : '';

// 保留換頁查詢條件網址字串
$sh_post = '&sh_pdcat_code=' . urlencode($sh_pdcat_code) . '&sh_txt=' . urlencode($sh_txt) . '&sh_qty=' . urlencode($sh_qty) . '&sh_publishing_code=' . urlencode($sh_publishing_code) . '&sh_status=' . urlencode($sh_status);
$argument = $sh_post;
$order = " ORDER BY no DESC";

// 模糊關鍵字搜尋
if ($sh_txt != '') {
  $where = " WHERE (`product_code` LIKE ? OR `name` LIKE ? OR `pd_info` LIKE ? )";
  $params = ["%{$sh_txt}%", "%{$sh_txt}%", "%{$sh_txt}%",];
}

// 實例化分頁查詢物件
$productClass = new DBClass('product', $page, $pagesize);
$listData = $productClass->getAdminList($where, $order, $trace, $params);
$productList = $listData['data'];
$total = $listData['total'];
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?php echo ADMIN_TITLE; ?></title>
  <script type="text/javascript">
    var sh_post = '<?= $sh_post ?>';

    function onDel(no, name) {
      if (confirm("確定要刪除 [ " + name + " ] 這筆資料嗎?")) {
        window.location.href = "save.php?cms_mode=del&total=<?= $total ?>&no=" + no + sh_post;
        return true;
      }
      return false;
    }

    function onEdit(no) {
      window.location.href = "edit.php?cms_mode=edit&no=" + no + sh_post;
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
      <div class="panel panel_lg">
        <div class="panel_head tb_menu_title">
          <div>產品管理</div>
          <div><input name="Submit" type="button" value="新增" onClick="onAdd()" /></div>
        </div>
        <div class="panel_body">
<div class="toolbar">
  <form action="" method="GET">
    模糊查詢：
    <input name="sh_txt" type="text" value="<?= htmlspecialchars($sh_txt) ?>" size="46">&nbsp;&nbsp;
    <input type="submit" name="button" id="button" value="送出">
  </form>
</div>
<form action="" method="post" name="form1" id="form1">
<div class="list_scroll">
  <div class="list_table" style="--cols:70px 1fr 130px 80px 70px 80px 140px;">
    <div class="list_row list_head">
      <div class="list_cell tb_list_title al_left">No</div>
      <div class="list_cell tb_list_title al_left">商品名稱</div>
      <div class="list_cell tb_list_title al_left">商品類別</div>
      <div class="list_cell tb_list_title">價格</div>
      <div class="list_cell tb_list_title">庫存</div>
      <div class="list_cell tb_list_title">狀態</div>
      <div class="list_cell tb_list_title">編輯</div>
    </div>
    <?php
    foreach ($productList as $list_info) {
    ?>
    <div class="list_row">
      <div class="list_cell tb_list_info al_left"><?= htmlspecialchars($list_info['no']); ?></div>
      <div class="list_cell tb_list_info al_left"><?= htmlspecialchars($list_info['name']); ?></div>
      <div class="list_cell tb_list_info al_left"><?= htmlspecialchars($list_info['pdcat_code']); ?></div>
      <div class="list_cell tb_list_info"><?= htmlspecialchars($list_info['price']); ?></div>
      <div class="list_cell tb_list_info"><?= htmlspecialchars($list_info['pd_stock']); ?></div>
      <div class="list_cell tb_list_info"><?= getIshow($list_info['ishow']); ?></div>
      <div class="list_cell tb_list_info">
        <input name="Submit2" type="button" class="btn_text" value="編輯" onClick="onEdit('<?= $list_info['no']; ?>')" />
        <input name="Submit2" type="button" class="btn_text" value="刪除" onClick="onDel('<?= $list_info['no']; ?>','<?= htmlspecialchars(addslashes($list_info['name'])); ?>')" />
      </div>
    </div>
    <?php } ?>
  </div>
</div>
</form>
<div class="page pager"><span class="wd_white_12"><?= $productClass->showPageMenu($argument) ?></span></div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
