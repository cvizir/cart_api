<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pagesize = 20;

$argument = "";   // 分頁連結額外參數（目前沒有搜尋條件）
$trace    = 0;
$where    = "";
$params   = array();
$order    = " ORDER BY `no` DESC";

$contactClass = new DBClass('contact', $page, $pagesize);
$result       = $contactClass->getAdminList($where, $order, $trace, $params);
$contactList  = $result['data'];
$total        = $result['total'];

// 編輯 / 刪除 時帶給 save.php 的參數
$sh_post = "&page=" . $page . "&pagesize=" . $pagesize . "&total=" . $total . $argument;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<script language="JavaScript" type="text/JavaScript">
  var sh_post = <?php echo json_encode($sh_post, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

  function onDel(code, name) {
    if (window.confirm("確定要刪除 [ " + name + " ] 這筆資料嗎?")) {
      window.location.href = "save.php?cms_mode=del&contact_code=" + encodeURIComponent(code) + sh_post;
      return true;
    }
    return false;
  }
  function onEdit(code) {
    window.location.href = "edit.php?cms_mode=edit&contact_code=" + encodeURIComponent(code) + sh_post;
  }
</script>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2"><?php require_once("../header.php"); ?></td>
  </tr>
  <tr>
    <td width="10%" align="left" valign="top" bgcolor="#525252"><?php require_once("../menu.php"); ?></td>
    <td width="90%" valign="top"><div align="center"><br />
      <br />
      <table width="806" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
        <tr>
          <td width="798"><table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
              <td class="tb_menu_title">聯絡我們管理</td>
              <td align="right" class="tb_menu_title">&nbsp;</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="300" align="center" valign="top"><table width="89%">
            <tr>
              <td align="center" valign="top">
                <table width="792" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                  <tr>
                    <td width="60" height="28" class="tb_list_title">序號</td>
                    <td width="150" align="left" class="tb_list_title">姓名</td>
                    <td width="250" align="left" class="tb_list_title">主題</td>
                    <td width="150" class="tb_list_title">訊息時間</td>
                    <td width="135" class="tb_list_title">編輯</td>
                  </tr>
                  <?php
                  $no = ($page - 1) * $pagesize;
                  foreach ($contactList as $list_info) {
                    $no++;
                    $code_js = h(json_encode((string)$list_info['contact_code']));
                    $name_js = h(json_encode((string)$list_info['name']));
                  ?>
                  <tr>
                    <td class="tb_list_info"><?php echo $no; ?></td>
                    <td class="tb_list_info" style="text-align:left;"><?php echo h($list_info['name']); ?></td>
                    <td class="tb_list_info" style="text-align:left;"><?php echo h($list_info['subject']); ?></td>
                    <td class="tb_list_info"><?php echo h($list_info['post_time']); ?></td>
                    <td class="tb_list_info">
                      <input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $code_js; ?>)" />
                      <input type="button" class="btn_text" value="刪除" onClick="onDel(<?php echo $code_js; ?>, <?php echo $name_js; ?>)" />
                    </td>
                  </tr>
                  <?php } ?>
                  <?php if (empty($contactList)) { ?>
                  <tr><td colspan="5" class="tb_list_info">目前沒有資料</td></tr>
                  <?php } ?>
                </table>
              </td>
            </tr>
            <tr>
              <td align="center" class="page"><span class="wd_white_12">
                <?php echo $contactClass->showPageMenu($argument); ?>
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
