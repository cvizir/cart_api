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
// 原本的排序條件被註解掉（$order 沒定義）；表單有排序欄位，所以依排序、再依編號排列，換頁順序才穩定
$order    = " ORDER BY `m_sort` ASC, `no` ASC";
$where    = "";
$params   = array();

$specClass = new DBClass('spec_info', $page, $pagesize);
$result    = $specClass->getAdminList($where, $order, $trace, $params);
$specList  = $result['data'];
$total     = $result['total'];

// 編輯 / 刪除 / 新增 時帶給下一頁的參數
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
      window.location.href = "save.php?cms_mode=del&spec_code=" + encodeURIComponent(code) + sh_post;
      return true;
    }
    return false;
  }
  function onEdit(code) {
    window.location.href = "edit.php?cms_mode=edit&spec_code=" + encodeURIComponent(code) + sh_post;
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
              <td class="tb_menu_title">規格管理</td>
              <td align="right" class="tb_menu_title"><span class="tb_list_title_bt">
                <input name="Submit" type="button" value="新增" onClick="onAdd()" />
              </span></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="300" align="center" valign="top"><table width="89%">
            <tr><td valign="bottom">&nbsp;</td></tr>
            <tr>
              <td align="center" valign="top">
                <table width="827" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                  <tr>
                    <td width="93" height="28" class="tb_list_title">序號</td>
                    <td width="332" align="left" class="tb_list_title">規格名稱</td>
                    <td width="100" class="tb_list_title">上下架</td>
                    <td width="135" class="tb_list_title">編輯</td>
                  </tr>
                  <?php
                  $no = ($page - 1) * $pagesize;
                  foreach ($specList as $list_info) {
                    $no++;
                    $code_js = h(json_encode((string)$list_info['spec_code']));
                    $name_js = h(json_encode((string)$list_info['text']));
                  ?>
                  <tr>
                    <td class="tb_list_info"><?php echo $no; ?></td>
                    <td class="tb_list_info" style="text-align:left;"><?php echo h($list_info['text']); ?></td>
                    <td class="tb_list_info"><?php echo getIshow($list_info['ishow']); ?></td>
                    <td class="tb_list_info">
                      <input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $code_js; ?>)" />
                      <input type="button" class="btn_text" value="刪除" onClick="onDel(<?php echo $code_js; ?>, <?php echo $name_js; ?>)" />
                    </td>
                  </tr>
                  <?php } ?>
                  <?php if (empty($specList)) { ?>
                  <tr><td colspan="4" class="tb_list_info">目前沒有資料</td></tr>
                  <?php } ?>
                </table>
              </td>
            </tr>
            <tr>
              <td align="center" class="page"><span class="wd_white_12">
                <?php echo $specClass->showPageMenu($argument); ?>
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
