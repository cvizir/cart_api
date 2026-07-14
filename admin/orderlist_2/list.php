<? 
	include_once("../session.php");
	require_once("../../include/config.inc.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");
	
	$page=($_GET["page"]!="")?$_GET["page"]: 1;
	$pagesize=20;	
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
	$where=" WHERE `order_code` LIKE '%$sh_txt%' OR `invoice_name` LIKE '%$sh_txt%' OR `invoice_email` LIKE '%$sh_txt%'  OR `order_state` LIKE '%$sh_txt%' ";	
	}	
	$order=' ORDER BY buytime DESC';
 	$orderlistClass = new DBClass('orderlist',$page, $pagesize);
	$orderlistList = $orderlistClass->getAdminList($where,$order,0);
	$total = $articleClass->total;//count($articleList) 

?>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=ADMIN_TITLE?></title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="../../js/jquery.simplemodal.1.4.1.min.js"></script>
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
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2"><?php require_once("../header.php");?></td>
  </tr>
  <tr>
    <td width="10%" align="left" valign="top" bgcolor="#525252"><?php require_once("../menu.php");?></td>
<td width="90%" valign="top"><div align="center"><br />
            <br />
            <table width="975" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
              <tr>
                <td width="967"><table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr>
                      <td width="25%" class="tb_menu_title">訂單資料</td>
                      <td align="right" class="tb_menu_title"><input name="Submit" type="button" value="新增訂單" onClick="onAdd()" /></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td height="300" align="center" valign="top"><table width="90%">
                <tr>
                  <td><form name="form1" method="post" action="excel_tr.php">
                    <select name="sh_state" id="sh_state"  >
                      <option value="all"  <?php chkSelected('all',$_REQUEST['sh_state']); ?> >全部</option>
                      <option value="作業中"  <?php chkSelected('作業中',$_REQUEST['sh_state']); ?> >作業中</option>
                      <option value="確認付款"  <?php chkSelected('確認付款',$_REQUEST['sh_state']); ?> >確認付款</option>
                      <option value="訂購完成"  <?php chkSelected('訂購完成',$_REQUEST['sh_state']); ?> >訂購完成</option>
                      <option value="取消訂單"  <?php chkSelected('取消訂單',$_REQUEST['sh_state']); ?> >取消訂單</option>
                      <option value="已出貨"  <?php chkSelected('已出貨',$_REQUEST['sh_state']); ?> >已出貨</option>
                    </select>
                    </span>開始日期
<input type="text" value="<? echo $_REQUEST['start_time']; ?>" name="start_time" id="start_time">
                    <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'start_time'})" onfocus="WdatePicker({errDealMode:1})">&nbsp;~&nbsp;結束日期
                     <input type="text" value="<? echo $_REQUEST['end_time']; ?>" name="end_time" id="end_time">
                    <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'end_time'})" onfocus="WdatePicker({errDealMode:1})">
                    <input type="submit" name="button" id="button" value="送出">
                  </form></td>
                </tr>
                <tr>
                      <td>
                      <form name="form2" method="post" action="list.php">
                      <input name="sh_txt" type="text"><input name="" type="submit" value="送出">
                      </form>
                      </td>
                  </tr>
                    <tr>
                      <td align="center" valign="top">
                          <table width="966" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                            <tr>
                              <td width="158" class="tb_list_title">收件人</td>
                              <td width="211" class="tb_list_title">訂單日期</td>
                              <td width="193" class="tb_list_title">訂單金額</td>
                              <td width="111" class="tb_list_title">授權資訊</td>
                              <td width="111" class="tb_list_title">訂單狀態</td>
                              <td width="144" class="tb_list_title">編輯</td>
                            </tr>
                          <?
                          $no = ($page-1) * $pagesize;
                          while($orderlist=@mysql_fetch_array($orderlistList,MYSQL_ASSOC)){
                          $no++;
                          ?>
                            <tr>
                              <td class="tb_list_info"><?php echo ($orderlist['receives_name']!="")? $orderlist['receives_name']: "&nbsp;";?></td>
                              <td class="tb_list_info"><?php echo ($orderlist['buytime']!="")? $orderlist['buytime']: "&nbsp;";?></td>
                              <td class="tb_list_info"><?php echo $orderlist['total_amount']; ?></td>
                              <td  class="tb_list_info">
                              <?php 
							  $pay_info_array = (array) simplexml_load_string($orderlist['pay_info']);
							  $AUTHINFO=(array)$pay_info_array['AUTHINFO'];
							  $AUTHMSG=$AUTHINFO['AUTHMSG'];
                              if($AUTHMSG=='授權失敗'){ echo'<font style="color:#F00;">';}
                              echo "$AUTHMSG";
                              if($AUTHMSG=='授權失敗'){ echo'</font>';}
							  ?>
                              &nbsp;</td>
                              <td class="tb_list_info">
                                <select name="order_state" id="order_state" onChange="changPayMode('<?php echo $orderlist['order_code']; ?>',this.value)" >
                                  <option value="作業中"  <?php chkSelected('作業中',$orderlist['order_state']); ?> >作業中</option>
                                  <option value="確認付款"  <?php chkSelected('確認付款',$orderlist['order_state']); ?> >確認付款</option>
                                  <option value="刷卡失敗"  <?php chkSelected('刷卡失敗',$orderlist['order_state']); ?> >刷卡失敗</option>
                                  <option value="訂購完成"  <?php chkSelected('訂購完成',$orderlist['order_state']); ?> >訂購完成</option>
                                  <option value="取消訂單"  <?php chkSelected('取消訂單',$orderlist['order_state']); ?> >取消訂單</option>
                                  <option value="已出貨"  <?php chkSelected('已出貨',$orderlist['order_state']); ?> >已出貨</option>
                              </select></td>
                              <td class="tb_list_info"><input name="Submit2" type="button" value="編輯訂單" onClick="onEdit('<?=$orderlist['no'];?>')" /> 
                              <input name="Submit3" type="button" value="列印" onClick="window.open('../../order_print.php?action_mode=print&order_code=<?=$orderlist['order_code'];?>')" /></td>
                            </tr>
                            <? }?>
                          </table></td>
                    </tr>
                    <tr>
                      <td align="center" class="page"><span class="wd_white_12">
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
<style>
#showCha{

	display:none;
	padding:20px;
	overflow-y:auto;
	height:340px;

	
	}
#first{
	font-size:18px;
	font-weight:bold;
	;}
	#basic-modal-content {display:none;}

/* Overlay */
#simplemodal-overlay {background-color:#000; cursor:wait;}

/* Container */
#simplemodal-container {height:360px; width:600px; color:#bbb; background-color:#333; border:4px solid #444; padding:12px;}
#simplemodal-container .simplemodal-data {padding:8px;}
#simplemodal-container code {background:#141414; border-left:3px solid #65B43D; color:#bbb; display:block; font-size:12px; margin-bottom:12px; padding:4px 6px 6px;}
#simplemodal-container a {color:#ddd;}
#simplemodal-container a.modalCloseImg {background:url(../../img/x.png) no-repeat; width:25px; height:29px; display:inline; z-index:3200; position:absolute; top:-15px; right:-16px; cursor:pointer;}
#simplemodal-container h3 {color:#84b8d9;}

</style>
<div id='showCha'><div id='first'>正在進行ATM銷帳更新,請稍後...</div></div>
<script>
cha=function(){
	$("#showCha").modal();
	$("#showCha").load('pay_cha.php');
	
	}

</script>
</body>
</html>





