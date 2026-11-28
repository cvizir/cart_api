<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");
if (!$pdo instanceof PDO) {
  die("Database connection failed.");
}
// 接收參數
$pdcat_code = isset($_REQUEST["pdcat_code"]) ? $_REQUEST["pdcat_code"] : '';
$product_code = isset($_REQUEST["product_code"]) ? $_REQUEST["product_code"] : '';
$cms_mode = isset($_REQUEST["cms_mode"]) ? $_REQUEST["cms_mode"] : 'add';
$sh_post = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
$no = isset($_REQUEST['no']) ? $_REQUEST['no'] : '';
$db_name = 'product';
$trace = 0;

// 透過 PDO quote 處理條件，防止注入
$sortmax_where = ($pdcat_code == '') ? '' : " WHERE `pdcat_code` = " . $pdo->quote($pdcat_code);
$sort_max = sortMax($cms_mode, $db_name, $sortmax_where, 'no', $trace);

$product = [];
// 編輯模式時透過預處理語句取得商品資料
if ($no != "") {
  $stmt = $pdo->prepare("SELECT * FROM `product` WHERE `no` = ?");
  $stmt->execute([$no]);
  $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($cms_mode == 'add') {
  $product['pdcat_code'] = $pdcat_code;
}
if (empty($product['pd_mode'])) {
  $product['pd_mode'] = 1;
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?= ADMIN_TITLE ?></title>
  <meta http-equiv="cache-control" content="no-cache">
  <meta http-equiv="pragma" content="no-cache">
  <meta http-equiv="expires" content="0">
  <script type="text/javascript" src="../script/ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="../script/datepicker/WdatePicker.js"></script>
  <script type="text/javascript" src="../script/jquery.min.js"></script>
  <script type="text/javascript">
    <?php
    $check_txt = 'product_read';
    $all_str = isset($_SESSION["admin_pv"]) ? $_SESSION["admin_pv"] : '';
    if (function_exists('checkAdmim') && !checkAdmim($all_str, $check_txt, 0)) {
    ?>
      alert("<?= isset($pv_err_msg) ? $pv_err_msg : '無權限' ?>");
      window.location.href = "../main.php";
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
      <div class="panel panel_md">
        <div class="panel_head tb_menu_title">
          <div>產品管理</div>
          <div><input name="Submit" type="button" value="回上一頁" onClick="history.go(-1);" /></div>
        </div>
        <div class="panel_body">
<form action="save.php?<?= htmlspecialchars($sh_post) ?>" method="post" id="form1" enctype="multipart/form-data" name="CodeForm">
  <div class="edit_table">
<div class="edit_row">
  <div class="edit_title tb_edit_title">上次修改時間</div>
  <div class="edit_info tb_edit_info"><?= isset($product['uptime']) ? $product['uptime'] : '' ?></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">商品編號</div>
  <div class="edit_info tb_edit_info"><?= isset($product['product_code']) ? $product['product_code'] : '' ?></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">名稱</div>
  <div class="edit_info tb_edit_info"><input name="name" type="text" id="name" value="<?= isset($product['name']) ? htmlspecialchars($product['name']) : '' ?>" size="60"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">上架日期</div>
  <div class="edit_info tb_edit_info"><input type="text" value="<?= isset($product['release_date']) ? $product['release_date'] : '' ?>" name="release_date" id="release_date">
    <img src="../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" class="date_icon" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',el:'release_date'})"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">商品類別</div>
  <div class="edit_info tb_edit_info"><select name="pdcat_code" id="pdcat_code">
      <option value="" <?php chkSelected("", $product['pdcat_code']) ?>>全部商品</option>
      <?php
      // 使用 PDO 讀取商品分類下拉選單
      $sql_pd_cat = "SELECT * FROM `pdcat`";
      $stmt_cat = $pdo->query($sql_pd_cat);
      while ($row_pd_cat = $stmt_cat->fetch(PDO::FETCH_ASSOC)) {
      ?>
        <option value="<?php echo $row_pd_cat['pdcat_code']; ?>" <?php chkSelected($row_pd_cat['pdcat_code'], $product['pdcat_code']) ?>>
          <?php echo $row_pd_cat['name']; ?>
        </option>
      <?php } ?>
    </select></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">庫存數</div>
  <div class="edit_info tb_edit_info"><input name="pd_stock" type="text" value="<?= isset($product['pd_stock']) ? htmlspecialchars($product['pd_stock']) : '' ?>" size="60"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">原價</div>
  <div class="edit_info tb_edit_info"><input type="text" name="o_price" value="<?= isset($product['o_price']) ? htmlspecialchars($product['o_price']) : '' ?>"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">售價</div>
  <div class="edit_info tb_edit_info"><input type="text" name="price" value="<?= isset($product['price']) ? htmlspecialchars($product['price']) : '' ?>"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">上下架</div>
  <div class="edit_info tb_edit_info"><?php $ishow = isset($product['ishow']) ? $product['ishow'] : '0'; ?>
    上架<input type="radio" value="1" name="ishow" <?php chkChecked('1', $ishow); ?>>&nbsp;&nbsp;&nbsp;
    下架<input type="radio" value="0" name="ishow" <?php chkChecked('0', $ishow); ?>>&nbsp;&nbsp;&nbsp;
    待審查<input type="radio" value="9" name="ishow" <?php chkChecked('9', $ishow); ?>></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">列表排序</div>
  <div class="edit_info tb_edit_info"><select name="m_sort" id="m_sort">
      <?php getDateSelectOption('1', $sort_max, isset($product['m_sort']) ? $product['m_sort'] : 1); ?>
    </select></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">商品特色</div>
  <div class="edit_info tb_edit_info"><textarea class="ckeditor" name="pd_info" cols="60" rows="6"><?= isset($product['pd_info']) ? htmlspecialchars($product['pd_info']) : '' ?></textarea></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">&nbsp;</div>
  <div class="edit_info tb_edit_info"><script type="text/javascript">
      function clearFile(id_name) {
        var obj = document.getElementById(id_name);
        obj.value = '' //FF下
        obj.select(); //IE下
        document.execCommand('Delete');
      }
    </script>
    <div id="div_photo_upload" class="photo_grid">
      <?php
      $photo_array = isset($product['photo']) && $product['photo'] != '' ? explode(",", $product['photo']) :  ['', '', '', '', '', '', '', ''];
      $photo_ps = ['', '', '', '', '', '', '', ''];
      for ($I = 0; $I < 8; $I++) {
        if (!isset($photo_array[$I])) {
          $photo_array[$I] = '';
        }
        $filename = '../../images/upload/product/' . $photo_array[$I];
        if (!file_exists($filename) || $photo_array[$I] == '') {
          $filename = 'images/no_pic.gif';
        }
      ?>
        <div class="photo_item">
          <ul>
            <li>
              <input type="checkbox" name="del_pic[<?php echo "$I"; ?>]" value="1">
              刪除此產品圖片
              <?php if ($photo_ps[$I] <> '') {
                echo '(' . $photo_ps[$I] . ')';
              } ?>
            </li>
            <li>
              <input id="input_file_<?php echo "$I"; ?>" name="add_pic[<?php echo "$I"; ?>]" type="file" size="19">
              <input name="Submit2" type="button" value="清除" onClick="clearFile('input_file_<?php echo "$I"; ?>');" />
            </li>
            <li><img src="<?php echo "$filename";  ?>" alt="" width="100%"></li>
          </ul>
        </div>
      <?php } ?>
    </div></div>
</div>
    <div class="edit_actions">
      <input type="hidden" value="<?= isset($product['no']) ? $product['no'] : '' ?>" name="no" />
      <input type="hidden" value="<?= isset($product['m_sort']) ? $product['m_sort'] : '' ?>" name="sort_old_value" />
      <input type="hidden" value="<?= isset($product['product_code']) ? $product['product_code'] : '' ?>" name="product_code" />
      <input type="hidden" value="<?= isset($product['photo']) ? $product['photo'] : '' ?>" name="photo" />
      <input type="hidden" value="<?= isset($page) ? $page : 1 ?>" name="page" />
      <input type="submit" name="button" value="<?= ($cms_mode == 'add') ? '新增資料' : '修改資料' ?>">
    </div>
  </div>
</form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
