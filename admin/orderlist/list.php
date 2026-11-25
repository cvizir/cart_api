<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pagesize = 40;
$trace    = 0;

// 訂單狀態（下拉選單共用，原本在頁面裡重複寫了三份）
$order_states = array('未付款', '待付款', '完成訂購', '確認運費', '訂單處理中', '刷卡失敗',
                      '取消訂單', '已出貨', '先出貨待付款', '退貨訂單', '失效訂單');

// 付款方式 / 會員等級對照表：原本由舊的 config.php 提供，新的 config.inc.php 沒有，這裡補上預設值
if (!isset($pay_mode_array)) {
  $pay_mode_array = array('card' => '信用卡', 'atm' => 'ATM轉帳', 'store_pay' => '超商付款',
                          'line_pay' => 'Line Pay付款', 'post' => '郵政劃撥', 'foreign' => '國外訂購');
}
if (!isset($mem_lv_array)) { $mem_lv_array = array(); }

$sh_txt   = isset($_REQUEST['sh_txt']) ? trim($_REQUEST['sh_txt']) : '';
$sh_mem   = isset($_REQUEST['sh_mem']) ? trim($_REQUEST['sh_mem']) : '';
$sh_order = isset($_REQUEST['sh_order']) ? trim($_REQUEST['sh_order']) : '';

/*
 * 搜尋條件。原本三組條件之間用 OR 串接（同時選了狀態和關鍵字會變成「聯集」），
 * 現在改成各組條件之間用 AND，組內（模糊搜尋的多個欄位）才用 OR。
 */
$conds  = array();
$params = array();
if ($sh_mem !== '') {
  $conds[]  = "(`member_no` = ? OR `member_code` = ?)";
  $params[] = $sh_mem;
  $params[] = $sh_mem;
}
if ($sh_txt !== '') {
  $like     = '%' . $sh_txt . '%';
  $conds[]  = "(`order_code` LIKE ? OR `invoice_name` LIKE ? OR `invoice_email` LIKE ? OR `total_amount` LIKE ? OR `no` LIKE ? OR `store_num` = ?)";
  $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
  $params[] = $sh_txt;
}
if ($sh_order !== '') {
  // 原本用 LIKE '%待付款%'，會連「先出貨待付款」也一起查出來，改成完全比對
  $conds[]  = "`order_state` = ?";
  $params[] = $sh_order;
}
$where = $conds ? " WHERE " . implode(" AND ", $conds) : "";
$order = " ORDER BY `buytime` DESC";

// 分頁、編輯都要帶著搜尋條件
$argument = "&sh_mem=" . urlencode($sh_mem) . "&sh_txt=" . urlencode($sh_txt) . "&sh_order=" . urlencode($sh_order);
$sh_post  = $argument . "&page=" . $page . "&pagesize=" . $pagesize;

$orderlistClass = new DBClass('orderlist', $page, $pagesize);
$result         = $orderlistClass->getAdminList($where, $order, $trace, $params);
$orderlistList  = $result['data'];
$total          = $result['total'];

// 一次查出這一頁所有訂單的會員資料（原本每一列都查一次）
$members = array();
$member_nos = array();
foreach ($orderlistList as $o) {
  if ($o['member_no'] !== '' && $o['member_no'] !== null) { $member_nos[$o['member_no']] = true; }
}
if ($member_nos) {
  $ids  = array_keys($member_nos);
  $in   = implode(',', array_fill(0, count($ids), '?'));
  $stmt = $pdo->prepare("SELECT * FROM `member` WHERE `no` IN ({$in})");
  $stmt->execute($ids);
  foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $m) { $members[$m['no']] = $m; }
}

// 前端的權限提示（真正的權限檢查在 save.php / chang_state.php）
$admin_pv = isset($_SESSION["admin_pv"]) ? $_SESSION["admin_pv"] : '';
$can_edit = empty($admin_pv_check) || strExist($admin_pv, 'orderlist_et');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<link href="../style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../script/jquery-1.6.3.min.js"></script>
<script type="text/javascript" src="../../script/datepicker/WdatePicker.js"></script>
<script language="JavaScript" type="text/JavaScript">
  var sh_post = <?php echo json_encode($sh_post, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

  function onEdit(no) {
    window.location.href = "edit.php?cms_mode=edit&no=" + encodeURIComponent(no) + sh_post;
  }
  <?php if ($can_edit) { ?>
  function onAdd() {
    window.location.href = "edit.php?cms_mode=add" + sh_post;
  }
  <?php } else { ?>
  function onAdd() { alert("您沒有編輯的權限"); }
  <?php } ?>

  // 在列表上直接修改訂單狀態
  function chang_state(no, order_code, order_state, selectObj) {
    $.ajax({
      url: "chang_state.php",
      type: "POST",
      dataType: "json",
      data: { no: no, order_code: order_code, order_state: order_state },
      success: function (res) {
        if (!res || !res.ok) {
          alert((res && res.msg) ? res.msg : "狀態更新失敗");
        }
      },
      error: function () {
        alert("狀態更新失敗，請稍後再試");
      }
    });
  }

  // 匯出：明細匯出 / 彙整匯出 共用同一個表單，只是送到不同的檔案
  function doExport(action) {
    var f = document.forms['form1'];
    f.action = action;
    f.submit();
  }
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="10%" align="left" valign="top" bgcolor="#525252"><?php require_once("../menu.php"); ?></td>
    <td width="90%" valign="top"><div align="center"><br />
      <br />
      <table width="95%" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
        <tr>
          <td width="1170"><table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
              <td width="25%" class="tb_menu_title">訂單資料</td>
              <td align="right" class="tb_menu_title"><input name="Submit" type="button" value="新增訂單" onClick="onAdd()" /></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="300" align="center" valign="top"><table width="90%">
            <tr>
              <td colspan="3">
                <form name="form1" method="post" action="excel_tr.php">
                  訂單匯出：
                  <select name="sh_state" id="sh_state">
                    <option value="all" selected="selected">全部</option>
                    <?php foreach ($order_states as $st) { ?>
                    <option value="<?php echo h($st); ?>"><?php echo h($st); ?></option>
                    <?php } ?>
                  </select>
                  日期
                  <input type="text" value="" name="start_time" id="start_time">
                  <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'start_time'})">&nbsp;~
                  <input type="text" value="" name="end_time" id="end_time">
                  <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'end_time'})">
                  <input type="button" name="button1" id="button1" value="明細匯出" onClick="doExport('order_tr.php')">
                  <input type="button" name="button2" id="button2" value="彙整匯出" onClick="doExport('excel_tr.php')">
                </form>
              </td>
            </tr>
            <tr>
              <td colspan="3" align="left" valign="top">
                <form name="form3" method="get" action="list.php">
                  會員編號：W <input name="sh_mem" type="text" value="<?php echo h($sh_mem); ?>"> &nbsp;
                  模糊搜尋：<input name="sh_txt" type="text" value="<?php echo h($sh_txt); ?>"> &nbsp;
                  訂單狀態：
                  <select name="sh_order" id="sh_order">
                    <option value="">全部</option>
                    <?php foreach ($order_states as $st) { ?>
                    <option value="<?php echo h($st); ?>" <?php chkSelected($st, $sh_order); ?>><?php echo h($st); ?></option>
                    <?php } ?>
                  </select>
                  &nbsp;&nbsp;
                  <input type="submit" value="送出">
                </form>
              </td>
            </tr>
            <tr>
              <td colspan="3" align="center" valign="top">
                <table width="100%" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                  <tr>
                    <td width="131" class="tb_list_title">訂單日期</td>
                    <td width="76" class="tb_list_title">訂單編號</td>
                    <td width="138" class="tb_list_title">購買人</td>
                    <td width="70" class="tb_list_title">付款方式</td>
                    <td width="66" class="tb_list_title">訂單金額</td>
                    <td width="90" class="tb_list_title">收件人</td>
                    <td width="76" class="tb_list_title">匯款號碼</td>
                    <td width="110" class="tb_list_title">出貨單號</td>
                    <td width="110" class="tb_list_title">訂單狀態</td>
                    <td width="72" class="tb_list_title">編輯</td>
                  </tr>
                  <?php
                  foreach ($orderlistList as $orderlist) {
                    $row_member = isset($members[$orderlist['member_no']]) ? $members[$orderlist['member_no']] : array();
                    $lv_code    = isset($row_member['mem_lv']) ? $row_member['mem_lv'] : '';
                    $lv_name    = isset($mem_lv_array[$lv_code]) ? $mem_lv_array[$lv_code] : $lv_code;
                    $spc        = isset($row_member['spc_discount']) ? $row_member['spc_discount'] : '';
                    $pay_name   = isset($pay_mode_array[$orderlist['pay_mode']]) ? $pay_mode_array[$orderlist['pay_mode']] : $orderlist['pay_mode'];
                    $order_url  = 'https://shopping.windmill.com.tw/email_order.php?order_code=' . urlencode($orderlist['order_code']) . '&member_code=' . urlencode($orderlist['member_code']);
                    $no_js      = h(json_encode((string)$orderlist['no']));
                    $code_js    = h(json_encode((string)$orderlist['order_code']));
                  ?>
                  <tr>
                    <td class="tb_list_info">
                      <a href="order_tr.php?no=<?php echo urlencode($orderlist['no']); ?>" target="_blank"><?php echo ($orderlist['buytime'] != "") ? h($orderlist['buytime']) : "&nbsp;"; ?></a>
                    </td>
                    <td align="left" class="tb_list_info" style="text-align:left;">
                      <a href="<?php echo h($order_url); ?>" target="_blank"><?php echo 'C17' . sprintf("%06d", $orderlist['no']); ?></a>
                    </td>
                    <td align="left" class="tb_list_info" style="text-align:left;"><?php echo h($lv_name); ?>-W<?php echo h($orderlist['member_no']); ?><?php echo h($spc); ?></td>
                    <td align="left" class="tb_list_info" style="text-align:left;"><?php echo h($pay_name); ?></td>
                    <td align="right" class="tb_list_info" style="text-align:right;"><?php echo h($orderlist['total_amount']); ?>&nbsp;</td>
                    <td align="left" class="tb_list_info" style="text-align:left;"><?php echo h($orderlist['invoice_name']); ?></td>
                    <td align="left" class="tb_list_info"><?php echo h($orderlist['atm_num']); ?></td>
                    <td align="left" class="tb_list_info" style="text-align:left;"><?php echo h($orderlist['post_num']); ?></td>
                    <td align="left" class="tb_list_info" style="text-align:left;">
                      <select onChange="chang_state(<?php echo $no_js; ?>, <?php echo $code_js; ?>, this.value, this)">
                        <?php foreach ($order_states as $st) { ?>
                        <option value="<?php echo h($st); ?>" <?php chkSelected($st, $orderlist['order_state']); ?>><?php echo h($st); ?></option>
                        <?php } ?>
                      </select>
                    </td>
                    <td class="tb_list_info"><input type="button" value="編輯訂單" onClick="onEdit(<?php echo $no_js; ?>)" /></td>
                  </tr>
                  <?php } ?>
                  <?php if (empty($orderlistList)) { ?>
                  <tr><td colspan="10" class="tb_list_info">沒有符合的訂單</td></tr>
                  <?php } ?>
                </table>
              </td>
            </tr>
            <tr>
              <td colspan="2" align="center" class="page"><span class="wd_white_12">
                <?php echo $orderlistClass->showPageMenu($argument); ?>
              </span></td>
            </tr>
          </table></td>
        </tr>
      </table>
      <br />
    </div></td>
  </tr>
</table>
</body>
</html>
