<?php 

	require_once("../../include/config.inc.php");
	include_once("../session.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");


  $trace=0;
  $sort_max=sortMax($cms_mode,'news',$sortmax_where,'no',$trace);
	$news_code = $_REQUEST["news_code"];	
	$cms_mode = $_REQUEST["cms_mode"];	
	$sh_post = $_SERVER['QUERY_STRING'];
	
	
	if($news_code<>""){
		startDB();
		$sql_edit="SELECT * FROM `news` WHERE `news_code`='$news_code'";
		$rs_edit=mysql_query($sql_edit);
        $row_edit=mysql_fetch_array($rs_edit,MYSQL_ASSOC);
	}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo ADMIN_TITLE; ?></title>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache"> 
<meta http-equiv="expires" content="0">
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
                      <td width="49%" class="tb_menu_title">最新訊息</td>
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
        <?php if($row_edit['uptime']!=""){ ?>
    <tr>
      <td class="tb_edit_title">最後修改時間</td>
      <td class="tb_edit_info"><?php echo $row_edit['uptime']; ?>&nbsp;</td>
    </tr>
    <?php } ?>
      <tr>
          <td width="15%" class="tb_edit_title"><span class="tb_title_text">名稱</span></td>
          <td class="tb_edit_info"><input name="name" type="text" id="name" value="<?php echo $row_edit['name']; ?>" size="60"></td>
    </tr>     
    <tr>
      <td class="tb_edit_title">撰寫日期</td>
      <td width="84%" class="tb_edit_info"><input type="text" value="<? echo $row_edit['wttime']; ?>" name="wttime" id="wttime">
        <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd ',el:'wttime'})" onfocus="WdatePicker({errDealMode:1})"></td>
    </tr>
     <tr>
        <td class="tb_edit_title">上架日期</td>
        <td width="84%" class="tb_edit_info"><input type="text" value="<? echo $row_edit['start_time']; ?>" name="start_time" id="start_time">
          <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'start_time'})" onfocus="WdatePicker({errDealMode:1})"></td>
      </tr>
      <tr>
        <td class="tb_edit_title"><span class="tb_title_text">下架日期</span></td>
        <td class="tb_edit_info"><input type="text" value="<? echo $row_edit['end_time']; ?>" name="end_time" id="end_time">
          <img onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'end_time'})" onfocus="WdatePicker({errDealMode:1})" src="../../script/datepicker/skin/datePicker.gif" width="16" height="22" align="absmiddle"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">摘要</td>
        <td class="tb_edit_info"><textarea class="ckeditor" id="summary'" name="summary" cols="" rows=""><?php echo $row_edit['summary']; ?></textarea></td>
      </tr>
    <tr>
      <td class="tb_edit_title">連結</td>
      <td class="tb_edit_info"><textarea name="web_url"  id="web_url" style="width:60%;" ><?php echo $row_edit['web_url']; ?></textarea></td>
    </tr>
    <?php /* <tr>
      <td align="center" class="tb_edit_title">點擊紀錄</td>
      <td class="tb_edit_info">
        <input name="click_num" type="text" id="textfield" value="<?php echo $row_edit['click_num']; ?>"></td>
    </tr> */?>
    <tr>
      <td align="center" class="tb_edit_title">上下架</td>
      <td class="tb_edit_info"><input type="radio" name="ishow" value="1"  <?php chkChecked('1',$row_edit['ishow']); ?>>
        上架
        <input type="radio" name="ishow" value="0"  <?php chkChecked('0',$row_edit['ishow']); ?>>
        下架 </td>
    </tr>
    <?php /*      <tr bordercolor="#eeeeee">
      <td align="center" class="tb_edit_title">說明圖片</td>
      <td class="text_1"><input name="del_pic[<?php echo ($I-1); ?>]" type="checkbox" id="checkbox" value="1">
        刪除圖片<br>
        <input type="file" name="add_pic[]" id="fileField">
        (上傳尺寸寬600)
        <?php
        $filename = '../../images/upload/news/news_'.sprintf("%06d",$row_edit['no'])."_2.jpg";
        if (file_exists($filename)) {    echo '<br><br><img src="'."$filename".'" width="360" >';} 
        ?></td>
    </tr>
   <tr>
      <td class="tb_edit_title">備註</td>
      <td class="tb_edit_info"><textarea name="ps" id="ps" cols="45" rows="5"><?php echo $row_edit['ps']; ?></textarea></td>
    </tr> */?>
    <tr>
      <td colspan="2" align="center">
        <input type="hidden" value="<?php echo $row_edit['no']; ?>" name="no" id="no" />
        <input type="hidden" value="<?php echo $row_edit['news_code']; ?>" name="news_code" id="news_code" />
        <input type="hidden" value="<?php echo $row_edit['photo']; ?>" name="photo" id="photo" />
        <input type="hidden" value="<?=$page?>" name="page" id="page" />
        <?php if(!$admin_pv_check || strExist($_SESSION["admin_pv"],'news_et')==true){?>
        <input type="submit" name="button" id="button" value="<?php if($cms_mode=="add"){ echo'新增資料'; }else{ echo'修改資料';}?>" >
        <? }?>   
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





