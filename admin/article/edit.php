<?php
require_once("../../include/config.inc.php");
include_once("../session.php");
require_once("../../include/function.php");
require_once("../../include/DBClass.php");

if (!function_exists('h')) {
  function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

$trace     = 0;
$news_code = isset($_REQUEST['news_code']) ? trim($_REQUEST['news_code']) : '';
$cms_mode  = isset($_REQUEST['cms_mode']) ? trim($_REQUEST['cms_mode']) : ($news_code === '' ? 'add' : 'edit');
$page      = isset($_REQUEST['page']) ? max(1, (int)$_REQUEST['page']) : 1;
$sh_post   = $_SERVER['QUERY_STRING'];

// 新增模式預設值（撰寫日期預設為現在）
$row_edit = array(
  'no' => '', 'news_code' => '', 'uptime' => '', 'name' => '',
  'wttime' => date("Y-m-d H:i:s"), 'author' => '', 'start_time' => '', 'end_time' => '',
  'summary' => '', 'info' => '', 'web_url' => '', 'ishow' => '1', 'photo' => ''
);

if ($news_code !== '') {
  $stmt = $pdo->prepare("SELECT * FROM `article` WHERE `news_code` = ?");
  $stmt->execute(array($news_code));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $row_edit = array_merge($row_edit, $row);
  }
}

// 權限相關變數可能由 session.php 提供；沒有時視為不做權限限制
$admin_pv = isset($_SESSION["admin_pv"]) ? $_SESSION["admin_pv"] : '';
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<?php
$check_txt  = 'article';
$check_type = 'read';
checkAdmimPv($admin_pv, $check_txt, $check_type, $trace);
?>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<script type="text/javascript" src="../../script/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../../script/datepicker/WdatePicker.js"></script>
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
    <td width="10%" align="left" bgcolor="#525252" valign="top"><?php require_once("../menu.php"); ?></td>
    <td width="90%" valign="top"><div align="center"><br />
      <br />
      <table width="800" border="1" align="center" cellpadding="2" cellspacing="0" bordercolor="#637A9C">
        <tr>
          <td width="100%"><table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
              <td width="49%" class="tb_menu_title">分享文</td>
              <td width="51%" align="right" class="tb_menu_title"><input type="button" value="回上一頁" onClick="window.history.go(-1);" /></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="300" align="center" valign="top"><table width="100%">
            <tr><td>&nbsp;</td></tr>
            <tr>
              <td align="center" valign="top">
                <form action="save.php?<?php echo h($sh_post); ?>" method="post" id="form1" enctype="multipart/form-data" name="CodeForm" onSubmit="return validator(this)">
                  <table width="90%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                    <?php if ($row_edit['uptime'] != "") { ?>
                    <tr>
                      <td class="tb_edit_title">最後修改時間</td>
                      <td class="tb_edit_info"><?php echo h($row_edit['uptime']); ?>&nbsp;</td>
                    </tr>
                    <?php } ?>
                    <tr>
                      <td width="15%" class="tb_edit_title">名稱</td>
                      <td class="tb_edit_info"><input name="name" type="text" id="name" value="<?php echo h($row_edit['name']); ?>" size="60"></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">撰寫日期</td>
                      <td width="84%" class="tb_edit_info">
                        <input type="text" value="<?php echo h($row_edit['wttime']); ?>" name="wttime" id="wttime">
                        <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'wttime'})">
                      </td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">作者</td>
                      <td class="tb_edit_info"><input name="author" type="text" id="author" value="<?php echo h($row_edit['author']); ?>" size="30"></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">上架日期</td>
                      <td width="84%" class="tb_edit_info">
                        <input type="text" value="<?php echo h($row_edit['start_time']); ?>" name="start_time" id="start_time">
                        <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'start_time'})">
                      </td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">下架日期</td>
                      <td class="tb_edit_info">
                        <input type="text" value="<?php echo h($row_edit['end_time']); ?>" name="end_time" id="end_time">
                        <img src="../../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" align="absmiddle" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'end_time'})">
                      </td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">摘要</td>
                      <td class="tb_edit_info"><textarea name="summary" id="summary" style="width:60%;"><?php echo h($row_edit['summary']); ?></textarea></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">內容</td>
                      <td class="tb_edit_info"><textarea class="ckeditor" id="info" name="info" cols="" rows=""><?php echo h($row_edit['info']); ?></textarea></td>
                    </tr>
                    <tr>
                      <td class="tb_edit_title">連結</td>
                      <td class="tb_edit_info"><textarea name="web_url" id="web_url" style="width:90%;"><?php echo h($row_edit['web_url']); ?></textarea></td>
                    </tr>
                    <tr>
                      <td align="center" class="tb_edit_title">上下架</td>
                      <td class="tb_edit_info">
                        <label><input type="radio" name="ishow" value="1" <?php chkChecked('1', $row_edit['ishow']); ?>>上架</label>
                        <label><input type="radio" name="ishow" value="0" <?php chkChecked('0', $row_edit['ishow']); ?>>下架</label>
                      </td>
                    </tr>
                    <tr bordercolor="#eeeeee">
                      <td align="center" class="tb_edit_title">縮圖照片</td>
                      <td class="text_1">
                        <label><input name="del_pic[0]" type="checkbox" id="del_pic" value="1">刪除圖片</label><br>
                        <input type="file" name="add_pic[]" id="fileField" accept="image/jpeg,image/png">
                        (上傳尺寸寬1240px 高600px，僅限 JPG / PNG)
                        <?php
                        if (preg_match('/^[A-Za-z0-9]+$/', (string)$row_edit['news_code'])) {
                          $filename = '../../images/upload/article/article_' . $row_edit['news_code'] . '_1.jpg';
                          if (file_exists($filename)) {
                            echo '<br><br><img src="' . h($filename) . '?v=' . (int)@filemtime($filename) . '" width="300">';
                          }
                        }
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" align="center">
                        <input type="hidden" value="<?php echo h($row_edit['no']); ?>" name="no" id="no" />
                        <input type="hidden" value="<?php echo h($row_edit['news_code']); ?>" name="news_code" id="news_code" />
                        <input type="hidden" value="<?php echo (int)$page; ?>" name="page" id="page" />
                        <?php if (empty($admin_pv_check) || strExist($admin_pv, 'news_et')) { ?>
                        <input type="submit" name="button" id="button" value="<?php echo ($cms_mode == 'add') ? '新增資料' : '修改資料'; ?>">
                        <?php } ?>
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
