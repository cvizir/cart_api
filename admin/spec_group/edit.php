<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$spec_group_code = isset($_REQUEST['spec_group_code']) ? trim($_REQUEST['spec_group_code']) : '';
$cms_mode        = isset($_REQUEST['cms_mode']) ? trim($_REQUEST['cms_mode']) : ($spec_group_code === '' ? 'add' : 'edit');
$page            = isset($_REQUEST['page']) ? max(1, (int)$_REQUEST['page']) : 1;
$sh_post         = $_SERVER['QUERY_STRING'];
// 原本 $trace = 1 會把 $sort_max 印在畫面最上方；表單上也沒有排序欄位，sortMax 與排序隱藏欄位都已移除

// 新增模式預設值
$spec_group = array(
  'no' => '', 'spec_group_code' => '', 'name' => '', 'pdcat_code' => '', 'spec_code' => '', 'ishow' => '1'
);

if ($spec_group_code !== '') {
  $stmt = $pdo->prepare("SELECT * FROM `spec_group` WHERE `spec_group_code` = ?");
  $stmt->execute(array($spec_group_code));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $spec_group = array_merge($spec_group, $row);
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
      alert('請輸入規格名稱');
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
    <td width="10%" align="left" bgcolor="#525252" valign="top"><?php require_once("../menu.php"); ?></td>
    <td width="90%" valign="top"><div align="center"><br />
      <br />
      <table width="800" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
        <tr>
          <td width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
              <td width="49%" class="tb_menu_title">規格群組管理</td>
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
                      <td width="15%" class="tb_edit_title">規格名稱</td>
                      <td width="84%" class="tb_edit_info"><input name="name" type="text" id="name" value="<?php echo h($spec_group['name']); ?>" size="60"></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">商品類別</td>
                      <td class="tb_edit_info">
                        <select name="pdcat_code" id="pdcat_code">
                          <option value="">請選擇</option>
                          <?php
                          $stmt_cat = $pdo->query("SELECT `pdcat_code`, `name` FROM `pdcat` ORDER BY `name`");
                          while ($row_pdcat = $stmt_cat->fetch(PDO::FETCH_ASSOC)) {
                          ?>
                          <option value="<?php echo h($row_pdcat['pdcat_code']); ?>" <?php chkSelected($row_pdcat['pdcat_code'], $spec_group['pdcat_code']); ?>><?php echo h($row_pdcat['name']); ?></option>
                          <?php } ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">規格選項</td>
                      <td class="tb_edit_info">
                        <?php
                        $stmt_spec = $pdo->query("SELECT `spec_code`, `text` FROM `spec_info` ORDER BY `text`");
                        while ($row_spec = $stmt_spec->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <label><input type="checkbox" name="spec_code[]" value="<?php echo h($row_spec['spec_code']); ?>" <?php chkCheckbox($row_spec['spec_code'], $spec_group['spec_code']); ?>><?php echo h($row_spec['text']); ?></label>&nbsp;
                        <?php } ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">上下架</td>
                      <td class="tb_edit_info">
                        <label>上架<input type="radio" value="1" name="ishow" <?php chkChecked('1', $spec_group['ishow']); ?>></label>
                        <label>下架<input type="radio" value="0" name="ishow" <?php chkChecked('0', $spec_group['ishow']); ?>></label>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" align="center">
                        <input type="hidden" value="<?php echo h($spec_group['no']); ?>" name="no" id="no" />
                        <input type="hidden" value="<?php echo h($spec_group['spec_group_code']); ?>" name="spec_group_code" id="spec_group_code" />
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
