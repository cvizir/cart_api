<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

function reqv($key) {
  return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : '';
}

$pdcat_m_name = reqv('pdcat_m_name');
$pdcat_m_code = reqv('pdcat_m_code');
$pdcat_t_name = reqv('pdcat_t_name');
$pdcat_t_code = reqv('pdcat_t_code');
$pdcat_code   = reqv('pdcat_code');
$cms_mode     = reqv('cms_mode');
if ($cms_mode === '') {
  $cms_mode = ($pdcat_code === '') ? 'add' : 'edit';
}
$page         = isset($_REQUEST['page']) ? max(1, (int)$_REQUEST['page']) : 1;
$sh_post      = $_SERVER['QUERY_STRING'];
$trace        = 0;   // 原本是 1，會把除錯資訊印在畫面最上方

// 新增模式預設值
$pd_cat = array(
  'no' => '', 'pdcat_code' => '', 'pdcat_m_code' => '', 'uptime' => '', 'name' => '',
  'url' => '', 'link_type' => '', 'ishow' => '1', 'm_sort' => ''
);

// 原本是用 pdcat_m_code 判斷要不要查詢（新增時也會誤查），現在改用 pdcat_code
if ($pdcat_code !== '') {
  $stmt = $pdo->prepare("SELECT * FROM `pdcat` WHERE `pdcat_code` = ?");
  $stmt->execute(array($pdcat_code));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $pd_cat = array_merge($pd_cat, $row);
    $pdcat_m_code = (string)$row['pdcat_m_code'];   // 以資料本身所屬的上層類別為準
  }
}

// 網址沒帶上層名稱時，由資料庫補齊（麵包屑與回列表都會用到）
if ($pdcat_m_code !== '' && ($pdcat_m_name === '' || $pdcat_t_code === '')) {
  $stmt = $pdo->prepare("SELECT `name`, `pdcat_t_code` FROM `pdcat_m` WHERE `pdcat_m_code` = ?");
  $stmt->execute(array($pdcat_m_code));
  $m = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($m) {
    if ($pdcat_m_name === '') { $pdcat_m_name = (string)$m['name']; }
    if ($pdcat_t_code === '') { $pdcat_t_code = (string)$m['pdcat_t_code']; }
  }
}
if ($pdcat_t_code !== '' && $pdcat_t_name === '') {
  $stmt = $pdo->prepare("SELECT `name` FROM `pdcat_t` WHERE `pdcat_t_code` = ?");
  $stmt->execute(array($pdcat_t_code));
  $pdcat_t_name = (string)$stmt->fetchColumn();
}

// 排序最大值：只算同一個上層類別底下的筆數
$sortmax_where = " WHERE `pdcat_m_code` = " . $pdo->quote($pdcat_m_code);
$sort_max      = sortMax($cms_mode, 'pdcat', $sortmax_where, 'no', $trace);
$sort_selected = ($pd_cat['m_sort'] === '' || $pd_cat['m_sort'] === null) ? $sort_max : $pd_cat['m_sort'];
$sort_end      = max((int)$sort_max, (int)$sort_selected);

$url_t = "../pdcat_t/list.php";
$url_m = "../pdcat_m/list.php?pdcat_t_name=" . urlencode($pdcat_t_name) . "&pdcat_t_code=" . urlencode($pdcat_t_code);

if ($trace == 1) {
  echo '$sort_max=' . h($sort_max) . '<br>';
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<script type="text/javascript" src="../script/ckeditor/ckeditor.js"></script>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
  function validator(f) {
    if (f.name.value.replace(/\s/g, '') === '') {
      alert('請輸入名稱');
      f.name.focus();
      return false;
    }
    return true;
  }
</script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="101%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2"><?php require_once("../header.php"); ?></td>
  </tr>
  <tr>
    <td width="10%" align="left" bgcolor="#525252" valign="top"><?php require_once("../menu.php"); ?></td>
    <td width="90%" valign="top"><div align="center"><br />
      <br />
      <table width="800" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
        <tr>
          <td width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
              <td width="49%" class="tb_menu_title">商品類別
                <?php
                if ($pdcat_t_name != '' && $pdcat_t_code != '') {
                  echo '<a href="' . h($url_t) . '"> &gt; ' . h($pdcat_t_name) . '</a>';
                }
                if ($pdcat_m_name != '' && $pdcat_m_code != '') {
                  echo '<a href="' . h($url_m) . '"> &gt; ' . h($pdcat_m_name) . '</a>';
                }
                ?>
              </td>
              <td width="51%" align="right" class="tb_menu_title"><input type="button" value="回上一頁" onClick="window.history.go(-1);" /></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="300" align="center" valign="top"><table width="100%">
            <tr><td>&nbsp;</td></tr>
            <tr>
              <td align="center" valign="top">
                <form action="save.php?<?php echo h($sh_post); ?>" method="post" id="form1" name="CodeForm" onSubmit="return validator(this)">
                  <table width="90%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                    <tr>
                      <td class="tb_edit_title">上次修改時間</td>
                      <td class="tb_edit_info"><?php echo h($pd_cat['uptime']); ?>&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="15%" class="tb_edit_title">名稱</td>
                      <td width="84%" class="tb_edit_info"><input name="name" type="text" id="name" value="<?php echo h($pd_cat['name']); ?>" size="60"></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">說明</td>
                      <td class="tb_edit_info"><textarea class="ckeditor" name="url" cols="60" rows="6"><?php echo h($pd_cat['url']); ?></textarea></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">連結方式</td>
                      <td class="tb_edit_info">
                        <label>預設<input type="radio" value="" name="link_type" <?php chkChecked('', $pd_cat['link_type']); ?>></label>
                        &nbsp;&nbsp;&nbsp;
                        <label>連結<input type="radio" value="_self" name="link_type" <?php chkChecked('_self', $pd_cat['link_type']); ?>></label>
                        &nbsp;&nbsp;&nbsp;
                        <label>連結另開<input type="radio" value="_blank" name="link_type" <?php chkChecked('_blank', $pd_cat['link_type']); ?>></label>
                      </td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">上下架</td>
                      <td class="tb_edit_info">
                        <label>上架<input type="radio" value="1" name="ishow" <?php chkChecked('1', $pd_cat['ishow']); ?>></label>
                        &nbsp;&nbsp;&nbsp;
                        <label>下架<input type="radio" value="0" name="ishow" <?php chkChecked('0', $pd_cat['ishow']); ?>></label>
                      </td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">排序</td>
                      <td class="tb_edit_info">
                        <select name="m_sort" id="m_sort">
                          <?php getDateSelectOption(1, $sort_end, $sort_selected); ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" align="center">
                        <input type="hidden" value="<?php echo h($pd_cat['no']); ?>" name="no" id="no" />
                        <input type="hidden" value="<?php echo h($pd_cat['pdcat_code']); ?>" name="pdcat_code" id="pdcat_code" />
                        <input type="hidden" value="<?php echo h($pdcat_m_code); ?>" name="pdcat_m_code" id="pdcat_m_code" />
                        <input type="hidden" value="<?php echo h($pdcat_m_name); ?>" name="pdcat_m_name" id="pdcat_m_name" />
                        <input type="hidden" value="<?php echo h($pdcat_t_code); ?>" name="pdcat_t_code" id="pdcat_t_code" />
                        <input type="hidden" value="<?php echo h($pdcat_t_name); ?>" name="pdcat_t_name" id="pdcat_t_name" />
                        <input type="hidden" value="<?php echo (int)$page; ?>" name="page" id="page" />
                        <input type="submit" name="button" id="button" value="<?php echo ($cms_mode == 'add') ? '新增資料' : '修改資料'; ?>">
                      </td>
                    </tr>
                  </table>
                </form>
                <br><br>
              </td>
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
