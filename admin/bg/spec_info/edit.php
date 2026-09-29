<?php

require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

startDB();
$spec_code = $_REQUEST["spec_code"];
$cms_mode = $_REQUEST["cms_mode"];
$sh_post = $_SERVER['QUERY_STRING'];
$trace = 0;
$sort_max = sortMax($cms_mode, 'spec_info', $sql_where, $field_name = 'no', $trace);

if ($spec_code <> "") {
  $sql_edit = "SELECT * FROM `spec_info` WHERE `spec_code`='$spec_code'";
  $rs_edit = mysql_query($sql_edit);
  $spec_info = mysql_fetch_array($rs_edit, MYSQL_ASSOC);
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
                    <td width="49%" class="tb_menu_title">規格管理</td>
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
                          <td width="84%" class="tb_edit_info"><input name="text" type="text" id="name" value="<?php echo $spec_info['text']; ?>" size="60"></td>
                        </tr>
                        <tr>
                          <td width="15%" class="tb_edit_title">規格色票</td>
                          <td width="84%" class="tb_edit_info"><input name="color" type="text" id="color" value="<?php echo $spec_info['color']; ?>" size="60"></td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">規格類型</td>
                          <td class="tb_edit_info">
                            顏色<input type="radio" value="color" name="spec_type" <?php chkChecked('color', $spec_info['spec_type']); ?>>
                            容量<input type="radio" value="capacity" name="spec_type" <?php chkChecked('capacity', $spec_info['spec_type']); ?>>
                            解析度<input type="radio" value="resolution" name="spec_type" <?php chkChecked('resolution', $spec_info['spec_type']); ?>>
                            尺寸<input type="radio" value="size" name="spec_type" id="spec_type" <?php chkChecked('size', $spec_info['spec_type']); ?>>
                          </td>
                        </tr>
                    </td>

                  </tr>
                  <tr>
                    <td class="tb_edit_title">顯示類型</td>
                    <td class="tb_edit_info">
                      文字<input type="radio" value="text" name="show_type" <?php chkChecked('text', $spec_info['show_type']); ?>>
                      色票<input type="radio" value="code" name="show_type" <?php chkChecked('code', $spec_info['show_type']); ?>>
                      圖片<input type="radio" value="image" name="show_type" <?php chkChecked('image', $spec_info['show_type']); ?>>
                    </td>
                  </tr>
                  <tr>
                    <td class="tb_edit_title">上下架</td>
                    <td class="tb_edit_info">
                      上架<input type="radio" value="1" name="ishow" id="ishow" <?php chkChecked('1', $spec_info['ishow']); ?>>
                      下架<input type="radio" value="0" name="ishow" id="ishow" <?php chkChecked('0', $spec_info['ishow']); ?>>
                    </td>
                  </tr>
                  <tr>
                    <td class="tb_edit_title">排序</td>
                    <td class="tb_edit_info"><select name="m_sort" id="m_sort">
                        <?php getDateSelectOption('1', $sort_max, $spec_info['m_sort']); ?>
                      </select></td>
                  </tr>
                  <tr>
                    <td class="tb_edit_title">&nbsp;</td>
                    <td class="tb_edit_info">
                      <script type="text/javascript">
                        function clearFile(id_name) {
                          var obj = document.getElementById(id_name);
                          obj.value = '' //FF下
                          obj.select(); //IE下
                          document.execCommand('Delete');
                        }
                      </script>
                      <div id="div_photo_upload">
                        <?php
                        $img_url = $spec_info['img_url'];
                        $photo_ps = array("", "", "");
                        for ($I = 1; $I <= 1; $I++) {
                          $pix = ($I - 1);
                          $filename = '../../images/upload/spec_info/' . $img_url;
                          //echo"$filename";
                          if (!file_exists($filename)) {
                            $filename = 'images/no_pic.gif';
                          }
                        ?>
                          <div style="width:45%; height:auto; float:left; margin:10px;">
                            <ul>
                              <li><input type="checkbox" name="del_pic[0]" value="1">刪除此產品圖片</li>
                              <li style="width:240px; height:auto;"><img src="<?php echo "$filename";  ?>" alt="" width="240"></li>
                              <li>
                                <input id="input_file_<?php echo "$pix"; ?>" name="add_pic[<?php echo "$pix"; ?>]" type="file" size="19">
                                <input name="Submit2" type="button" value="清除" onClick="clearFile('input_file_<?php echo "$pix"; ?>');" />
                              </li>
                            </ul>
                          </div>
                        <?php } ?>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" align="center">
                      <input type="hidden" value="<?php echo $spec_info['no']; ?>" name="no" id="no" />
                      <input type="hidden" value="<?php echo $spec_info['spec_code']; ?>" name="spec_code" id="spec_code" />
                      <input type="hidden" value="<?= $page ?>" name="page" id="page" />
                      <input type="hidden" value="<? echo $spec_info['m_sort']; ?>" name="sort_old_value" id="sort_old_value" />
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