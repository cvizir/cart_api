<?
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

startDB();


$cms_mode = $_REQUEST["cms_mode"];
$no = trim($_REQUEST['no']);
$pd_cat_code = trim($_REQUEST['pd_cat_code']);
$page = trim($_REQUEST['page']);

$add_pic = $_FILES['add_pic'];
$del_pic = $_POST['del_pic'];

$sort_old_value = $_REQUEST["sort_old_value"];

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

$db_name = 'pdcat_t';
$sh_post = isset($_REQUEST['sh_post']) ? $_REQUEST['sh_post'] : '';
$web_url = "list.php?$sh_post";
$trace = 1;

$sql_data = array(
  'uptime' => $uptime,
  'pdcat_t_code' => $_REQUEST['pdcat_t_code'],
  'name' => $_REQUEST['name'],
  'url' => $_REQUEST['url'],
  'link_type' => $_REQUEST['link_type'],
  'm_sort' => $_REQUEST['m_sort'],
  'ishow' => $_REQUEST['ishow']
);




switch ($cms_mode) {
  case 'add':

    changSortSet($cms_mode, $db_name, $sort_sql_where, 'm_sort', $_REQUEST['m_sort'], $_REQUEST['sort_old_value'], $trace);
    $sql_data['pdcat_t_code'] = createCode($tb_name, 'pdcat_t_code', 6);
    $insert_id = insertArray($db_name, $sql_data, $trace);
    $msg = "新增成功!";

    break;

  case 'edit':
    changSortSet($cms_mode, $db_name, $sort_sql_where, 'm_sort', $_REQUEST['m_sort'], $_REQUEST['sort_old_value'], $trace);
    $where_str = " WHERE `pdcat_t_code`='$pdcat_t_code'";
    updataArray($db_name, $where_str, $sql_data, $trace);
    $msg = "更新成功!!";
    $insert_id = $no;

    break;

  case 'del':
    //去除圖檔
    //刪除m_sort
    if ($_REQUEST['m_sort'] != '' && $_REQUEST['m_sort'] != '0') {
      changSortSet($cms_mode, $db_name, $sort_sql_where, 'm_sort', $_REQUEST['m_sort'], 0, $trace);
    }
    //檢查是否有子類別
    $sql_pdcat_t = "SELECT * FROM `pdcat_m` WHERE `pdcat_t_code`='" . $_REQUEST['pdcat_t_code'] . "'";
    $rs_pdcat_t = mysql_query($sql_pdcat_t);
    $num_pdcat_t = mysql_num_rows($rs_pdcat_t);

    if ($num_pdcat_t == '0') {
      $where_str = " WHERE `pdcat_t_code`='$pdcat_t_code'";
      deleteDb($db_name, $where_str, $sql_data, $trace);
      $msg = "刪除成功!!";
    } else {
      $msg = "還有子類別所以無法刪除!!";
    }

    break;

  default:
    $msg = "不正確的操作模式!!";
}


if ($trace == 1) {
  echo '$sort_old_value=' . "$sort_old_value";
  echo '<br>';
  echo '$sort_new_value=' . $_REQUEST['m_sort'];
  echo '<br>';
}


?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<script language="JavaScript" type="text/JavaScript">
  alert("<?php echo $msg ?>");
	window.location.href="<?php echo $web_url; ?>";
</script>