<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$qa_code  = isset($_REQUEST['qa_code']) ? trim($_REQUEST['qa_code']) : '';
$cms_mode = isset($_REQUEST['cms_mode']) ? $_REQUEST['cms_mode'] : 'add';
$page     = isset($_REQUEST['page']) ? max(1, (int)$_REQUEST['page']) : 1;
$sh_post  = $_SERVER['QUERY_STRING'];

// 新增模式預設值（排序預設排在最後）
$qa = array(
  'qa_code' => '', 'uptime' => '', 'cat_id' => 'b', 'question' => '',
  'ans' => '', 'ishow' => '1', 'm_sort' => sortMax('add', 'qa', '')
);

if ($qa_code !== '') {
  $stmt = $pdo->prepare("SELECT * FROM `qa` WHERE `qa_code` = ?");
  $stmt->execute(array($qa_code));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $qa = array_merge($qa, $row);
  }
}
$sort_end = max(99, (int)$qa['m_sort']);
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<script type="text/javascript" src="../script/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../script/datepicker/WdatePicker.js"></script>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
  function validator(f) {
    if (f.question.value.replace(/\s/g, '') === '') {
      alert('請輸入問題');
      f.question.focus();
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
          <div>Q&amp;A管理</div>
          <div><input type="button" value="回上一頁" onClick="window.history.go(-1);" /></div>
        </div>
        <div class="panel_body">
<form action="save.php?<?php echo h($sh_post); ?>" method="post" id="form1" name="CodeForm" onSubmit="return validator(this)">
  <div class="edit_table">
<div class="edit_row">
  <div class="edit_title tb_edit_title">最後修改時間</div>
  <div class="edit_info"><input type="text" value="<?php echo h($qa['uptime']); ?>" name="uptime" id="uptime">
    <img src="../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" class="date_icon" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'uptime'})">
    <span style="color:#888;">（留空則自動使用目前時間）</span></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">類型</div>
  <div class="edit_info tb_edit_info"><label><input name="cat_id" type="radio" value="b" <?php chkChecked('b', $qa['cat_id']); ?>>購物問題</label>&nbsp;&nbsp;&nbsp;
    <label><input name="cat_id" type="radio" value="m" <?php chkChecked('m', $qa['cat_id']); ?>>會員問題</label></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">問題</div>
  <div class="edit_info tb_edit_info"><input name="question" type="text" id="question" value="<?php echo h($qa['question']); ?>" size="60"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">答覆</div>
  <div class="edit_info tb_edit_info"><textarea class="ckeditor" id="ans" name="ans" cols="" rows=""><?php echo h($qa['ans']); ?></textarea></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">上下架</div>
  <div class="edit_info tb_edit_info"><label>上架<input name="ishow" type="radio" value="1" <?php chkChecked('1', $qa['ishow']); ?>></label>&nbsp;&nbsp;&nbsp;
    <label>下架<input name="ishow" type="radio" value="0" <?php chkChecked('0', $qa['ishow']); ?>></label></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">排序</div>
  <div class="edit_info tb_edit_info"><select name="m_sort" id="m_sort">
      <?php getDateSelectOption(1, $sort_end, $qa['m_sort']); ?>
    </select></div>
</div>
    <div class="edit_actions">
      <input type="hidden" value="<?php echo h($qa['qa_code']); ?>" name="qa_code" id="qa_code" />
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
