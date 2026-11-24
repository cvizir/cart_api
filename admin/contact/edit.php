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
<html>
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
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2"><?php require_once("../header.php"); ?></td>
  </tr>
  <tr>
    <td width="10%" align="left" bgcolor="#525252" valign="top"><?php require_once("../menu.php"); ?></td>
    <td width="90%" valign="top"><div align="center"><br />
      <br />
      <table width="800" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
        <tr>
          <td width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
              <td width="49%" class="tb_menu_title">聯絡我們管理</td>
              <td width="51%" align="right" class="tb_menu_title"><input type="button" value="回上一頁" onClick="window.history.go(-1);" /></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="300" align="center" valign="top"><table width="100%">
            <tr><td>&nbsp;</td></tr>
            <tr>
              <td align="center" valign="top">
                <form action="save.php?<?php echo h($sh_post); ?>" method="post" id="form1" name="CodeForm" onSubmit="return validator(this)">
                  <table width="90%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                    <tr>
                      <td width="15%" class="tb_edit_title">上次修改時間</td>
                      <td width="84%" class="tb_edit_info"><?php echo h($contact['uptime']); ?>&nbsp;</td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">訊息時間</td>
                      <td class="tb_edit_info"><?php echo h($contact['post_time']); ?>&nbsp;</td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">姓名</td>
                      <td class="tb_edit_info"><input name="name" type="text" id="name" value="<?php echo h($contact['name']); ?>" size="60"></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">主題</td>
                      <td class="tb_edit_info"><input name="subject" type="text" id="subject" value="<?php echo h($contact['subject']); ?>" size="60"></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">信箱</td>
                      <td class="tb_edit_info"><input name="email" type="text" id="email" value="<?php echo h($contact['email']); ?>" size="60"></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">備註</td>
                      <td class="tb_edit_info"><textarea name="msg" id="msg" cols="45" rows="5"><?php echo h($contact['msg']); ?></textarea></td>
                    </tr>
                    <tr>
                      <td colspan="2" align="center">
                        <input type="hidden" value="<?php echo h($contact['contact_code']); ?>" name="contact_code" id="contact_code" />
                        <input type="hidden" value="<?php echo (int)$page; ?>" name="page" id="page" />
                        <input type="submit" name="button" id="button" value="<?php echo ($cms_mode == 'add') ? '新增資料' : '修改資料'; ?>">
                      </td>
                    </tr>
                  </table>
                </form>
                <br><br>
              </td>
            </tr>
          </table></td>
        </tr>
      </table>
      <br />
    </div></td>
  </tr>
</table>
</body>
</html>
