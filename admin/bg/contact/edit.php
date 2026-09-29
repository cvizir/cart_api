<?php 

	require_once("../../include/config.inc.php");
	include_once("../session.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");


	$contact_code = $_REQUEST["contact_code"];	
	$cms_mode = $_REQUEST["cms_mode"];	
	$sh_post = $_SERVER['QUERY_STRING'];
	
	if($contact_code<>""){
		startDB();
		$sql_edit="SELECT * FROM `contact` WHERE `contact_code`='$contact_code'";
		$rs_edit=mysql_query($sql_edit);
        $product=mysql_fetch_array($rs_edit,MYSQL_ASSOC);
	}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=ADMIN_TITLE?></title>
<script type="text/javascript" src="../../script/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../script/datepicker/WdatePicker.js"></script>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
    <tr>
		<td colspan="2"><?php require_once("../header.php");?></td>
    </tr>
    <tr>
		<td width="10%" align="left" bgcolor="525252" valign="top"><?php require_once("../menu.php");?></td>
		<td width="90%" valign="top"><div align="center"><br />
              <br />
              <table width="800" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
                <tr>
                  <td width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr>
                      <td width="49%" class="tb_menu_title">聯絡我們管理</td>
                      <td width="51%" align="right" class="tb_menu_title"><input name="Submit" type="button" value="回上一頁" onClick="javascrip:window.history.go(-1);" /></td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td height="300" align="center" valign="top"><table width="100%">
                      <tr>
                        <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td>&nbsp;</td>
                          </tr>
                        </table></td>
                      </tr>
                      <tr>
                        <td align="center" valign="top">
                        <?php echo'<form action="save.php?'.$sh_post.'" method="post" id="form1" enctype="multipart/form-data" name="CodeForm" onSubmit="return validator(this)">'; ?>
    <table width="90%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
      <tr>
        <td width="15%" class="tb_edit_title">上次修改時見</td>
        <td width="84%" class="tb_edit_info"><?php echo $product['uptime']; ?>&nbsp;</td>
      </tr>
      <tr>
        <td class="tb_edit_title">訊息時間</td>
        <td class="tb_edit_info"><? echo $product['post_time']; ?></td>
      </tr>
      <tr>
        <td class="tb_edit_title"><span class="tb_title_text">姓名</span></td>
        <td class="tb_edit_info"><input name="name" type="text" id="name" value="<?php echo $product['name']; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">主題</td>
        <td class="tb_edit_info"><input name="subject" type="text" id="subject" value="<?php echo $product['subject']; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">信箱</td>
        <td class="tb_edit_info"><input name="email" type="text" id="email" value="<?php echo $product['email']; ?>" size="60"></td>
      </tr>
<?php /*       <tr>
        <td class="tb_edit_title">電話</td>
        <td class="tb_edit_info"><input name="tel" type="text" id="emtelail" value="<?php echo $product['tel']; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">手機</td>
        <td class="tb_edit_info"><input name="mphone" type="text" id="mphone" value="<?php echo $product['mphone']; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">地址</td>
        <td class="tb_edit_info"><input name="addr" type="text" id="addr" value="<?php echo $product['addr']; ?>" size="60"></td>
      </tr> */?>
      <tr>
        <td class="tb_edit_title">備註</td>
        <td class="tb_edit_info"><textarea name="msg" id="msg" cols="45" rows="5"><?php echo $product['msg']; ?></textarea></td>
      </tr>
    <tr>
      <td colspan="2" align="center">
        <input type="hidden" value="<?php echo $product['no']; ?>" name="no" id="no" />
        <input type="hidden" value="<?php echo $product['contact_code']; ?>" name="contact_code" id="contact_code" />
        <input type="hidden" value="<?php echo $product['post_time']; ?>" name="post_time" id="post_time" />
        <input type="hidden" value="<?=$page?>" name="page" id="page" />
        <input type="submit" name="button" id="button" value="<?php if($cms_mode=="add"){ echo'新增資料'; }else{ echo'修改資料';}?>" >        </td>
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





