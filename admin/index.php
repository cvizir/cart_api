<?php
require_once("../config.php");
?>
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?= ADMIN_TITLE ?></title>
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td height="26" bgcolor="#666666">
        <table width="940" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="115" height="26">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
        <!--header end-->
      </td>
    </tr>
    <tr>
      <td height="280" background="images/index_board_bg.jpg">
        <form id="form1" name="form1" method="post" action="login.php">
          <table border="0" align="center" cellpadding="5" cellspacing="0">
            <tr>
              <td height="40" colspan="2" align="center" class="index_title"><?php echo ADMIN_TITLE; ?></td>
            </tr>
            <tr>
              <td align="right" class="index_input_text">ID：</td>
              <td><input name="admin_id" type="text" id="admin_id" style="width:180px" /></td>
            </tr>
            <tr>
              <td align="right" class="index_input_text">PASSWORD：</td>
              <td><input name="admin_psw" type="password" id="admin_psw" style="width:180px" /></td>
            </tr>
            <tr>
              <td colspan="2" align="right">
                <hr size="1" noshade="noshade" />
                <input name="Submit" type="submit" class="form_button" value="LOGIN" />
              </td>
            </tr>
          </table>
        </form>
      </td>
    </tr>
  </table>
</body>

</html>