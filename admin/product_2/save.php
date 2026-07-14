<? 
	include_once("../session.php");
	require_once("../../include/config.inc.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");
	$cms_mode=$_REQUEST['cms_mode'];
	$no=trim($_REQUEST['no']);
	$product_code=trim($_REQUEST['product_code']);
	$page=trim($_REQUEST['page']);
    $add_pic=$_FILES['add_pic'];
    $del_pic=$_POST['del_pic'];
    $size_file=$_FILES['size_file'];
    $user_file=$_FILES['user_file'];

	//print_r($del_pic);

	
	$uptime= date("Y-m-d H:i:s");//顯示當前時
    if($_REQUEST['start_time']==""){ $start_time='0000-00-00 00:00:00'; }else{ $start_time=$_REQUEST['start_time'];}
    if($_REQUEST['end_time']==""){ $end_time='2020-00-00 00:00:00'; }else{ $end_time=$_REQUEST['end_time'];}

	$db_name='product';
 	$web_url="list.php?$sh_post";
 	$trace=0;

    $sql_data=array(
        product_code=>$_REQUEST['product_code'],
        uptime=>$_REQUEST['uptime'],
        name_title=>$_REQUEST['name_title'],
        name=>$_REQUEST['name'],
        pd_mode=>$_REQUEST['pd_mode'],
        style_name=>$_REQUEST['style_name'],
        slt_lg=>$_REQUEST['slt_lg'],
        pd_cat_id=>$_REQUEST['pd_cat_id'],
        o_price=>$_REQUEST['o_price'],
        price=>$_REQUEST['price'],
        discount_rate=>$_REQUEST['discount_rate'],	
        photo=>$_REQUEST['photo'],
        ps=>$_REQUEST['ps'],
        pd_info=>$_REQUEST['pd_info'],
        size_pic=>$_REQUEST['size_pic'],
        user_pic=>$_REQUEST['user_pic'],
        user_info=>$_REQUEST['user_info'],	
		no_buy_info=>$_REQUEST['no_buy_info'],	
		unit_info=>$_REQUEST['unit_info'],	
        m_sort=>$_REQUEST['m_sort'],
        ishow=>$_REQUEST['ishow']
    );
	

	
	switch($cms_mode){
	case 'add':
		$sql_data['product_code']=createCode($tb_name,'product_code',11);
		$insert_id=insertArray($db_name,$sql_data,$trace);
		$msg = "新增成功!";
	break;
	
	case 'edit':
		$where_str=" WHERE `product_code`='$product_code'";
        updataArray($db_name,$where_str,$sql_data,$trace);
		$msg = "更新成功!!";
		$insert_id= $no;
	break;
	
	case 'del':
	    $where_str=" WHERE `product_code`='$product_code'";
	    deleteDb($db_name,$where_str,$sql_data,$trace);
		$msg = "刪除成功!!";
	break;
	
	default:
		$msg = "不正確的操作模式!!";
	}
	
	if($cms_mode=='add' || $cms_mode=='edit'){
	//echo'$insert_id='."$insert_id";
	$xid='product_'.sprintf("%06d",$insert_id).'_'; 
	$filePath = dirname(dirname(dirname(__FILE__)))."/images/upload/product/";	

	$photo_array=@explode(",",$_REQUEST['photo']);
	//print_r($size_file);
	if($size_file['tmp_name']<>""){
		$pic_name='product_size_'.sprintf("%06d",$insert_id).'.jpg'; 
		$small = array(array("$pic_name", "w",  "600", "203"));	
		uploadedPhotoPathR($size_file["tmp_name"], $filePath,$size_file["type"], $rotate,$small);
	}
	if($user_file['tmp_name']<>""){
		$pic_name='product_user_'.sprintf("%06d",$insert_id).'.jpg'; 
		$small = array(array("$pic_name", "w",  "600", "203"));	
		uploadedPhotoPathR($user_file["tmp_name"], $filePath,$user_file["type"], $rotate,$small);
	}


	for( $I=0; $I<=0; $I++){
	$pic_num++;
	if($add_pic['type'][$I]=='image/png'){
		$pic_name="$xid"."$pic_num".".png";	
	}else{
		$pic_name="$xid"."$pic_num".".jpg";	
	}

	$small = array(array("$pic_name", "a",  "203", "203"));	
	if($del_pic[$I]=="1"){
			$photo_text='';
		}else{
		    //print_r($small[$I]);
			if($add_pic['tmp_name'][$I]<>""){
				$photo_text=$pic_name;
				//uploadedPhotoPathx($add_pic["tmp_name"][$I], $filePath, $small[$I]);
				uploadedPhotoPathR($add_pic["tmp_name"][$I], $filePath,$add_pic["type"][$I], $rotate[$I],$small);
				$photo_array[$I]=$pic_name;
			}else{
				if($photo_array[$I]==""){$photo_array[$I]="";}
			}			
		}	
	}
	/* 
	print_r($photo_array);
	$photo_text=join(",",$photo_array);
    echo'$sql_updata='."$sql_updata";
	*/
	$sql_updata="UPDATE `$db_name` SET `photo` = '$pic_name' WHERE `no` ='$insert_id'";
	//echo"$sql_updata";
	mysql_query($sql_updata);  
	}
	

?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<script language="JavaScript" type="text/JavaScript">
 	alert("<?=$msg?>");
	window.location.href="<?php echo $web_url; ?>";
</script>    