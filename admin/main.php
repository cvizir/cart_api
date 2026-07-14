<?php 
	require_once("../config.php");
	include_once("session.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=ADMIN_TITLE?></title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2"><?php require_once("header.php");?></td>
  </tr>
  <tr>
    <td width="130" align="left" valign="top" bgcolor="#525252"><?php require_once("menu.php");?></td>
    <td width="1521" valign="top"><div align="center"><br />
        <br />
          <table border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td height="200" valign="middle"><table align="center">
                  <tr>
                    <td><p>歡迎進入後台管理介面</p></td>
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
