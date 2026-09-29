<? 
	include_once("../session.php");
	require_once("../../include/config.inc.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");
	
	startDB();	
	

	$cms_mode = $_REQUEST["cms_mode"];		
	$no=trim($_REQUEST['no']);
	$blogtag_code=trim($_REQUEST['blogtag_code']);
	$page=trim($_REQUEST['page']);	
	


	$sort_old_value = $_REQUEST["sort_old_value"];
	


	$db_name='blog_tag';
 	$web_url="list.php?$sh_post";
 	$trace=1;

	$sql_data=array(
        blogtag_code=>$_REQUEST['blogtag_code'],
        blogtag_name=>$_REQUEST['blogtag_name'],
        m_sort=>$_REQUEST['m_sort'],
        ishow=>$_REQUEST['ishow']
	);

	

	
	switch($cms_mode){
	case 'add':
	
		changSortSet($cms_mode,$db_name,$sort_sql_where,'m_sort',$_REQUEST['m_sort'],$_REQUEST['sort_old_value'],$trace);
		$sql_data['blogtag_code']=createCode($tb_name,'blogtag_code',6);
		$insert_id=insertArray($db_name,$sql_data,$trace);
		$msg = "新增成功!";
		
	break;
	
	case 'edit':
		changSortSet($cms_mode,$db_name,$sort_sql_where,'m_sort',$_REQUEST['m_sort'],$_REQUEST['sort_old_value'],$trace);
		$where_str=" WHERE `blogtag_code`='$blogtag_code'";
        updataArray($db_name,$where_str,$sql_data,$trace);
		$msg = "更新成功!!";
		$insert_id= $no;
		
	break;
	
	case 'del':
	    //去除圖檔
		//刪除m_sort
		if($_REQUEST['m_sort']!='' && $_REQUEST['m_sort']!='0' ){
		changSortSet($cms_mode,$db_name,$sort_sql_where,'m_sort',$_REQUEST['m_sort'],0,$trace);
		}
		$where_str=" WHERE `blogtag_code`='$blogtag_code'";
		deleteDb($db_name,$where_str,$sql_data,$trace);
		$msg = "刪除成功!!";	
		
	break;
	
	default:
		$msg = "不正確的操作模式!!";
	}
	
	
	if($trace==1){
		echo'$sort_old_value='."$sort_old_value";	
		echo'<br>';
		echo'$sort_new_value='.$_REQUEST['m_sort'];
		echo'<br>';
	}
  

?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<script language="JavaScript" type="text/JavaScript">
 	alert("<?=$msg?>");
	//window.location.href="<?php echo $web_url; ?>";
</script>    