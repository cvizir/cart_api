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
$order    = " ORDER BY `no` DESC";
$where    = "";
$params   = array();

$catClass = new DBClass('billboard_cat', $page, $pagesize);
$result   = $catClass->getAdminList($where, $order, $trace, $params);
$catList  = $result['data'];
$total    = $result['total'];

// 編輯 / 新增 時帶給下一頁的參數
$sh_post = "&page=" . $page . "&pagesize=" . $pagesize . "&total=" . $total;

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

  function onEdit(no) {
    window.location.href = "edit.php?cms_mode=edit&no=" + encodeURIComponent(no) + sh_post;
  }
  function onAdd() {
    window.location.href = "edit.php?cms_mode=add" + sh_post;
  }
  // 進入該類別底下的廣告列表
  function onSub(no, name) {
    window.location.href = "../billboard/list.php?bd_id=" + encodeURIComponent(no) + "&bd_name=" + encodeURIComponent(name);
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
              <td class="tb_menu_title">版面管理</td>
              <td align="right" class="tb_menu_title"><input type="button" value="新增廣告" onClick="onAdd()" /></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td align="center" valign="top"><table width="88%">
            <tr><td valign="bottom">&nbsp;</td></tr>
            <tr>
              <td align="center" valign="top">
                <table width="734" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
                  <tr>
                    <td width="72" height="29" align="center" class="tb_list_title">序號</td>
                    <td width="272" align="center" class="tb_list_title">廣告名稱</td>
                    <td width="88" align="center" class="tb_list_title">編輯子類別</td>
                    <td width="98" align="center" class="tb_list_title">編輯</td>
                  </tr>
                  <?php
                  $no = ($page - 1) * $pagesize;
                  foreach ($catList as $list_info) {
                    $no++;
                    $no_js   = h(json_encode((string)$list_info['no']));
                    $name_js = h(json_encode((string)$list_info['name']));
                    $link    = safeUrl($list_info['web_url']);
                  ?>
                  <tr>
                    <td align="center" class="text_1"><?php echo $no; ?></td>
                    <td align="left" class="text_1">&nbsp;<?php echo h($list_info['name']); ?>
                      <?php if ($link !== '') { ?>
                      &nbsp;<input type="button" class="btn_text" value="連結" onClick="window.open(<?php echo h(json_encode($link)); ?>, '_blank');" />
                      <?php } ?>
                    </td>
                    <td align="center" class="text_1"><input type="button" class="btn_text" value="編輯子類別" onClick="onSub(<?php echo $no_js; ?>, <?php echo $name_js; ?>)" /></td>
                    <td align="center" class="text_1"><input type="button" class="btn_text" value="檢視 / 編輯" onClick="onEdit(<?php echo $no_js; ?>)" /></td>
                  </tr>
                  <?php } ?>
                  <?php if (empty($catList)) { ?>
                  <tr><td colspan="4" align="center" class="text_1">目前沒有資料</td></tr>
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
