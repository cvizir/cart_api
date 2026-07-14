<? 
	include_once("../session.php");
	require_once("../../include/config.inc.php");
	require_once("../../include/function.php");
	require_once("../../include/DBClass.php");
	
	$no=trim($_REQUEST['no']);
	$member_code=trim($_REQUEST['member_code']);
	$page=trim($_REQUEST['page']);
    $add_pic=$_FILES['add_pic'];
    $del_pic=$_POST['del_pic'];
	//print_r($del_pic);

	
	$uptime= date("Y-m-d H:i:s");//顯示當前時
	
	$db_name='member';
 	$web_url="list.php?$sh_post";
 	$trace=1;

	$sql_data=array(
        uptime=>$_REQUEST['uptime'],
        name=>$_REQUEST['name'],
        member_code=>$_REQUEST['member_code'],
        nickname=>$_REQUEST['nickname'],
        sex=>$_REQUEST['sex'],
        pid=>$_REQUEST['pid'],
        member_mode=>$_REQUEST['member_mode'],
        regtime=>$_REQUEST['regtime'],
        email=>$_REQUEST['email'],
        psw=>$_REQUEST['psw'],
        tel=>$_REQUEST['tel'],
        phone=>$_REQUEST['phone'],
        addr=>$_REQUEST['addr'],
        cp_id=>$_REQUEST['cp_id'],
        cp_title=>$_REQUEST['cp_title'],
        birthday=>$_REQUEST['birthday'],
        esend=>$_REQUEST['esend'],
        photo=>$_REQUEST['photo']
	);

	

	
	switch($cms_mode){
	case 'add':
		$sql_data['member_code']=createOrderCode($tb_name,'member_code',11);
		$insert_id=insertArray($db_name,$sql_data,$trace);
		$msg = "新增成功!";
	break;
	
	case 'edit':
		$where_str=" WHERE `member_code`='$member_code'";
        updataArray($db_name,$where_str,$sql_data,$trace);
		$msg = "更新成功!!";
		$insert_id= $no;
	break;
	
	case 'del':
	    $where_str=" WHERE `member_code`='$member_code'";
	    deleteDb($db_name,$where_str,$sql_data,$trace);
		$msg = "刪除成功!!";
	break;
	
	default:
		$msg = "不正確的操作模式!!";
	}
/* 	
	if($cms_mode=='add' || $cms_mode=='edit'){
	//echo'$insert_id='."$insert_id";
	$xid='member_'.sprintf("%06d",$insert_id).'_'; 
	$filePath = dirname(dirname(dirname(__FILE__)))."/images/upload/member/";	

	$photo_array=@explode(",",$_REQUEST['photo']);
	


	for( $I=0; $I<=3; $I++){
	$pic_num++;
	$pic_name="$xid"."$pic_num".".jpg";		
	$small[0] = array(array("$pic_name", "wh",  "203", "203"));	
	$small[1] = array(array("$pic_name", "wh",  "203", "203"));	
	$small[2] = array(array("$pic_name", "wh",  "203", "203"));	
	$small[3] = array(array("$pic_name", "wh",  "203", "203"));	
	if($del_pic[$I]=="1"){
			$photo_array[$I]='';
		}else{
		    //print_r($small[$I]);
			if($add_pic['tmp_name'][$I]<>""){
				uploadedPhotoPathx($add_pic["tmp_name"][$I], $filePath, $small[$I]);
				$photo_array[$I]=$pic_name;
			}else{
				if($photo_array[$I]==""){$photo_array[$I]="";}
			}			
		}	
	}
	//print_r($photo_array);
	$photo_text=join(",",$photo_array);
	$sql_updata="UPDATE `$db_name` SET `photo` = '$photo_text' WHERE `no` ='$insert_id'";
    //echo'$sql_updata='."$sql_updata";
	mysql_query($sql_updata); 

	}
	 */

?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<script language="JavaScript" type="text/JavaScript">
 	alert("<?=$msg?>");
	window.location.href="<?php echo $web_url; ?>";
</script>    