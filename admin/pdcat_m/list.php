<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$page         = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pdcat_t_name = isset($_REQUEST['pdcat_t_name']) ? trim($_REQUEST['pdcat_t_name']) : '';
$pdcat_t_code = isset($_REQUEST['pdcat_t_code']) ? trim($_REQUEST['pdcat_t_code']) : '';
$pagesize     = 20;

// 上層類別參數（分頁連結、進入子分類都要帶）
$argument = "&pdcat_t_code=" . urlencode($pdcat_t_code) . "&pdcat_t_name=" . urlencode($pdcat_t_name);
$trace    = 0;
$order    = " ORDER BY `m_sort` ASC";
$where    = " WHERE `pdcat_t_code` = ?";
$params   = array($pdcat_t_code);

$catClass = new DBClass('pdcat_m', $page, $pagesize);
$result   = $catClass->getAdminList($where, $order, $trace, $params);
$catList  = $result['data'];
$total    = $result['total'];

// 編輯 / 刪除 / 新增 時帶給下一頁的參數（多了頁碼資訊）
$sh_post = $argument . "&page=" . $page . "&pagesize=" . $pagesize . "&total=" . $total;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<script language="JavaScript" type="text/JavaScript">
  var sh_post  = <?php echo json_encode($sh_post, JSON_HEX_TAG | JSON_HEX_AMP); ?>;
  var argument = <?php echo json_encode($argument, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

  function onDel(code, name) {
    if (window.confirm("確定要刪除 [ " + name + " ] 這筆資料嗎?")) {
      window.location.href = "save.php?cms_mode=del&pdcat_m_code=" + encodeURIComponent(code) + sh_post;
      return true;
    }
    return false;
  }
  function onEdit(code) {
    window.location.href = "edit.php?cms_mode=edit&pdcat_m_code=" + encodeURIComponent(code) + sh_post;
  }
  function onAdd() {
    window.location.href = "edit.php?cms_mode=add" + sh_post;
  }
  // 進入子分類：只帶上層類別參數，不帶本頁的頁碼
  function goPdcat(code, name) {
    window.location.href = "../pdcat/list.php?pdcat_m_name=" + encodeURIComponent(name) + "&pdcat_m_code=" + encodeURIComponent(code) + argument;
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
              <td class="tb_menu_title">商品類別<?php if ($pdcat_t_name != '' && $pdcat_t_code != '') { echo ' &gt; ' . h($pdcat_t_name); } ?></td>
              <td align="right" class="tb_menu_title"><span class="tb_list_title_bt">
                <input type="button" value="回上一層" onClick="window.location.href='../pdcat_t/list.php'" />
                <input type="button" value="新增" onClick="onAdd()" />
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
                    <td width="332" align="left" class="tb_list_title">商品名稱</td>
                    <td width="100" class="tb_list_title">上下架</td>
                    <td width="135" class="tb_list_title">增加子分類</td>
                    <td width="135" class="tb_list_title">編輯</td>
                  </tr>
                  <?php
                  $no = ($page - 1) * $pagesize;
                  foreach ($catList as $list_info) {
                    $no++;
                    $code_js = h(json_encode((string)$list_info['pdcat_m_code']));
                    $name_js = h(json_encode((string)$list_info['name']));
                  ?>
                  <tr>
                    <td class="tb_list_info"><?php echo $no; ?></td>
                    <td class="tb_list_info" style="text-align:left;"><?php echo h($list_info['name']); ?></td>
                    <td class="tb_list_info"><?php echo getIshow($list_info['ishow']); ?></td>
                    <td class="tb_list_info"><input type="button" class="btn_text" value="子分類" onClick="goPdcat(<?php echo $code_js; ?>, <?php echo $name_js; ?>)" /></td>
                    <td class="tb_list_info">
                      <input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $code_js; ?>)" />
                      <input type="button" class="btn_text" value="刪除" onClick="onDel(<?php echo $code_js; ?>, <?php echo $name_js; ?>)" />
                    </td>
                  </tr>
                  <?php } ?>
                  <?php if (empty($catList)) { ?>
                  <tr><td colspan="5" class="tb_list_info">目前沒有資料</td></tr>
                  <?php } ?>
                </table>
              </td>
            </tr>
            <tr>
              <td align="center" class="page"><span class="wd_white_12">
                <?php echo $catClass->showPageMenu($argument); ?>
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
