<?php 
	require_once("../../include/config.inc.php");
	include_once("../session.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");
	//print_r($order_all_list);
	
	
	$no = $_REQUEST["no"];	
	$cms_mode = $_REQUEST["cms_mode"];	
	
	$m_action = $_REQUEST["m_action"];
	$add_order_code = $_REQUEST["add_order_code"];
	$tr_money = $_REQUEST["tr_money"];
	$tr_ps = $_REQUEST["tr_ps"];
	$ot_money = $_REQUEST["ot_money"];
	$ot_ps = $_REQUEST["ot_ps"];
	$add_money=$ot_money+$tr_money;
	if($m_action=='add_tr' && $add_order_code!='' && $tr_money!=''){
	$sql_update="UPDATE `orderlist` SET  `transportation` = '$tr_money' ,`transportation_ps` = '$tr_ps' ,`other_price` = '$ot_money' ,`other_ps` = '$ot_ps' , `total_amount` = `total_amount` + $add_money  WHERE `order_code` ='$add_order_code'";
    mysql_query($sql_update);	
	}
	
	
	$sh_post = $_SERVER['QUERY_STRING'];
	$pd_no = $_REQUEST["pd_no"];	
    $stste=array('0'=>'作業中','3'=>'確認付款','5'=>'訂購完成','9'=>'取消訂單');	

	if($no<>""){
		startDB();
		$sql_edit="SELECT * FROM `orderlist` WHERE `no`='$no'";
		$rs_edit=mysql_query($sql_edit);
        $orderlist=mysql_fetch_array($rs_edit,MYSQL_ASSOC);
		
		 $sql_member="SELECT * FROM `member` WHERE `no`='".$orderlist['member_no']."'";
						  $rs_member=mysql_query($sql_member);
						  $num_member=mysql_num_rows($rs_member);
						  $row_member=mysql_fetch_array($rs_member,MYSQL_ASSOC);
	}
	//$order_all_list=@json_decode($orderlist['order_info'],true);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo ADMIN_TITLE; ?></title>

<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache"> 
<meta http-equiv="expires" content="0">
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
<script>
  
	
    function resend($no,$order_code,$member_code){
		//alert('$no='+$no+'$order_code='+$order_code);
		$.ajax({
		url: "resend.php",
		type: "POST",
		data: {
			no: $no,
			member_code: $member_code,
			order_code: $order_code
		},
		dataType: "JSON",
		success: function (jsonStr) {
	       alert('已寄出!!');
		}
		});
	}
	
	
/*





		alert('$no='.$no.'$order_code='.$order_code.'$order_state='.$order_state);

cha=function(){
	$("#showCha").modal();
	$("#showCha").load('pay_cha.php');
	
	}

*/
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  
   <tr>
    <td width="10%" align="left" bgcolor="525252" valign="top"><?php require_once("../menu.php"); ?></td>
<td width="90%" valign="top"><br />
            <br />
            <table width="95%" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
              <tr>
                <td width="1170"><table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr>
                      <td class="tb_menu_title">訂單資料</td>
                      <td align="right" class="tb_menu_title"><input name="Submit" type="button" value="回上一頁" onClick="javascrip:window.history.go(-1);" /></td>
                    </tr>
                </table></td>
              </tr>
              <tr>
                <td height="300" align="center" valign="top"><table width="99%">
                    <tr>
                      <td>
                      <form name="form1" method="post" action="">
                      <br>
                      <table width="68%" style="font-size:16px;" border="0" align="center" cellpadding="0" cellspacing="0">

                          <tr>
                            <td width="40%" align="left">國外運費&nbsp;:&nbsp;<input type="text" name="tr_money" id="tr_money"></td>
                            <td width="35%" align="left">備註&nbsp;:&nbsp;<input type="text" name="tr_ps" id="tr_ps"></td>
                            <td width="25%" align="left">
                            <input type="submit" name="button2" id="button2" value="運費送出">
                            <input name="m_action" type="hidden" value="add_tr">
                            <input name="add_order_code" type="hidden" value="<? echo $orderlist['order_code']; ?>"></td>
                          </tr>
                          <tr>
                            <td align="left">其他費用&nbsp;:&nbsp;<input type="text" name="ot_money" id="ot_money"></td>
                            <td align="left">備註&nbsp;:&nbsp;<input type="text" name="ot_ps" id="ot_ps"></td>
                            <td align="left">&nbsp;</td>
                          </tr>
                      </table>
                      </form>
                      </td>
                    </tr>
                    <tr>
                      <td align="center" valign="top">
					  <?php echo'<form action="save.php?'.$sh_post.'" method="post" id="form1" enctype="multipart/form-data" name="CodeForm" onSubmit="return validator(this)">'; ?>
                          <table width="99%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                            <tr>
                               <td height="30" align="center" class="tb_edit_title">訂單狀態</td>
                              <td width="272"> <select name="order_state" id="order_state"><?php echo $orderlist['order_state']; ?>
                              <option value="未付款" selected="selected"  <?php chkSelected('未付款',$orderlist['order_state']); ?> >未付款</option>
                              <option value="確認運費"  <?php chkSelected('確認運費',$orderlist['order_state']); ?> >確認運費</option>
                              <option value="待付款"  <?php chkSelected('待付款',$orderlist['order_state']); ?> >待付款</option>
                              
<option value="刷卡失敗"  <?php chkSelected('刷卡失敗',$orderlist['order_state']); ?> >刷卡失敗</option>
                              <option value="完成訂購"  <?php chkSelected('完成訂購',$orderlist['order_state']); ?> >完成訂購</option>
                              <option value="訂單處理中"  <?php chkSelected('訂單處理中',$orderlist['order_state']); ?> >訂單處理中</option>
 
<option value="已出貨"  <?php chkSelected('已出貨',$orderlist['order_state']); ?> >已出貨</option>

<option value="先出貨待付款"  <?php chkSelected('先出貨待付款',$orderlist['order_state']); ?> >先出貨待付款</option>
                              <option value="取消訂單"  <?php chkSelected('取消訂單',$orderlist['order_state']); ?> >取消訂單</option>
                                  <option value="退貨訂單"  <?php chkSelected('退貨訂單',$orderlist['order_state']); ?> >退貨訂單</option>

<option value="失效訂單"  <?php chkSelected('退貨訂單',$orderlist['order_state']); ?> >失效訂單</option>                              
                              </select></td>
                              <td width="140" align="center">
<input name="Submit5" type="button" value="重寄Email訂單" onClick="resend('<?php echo $orderlist['no']; ?>','<?php echo $orderlist['order_code']; ?>','<?php echo $orderlist['member_code']; ?>')" />
</td>
                              <td width="395" align="right">
                                                  <input type="hidden" value="<? echo $orderlist['no']; ?>" name="no" id="no" />  
                              <input type="hidden" value="<? echo $orderlist['uptime']; ?>" name="uptime" id="uptime" />  
                              <input type="hidden" value="<? echo $orderlist['buytime']; ?>" name="buytime" id="buytime" />  
                              <input type="hidden" value="<? echo $orderlist['order_code']; ?>" name="order_code" id="order_code" />  
                              <input type="hidden" value="<? echo $orderlist['order_state']; ?>" name="old_order_state" id="old_order_state" />  
                              
                              <input type="submit" name="button" id="button" value="<?php if($cms_mode=="add"){ echo'新增資料'; }else{ echo'修改資料';}?>" ></td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">修改日期</td>
                              <td colspan="3"><?php echo $orderlist['uptime']; ?>&nbsp;</td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">訂單日期</td>
                              <td colspan="3"><?php echo $orderlist['buytime']; ?></td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">訂單編號</td>
                              <td colspan="3"> <? echo 'C17'.sprintf("%06d",$orderlist['no']); ?></td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">訂單編碼</td>
                              <td colspan="3"><? echo $orderlist['order_code']; ?></td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">物流公司</td>
                              <td colspan="3">
                              新竹物流<input  type="radio" value="新竹物流" name="post_cpy" id="post_cpy"   <?php chkChecked('新竹物流',$orderlist['post_cpy']); ?> >&nbsp;&nbsp;&nbsp; 
                              郵政物流<input  type="radio" value="郵政物流" name="post_cpy" id="post_cpy"   <?php chkChecked('郵政物流',$orderlist['post_cpy']); ?> >&nbsp;&nbsp;&nbsp;
                              順豐快遞<input  type="radio" value="順豐快遞" name="post_cpy" id="post_cpy"   <?php chkChecked('順豐快遞',$orderlist['post_cpy']); ?> >&nbsp;&nbsp;&nbsp;
                               通盈通運<input  type="radio" value="通盈通運" name="post_cpy" id="post_cpy"   <?php chkChecked('通盈通運',$orderlist['post_cpy']); ?> >&nbsp;&nbsp;&nbsp;
                              海外船運<input  type="radio" value="海外船運" name="post_cpy" id="post_cpy"   <?php chkChecked('海外船運',$orderlist['post_cpy']); ?> >&nbsp;&nbsp;&nbsp;
                              </td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">寄送日期</td>
                              <td colspan="3"><input type="text" value="<? echo $orderlist['post_date']; ?>" name="post_date" id="post_date">
          <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',el:'post_date'})" onfocus="WdatePicker({errDealMode:1})"></td>
                              
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">寄送貨號</td>
                              <td colspan="3"><input name="post_num" type="text" id="post_num" value="<?php echo $orderlist['post_num']; ?>" size="30" /></td>
                              
                            </tr>
                            <tr>
                             <td align="center" valign="middle" class="tb_edit_title">訂單備註<br>
                              (管理員填寫)<br></td>
                              <td colspan="3">
<textarea name="order_ps" id="order_ps" style="width:98%; " cols="45" rows="5"><?php echo $orderlist['order_ps']; ?></textarea>                             </td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">匯款號碼</td>
                              <td colspan="3"><input name="atm_num" type="text" id="atm_num" value="<?php echo $orderlist['atm_num']; ?>" size="30" /></td>
                            </tr>
                              <tr>
                              <td align="center" class="tb_edit_title">超商代碼</td>
                              <td colspan="3"><input name="store_num" type="text" id="store_num" value="<?php echo $orderlist['store_num']; ?>" size="30" /></td>
                            </tr>
                            <tr>
                              <td width="103" align="center" class="tb_edit_title">會員編號</td>
                              <td colspan="3"><input name="member_no" type="text" id="member_no" value="<?php echo $orderlist['member_no']; ?>" size="30" readonly /> 
                              <?php echo $mem_lv_array[$row_member['mem_lv']]; ?>-<?php echo $row_member['name']; ?>
                              </td>
                            </tr>
                            <tr>
                              <td align="center" class="tb_edit_title">會員代碼</td>
                              <td colspan="3"><input name="member_code" type="text" id="member_code" value="<?php echo $orderlist['member_code']; ?>" size="30" readonly /></td>
                            </tr>
                            <tr>
                              <td height="30" align="center" class="tb_edit_title">收件人姓名</td>
                              <td height="30" colspan="3"><input name="invoice_name" type="text" id="invoice_name" value="<?php echo $orderlist['invoice_name']; ?>" size="30" /></td>
                            </tr>
                            <tr>
                              <td height="30" align="center" class="tb_edit_title">收件人電話</td>
                              <td height="30" colspan="3"><input name="invoice_tel" type="text" id="invoice_tel" value="<?php echo $orderlist['invoice_tel']; ?>" size="30" /></td>
                            </tr>
                            <tr>
                              <td height="30" align="center" class="tb_edit_title">訂單信箱</td>
                              <td height="30" colspan="3"><input name="invoice_email" type="text" id="invoice_email" value="<?php echo $orderlist['invoice_email']; ?>" size="30"  /></td>
                            </tr>
                            <tr>
                              <td height="30" align="center" class="tb_edit_title">收件人地址</td>
                              <td height="30" colspan="3"><span class="accInfMainContentForm">
                                <select name="invoice_country" id="invoice_country" class="accInfAddWh" >
                                    <option value="" selected="selected">請選擇</option>
                                    <option value="tw">台灣</option>
                                    <option value="Afghanistan">Afghanistan</option>
                                    <option value="Alaska">Alaska</option>
                                    <option value="Albania">Albania</option>
                                    <option value="Algeria">Algeria</option>
                                    <option value="American+Samoa">American Samoa</option>
                                    <option value="Andorra">Andorra</option>                        
                                    <option value="Angola">Angola</option>                        
                                    <option value="Anguilla">Anguilla</option>                        
                                    <option value="Antarctica">Antarctica</option>                        
                                    <option value="Antigua">Antigua</option>                        
                                    <option value="Argentina">Argentina</option>                        
                                    <option value="Armenia">Armenia</option>                        
                                    <option value="Aruba">Aruba</option>                        
                                    <option value="Ascension">Ascension</option>                        
                                    <option value="Australia">Australia</option>                        
                                    <option value="Austria">Austria</option>                        
                                    <option value="Azerbaijan">Azerbaijan</option>                        
                                    <option value="Bahamas">Bahamas</option>                        
                                    <option value="Bahrain">Bahrain</option>                        
                                    <option value="Bangladesh">Bangladesh</option>                        
                                    <option value="Barbados">Barbados</option>                        
                                    <option value="Belarus">Belarus</option>                        
                                    <option value="Belgium">Belgium</option>                        
                                    <option value="Belize">Belize</option>                        
                                    <option value="Benin">Benin</option>                        
                                    <option value="Bermuda">Bermuda</option>                        
                                    <option value="Bhutan">Bhutan</option>                        
                                    <option value="Bolivia">Bolivia</option>                        
                                    <option value="Bosnia">Bosnia</option>                        
                                    <option value="Botswana">Botswana</option>                        
                                    <option value="Brazil">Brazil</option>                        
                                    <option value="British+Virgin+IS.">British Virgin IS.</option>                        
                                    <option value="Brunei">Brunei</option>                        
                                    <option value="Bulgaria">Bulgaria</option>                        
                                    <option value="Burkina+Faso">Burkina Faso</option>                        
                                    <option value="Burundi">Burundi</option>                        
                                    <option value="Cambodia">Cambodia</option>                        
                                    <option value="Cameroon">Cameroon</option>                        
                                    <option value="Canada">Canada</option>                        
                                    <option value="Cape+Verde+IS.">Cape Verde IS.</option>                        
                                    <option value="Cayman+IS.">Cayman IS.</option>                        
                                    <option value="Central+African+REP.">Central African REP.</option>                        
                                    <option value="Chad">Chad</option>                        
                                    <option value="Chile">Chile</option>                        
                                    <option value="China">中國</option>                        
                                    <option value="Colombia">Colombia</option>                        
                                    <option value="Comoros+IS.">Comoros IS.</option>                        
                                    <option value="Congo+REP.">Congo REP.</option>                        
                                    <option value="Cook+IS.">Cook IS.</option>                        
                                    <option value="Costa+Rica">Costa Rica</option>                        
                                    <option value="Croatia">Croatia</option>                        
                                    <option value="Cuba">Cuba</option>                        
                                    <option value="Cyprus">Cyprus</option>                        
                                    <option value="Czech+Republic">Czech Republic</option>                        
                                    <option value="DEM.+REP.+Of+Congo">DEM. REP. Of Congo</option>                        
                                    <option value="Denmark">Denmark</option>                        
                                    <option value="Diego+Garcia">Diego Garcia</option>                        
                                    <option value="Djibouti+REP.">Djibouti REP.</option>                        
                                    <option value="Dominica+IS.">Dominica IS.</option>                        
                                    <option value="Dominican+REP.">Dominican REP.</option>                        
                                    <option value="East+Timor">East Timor</option>                        
                                    <option value="Ecuador">Ecuador</option>                        
                                    <option value="Egypt">Egypt</option>                        
                                    <option value="El+Salvador">El Salvador</option>                        
                                    <option value="Equatorial+Guinea">Equatorial Guinea</option>                        
                                    <option value="Eritrea">Eritrea</option>                        
                                    <option value="Estonia">Estonia</option>                       
                                    <option value="Ethiopia">Ethiopia</option>                        
                                    <option value="Falkland">Falkland</option>                        
                                    <option value="Faroe+IS.">Faroe IS.</option>                        
                                    <option value="Fiji+IS.">Fiji IS.</option>                        
                                    <option value="Finland">Finland</option>                        
                                    <option value="France">France</option>                        
                                    <option value="French+Antilles">French Antilles</option>                        
                                    <option value="French+Guiana">French Guiana</option>                        
                                    <option value="French+Polynesia">French Polynesia</option>                        
                                    <option value="Gabon">Gabon</option>                        
                                    <option value="Gambia">Gambia</option>                        
                                    <option value="Georgia">Georgia</option>                        
                                    <option value="Germany">Germany</option>                        
                                    <option value="Ghana">Ghana</option>                        
                                    <option value="Gibraltar">Gibraltar</option>                        
                                    <option value="Greece">Greece</option>                        
                                    <option value="Greenland">Greenland</option>                        
                                    <option value="Grenada">Grenada</option>                        
                                    <option value="Guam">Guam</option>                        
                                    <option value="Guatemala">Guatemala</option>                        
                                    <option value="Guinea+REP.">Guinea REP.</option>                        
                                    <option value="Guinea-Bissau">Guinea-Bissau</option>                        
                                    <option value="Guyana">Guyana</option>                        
                                    <option value="Haiti">Haiti</option>                        
                                    <option value="Hawaii">Hawaii</option>                        
                                    <option value="Honduras+REP.">Honduras REP.</option>                        
                                    <option value="Hong+Kong">香港</option>                        
                                    <option value="Hungary">Hungary</option>                        
                                    <option value="Iceland">Iceland</option>                        
                                    <option value="India">India</option>                        
                                    <option value="Indonesia">Indonesia</option>                        
                                    <option value="Iran">Iran</option>                        
                                    <option value="Iraq">Iraq</option>                        
                                    <option value="Iridium">Iridium</option>                        
                                    <option value="Irish+REP.">Irish REP.</option>                        
                                    <option value="Israel">Israel</option>                        
                                    <option value="Italy">Italy</option>                        
                                    <option value="Ivory+Coast">Ivory Coast</option>                        
                                    <option value="Jamaica">Jamaica</option>                        
                                    <option value="Japan">Japan</option>                        
                                    <option value="Jordan">Jordan</option>                        
                                    <option value="Kazakhstan">Kazakhstan</option>                        
                                    <option value="Kenya">Kenya</option>                        
                                    <option value="Kiribati">Kiribati</option>                        
                                    <option value="Korea-North">Korea-North</option>                        
                                    <option value="Kuwait">Kuwait</option>                        
                                    <option value="Kyrgyzstan">Kyrgyzstan</option>                        
                                    <option value="Laos">Laos</option>                        
                                    <option value="Latvia">Latvia</option>                        
                                    <option value="Lebanon">Lebanon</option>                        
                                    <option value="Lesotho">Lesotho</option>                        
                                    <option value="Liberia">Liberia</option>                        
                                    <option value="Libya">Libya</option>                        
                                    <option value="Liechtenstein">Liechtenstein</option>                        
                                    <option value="Lithuania">Lithuania</option>                        
                                    <option value="Luxembourg">Luxembourg</option>                        
                                    <option value="Macau">Macau</option>                        
                                    <option value="Macedonia">Macedonia</option>                        
                                    <option value="Madagascar">Madagascar</option>                        
                                    <option value="Malawi">Malawi</option>                        
                                    <option value="Malaysia">Malaysia</option>                        
                                    <option value="Maldive+IS.">Maldive IS.</option>                        
                                    <option value="Mali">Mali</option>                        
                                    <option value="Malta">Malta</option>                        
                                    <option value="Mariana+IS.">Mariana IS.</option>                        
                                    <option value="Marshall+IS.">Marshall IS.</option>                        
                                    <option value="Mauritania">Mauritania</option>                        
                                    <option value="Mauritius">Mauritius</option>                        
                                    <option value="Mayotte">Mayotte</option>                        
                                    <option value="Mexico">Mexico</option>                        
                                    <option value="Micronesia">Micronesia</option>                        
                                    <option value="Moldova">Moldova</option>                        
                                    <option value="Monaco">Monaco</option>                        
                                    <option value="Mongolia">Mongolia</option>                        
                                    <option value="Montenegro">Montenegro</option>                        
                                    <option value="Montserrat">Montserrat</option>                        
                                    <option value="Morocco">Morocco</option>                        
                                    <option value="Mozambique">Mozambique</option>                        
                                    <option value="Myanmar">Myanmar</option>                        
                                    <option value="Namibia">Namibia</option>                        
                                    <option value="Nauru+IS.">Nauru IS.</option>                        
                                    <option value="Nepal">Nepal</option>                        
                                    <option value="Netherlands">Netherlands</option>                        
                                    <option value="Netherlands+Antilles">Netherlands Antilles</option>                        
                                    <option value="New+Caledonia">New Caledonia</option>                        
                                    <option value="New+Zealand">New Zealand</option>                        
                                    <option value="Nicargua">Nicargua</option>                        
                                    <option value="Niger">Niger</option>                        
                                    <option value="Nigeria">Nigeria</option>                        
                                    <option value="Niue+IS.">Niue IS.</option>                        
                                    <option value="Norfolk+IS.">Norfolk IS.</option>                        
                                    <option value="Norway">Norway</option>                        
                                    <option value="Oman">Oman</option>                        
                                    <option value="Pakistan">Pakistan</option>                        
                                    <option value="Palau">Palau</option>                        
                                    <option value="Palestine">Palestine</option>                        
                                    <option value="Panama">Panama</option>                        
                                    <option value="Papua+New+Guinea">Papua New Guinea</option>                        
                                    <option value="Paraguay">Paraguay</option>                        
                                    <option value="Peru">Peru</option>                        
                                    <option value="Philippines">Philippines</option>                        
                                    <option value="Poland">Poland</option>                        
                                    <option value="Portugal">Portugal</option>                        
                                    <option value="Puerto+Rico">Puerto Rico</option>                        
                                    <option value="Qatar">Qatar</option>                        
                                    <option value="Reunion+IS.">Reunion IS.</option>                        
                                    <option value="Romania">Romania</option>                        
                                    <option value="Russia">Russia</option>                        
                                    <option value="Rwanda">Rwanda</option>                        
                                    <option value="S.+Africa">S. Africa</option>                        
                                    <option value="S.+Korea">S. Korea</option>                        
                                    <option value="ST.+Helena">ST. Helena</option>                        
                                    <option value="ST.+Kitts">ST. Kitts</option>                        
                                    <option value="ST.+Lucia">ST. Lucia</option>                        
                                    <option value="ST.+Maarten">ST. Maarten</option>                        
                                    <option value="ST.+Pierre+%26+Miquelon">ST. Pierre & Miquelon</option>                        
                                    <option value="ST.+Vincent">ST. Vincent</option>                        
                                    <option value="San+Marino">San Marino</option>                        
                                    <option value="Sao+Tome%26+Principe">Sao Tome& Principe</option>                        
                                    <option value="Saudi+Arabia">Saudi Arabia</option>                        
                                    <option value="Senegal">Senegal</option>                        
                                    <option value="Serbia">Serbia</option>                        
                                    <option value="Seychelles">Seychelles</option>                        
                                    <option value="Sierra+Leone">Sierra Leone</option>                        
                                    <option value="Singapore">Singapore</option>                        
                                    <option value="Slovak+Republic">Slovak Republic</option>                        
                                    <option value="Slovenia">Slovenia</option>                        
                                    <option value="Solomon+IS.">Solomon IS.</option>                        
                                    <option value="Somalia">Somalia</option>                        
                                    <option value="South+Sudan">South Sudan</option>                        
                                    <option value="Spain">Spain</option>                        
                                    <option value="Srilanka">Srilanka</option>                        
                                    <option value="Sudan">Sudan</option>                        
                                    <option value="Surinam">Surinam</option>                        
                                    <option value="Swaziland">Swaziland</option>                        
                                    <option value="Sweden">Sweden</option>                        
                                    <option value="Switzerland">Switzerland</option>                        
                                    <option value="Syria">Syria</option>                                    
                                    <option value="Tajikistan">Tajikistan</option>                        
                                    <option value="Tanzania">Tanzania</option>                        
                                    <option value="Thailand">Thailand</option>                        
                                    <option value="Thuraya">Thuraya</option>                        
                                    <option value="Togo">Togo</option>                        
                                    <option value="Tonga+IS.">Tonga IS.</option>                        
                                    <option value="Trinidad+%26+Tobago">Trinidad & Tobago</option>                        
                                    <option value="Tunisia">Tunisia</option>                        
                                    <option value="Turkey">Turkey</option>                        
                                    <option value="Turkmenistan">Turkmenistan</option>                        
                                    <option value="Turks+%26+Caicos+IS.">Turks & Caicos IS.</option>                        
                                    <option value="Tuvalu+IS.">Tuvalu IS.</option>                        
                                    <option value="UK">UK</option>                        
                                    <option value="USA">USA</option>                        
                                    <option value="Uganda">Uganda</option>                        
                                    <option value="Ukraine">Ukraine</option>                        
                                    <option value="United+Arab+Emirates">United Arab Emirates</option>                        
                                    <option value="Uruguay">Uruguay</option>                        
                                    <option value="Uzbekistan">Uzbekistan</option>                        
                                    <option value="Vanuatu">Vanuatu</option>                        
                                    <option value="Venezuela">Venezuela</option>                        
                                    <option value="Vietnam">Vietnam</option>                        
                                    <option value="Virgin+IS.">Virgin IS.</option>                        
                                    <option value="Wallis+%26+Futuna+IS.">Wallis & Futuna IS.</option>                        
                                    <option value="Western+Samoa">Western Samoa</option>                        
                                    <option value="Yemen+Republic">Yemen Republic</option>                        
                                    <option value="Zambia">Zambia</option>                        
                                    <option value="Zimbabwe">Zimbabwe</option>
                                </select>
                                <input  name="invoice_zipcode" class="accInfFormZip" id="invoice_zipcode" size="4" />
                                </span>
                                <select name="invoice_city" id="invoice_city" class="accInfAddWh" onChange="addOption_list(this.options[this.options.selectedIndex].text,'invoice_area','1','invoice_zipcode');">
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
                                  <br />
                                  <input name="invoice_addr" type="text" id="invoice_addr" style="width:98%; " class="accInfFormWh" value="<?php echo $orderlist['invoice_addr']; ?>" size="40" />
                                </span></td>
                            

<?php /*                             <tr>
                              <td class="tb_edit_title">寄送時間</td>
                              <td class="tb_edit_info">
                 <input name="receives_time" type="radio" value="0" <?php if($orderlist['receives_time']=='0'){ echo'checked="checked"'; }?>>皆可&nbsp;
                 <input name="receives_time" type="radio" value="1" <?php if($orderlist['receives_time']=='1'){ echo'checked="checked"'; }?>>上午(12時前)&nbsp;
                 <input name="receives_time" type="radio" value="2" <?php if($orderlist['receives_time']=='2'){ echo'checked="checked"'; }?>>下午(12-17時)&nbsp;
                              </td>
                            </tr>*/?>
                            <tr> 
                                  <td class="tb_edit_title" align="center" >付款方式</td>
                                  <td colspan="3" class="tb_edit_info">
                                  信用卡
                                    <input  type="radio" value="card" name="pay_mode" id="ishow"   <?php chkChecked('card',$orderlist['pay_mode']); ?> >    
                                  &nbsp;&nbsp;&nbsp; 
                                  
                                  ATM轉帳<input  type="radio" value="atm" name="pay_mode" id="ishow"   <?php chkChecked('atm',$orderlist['pay_mode']); ?> >
                                    &nbsp;&nbsp;&nbsp; 
                                  
                                  
                                  
                                  超商付款<input  type="radio" value="store_pay" name="pay_mode" id="ishow"   <?php chkChecked('store_pay',$orderlist['pay_mode']); ?> >   
                                                                    &nbsp;&nbsp;&nbsp; 
                                  
                                  郵政劃撥<input  type="radio" value="post" name="pay_mode" id="ishow"   <?php chkChecked('post',$orderlist['pay_mode']); ?> >                               &nbsp;&nbsp;&nbsp; 
                                    &nbsp;&nbsp;&nbsp; 
                                    國外訂購<input  type="radio" value="foreign" name="pay_mode" id="ishow"   <?php chkChecked('foreign',$orderlist['pay_mode']); ?> >    
                                  &nbsp;&nbsp;&nbsp; 
                              </td>
                            </tr>

                            <tr>
                              <td class="tb_edit_title" align="center" >配送方式</td>
                              <td colspan="3" class="tb_edit_info">
                                  宅配
                                  <input  type="radio" value="post" name="get_mode" id="get_mode"   <?php chkChecked('post',$orderlist['get_mode']); ?> >
                                    &nbsp;&nbsp;&nbsp; 
                                  
                                  超商取貨<input  type="radio" value="store" name="get_mode" id="get_mode"   <?php chkChecked('store',$orderlist['get_mode']); ?> >    
                              </td>
                            </tr>
                            <tr>
                                  <td class="tb_edit_title" align="center" >發票</td>
                                  <td colspan="3" class="tb_edit_info">
                                  
                                  
                                  二聯<input  type="radio" value="2" name="invoice_type" id="ishow"   <?php chkChecked('2',$orderlist['invoice_type']); ?> >
                                    &nbsp;&nbsp;&nbsp; 
                                  
                                  三聯<input  type="radio" value="3" name="invoice_type" id="ishow"   <?php chkChecked('3',$orderlist['invoice_type']); ?> >                                    &nbsp;&nbsp;&nbsp; 
                                    </td>
                            </tr>
                            <tr>
                              <td height="30" align="center" class="tb_edit_title">公司抬頭</td>
                              <td height="30"><input name="invoice_title" type="text" id="invoice_title" value="<?php echo $orderlist['invoice_title']; ?>" size="30" readonly /></td>
                              <td height="30" align="center" class="tb_edit_title">統一編號</span></td>
                              <td height="30"><input name="invoice_num" type="text" id="invoice_num" value="<?php echo $orderlist['invoice_num']; ?>" size="30" readonly /></td>
                            </tr>
                             
                           <?php /*
                               <td height="30" align="center" class="tb_edit_title">收件人姓名</td>
                               <td height="30"><input name="receives_name" type="text" id="receives_name" value="<?php echo $orderlist['receives_name']; ?>" size="30" /></td>
                             </tr>
                            <tr>
                              <td height="30" align="center" class="tb_edit_title">收件人電話</td>
                              <td height="30"><input name="receives_tel" type="text" id="receives_tel" value="<?php echo $orderlist['receives_tel']; ?>" size="30" /></td>
                            </tr>
                            <tr>
                              <td height="30" align="center" class="tb_edit_title">收件人地址</td>
                              <td height="30"><span class="accInfMainContentForm">
                              <select name="receives_country" id="receives_country" class="accInfAddWh" >
                                <option value="" selected="selected">請選擇</option>
                                <option value="tw" <?php chkSelected('tw',$orderlist['receives_country']); ?> post_money="60" >台灣</option>

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
                          </script><br />
                          <input name="receives_addr" type="text" id="receives_addr" class="accInfFormWh" value="<?php echo $orderlist['receives_addr']; ?>" size="40" />
                          </span>
                          </td>
                             </tr> */?>
                             <tr>
                               <td height="30" align="center" class="tb_edit_title">商品小計</td>
                               <td height="30" colspan="3"><input name="amount" type="text" id="amount" value="<?php echo $orderlist['amount']; ?>" size="30" /></td>
                             </tr>
                              <tr>
                               <td height="30" align="center" class="tb_edit_title">加購小計</td>
                               <td height="30" colspan="3"><input name="total_m_addpd" type="text" id="total_m_addpd" value="<?php echo @$orderlist['total_m_addpd']; ?>" size="30" readonly /></td>
                             </tr>
                             <tr>
                               <td align="center" class="tb_edit_title">運費</td>
                               <td><input name="transportation" type="text" id="transportation" value="<?php echo $orderlist['transportation']; ?>" size="30" /></td>
                               <td height="30" align="center" class="tb_edit_title">運費備註 </span></td>
                               <td><span class="tb_edit_title">
                                 <input name="transportation_ps" type="text" id="transportation_ps" value="<?php echo $orderlist['transportation_ps']; ?>" size="30" />
                               </span></td>
                             </tr>
                             <?php /* <tr>
                               <td align="center" class="tb_edit_title">手續費</td>
                               <td><input name="pay_mode_price" type="text" id="pay_mode_price" value="<?php echo $orderlist['pay_mode_price']; ?>" size="30" /></td>
                             </tr> */?>
                            
                             <tr>
                               <td align="center" class="tb_edit_title">超商付款手續費</td>
                               <td colspan="3"><input name="pay_mode_price" type="text" id="pay_mode_price" value="<?php echo $orderlist['pay_mode_price']; ?>" size="30"  /></td>
                             </tr>
                             <tr>
                               <td align="center" class="tb_edit_title">其他費用</td>
                               <td><input name="other_price" type="text" id="other_price" value="<?php echo $orderlist['other_price']; ?>" size="30"  /></td>
                               <td height="30" align="center" class="tb_edit_title">其他費用備註</span></td>
                               <td><input name="other_ps" type="text" id="other_ps" value="<?php echo $orderlist['other_ps']; ?>" size="30" /></td>
                             </tr>
                            
                             <tr>
                               <td height="30" align="center" class="tb_edit_title">訂單金額</td>
                               <td height="30" colspan="3"><input name="total_amount" type="text" id="total_amount" value="<?php echo $orderlist['total_amount']; ?>" size="30" /></td>
                             </tr>
                             <tr>
                               <td  align="center" class="tb_edit_title">訂單內容</td>
                               <td colspan="3" ><p>
                                 <textarea name="order_info" id="order_info" style="width:98%; " cols="45" rows="30"><?php echo $orderlist['order_info']; ?></textarea>
                                 <br>
                                 <br>
                               <div id='order_msg' style="overflow:scroll; height:400px; width:95%;display:none;" ></div>
                               </td>
                             </tr>
                             <tr>
                              <td align="center" valign="middle" class="tb_edit_title">訂單備註<br>
                                (客戶填寫)</td>
                              <td colspan="3">
                              <textarea name="ps" id="ps" style="width:98%; " cols="45" rows="5"><?php echo $orderlist['ps']; ?></textarea>
                             </td>
                             </tr>
                             <tr>
                               <td align="center" class="tb_edit_title">付款資訊</td>
                               <td colspan="3"><textarea name="pay_info" style="width:98%; " cols="45" rows="5" readonly id="pay_info"><?php echo $orderlist['pay_info']; ?></textarea></td>
                             </tr>
  <tr>
                              <td colspan="4" align="center">
                              <input type="hidden" value="<? echo $orderlist['no']; ?>" name="no" id="no" />  
                              <input type="hidden" value="<? echo $orderlist['uptime']; ?>" name="uptime" id="uptime" />  
                              <input type="hidden" value="<? echo $orderlist['buytime']; ?>" name="buytime" id="buytime" />  
                              <input type="hidden" value="<? echo $orderlist['order_code']; ?>" name="order_code" id="order_code" />  
                              <input type="hidden" value="<? echo $orderlist['order_state']; ?>" name="old_order_state" id="old_order_state" />  
                              
                              <input type="submit" name="button" id="button" value="<?php if($cms_mode=="add"){ echo'新增資料'; }else{ echo'修改資料';}?>" >

                              </td>
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
$("#invoice_country").val('<?php echo $orderlist['invoice_country']; ?>');

	<?php 
	$url=WEB_ROOT.'email_order.php?order_code='.$orderlist['order_code'];
	?>
	$.ajax({
		url: '<?php echo $url; ?>',
		error: function(xhr) {
			alert('Ajax request 發生錯誤');
		},
		success: function(response) {
		    
			$('#order_msg').html(response);
		
		}
	});
alert(response);


</script>
</body>
</html>





