<?php 

	require_once("../../include/config.inc.php");
	include_once("../session.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");

	$no = $_REQUEST["no"];	
	$product_code = $_REQUEST["product_code"];	
	$cms_mode = $_REQUEST["cms_mode"];	
	$sh_post = $_SERVER['QUERY_STRING'];
	$trace=0;
	
	if($no<>""){
		startDB();
		$sql_edit="SELECT * FROM `billboard_cat` WHERE `no`='$no'";
		$rs_edit=mysql_query($sql_edit);
        $billboardcat=mysql_fetch_array($rs_edit,MYSQL_ASSOC);
	}
	
    $no_max=sortMax('billboard_cat',$sql_where,$field_name='no',$trace);
	if($trace=="1"){
		echo'$no_max='."$no_max".'<br>';
	}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=ADMIN_TITLE?></title>
<link href="favicon3.ico" rel="shortcut icon">
<script language="JavaScript" type="text/javascript" src="../FormValid.js"></script>
<script type="text/javascript" src="../../script/js_calendar/calendar.js"></script>
<script type="text/javascript" src="../../script/js_calendar/calendar-setup.js"></script>
<script type="text/javascript" src="../../script/js_calendar/lang/calendar-big5.js"></script>
<style type="text/css"> @import url("../../script/js_calendar/calendar-win2k-cold-1.css"); </style>

<script type="text/javascript">

	function noDel(dp_n,dpix) {
	   document.getElementById(dp_n).style.display = "none"
	   document.getElementById(dpix).style.display = "block"
	}

	function resetFilePath(op,dp_b,dpix) {
	   document.getElementById(dpix).style.display = "none"
	   document.getElementById(dp_b).style.display = "block"
	   var fileObject = document.getElementById(op);
	   fileObject.outerHTML = fileObject.outerHTML;
	}
	function resetFilePath2(op,dp_b,dpix) {
	   var fileObject = document.getElementById(op);
	   fileObject.outerHTML = fileObject.outerHTML;
	}
	function switcDate(block_file,block_hide,op2){
		var fileObject = document.getElementById(op2);
	    var date = document.getElementById(block_hide);
		var cg = document.getElementById(block_file);
		fileObject.outerHTML = fileObject.outerHTML;		
        if(date.style.display != "block"){
				date.style.display = "block";
				cg.style.display = "none";
		}else{
				date.style.display = "none";
				cg.style.display = "block";
		}
	}
</script>


<link href="../style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {color: #0000FF}
-->
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2"><?php require_once("../header.php");?></td>
  </tr>
  <tr>
    <td width="10%" bgcolor="525252" align="left" valign="top"><?php require_once("../menu.php");?></td>
<td width="90%" valign="top"><div align="center"><br />
            <br />
            <table width="800" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
              <tr>
                <td width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr>
                      <td class="tb_menu_title">版面管理</td>
                      <td align="right" class="tb_menu_title"><input name="Submit" type="button" value="回上一頁" onClick="javascrip:window.history.go(-1);" /></td>
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
                      <td align="center" valign="top"><?php echo'<form action="save.php?'.$sh_post.'" method="post" id="form1" enctype="multipart/form-data" name="CodeForm" onSubmit="return validator(this)">'; ?>
                        <table width="66%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                            <tr>
                              <td width="19%" align="center" class="tb_edit_title">名稱</td>
                              <td width="81%" class="tb_edit_info"><input name="name" type="text" value="<?php echo $billboardcat['name']; ?>" size="50" maxlength="50"></td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">網址</td>
                              <td class="tb_edit_info"><input name="web_url" type="text" value="<?php echo $billboardcat['web_url']; ?>" size="50"></td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">備註</td>
                              <td class="tb_edit_info"><textarea name="ps" id="ps" cols="45" rows="5"><?php echo $billboardcat['ps']; ?></textarea></td>
                            </tr>
                          <tr>
                            <td colspan="2" align="center"><span class="tb_edit_info">
                              <input type="hidden" value="<?php echo $billboardcat['no']; ?>" name="no" id="no" />
                              <input type="hidden" value="<?php echo $billboardcat['no']; ?>" name="no" id="no" />
                              <input type="hidden" value="<? echo $page; ?>" name="page" id="page" />
                              </span>
                          <input type="submit" name="button" id="button" value="修改資料" >                            </td></tr>
                        </table>
<br>
                          <br>
                      </form></td>
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





