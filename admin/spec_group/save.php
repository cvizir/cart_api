<?
include_once("../session.php");
require_once("../../include/config.inc.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

startDB();

print_r($_REQUEST);  
$cms_mode = $_REQUEST["cms_mode"];
$no = trim($_REQUEST['no']);
$page = trim($_REQUEST['page']);
$add_pic = $_FILES['add_pic'];
$del_pic = $_POST['del_pic'];
$size_file = $_FILES['size_file'];
$user_file = $_FILES['user_file'];
if ($_REQUEST['ishow'] == "") {
  $_REQUEST['ishow'] = "0";
}

$sort_old_value = $_REQUEST["sort_old_value"];

print_r($_REQUEST['spec_group_code']);

$db_name = 'spec_group';
$web_url = "list.php?$sh_post";
$trace = 1;

$sql_data = array(
  'spec_group_code' => $_REQUEST['spec_group_code'],
  'pdcat_code' => $_REQUEST['pdcat_code'],
  'spec_code' => join(",", $_REQUEST['spec_code']),
  'name' => $_REQUEST['name'],
  'ishow' => $_REQUEST['ishow']
);




switch ($cms_mode) {
  case 'add':

    changSortSet($cms_mode, $db_name, $sort_sql_where, 'm_sort', $_REQUEST['m_sort'], $_REQUEST['sort_old_value'], $trace);
    $sql_data['spec_group_code'] = createCode($tb_name, 'spec_group_code', 6);
    $insert_id = insertArray($db_name, $sql_data, $trace);
    $msg = "新增成功!";

    break;

  case 'edit':
    changSortSet($cms_mode, $db_name, $sort_sql_where, 'm_sort', $_REQUEST['m_sort'], $_REQUEST['sort_old_value'], $trace);
    $where_str = " WHERE `spec_group_code`='$spec_group_code'";
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
    $where_str = " WHERE `spec_group_code`='$spec_group_code'";
    deleteDb($db_name, $where_str, $sql_data, $trace);
    $msg = "刪除成功!!";

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
  alert("<?= $msg ?>");
	// window.location.href="<?php echo $web_url; ?>";
</script>