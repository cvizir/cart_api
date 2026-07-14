<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");


$qa_code = $_REQUEST["qa_code"];
$cms_mode = $_REQUEST["cms_mode"];
$sh_post = $_SERVER['QUERY_STRING'];
//echo"$sh_post";
if ($qa_code <> "") {
  startDB();
  $sql_edit = "SELECT * FROM `qa` WHERE `qa_code`='$qa_code'";
  $rs_edit = mysql_query($sql_edit);
  $qa = mysql_fetch_array($rs_edit, MYSQL_ASSOC);
}

?>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title><?php echo ADMIN_TITLE; ?></title>
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
                    <td width="49%" class="tb_menu_title">Q&amp;A管理</td>
                    <td width="51%" class="tb_menu_title" align="right"><input name="Submit" type="button" value="回上一頁" onClick="javascrip:window.history.go(-1);" /></td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td height="300" align="center" valign="top">
                <table width="100%">
                  <tr>
                    <td align="center" valign="top">
                      <?php echo '<form action="save.php?' . $sh_post . '" method="post" id="form1" enctype="multipart/form-data" name="CodeForm" onSubmit="return validator(this)">'; ?>
                      <table width="90%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                        <tr>
                          <td width="15%" height="41" align="center" class="tb_edit_title">最後修改時間</td>
                          <td width="84%" class="tb_edit_info">
                            <input name="uptime" type="text" id="uptime" value="<?php echo $qa['uptime']; ?>" />
                            <img onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'uptime'})" onfocus="WdatePicker({errDealMode:1})" src="../../script/datepicker/skin/datePicker.gif" width="16" height="22" align="absmiddle">
                          </td>
                        </tr>
                        <tr>
                          <td align="center" class="tb_edit_title">類型</td>
                          <td class="tb_edit_info"><input name="cat_id" type="radio" id="ishow" value="b" checked <?php chkChecked('b', $qa['cat_id']); ?>>
                            購物問題&nbsp;&nbsp;&nbsp;
                            <input type="radio" value="m" name="cat_id" id="ishow" <?php chkChecked('m', $qa['cat_id']); ?>>會員問題
                          </td>
                        </tr>
                        <tr>
                          <td class="tb_edit_title">問題</td>
                          <td class="tb_edit_info"><input name="question" type="text" id="question" value="<?php echo $qa['question']; ?>" size="60"></td>
                        </tr>
                        <tr>
                          <td align="center" class="tb_edit_title">答覆</td>
                          <td class="tb_edit_info"><textarea class="ckeditor" id="ans" name="ans" cols="" rows=""><?php echo $qa['ans']; ?></textarea></td>
                        </tr>
                        <tr>
                          <td align="center" class="tb_edit_title">上下架</td>
                          <td class="tb_edit_info">上架<input name="ishow" type="radio" id="ishow" value="1" checked <?php chkChecked('1', $qa['ishow']); ?>>
                            &nbsp;&nbsp;&nbsp;
                            下架<input type="radio" value="0" name="ishow" id="ishow" <?php chkChecked('0', $qa['ishow']); ?>></td>
                        </tr>
                        <tr>
                          <td align="center" class="tb_edit_title">排序</td>
                          <td class="tb_edit_info">
                            <select name="m_sort" id="m_sort">
                              <?php getDateSelectOption('1', '99', $qa['m_sort']); ?>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2" align="center">
                            <input type="hidden" value="<?php echo $no; ?>" name="no" id="no" />
                            <input type="hidden" value="<?php echo $qa['qa_code']; ?>" name="qa_code" id="no" />
                            <input type="hidden" value="<?php echo $page; ?>" name="page" id="page" />
                            <input type="submit" name="button" id="button" value="<?php if ($no == "") {
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