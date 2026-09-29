<?php 
	include_once("../session.php");
	require_once("../../include/config.inc.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");


	$no = $_REQUEST["no"];	
	$cms_mode = $_REQUEST["cms_mode"];	
	$sh_post = $_SERVER['QUERY_STRING'];
	
	//echo"$sh_post";
	if($no<>""){
		startDB();
		$sql_edit="SELECT * FROM `tmp_model` WHERE `no`='$no'";
		$rs_edit=mysql_query($sql_edit);
        $member=mysql_fetch_array($rs_edit,MYSQL_ASSOC);
	}

	$bday=explode("-",$member['bday']);
	$interest_array=array('1'=>'羽球','2'=>'籃球','3'=>'棒球','4'=>'游泳','5'=>'慢跑');
	$buy_type_array=array('1'=>'手機','2'=>'平板','3'=>'書籍','4'=>'基金','5'=>'外匯');
	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=ADMIN_TITLE?></title>
<script type="text/javascript" src="../../script/tw_zip.js"></script>
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
                      <td width="49%" class="tb_menu_title">TMP</td>
                      <td width="51%" align="right" class="tb_menu_title">
                      <input name="Submit" type="button" value="回上一頁" onClick="javascrip:window.history.go(-1);" />
                      </td>
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
        <td class="tb_edit_title">帳號(Email)</td>
        <td width="83%" class="tb_edit_info"><input name="email" type="text" id="email" value="<? echo $member['email']; ?>" size="40" /></td>
      </tr>
      <tr>
        <td class="tb_edit_title">註冊日期</td>
        <td class="tb_edit_info"><input type="text" value="<? echo $member['regtime']; ?>" name="regtime" id="regtime">
          <img onClick="WdatePicker({dateFmt:'yyyy-MM-dd',el:'regtime'})" onfocus="WdatePicker({errDealMode:1})" src="../../script/datepicker/skin/datePicker.gif" width="16" height="22" align="absmiddle"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">最後登入時間</td>
        <td class="tb_edit_info"><input type="text" value="<? echo $member['last_login']; ?>" name="last_login" id="last_login">
          <img onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'last_login'})" onfocus="WdatePicker({errDealMode:1})" src="../../script/datepicker/skin/datePicker.gif" width="16" height="22" align="absmiddle"></td>
      </tr>
      <tr>
        <td width="17%" class="tb_edit_title">暱稱</td>
        <td class="tb_edit_info"><input name="nickname" type="text" id="nickname" value="<? echo $member['nickname']; ?>" ></td>
      </tr>
      <tr>
        <td class="tb_edit_title">姓名</td>
        <td class="tb_edit_info"><input name="name" type="text" id="name" value="<? echo $member['name']; ?>"  /></td>
      </tr>
      <?php /*   <tr>
    <td class="tb_edit_title">所在區域</td>
    <td class="tb_edit_info"><select name="region" id="region">
      <?php  getDateSelectOption3(region, name, name, $member['region,$where)?>
    </select></td>
  </tr> */?>
      <tr>
        <td class="tb_edit_title">密碼</td>
        <td class="tb_edit_info"><input type="text" value="<? echo $member['psw']; ?>" name="psw" id="psw"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">興趣</td>
        <td class="tb_edit_info">
        <?php 	
        $sql_product_cat="SELECT * FROM `product_cat` ORDER  BY `name`";
        $rs_product_cat=mysql_query($sql_product_cat);
        while($row_product_cat=mysql_fetch_array($rs_product_cat,MYSQL_ASSOC)){
        ?>
        <input name="interest[]" type="checkbox" id="interest" value="<?php echo $row_product_cat['no'];  ?>" <?php chkCheckbox($row_product_cat['no'],$member['interest'])?>><?php echo $row_product_cat['name'];  ?>
        <?php } ?>       
        </td>
      </tr>
      <tr>
        <td class="tb_edit_title">興趣2</td>
        <td class="tb_edit_info">
        <?php 	
        foreach($interest_array as $key => $value){
        ?>
        <input name="interest_2[]" type="checkbox" id="interest_2" value="<?php echo $key;  ?>" <?php chkCheckbox($key,$member['interest_2'])?>><?php echo $value;  ?>
        <?php } ?> 
        </td>
      </tr>
      <tr>
        <td class="tb_edit_title">消費類型</td>
        <td class="tb_edit_info">
        <select name="buy_type" id="buy_type">
        <option value="">請選擇</option>
        <?php 	
        $sql_product_cat="SELECT * FROM `product_cat` ORDER  BY `name`";
        $rs_product_cat=mysql_query($sql_product_cat);
        while($row_product_cat=mysql_fetch_array($rs_product_cat,MYSQL_ASSOC)){
        ?>
        <option value="<?php echo $row_product_cat['no'];  ?>"<?php  chkSelected($row_product_cat['no'],$member['buy_type'])?>> <?php echo $row_product_cat['name'];  ?> </option>
        <?php } ?>
        </select></td>
      </tr>
      <tr>
        <td class="tb_edit_title">消費類型2</td>
        <td class="tb_edit_info"><select name="buy_type_2" id="buy_type_2">
        <option value="">請選擇</option>
        <?php 	
        foreach($buy_type_array as $key => $value){
        ?>
          <option value="<?php echo $key;  ?>"<?php  chkSelected($key,$member['buy_type_2'])?>> <?php echo $value;  ?></option>
          <?php } ?>
        </select></td>
      </tr>
      <tr>
        <td class="tb_edit_title">性別</td>
        <td class="tb_edit_info">男性
          <input type="radio"  value="1" <?php if(1==$member['sex']){ echo"checked"; }?> name="sex" id="sex">
          &nbsp;&nbsp;&nbsp;女性
          <input type="radio"  value="0" <?php if(0==$member['sex']){ echo"checked"; }?> name="sex" id="sex"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">生日</td>
        <td class="tb_edit_info"><select name="bdy" id="bdy">
          <?php  getDateSelectOption2(1900, 2012,$bday[0],'4');  ?>
        </select>
          <select name="bdm" id="bdm">
            <?php  getDateSelectOption2(1,12 ,$bday[1],'2');  ?>
          </select>
          <select name="bdd" id="bdd">
            <?php  getDateSelectOption2(1,31 , $bday[2],'2');  ?>
          </select></td>
      </tr>
      <tr>
    <td class="tb_edit_title">電話</td>
    <td class="tb_edit_info"><input name="tel" type="text" id="tel" value="<? echo $member['tel']; ?>" size="40" /></td>
  </tr> 
      <tr>
        <td class="tb_edit_title">行動電話</td>
        <td class="tb_edit_info"><input name="mphone" type="text" id="mphone" value="<? echo $member['mphone']; ?>" size="40" /></td>
      </tr>
      <tr>
        <td class="tb_edit_title">居住地址</td>
        <td class="tb_edit_info"><input name="zipcode" class="text4" id="zipcode" size="4" />
          <select name="city" id="city" onChange="addOption_list(this.options[this.options.selectedIndex].text,'area','1','zipcode');">
            <option value="" selected="selected">請選擇</option>
            <option value="臺北市" >臺北市</option>
            <option value="基隆市" >基隆市</option>
            <option value="新北市" >新北市</option>
            <option value="宜蘭縣" >宜蘭縣</option>
            <option value="新竹縣市" >新竹縣市</option>
            <option value="桃園縣" >桃園縣</option>
            <option value="苗栗縣" >苗栗縣</option>
            <option value="臺中市" >臺中市</option>
            <option value="彰化縣" >彰化縣</option>
            <option value="南投縣" >南投縣</option>
            <option value="嘉義縣市" >嘉義縣市</option>
            <option value="雲林縣" >雲林縣</option>
            <option value="臺南市" >臺南市</option>
            <option value="高雄市" >高雄市</option>
            <option value="澎湖縣" >澎湖縣</option>
            <option value="屏東縣" >屏東縣</option>
            <option value="臺東縣" >臺東縣</option>
            <option value="花蓮縣" >花蓮縣</option>
            <option value="金門縣" >金門縣</option>
            <option value="連江縣" >連江縣</option>
            <option value="南海諸島" >南海諸島</option>
          </select>
          <select name="area" id="area" onChange="changZipcode('zipcode',this.options[this.options.selectedIndex].value,this.options[this.options.selectedIndex].text)">
            <option value='1' selected="selected">請選擇</option>
          </select>
          <!--預設值填入-->
          <!--  function addOption_list(傳送城市名稱,地區欄位名稱,地區欄位預設值,郵遞區號欄位名稱){   -->
          <script language="JavaScript" type="text/javascript">
               addOption_list('<? echo $member['city']; ?>','area','<? echo $member['area']; ?>','zipcode'); 
			    <!--  function citySelect(城市欄位名稱,城市欄位預設值){   -->
               citySelect('city','<? echo $member['city']; ?>');
              </script>
          <input name="addr" type="text" id="addr" value="<? echo $member['addr'];?>" size="40" /></td>
      </tr>
      <tr>
        <td class="tb_edit_title">戶籍地址</td>
        <td class="tb_edit_info"><input name="zipcode_2" class="text4" id="zipcode_2" size="4" />
          <select name="city_2" id="city_2" onChange="addOption_list(this.options[this.options.selectedIndex].text,'area_2','1','zipcode_2');">
            <option value="" selected="selected">請選擇</option>
            <option value="臺北市" >臺北市</option>
            <option value="基隆市" >基隆市</option>
            <option value="新北市" >新北市</option>
            <option value="宜蘭縣" >宜蘭縣</option>
            <option value="新竹縣市" >新竹縣市</option>
            <option value="桃園縣" >桃園縣</option>
            <option value="苗栗縣" >苗栗縣</option>
            <option value="臺中市" >臺中市</option>
            <option value="彰化縣" >彰化縣</option>
            <option value="南投縣" >南投縣</option>
            <option value="嘉義縣市" >嘉義縣市</option>
            <option value="雲林縣" >雲林縣</option>
            <option value="臺南市" >臺南市</option>
            <option value="高雄市" >高雄市</option>
            <option value="澎湖縣" >澎湖縣</option>
            <option value="屏東縣" >屏東縣</option>
            <option value="臺東縣" >臺東縣</option>
            <option value="花蓮縣" >花蓮縣</option>
            <option value="金門縣" >金門縣</option>
            <option value="連江縣" >連江縣</option>
            <option value="南海諸島" >南海諸島</option>
          </select>
          <select name="area_2" id="area_2" onChange="changZipcode('zipcode_2',this.options[this.options.selectedIndex].value,this.options[this.options.selectedIndex].text)">
            <option value='1' selected="selected">請選擇</option>
          </select>
          <!--預設值填入-->
          <!--  function addOption_list(傳送城市名稱,地區欄位名稱,地區欄位預設值,郵遞區號欄位名稱){   -->
          <script language="JavaScript" type="text/javascript">
               addOption_list('<? echo $member['city_2']; ?>','area_2','<? echo $member['area_2']; ?>','zipcode_2'); 
			    <!--  function citySelect(城市欄位名稱,城市欄位預設值){   -->
               citySelect('city_2','<? echo $member['city_2']; ?>');
              </script>
          <input name="addr_2" type="text" id="addr_2" value="<? echo $member['addr_2'];?>" size="40" /></td>
      </tr>
      <tr>
        <td class="tb_edit_title">寄送地址</td>
        <td class="tb_edit_info"><input name="zipcode_3" class="text4" id="zipcode_3" size="4" />
          <select name="city_3" id="city_3" onChange="addOption_list(this.options[this.options.selectedIndex].text,'area_3','1','zipcode_3');">
            <option value="" selected="selected">請選擇</option>
            <option value="臺北市" >臺北市</option>
            <option value="基隆市" >基隆市</option>
            <option value="新北市" >新北市</option>
            <option value="宜蘭縣" >宜蘭縣</option>
            <option value="新竹縣市" >新竹縣市</option>
            <option value="桃園縣" >桃園縣</option>
            <option value="苗栗縣" >苗栗縣</option>
            <option value="臺中市" >臺中市</option>
            <option value="彰化縣" >彰化縣</option>
            <option value="南投縣" >南投縣</option>
            <option value="嘉義縣市" >嘉義縣市</option>
            <option value="雲林縣" >雲林縣</option>
            <option value="臺南市" >臺南市</option>
            <option value="高雄市" >高雄市</option>
            <option value="澎湖縣" >澎湖縣</option>
            <option value="屏東縣" >屏東縣</option>
            <option value="臺東縣" >臺東縣</option>
            <option value="花蓮縣" >花蓮縣</option>
            <option value="金門縣" >金門縣</option>
            <option value="連江縣" >連江縣</option>
            <option value="南海諸島" >南海諸島</option>
          </select>
          <select name="area_3" id="area_3" onChange="changZipcode('zipcode_3',this.options[this.options.selectedIndex].value,this.options[this.options.selectedIndex].text)">
            <option value='1' selected="selected">請選擇</option>
          </select>
          <!--預設值填入-->
          <!--  function addOption_list(傳送城市名稱,地區欄位名稱,地區欄位預設值,郵遞區號欄位名稱){   -->
          <script language="JavaScript" type="text/javascript">
               addOption_list('<? echo $member['city_3']; ?>','area_3','<? echo $member['area_3']; ?>','zipcode_3'); 
			    <!--  function citySelect(城市欄位名稱,城市欄位預設值){   -->
               citySelect('city_3','<? echo $member['city_3']; ?>');
          </script>
          <input name="addr_3" type="text" id="addr_3" value="<? echo $member['addr_3'];?>" size="40" /></td>
      </tr>
      <tr>
        <td class="tb_edit_title">會員型態</td>
        <td class="tb_edit_info">未認證
          <input type="radio"  value="0" <?php if(0==$member['vip_mode']){ echo"checked"; }?> name="vip_mode" id="vip_mode">
          &nbsp;&nbsp;&nbsp;已認證
          <input type="radio"  value="1" <?php if(1==$member['vip_mode']){ echo"checked"; }?> name="vip_mode" id="vip_mode">
          &nbsp;&nbsp;&nbsp;已刪除
          <input type="radio"  value="9" <?php if(9==$member['vip_mode']){ echo"checked"; }?> name="vip_mode" id="vip_mode"> 
         </td>
      </tr>
      <tr>
        <td class="tb_edit_title">電子報</td>
        <td class="tb_edit_info">願意
          <input name="esend" type="radio" id="esend"  value="1" <?php chkChecked($member['esend'],1)?> >
          &nbsp;&nbsp;&nbsp;
          反對
          <input type="radio"  value="0" <?php chkChecked($member['esend'],0)?> name="esend" id="esend"></td>
      </tr>  
      <tr>
        <td class="tb_edit_title">認證碼</td>
        <td class="tb_edit_info"><input name="authcode" type="text" id="authcode" value="<? echo $member['authcode']; ?>" size="40" /></td>
      </tr>
      <tr>
        <td class="tb_edit_title">會員照片</td>
        <td class="tb_edit_info"><input type="file" name="add_pic[]" id="fileField">
        (上傳尺寸360x360)
        <?php
        $filename = '../../images/upload/member/member_'.sprintf("%06d",$member['no']).'_1.jpg';
        if (file_exists($filename)) {    echo '<br><br><img src="'."$filename".'" width="150" height="150">';} 
        ?></td>
      </tr>
      <tr>
        <td colspan="2" align="center"><input type="hidden" value="<? echo $member['no']; ?>" name="no" id="no" />
          <input type="hidden" value="<? echo $page; ?>" name="page" id="page" />
          <input type="submit" name="button2" id="button2" value="<?php if($no==""){ echo'新增資料'; }else{ echo'修改資料';}?>" ></td>
      </tr>
    </table>
    <br>
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





