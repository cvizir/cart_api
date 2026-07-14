<? 
	include_once("../session.php");
	require_once("../../include/config.inc.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");

	$sh_post = $_SERVER['QUERY_STRING'];
	$pagesize=$_REQUEST["pagesize"];
	$total=$_REQUEST["total"];
	$total--;
	if($page!=1){
		if(($page-1)*$pagesize>=$total){
			$page--;
		}
	}


	$no=trim($_REQUEST['no']);
	$cms_mode=trim($_REQUEST['cms_mode']);
    $add_pic=$_FILES['add_pic'];
    $del_pic=$_POST['del_pic'];	
	
	$db_name='orderlist';
 	$web_url="list.php?page=$page";
 	$trace=1;

    if($_REQUEST['buytime']=="" || $_REQUEST['buytime']=='0000-00-00 00:00:00' ){ $buytime=date("Y-m-d H:i:s"); }else{ $buytime=$_REQUEST['buytime'];}
    $uptime=date("Y-m-d H:i:s");

   $sql_data=array(
        order_code=>$_REQUEST['order_code'],
        uptime=>$uptime,
        buytime=>$buytime,
        member_no=>$_REQUEST['member_no'],
        member_code=>$_REQUEST['member_code'],
        post_num=>$_REQUEST['post_num'],
        order_info=>$_REQUEST['order_info'],
        order_html=>$_REQUEST['order_html'],
        amount=>$_REQUEST['amount'],
        invoice_name=>$_REQUEST['invoice_name'],
        invoice_email=>$_REQUEST['invoice_email'],
        invoice_tel=>$_REQUEST['invoice_tel'],
        invoice_get=>$_REQUEST['invoice_get'],
        invoice_country=>$_REQUEST['invoice_country'],
        invoice_city=>$_REQUEST['invoice_city'],
        invoice_area=>$_REQUEST['invoice_area'],
        invoice_addr=>$_REQUEST['invoice_addr'],
        invoice_zipcode=>$_REQUEST['invoice_zipcode'],
        receives_name=>$_REQUEST['receives_name'],
        receives_tel=>$_REQUEST['receives_tel'],
        receives_mphone=>$_REQUEST['receives_mphone'],
        receives_country=>$_REQUEST['receives_country'],
        receives_city=>$_REQUEST['receives_city'],
        receives_area=>$_REQUEST['receives_area'],
        receives_addr=>$_REQUEST['receives_addr'],
        receives_zipcode=>$_REQUEST['receives_zipcode'],
        transportation=>$_REQUEST['transportation'],
        pay_mode=>$_REQUEST['pay_mode'],
        pay_info=>$_REQUEST['pay_info'],
        ps=>$_REQUEST['ps'],
        order_ps=>$_REQUEST['order_ps'],
        total_amount=>$_REQUEST['total_amount'],
        order_state=>$_REQUEST['order_state']
    );


	
	//print_r($_POST["del_pic"]);


	switch($cms_mode){
	case 'add':
		$sql_data['order_code']=createOrderCode($tb_name,'order_code',10);
		$insert_id=insertArray($db_name,$sql_data,$trace);
		$msg = "新增成功!";
	break;
	
	case 'edit':
		$where_str=" WHERE `no`='$no'";
        updataArray($db_name,$where_str,$sql_data,$trace);
		$msg = "更新成功!!";
		$insert_id= $no;
	break;
	
	case 'del':
	    $where_str=" WHERE `order_code`='$order_code'";
	    deleteDb($db_name,$where_str,$sql_data,$trace);
		$msg = "刪除成功!!";
	break;
	
	default:
		$msg = "不正確的操作模式!!";
	}
	



	

?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<script language="JavaScript" type="text/JavaScript">
 	alert("<?=$msg?>");
	window.location.href="<?php echo $web_url; ?>";
</script>    