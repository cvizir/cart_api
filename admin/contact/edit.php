<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$contact_code = isset($_REQUEST['contact_code']) ? trim($_REQUEST['contact_code']) : '';
$cms_mode     = isset($_REQUEST['cms_mode']) ? $_REQUEST['cms_mode'] : 'add';
$page         = isset($_REQUEST['page']) ? max(1, (int)$_REQUEST['page']) : 1;
$sh_post      = $_SERVER['QUERY_STRING'];

// 新增模式預設值
$contact = array(
  'no' => '', 'contact_code' => '', 'uptime' => '', 'post_time' => '',
  'name' => '', 'subject' => '', 'email' => '', 'msg' => ''
);

if ($contact_code !== '') {
  $stmt = $pdo->prepare("SELECT * FROM `contact` WHERE `contact_code` = ?");
  $stmt->execute(array($contact_code));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $contact = array_merge($contact, $row);
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
      alert('請輸入姓名');
      f.name.focus();
      return false;
    }
    if (f.email.value !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.email.value)) {
      alert('信箱格式不正確');
      f.email.focus();
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
          <div>聯絡我們管理</div>
          <div><input type="button" value="回上一頁" onClick="window.history.go(-1);" /></div>
        </div>
        <div class="panel_body">
<form action="save.php?<?php echo h($sh_post); ?>" method="post" id="form1" name="CodeForm" onSubmit="return validator(this)">
  <div class="edit_table">
<div class="edit_row">
  <div class="edit_title tb_edit_title">上次修改時間</div>
  <div class="edit_info tb_edit_info"><?php echo h($contact['uptime']); ?></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">訊息時間</div>
  <div class="edit_info tb_edit_info"><?php echo h($contact['post_time']); ?></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">姓名</div>
  <div class="edit_info tb_edit_info"><input name="name" type="text" id="name" value="<?php echo h($contact['name']); ?>" size="60"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">主題</div>
  <div class="edit_info tb_edit_info"><input name="subject" type="text" id="subject" value="<?php echo h($contact['subject']); ?>" size="60"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">信箱</div>
  <div class="edit_info tb_edit_info"><input name="email" type="text" id="email" value="<?php echo h($contact['email']); ?>" size="60"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">備註</div>
  <div class="edit_info tb_edit_info"><textarea name="msg" id="msg" cols="45" rows="5"><?php echo h($contact['msg']); ?></textarea></div>
</div>
    <div class="edit_actions">
      <input type="hidden" value="<?php echo h($contact['contact_code']); ?>" name="contact_code" id="contact_code" />
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
