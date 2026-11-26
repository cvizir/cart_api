<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s)
  {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
  }
}

$member_code = isset($_REQUEST['member_code']) ? trim($_REQUEST['member_code']) : '';
$cms_mode    = isset($_REQUEST['cms_mode']) ? $_REQUEST['cms_mode'] : 'add';
$page        = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
$sh_post     = $_SERVER['QUERY_STRING'];

// 新增模式的預設值
$product = array(
  'no' => '',
  'member_code' => '',
  'uptime' => '',
  'name' => '',
  'psw' => '',
  'nickname' => '',
  'regtime' => '',
  'sex' => '0',
  'birthday' => '',
  'email' => '',
  'tel' => '',
  'phone' => '',
  'addr' => '',
  'esend' => '1',
  'cat_gp_id' => ''
);

if ($member_code !== '') {
  $stmt = $pdo->prepare("SELECT * FROM `member` WHERE `member_code` = ?");
  $stmt->execute(array($member_code));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $product = array_merge($product, $row);
  }
}
?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?php echo h(ADMIN_TITLE); ?></title>
  <script type="text/javascript" src="../script/datepicker/WdatePicker.js"></script>
  <link href="../style.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript">
    function validator(f) {
      if (f.name.value.replace(/\s/g, '') === '') {
        alert('請輸入名稱');
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
      <td width="90%" valign="top">
        <div align="center"><br />
          <br />
          <table width="800" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
            <tr>
              <td width="100%">
                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                  <tr>
                    <td width="49%" class="tb_menu_title">會員管理</td>
                    <td width="51%" align="right" class="tb_menu_title"><input type="button" value="回上一頁" onClick="window.history.go(-1);" /></td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td height="300" align="center" valign="top">
                <table width="100%">
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td align="center" valign="top">
                      <form action="save.php?<?php echo h($sh_post); ?>" method="post" id="form1" name="CodeForm" onSubmit="return validator(this)">
                        <table width="90%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                          <tr>
                            <td width="15%" class="tb_edit_title">上次修改時間</td>
                            <td width="84%" class="tb_edit_info"><?php echo h($product['uptime']); ?>&nbsp;</td>
                          </tr>
                          <tr>
                            <td class="tb_edit_title">名稱</td>
                            <td class="tb_edit_info"><input name="name" type="text" id="name" value="<?php echo h($product['name']); ?>" size="60"></td>
                          </tr>
                          <tr>
                            <td class="tb_edit_title">密碼</td>
                            <td class="tb_edit_info"><input name="psw" type="text" id="psw" value="<?php echo h($product['psw']); ?>" size="60"></td>
                          </tr>
                          <tr>
                            <td class="tb_edit_title">暱稱</td>
                            <td class="tb_edit_info"><input name="nickname" type="text" id="nickname" value="<?php echo h($product['nickname']); ?>" size="60"></td>
                          </tr>
                          <tr>
                            <td class="tb_edit_title">註冊時間</td>
                            <td class="tb_edit_info"><?php echo h($product['regtime']); ?>&nbsp;</td>
                          </tr>
                          <tr>
                            <td class="tb_edit_title">性別</td>
                            <td class="tb_edit_info">
                              <label><input type="radio" value="0" name="sex" <?php if ($product['sex'] === '0' || $product['sex'] === '' || $product['sex'] === null) echo 'checked="checked"'; ?> />女性</label>&nbsp;&nbsp;&nbsp;
                              <label><input type="radio" value="1" name="sex" <?php if ($product['sex'] === '1') echo 'checked="checked"'; ?> />男性</label>
                            </td>
                          </tr>
                          <tr>
                            <td class="tb_edit_title">生日</td>
                            <td class="tb_edit_info">
                              <input type="text" value="<?php echo h($product['birthday']); ?>" name="birthday" id="birthday">
                              <img src="../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',el:'birthday'})">
                            </td>
                          </tr>
                          <tr>
                            <td class="tb_edit_title">信箱</td>
                            <td class="tb_edit_info"><input name="email" type="text" id="email" value="<?php echo h($product['email']); ?>" size="60"></td>
                          </tr>
                          <tr>
                            <td class="tb_edit_title">電話</td>
                            <td class="tb_edit_info"><input name="tel" type="text" id="tel" value="<?php echo h($product['tel']); ?>" size="60"></td>
                          </tr>
                          <tr>
                            <td class="tb_edit_title">手機</td>
                            <td class="tb_edit_info"><input name="phone" type="text" id="phone" value="<?php echo h($product['phone']); ?>" size="60"></td>
                          </tr>
                          <tr>
                            <td class="tb_edit_title">地址</td>
                            <td class="tb_edit_info"><input name="addr" type="text" id="addr" value="<?php echo h($product['addr']); ?>" size="60"></td>
                          </tr>
                          <tr>
                            <td class="tb_edit_title">電子報</td>
                            <td class="tb_edit_info">
                              <label><input name="esend" type="radio" value="1" <?php if ($product['esend'] === '1' || $product['esend'] === '' || $product['esend'] === null) echo 'checked="checked"'; ?> />願意</label>&nbsp;&nbsp;&nbsp;
                              <label><input name="esend" type="radio" value="0" <?php if ($product['esend'] === '0') echo 'checked="checked"'; ?> />反對</label>
                            </td>
                          </tr>
                          <tr>
                            <td class="tb_edit_title">會員等級</td>
                            <td class="tb_edit_info">
                              <select name="cat_gp_id" id="cat_gp_id">
                                <option value="">請選擇</option>
                                <?php
                                $stmt_cat = $pdo->query("SELECT * FROM `cat_gp` ORDER BY `name`");
                                while ($row_cat = $stmt_cat->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                  <option value="<?php echo h($row_cat['no']); ?>" <?php chkSelected($row_cat['no'], $product['cat_gp_id']); ?>><?php echo h($row_cat['name']); ?></option>
                                <?php } ?>
                              </select>
                            </td>
                          </tr>
                          <tr>
                            <td colspan="2" align="center">
                              <input type="hidden" value="<?php echo h($product['no']); ?>" name="no" id="no" />
                              <input type="hidden" value="<?php echo h($product['member_code']); ?>" name="member_code" id="member_code" />
                              <input type="hidden" value="<?php echo (int)$page; ?>" name="page" id="page" />
                              <input type="submit" name="button" id="button" value="<?php echo ($cms_mode == 'add') ? '新增資料' : '修改資料'; ?>">
                            </td>
                          </tr>
                        </table>
                      </form>
                      <br><br>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <br />
        </div>
      </td>
    </tr>
  </table>
</body>

</html>