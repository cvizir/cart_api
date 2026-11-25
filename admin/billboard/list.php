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
$bd_name  = isset($_REQUEST['bd_name']) ? trim($_REQUEST['bd_name']) : '';
$bd_id    = isset($_REQUEST['bd_id']) ? trim($_REQUEST['bd_id']) : '';

// 分頁連結參數
$argument = "&bd_name=" . urlencode($bd_name) . "&bd_id=" . urlencode($bd_id);
$trace    = 0;
$order    = " ORDER BY `no` DESC";
$where    = " WHERE `bd_id` = ?";
$params   = array($bd_id);

$billboardClass = new DBClass('billboard', $page, $pagesize);
$result         = $billboardClass->getAdminList($where, $order, $trace, $params);
$billboardList  = $result['data'];
$total          = $result['total'];

// 編輯 / 刪除 / 新增 時帶給下一頁的參數
$sh_post = $argument . "&page=" . $page . "&pagesize=" . $pagesize . "&total=" . $total;

$billboard_ishow = array('0' => '下架', '1' => '上架', '3' => '依時間上下架');

// 只允許安全的連結（擋掉 javascript: 等）
function safeUrl($url) {
  $url = trim((string)$url);
  if ($url === '' || preg_match('/^\s*(javascript|data|vbscript)\s*:/i', $url)) {
    return '';
  }
  return $url;
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/JavaScript">
  var sh_post = <?php echo json_encode($sh_post, JSON_HEX_TAG | JSON_HEX_AMP); ?>;

  function onDel(no, name) {
    if (window.confirm("確定要刪除 [ " + name + " ] 這筆資料嗎?")) {
      window.location.href = "save.php?cms_mode=del&no=" + encodeURIComponent(no) + sh_post;
      return true;
    }
    return false;
  }
  function onEdit(no) {
    window.location.href = "edit.php?cms_mode=edit&no=" + encodeURIComponent(no) + sh_post;
  }
  function onAdd() {
    window.location.href = "edit.php?cms_mode=add" + sh_post;
  }
</script>
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
      <table width="709" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
        <tr>
          <td width="744"><table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
              <td class="tb_menu_title"><?php echo h($bd_name); ?>-版面管理</td>
              <td class="tb_menu_title" align="right">
                <input type="button" value="回主目錄" onClick="window.location.href='../billboard_cat/list.php'" />
                <input type="button" value="新增廣告" onClick="onAdd()" />
              </td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="300" align="center" valign="top"><table width="100%" height="225">
            <tr><td valign="bottom">&nbsp;</td></tr>
            <tr>
              <td align="center" valign="top">
                <table width="733" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                  <tr>
                    <td width="87" height="29" align="center" class="tb_list_title">序號</td>
                    <td width="222" align="center" class="tb_list_title">名稱</td>
                    <td width="103" align="center" class="tb_list_title">上下架</td>
                    <td width="161" align="center" class="tb_list_title">編輯</td>
                  </tr>
                  <?php
                  foreach ($billboardList as $list_info) {
                    $no_js   = h(json_encode((string)$list_info['no']));
                    $name_js = h(json_encode((string)$list_info['name']));
                    $link    = safeUrl($list_info['web_url']);
                    $ishow   = isset($billboard_ishow[$list_info['ishow']]) ? $billboard_ishow[$list_info['ishow']] : '';
                  ?>
                  <tr>
                    <td align="center" class="text_1"><?php echo h($list_info['no']); ?></td>
                    <td align="center" class="text_1"><?php echo h($list_info['name']); ?>
                      <?php if ($link !== '') { ?>
                      &nbsp;<input type="button" class="btn_text" value="連結" onClick="window.open(<?php echo h(json_encode($link)); ?>, '_blank');" />
                      <?php } ?>
                    </td>
                    <td align="center" class="text_1"><?php echo h($ishow); ?></td>
                    <td align="center" class="text_1">
                      <input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $no_js; ?>)" />
                      <?php if ($list_info['del_sw'] == '0') { ?>
                      <input type="button" class="btn_text" value="刪除" onClick="onDel(<?php echo $no_js; ?>, <?php echo $name_js; ?>)" />
                      <?php } ?>
                    </td>
                  </tr>
                  <?php } ?>
                  <?php if (empty($billboardList)) { ?>
                  <tr><td colspan="4" align="center" class="text_1">目前沒有資料</td></tr>
                  <?php } ?>
                </table>
              </td>
            </tr>
            <tr>
              <td align="center" class="page"><span class="wd_white_12">
                <?php echo $billboardClass->showPageMenu($argument); ?>
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
