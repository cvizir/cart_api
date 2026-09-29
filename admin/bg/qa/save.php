<? require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");
$qa_code = trim($_REQUEST['qa_code']);
$sh_post = $_SERVER['QUERY_STRING'];
$pagesize = $_REQUEST["pagesize"];
$total = $_REQUEST["total"];
$total--;
if ($page != 1) {
  if (($page - 1) * $pagesize >= $total) {
    $page--;
  }
}
$cms_mode = trim($_REQUEST['cms_mode']);
$add_pic = $_FILES['add_pic'];
$del_pic = $_POST['del_pic'];
$db_name = 'qa';
$web_url = "list.php?$sh_post";
$trace = 0;
if ($_REQUEST['uptime'] == "") {
  $uptime = date("Y-m-d H:i:s");
} else {
  $uptime = $_REQUEST['uptime'];
};

$sql_data = array(
  'qa_code' => $_REQUEST['qa_code'],
  'uptime' => $uptime,
  'cat_id' => $_REQUEST['cat_id'],
  'question' => $_REQUEST['question'],
  'ans' => $_REQUEST['ans'],
  'm_sort' => $_REQUEST['m_sort'],
  'ishow' => $_REQUEST['ishow']
);
switch ($cms_mode) {
  case 'add':
    $sql_data['qa_code'] = createCode($tb_name, 'qa_code', 11);
    $insert_id = insertArray($db_name, $sql_data, $trace);
    $msg = "新增成功!";
    break;
  case 'edit':
    $where_str = " WHERE `qa_code`='$qa_code'";
    updataArray($db_name, $where_str, $sql_data, $trace);
    $msg = "更新成功!!";
    $insert_id = $no;
    break;
  case 'del':
    $where_str = " WHERE `qa_code`='$qa_code'";
    deleteDb($db_name, $where_str, $sql_data, $trace);
    $msg = "刪除成功!!";
    break;
  default:
    $msg = "不正確的操作模式!!";
} ?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<script language="JavaScript" type="text/JavaScript">
  alert("<?= $msg ?>");	
  window.location.href="<?php echo $web_url; ?>";
</script>