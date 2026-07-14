<?php
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

$cms_mode = $_REQUEST['cms_mode'];
$no = trim($_REQUEST['no']);
$product_code = trim($_REQUEST['product_code']);
$page = trim($_REQUEST['page']);
$add_pic = $_FILES['add_pic'];
$del_pic = $_POST['del_pic'];
$size_file = $_FILES['size_file'];
$user_file = $_FILES['user_file'];
$filePath = dirname(dirname(dirname(__FILE__))) . "/images/upload/product/";
//print_r($del_pic);
$sh_post = 'sh_pdcat_code=' . $_REQUEST['pdcat_code'];

$pdcat_code = ',' . @join(",", $_REQUEST['pdcat_code']) . ',';

$_REQUEST['uptime'] = date("Y-m-d H:i:s"); //顯示當前時
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

$_REQUEST['o_price'] = 9999;
$text = $_REQUEST['push_product_code']; //获取值
$text = nl2br($text);
$textArr = explode("<br />", $text); //"<br />"作为分隔切成数组
//print_r($textArr);
//除去数组中的空格
for ($I = 0; $I < sizeof($textArr); $I++) {
  if (str_replace(array("\r", "\n", "\r\n", "\n\r"), '', $textArr[$I]) != '') {
    $newArr[] = $textArr[$I];
  }
}
print_r($newArr);
$push_product_code = @join(",", $newArr);
$push_product_code = str_replace(array("\r", "\n", "\r\n", "\n\r"), '', $push_product_code);


$db_name = 'product';
$web_url = "list.php?$sh_post";
$trace = 0;

$sql_data = array(
  'uptime' => $_REQUEST['uptime'],
  'product_code' => $_REQUEST['product_code'],
  'product_num' => $_REQUEST['product_num'],
  'new_pd_date' => $_REQUEST['new_pd_date'],
  'bar_code' => $_REQUEST['bar_code'],
  'isbn' => $_REQUEST['isbn'],
  'pd_title' => $_REQUEST['pd_title'],
  'name' => mysql_real_escape_string($_REQUEST['name']),
  'pd_stock' => $_REQUEST['pd_stock'],
  'pd_size' => $_REQUEST['pd_size'],
  'pd_age' => $_REQUEST['pd_age'],
  'pd_author' => $_REQUEST['pd_author'],
  'pd_publishing' => $_REQUEST['pd_publishing'],
  'pd_publish_date' => $_REQUEST['pd_publish_date'],
  'pd_weight' => $_REQUEST['pd_weight'],
  'pd_series' => $_REQUEST['pd_series'],
  'pd_mode' => $_REQUEST['pd_mode'],
  'pdcat_code' => $pdcat_code,
  'o_price' => $_REQUEST['o_price'],
  'price' => $_REQUEST['price'],
  'addpd_price' => $_REQUEST['addpd_price'],
  'hot_item' => $_REQUEST['hot_item'],
  'you_tube_code' => $_REQUEST['you_tube_code'],
  'pd_info' => $_REQUEST['pd_info'],
  'pd_summary' => $_REQUEST['pd_summary'],
  'pd_get' => $_REQUEST['pd_get'],
  'discount_code' => $_REQUEST['discount_code'],
  'push_product_code' => $push_product_code,
  'addpd_title' => $_REQUEST['addpd_title'],
  'photo' => 'product_' . $_REQUEST['product_num'] . '_1.jpg,,,,,,,,,',
  'ps' => $_REQUEST['ps'],
  'm_sort' => $_REQUEST['m_sort'],
  'ishow' => $_REQUEST['ishow']
);

$check_txt = 'product_' . $cms_mode;
$all_str = $_SESSION["admin_pv"];

if ($cms_mode == 'add') {
  $sql_product = "SELECT * FROM `product` WHERE `bar_code`='" . $_REQUEST['bar_code'] . "'";
  $rs_product = mysql_query($sql_product);
  $num_product = mysql_num_rows($rs_product);
  echo "$sql_product";
  if ($num_product >= 1) {
    $cms_mode = 're_add';
  }
}
//echo'$cms_mode='."$cms_mode";

switch ($cms_mode) {
  case 'add':
    //$sql_data['product_code']=createCode($tb_name,'product_code',11);
    $sql_data['product_code'] = $_REQUEST['product_num'];
    $insert_id = insertArray($db_name, $sql_data, $trace);
    $insert_id = $sql_data['product_code'];
    $msg = "新增成功!";
    break;
  case 're_add':
    $msg = "此商品已經新增過了!!";
    break;

  case 'edit':
    $where_str = " WHERE `no`='$no'";
    updataArray($db_name, $where_str, $sql_data, $trace);
    $msg = "更新成功!!";
    $insert_id = $product_code;
    break;

  case 'del':
    $where_str = " WHERE `no`='$no'";
    deleteDb($db_name, $where_str, $sql_data, $trace);
    $msg = "刪除成功!!";


    for ($I = 1; $I <= 8; $I++) {
      $filename = $filePath . 'product_' . $product_code . '_' . $I . '.jpg';
      @unlink($filename);
      $filename_2 = $filePath . 'product_' . $product_code . '_' . $I . '.png';
      @unlink($filename_2);
    }


    break;

  default:
}

/* $sort_new_value=trim($_REQUEST['m_sort']);
	$sort_old_value=trim($_REQUEST['sort_old_value']);
	
	$sort_sql_where=" WHERE `pdcat_code`='".$_REQUEST['pdcat_code']."'";
	changSortSet($cms_mode,$db_name,$sort_sql_where,$field_name='m_sort',$sort_new_value,$sort_old_value,$trace);
	 */



$sql_product = "SELECT * FROM `product` WHERE `pdcat_code`='" . $_REQUEST['pdcat_code'] . "' ORDER BY m_sort ASC ,uptime DESC , no DESC";
$rs_product = mysql_query($sql_product);
$num_product = mysql_num_rows($rs_product);
$I = 1;
while ($row_product = mysql_fetch_array($rs_product, MYSQL_ASSOC)) {
  $sql_update = "UPDATE `product` SET `m_sort` = '$I' WHERE `product_code` ='" . $row_product['product_code'] . "'";
  mysql_query($sql_update);
  $I++;

  //echo"$sql_update".'<br /><br />';
}



if ($cms_mode == 'add' || $cms_mode == 'edit') {
  //echo'$insert_id='."$insert_id";
  $xid = 'product_' . $insert_id . '_';
  $filePath = dirname(dirname(dirname(__FILE__))) . "/images/upload/product/";

  $photo_array = @explode(",", $_REQUEST['photo']);



  for ($I = 0; $I <= 9; $I++) {
    $pic_num++;
    if ($add_pic['type'][$I] == 'image/png') {
      $pic_name = "$xid" . "$pic_num" . ".png";
    } else {
      $pic_name = "$xid" . "$pic_num" . ".jpg";
    }
    if ($I == 0) {
      $small = array(array("$pic_name", "a",  "640", "640"));
    } else {
      $small = array(array("$pic_name", "a",  "640", "640"));
    }

    if ($del_pic[$I] == "1") {
      $photo_array[$I] = '';
    } else {
      //print_r($small[$I]);
      if ($add_pic['tmp_name'][$I] <> "") {
        $photo_text = $pic_name;
        //uploadedPhotoPathx($add_pic["tmp_name"][$I], $filePath, $small[$I]);
        uploadedPhotoPathR($add_pic["tmp_name"][$I], $filePath, $add_pic["type"][$I], $rotate[$I], $small);
        $photo_array[$I] = $pic_name;
      } else {
        if ($photo_array[$I] == "") {
          $photo_array[$I] = "";
        }
      }
    }
  }
  $photo_text = join(",", $photo_array);

  $sql_updata = "UPDATE `$db_name` SET `photo` = '$photo_text' WHERE `product_code` ='$insert_id'";
  mysql_query($sql_updata);
  //echo $photo_text;
  $pic_num = 1;
  for ($I = 0; $I <= 7; $I++) {

    if ($photo_array[$I] == '') {
      $filename = $filePath . 'product_' . $insert_id . '_' . $pic_num . '.jpg';
      @unlink($filename);
      $filename_2 = $filePath . 'product_' . $insert_id . '_' . $pic_num . '.png';
      @unlink($filename_2);
    }
    $pic_num++;
  }


  /* 
	print_r($photo_array);
    echo'$sql_updata='."$sql_updata";
	*/
}


?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<script language="JavaScript" type="text/JavaScript">
  alert("<?= $msg ?>");
	window.location.href="<?php echo $web_url; ?>";
</script>