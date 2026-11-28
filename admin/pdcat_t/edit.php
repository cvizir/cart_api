<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$pdcat_t_code = isset($_REQUEST['pdcat_t_code']) ? trim($_REQUEST['pdcat_t_code']) : '';
$cms_mode     = isset($_REQUEST['cms_mode']) ? trim($_REQUEST['cms_mode']) : ($pdcat_t_code === '' ? 'add' : 'edit');
$page         = isset($_REQUEST['page']) ? max(1, (int)$_REQUEST['page']) : 1;
$sh_post      = $_SERVER['QUERY_STRING'];
$trace        = 0;

// 新增模式預設值
$pd_cat = array(
  'no' => '', 'pdcat_t_code' => '', 'uptime' => '', 'name' => '', 'url' => '',
  'link_type' => '', 'ishow' => '1', 'm_sort' => ''
);

if ($pdcat_t_code !== '') {
  $stmt = $pdo->prepare("SELECT * FROM `pdcat_t` WHERE `pdcat_t_code` = ?");
  $stmt->execute(array($pdcat_t_code));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $pd_cat = array_merge($pd_cat, $row);
  }
}

// 排序最大值：新增時 = 筆數 + 1，編輯時 = 筆數（原本的 $sql_where 沒定義）
$sort_max      = sortMax($cms_mode, 'pdcat_t', '', 'no', $trace);
$sort_selected = ($pd_cat['m_sort'] === '' || $pd_cat['m_sort'] === null) ? $sort_max : $pd_cat['m_sort'];
$sort_end      = max((int)$sort_max, (int)$sort_selected);

if ($trace == 1) {
  echo '$sort_max=' . h($sort_max) . '<br>';
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<script type="text/javascript" src="../script/ckeditor/ckeditor.js"></script>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
  function validator(f) {
    if (f.name.value.replace(/\s/g, '') === '') {
      alert('請輸入名稱');
      f.name.focus();
      return false;
    }
    return true;
  }
</script>
</head>
<body>
<div class="layout">
  <div class="layout_header"><?php require_once("../header.php"); ?></div>
  <div class="layout_body">
    <div class="layout_sidebar"><?php require_once("../menu.php"); ?></div>
    <div class="layout_content">
      <div class="panel panel_md">
        <div class="panel_head tb_menu_title">
          <div>商品類別</div>
          <div><input type="button" value="回上一頁" onClick="window.history.go(-1);" /></div>
        </div>
        <div class="panel_body">
<form action="save.php?<?php echo h($sh_post); ?>" method="post" id="form1" name="CodeForm" onSubmit="return validator(this)">
  <div class="edit_table">
<div class="edit_row">
  <div class="edit_title tb_edit_title">上次修改時間</div>
  <div class="edit_info tb_edit_info"><?php echo h($pd_cat['uptime']); ?></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">名稱</div>
  <div class="edit_info tb_edit_info"><input name="name" type="text" id="name" value="<?php echo h($pd_cat['name']); ?>" size="60"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">說明</div>
  <div class="edit_info tb_edit_info"><textarea class="ckeditor" name="url" cols="60" rows="6"><?php echo h($pd_cat['url']); ?></textarea></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">連結方式</div>
  <div class="edit_info tb_edit_info"><label>預設<input type="radio" value="" name="link_type" <?php chkChecked('', $pd_cat['link_type']); ?>></label>
    &nbsp;&nbsp;&nbsp;
    <label>連結<input type="radio" value="_self" name="link_type" <?php chkChecked('_self', $pd_cat['link_type']); ?>></label>
    &nbsp;&nbsp;&nbsp;
    <label>連結另開<input type="radio" value="_blank" name="link_type" <?php chkChecked('_blank', $pd_cat['link_type']); ?>></label></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">上下架</div>
  <div class="edit_info tb_edit_info"><label>上架<input type="radio" value="1" name="ishow" <?php chkChecked('1', $pd_cat['ishow']); ?>></label>
    &nbsp;&nbsp;&nbsp;
    <label>下架<input type="radio" value="0" name="ishow" <?php chkChecked('0', $pd_cat['ishow']); ?>></label></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">排序</div>
  <div class="edit_info tb_edit_info"><select name="m_sort" id="m_sort">
      <?php getDateSelectOption(1, $sort_end, $sort_selected); ?>
    </select></div>
</div>
    <div class="edit_actions">
      <input type="hidden" value="<?php echo h($pd_cat['no']); ?>" name="no" id="no" />
      <input type="hidden" value="<?php echo h($pd_cat['pdcat_t_code']); ?>" name="pdcat_t_code" id="pdcat_t_code" />
      <input type="hidden" value="<?php echo (int)$page; ?>" name="page" id="page" />
      <input type="submit" name="button" id="button" value="<?php echo ($cms_mode == 'add') ? '新增資料' : '修改資料'; ?>">
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
