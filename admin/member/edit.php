<?php 

	require_once("../../include/config.inc.php");
	include_once("../session.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");


	$member_code = $_REQUEST["member_code"];	
	$cms_mode = $_REQUEST["cms_mode"];	
	$sh_post = $_SERVER['QUERY_STRING'];
	
	if($member_code<>""){
		startDB();
		$sql_edit="SELECT * FROM `member` WHERE `member_code`='$member_code'";
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
                      <td width="49%" class="tb_menu_title">會員管理</td>
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
        <td class="tb_edit_title"><span class="tb_title_text">名稱</span></td>
        <td class="tb_edit_info"><input name="name" type="text" id="name" value="<?php echo $product['name']; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="tb_edit_title"><span class="tb_title_text">密碼</span></td>
        <td class="tb_edit_info"><input name="psw" type="text" id="psw" value="<?php echo $product['psw']; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="tb_edit_title"><span class="tb_title_text">暱稱</span></td>
        <td class="tb_edit_info"><input name="nickname" type="text" id="nickname" value="<?php echo $product['nickname']; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">註冊時間</td>
        <td class="tb_edit_info"><? echo $product['regtime']; ?></td>
      </tr>
      <tr>
          <td class="tb_edit_title">性別</td>
          <td class="tb_edit_info">
            <input type="radio"  value="0" <?php if($product['sex']=='0' || $product['sex']==''){ echo'checked="checked"'; }?> name="sex" id="sex" />
            女性&nbsp;&nbsp;&nbsp;
            <input type="radio"  value="1" <?php if($product['sex']=='1'){ echo'checked="checked"'; }?> name="sex" id="sex" />男性</td>
      </tr>
      <tr>
        <td class="tb_edit_title">生日</td>
        <td class="tb_edit_info"><input type="text" value="<? echo $product['birthday']; ?>" name="birthday2" id="birthday2">
          <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',el:'birthday'})" onfocus="WdatePicker({errDealMode:1})"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">信箱</td>
        <td class="tb_edit_info"><input name="email" type="text" id="email" value="<?php echo $product['email']; ?>" size="60"></td>
      </tr>
      <tr>
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
      </tr>
      <tr>
        <td class="tb_edit_title">電子報</td>
        <td class="tb_edit_info"><input name="esend" type="radio"  value="1" <?php if($product['esend']=='1' || $product['esend']==""){ echo'checked="checked"'; }?> />
            願意&nbsp;&nbsp;&nbsp;
            <input type="radio" name="esend" value="0" <?php if($product['esend']=='0'){ echo'checked="checked"'; }?>  />反對</td>
      </tr>
      <tr>
        <td class="tb_edit_title">會員等級</td>
        <td class="tb_edit_info"><select name="cat_gp_id" id="cat_gp_id">
          <option value="">請選擇</option>
          <?php 	
        $sql_product_cat="SELECT * FROM `cat_gp` ORDER  BY `name`";
        $rs_product_cat=mysql_query($sql_product_cat);
        while($row_product_cat=mysql_fetch_array($rs_product_cat,MYSQL_ASSOC)){
        ?>
          <option value="<?php echo $row_product_cat['no'];  ?>"<?php  chkSelected($row_product_cat['no'],$product['cat_gp_id'])?>> <?php echo $row_product_cat['name'];  ?></option>
          <?php } ?>
          </select></td>
      </tr>
<?php /*     <tr>
      <td class="tb_edit_title">&nbsp;</td>
      <td class="tb_edit_info">
		<script type="text/javascript">
         function clearFile(id_name){
              var  obj=document.getElementById(id_name);
              obj.value=''  //FF下
              obj.select();   //IE下
              document.execCommand('Delete'); 
          }
        </script>
        <div id="div_photo_upload">
          <?php 
			$photo_array=explode(",",$product['photo']);
			$photo_ps=array("照片一張100*100");
			for( $I=1; $I <= 1; $I++ ){
			$pix=($I-1);
			$filename = '../../images/upload/product/'.$photo_array[$pix];
            if ($photo_array[$pix]=="" || !file_exists($filename)) { $filename='images/no_pic.gif';  } 
          ?>
          <div style="width:45%; height:225px; float:left; margin:10px;">
            <ul>
              <li><input type="checkbox" name="del_pic[<?php echo "$pix"; ?>]" value="1" >刪除此產品圖片<?php if($photo_ps[$I]<>""){echo '('.$photo_ps[$I].')';}?></li>
              <li style="width:240px; height:160px;"><img style="max-height:160px;" src="<?php echo"$filename";  ?>" alt="" width="240" ></li>
              <li>
              		<input id="input_file_<?php echo "$pix"; ?>" name="add_pic[<?php echo "$pix"; ?>]" type="file"  size="19" >
              		<input name="Submit2" type="button" value="清除" onClick="clearFile('input_file_<?php echo "$pix"; ?>');" />
              </li>
              </ul>
            </div>
          <?php } ?>
          </div>        </td>
    </tr> */?>
    <tr>
      <td colspan="2" align="center">
        <input type="hidden" value="<?php echo $product['no']; ?>" name="no" id="no" />
        <input type="hidden" value="<?php echo $product['member_code']; ?>" name="member_code" id="member_code" />
        <input type="hidden" value="<?php echo $product['regtime']; ?>" name="regtime" id="regtime" />
        <input type="hidden" value="<?php echo $product['photo']; ?>" name="photo" id="photo" />
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





