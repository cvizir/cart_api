<? 
	include_once("../session.php");
	require_once("../../include/config.inc.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");
	$cms_mode=$_REQUEST['cms_mode'];
	$no=trim($_REQUEST['no']);
	$page=trim($_REQUEST['page']);
    $add_pic=$_FILES['add_pic'];
    $del_pic=$_POST['del_pic'];
	//print_r($del_pic);

	
	$uptime= date("Y-m-d H:i:s");//顯示當前時
    if($_REQUEST['start_time']==""){ $start_time='0000-00-00 00:00:00'; }else{ $start_time=$_REQUEST['start_time'];}
    if($_REQUEST['end_time']==""){ $end_time='2020-00-00 00:00:00'; }else{ $end_time=$_REQUEST['end_time'];}

	$db_name='billboard_cat';
 	$web_url="list.php?$sh_post";
 	$trace=0;


    $sql_data=array(
        name=>$_REQUEST['name'],
        slt_lg=>$_REQUEST['slt_lg'],
        limit_num=>$_REQUEST['limit_num'],
        web_url=>$_REQUEST['web_url'],
        ps=>$_REQUEST['ps'],
        del_sw=>$_REQUEST['del_sw'],
        m_sort=>$_REQUEST['m_sort'],
        ishow=>$_REQUEST['ishow']
    );
	

	
	switch($cms_mode){
	case 'add':
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
	    $where_str=" WHERE `no`='$no'";
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