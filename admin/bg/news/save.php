<?
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

$no = trim($_REQUEST['no']);
$news_code = trim($_REQUEST['news_code']);
$page = trim($_REQUEST['page']);
$add_pic = $_FILES['add_pic'];
$del_pic = $_POST['del_pic'];
$cms_mode = trim($_REQUEST['cms_mode']);
//print_r($del_pic);

if ($_REQUEST['wttime'] == '') {
  $_REQUEST['wttime'] = date("Y-m-d");
}
$uptime = date("Y-m-d H:i:s"); //顯示當前時
if ($_REQUEST['start_time'] == "") {
  $start_time = '0000-00-00 00:00:00';
} else {
  $start_time = $_REQUEST['start_time'];
}
if ($_REQUEST['end_time'] == "") {
  $end_time = '2020-00-00 00:00:00';
} else {
  $end_time = $_REQUEST['end_time'];
}

$db_name = 'news';
$web_url = "list.php?$sh_post";
$trace = 0;

$sql_data = array(
  news_code => $_REQUEST['news_code'],
  news_type => $_REQUEST['news_type'],
  uptime => $uptime,
  wttime => $_REQUEST['wttime'],
  name => $_REQUEST['name'],
  you_tube_code => $_REQUEST['you_tube_code'],
  web_url => $_REQUEST['web_url'],
  summary => $_REQUEST['summary'],
  info => $_REQUEST['info'],
  start_time => $start_time,
  end_time => $end_time,
  photo => $_REQUEST['photo'],
  ps => $_REQUEST['ps'],
  click_num => $_REQUEST['click_num'],
  m_sort => $_REQUEST['m_sort'],
  top_ishow => $_REQUEST['top_ishow'],
  ishow => $_REQUEST['ishow']
);




switch ($cms_mode) {
  case 'add':
    $sql_data['news_code'] = createCode($tb_name, 'news_code', 11);
    $news_code = $sql_data['news_code'];
    $insert_id = insertArray($db_name, $sql_data, $trace);
    $insert_id = $news_code;
    $msg = "新增成功!";
    break;

  case 'edit':
    $where_str = " WHERE `news_code`='$news_code'";
    updataArray($db_name, $where_str, $sql_data, $trace);
    $msg = "更新成功!!";
    $insert_id = $news_code;
    break;

  case 'del':
    $where_str = " WHERE `news_code`='$news_code'";
    deleteDb($db_name, $where_str, $sql_data, $trace);
    $msg = "刪除成功!!";
    break;

  default:
    $msg = "不正確的操作模式!!";
}

/*------------------------------------ 圖片處理 ----------------------------------------*/
/* 	
	$xid='news_'.$insert_id.'_'; 
	$filePath = dirname(dirname(dirname(__FILE__)))."/images/upload/news/";	

	$photo_array=explode(",",$_REQUEST['photo']);
    
	$pic_num=0;
	for( $I=0; $I <= 1; $I++){
	$pic_num++;
	$pic_name="$xid"."$pic_num".".jpg";		


		if($del_pic[$I]=="1"){
			$photo_array[$I]='';
			$del_filePath=$filePath.$pic_name;
			echo"$del_filePath";
			unlink($del_filePath);		
		}else{
		     //print_r($small[$I]);
			$small = array(array("$pic_name", "wh",  "950", "400"));	
			 
			if($add_pic['tmp_name'][$I]<>""){
				//echo $add_pic['tmp_name'][$I];
				//echo $small;
				uploadedPhotoPathx($add_pic["tmp_name"][$I], $filePath, $small);
				$photo_array[$I]=$pic_name;
			}else{
				if($photo_array[$I]==""){$photo_array[$I]="";}
			}			
		}	
	}

	$photo_text=join(",",$photo_array);
	$sql_photo_updata="UPDATE `news` SET `photo` = '$photo_text' WHERE `news_code` ='$news_code'";
    mysql_query($sql_photo_updata); 
 */
if ($trace == '1') {
  print_r($del_pic);
  echo '<br />';
  echo '$sql_photo_updata=' . "$sql_photo_updata" . '<br />';
}


?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<?php
if (function_exists('addAdminLog')) {
  $info = '最新消息管理 <' . $msg . '> [news_code=' . $news_code . ']';
  addAdminLog($_SESSION["admin_id"], $info);
}
?>
<script language="JavaScript" type="text/JavaScript">
  alert("<?= $msg ?>");
	window.location.href="<?php echo $web_url; ?>";
</script>