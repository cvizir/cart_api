<? 

	include_once("../session.php");
	require_once("../../include/config.inc.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");


	$no = $_REQUEST["no"];	
	$cms_mode = $_REQUEST["cms_mode"];	
	$sh_post = $_SERVER['QUERY_STRING'];
	$pd_no = $_REQUEST["pd_no"];	
    $stste=array('0'=>'作業中','3'=>'確認付款','5'=>'訂購完成','9'=>'取消訂單');	
		
	if($no<>""){
		startDB();
		$sql_edit="SELECT * FROM `orderlist` WHERE `no`='$no'";
		$rs_edit=mysql_query($sql_edit);
        $orderlist=mysql_fetch_array($rs_edit,MYSQL_ASSOC);
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=ADMIN_TITLE?></title>
<script type="text/javascript" src="../../script/jquery-1.6.3.min.js"></script>
<script type="text/javascript" src="../../script/tw_zip.js"></script>
<script type="text/javascript" src="../../script/js_calendar/calendar.js"></script>
<script type="text/javascript" src="../../script/js_calendar/calendar-setup.js"></script>
<script type="text/javascript" src="../../script/js_calendar/lang/calendar-big5.js"></script>
<style type="text/css"> @import url("../../script/js_calendar/calendar-win2k-cold-1.css"); </style>
<link href="../style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style4 {font-size: 12px; color: #000000; }
.stylet {	font-size: 12px;
	color: #616161;
}
-->
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2"><?php require_once("../header.php"); ?></td>
  </tr>
  <tr>
    <td width="10%" align="left" bgcolor="525252" valign="top"><?php require_once("../menu.php"); ?></td>
<td width="90%" valign="top"><br />
            <br />
            <table width="845" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
              <tr>
                <td><table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr>
                      <td class="tb_menu_title">訂單資料</td>
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
                      <td align="center" valign="top">
					  <?php echo'<form action="save.php?'.$sh_post.'" method="post" id="form1" enctype="multipart/form-data" name="CodeForm" onSubmit="return validator(this)">'; ?>
                          <table width="770" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                            <tr>
                              <td align="center" class="tb_edit_title">修改日期</td>
                              <td><?php echo $orderlist['uptime']; ?>&nbsp;</td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">訂單日期</td>
                              <td><?php echo $orderlist['buytime']; ?></td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">訂單編號</td>
                              <td><? echo $orderlist['order_code']; ?></td>
                            </tr>
                            <tr>
                              <td width="145" align="center" class="tb_edit_title">會員編號</td>
                              <td width="615"><input name="member_no" type="text" id="member_no" value="<?php echo $orderlist['member_no']; ?>" size="30" />
                              </td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">會員代碼</td>
                              <td><input name="member_code" type="text" id="member_code" value="<?php echo $orderlist['member_code']; ?>" size="30" /></td>
                            </tr>
                            <tr>
                              <td class="tb_edit_title">寄送貨號</td>
                              <td class="tb_edit_info"><input name="post_num" type="text" id="post_num" value="<?php echo $orderlist['post_num']; ?>" size="30" /></td>
                            </tr>
                            <tr>
                              <td class="tb_edit_title">發票索取</td>
                              <td class="tb_edit_info">
                                <input  type="radio" value="1" name="invoice_get" id="invoice_get"   <?php chkChecked('1',$orderlist['invoice_get']); ?> >
                                索取發票 &nbsp;&nbsp;&nbsp;
                                <input type="radio"   value="0" name="invoice_get" id="invoice_get" <?php chkChecked('0',$orderlist['invoice_get']); ?> >捐贈</td>
                            </tr>

                            <tr>
                              <td height="30" align="center" class="tb_edit_title">發票人姓名</td>
                              <td height="30"><input name="invoice_name" type="text" id="invoice_name" value="<?php echo $orderlist['invoice_name']; ?>" size="30" /></td>
                            </tr>
                            <tr>
                              <td height="30" align="center" class="tb_edit_title">發票人電話</td>
                              <td height="30"><input name="invoice_tel" type="text" id="invoice_tel" value="<?php echo $orderlist['invoice_tel']; ?>" size="30" /></td>
                            </tr>
                             <tr>
                               <td height="30" align="center" class="tb_edit_title">發票人信箱</td>
                               <td height="30"><input name="invoice_email" type="text" id="invoice_email" value="<?php echo $orderlist['invoice_email']; ?>" size="30" /></td>
                             </tr>
                             <tr>
                               <td height="30" align="center" class="tb_edit_title">發票人地址</td>
                               <td height="30"><span class="accInfMainContentForm">
                              <select name="invoice_country" id="invoice_country" class="accInfAddWh" >
                                <option value="" selected="selected">請選擇</option>
                                <option value="台灣" <?php chkSelected('台灣',$orderlist['invoice_country']); ?> post_money="60" >台灣</option>
                                <option value="中國" <?php chkSelected('中國',$orderlist['invoice_country']); ?> post_money="200" >中國</option>
                                <option value="韓國" <?php chkSelected('韓國',$orderlist['invoice_country']); ?> post_money="600" >韓國</option>
                                <option value="新加坡" <?php chkSelected('新加坡',$orderlist['invoice_country']); ?>  post_money="600" >新加坡</option>
                                <option value="馬來西亞"  <?php chkSelected('馬來西亞',$orderlist['invoice_country']); ?> post_money="600" >馬來西亞</option>
                                <option value="日本" <?php chkSelected('日本',$orderlist['invoice_country']); ?>  post_money="1100" >日本</option>
                                <option value="美國" <?php chkSelected('美國',$orderlist['invoice_country']); ?> post_money="1100" >美國</option>     
                                <option value="澳洲" <?php chkSelected('澳洲',$orderlist['invoice_country']); ?> post_money="1100" >澳洲</option>  
                              </select>
                                 <input  name="invoice_zipcode" class="accInfFormZip" id="invoice_zipcode" size="4" />
                                 </span>
                                 <select name="invoice_city" id="invoice_city" class="accInfAddWh" onChange="addOption_list(this.options[this.options.selectedIndex].text,'invoice_area','1','invoice_zipcode');">
                                   <option value="0" selected="selected">請選擇</option>
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
                                   <option value="嘉義縣" >嘉義縣</option>
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
                                 <select name="invoice_area" id="invoice_area" class="accInfAddWh" onChange="changZipcode('invoice_zipcode',this.options[this.options.selectedIndex].value,this.options[this.options.selectedIndex].text)">
                                   <option value='1' selected="selected">請選擇</option>
                                 </select>
                                 <span class="accInfMainContentForm">
                                   <!--預設值填入-->
                                   <!--  function addOption_list(傳送城市名稱,地區欄位名稱,地區欄位預設值,郵遞區號欄位名稱){   -->
                                   <script language="JavaScript" type="text/javascript">
                          <?php 						 
                          if($no==""){
                          $city_def='臺北市';
						  $area_def='中正區';
                          }else{
                          $city_def=$orderlist['invoice_city'];
						  $area_def=$orderlist['invoice_area'];                  
                          }
                          ?>
                          addOption_list('<?php echo $city_def ?>','invoice_area','<?php echo $area_def ?>','invoice_zipcode'); 
                          <!--  function citySelect(城市欄位名稱,城市欄位預設值){   -->
                          citySelect('invoice_city','<?php echo $city_def ?>');
                               </script>
                                   <input name="invoice_addr" type="text" id="invoice_addr" class="accInfFormWh" value="<?php echo $orderlist['invoice_addr']; ?>" size="40" />
                                 </span></td>
                             </tr>
                             <tr>
                               <td height="30" align="center" class="tb_edit_title">收件人姓名</td>
                               <td height="30"><input name="receives_name" type="text" id="receives_name" value="<?php echo $orderlist['receives_name']; ?>" size="30" /></td>
                             </tr>
                            <tr>
                              <td height="30" align="center" class="tb_edit_title">收件人電話</td>
                              <td height="30"><input name="receives_tel" type="text" id="receives_tel" value="<?php echo $orderlist['receives_tel']; ?>" size="30" /></td>
                            </tr>
                            <tr>
                              <td height="30" align="center" class="tb_edit_title">收件人手機</td>
                              <td height="30"><input name="receives_mphone" type="text" id="receives_mphone" value="<?php echo $orderlist['receives_mphone']; ?>" size="30" /></td>
                            </tr>
                            <tr>
                              <td height="30" align="center" class="tb_edit_title">收件人地址</td>
                              <td height="30"><span class="accInfMainContentForm">
                              <select name="receives_country" id="receives_country" class="accInfAddWh" >
                                <option value="" selected="selected">請選擇</option>
                                <option value="台灣" <?php chkSelected('台灣',$orderlist['receives_country']); ?> post_money="60" >台灣</option>
                                <option value="中國" <?php chkSelected('中國',$orderlist['receives_country']); ?> post_money="200" >中國</option>
                                <option value="韓國" <?php chkSelected('韓國',$orderlist['receives_country']); ?> post_money="600" >韓國</option>
                                <option value="新加坡" <?php chkSelected('新加坡',$orderlist['receives_country']); ?>  post_money="600" >新加坡</option>
                                <option value="馬來西亞"  <?php chkSelected('馬來西亞',$orderlist['receives_country']); ?> post_money="600" >馬來西亞</option>
                                <option value="日本" <?php chkSelected('日本',$orderlist['receives_country']); ?>  post_money="1100" >日本</option>
                                <option value="美國" <?php chkSelected('美國',$orderlist['receives_country']); ?> post_money="1100" >美國</option>     
                                <option value="澳洲" <?php chkSelected('澳洲',$orderlist['receives_country']); ?> post_money="1100" >澳洲</option>  
                              </select>
<input  name="receives_zipcode" class="accInfFormZip" id="receives_zipcode" size="4" />
                              </span>
                                <select name="receives_city" id="receives_city" class="accInfAddWh" onChange="addOption_list(this.options[this.options.selectedIndex].text,'receives_area','1','receives_zipcode');">
                            <option value="0" selected="selected">請選擇</option>
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
                            <option value="嘉義縣" >嘉義縣</option>
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
                          <select name="receives_area" id="receives_area" class="accInfAddWh" onChange="changZipcode('receives_zipcode',this.options[this.options.selectedIndex].value,this.options[this.options.selectedIndex].text)">
                            <option value='1' selected="selected">請選擇</option>
                          </select>
                          <span class="accInfMainContentForm"><!--預設值填入-->
                          <!--  function addOption_list(傳送城市名稱,地區欄位名稱,地區欄位預設值,郵遞區號欄位名稱){   -->
                          <script language="JavaScript" type="text/javascript">
                          <?php 						 
                          if($no==""){
                          $city_def='臺北市';
						  $area_def='中正區';
                          }else{
                          $city_def=$orderlist['receives_city'];
						  $area_def=$orderlist['receives_area'];                  
                          }
                          ?>
                          addOption_list('<?php echo $city_def ?>','receives_area','<?php echo $area_def ?>','receives_zipcode'); 
                          <!--  function citySelect(城市欄位名稱,城市欄位預設值){   -->
                          citySelect('receives_city','<?php echo $city_def ?>');
                          </script>
                          <input name="receives_addr" type="text" id="receives_addr" class="accInfFormWh" value="<?php echo $orderlist['receives_addr']; ?>" size="40" />
                          </span></td>
                            </tr>
                             <tr>
                               <td align="center" class="tb_edit_title">運費</td>
                               <td><input name="transportation" type="text" id="transportation" value="<?php echo $orderlist['transportation']; ?>" size="30" /></td>
                             </tr>
                             <tr>
                               <td height="30" align="center" class="tb_edit_title">商品小計</td>
                               <td height="30"><input name="amount" type="text" id="amount" value="<?php echo $orderlist['amount']; ?>" size="30" /></td>
                             </tr>
                             <tr>
                               <td height="30" align="center" class="tb_edit_title">訂單金額</td>
                               <td height="30"><input name="total_amount" type="text" id="total_amount" value="<?php echo $orderlist['total_amount']; ?>" size="30" /></td>
                             </tr>
                             <tr>
                               <td align="center" class="tb_edit_title">訂單內容</td>
                               <td><textarea name="order_info" id="order_info" cols="45" rows="5"><?php echo $orderlist['order_info']; ?></textarea>
                                 <br>
                                 <br>
                               <table width="100%" border="1" cellspacing="0" cellpadding="6" >
                                 <tr style="font-size:16px;">
                                   <td width="63%" align="center">名稱</td>
                                   <td width="19%" align="center">數量</td>
                                   <td width="18%" align="center">價格</td>
                                 </tr>
								  <?php
								  $order_array=json_decode($orderlist['order_info'],true);
                                  //echo sizeof($order_array);
								  //print_r($order_array);
                                  $amount=0;
                                  for( $I=1; $I <= (sizeof($order_array)); $I++ ){
                                  $key_no=key($order_array);  
								  
                                  //echo '$key_no='.$key_no;
                                  $pd_name=$order_array["$key_no"]['pd_name'];


                                  ?>
                                  <tr>
                                    <td><?php echo $order_array["$key_no"]['pd_name'] ?><?php echo $order_array["$key_no"]['pd_size_name'] ?><?php echo $order_array["$key_no"]['pd_color_name'] ?></td>
                                    <td align="center"><?php echo $order_array["$key_no"]['pd_num']; ?></td>
                                    <td align="center"><?php echo $order_array["$key_no"]['pd_price']; ?></td>
                                  </tr>
                                  <?php	
								  $amount=$amount+$order_array["$key_no"]['pd_num']*$order_array["$key_no"]['pd_price'];
                                  next($order_array);
                                  }
                                  ?>
                                 <tr>
                                   <td colspan="3" align="right">總計&nbsp;&nbsp;<?php echo $amount; ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                 </tr>
                               </table></td>
                             </tr>
                             <tr>
                               <td align="center" class="tb_edit_title">付款資訊</td>
                               <td><textarea name="pay_info" id="pay_info" cols="45" rows="5"><?php echo $orderlist['pay_info']; ?></textarea></td>
                             </tr>
                            <tr>
                              <td align="center" valign="middle" class="tb_edit_title">訂單資訊</td>
                              <td>
                              <textarea name="ps" id="ps" cols="45" rows="5"><?php echo $orderlist['ps']; ?></textarea>
                             </td>
                            </tr>
                            <tr>
                              <td align="center" valign="middle" class="tb_edit_title">訂單備註</td>
                              <td><textarea name="order_ps" id="order_ps" cols="45" rows="5"><?php echo $orderlist['order_ps']; ?></textarea></td>
                            </tr>

                            <tr>
                              <td height="30" align="center" class="tb_edit_title">訂單狀態</td>
                              <td height="30">
                                <select name="order_state" id="order_state">
                                  <option value="作業中"  <?php chkSelected('作業中',$orderlist['order_state']); ?> >作業中</option>
                                  <option value="確認付款"  <?php chkSelected('確認付款',$orderlist['order_state']); ?> >確認付款</option>
                                  <option value="刷卡失敗"  <?php chkSelected('刷卡失敗',$orderlist['order_state']); ?> >刷卡失敗</option>
                                  <option value="訂購完成"  <?php chkSelected('訂購完成',$orderlist['order_state']); ?> >訂購完成</option>
                                  <option value="取消訂單"  <?php chkSelected('取消訂單',$orderlist['order_state']); ?> >取消訂單</option>
                                  <option value="已出貨"  <?php chkSelected('已出貨',$orderlist['order_state']); ?> >已出貨</option>
                                </select>
                              </td>
                            </tr>
                            <tr>
                              <td colspan="2" align="center"><span class="text_1">
                              <input type="hidden" value="<? echo $orderlist['no']; ?>" name="no" id="no" />  
                              <input type="hidden" value="<? echo $orderlist['uptime']; ?>" name="uptime" id="uptime" />  
                              <input type="hidden" value="<? echo $orderlist['buytime']; ?>" name="buytime" id="buytime" />  
                              <input type="hidden" value="<? echo $orderlist['order_code']; ?>" name="order_code" id="order_code" />  
                              </span>
                              <input type="submit" name="editorder" id="button" value="修改/存儲訂單" > &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; </td>
                            </tr>
                          </table>
                      </form></td>
                    </tr>
                </table></td>
              </tr>
            </table>
        <br />
    </td>
  </tr>
</table>
<script type="text/javascript">
$('#post_country').change(function(){
  var $post_country=$('#post_country :selected').attr('post_money'); 
  //alert($post_country);
  $('#transportation').val($post_country);
});
</script>
</body>
</html>





