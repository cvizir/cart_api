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
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo h(ADMIN_TITLE); ?></title>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<script type="text/javascript" src="../script/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../script/datepicker/WdatePicker.js"></script>
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
<body>
<div class="layout">
  <div class="layout_header"><?php require_once("../header.php"); ?></div>
  <div class="layout_body">
    <div class="layout_sidebar"><?php require_once("../menu.php"); ?></div>
    <div class="layout_content">
      <div class="panel panel_md">
        <div class="panel_head tb_menu_title">
          <div>分享文</div>
          <div><input type="button" value="回上一頁" onClick="window.history.go(-1);" /></div>
        </div>
        <div class="panel_body">
<form action="save.php?<?php echo h($sh_post); ?>" method="post" id="form1" enctype="multipart/form-data" name="CodeForm" onSubmit="return validator(this)">
  <div class="edit_table">
<?php if ($row_edit['uptime'] != "") { ?>
<div class="edit_row">
  <div class="edit_title tb_edit_title">最後修改時間</div>
  <div class="edit_info tb_edit_info"><?php echo h($row_edit['uptime']); ?></div>
</div>
<?php } ?>
<div class="edit_row">
  <div class="edit_title tb_edit_title">名稱</div>
  <div class="edit_info tb_edit_info"><input name="name" type="text" id="name" value="<?php echo h($row_edit['name']); ?>" size="60"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">撰寫日期</div>
  <div class="edit_info tb_edit_info"><input type="text" value="<?php echo h($row_edit['wttime']); ?>" name="wttime" id="wttime">
    <img src="../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" class="date_icon" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'wttime'})"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">作者</div>
  <div class="edit_info tb_edit_info"><input name="author" type="text" id="author" value="<?php echo h($row_edit['author']); ?>" size="30"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">上架日期</div>
  <div class="edit_info tb_edit_info"><input type="text" value="<?php echo h($row_edit['start_time']); ?>" name="start_time" id="start_time">
    <img src="../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" class="date_icon" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'start_time'})"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">下架日期</div>
  <div class="edit_info tb_edit_info"><input type="text" value="<?php echo h($row_edit['end_time']); ?>" name="end_time" id="end_time">
    <img src="../script/datepicker/skin/datePicker.gif" alt="" width="16" height="22" class="date_icon" onClick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',el:'end_time'})"></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">摘要</div>
  <div class="edit_info tb_edit_info"><textarea name="summary" id="summary" style="width:60%;"><?php echo h($row_edit['summary']); ?></textarea></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">內容</div>
  <div class="edit_info tb_edit_info"><textarea class="ckeditor" id="info" name="info" cols="" rows=""><?php echo h($row_edit['info']); ?></textarea></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">連結</div>
  <div class="edit_info tb_edit_info"><textarea name="web_url" id="web_url" style="width:90%;"><?php echo h($row_edit['web_url']); ?></textarea></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">上下架</div>
  <div class="edit_info tb_edit_info"><label><input type="radio" name="ishow" value="1" <?php chkChecked('1', $row_edit['ishow']); ?>>上架</label>
    <label><input type="radio" name="ishow" value="0" <?php chkChecked('0', $row_edit['ishow']); ?>>下架</label></div>
</div>
<div class="edit_row">
  <div class="edit_title tb_edit_title">縮圖照片</div>
  <div class="edit_info tb_edit_info"><label><input name="del_pic[0]" type="checkbox" id="del_pic" value="1">刪除圖片</label><br>
    <input type="file" name="add_pic[]" id="fileField" accept="image/jpeg,image/png">
    (上傳尺寸寬1240px 高600px，僅限 JPG / PNG)
    <?php
    if (preg_match('/^[A-Za-z0-9]+$/', (string)$row_edit['news_code'])) {
      $filename = '../../images/upload/article/article_' . $row_edit['news_code'] . '_1.jpg';
      if (file_exists($filename)) {
        echo '<br><br><img src="' . h($filename) . '?v=' . (int)@filemtime($filename) . '" width="300">';
      }
    }
    ?></div>
</div>
    <div class="edit_actions">
      <input type="hidden" value="<?php echo h($row_edit['no']); ?>" name="no" id="no" />
      <input type="hidden" value="<?php echo h($row_edit['news_code']); ?>" name="news_code" id="news_code" />
      <input type="hidden" value="<?php echo (int)$page; ?>" name="page" id="page" />
      <?php if (empty($admin_pv_check) || strExist($admin_pv, 'news_et')) { ?>
      <input type="submit" name="button" id="button" value="<?php echo ($cms_mode == 'add') ? '新增資料' : '修改資料'; ?>">
      <?php } ?>
    </div>
  </div>
</form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
