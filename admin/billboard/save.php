<? 

	ini_set("memory_limit","100M");
	set_time_limit(900);

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

	
	$cms_mode=trim($_REQUEST['cms_mode']);	
	
	$no=trim($_REQUEST['no']);
    $add_pic=$_FILES['add_pic'];
    $del_pic=$_POST['del_pic'];	
	
	$db_name='billboard';
 	$web_url="list.php?$sh_post";
	
 	$trace=1;
	
	if($_POST["start_time"]=="" || $_POST["start_time"]=='0000-00-00 00:00:00' ){		
		$start_time='0000-12-30 00:00:00';	
	}else{		
		$start_time=trim($_POST["start_time"]);
	}	
	if($_POST["end_time"]=="" || $_POST["end_time"]=='0000-00-00 00:00:00'){
		$end_time='2100-12-30 00:00:00'; 
	}else{		
		$end_time=trim($_POST["end_time"]);
	}	
		
	$sort_sql_where=" WHERE `bd_id`='".$_REQUEST['bd_id']."'";
	
    $sql_data=array(
        bd_id=>$_REQUEST['bd_id'],
        del_sw=>$_REQUEST['del_sw'],
        name=>$_REQUEST['name'],
        m_type=>$_REQUEST['m_type'],
        info=>$_REQUEST['info'],
        pic_size=>$_REQUEST['pic_size'][0],
        start_time=>$_REQUEST['start_time'],
        end_time=>$_REQUEST['end_time'],
        web_code=>$_REQUEST['web_code'],
        web_url=>$_REQUEST['web_url'],
        m_sort=>$_REQUEST['m_sort'],
        ishow=>$_REQUEST['ishow']
    );

	switch($cms_mode){
	case 'add':
		$insert_id=insertArray($db_name,$sql_data,$trace);
		changSortSet($cms_mode,$db_name,$sort_sql_where,'m_sort',$_REQUEST['m_sort'],$_REQUEST['sort_old_value'],$trace);
		$msg = "新增成功!";
	break;
	
	case 'edit':
		$where_str=" WHERE `no`='$no'";
        updataArray($db_name,$where_str,$sql_data,$trace);
		changSortSet($cms_mode,$db_name,$sort_sql_where,'m_sort',$_REQUEST['m_sort'],0,$trace);
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


    /* --------------------------------- 圖片處理 -------------------------------- */

	if($cms_mode=='add' || $cms_mode=='edit'){


		$xid='billboard_'.sprintf("%06d",$insert_id).'_'; 
		$filePath = dirname(dirname(dirname(__FILE__)))."/images/upload/billboard/";	
	
		$photo_array=explode(",",$_REQUEST['photo']);
		
		$pic_num=0;
		$pic_num_max=1;
		for( $I=0; $I < $pic_num_max; $I++){
			$pic_index++;
			if($add_pic["type"][$I]=='image/png'){
				$pic_name="$xid"."$pic_index".".png";
			}else{
				$pic_name="$xid"."$pic_index".".jpg";
			}
			
			/* --------------------------------- 圖片縮&格式放處理 -------------------------------- */
			
			if($pic_size[$I]=='0x0' || $pic_size[$I]==''){
				$small = array(array("$pic_name", "a",  "", ""));	
			}else{
				$pic_size_array=explode("x",$pic_size[$I]);
				if($pic_size_array[1]==0){
					$small = array(array("$pic_name", "w",  $pic_size_array[0], $pic_size_array[1]));	
					}else{
					$small = array(array("$pic_name", "wh",  $pic_size_array[0], $pic_size_array[1]));		
				}
			}
			//$small = array(array("$pic_name", "wh",  "960", "720"));
			
            /* ----------------------------------------------------------------------------------- */
				
			if($del_pic[$I]=="1"){
				$photo_array[$I]='';
			}else{
				 //print_r($small[$I]);
				if($add_pic['tmp_name'][$I]<>""){
					uploadedPhotoPathR($add_pic["tmp_name"][$I], $filePath,$add_pic["type"][$I], $rotate[$I],$small);
					$photo_array[$I]=$pic_name;
					$chang_index=1;
				}else{
					if($photo_array[$I]==""){$photo_array[$I]="";}
				}			
			}	
		}
		
		
	    if($pic_num_max>1){
			$photo_text=$photo_array[0];
		}else{
			$photo_text=join(",",$photo_array);
		}
		if($chang_index=='1'){
			$sql_updata="UPDATE `order_list` SET `photo` = '$photo_text' WHERE `no` ='$insert_id'";
			mysql_query($sql_updata);			
		}

		
		if($trace==1){
			print_r($small);	
			echo'<br>';
			print_r($photo_array);	
			echo'<br>';
			echo'$sql_updata='."$sql_updata";
			echo'<br>';
		}

 

	}

	



?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<script language="JavaScript" type="text/JavaScript">
 	alert("<?=$msg?>");
	window.location.href="<?php echo $web_url; ?>";
</script>    