<? 
	require_once("../../include/config.inc.php");
	include_once("../session.php");
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

 	$trace=0;
	$no=trim($_REQUEST['no']);
	$cms_mode=trim($_REQUEST['cms_mode']);
    $add_pic=$_FILES['add_pic'];
    $del_pic=$_POST['del_pic'];	
	
	$db_name='tmp_model';
 	$web_url="list.php?$sh_post";


    $interest=join(",",$_REQUEST['interest']);
    $interest_2=join(",",$_REQUEST['interest_2']);
	$now= date("Y-m-d H:i:s");//顯示當前時
	
    if($_REQUEST['regtime']==""){ $regtime=date("Y-m-d H:i:s"); }else{ $regtime=$_REQUEST['regtime'];}

    $sql_data=array(
        regtime=>$_REQUEST['regtime'],
        nickname=>$_REQUEST['nickname'],
        name=>$_REQUEST['name'],
        email=>$_REQUEST['email'],
        psw=>$_REQUEST['psw'],
        region=>$_REQUEST['region'],
        sex=>$_REQUEST['sex'],
        bday=>$_REQUEST['bday'],
        tel=>$_REQUEST['tel'],
        mphone=>$_REQUEST['mphone'],
        city=>$_REQUEST['city'],
        area=>$_REQUEST['area'],
        addr=>$_REQUEST['addr'],
        zipcode=>$_REQUEST['zipcode'],
        city_2=>$_REQUEST['city_2'],
        area_2=>$_REQUEST['area_2'],
        addr_2=>$_REQUEST['addr_2'],
        zipcode_2=>$_REQUEST['zipcode_2'],
        city_3=>$_REQUEST['city_3'],
        area_3=>$_REQUEST['area_3'],
        addr_3=>$_REQUEST['addr_3'],
        zipcode_3=>$_REQUEST['zipcode_3'],
        interest=>$interest,
        interest_2=>$interest_2,
        buy_type=>$_REQUEST['buy_type'],
        buy_type_2=>$_REQUEST['buy_type_2'],
        career=>$_REQUEST['career'],
        income=>$_REQUEST['income'],
        vip_mode=>$_REQUEST['vip_mode'],
        last_login=>$now,
        esend=>$_REQUEST['esend'],
        authcode=>createCode('tmp_model','authcode','11')
    );

    $sql_data_edit=array(
        nickname=>$_REQUEST['nickname'],
        name=>$_REQUEST['name'],
        email=>$_REQUEST['email'],
        psw=>$_REQUEST['psw'],
        region=>$_REQUEST['region'],
        sex=>$_REQUEST['sex'],
        bday=>$_REQUEST['bday'],
        tel=>$_REQUEST['tel'],
        mphone=>$_REQUEST['mphone'],
        city=>$_REQUEST['city'],
        area=>$_REQUEST['area'],
        addr=>$_REQUEST['addr'],
        zipcode=>$_REQUEST['zipcode'],
        city_2=>$_REQUEST['city_2'],
        area_2=>$_REQUEST['area_2'],
        addr_2=>$_REQUEST['addr_2'],
        zipcode_2=>$_REQUEST['zipcode_2'],
        city_3=>$_REQUEST['city_3'],
        area_3=>$_REQUEST['area_3'],
        addr_3=>$_REQUEST['addr_3'],
        zipcode_3=>$_REQUEST['zipcode_3'],
        interest=>$interest,
        interest_2=>$interest_2,
        buy_type=>$_REQUEST['buy_type'],
        buy_type_2=>$_REQUEST['buy_type_2'],
        career=>$_REQUEST['career'],
        income=>$_REQUEST['income'],
        vip_mode=>$_REQUEST['vip_mode'],
        last_login=>$now,
        esend=>$_REQUEST['esend']
    );


	switch($cms_mode){
	case 'add':
		$insert_id=insertArray($db_name,$sql_data,$trace);
		$msg = "新增成功!";
	break;
	
	case 'edit':
		$where_str=" WHERE `no`='$no'";
        updataArray($db_name,$where_str,$sql_data_edit,$trace);
		//updataSortSet($db_name,$field_value_ed,$index_name,$index_value,'m_sort');
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
	

	$xid='member_'.sprintf("%06d",$insert_id).'_1'; 
	$filePath = dirname(dirname(dirname(__FILE__)))."/images/upload/member/";	

	if($add_pic['tmp_name'][0]<>""){
		  $pic_name="$xid.jpg";		
		  $small = array(array("$pic_name", "wh",  "360", "360"));
		  uploadedPhotoPathx($add_pic["tmp_name"][0], $filePath, $small);
	}
    

		




	

?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<script language="JavaScript" type="text/JavaScript">
 	alert("<?=$msg?>");
	window.location.href="<?php echo $web_url; ?>";
</script>    