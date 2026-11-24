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
$sh_txt   = isset($_REQUEST['sh_txt']) ? trim($_REQUEST['sh_txt']) : '';

// 上下頁 / 編輯 / 刪除 要帶的參數
$argument = "&sh_txt=" . urlencode($sh_txt);
$sh_post  = "&page=" . $page . $argument;
$trace    = 0;

$order  = " ORDER BY `no` DESC";
$where  = "";
$params = array();
if ($sh_txt !== "") {
  $where  = " WHERE `name` LIKE ? OR `email` LIKE ? ";
  $like   = '%' . $sh_txt . '%';
  $params = array($like, $like);
}

$memberClass = new DBClass('member', $page, $pagesize);
$result      = $memberClass->getAdminList($where, $order, $trace, $params);
$memberList  = $result['data'];
$total       = $result['total'];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<script language="JavaScript" type="text/JavaScript">
  var sh_post = <?php echo json_encode($sh_post, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

  function onDel(code, name) {
    if (window.confirm("確定要刪除 [ " + name + " ] 這筆資料嗎?")) {
      window.location.href = "save.php?cms_mode=del&total=<?php echo (int)$total; ?>&member_code=" + encodeURIComponent(code) + sh_post;
      return true;
    }
    return false;
  }
  function onEdit(code) {
    window.location.href = "edit.php?cms_mode=edit&member_code=" + encodeURIComponent(code) + sh_post;
  }
  function onAdd() {
    window.location.href = "edit.php?cms_mode=add" + sh_post;
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
              <td class="tb_menu_title">會員管理</td>
              <td align="right" class="tb_menu_title"><span class="tb_list_title_bt">
                <input name="Submit" type="button" value="新增" onClick="onAdd()" />
              </span></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="300" align="center" valign="top"><table width="89%">
            <tr>
              <td valign="bottom"><div align="right">
                <table width="790" border="0" align="center" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="412" height="20" valign="middle">
                      <form name="form2" method="get" action="">
                        <input name="sh_txt" type="text" value="<?php echo h($sh_txt); ?>" placeholder="姓名 / Email">
                        <input type="submit" value="送出">
                      </form>
                    </td>
                    <td width="358" align="right" valign="middle">
                      <form name="form3" method="post" action="excel_tr.php">
                        <input type="hidden" name="sh_txt" value="<?php echo h($sh_txt); ?>">
                        <input type="submit" value="匯出">
                      </form>
                    </td>
                  </tr>
                </table>
              </div></td>
            </tr>
            <tr>
              <td align="center" valign="top">
                <table width="792" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                  <tr>
                    <td width="93" height="28" class="tb_list_title">序號</td>
                    <td width="232" align="left" class="tb_list_title">會員名稱</td>
                    <td width="200" align="left" class="tb_list_title">信箱</td>
                    <td width="135" class="tb_list_title">編輯</td>
                  </tr>
                  <?php
                  $no = ($page - 1) * $pagesize;
                  foreach ($memberList as $list_info) {
                    $no++;
                    $code_js = h(json_encode((string)$list_info['member_code']));
                    $name_js = h(json_encode((string)$list_info['name']));
                  ?>
                  <tr>
                    <td class="tb_list_info"><?php echo $no; ?></td>
                    <td class="tb_list_info" style="text-align:left;"><?php echo h($list_info['name']); ?></td>
                    <td class="tb_list_info" style="text-align:left;"><?php echo h($list_info['email']); ?></td>
                    <td class="tb_list_info">
                      <input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $code_js; ?>)" />
                      <input type="button" class="btn_text" value="刪除" onClick="onDel(<?php echo $code_js; ?>, <?php echo $name_js; ?>)" />
                    </td>
                  </tr>
                  <?php } ?>
                  <?php if (empty($memberList)) { ?>
                  <tr><td colspan="4" class="tb_list_info">沒有符合的資料</td></tr>
                  <?php } ?>
                </table>
              </td>
            </tr>
            <tr>
              <td align="center" class="page"><span class="wd_white_12">
                <?php echo $memberClass->showPageMenu($argument); ?>
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
