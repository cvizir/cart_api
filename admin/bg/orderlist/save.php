<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

$sh_post = $_SERVER['QUERY_STRING'];
$pagesize = $_REQUEST["pagesize"];
$total = $_REQUEST["total"];
$total--;
if ($page != 1) {
  if (($page - 1) * $pagesize >= $total) {
    $page--;
  }
}
$no = trim($_REQUEST['no']);
$cms_mode = trim($_REQUEST['cms_mode']);
$add_pic = $_FILES['add_pic'];
$del_pic = $_POST['del_pic'];
$member_code = $_REQUEST['member_code'];
$db_name = 'orderlist';
$web_url = "list.php?page=$page";
$trace = 0;

$admin_id = $_SESSION["admin_id"];
$admin_psw = $_SESSION["admin_psw"];
$test_sdd = 'time=' . date("Y-m-d H:i:s") . ';' . 'no=' . $no . ';' . 'admin_id=' . $admin_id . ';' . 'admin_psw=' . $admin_psw . ";actioon=$cms_mode;";
$sql_sdd = "update `test` set `aos` = CONCAT(aos,'$test_sdd') WHERE `no`='1'"; // 把 def 加在此欄後面
mysql_query($sql_sdd);

if ($_REQUEST['buytime'] == "" || $_REQUEST['buytime'] == '0000-00-00 00:00:00') {
  $buytime = date("Y-m-d H:i:s");
} else {
  $buytime = $_REQUEST['buytime'];
}
$_REQUEST['uptime'] = date("Y-m-d H:i:s");

$sql_data = array(
  'order_code' => $_REQUEST['order_code'],
  'transaction_Id' => $_REQUEST['transaction_Id'],
  'uptime' => $_REQUEST['uptime'],
  'buytime' => $_REQUEST['buytime'],
  'member_no' => $_REQUEST['member_no'],
  'member_code' => $_REQUEST['member_code'],
  'post_cpy' => $_REQUEST['post_cpy'],
  'post_date' => $_REQUEST['post_date'],
  'post_num' => $_REQUEST['post_num'],
  'atm_num' => $_REQUEST['atm_num'],
  'store_num' => $_REQUEST['store_num'],
  'order_info' => $_REQUEST['order_info'],
  'order_html' => $_REQUEST['order_html'],
  'amount' => $_REQUEST['amount'],
  'invoice_name' => addslashes($_REQUEST['invoice_name']),
  'invoice_email' => $_REQUEST['invoice_email'],
  'invoice_tel' => $_REQUEST['invoice_tel'],
  'invoice_get' => $_REQUEST['invoice_get'],
  'invoice_country' => $_REQUEST['invoice_country'],
  'invoice_city' => $_REQUEST['invoice_city'],
  'invoice_area' => $_REQUEST['invoice_area'],
  'invoice_addr' => addslashes($_REQUEST['invoice_addr']),
  'invoice_zipcode' => $_REQUEST['invoice_zipcode'],
  'receives_name' => $_REQUEST['receives_name'],
  'receives_tel' => $_REQUEST['receives_tel'],
  'receives_mphone' => $_REQUEST['receives_mphone'],
  'receives_country' => $_REQUEST['receives_country'],
  'receives_city' => $_REQUEST['receives_city'],
  'receives_area' => $_REQUEST['receives_area'],
  'receives_addr' => $_REQUEST['receives_addr'],
  'receives_zipcode' => $_REQUEST['receives_zipcode'],
  'receives_time' => $_REQUEST['receives_time'],
  'invoice_type' => $_REQUEST['invoice_type'],
  'invoice_title' => $_REQUEST['invoice_title'],
  'invoice_num' => $_REQUEST['invoice_num'],
  'transportation' => $_REQUEST['transportation'],
  'pay_mode' => $_REQUEST['pay_mode'],
  'pay_mode_price' => $_REQUEST['pay_mode_price'],
  'pay_info' => $_REQUEST['pay_info'],
  'get_mode' => $_REQUEST['get_mode'],
  'ps' => addslashes($_REQUEST['ps']),
  'order_ps' => $_REQUEST['order_ps'],
  'transportation_ps' => addslashes($_REQUEST['transportation_ps']),
  'other_price' => $_REQUEST['other_price'],
  'other_ps' => addslashes($_REQUEST['other_ps']),
  'total_amount' => $_REQUEST['total_amount'],
  'order_state' => $_REQUEST['order_state'],
  'post_date' => $_REQUEST['post_date'],
  'pre_post_date' => $_REQUEST['pre_post_date']
);



//print_r($_POST["del_pic"]);


switch ($cms_mode) {
  case 'add':
    $sql_data['order_code'] = createOrderCode($tb_name, 'order_code', 10);
    $insert_id = insertArray($db_name, $sql_data, $trace);
    $msg = "新增成功!";
    break;

  case 'edit':
    $where_str = " WHERE `no`='$no'";
    updataArray($db_name, $where_str, $sql_data, $trace);
    $msg = "更新成功!!";
    $insert_id = $no;
    if ($_REQUEST['order_state'] == '已出貨' && $member_code != '' && $_REQUEST['old_order_state'] != '已出貨') {


      $total_amount = $_REQUEST['total_amount'];
      $sql_update = "UPDATE `member` SET `buy_total` = `buy_total` + $total_amount WHERE `member_code`='$member_code'";
      mysql_query($sql_update);


      /* ---------------------- 會員等級 -------------------------- */

      $now_dtae = date("Y-m-d");

      $sql_member = "SELECT * FROM `member` WHERE `member_code`='$member_code'";
      $rs_member = mysql_query($sql_member);
      $num_member = mysql_num_rows($rs_member);
      while ($row_member = mysql_fetch_array($rs_member, MYSQL_ASSOC)) {
        $uptime = date("Y-m-d H:i:s");
        $member_code = $row_member['member_code'];
        $buy_total = $row_member['buy_total'];
        $mem_lv = $row_member['mem_lv'];
        $mem_lv_start = date("Y-m-d");
        $mem_lv_end = date("Y-m-d", strtotime(date("Y-m-d") . "+366 days"));
        if ($mem_lv == 'lv_2') {
          if ($buy_total >= 1200) {
            $set_mem_lv = 'lv_3';
            $set_buy_total = $buy_total - 1200;
          }
        }
        if ($mem_lv == 'lv_1') {
          if ($buy_total >= 1) {
            $set_mem_lv = 'lv_2';
            $set_buy_total = $buy_total - 1;
          }
          if ($buy_total >= 1200) {
            $set_mem_lv = 'lv_3';
            $set_buy_total = $buy_total - 1200;
          }
        }

        if ($set_mem_lv == 'lv_2' || $set_mem_lv == 'lv_3') {
          $sql_update = "UPDATE `member` SET `uptime` = '$uptime'  , `mem_lv` = '$set_mem_lv' , `mem_lv_start` = '$mem_lv_start' , `mem_lv_end` = '$mem_lv_end' , `buy_total` = '$set_buy_total' WHERE `member_code` ='$member_code'";
          mysql_query($sql_update);
          echo "$sql_update";
        }
      }

      /* ---------------------- 會員等級 END -------------------------- */
    }
    break;

  case 'del':
    $where_str = " WHERE `order_code`='$order_code'";
    deleteDb($db_name, $where_str, $sql_data, $trace);
    $msg = "刪除成功!!";
    break;

  default:
    $msg = "不正確的操作模式!!";
}






?>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<script language="JavaScript" type="text/JavaScript">
  alert("<?= $msg ?>");
	window.location.href="<?php echo $web_url; ?>";
</script>