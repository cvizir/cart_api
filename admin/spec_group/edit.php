<?php

require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

startDB();
$spec_group_code = $_REQUEST["spec_group_code"];
$cms_mode = $_REQUEST["cms_mode"];
$sh_post = $_SERVER['QUERY_STRING'];
$trace = 1;
$sort_max = sortMax($cms_mode, 'spec_group', $sql_where, $field_name = 'no', $trace);

if ($spec_group_code <> "") {
  $sql_edit = "SELECT * FROM `spec_group` WHERE `spec_group_code`='$spec_group_code'";
  $rs_edit = mysql_query($sql_edit);
  $spec_group = mysql_fetch_array($rs_edit, MYSQL_ASSOC);
}


if ($trace == "1") {
  echo '$sort_max=' . "$sort_max" . '<br>';
}


?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?= ADMIN_TITLE ?></title>
  <script type="text/javascript" src="../../script/ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="../../script/datepicker/WdatePicker.js"></script>
  <link href="../style.css" rel="stylesheet" type="text/css" />
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
  <table width="101%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td colspan="2"><?php require_once("../header.php"); ?></td>
    </tr>
    <tr>
      <td width="10%" align="left" bgcolor="525252" valign="top"><?php require_once("../menu.php"); ?></td>
      <td width="90%" valign="top">
        <div align="center"><br />
          <br />
          <table width="800" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
            <tr>
              <td width="100%">
                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                  <tr>
                    <td width="49%" class="tb_menu_title">規格群組管理</td>
                    <td width="51%" align="right" class="tb_menu_title"><input name="Submit" type="button" value="回上一頁" onClick="javascrip:window.history.go(-1);" /></td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td height="300" align="center" valign="top">
                <table width="100%">
                  <tr>
                    <td>
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>&nbsp;</td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" valign="top">
                      <?php echo '<form action="save.php?' . $sh_post . '" method="post" id="form1" enctype="multipart/form-data" name="CodeForm" onSubmit="return validator(this)">'; ?>
                      <table width="90%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                        <tr>
                          <td width="15%" class="tb_edit_title">規格名稱</td>
                          <td width="84%" class="tb_edit_info"><input name="name" type="text" id="name" value="<?php echo $spec_group['name']; ?>" size="60"></td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">商品類別</td>
                          <td class="tb_edit_info">
                            <select name="pdcat_code" id="pdcat_code">
                              <option value="">請選擇</option>
                              <?php
                              $sql_product_cat = "SELECT * FROM `pdcat` ORDER  BY `name`";
                              $rs_product_cat = mysql_query($sql_product_cat);
                              while ($row_pdcat = mysql_fetch_array($rs_product_cat, MYSQL_ASSOC)) {
                              ?>
                                <option value="<?php echo $row_pdcat['pdcat_code'];  ?>" <?php chkSelected($row_pdcat['pdcat_code'], $spec_group['pdcat_code']) ?>>
                                  <?php echo $row_pdcat['name'];  ?>
                                </option>
                              <?php } ?>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">規格名稱</td>
                          <td class="tb_edit_info">
                            <?php
                            $sql_spec = "SELECT * FROM `spec_info` ORDER  BY `text`";
                            $rs_spec = mysql_query($sql_spec);
                            while ($row_spec = mysql_fetch_array($rs_spec, MYSQL_ASSOC)) {
                            ?>
                              <input type="checkbox" name="spec_code[]" value="<?php echo $row_spec['spec_code'];  ?>" <?php chkCheckbox($row_spec['spec_code'], $spec_group['spec_code']) ?> >
                              <?php echo $row_spec['text'];  ?>
                            <?php } ?>
                          </td>
                        <tr>
                          <td class="tb_edit_title">上下架</td>
                          <td class="tb_edit_info">
                            上架<input type="radio" value="1" name="ishow" id="ishow" <?php chkChecked('1', $spec_group['ishow']); ?>>
                            下架<input type="radio" value="0" name="ishow" id="ishow" <?php chkChecked('0', $spec_group['ishow']); ?>>
                          </td>
                        </tr>
                    </td>

                  </tr>


                  <tr>
                    <td colspan="2" align="center">
                      <input type="hidden" value="<?php echo $spec_group['no']; ?>" name="no" id="no" />
                      <input type="hidden" value="<?php echo $spec_group['spec_group_code']; ?>" name="spec_group_code" id="spec_group_code" />
                      <input type="hidden" value="<?= $page ?>" name="page" id="page" />
                      <input type="hidden" value="<? echo $spec_group['m_sort']; ?>" name="sort_old_value" id="sort_old_value" />
                      <input type="submit" name="button" id="button" value="<?php if ($cms_mode == "add") {
                                                                              echo '新增資料';
                                                                            } else {
                                                                              echo '修改資料';
                                                                            } ?>">
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