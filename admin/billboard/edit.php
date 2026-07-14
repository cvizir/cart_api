<?php 

	include_once("../session.php");
	require_once("../../include/config.inc.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");

    startDB();
	$bd_id = $_REQUEST["bd_id"];
	$bd_name = $_REQUEST["bd_name"];
	$page=($_REQUEST["page"]!="")?$_REQUEST["page"]: 1;

	$no = $_REQUEST["no"];	
	$cms_mode = $_REQUEST["cms_mode"];	
	$sh_post = $_SERVER['QUERY_STRING'];
    $trace=0;

    $sql_where=" WHERE `bd_id`='$bd_id'";
    $sort_max=sortMax($no,'billboard',$sql_where,$field_name='no',$trace);
	
	if($no<>""){
		$sql_edit="SELECT * FROM `billboard` WHERE `no`='$no'";
		$rs_edit=mysql_query($sql_edit);
        $billboard=mysql_fetch_array($rs_edit,MYSQL_ASSOC);
	}
	
	if($trace=="1"){
		echo'$sort_max='."$sort_max".'<br>';
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=ADMIN_TITLE?></title>
<link href="favicon3.ico" rel="shortcut icon">
<script type="text/javascript" src="../../script/tw_zip.js"></script>
<script type="text/javascript" src="../../script/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../script/datepicker/WdatePicker.js"></script>
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
                      <td class="tb_menu_title"><?php echo "$bd_name"; ?>-版面管理</td>
                      <td class="tb_menu_title" align="right" ><input name="Submit" type="button" value="回上一頁" onClick="javascrip:window.history.go(-1);" /></td>
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
                        <table width="96%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                            <tr>
                              <td align="center" class="tb_edit_title">名稱</td>
                              <td class="tb_edit_info"><input name="name" type="text" id="textfield" value="<?php echo $billboard['name'];; ?>" size="64"></td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">廣告類別</td>
                              <td class="tb_edit_info"><select name="cat_id">
                                <?php 
								$sql_category='SELECT * FROM `billboard_cat`';
								$rs_category=mysql_query($sql_category);
								while($row_category=mysql_fetch_array($rs_category)){  
								if($billboard['bd_id']!=""){$bd_id=$billboard['bd_id'];}
								?>
                                <option value="<?php echo $row_category['no']; ?>" <?php chkSelected($row_category['no'],$bd_id)?>><?php echo $row_category['name']; ?></option>
                                <?php } ?>
                              </select></td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">顯示類型</td>
                              <td class="tb_edit_info">
                              <input type="radio" name="m_type" id="m_type" value="p" <?php chkChecked('p',$billboard['m_type']); ?> >圖片
                              <input type="radio" style="margin-left:10px;" name="m_type" id="m_type" value="pu" <?php chkChecked('pu',$billboard['m_type']); ?>>圖片+連結
                              <input type="radio" style="margin-left:10px;" name="m_type" id="m_type" value="pi" <?php chkChecked('pi',$billboard['m_type']); ?>>圖片+說明
                              <input type="radio" style="margin-left:10px;" name="m_type" id="m_type" value="piu" <?php chkChecked('piu',$billboard['m_type']); ?>>圖片+說明連結
                              <input type="radio" style="margin-left:10px;" name="m_type" id="m_type" value="c" <?php chkChecked('c',$billboard['m_type']); ?>>程式碼
                              <input type="radio" style="margin-left:10px;" name="m_type" id="m_type" value="ciu" <?php chkChecked('ciu',$billboard['m_type']); ?>>程式碼+說明連結
                              </td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">可否刪除</td>
                              <td class="tb_edit_info"><input type="radio" name="del_sw" id="del_sw" value="0" <?php chkChecked('0',$billboard['del_sw']); ?> >
                                可刪除
                                <input type="radio" name="del_sw" id="del_sw" value="1" style="margin-left:10px;" <?php chkChecked('1',$billboard['del_sw']); ?>>
                                不可刪除 </td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">程式碼</td>
                              <td class="tb_edit_info"><textarea class="ckeditor" name="web_code" cols="60" rows="6"  ><?php echo $billboard['web_code']; ?></textarea>
                                <br>
                               	範例:                               	
                               	<br>
                               	&lt;iframe   width=&quot;100%&quot; height=&quot;100%&quot; src=&quot;//www.youtube.com/embed/<font style="color:#F00;">n3ZvI70GCb0(影片代號)</font>?rel=0&amp;wmode=opaque&quot; frameborder=&quot;0&quot; allowfullscreen&gt;&lt;/iframe&gt;<br><br></td>
                            </tr>
                            <tr>
                              <td class="tb_edit_title">上架日期</td>
                              <td class="tb_edit_info"><input type="text" value="<? echo $row_edit['start_time']; ?>" name="start_time" id="start_time">
                                <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'start_time'})" onfocus="WdatePicker({errDealMode:1})"></td>
                            </tr>
                            <tr>
                              <td class="tb_edit_title"><span class="tb_title_text">下架日期</span></td>
                              <td class="tb_edit_info"><input type="text" value="<? echo $row_edit['end_time']; ?>" name="end_time" id="end_time">
                                <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'end_time'})" onfocus="WdatePicker({errDealMode:1})" align="absmiddle"></td>
                            </tr>
                            <tr>
                              <td width="15%" align="center" class="tb_edit_title">連結</td>
                              <td width="84%" class="tb_edit_info"><textarea name="web_url" cols="60" rows="6"  ><?php echo $billboard['web_url']; ?></textarea></td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">廣告說明</td>
                              <td class="tb_edit_info"><textarea class="ckeditor" name="info" cols="60" rows="6"  ><?php echo $billboard['info']; ?></textarea></td>
                            </tr>
                            <tr>
                              <td class="tb_edit_title">排序</td>
                              <td class="tb_edit_info"><select name="m_sort" id="m_sort">
                                <?php getDateSelectOption('1',$sort_max,$row_edit['m_sort']); ?>
                              </select></td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">上下架</td>
                              <td class="tb_edit_info"><input type="radio" name="ishow" value="1"  <?php chkChecked('1',$billboard['ishow']); ?>>
上架
  <input type="radio" name="ishow" value="0"  <?php chkChecked('0',$billboard['ishow']); ?>>
下架
</td>
                            </tr>
                           <tr>
                              <td rowspan="3" align="center" class="tb_edit_title">列表圖示</td>
                              <td class="tb_edit_info"><input type="file" name="add_pic[]" id="fileField"></td>
                            </tr>
                            <tr>
                              <td class="tb_edit_info"><input type="text" name="pic_size[]" id="pic_size" value="<?php echo $billboard['pic_size']; ?>" >
                                <br>(如果填0x0將以原尺寸上傳，200x150及寬200高150。若要限制寬度高度填0即可(例:140x0))</td>
                            </tr>
                            <tr>
                              <td class="tb_edit_info">
                              <?php 
                              $filename = '../../images/upload/billboard/billboard_'.sprintf("%06d", $billboard['no']).'_1.jpg';
                              //echo"$filename";
                              if (file_exists($filename)) { 
							  ?>
                              <a href="<? echo $billboard['weburl'];?>" target="_blank">
                              <img src="<?php echo "$filename";?>"  width="220"  border="0">
                              </a>
                              <?php } ?>
                              </td>
                            </tr>
                          <tr>
                            <td colspan="2" align="center"><span class="tb_edit_info">
                              <input type="hidden" value="<? echo $bd_name; ?>" name="bd_name" id="bd_name" />
                              <input type="hidden" value="<? echo $bd_id; ?>" name="bd_id" id="bd_id" />
                              <input type="hidden" value="<? echo $billboard['photo']; ?>" name="photo" id="photo" />
                              <input type="hidden" value="<? echo $billboard['no']; ?>" name="no" id="no" />
                              <input type="hidden" value="<? echo $billboard['m_sort']; ?>" name="sort_old_value" id="sort_old_value" />
                              <input type="hidden" value="<? echo $page; ?>" name="page" id="page" />
                              </span>
                              <input type="submit" name="button" id="button" value="修改資料" >                            
                            </td>
                            </tr>
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





