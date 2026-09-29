<?php
	require_once("../../include/config.inc.php");
	include_once("../session.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");
	
	$page=($_GET["page"]!="")?$_GET["page"]: 1;
	$pagesize=40;	
	$sh_txt=$_REQUEST['sh_txt'];
	

	//上下頁參數
	$argument= "&sh_txt=$sh_txt";	
	

	if($_REQUEST['mode_chang']=='ok'){
	$order_code=$_REQUEST['order_code'];
	$order_state=$_REQUEST['order_state'];	
    $sql_update="UPDATE `orderlist` SET  `order_state` = '$order_state'  WHERE `order_code` ='$order_code'";
	//echo "$sql_update";
    mysql_query($sql_update);
	}
	if($sh_txt!=""){
	$where=" WHERE `order_code` LIKE '%$sh_txt%' OR `invoice_name` LIKE '%$sh_txt%' OR `invoice_email` LIKE '%$sh_txt%'  OR `total_amount` LIKE '%$sh_txt%'OR `no` LIKE '%$sh_txt%' OR `order_state` LIKE '%$sh_txt%'";	
	}	
	$order=' ORDER BY buytime DESC';
	
	
	$page=($_GET["page"]!="")?$_GET["page"]: 1;
	$pagesize=40;	
	$sh2_txt=$_REQUEST['sh2_txt'];
	

	//上下頁參數
	$argument= "&sh2_txt=$sh2_txt";	
	if($sh2_txt!=""){
	$where=" WHERE `member_no` = '$sh2_txt'";	
	}	
	$order=' ORDER BY buytime DESC';
	
 	$orderlistClass = new DBClass('orderlist',$page, $pagesize);
	$orderlistList = $orderlistClass->getAdminList($where,$order,0);
	$total = $articleClass->total;//count($articleList) 

?>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo ADMIN_TITLE; ?></title>

<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache"> 
<meta http-equiv="expires" content="0">
<link href="../style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../script/jquery-1.6.3.min.js"></script>

<script type="text/javascript" src="../../script/datepicker/WdatePicker.js"></script>
<script language="JavaScript" type="text/JavaScript">

    sh_post='<?=$sh_post?>';
	function onDel(no,name){
		if(window.confirm("確定要刪除 [ "+name+" ] 這筆資料嗎?")){
	 	window.location.href="save.php?cms_mode=del&total=<?php echo "$total" ?>&no="+no+sh_post;
			return true;
		}
		return false;
	}
    function onEdit(no){	 	
	 	window.location.href="edit.php?cms_mode=edit&no="+no+sh_post;
	}
    function onAdd(){	 	
	 	window.location.href="edit.php?cms_mode=add"+sh_post;
	}
    function changPayMode(order_code,order_state){	 	
	 	window.location.href="list.php?mode_chang=ok&order_code="+order_code+'&order_state='+order_state;
	}
	<?php 
	if($admin_pv_check==true && strExist($_SESSION["admin_pv"],'orderlist_et')==false){
		
		echo'function onAdd(){ alert("您沒有編輯的權限");}';		echo'function onDel(){ alert("您沒有編輯的權限");}';	
	}?>
</script>
<script>
    function chang_state($no,$order_code,$order_state){
		//alert('$no='+$no+'$order_code='+$order_code+'$order_state='+$order_state);
		$.ajax({
		url: "chang_state.php",
		type: "POST",
		data: {
			no: $no,
			order_code: $order_code,
			order_state: $order_state
		},
		success: function (response) {
		}
		});
	}
	

</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  
  <tr>
    <td width="10%" align="left" valign="top" bgcolor="#525252"><?php require_once("../menu.php");?></td>
<td width="90%" valign="top"><div align="center"><br />
            <br />
            <table width="95%" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
              <tr>
                <td width="1170"><table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr>
                      <td width="25%" class="tb_menu_title">訂單資料</td>
                      <td align="right" class="tb_menu_title">
                      <input name="Submit" type="button" value="新增訂單" onClick="onAdd()" />
                      </td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td height="300" align="center" valign="top"><table width="90%">
                <tr>
                  <td colspan="3">
                    <form name="form1" method="post" action="excel_tr.php">
                   訂單匯出： <select name="sh_state" id="sh_state"  >
                      <option value="all" selected="selected"  <?php chkSelected('all',$_REQUEST['sh_state']); ?> >全部</option>
                      <option value="未付款"  <?php chkSelected('未付款',$_REQUEST['sh_state']); ?> >未付款</option>
                      <option value="待付款"  <?php chkSelected('待付款',$_REQUEST['sh_state']); ?> >待付款</option>
                      <option value="完成訂購"  <?php chkSelected('完成訂購',$_REQUEST['sh_state']); ?> >完成訂購</option>
                      <option value="確認運費"  <?php chkSelected('確認運費',$_REQUEST['sh_state']); ?> >確認運費</option>
                      <option value="訂單處理中"  <?php chkSelected('訂單處理中',$_REQUEST['sh_state']); ?> >訂單處理中</option>
                      <option value="刷卡失敗"  <?php chkSelected('刷卡失敗',$_REQUEST['sh_state']); ?> >刷卡失敗</option>                     <option value="取消訂單"  <?php chkSelected('取消訂單',$_REQUEST['sh_state']); ?> >取消訂單</option>
                      <option value="已出貨"  <?php chkSelected('已出貨',$_REQUEST['sh_state']); ?> >已出貨</option>
                      <option value="先出貨待付款"  <?php chkSelected('先出貨待付款',$_REQUEST['sh_state']); ?> >先出貨待付款</option>
                      <option value="退貨訂單"  <?php chkSelected('退貨訂單',$_REQUEST['sh_state']); ?> >退貨訂單</option>
                      <option value="失效訂單"  <?php chkSelected('退貨訂單',$_REQUEST['sh_state']); ?> >退貨訂單</option>
                      </select>
                    </span>日期
                    <input type="text" value="<? echo $_REQUEST['start_time']; ?>" name="start_time" id="start_time">
                    <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'start_time'})" onfocus="WdatePicker({errDealMode:1})">&nbsp;~
                    <input type="text" value="<? echo $_REQUEST['end_time']; ?>" name="end_time" id="end_time">
                    <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'end_time'})" onfocus="WdatePicker({errDealMode:1})">
                    <input type="submit" name="button" id="button" value="匯出"> 
                    <input type="button" name="button2" id="button2" onClick="javascript:window.location.href='list.php?del_od_file=del_od_file'" value="刪除匯出檔">
                      </p>
                  </form>
                  </td>
                  </tr>
                 <tr>
                  <td width="34%" align="left" valign="middle"><form name="form3" method="get" action="list.php">
                       
                        會員編號：W
                            <input name="sh2_txt" type="text"> 
                  <input name="" type="submit" value="送出">                        </form></td>
                  <td width="45%"  align="left" valign="middle"><form name="form2" method="get" action="list.php">
                       
                        模糊搜尋：
                            <input name="sh_txt" type="text"> 
                  <input name="" type="submit" value="送出">                        </form></td>
                  <td width="32%"  align="left" valign="middle"><form name="form3" method="get" action="list.php">
                          訂單狀態：<select name="sh_txt"  id="sh_txt">
                            <option value="%" selected>全部</option>
                            <option value="未付款">未付款</option>
                            <option value="待付款">待付款</option>
                            <option value="完成訂購">完成訂購</option>
                            <option value="確認運費">確認運費</option>
                            <option value="訂單處理中">訂單處理中</option>
                            <option value="刷卡失敗">刷卡失敗</option>
                            <option value="已出貨">已出貨</option>
                            <option value="先出貨待付款">先出貨待付款</option>                                  
                            <option value="取消訂單">取消訂單</option>
                            <option value="退貨訂單">退貨訂單</option>
                            <option value="失效訂單">失效訂單</option>
                          </select>
                          <input name="" type="submit" value="送出">
                  </form>
                  </td>
                </tr>
                 <tr>
                      <td colspan="3" align="center" valign="top">
                          <table width="1077" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                            <tr>
                              <td width="131" class="tb_list_title">訂單日期</td>
                              <td width="76" class="tb_list_title">訂單編號</td>

                              <td width="138" class="tb_list_title">購買人</td>                              <td width="70" class="tb_list_title">付款方式</td>
                              <td width="66" class="tb_list_title">訂單金額</td>
                              
                              <td width="90" class="tb_list_title">收件人</td>
                              
                              <td width="76" class="tb_list_title">匯款號碼</td>
                              <td width="110" class="tb_list_title">出貨單號</td>
                              <td width="110" class="tb_list_title">訂單狀態</td>
                              <td width="72" class="tb_list_title">編輯</td>
                            </tr>
                          <?php
                          $no = ($page-1) * $pagesize;
                          while($orderlist=@mysql_fetch_array($orderlistList,MYSQL_ASSOC)){
                          $no++;
						  
						  $sql_member="SELECT * FROM `member` WHERE `no`='".$orderlist['member_no']."'";
						  $rs_member=mysql_query($sql_member);
						  $num_member=mysql_num_rows($rs_member);
						  $row_member=mysql_fetch_array($rs_member,MYSQL_ASSOC);
                          ?>
                            <tr>
  
<td class="tb_list_info">
<a href="order_tr.php?no=<?php echo $orderlist['no']; ?>" target="_blank">
<?php echo ($orderlist['buytime']!="")? $orderlist['buytime']: "&nbsp;";?>
</a>
</td>                           
<td align="left" class="tb_list_info"style="text-align:left;">  
<a href="https://shopping.windmill.com.tw/email_order.php?order_code=<?php echo $orderlist['order_code']; ?>&member_code=<?php echo $orderlist['member_code']; ?>" target="_blank">
 <? echo 'C17'.sprintf("%06d",$orderlist['no']); ?> </a></td>
        
<td align="left" class="tb_list_info"style="text-align:left;"><?php echo $mem_lv_array[$row_member['mem_lv']]; ?>-W<?php echo $orderlist['member_no']; ?><?php echo $row_member['spc_discount']; ?></td>

<td align="left" class="tb_list_info"style="text-align:left;"><?php echo$pay_mode_array[ $orderlist['pay_mode']];?></td>
                              
<td align="right" class="tb_list_info"style="text-align:right;"><?php echo $orderlist['total_amount'];?>&nbsp;</td>



<td align="left" class="tb_list_info"style="text-align:left;"><a href="https://shopping.windmill.com.tw/EeuAher_18849/orderlist/email_order-R2.php?order_code=<?php echo $orderlist['order_code']; ?>&member_code=<?php echo $orderlist['member_code']; ?>" target="_blank">
<?php echo $orderlist['invoice_name'];?></a></td>


                              <td align="left" class="tb_list_info"><?php echo $orderlist['atm_num']; ?></td>
                              
                              <td align="left" class="tb_list_info"style="text-align:left;"><?php echo $orderlist['post_num'];?></td>
                              <td align="left" class="tb_list_info"style="text-align:left;">
                              <select onChange="chang_state('<?php echo $orderlist['no']; ?>','<?php echo $orderlist['order_code']; ?>',this.value)"  >
                              <option value="未付款"  <?php chkSelected('未付款',$orderlist['order_state']); ?> >未付款</option>
                              <option value="待付款"  <?php chkSelected('待付款',$orderlist['order_state']); ?> >待付款</option>
                              <option value="完成訂購"  <?php chkSelected('完成訂購',$orderlist['order_state']); ?> >完成訂購</option>
                              <option value="確認運費"  <?php chkSelected('確認運費',$orderlist['order_state']); ?> >確認運費</option>
                              <option value="訂單處理中"  <?php chkSelected('訂單處理中',$orderlist['order_state']); ?> >訂單處理中</option>
                              <option value="刷卡失敗"  <?php chkSelected('刷卡失敗',$orderlist['order_state']); ?> >刷卡失敗</option>                     
                              <option value="取消訂單"  <?php chkSelected('取消訂單',$orderlist['order_state']); ?> >取消訂單</option>
                              <option value="已出貨"  <?php chkSelected('已出貨',$orderlist['order_state']); ?> >已出貨</option>
                              <option value="先出貨待付款"  <?php chkSelected('先出貨待付款',$orderlist['order_state']); ?> >先出貨待付款</option>
                              <option value="退貨訂單"  <?php chkSelected('退貨訂單',$orderlist['order_state']); ?> >退貨訂單</option>
                              <option value="失效訂單"  <?php chkSelected('失效訂單',$orderlist['order_state']); ?> >失效訂單</option>
                              </select>							  </td>
                              <td class="tb_list_info"><input name="Submit2" type="button" value="編輯訂單" onClick="onEdit('<?=$orderlist['no'];?>')" /> 
                              </td>
                            </tr>
                            <? }?>
                          </table></td>
                    </tr>
                    <tr>
                      <td colspan="2" align="center" class="page"><span class="wd_white_12">
                        <?=$orderlistClass->showPageMenu($argument)?>
                      </span> </td>
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





