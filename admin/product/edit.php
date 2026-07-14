<?php 

	require_once("../../include/config.inc.php");
	include_once("../session.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");

	$pdcat_code = $_REQUEST["pdcat_code"];	
	$product_code = $_REQUEST["product_code"];	
	$cms_mode = $_REQUEST["cms_mode"];	
	$sh_post = $_SERVER['QUERY_STRING'];
	$no = $_REQUEST['no'];
	$db_name='product';
	$trace=0;
	if($pdcat_code==''){
		$sortmax_where='';
		}else{
		$sortmax_where=" WHERE `pdcat_code`='$pdcat_code'";
	}
	$sort_max=sortMax($cms_mode,$db_name,$sortmax_where,$field_name='no',$trace);
	if($no<>""){
		startDB();
		$sql_edit="SELECT * FROM `product` WHERE `no`='$no'";
		$rs_edit=mysql_query($sql_edit);
        $product=mysql_fetch_array($rs_edit,MYSQL_ASSOC);
	}
	if($cms_mode=='add'){ $product['pdcat_code']=$_REQUEST["pdcat_code"];	}
  if($product['pd_mode'] =='' || $product['pd_mode'] == 0){ $product['pd_mode'] = 1; }
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=ADMIN_TITLE?></title>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache"> 
<meta http-equiv="expires" content="0">
<script type="text/javascript" src="../../script/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../script/datepicker/WdatePicker.js"></script>
<script type="text/javascript" src="../../script/jquery.min.js"></script>
<script type="text/javascript" src="../../script/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../script/datepicker/WdatePicker.js"></script>
<script type="text/javascript" src="../../script/jquery.min.js"></script>
<script type="text/javascript">
	<?php 
	$check_txt='product_read';
	$all_str=$_SESSION["admin_pv"];
	$trace='0';
	if(!checkAdmim($all_str,$check_txt,$trace)){
	?>

 	alert("<?php echo $pv_err_msg ?>");
	window.location.href="../main.php";
 
	<?php } ?>
</script>
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
                      <td width="49%" class="tb_menu_title">產品管理</td>
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
        <td class="tb_edit_title">上次修改時見</td>
        <td class="tb_edit_info"><?php echo $product['uptime']; ?>&nbsp;</td>
      </tr>
      <tr>
          <td width="15%" class="tb_edit_title">名稱</td>
          <td width="84%" class="tb_edit_info"><input name="name" type="text" id="name" value="<?php echo $product['name']; ?>" size="60"></td>
    </tr>
      <tr>
        <td class="tb_edit_title">出版社</td>
        <td class="tb_edit_info">
        <select name="pd_publishing" id="pd_publishing" >
          <?php 	
            $sql_publishing="SELECT * FROM  publishing";
            $rs_publishing=mysql_query($sql_publishing);
            while($row_publishing=mysql_fetch_array($rs_publishing,MYSQL_ASSOC)){
            ?>
          <option value="<?php echo $row_publishing['publishing_code'];  ?>" <?php  chkSelected($row_publishing['publishing_code'],$product['pd_publishing'])?> >
		  	<?php echo $row_publishing['name']; ?>
          </option>
          <?php } ?>
        </select>
        </td>
      </tr>
      <tr>
        <td class="tb_edit_title">折扣方式</td>
        <td class="tb_edit_info">
        <select name="discount_code" id="discount_code" >
          <?php 	
            $sql_discount="SELECT * FROM discount";
            $rs_discount=mysql_query($sql_discount);
            while($row_discount=mysql_fetch_array($rs_discount,MYSQL_ASSOC)){
            ?>
          <option value="<?php echo $row_discount['discount_code'];  ?>" <?php chkSelected($row_discount['discount_code'],$product['discount_code'])?> >
		  	<?php echo $row_discount['name']; ?>
          </option>
          <?php } ?>
        </select>
        </td>
      </tr>
      <tr>
        <td class="tb_edit_title">新品日期</td>
        <td class="tb_edit_info"><input type="text" value="<? echo $product['new_pd_date']; ?>" name="new_pd_date" id="new_pd_date">
          <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',el:'new_pd_date'})" onfocus="WdatePicker({errDealMode:1})"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">出版日期</td>
        <td class="tb_edit_info"><input type="text" value="<? echo $product['pd_publish_date']; ?>" name="pd_publish_date" id="pd_publish_date">
          <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',el:'pd_publish_date'})" onfocus="WdatePicker({errDealMode:1})"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">商品類別</td>
        <td class="tb_edit_info">
        <div style="width:100%; height:200px; overflow:scroll;overflow-x: hidden;">
        <?php 
		$sql_pdcat_m="SELECT * FROM `pdcat_m` ORDER BY m_sort ASC , no DESC , uptime ASC";
		$rs_pdcat_m=mysql_query($sql_pdcat_m);
		$num_pdcat_m=mysql_num_rows($rs_pdcat_m);
		while($row_pdcat_m=mysql_fetch_array($rs_pdcat_m,MYSQL_ASSOC)){
		?>
		<input name="pdcat_code[]" type="checkbox" value="<?php echo $row_pdcat_m['pdcat_m_code']; ?>" <?php chkCheckbox($row_pdcat_m['pdcat_m_code'],$product['pdcat_code'])?>>
		<?php echo $row_pdcat_m['name']; ?>&nbsp;&nbsp;&nbsp; <br>

        <?php
		$sql_pdcat="SELECT * FROM `pdcat` WHERE `pdcat_m_code`='".$row_pdcat_m['pdcat_m_code']."' ORDER BY m_sort ASC , no DESC , uptime ASC";
		$rs_pdcat=mysql_query($sql_pdcat);
		$num_pdcat=mysql_num_rows($rs_pdcat);
		while($row_pdcat=mysql_fetch_array($rs_pdcat,MYSQL_ASSOC)){
	
		
		?>
        <input name="pdcat_code[]" type="checkbox" value="<?php echo $row_pdcat['pdcat_code']; ?>" <?php chkCheckbox($row_pdcat['pdcat_code'],$product['pdcat_code'])?>>
		<?php echo $row_pdcat['name']; ?>&nbsp;&nbsp;&nbsp; 
        <?php 
		}
		echo'<br><br>';
		 }?>
        </div>
        </td>
      </tr>
      <tr>
        <td class="tb_edit_title">優惠類別</td>
        <td class="tb_edit_info">
          <input type="radio" value="1" name="pd_mode" id="pd_mode" <?php chkChecked('1',$product['pd_mode']); ?> >單品&nbsp;&nbsp;&nbsp;
          <input type="radio" value="2" name="pd_mode" id="pd_mode" <?php chkChecked('2',$product['pd_mode']); ?> >幾本幾折&nbsp;&nbsp;&nbsp;
          <input type="radio" value="3" name="pd_mode" id="pd_mode" <?php chkChecked('3',$product['pd_mode']); ?> >全館特折&nbsp;&nbsp;&nbsp;
          <input type="radio" value="4" name="pd_mode" id="pd_mode" <?php chkChecked('4',$product['pd_mode']); ?> >加價購&nbsp;&nbsp;&nbsp;
          <input type="radio" value="5" name="pd_mode" id="pd_mode" <?php chkChecked('5',$product['pd_mode']); ?> >訂單贈品&nbsp;&nbsp;&nbsp;
        </td>
      </tr>
      <tr>
        <td class="tb_edit_title">商品編號</td>
        <td class="tb_edit_info"><input name="product_num" type="text" id="product_num" value="<?php echo $product['product_num']; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">ISBN</td>
        <td class="tb_edit_info"><input name="isbn" type="text" id="isbn" value="<?php echo $product['isbn']; ?>" size="60"></td>
      </tr>

      <tr>
        <td class="tb_edit_title">條碼</td>
        <td class="tb_edit_info"><input name="bar_code" type="text" id="bar_code" value="<?php echo $product['bar_code']; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">庫存數</td>
        <td class="tb_edit_info"><input name="pd_stock" type="text" id="pd_stock" value="<?php echo $product['pd_stock']; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">尺寸</td>
        <td class="tb_edit_info"><input name="pd_size" type="text" id="pd_size" value="<?php echo $product['pd_size']; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">適讀年齡</td>
        <td class="tb_edit_info"><input name="pd_age" type="text" id="pd_age" value="<?php echo $product['pd_age']; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">作者</td>
        <td class="tb_edit_info"><input name="pd_author" type="text" id="pd_author" value="<?php echo $product['pd_author']; ?>" size="60"></td>
      </tr>      
      <tr>
        <td class="tb_edit_title">重量(g)</td>
        <td class="tb_edit_info"><input name="pd_weight" type="text" id="pd_weight" value="<?php echo $product['pd_weight']; ?>" size="60"></td>
      </tr>
      <tr>
        <td class="tb_edit_title">系列</td>
        <td class="tb_edit_info"><input name="pd_series" type="text" id="pd_series" value="<?php echo $product['pd_series']; ?>" size="60"></td>
      </tr>
      <?php /* <tr>
        <td class="tb_edit_title">原價</td>
        <td class="tb_edit_info"><input type="text" name="o_price" id="o_price" value="<?php echo $product['o_price']; ?>" ></td>
      </tr> */?>
      <tr>
        <td class="tb_edit_title">售價</td>
        <td class="tb_edit_info"><input type="text" name="price" id="price" value="<?php echo $product['price']; ?>" ></td>
      </tr>
      <tr>
        <td class="tb_edit_title">加價購售價</td>
        <td class="tb_edit_info"><input type="text" name="addpd_price" id="addpd_price" value="<?php echo $product['addpd_price']; ?>" ></td>
      </tr>
      <tr>
        <td class="tb_edit_title">加價購標題</td>
        <td class="tb_edit_info"><input name="addpd_title" type="text" id="addpd_title" value="<?php echo $product['addpd_title']; ?>" size="60"></td>
      </tr>
    <tr>
      <td class="tb_edit_title">推薦商品</td>
      <td class="tb_edit_info">上架
        <input  type="radio" value="1" name="hot_item" id="hot_item"   <?php chkChecked('1',$product['hot_item']); ?> >
        &nbsp;&nbsp;&nbsp;
        下架
        <input type="radio"   value="0" name="hot_item" id="hot_item" <?php chkChecked('0',$product['hot_item']); ?> ></td>
    </tr>
    <tr>
      <td class="tb_edit_title">上下架</td>
      <td class="tb_edit_info">
      上架<input  type="radio" value="1" name="ishow" id="ishow"   <?php chkChecked('1',$product['ishow']); ?> >&nbsp;&nbsp;&nbsp;
      下架<input type="radio"   value="0" name="ishow" id="ishow" <?php chkChecked('0',$product['ishow']); ?> >&nbsp;&nbsp;&nbsp;
      待審查<input type="radio"   value="9" name="ishow" id="ishow" <?php chkChecked('9',$product['ishow']); ?> >&nbsp;&nbsp;&nbsp;
      </td>
    </tr>
    <tr>
      <td class="tb_edit_title">超商取貨</td>
      <td class="tb_edit_info">上架
        <input  type="radio" value="1" name="pd_get" id="pd_get"   <?php chkChecked('1',$product['pd_get']); ?> >
        &nbsp;&nbsp;&nbsp;
        下架
        <input type="radio"   value="0" name="pd_get" id="pd_get" <?php chkChecked('0',$product['pd_get']); ?> ></td>
    </tr>

    <tr>
      <td class="tb_edit_title">列表排序</td>
      <td class="tb_edit_info"><select name="m_sort" id="m_sort">
        <?php getDateSelectOption('1',$sort_max,$product['m_sort']); ?>
        </select></td>
    </tr>

    <tr>
      <td align="center" class="tb_edit_title">商品特色</td>
      <td class="tb_edit_info"><textarea class="ckeditor" name="pd_info" cols="60" rows="6"  ><?php echo $product['pd_info']; ?></textarea>
        </td>
    </tr>
    <?php /* <tr>
      <td align="center" class="tb_edit_title">Youtube程式碼</td>
      <td class="tb_edit_info"><textarea class="ckeditor" name="you_tube_code" cols="60" rows="6"  ><?php echo $product['you_tube_code']; ?></textarea>
        </td>
    </tr> */?>
    <tr>
      <td class="tb_edit_title">摘要</td>
      <td class="tb_edit_info"><textarea class="ckeditor" name="pd_summary" id="pd_summary" cols="60" rows="5"><?php echo $product['pd_summary']; ?></textarea></td>
    </tr>
    <tr>
      <td align="center" class="tb_edit_title">推薦商品</td>
      <td class="tb_edit_info"><textarea name="push_product_code" id="push_product_code" style="width:250px; height:120px;"><?php echo str_replace(",", "\r\n", $product['push_product_code']); ?></textarea></td>
    </tr>
    <tr>
      <td class="tb_edit_title">&nbsp;</td>
      <td class="tb_edit_info"><script type="text/javascript">
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
			for( $I=1; $I <= 10; $I++ ){
			$pix=($I-1);
			$filename = '../../images/upload/product/'.$photo_array[$pix];
            if ( !file_exists($filename)  || $photo_array[$pix]=='') { $filename='images/no_pic.gif';  } 
          ?>
          <div style="width:45%; height:350px;; float:left; margin:10px;">
            <ul>
              <li>
                <input type="checkbox" name="del_pic[<?php echo "$pix"; ?>]" value="1" >
                刪除此產品圖片
                <?php if($photo_ps[$I]<>""){echo '('.$photo_ps[$I].')';}?>
                </li>
              <li>
                <input id="input_file_<?php echo "$pix"; ?>" name="add_pic[<?php echo "$pix"; ?>]" type="file"  size="19" >
                <input name="Submit2" type="button" value="清除" onClick="clearFile('input_file_<?php echo "$pix"; ?>');" />
                </li>
              <li style="width:240px; height:auto;"><img  src="<?php echo"$filename";  ?>" alt="" width="240" ></li>
              </ul>
            </div>
          <?php } ?>
          </div></td>
    </tr>
    <tr>
      <td colspan="2" align="center">
        <input type="hidden" value="<?php echo $product['no']; ?>" name="no" id="no" />
        <input type="hidden" value="<?php echo $product['m_sort']; ?>" name="sort_old_value" id="sort_old_value" />
        <input type="hidden" value="<?php echo $product['product_code']; ?>" name="product_code" id="product_code" />
        <input type="hidden" value="<?php echo $product['photo']; ?>" name="photo" id="photo" />
        <input type="hidden" value="<?=$page?>" name="page" id="page" />
        <?php if(!$admin_pv_check || strExist($_SESSION["admin_pv"],'product_et')==true){?>
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





