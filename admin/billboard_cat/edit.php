<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$no       = isset($_REQUEST['no']) ? trim($_REQUEST['no']) : '';
$cms_mode = isset($_REQUEST['cms_mode']) ? trim($_REQUEST['cms_mode']) : ($no === '' ? 'add' : 'edit');
$page     = isset($_REQUEST['page']) ? max(1, (int)$_REQUEST['page']) : 1;
$sh_post  = $_SERVER['QUERY_STRING'];

// 新增模式預設值
$billboardcat = array('no' => '', 'name' => '', 'web_url' => '', 'ps' => '');

if ($no !== '') {
  $stmt = $pdo->prepare("SELECT * FROM `billboard_cat` WHERE `no` = ?");
  $stmt->execute(array($no));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $billboardcat = array_merge($billboardcat, $row);
  }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
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
          <div>版面管理</div>
          <div><input type="button" value="回上一頁" onClick="window.history.go(-1);" /></div>
        </div>
        <div class="panel_body">
<form action="save.php?<?php echo h($sh_post); ?>" method="post" id="form1" name="CodeForm" onSubmit="return validator(this)">
  <div class="edit_table" style="width:66%;">
<div class="edit_row">
  <div class="edit_title tb_edit_title">名稱</div>
  <div class="edit_info tb_edit_info"><input name="name" type="text" value="<?php echo h($billboardcat['name']); ?>" size="50" maxlength="50"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">網址</div>
  <div class="edit_info tb_edit_info"><input name="web_url" type="text" value="<?php echo h($billboardcat['web_url']); ?>" size="50"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">備註</div>
  <div class="edit_info tb_edit_info"><textarea name="ps" id="ps" cols="45" rows="5"><?php echo h($billboardcat['ps']); ?></textarea></div>
</div>
    <div class="edit_actions">
      <input type="hidden" value="<?php echo h($billboardcat['no']); ?>" name="no" id="no" />
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
