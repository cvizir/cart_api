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
<html>
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
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2"><?php require_once("../header.php"); ?></td>
  </tr>
  <tr>
    <td width="10%" bgcolor="#525252" align="left" valign="top"><?php require_once("../menu.php"); ?></td>
    <td width="90%" valign="top"><div align="center"><br />
      <br />
      <table width="800" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
        <tr>
          <td width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
              <td class="tb_menu_title">版面管理</td>
              <td align="right" class="tb_menu_title"><input type="button" value="回上一頁" onClick="window.history.go(-1);" /></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="300" align="center" valign="top"><table width="100%">
            <tr><td>&nbsp;</td></tr>
            <tr>
              <td align="center" valign="top">
                <form action="save.php?<?php echo h($sh_post); ?>" method="post" id="form1" name="CodeForm" onSubmit="return validator(this)">
                  <table width="66%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                    <tr>
                      <td width="19%" align="center" class="tb_edit_title">名稱</td>
                      <td width="81%" class="tb_edit_info"><input name="name" type="text" value="<?php echo h($billboardcat['name']); ?>" size="50" maxlength="50"></td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">網址</td>
                      <td class="tb_edit_info"><input name="web_url" type="text" value="<?php echo h($billboardcat['web_url']); ?>" size="50"></td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">備註</td>
                      <td class="tb_edit_info"><textarea name="ps" id="ps" cols="45" rows="5"><?php echo h($billboardcat['ps']); ?></textarea></td>
                    </tr>
                    <tr>
                      <td colspan="2" align="center">
                        <input type="hidden" value="<?php echo h($billboardcat['no']); ?>" name="no" id="no" />
                        <input type="hidden" value="<?php echo (int)$page; ?>" name="page" id="page" />
                        <input type="submit" name="button" id="button" value="<?php echo ($cms_mode == 'add') ? '新增資料' : '修改資料'; ?>">
                      </td>
                    </tr>
                  </table>
                  <br>
                  <br>
                </form>
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
