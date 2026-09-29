<? 
	include_once("../session.php");
	require_once("../../include/config.inc.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");
	
	$cms_mode=trim($_REQUEST['cms_mode']);
	$no=trim($_REQUEST['no']);
	$contact_code=trim($_REQUEST['contact_code']);
	$page=trim($_REQUEST['page']);
    $add_pic=$_FILES['add_pic'];
    $del_pic=$_POST['del_pic'];
	//print_r($del_pic);

	

    if($_REQUEST['uptime']==""){ $uptime=date("Y-m-d H:i:s"); }else{ $uptime=$_REQUEST['uptime'];}
    if($_REQUEST['post_time']==""){ $post_time=date("Y-m-d H:i:s"); }else{ $post_time=$_REQUEST['post_time'];}
	
	$db_name='contact';
 	$web_url="list.php?$sh_post";
 	$trace=0;

	$sql_data=array(
        uptime=>$uptime,
        post_time=>$_REQUEST['post_time'],
        subject=>$_REQUEST['subject'],
        name=>$_REQUEST['name'],
        email=>$_REQUEST['email'],
        tel=>$_REQUEST['tel'],
        mphone=>$_REQUEST['mphone'],
        addr=>$_REQUEST['addr'],
        msg=>$_REQUEST['msg'],
        contact_code=>$_REQUEST['contact_code']
	);

	

	
	switch($cms_mode){
	case 'add':
		$sql_data['contact_code']=createCode($tb_name,'contact_code',11);
		$insert_id=insertArray($db_name,$sql_data,$trace);
		$msg = "新增成功!";
	break;
	
	case 'edit':
		$where_str=" WHERE `contact_code`='$contact_code'";
        updataArray($db_name,$where_str,$sql_data,$trace);
		$msg = "更新成功!!";
		$insert_id= $no;
	break;
	
	case 'del':
	    $where_str=" WHERE `contact_code`='$contact_code'";
	    deleteDb($db_name,$where_str,$sql_data,$trace);
		$msg = "刪除成功!!";
	break;
	
	default:
		$msg = "不正確的操作模式!!";
	}
	
7

?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<script language="JavaScript" type="text/JavaScript">
 	alert("<?=$msg?>");
	window.location.href="<?php echo $web_url; ?>";
</script>    