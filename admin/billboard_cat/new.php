<?php 
	require_once("../../config.php");
	include_once("../session.php");
	
	if($_GET["page"]==""){
	$page=1;
	}else{
	$page=$_GET["page"];
	}


	$sql_no_max="SELECT MAX(no) AS no_max FROM `billboard_cat`";
	$rs_no_max=mysql_query($sql_no_max);
    $row_no_max=mysql_fetch_array($rs_no_max,MYSQL_ASSOC);
	
	if($row_no_max['no_max']==""){
		$no_max=1;
	}else{
		$no_max=$row_no_max['no_max'];
	}



    $no=$_GET["no"];
	//echo 'no='.$no;
	$billboardcat = new BillboardCat($no);
	//echo $billboardcat->name;
	
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
                      <td bgcolor="#666666" class="title_2">廣告管理</td>
                      <td align="right" bgcolor="#666666" class="title_2"><input name="Submit" type="button" value="回上一頁" onClick="javascrip:window.history.go(-1);" /></td>
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
                      <td align="center" valign="top"><form action="save.php" method="post" id="form1" enctype="multipart/form-data" name="CodeForm" onSubmit="return validator(this)">
                        <table width="66%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                            <?php /* <tr>
                              <td align="center" class="title_3">NEWS標題</td>
                              <td class="text_1"><input name="title" value="<? echo $billboard->title; ?>" type="text" id="title" valid="required" errmsg="建檔時間不能為空!" />                               </td>
                            </tr> */?>
                            

                            <tr>
                              <td width="19%" align="center" class="title_3">名稱</td>
                              <td width="81%" class="text_1"><input name="name" type="text" value="<?php echo $billboardcat->name; ?>" size="50" maxlength="50"></td>
                            </tr>
                            <tr>
                              <td align="center" class="title_3">網址</td>
                              <td class="text_1"><input name="web_url" type="text" value="<?php echo $billboardcat->web_url; ?>" size="50"></td>
                            </tr>
                            <tr>
                              <td align="center" class="title_3">備註</td>
                              <td class="text_1"><textarea name="ps" id="ps" cols="45" rows="5"><?php echo $billboardcat->ps; ?></textarea></td>
                            </tr>
                          <tr>
                            <td colspan="2" align="center"><span class="text_1">
                              <input type="hidden" value="<? echo $billboardcat->no; ?>" name="no" id="no" />
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





